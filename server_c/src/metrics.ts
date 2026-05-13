/**
 * 운영 가시성을 위한 단순 카운터.
 * - 최근 1시간 내 에러 카운트 (슬라이딩 윈도우)
 * - /health 에서 노출
 *
 * Prometheus / OpenTelemetry 도입은 Phase 6 으로 미룸 (PRD §12.2 참고).
 */

const ERROR_WINDOW_MS = 60 * 60 * 1000;
const errorEvents: number[] = []; // 발생 시각(ms) 만 보관

export function recordError(_eventType: string): void {
  const now = Date.now();
  errorEvents.push(now);
  // 1시간 지난 항목 정리 (lazy)
  while (errorEvents.length > 0 && now - (errorEvents[0] ?? 0) > ERROR_WINDOW_MS) {
    errorEvents.shift();
  }
}

export function getErrorCountLastHour(): number {
  const cutoff = Date.now() - ERROR_WINDOW_MS;
  while (errorEvents.length > 0 && (errorEvents[0] ?? 0) < cutoff) {
    errorEvents.shift();
  }
  return errorEvents.length;
}
