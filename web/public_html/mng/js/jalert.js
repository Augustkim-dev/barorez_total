jconfirm.defaults = {
    scrollToPreviousElement: false,
    scrollToPreviousElementAnimate: false,
};

function jalert(c, t = "", a = "") {
    $.alert({
        title: t,
        content: c,
        buttons: {
            confirm: {
                text: "확인",
                action: function () {
                    if (a) {
                        a;
                    }
                },
            },
        },
    });
}

function jalert_url(c, u, t = "", f = "") {
    $.alert({
        title: t,
        content: c,
        buttons: {
            confirm: {
                text: "확인",
                action: function () {
                    if (u == "back") {
                        history.go(-1);
                    } else if (u == "reload") {
                        location.hash = "";
                        location.reload();
                    } else if (u == "focus") {
                        $(f).focus();
                    } else {
                        if (u == "function") {
                            document.write(f);
                        } else {
                            location.replace(u);
                        }
                    }
                },
            },
        },
    });
}

function jalert_focus(c, t = "", i = "") {
    $.alert({
        title: t,
        content: c,
        buttons: {
            confirm: {
                text: "확인",
            },
        },
        onClose: function () {
            $("#" + i).focus();
        },
    });
}

function jconfirm1(c, t = "", a = "", v = "") {
    $.confirm({
        title: t,
        content: c,
        buttons: {
            cancel: {
                text: "취소",
                btnClass: "btn-outline-light",
            },
            confirm: {
                text: "확인",
                btnClass: "btn-primary",
                action: function () {
                    if (a) {
                        a(v);
                    }
                },
            },
        },
    });

    return false;
}
