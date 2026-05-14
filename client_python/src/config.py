"""config.ini 로더. D012 범위 — printer 섹션만 다룬다."""

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


@dataclass(frozen=True)
class AppConfig:
    printer: PrinterConfig


def load(path: str | Path) -> AppConfig:
    p = Path(path)
    if not p.exists():
        raise FileNotFoundError(f"config file not found: {p}")

    cp = configparser.ConfigParser()
    cp.read(p, encoding="utf-8")

    section = cp["printer"]
    raw_id = section.get("escpos_codepage_id", "").strip()
    return AppConfig(
        printer=PrinterConfig(
            name=section["name"].strip(),
            codepage=section.get("codepage", "cp949").strip(),
            width=int(section.get("width", "48")),
            escpos_codepage_id=int(raw_id) if raw_id else None,
        )
    )
