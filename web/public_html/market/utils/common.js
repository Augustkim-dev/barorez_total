const validatePwRule = (pw) => {
    // ✅ 영소문 1개 이상 + 숫자 1개 이상 + 8~16자
    const hasLower = /[a-z]/.test(pw);
    const hasNumber = /[0-9]/.test(pw);
    const lenOk = pw.length >= 8 && pw.length <= 16;
    return hasLower && hasNumber && lenOk;
};
