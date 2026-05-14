"""last_known_job_id + 처리된 job_id 캐시의 JSON 영속화.

PRD §8.4 — 24시간 내 중복 수신 job_id 는 출력 생략 + ACK 만 회신.
재접속 시 last_known_job_id 를 Server C 에 전송하여 미수신 작업을 일괄
수신 (sync.ts).

원자 쓰기 — tmp 파일에 기록 후 rename. 동시 접근은 가정하지 않음
(단일 프로세스, 단일 워커).
"""

from __future__ import annotations

import json
import threading
import time
from dataclasses import dataclass, field
from pathlib import Path

DEDUP_TTL_SECONDS = 24 * 60 * 60  # 24 hours


@dataclass
class State:
    path: Path
    last_known_job_id: int | None = None
    # job_id (int) -> {"printed_at": iso?, "ts": epoch_seconds}
    processed: dict[int, dict[str, object]] = field(default_factory=dict)
    _lock: threading.Lock = field(default_factory=threading.Lock, repr=False)

    @classmethod
    def load(cls, path: str | Path) -> "State":
        p = Path(path)
        p.parent.mkdir(parents=True, exist_ok=True)
        if not p.exists():
            return cls(path=p)
        try:
            data = json.loads(p.read_text(encoding="utf-8"))
        except (OSError, json.JSONDecodeError):
            return cls(path=p)

        last = data.get("last_known_job_id")
        processed_raw = data.get("processed", {})
        processed: dict[int, dict[str, object]] = {}
        if isinstance(processed_raw, dict):
            for k, v in processed_raw.items():
                try:
                    job_id = int(k)
                except (TypeError, ValueError):
                    continue
                if isinstance(v, dict):
                    processed[job_id] = v
                elif isinstance(v, (int, float)):
                    # 구버전 형식: 단순 timestamp
                    processed[job_id] = {"ts": float(v)}

        return cls(
            path=p,
            last_known_job_id=int(last) if isinstance(last, (int, float)) else None,
            processed=processed,
        )

    def save(self) -> None:
        with self._lock:
            self._prune_locked(time.time())
            tmp = self.path.with_suffix(self.path.suffix + ".tmp")
            payload = {
                "last_known_job_id": self.last_known_job_id,
                "processed": {str(k): v for k, v in self.processed.items()},
            }
            tmp.write_text(json.dumps(payload, ensure_ascii=False), encoding="utf-8")
            tmp.replace(self.path)

    def is_duplicate(self, job_id: int) -> bool:
        with self._lock:
            return job_id in self.processed

    def get_printed_at(self, job_id: int) -> str | None:
        with self._lock:
            entry = self.processed.get(job_id)
            if entry is None:
                return None
            value = entry.get("printed_at")
            return value if isinstance(value, str) else None

    def record_processed(self, job_id: int, *, printed_at: str | None) -> None:
        with self._lock:
            entry: dict[str, object] = {"ts": time.time()}
            if printed_at:
                entry["printed_at"] = printed_at
            self.processed[job_id] = entry

    def observe_job_id(self, job_id: int) -> None:
        """수신한 모든 job_id 의 최댓값을 last_known_job_id 로 유지.

        성공/실패 무관 — Server C 의 sync 가 큐 상태 기준이므로 ACK 후
        같은 job 이 다시 오지 않음. 클라이언트는 수신 사실만 기록.
        """
        with self._lock:
            if self.last_known_job_id is None or job_id > self.last_known_job_id:
                self.last_known_job_id = job_id

    def _prune_locked(self, now: float) -> None:
        cutoff = now - DEDUP_TTL_SECONDS
        to_remove = [
            k
            for k, v in self.processed.items()
            if not isinstance(v.get("ts"), (int, float)) or float(v["ts"]) < cutoff  # type: ignore[arg-type]
        ]
        for k in to_remove:
            del self.processed[k]
