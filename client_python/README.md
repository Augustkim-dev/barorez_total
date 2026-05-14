# barorez-printer (Python prototype)

Phase 3 — Windows 매장 PC 용 영수증 출력 클라이언트 **프로토타입**.
양산 빌드는 Phase 5 에서 C# 으로 다시 작성됨. 본 디렉터리는 흐름·인코딩·
드라이버 호환성 검증이 목적.

## D012 범위 (현재)

- ESC/POS 페이로드 변환 (`src/formatter.py`)
- Print Spooler RAW 모드 출력 어댑터 (`src/printer.py`)
- 오류 분류 4종 — `PRINTER_OFFLINE` / `PRINTER_OUT_OF_PAPER` /
  `UNSUPPORTED_ENCODING` / `UNKNOWN` (`src/errors.py`)
- `config.ini` 로더 (`src/config.py`) — 프린터 이름·코드페이지·폭
- 가짜 페이로드 → 실프린터 출력 단독 CLI (`tools/print_sample.py`)
- 설치된 프린터 목록 확인 CLI (`tools/list_printers.py`)

D013 이후 (다음 단위) 에서 WS 클라이언트·재접속·중복 방지·트레이 UI 가
추가됨.

## 설정

```powershell
cp config.example.ini config.ini
# config.ini 의 printer.name 을 Windows '장치 및 프린터' 의 실제 이름으로 변경
python -m client_python.tools.list_printers   # 설치된 프린터 목록 확인
```

## 사용 (D012 검증)

저장소 루트 (`맛집바로_barorez/`) 에서 실행:

```powershell
# 가상환경 + 의존성
python -m venv client_python\.venv
client_python\.venv\Scripts\Activate.ps1
pip install -r client_python\requirements.txt

# 1) 실 프린터 없이 RAW 바이트 구조 검증 (dump 모드)
python -m client_python.tools.print_sample `
  client_python\sample_payloads\kitchen_basic.json --dump out.bin

# 2) config.ini 작성 후 실 프린터 출력
python -m client_python.tools.print_sample `
  client_python\sample_payloads\kitchen_basic.json
```

세 가지 샘플 페이로드:
- `kitchen_basic.json` — 일반 케이스 (메뉴 3, 옵션 일부, 메모 있음)
- `counter_basic.json` — 카운터/포장, 메모 없음
- `kitchen_long.json` — 메뉴 7건 + 긴 메모 (줄바꿈·정렬 시험)

## 한글 인코딩 메모

- 국내 ESC/POS 모델은 대부분 **CP949** 를 지원하므로 기본값.
- 일부 펌웨어가 EUC-KR 만 지원하면 `config.ini` 의 `codepage = euc-kr` 로 변경.
- 펌웨어가 한글 코드페이지 선택 명령을 요구하면 `escpos_codepage_id` 에
  해당 번호 입력 (Epson 계열은 보통 `13` 또는 `22`).
- 인코딩 불가 문자는 `UNSUPPORTED_ENCODING` 으로 분류되어 ACK 시
  `error_code` 로 그대로 송신됨 (재시도 대상 아님).

## 디렉터리

```
client_python/
├── config.example.ini
├── requirements.txt
├── src/
│   ├── config.py        # ini 로더
│   ├── errors.py        # 오류 분류
│   ├── formatter.py     # ESC/POS bytes 생성
│   └── printer.py       # Print Spooler RAW 어댑터
├── tools/
│   ├── list_printers.py # 설치된 프린터 목록
│   └── print_sample.py  # 가짜 페이로드 → 출력
└── sample_payloads/
    ├── kitchen_basic.json
    ├── counter_basic.json
    └── kitchen_long.json
```
