"""Phase 3 D013 — 베타 client 등록 + 평문 토큰 1회 발급 CLI.

PHP API `api/print_clients_register.php` 는 admin/매장주 세션을 요구한다.
브라우저에서 mng/login.php 로 admin 로그인 후, 개발자 도구 > Application >
Cookies 에서 `barorez` 쿠키 값을 복사해 --cookie 로 전달.

사용:
    python -m client_python.tools.register_client `
      --base https://barorez.com `
      --cookie "barorez=abc..." `
      --shop 48 `
      --name "메인 카운터 PC" `
      --caps kitchen,counter

응답이 정상이면 client_idx 와 plain_token 을 stdout 으로 출력하고,
--write-config 옵션이 있으면 config.ini 에도 자동 기록.
"""

from __future__ import annotations

import argparse
import configparser
import http.client
import json
import sys
from pathlib import Path
from urllib.parse import urlparse


def _post_json(base_url: str, path: str, cookie: str, body: dict) -> tuple[int, dict]:
    parsed = urlparse(base_url)
    if parsed.scheme == "https":
        conn = http.client.HTTPSConnection(parsed.hostname, parsed.port or 443, timeout=15)
    else:
        conn = http.client.HTTPConnection(parsed.hostname, parsed.port or 80, timeout=15)
    headers = {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "Cookie": cookie,
        "User-Agent": "barorez-printer-register/0.1",
    }
    try:
        conn.request("POST", path, body=json.dumps(body, ensure_ascii=False).encode("utf-8"), headers=headers)
        resp = conn.getresponse()
        raw = resp.read()
        try:
            data = json.loads(raw.decode("utf-8"))
        except json.JSONDecodeError:
            data = {"ok": False, "error": "non_json_response", "raw": raw[:500].decode("utf-8", errors="replace")}
        return resp.status, data
    finally:
        conn.close()


def _write_config(
    config_path: Path,
    client_idx: int,
    token: str,
    capabilities: list[str],
    shop_idx: int,
    shop_name: str,
) -> None:
    cp = configparser.ConfigParser()
    if config_path.exists():
        cp.read(config_path, encoding="utf-8")
    if not cp.has_section("client"):
        cp.add_section("client")
    cp.set("client", "client_idx", str(client_idx))
    cp.set("client", "token", token)
    cp.set("client", "capabilities", ", ".join(capabilities))
    cp.set("client", "shop_idx", str(shop_idx))
    if shop_name:
        cp.set("client", "shop_name", shop_name)
    if not cp.has_option("client", "app_version"):
        cp.set("client", "app_version", "0.1.0-d016")

    with config_path.open("w", encoding="utf-8") as f:
        cp.write(f)


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description="베타 client 등록 + 토큰 발급")
    parser.add_argument("--base", default="https://barorez.com", help="PHP base URL")
    parser.add_argument("--cookie", required=True, help='admin 세션 쿠키 (예: "barorez=abc...")')
    parser.add_argument("--shop", required=True, type=int, help="대상 shop_idx")
    parser.add_argument("--name", required=True, help="client 이름 (예: '메인 카운터 PC')")
    parser.add_argument(
        "--caps",
        required=True,
        help="capability 콤마 구분 (kitchen, counter, bar 중)",
    )
    parser.add_argument(
        "--write-config",
        default=None,
        help="발급 결과를 config.ini 에 자동 기록 (경로)",
    )
    parser.add_argument(
        "--shop-name",
        default="",
        help="config.ini 에 기록할 매장 표시 이름 (운영자가 식별용)",
    )
    args = parser.parse_args(argv)

    caps_raw = [c.strip() for c in args.caps.split(",") if c.strip()]
    valid = {"kitchen", "counter", "bar"}
    if not caps_raw or any(c not in valid for c in caps_raw):
        print(f"invalid --caps: {args.caps!r} (use kitchen,counter,bar)", file=sys.stderr)
        return 2

    body = {
        "shop_idx": args.shop,
        "client_name": args.name,
        "capabilities": caps_raw,
    }
    status, data = _post_json(args.base, "/api/print_clients_register.php", args.cookie, body)

    if status != 200 or not data.get("ok"):
        print(f"register failed: HTTP {status}", file=sys.stderr)
        print(json.dumps(data, ensure_ascii=False, indent=2), file=sys.stderr)
        return 3

    client_idx = int(data["client_idx"])
    plain_token = str(data["plain_token"])

    print(f"client_idx  = {client_idx}")
    print(f"plain_token = {plain_token}")
    print(f"capabilities= {','.join(caps_raw)}")
    print()
    print("이 토큰은 다시 표시되지 않습니다. config.ini 에 즉시 입력 후 폐기.")

    if args.write_config:
        cfg_path = Path(args.write_config)
        cfg_path.parent.mkdir(parents=True, exist_ok=True)
        _write_config(cfg_path, client_idx, plain_token, caps_raw, args.shop, args.shop_name)
        print(f"-> wrote {cfg_path}")

    return 0


if __name__ == "__main__":
    sys.exit(main())
