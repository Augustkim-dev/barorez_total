// 아이디 검증: 영소문자 + 숫자 필수 포함, 6~16자
function validateId(idValue) {
    const regex = /^(?=.*[a-z])(?=.*[0-9])[a-z0-9]{6,16}$/;
    if (!idValue) {
        return { valid: false, msg: '아이디를 입력해 주세요.' };
    }
    if (!regex.test(idValue)) {
        return { valid: false, msg: '아이디는 6~16자의 영문 소문자와 숫자를 조합해야 합니다.' };
    }
    return { valid: true, msg: '사용 가능한 아이디입니다.' };
}

// 비밀번호 검증: 영문 + 숫자 필수, 특수문자 선택, 8~16자
function validatePassword(pwValue, pwConfirmValue = null) {
    const regex = /^(?=.*[a-zA-Z])(?=.*\d)[A-Za-z\d!@#$%^&*()_+\-=]{8,16}$/;

    if (!pwValue) {
        return { valid: false, msg: '비밀번호를 입력해 주세요.' };
    }
    if (!regex.test(pwValue)) {
        return { valid: false, msg: '비밀번호는 8~16자의 영문, 숫자 조합(특수문자 선택)을 사용해야 합니다.' };
    }
    if (pwConfirmValue !== null && pwValue !== pwConfirmValue) {
        return { valid: false, msg: '비밀번호가 일치하지 않습니다.' };
    }
    return { valid: true, msg: '사용 가능한 비밀번호입니다.' };
}

// 이름 검증: 빈 값만 체크 (한글/영문 모두 허용)
function validateName(nameValue) {
    if (!nameValue || nameValue.trim() === '') {
        return { valid: false, msg: '이름을 입력해 주세요.' };
    }
    return { valid: true, msg: '' };
}

// 휴대폰번호 검증: 숫자만, 10~11자 (01012345678 또는 0111234567 등)
function validateHp(hpValue) {
    const cleaned = (hpValue || '').replace(/[^0-9]/g, '');
    if (!cleaned) {
        return { valid: false, msg: '휴대폰번호를 입력해 주세요.' };
    }
    if (!/^(010|011|016|017|018|019)[0-9]{7,8}$/.test(cleaned)) {
        return { valid: false, msg: '올바른 휴대폰번호 형식이 아닙니다.' };
    }
    return { valid: true, msg: '유효한 휴대폰번호입니다.', cleaned };
}

// 전역 노출
window.ValidationUtils = {
    validateId,
    validatePassword,
    validateName,
    validateHp
};
