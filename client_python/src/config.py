"""config.ini 로더."""

from __future__ import annotations

import configparser
from dataclasses import dataclass
from pathlib import Path


@dataclass(frozen=True)
class PrinterConfig:
    name: str
    codepage: str
    width: int
    escpos_codepage_id: int | None
    right_margin: int


@dataclass(frozen=True)
class ServerConfig:
    ws_url: str
    http_base: str


@dataclass(frozen=True)
class ClientConfig:
    client_idx: int
    token: str
    capabilities: tuple[str, ...]
    app_version: str
    shop_idx: int  # 메타 — 서버에는 전송되지 않음
    shop_name: str  # 메타 — 로그/트레이 표시용


@dataclass(frozen=True)
class AppConfig:
    server: ServerConfig
    client: ClientConfig
    printer: PrinterConfig


def _split_csv(raw: str) -> tuple[str, ...]:
    return tuple(s.strip() for s in raw.split(",") if s.strip())


def load(path: str | Path) -> AppConfig:
    p = Path(path)
    if not p.exists():
        raise FileNotFoundError(f"config file not found: {p}")

    cp = configparser.ConfigParser()
    cp.read(p, encoding="utf-8")

    p_sec = cp["printer"]
    raw_id = p_sec.get("escpos_codepage_id", "").strip()
    printer = PrinterConfig(
        name=p_sec["name"].strip(),
        codepage=p_sec.get("codepage", "cp949").strip(),
        width=int(p_sec.get("width", "48")),
        escpos_codepage_id=int(raw_id) if raw_id else None,
        right_margin=int(p_sec.get("right_margin", "2")),
    )

    if cp.has_section("server"):
        s_sec = cp["server"]
        server = ServerConfig(
            ws_url=s_sec.get("ws_url", "").strip(),
            http_base=s_sec.get("http_base", "").strip(),
        )
    else:
        server = ServerConfig(ws_url="", http_base="")

    if cp.has_section("client"):
        c_sec = cp["client"]
        client = ClientConfig(
            client_idx=int(c_sec.get("client_idx", "0")),
            token=c_sec.get("token", "").strip(),
            capabilities=_split_csv(c_sec.get("capabilities", "")),
            app_version=c_sec.get("app_version", "").strip() or "unknown",
            shop_idx=int(c_sec.get("shop_idx", "0") or "0"),
            shop_name=c_sec.get("shop_name", "").strip(),
        )
    else:
        client = ClientConfig(
            client_idx=0, token="", capabilities=(), app_version="unknown", shop_idx=0, shop_name=""
        )

    return AppConfig(server=server, client=client, printer=printer)
