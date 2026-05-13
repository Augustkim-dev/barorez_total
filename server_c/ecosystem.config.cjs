// PM2 ecosystem 설정 — Phase 2 D011
//
// 사용:
//   cd server_c
//   npm install --omit=dev   # 운영에서는 devDeps 미설치
//   npm run build             # tsc → dist/
//   pm2 start ecosystem.config.cjs
//   pm2 save && pm2 startup   # 부팅 시 자동 기동 등록 (root 권한 필요)
//
// 참고:
//   - .env 는 cwd 기준으로 읽음. 운영 .env 는 server_c/.env 로 배포 (.gitignore).
//   - fork 모드 1 인스턴스 (cluster 아님) — SQLite WAL 단일 프로세스 가정.
//   - max_memory_restart: 메모리 누수 보호. 100매장/1000연결 기준 충분.
//   - PM2 의 자동 재시작 + Server C 자체 SQLite 영속화로 미완료 작업 복구.

module.exports = {
  apps: [
    {
      name: 'barorez-server-c',
      script: 'dist/index.js',
      cwd: __dirname,
      exec_mode: 'fork',
      instances: 1,
      autorestart: true,
      max_memory_restart: '512M',
      // 종료 시그널 전달 시 graceful shutdown 으로 SIGTERM 수신
      kill_timeout: 10_000,
      env: {
        NODE_ENV: 'production',
      },
      // PM2 가 stdout / stderr 캡처. pino 가 동시에 logs/ 폴더에 일별 파일도 기록.
      out_file: './logs/pm2-out.log',
      error_file: './logs/pm2-err.log',
      log_date_format: 'YYYY-MM-DD HH:mm:ss',
      merge_logs: true,
    },
  ],
};
