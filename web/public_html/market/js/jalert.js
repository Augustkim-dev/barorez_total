function jalert(c, t = "", a = "") {
    $.alert({
        title: "<img src=\"https://wowc.dmonster.co.kr/img/logo.svg\" height=\"40px\" alt=\"logo\">"+t,
        content: c,
        buttons: {
            confirm: {
                text: "확인",
                action: function () {
                    a;
                },
            },
        },
    });
}

function jalert_url(c, u, t = "", f = "") {
    $.alert({
        title: "<img src=\"https://wowc.dmonster.co.kr/img/logo.svg\" height=\"40px\" alt=\"logo\">"+t,
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

function jalert_url_rpc(c, u, t = "") {
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
                    } else {
                        location.replace(u);
                    }
                },
            },
        },
    });
}