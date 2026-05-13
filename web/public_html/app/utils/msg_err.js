// 에러 표시
function showError($wrap, $errorEl, message) {
    if ($wrap && $wrap.length) {
        $wrap.removeClass('ip_valid').addClass('ip_invalid');
    }
    if ($errorEl && $errorEl.length) {
        $errorEl.text(message).show();
    }
}

// 성공 표시
function clearError($wrap, $errorEl) {
    if ($wrap && $wrap.length) {
        $wrap.removeClass('ip_invalid').addClass('ip_valid');
    }
    if ($errorEl && $errorEl.length) {
        $errorEl.hide();
    }
}

// 중립 상태
function resetFieldState($wrap, $errorEl) {
    if ($wrap && $wrap.length) {
        $wrap.removeClass('ip_invalid ip_valid');
    }
    if ($errorEl && $errorEl.length) {
        $errorEl.hide();
    }
}
