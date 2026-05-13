var eng_num = /[^a-zA-Z0-9_-]/g;
var eng_kor = /[^a-zA-Zㄱ-ㅎ가-힣]/g;
var eng_kor_num = /[^a-zA-Zㄱ-ㅎ가-힣0-9]/g;
var num = /[^0-9]/g;
var eng = /[^a-zA-Z]/g;
var kor = /[ㄱ-ㅎ가-힣]/g;
var email = /[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,3}$/i; ;
var emailf = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/;
var password = /^.*(?=.{6,20})(?=.*[0-9])(?=.*[a-zA-Z]).*$/;
var space = /\s/g;

function del(url) {
    if(confirm("정말 삭제하시겠습니까? 삭제된 자료는 복구되지 않습니다.")) {
        hidden_ifrm.location.href = url;
    }

    return false;
}

function f_post_del(url, idx, aidx, callback) {
    $.confirm({
        title: '자료 삭제',
        content: "정말 삭제하시겠습니까?<br/>삭제된 자료는 복구되지 않습니다.",
        buttons: {
            cancel: {
                text: "취소",
                btnClass: "btn-outline-light",
            },
            confirm: {
                text: "확인",
                btnClass: "btn-primary",
                action: function () {
                    $.post(url, {act: 'delete', idx: idx}, function (data) {
                        console.log(data);
                        if($.trim(data)=='Y') {
                            if (callback) {
                                callback(aidx);
                            } else {
                                jalert_url('삭제되었습니다.', 'reload');
                            }
                        } else {
                            // data = JSON.parse(data);
                            // console.log(data)
                            if(data.success){
                                app.toastr.showSuccess(data.message, data.redirect);
                            } else {
                                app.toastr.showError(data.message);
                            }
                        }
                    });
                },
            },
        },
    });

    return false;
}


function f_post_act_del(url, idx, act) {
    $.confirm({
        title: '자료 삭제',
        content: "정말 삭제하시겠습니까?<br/>삭제된 자료는 복구되지 않습니다.",
        buttons: {
            cancel: {
                text: "취소",
                btnClass: "btn-outline-light",
            },
            confirm: {
                text: "확인",
                btnClass: "btn-primary",
                action: function () {
                    $.post(url, {act: act, idx: idx}, function (data) {
                        // console.log(data);
                        if(data.success){
                            app.toastr.showSuccess(data.message, data.redirect);
                        } else {
                            app.toastr.showError(data.message);
                        }
                    });
                },
            },
        },
    });

    return false;
}

function f_post_del_toast(url, idx, aidx, callback) {
    if (toast_confirm) {
        $('#btn_toast_confirm_ok').attr('onclick', "f_post_del('"+url+"', '"+idx+"', '"+aidx+"', "+callback+")");
        $('#toast_confirm .txt').html("정말로 삭제하시겠습니까?<br/>삭제된 자료는 복구되지 않습니다.");
        toast_confirm.show();
    }

    return false;
}
function f_reset_toast(frm_name="") {
    if (toast_confirm) {
        console.log('f_reset_toast', frm_name);
        if (frm_name) {
            $('#btn_toast_confirm_ok').attr('onclick', "document.getElementById('"+frm_name+"').reset()");
        } else {
            $('#btn_toast_confirm_ok').attr('onclick', "history.back()");
        }
        $('#toast_confirm .txt').text("작성중인 내용을 취소하시겠습니까?");
        toast_confirm.show();
    }

    return false;
}

function retire(url) {
    if(confirm("정말 탈퇴하시겠습니까?")) {
        hidden_ifrm.location.href = url;
    }

    return false;
}

function update_confirm(txt, url) {
    if(confirm(txt)) {
        hidden_ifrm.location.href = url;
    }

    return false;
}

function comma(num) {
    //return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return String(num).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function uncomma(num) {
    //return num.toString().replace(/[^\d]+/g, '');
    return String(num).replace(/[^\d]+/g, '');
}
function setComma(obj) {
    obj.value = comma(uncomma(obj.value));
}
//숫자만 입력
function showKeyCode(event) {
    event = event || window.event;
    var keyID = (event.which) ? event.which : event.keyCode;
    if ( ( keyID >=48 && keyID <= 57 ) || ( keyID >=96 && keyID <= 105 ) || keyID == 8 || keyID == 46 || keyID == 37 || keyID == 39 || keyID == 116  || keyID == 9 ) {
        return;
    } else {
        event.value = "";
        return false;
    }
}
//숫자,-만 입력
function showKeyCodea(event) {
    event = event || window.event;
    var keyID = (event.which) ? event.which : event.keyCode;
    if ( ( keyID >=48 && keyID <= 57 ) || ( keyID >=96 && keyID <= 105 ) || keyID == 8 || keyID == 46 || keyID == 37 || keyID == 39 || keyID == 116 || keyID == 9 || keyID == 189 || keyID == 173) {
        return;
    } else {
        return false;
    }
}
//숫자,.만 입력
function showKeyCodeb(event) {
    event = event || window.event;
    var keyID = (event.which) ? event.which : event.keyCode;
    if ( ( keyID >=48 && keyID <= 57 ) || ( keyID >=96 && keyID <= 105 ) || keyID == 8 || keyID == 46 || keyID == 37 || keyID == 39 || keyID == 116 || keyID == 9 || keyID == 110 || keyID == 190 ) {
        return;
    } else {
        event.value = "";
        return false;
    }
}

function chkSpecialChar(obj){ // 특수문자 제거
    var RegExp = /[\{\}\[\]\/;:|\)*~`^┼<>@\#$%&\'\"\\\(\=]/gi;	//정규식 구문
    if (RegExp.test(obj.value)) {
        // 특수문자 모두 제거
        obj.value = obj.value.replace(RegExp , '');
    }
}
function phoneFomatter(obj,type){
    var num;
    var formatNum = '';
    num = obj.value.replace(/\-/g,'');
    if(num.length==11){
        if(type==0){
            formatNum = num.replace(/(\d{3})(\d{4})(\d{4})/, '$1-****-$3');
        }else{
            formatNum = num.replace(/(\d{3})(\d{4})(\d{4})/, '$1-$2-$3');
        }
    }else if(num.length==8){
        formatNum = num.replace(/(\d{4})(\d{4})/, '$1-$2');
    }else{
        if(num.indexOf('02')==0){
            if(num.length==10){
                if(type==0){
                    formatNum = num.replace(/(\d{2})(\d{4})(\d{4})/, '$1-****-$3');
                }else{
                    formatNum = num.replace(/(\d{2})(\d{4})(\d{4})/, '$1-$2-$3');
                }
            } else {
                if(type==0){
                    formatNum = num.replace(/(\d{2})(\d{3})(\d{4})/, '$1-****-$3');
                }else{
                    formatNum = num.replace(/(\d{2})(\d{3})(\d{4})/, '$1-$2-$3');
                }
            }
        }else{
            if(type==0){
                formatNum = num.replace(/(\d{3})(\d{3})(\d{4})/, '$1-***-$3');
            }else{
                formatNum = num.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
            }
        }
    }
    obj.value = formatNum;
}

//null처리
function conNull(v) {
    if(!v) v = "";
    return v;
}

function get_text_length(str, obj) {
    var len = 0;

    for(var i = 0;i < str.length;i++) {
        if(escape(str.charAt(i)).length==6) {
            len++;
        }
        len++;
    }

    if(len>0) {
        $(obj).html(len);
    }

    return false;
}

// 글숫자 검사
function check_byte(content, target) {
    var i = 0;
    var cnt = 0;
    var ch = '';
    var cont = document.getElementById(content).value;

    for (i=0; i<cont.length; i++) {
        ch = cont.charAt(i);
        if (escape(ch).length > 4) {
            cnt += 2;
        } else {
            cnt += 1;
        }
    }
    // 숫자를 출력
    document.getElementById(target).innerHTML = cnt;

    return cnt;
}

function f_checkbox_all(obj,name) {
    /*$('input:checkbox[name="'+obj+'[]"]').each(function() {
        if($(this).prop('checked')==true) {
            $(this).prop('checked', false);
        } else {
            $(this).prop('checked', true);
        }
    });*/
    if($(obj).prop('checked')==true) {
        $('input:checkbox[name="'+name+'[]"]').each(function() {
            $(this).prop('checked', true);
        });
    } else {
        $('input:checkbox[name="'+name+'[]"]').each(function() {
            $(this).prop('checked', false);
        });
    }

    return false;
}

function f_checkbox_each(name) {
    var count = 0;
    $('input:checkbox[name="'+name+'[]"]').each(function() {
        if($(this).prop('checked')==false) {
            count++;
        }
    });

    if(count==0) {
        $('#'+name+'_all').prop('checked', true);
    } else {
        $('#'+name+'_all').prop('checked', false);
    }

    return false;
}

function setCookie(cName, cValue, cDay) {
    var expire = new Date();
    expire.setDate(expire.getDate() + cDay);
    cookies = cName + '=' + escape(cValue) + '; path=/ ';
    if(typeof cDay != 'undefined') cookies += ';expires=' + expire.toGMTString() + ';';
    document.cookie = cookies;
}
function getCookie(cName) {
    cName = cName + '=';
    var cookieData = document.cookie;
    var start = cookieData.indexOf(cName);
    var cValue = '';
    if(start != -1){
        start += cName.length;
        var end = cookieData.indexOf(';', start);
        if(end == -1)end = cookieData.length;
        cValue = cookieData.substring(start, end);
    }
    return unescape(cValue);
}

function popup(url, wval, hval, tval, lval) {
    window.open(url,'popup','height='+hval+',width='+wval+',top='+tval+',left='+lval+',menubar=no,scrollbars=no,status=yes');
}

function gourl(url){
    if(url!= "") window.open(url);
}

function f_toast_show(toast, toastid, txt){
    if (toast) {
        $('#'+toastid+' .txt').text(txt);
        toast.show();
    } else {
        jalert(txt);
    }
}

var timer = null;
var isRunning = false;
function f_m_hp_chk(chk_type='') {
    if($('#mt_hp').val()=="") {
        if ($('span[for="mt_hp"]').length) {
            $('span[for="mt_hp"]').html('<span id="mt_hp-error" class="errText">휴대폰번호를 입력해 주세요. (\'-\'없이 숫자만)</span>');
        } else {
            var ttxt = "휴대폰번호를 입력해 주세요.";
            f_toast_show(toast_check, 'toast_check', ttxt);
        }
        $('#mt_hp').focus();
        return false;
    }

    if($('#mt_hp').val().length < 8) {
        var ttxt = "휴대폰번호는 8자리이상 입력되어야 합니다.";
        if ($('span[for="mt_hp"]').length) {
            $('span[for="mt_hp"]').html('<span id="mt_hp-error" class="errText">'+ttxt+'</span>');
        } else {
            f_toast_show(toast_check, 'toast_check', ttxt);
        }
        $('#mt_hp').focus();
        return false;
    }

    $("#mt_hp").removeClass('is-valid');

    if (typeof f_hp_change === 'function') {
        f_hp_change('N');
    }

    $('#mt_hp_confirm').prop('disabled', false);
    $('#m_hp_confirm_btn').prop('disabled', false);
    $('.mt_hp_chk.scsText').text('').css({'display': 'none'});

    $.post('./join_update.php', {act: 'chk_mt_hp', mt_hp: $('#mt_hp').val(), mt_id: $('#mt_id').val(), chk_type: chk_type}, function (data) {
        console.log(data);
        if($.trim(data)=='Y') {
            if ($("#mt_hp-error").length) {
                $('span[for="mt_hp"]').html('');
            }
            if ($("#mt_hp_chk-error").length) {
                $('span[for="mt_hp_chk"]').html('');
            }

            if (isRunning){
                clearInterval(timer);
                $("#m_hp_confirm_timer").hide();
                set_timer();
            }else{
                set_timer();
            }
        } else {
            data = JSON.parse(data);
            if (data.msg) {
                if ($('span[for="mt_hp"]').length) {
                    $('span[for="mt_hp"]').html('<span id="mt_hp-error" class="errText">'+data.msg+'</span>');
                } else {
                    f_toast_show(toast_check, 'toast_check', data.msg);
                }
                $('#mt_hp_confirm').prop('disabled', true);
                $('#m_hp_confirm_btn').prop('disabled', true);
                return false;
            } else {
                if ($("#mt_hp-error").length) {
                    $('span[for="mt_hp"]').html('');
                }
                if ($("#mt_hp_chk-error").length) {
                    $('span[for="mt_hp_chk"]').html('');
                }
            }
        }
    });

    return false;
}

function set_timer() {
    var time = 180;
    var min = "";
    var sec = "";
    $("#m_hp_chk_btn").prop("disabled", true);
    //$("#m_hp_chk_btn").css("background-color", "#e9ecef");
    //$("#m_hp_chk_btn").css("border-color", "#e9ecef");
    //$("#m_hp_chk_btn").css("color", "#222222");
    $("#mt_hp").prop("readonly", true);
    //$("#mt_hp").css("background-color", "#e9ecef");
    timer = setInterval(function () {
        min = parseInt(time / 60);
        sec = time % 60;
        $("#certi_hp").show();
        $("#m_hp_confirm_timer").show();
        document.getElementById("m_hp_confirm_timer").innerHTML = ""+(min.toString().length === 1 ? '0'+min : min)+":"+(sec.toString().length === 1 ? '0'+sec : sec)+"";
        time--;
        if(time<-1) {
            var ttxt = "인증번호 유효시간이 만료되었습니다.";
            f_toast_show(toast_check, 'toast_check', ttxt);
            clearInterval(timer);
            $("#certi_hp").hide();
            $('#mt_hp_confirm').val('');
            $('#mt_hp_confirm').prop('disabled', true);
            $('#m_hp_confirm_btn').prop('disabled', true);
            $("#m_hp_confirm_timer").hide();
            $("#m_hp_chk_btn").prop("disabled", false);
            //$("#m_hp_chk_btn").css("background-color", "#0091ea");
            //$("#m_hp_chk_btn").css("border-color", "#0091ea");
            //$("#m_hp_chk_btn").css("color", "#ffffff");
            $("#mt_hp").prop("readonly", false);
        }
    }, 1000);
    isRunning = true;
}

function f_hp_confirm() {
    if($('#mt_hp_confirm').val()=="") {
        var ttxt = "인증번호를 입력해주세요.";
        if ($('span[for="mt_hp_chk"]').length) {
            $('span[for="mt_hp_chk"]').html('<span id="mt_hp_chk-error" class="errText">'+ttxt+'</span>');
        } else {
            f_toast_show(toast_check, 'toast_check', ttxt);
        }
        $('#mt_hp_confirm').focus();
        return false;
    }

    $.post('./join_update.php', {act: 'confirm_mt_hp', mt_hp: $('#mt_hp').val(), mt_hp_confirm: $('#mt_hp_confirm').val()}, function (data) {
        if($.trim(data)=='Y') {
            var ttxt = "인증이 확인되었습니다.";
            f_toast_show(toast_success, 'toast_success', ttxt);
            $('.mt_hp_chk.scsText').text(ttxt).css({'display': 'block'});

            clearInterval(timer);
            $('#mt_hp_chk').val('Y');
            $("#certi_hp").hide();
            $("#m_hp_confirm_timer").hide();
            $('#mt_hp_confirm').prop('disabled', true);
            $("#m_hp_confirm_btn").prop("disabled", true);
            $("#m_hp_chk_btn").prop("disabled", true);
            $("#mt_hp").prop("readonly", true);
            $("#mt_hp").addClass('is-valid');
            if ($("#mt_hp-error").length) {
                $('span[for="mt_hp"]').html('');
            }
            if ($("#mt_hp_chk-error").length) {
                $('span[for="mt_hp_chk"]').html('');
            }

            if (typeof f_hp_change === 'function') {
                f_hp_change('Y');
            }
        } else {
            var ttxt = "인증이 확인되지 않습니다. 인증문자를 확인바랍니다.";
            if ($('span[for="mt_hp_chk"]').length) {
                $('span[for="mt_hp_chk"]').html('<span id="mt_hp_chk-error" class="errText">'+ttxt+'</span>');
            } else {
                f_toast_show(toast_check, 'toast_check', ttxt);
            }

            if (typeof f_hp_change === 'function') {
                f_hp_change('N');
            }
        }
    });

    return false;
}

jQuery(function ($) {
    //$(document).on("keyup", "input:text[numberOnly]", function() {$(this).val( $(this).val().replace(/[^0-9]/gi,"") );});
    $(document).on("keyup", "input[numberOnly]", function() {$(this).val( $(this).val().replace(/[^0-9]/gi,"") );});
    $(document).on("keyup", "input[abcOnly]", function() {$(this).val( $(this).val().replace(/[^a-zA-Z0-9]/gi,"") );});
    $(document).on("keyup", "input[datetimeOnly]", function() {$(this).val( $(this).val().replace(/[^0-9:\-]/gi,"") );});
    $(document).on("keyup", "input[abcOnlySmall]", function() {$(this).val( $(this).val().replace(/[^a-z0-9_]/gi,"") );});
    $(document).on("keyup", "input[abcdOnlySmall]", function() {$(this).val( $(this).val().replace(/[^a-z]/gi,"") );});
    $(document).on("keyup", "input[priceOnly]", function() {$(this).val( $(this).val().replace(/[^0-9,]/gi,"") );});
    $(document).on("keyup", "input[numberHypenOnly]", function() {$(this).val( $(this).val().replace(/[^0-9\-]/gi,"") );});

    //이미지 드래그 방지
    $(document).on('dragstart', 'img', function(event) { event.preventDefault(); });
    //$("#m_hp_confirm_btn").removeClass("disabled").removeClass('btn-secondary').addClass('btn-primary');

    $("#mt_id").filter(".lower").on("keyup", function() {
        $(this).val($(this).val().toLowerCase());
    });
    $('#mt_id, #mt_name, #mt_nick, #mt_email').on('keyup', function(){
        $(this).val($(this).val().replace(' ',''));
    });

    $(document).on('change', 'input[name="mt_hp"]', function(e) {
        if ($(document).find('#m_hp_chk_btn').length) {
            $('#mt_hp_chk').val('');
            $("#certi_hp").hide();
            clearInterval(timer);
            $("#m_hp_confirm_timer").hide();
            $("#m_hp_chk_btn").prop("disabled", false);
            $("#mt_hp").prop("readonly", false);
            $('#mt_hp').removeClass('is-valid');
            $('.mt_hp_chk.scsText').text('').css({'display': 'none'});
        }
    });
    $(document).on('click', '.pass_chk', function(e) {
        e.preventDefault();
        if ($(this).hasClass('on')) {
            $(this).parent().find('input').attr("type", "password");
            $(this).html('<i class="xi-eye-off"></i>');
        } else {
            $(this).parent().find('input').attr("type", "text");
            $(this).html('<i class="xi-eye"></i>');
        }
        $(this).toggleClass('on');
    });

    $(document).on("click", '.filebox .btn_remove', function (evt) {
        evt.preventDefault();
        evt.stopPropagation();
        var ele = $(this).parent();
        var id = ele.find('input[type="file"]').attr('id');

        ele.find('input[type="file"]').val('');
        if (ele.find('.file_box').length) {
            ele.find('.file_box').html('<i class="xi-plus"></i>').css({'background-image': 'url()', 'border': '1px dashed #B2B2B1'});
        }
        ele.find('.file_on').val('');
        ele.find('.del').val('1');
        ele.find('.btn_remove').hide();

        $(document).find('#'+id+'_chk').val('');
        if ($(document).find('.file_cnt[data-id="'+id+'"]').length) {
            var file_cnt = $(document).find('.file_cnt[data-id="'+id+'"]');
            var file_ele = $(document).find('input[type="file"][name^="'+id+'"]');
            var cnt = 0;
            file_ele.each(function(){
                if ($(this).val()) {
                    cnt++;
                }
            });
            file_cnt.text('('+cnt+'/'+file_ele.length+')');
        }
    });

    $(document).on('input', 'textarea, .input-textarea', function(){
        $(this).parent().find('.text_count').text($(this).val().length);
    });
    $('textarea[maxlength]').on('keyup keydown change paste', function() {
        var max = $(this).attr('maxlength');
        var $this = $(this);
        if ($this.val().length > max) {
            $this.val($this.val().substring(0, max));
        }
    });

    //------------------------------------------------------------------------------------------------------------------
    $('input[name="sch_name"]').on("keypress", function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            f_relation_product();
        }
    });
    $(document).on("click", ".btn_search_item", function() {
        f_relation_product();
    });

    $(document).on("click", ".relation .add_item", function() {
        // 이미 등록된 상품인지 체크
        var id = $(this).closest(".relation").data('id');
        var type = $(this).closest(".relation").data('type');
        var $li = $(this).closest("li");
        var pt_idx = $li.find("input:hidden").val();
        var pt_idx2;
        var dup = false;
        $('.reg_relation[data-id="'+id+'"] input[name="re_pt_idx[]"]').each(function() {
            pt_idx2 = $(this).val();
            if(pt_idx == pt_idx2) {
                dup = true;
                return false;
            }
        });

        if(dup) {
            jalert("이미 선택된 상품입니다.");
            return false;
        }

        //var cont = "<li class=\"list-group-item\">"+$li.html().replace("add_item", "del_item").replace("추가", "삭제")+"</li>";
        var count = $('.reg_relation[data-id="'+id+'"] .list_it').length;
        $.post(get_url + '/proc.php', {act: 'get_rel_product_slc', pt_idx: pt_idx}, function (data) {
            data = JSON.parse(data);
            if(data) {
                if (type==='multi') {
                    if(count > 0) {
                        $('.reg_relation[data-id="'+id+'"] .list_it:last').after(data.result);
                    } else {
                        $('.reg_relation[data-id="'+id+'"]').append(data.result);
                    }
                } else {
                    $('.reg_relation[data-id="'+id+'"]').append(data.result);
                }
            }
        });

        const sch_item_len = $('.relation[data-id="'+id+'"]').find('li').length;

        $li.remove();
        if (sch_item_len <= 1 ) {
            $('.relation[data-id="'+id+'"]').html('');
        }
    });

    $(document).on("click", ".reg_relation .del_item", function() {
        const $target = $(this);

        $.confirm({
            title: '',
            content: '상품을 삭제하시겠습니까?',
            buttons: {
                confirm: {
                    text: '확인',
                    action: function() {
                        $target.closest(".list_it").remove();

                        var count = $(".reg_relation .list_it").length;
                        if(count < 1)
                            $(".reg_relation").html("");
                    }
                },
                cancel: {
                    text: '취소',
                    //close
                },
            }
        });
    });
});

function f_relation_product(){
    var srel = $(".btn_search_item").closest('.srel');
    var tbl = $(".btn_search_item").data('tbl');
    //var seller_idx = $(".btn_search_item").data('seller_idx');
    var ct_id = '';//srel.find(".sch_relation").val();
    if (srel.find('select#sel_ct_id1').length) {
        ct_id = srel.find('select#sel_ct_id1').val();
    }
    var search_txt = $.trim(srel.find('input[name="sch_name"]').val());
    var $relation = srel.find(".relation");

    if (srel.find('select#sel_ct_id1').length) {
        if(ct_id == "" && search_txt == "") {
            $relation.html("상품의 분류를 선택하시거나 상품명을 입력하신 후 검색하세요.");
            return false;
        }
    } else {
        if(search_txt == "") {
            $relation.html("");//상품의 분류를 선택하시거나 상품명을 입력하신 후 검색하세요.
            return false;
        }
    }

    $.post(get_url + '/proc.php', {act: 'get_rel_product', tbl: tbl, ct_id: ct_id, search_txt: search_txt}, function (data) {
        data = JSON.parse(data);
        if(data) {
            $relation.html(data.result);
        }
    });
}

//--------------------------------------------------------------------------------------------------
function excelform(url){
    var opt = "width=600,height=450,left=10,top=10";
    window.open(url, "win_excel", opt);
    return false;
}

/**
 * 우편번호 창
 **/
var win_zip = function(zip_case, frm_name, frm_zip, frm_addr1, frm_addr2, frm_addr3, frm_jibeon, frm_lat, frm_lng, dataParse, i, callbackfunc) {
    if(typeof daum === 'undefined'){
        alert("다음 우편번호 postcode.v2.js 파일이 로드되지 않았습니다.");
        return false;
    }

    if (!zip_case || zip_case!=='0') {
        zip_case = 1;   //0이면 레이어, 1이면 페이지에 끼워 넣기, 2이면 새창
    }
    //console.log(zip_case);

    var complete_fn = function(data){
        // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

        // 각 주소의 노출 규칙에 따라 주소를 조합한다.
        // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
        var fullAddr = ''; // 최종 주소 변수
        var extraAddr = ''; // 조합형 주소 변수

        // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
        if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
            fullAddr = data.roadAddress;

        } else { // 사용자가 지번 주소를 선택했을 경우(J)
            fullAddr = data.jibunAddress;
        }

        // 사용자가 선택한 주소가 도로명 타입일때 조합한다.
        if(data.userSelectedType === 'R'){
            //법정동명이 있을 경우 추가한다.
            if(data.bname !== ''){
                extraAddr += data.bname;
            }
            // 건물명이 있을 경우 추가한다.
            if(data.buildingName !== ''){
                extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
            }
            // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
            extraAddr = (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
        }

        console.log(data);
        var addrStr = data.sido + ':' + data.sigungu;
        // if (data.bname1) { addrStr += '|' + data.bname1; }
        // if (data.bname2) { addrStr += '|' + data.bname2; }

        // 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
        var of = document[frm_name];

        if(of[frm_zip] !== undefined){
            of[frm_zip].value = data.zonecode;
        }

        if(of[frm_addr1] !== undefined){
            of[frm_addr1].value = fullAddr;
        }

        if(of[frm_addr3] !== undefined){
            of[frm_addr3].value = extraAddr;
        }

        if(of[frm_jibeon] !== undefined){
            // of[frm_jibeon].value = data.userSelectedType;
            of[frm_jibeon].value = addrStr;
        }

        if(of[frm_addr2] !== undefined){
            of[frm_addr2].value = '';
            // of[frm_addr2].focus();
        }

        if (of[frm_lat] && of[frm_lng]) {
            // 주소-좌표 변환 객체를 생성합니다
            var geocoder = new daum.maps.services.Geocoder();
            var callback = function(result, status) {
                if (status === kakao.maps.services.Status.OK) {
                    console.log(result);
                    of[frm_lat].value = result[0].y;
                    of[frm_lng].value = result[0].x;
                }
            };
            geocoder.addressSearch(fullAddr, callback);
        }
        //------------------------------------------------------------------------------------------
        // if (!i || i === undefined || i === 'undefined') { i = ""; }
        if (dataParse) {
            console.log(data);

            var addrStr = data.sido + ':' + data.sigungu;
            // if (data.bname1) { addrStr += '|' + data.bname1; }
            // if (data.bname2) { addrStr += '|' + data.bname2; }
            if (of['sido_gugun'] !== undefined) {
                of['sido_gugun'].value = addrStr;
            }
            if (of['sido'] !== undefined) {
                var sido = data.sido;
                var sigungu = data.sigungu;
                sigungu = sigungu.split(' ')[0];
                of['sido'].value = sido;
                of['gugun'].value = sigungu;
            }
        }
        //------------------------------------------------------------------------------------------
        if ($('span[id="'+frm_addr1+'-error"]').length) {
            $('span[for="'+frm_addr1+'"]').html('');
        }
        if (callbackfunc) {
            callbackfunc();
        }
    };

    switch(zip_case) {
        case 1 :    //iframe을 이용하여 페이지에 끼워 넣기
            var daum_pape_id = 'daum_juso_page'+frm_zip+(!i || i === undefined || i === 'undefined' ? '' : i),
                element_wrap = document.getElementById(daum_pape_id),
                currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);
            if (element_wrap == null) {
                element_wrap = document.createElement("div");
                element_wrap.setAttribute("id", daum_pape_id);
                element_wrap.style.cssText = 'display:none;border:1px solid;left:0;width:100%;height:300px;max-height:500px;margin:5px 0;position:relative;clear: both;-webkit-overflow-scrolling:touch;';
                element_wrap.innerHTML = '<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnFoldWrap" style="cursor:pointer;position:absolute;right:0px;top:-21px;z-index:1" class="close_daum_juso" alt="접기 버튼">';
                if(jQuery('form[name="'+frm_name+'"]').find('input[name="'+frm_zip+'"]').length){
                    jQuery('form[name="'+frm_name+'"]').find('input[name="'+frm_zip+'"]').after(element_wrap);
                } else {
                    jQuery('form[name="'+frm_name+'"]').find('input[name="'+frm_addr1+'"]').before(element_wrap);
                }
                jQuery("#"+daum_pape_id).off("click", ".close_daum_juso").on("click", ".close_daum_juso", function(e){
                    e.preventDefault();
                    jQuery(this).parent().hide();
                });
            }

            new daum.Postcode({
                oncomplete: function(data) {
                    complete_fn(data);
                    // iframe을 넣은 element를 안보이게 한다.
                    element_wrap.style.display = 'none';
                    // 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
                    document.body.scrollTop = currentScroll;
                },
                // 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분.
                // iframe을 넣은 element의 높이값을 조정한다.
                onresize : function(size) {
                    element_wrap.style.height = size.height + "px";
                },
                width : '100%',
                height : '100%'
            }).embed(element_wrap);

            // iframe을 넣은 element를 보이게 한다.
            element_wrap.style.display = 'block';
            break;
        case 2 :    //새창으로 띄우기
            new daum.Postcode({
                oncomplete: function(data) {
                    complete_fn(data);
                }
            }).open();
            break;
        default :   //iframe을 이용하여 레이어 띄우기
            var rayer_id = 'daum_juso_rayer'+frm_zip,
                element_layer = document.getElementById(rayer_id);
            if (element_layer == null) {
                element_layer = document.createElement("div");
                element_layer.setAttribute("id", rayer_id);
                element_layer.style.cssText = 'display:none;border:5px solid;position:fixed;width:300px;height:460px;left:50%;margin-left:-155px;top:50%;margin-top:-235px;overflow:hidden;-webkit-overflow-scrolling:touch;z-index:10000';
                element_layer.innerHTML = '<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" class="close_daum_juso" alt="닫기 버튼">';
                document.body.appendChild(element_layer);
                jQuery("#"+rayer_id).off("click", ".close_daum_juso").on("click", ".close_daum_juso", function(e){
                    e.preventDefault();
                    jQuery(this).parent().hide();
                });
            }

            new daum.Postcode({
                oncomplete: function(data) {
                    complete_fn(data);
                    // iframe을 넣은 element를 안보이게 한다.
                    element_layer.style.display = 'none';
                },
                width : '100%',
                height : '100%'
            }).embed(element_layer);

            // iframe을 넣은 element를 보이게 한다.
            element_layer.style.display = 'block';
    }
}

//로딩 표시
function showOnLoading() {
    $.fancybox({content:'<div id="fancybox-loading"><div></div></div>',closeBtn:false,'modal':true});
    $(".fancybox-skin").css({"box-shadow":"none","background":"rgba(255,255,255,0)"});
}
//fancybox 팝업닫기
function close_fancybox() {
    $.fancybox.close();
}
//--------------------------------------------------------------------------------------------------

function layerPop(act, idx1, idx2, idx3, idx4) {
    if (act==='add_singo') {
        if (!is_member) {
            //jalert('로그인 후 이용가능합니다.');
            f_login_goto();
            return false;
        }
    }
    $.post(get_url+'/popup_form.php', {act: act, idx1: idx1, idx2: idx2, idx3: idx3, idx4: idx4}, function (data) {
        if(data) {
            $('#modal-default-content').html(data);
            $('#modal-default').removeClass('modal_bottom');
            $('#modal-default .modal-dialog').removeClass('modal-lg').removeClass('modal-md').removeClass('modal-sm');

            if (act==='member_leave' || act==='shop_coupon' || act==='addCart') {
                $('#modal-default').addClass('modal_bottom');
            } else if (act==='qna_confirm' || act==='delete_table') {
                $('#modal-default .modal-dialog').addClass('modal-sm');
            } else if (act==='cancel_modal'
                || act==='delivery_modal'
                || act==='ct_memo_update'
                || act==='add_singo' || act==='swiper_image'
                || act==='set_points' || act==='excel_form' || act==='set_category' || act==='rel_banner' || act==='rel_push'
                ||act==='set_store_hours' || act==='set_store_holiday'
            ) {
                $('#modal-default .modal-dialog').addClass('modal-md');
            } else {
                $('#modal-default .modal-dialog').addClass('modal-lg');
            }

            if (act==='swiper_image') {
                $('#modal-default-content').addClass('bg-transparent border-0');
            } else {
                $('#modal-default-content').removeClass('bg-transparent border-0');
            }

            $('#modal-default').modal();
        }
    });

    return false;
}

function imagePop(tbl, idx) {
    $.post(get_url+'/popup_form.php', {act: 'swiper_image', tbl: tbl, idx: idx}, function (data) {
        if(data) {
            $('#modal-default-content').html(data).removeClass('bg-transparent border-0');

            $('#product-swiper').slick({
                dots: true,
                infinite: false,
                speed: 300,
                variableWidth: true,
                slidesToShow: 1,
            });

            $('#modal-default').modal();
        }
    });

    return false;
}

function f_swiper(obj_id) {
    var swiper = null;
    if ($('#'+obj_id).find('.swiper-wrapper').length) {
        /* item slide Swiper 메인 상품슬라이드와 같음*/
        swiper = new Swiper('#'+obj_id+" .swiper", {
            slidesPerView: "auto",
            spaceBetween: 8,
            breakpoints: {
                767.98: {
                    spaceBetween: 15,
                },
            }
        });
        //swiper.update();
    }
    setTimeout(function () {
        if ($('#'+obj_id).find('.swiper-wrapper').length) {
            /* item slide Swiper 메인 상품슬라이드와 같음*/
            // swiper = new Swiper('#'+obj_id+" .swiper", {
            //     slidesPerView: "auto",
            //     spaceBetween: 8,
            //     breakpoints: {
            //         767.98: {
            //             spaceBetween: 15,
            //         },
            //     }
            // });
            swiper.update();
        }
    }, 200);
}

function f_get_box_list(pg, obj_frm) {
    if (obj_frm) {
        var obj_frm_t = "#" + obj_frm + " ";
    } else {
        var obj_frm_t = "#frm_list ";
    }

    if (pg == null || pg == "" || !pg) pg = 1;

    $(obj_frm_t + 'input[name="obj_pg"]').val(parseInt(pg));

    //console.log('f_get_box_list', obj_frm, pg);

    if (obj_frm) {
        var form_t = $("#" + obj_frm)[0];
    } else {
        var form_t = $("#" + $(obj_frm_t + 'input[name="obj_frm"]').val())[0];
    }
    var formData_t = new FormData(form_t);
    var current_pg = parseInt($(obj_frm_t + 'input[name="obj_pg"]').val());
    // if ($(obj_frm_t + "#obj_orderby").val()) {
    //     var orderby_t = parseInt($(obj_frm_t + "#obj_orderby").val());
    // } else {
    //     var orderby_t = "";
    // }

    $.ajax({
        url: $(obj_frm_t + 'input[name="obj_uri"]').val(),
        enctype: "multipart/form-data",
        data: formData_t,
        type: "POST",
        async: true,
        contentType: false,
        processData: false,
        cache: true,
        timeout: 5000,
        success: function (data) {
            if (data) {
                history.replaceState(
                    {
                        page: current_pg,
                    },
                    $(obj_frm_t + 'input[name="obj_list"]').val()
                );
                if ($(obj_frm_t + 'input[name="pgtype"]').val() == 'scroll') {
                    $("#" + $(obj_frm_t + 'input[name="obj_list"]').val()).append(data);
                } else {
                    $("#" + $(obj_frm_t + 'input[name="obj_list"]').val()).html(data);
                }
                if (typeof f_get_box_list_callback === 'function') {
                    f_get_box_list_callback(formData_t);
                }
            }
        },
        error: function (err) {
            console.log(err);
        },
    });

    return false;
}

//무한루프 더보기 리스트 처리를 위한 함수 & 뒤로가기 시 해당 리스트 기억
var total_list = '';
var windowWidth = $(window).width();

function f_get_box_more_list(pg, obj_frm, tt="") {
    if (obj_frm) {
        var obj_frm_t = "#" + obj_frm + " ";
    } else {
        var obj_frm_t = "#frm_list ";
    }

    if (pg == null || pg == "" || !pg) {
        pg = 1;
        $(obj_frm_t + 'input[name="location_reset"]').val('Y');
    }

    var history_data = history.state;

    var location_reset_t = $(obj_frm_t + 'input[name="location_reset"]').val();
    if(location_reset_t) {
        location.hash = '';
        history_data = '';
        total_list = '';
        pg = 1;
    }

    const $obj_pg = $(obj_frm_t + 'input[name="obj_pg"]');
    if(tt=='list_append') {
        //$(obj_frm_t + 'input[name="obj_act"]').val('list_append');
        $obj_pg.val(parseInt($obj_pg.val())+1);
    } else {
        $obj_pg.val(parseInt(pg));
    }

    if (obj_frm) {
        var form_t = $("#" + obj_frm)[0];
    } else {
        var form_t = $("#" + $(obj_frm_t + 'input[name="obj_frm"]').val())[0];
    }
    var formData_t = new FormData(form_t);
    var current_pg = parseInt($(obj_frm_t + 'input[name="obj_pg"]').val());

    if ($(obj_frm_t + 'input[name="orderby"]').val()) {
        var orderby_t = parseInt($(obj_frm_t + 'input[name="orderby"]').val());
    } else {
        var orderby_t = "";
    }
    if ($(obj_frm_t + 'input[name="ct_pid"]').val()) {
        var ct_pid_t = parseInt($(obj_frm_t + 'input[name="ct_pid"]').val());
    } else {
        var ct_pid_t = "";
    }
    if ($(obj_frm_t + 'input[name="ct_id"]').val()) {
        var ct_id_t = parseInt($(obj_frm_t + 'input[name="ct_id"]').val());
    } else {
        var ct_id_t = "";
    }
    // if(orderby_t) {
    //     $('.orderby_tab').removeClass('on');
    //     $('#orderby_tab'+orderby_t).addClass('on');
    // }
    // var search_txt_t = $(obj_frm_t + 'input[name="search_txt"]').val();

    $(obj_frm_t + '.more_btn').hide();

    var historydatacheck = false;

    if(tt!='list_append' && history_data) {// && location.hash && // && history_data.page !== 1
        if(history_data.list_data) {
            if($.trim(history_data.obj_frm_t) == $.trim(obj_frm_t)) {
                console.log('f_get_box_more_list', obj_frm_t);
                //if(windowWidth<576) {
                historydatacheck = true;
                //}
            }
        }
    } else {
        // if(tt!='list_append') {
        //     $('#'+$(obj_frm_t + 'input[name="obj_list"]').val()).html('<div class="d-flex justify-content-center align-items-center" style="height:300px;"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
        // }
    }
    console.log('history_data::', tt, '/', location.hash, '/', history_data, obj_frm_t, historydatacheck);

    if (historydatacheck) {
        total_list = history_data.list_data;
        $('#'+$(obj_frm_t + 'input[name="obj_list"]').val()).html(history_data.list_data);
        $obj_pg.val(history_data.page);
        if(!isNaN(history_data.orderby)) $(obj_frm_t + 'input[name="orderby"]').val(history_data.orderby);
        if(!isNaN(history_data.ct_id)) $(obj_frm_t + 'input[name="ct_id"]').val(history_data.ct_id);
        if(!isNaN(history_data.ct_pid)) $(obj_frm_t + 'input[name="ct_pid"]').val(history_data.ct_pid);
        if (typeof f_get_box_more_list_callback === 'function') {
            f_get_box_more_list_callback(formData_t);
        }
    } else {
        $.ajax({
            url: $(obj_frm_t + 'input[name="obj_uri"]').val(),
            enctype: "multipart/form-data",
            data: formData_t,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 5000,
            success: function(data){
                if(data) {
                    //if(windowWidth<576) {
                    total_list += data;
                    history.replaceState({list_data: total_list, page: current_pg, orderby: orderby_t, ct_id: ct_id_t, ct_pid: ct_pid_t, obj_frm_t: obj_frm_t}, $(obj_frm_t + 'input[name="obj_list"]').val(), location.pathname+location.search);
                    //} else {
                    //    history.replaceState({page: current_pg}, $(obj_frm_t + 'input[name="obj_list"]').val());
                    //}

                    if(tt=='list_append') {
                        $('#'+$(obj_frm_t + 'input[name="obj_list"]').val()).append(data);
                    } else {
                        $('#'+$(obj_frm_t + 'input[name="obj_list"]').val()).html(data);
                    }
                    if (typeof f_get_box_more_list_callback === 'function') {
                        f_get_box_more_list_callback(formData_t);
                    }
                }
            },
            error: function(err){
                console.log(err);
            }
        });
    }

    if(location_reset_t) {
        $(obj_frm_t + 'input[name="location_reset"]').val('');
    }

    return false;
}

function f_get_box_in_list(pg, obj_frm, tt="list_append") {
    if (obj_frm) {
        var obj_frm_t = "#" + obj_frm + " ";
    } else {
        var obj_frm_t = "#frm_list ";
    }

    if (pg == null || pg == "" || !pg) pg = 1;

    const $obj_pg = $(obj_frm_t + 'input[name="obj_pg"]');
    if(tt=='list_append') {
        $(obj_frm_t + 'input[name="obj_act"]').val('list_append');
        $obj_pg.val(parseInt($obj_pg.val())+1);
    } else {
        $obj_pg.val(parseInt(pg));
    }

    if (obj_frm) {
        var form_t = $("#" + obj_frm)[0];
    } else {
        var form_t = $("#" + $(obj_frm_t + 'input[name="obj_frm"]').val())[0];
    }
    var formData_t = new FormData(form_t);
    var current_pg = parseInt($(obj_frm_t + 'input[name="obj_pg"]').val());

    $.ajax({
        url: $(obj_frm_t + 'input[name="obj_uri"]').val(),
        enctype: "multipart/form-data",
        data: formData_t,
        type: "POST",
        async: true,
        contentType: false,
        processData: false,
        cache: true,
        timeout: 5000,
        success: function (data) {
            if (data) {
                if(tt=='list_append') {
                    $("#" + $(obj_frm_t + 'input[name="obj_list"]').val()).append(data);
                } else {
                    $("#" + $(obj_frm_t + 'input[name="obj_list"]').val()).html(data);
                }
            }
        },
        error: function (err) {
            console.log(err);
        },
    });

    return false;
}

function f_get_box_mng_list_reset(obj_frm = "") {
    if (obj_frm) {
        var obj_frm_t = obj_frm + " ";
    } else {
        var obj_frm_t = "frm_list ";
    }

    $("#" + obj_frm_t)[0].reset();

    f_get_box_mng_list("1");
}

function f_get_box_mng_list(pg = "") {
    var form_t = $("#obj_frm").val();
    var obj_frm_t = "#" + form_t + " ";

    // console.log('f_get_box_mng_list start', pg)
    if (pg == null || pg == "") {
        var ls_obj_pg = localStorage.getItem("obj_pg");
        if (ls_obj_pg) {
            pg = ls_obj_pg;

            for (let i = 0; i < localStorage.length; i++) {
                let key = localStorage.key(i);
                if (key != '@tosspayments/client-id') {
                    if (localStorage.getItem(key) && $(obj_frm_t + "#" + key).val() == "") {
                        // $(obj_frm_t + "#" + key).val(localStorage.getItem(key));
                    }
                }
            }
        } else {
            pg = 1;
        }
    }

    // console.log('f_get_box_mng_list ing', pg)

    $(obj_frm_t + "#obj_pg").val(parseInt(pg));

    var form_t = $("#" + form_t)[0];
    var formData_t = new FormData(form_t);

    $.ajax({
        url: $("#obj_uri").val(),
        enctype: "multipart/form-data",
        data: formData_t,
        type: "POST",
        async: true,
        contentType: false,
        processData: false,
        cache: true,
        timeout: 5000,
        success: function (data) {
            if (data) {
                for (const [key, value] of formData_t.entries()) {
                    localStorage.setItem(key, value);
                }

                $("#" + $(obj_frm_t + "#obj_list").val()).html(data);
            }

            // initializeTable 순선 변경 초기화
            if (typeof initializeTable === 'function') {
                initializeTable();
            } else {
                console.log('initializeTable 함수가 정의되지 않았습니다.');
            }
        },
        error: function (err) {
            console.log(err);
        },
    });

    return false;
}


function f_get_box_mng_second_list(pg = "") {
    var form_t = $("#list_v_frm").val();
    var list_v_frm_t = "#" + form_t + " ";

    if (pg == null || pg == "") {
        var ls_list_v_pg = localStorage.getItem("list_v_pg");
        if (ls_list_v_pg) {
            pg = ls_list_v_pg;

            for (let i = 0; i < localStorage.length; i++) {
                let key = localStorage.key(i);
                if (key != '@tosspayments/client-id') {
                    if (localStorage.getItem(key) && $(list_v_frm_t + "#" + key).val() == "") {
                        // $(list_v_frm_t + "#" + key).val(localStorage.getItem(key));
                    }
                }
            }
        } else {
            pg = 1;
        }
    }

    $(list_v_frm_t + "#list_v_pg").val(parseInt(pg));

    var form_t = $("#" + form_t)[0];
    var formData_t = new FormData(form_t);

    console.log($("#list_v_uri").val())

    $.ajax({
        url: $("#list_v_uri").val(),
        enctype: "multipart/form-data",
        data: formData_t,
        type: "POST",
        async: true,
        contentType: false,
        processData: false,
        cache: true,
        timeout: 5000,
        success: function (data) {

            if (data) {
                for (const [key, value] of formData_t.entries()) {
                    localStorage.setItem(key, value);
                }

                $("#" + $(list_v_frm_t + "#list_v_list").val()).html(data);
            }

            // initializeTable 순선 변경 초기화
            if (typeof initializeTable === 'function') {
                initializeTable('buyListTable');
            } else {
                console.log('initializeTable 함수가 정의되지 않았습니다.');
            }
        },
        error: function (err) {
            console.log(err);
        },
    });

    return false;
}

function f_localStorage_reset() {
    localStorage.clear();
}

function f_localStorage_reset_go(url) {
    localStorage.clear();
    location.href = url;
}

function sendfile_summernote(ctype, file, no, editor) {
    if (!file.type.match("image.*")) {
        jalert("확장자는 이미지 확장자만 가능합니다.");
        return;
    }

    if (file.size > 12000000) {
        jalert("업로드는 10메가 이하만 가능합니다.");
        return;
    }

    var form_data = new FormData();
    form_data.append("act", "upload");
    form_data.append("editor_name", "summernote");
    form_data.append("ctype", ctype);
    form_data.append("file_no", no);
    form_data.append("upload", file);
    $.ajax({
        data: form_data,
        type: "POST",
        enctype: "multipart/form-data",
        url: "./file_upload.php",
        cache: false,
        timeout: 5000,
        contentType: false,
        processData: false,
        success: function (data) {
            var obj = JSON.parse(data);
            $(editor).summernote("insertImage", obj.url);
        },
        error: function (err) {
            console.log(err);
        },
    });
}
function deletefile_summernote(url) {
    var form_data = new FormData();
    form_data.append("act", "delete");
    form_data.append("editor_name", "summernote");
    form_data.append("url", url);
    $.ajax({
        data: form_data,
        type: "POST",
        enctype: "multipart/form-data",
        url: "./file_upload.php",
        cache: false,
        timeout: 5000,
        contentType: false,
        processData: false,
        success: function (data) {
        },
        error: function (err) {
            console.log(err);
        },
    });
}
//--------------------------------------------------------------------------------------------------
//페이징 생성
function setPagingControl(data,fid) {
    var shtml = "";
    if(data) {
        var total_page = data.total_page;
        var cur_page = data.page;
        var pages = 5;

        if(total_page < pages) pages = total_page;
        if(cur_page == "") cur_page = 1;

        var start_page = (parseInt(cur_page-1)/pages)*pages+1;
        if(total_page == pages) start_page = 1;
        var end_page = parseInt(start_page) + pages -1;
        if(end_page > total_page) end_page = total_page;

        if(cur_page > 1) {
            shtml += '<li class="page-item pg_start" onclick="movePaging(\''+fid+'\',1);"><i class="mdi mdi-chevron-double-left page-link" title="처음"></i></li>';
        }
        if(start_page > 1) {
            shtml += '<li class="page-item pg_prev" onclick="movePaging(\''+fid+'\','+parseInt(start_page-1)+');"><i class="mdi mdi-chevron-left page-link" title="이전"></i></li>';
        }
        for(var i=start_page;i<=end_page;i++) {
            var cls_selected = '';
            if(cur_page == i) cls_selected = ' active';
            shtml += '<li class="page-item'+cls_selected+'" onclick="movePaging(\''+fid+'\','+i+');"><span class="page-link">'+i+'</span></li>';
        }
        if(total_page > end_page) {
            var next_page = parseInt(end_page+1);
            if(next_page > total_page) next_page = total_page;
            shtml += '<li class="page-item pg_next" onclick="movePaging(\''+fid+'\','+next_page+');"><i class="mdi mdi-chevron-right page-link" title="다음"></i></li>';
        }
        if(cur_page < total_page) {
            shtml += '<li class="page-item pg_end" onclick="movePaging(\''+fid+'\','+total_page+');"><i class="mdi mdi-chevron-double-right page-link" title="끝"></i></li>';
        }
        //if(total_page > 0) $("#"+fid+" .paging_wrap").html(shtml);
    }
    $("#"+fid+" .paging_wrap").html(shtml?'<ul class="page-light pagination justify-content-center">'+shtml+'</ul>':'');
}
//--------------------------------------------------------------------------------------------------
//페이징 이동
function movePaging(fid, page) {
    $("#"+fid+" input[name=page]").val(page);
    $("#"+fid+" button[name=search_paging]").click();
    //-- 뒤로가기시 필요한
    window.location.hash = '#page' + page;
}
//--------------------------------------------------------------------------------------------------
function upload_video(input) {
    if (input.files && input.files[0]) {
        var file_size = 50;
        if (input.files[0].size > (file_size*1024*1024)) {
            input.value = "";
            jalert(file_size+"MB 이하만 업로드 가능합니다.");
            return false;
        }
    }
}
//--------------------------------------------------------------------------------------------------
//업로드 이미지 미리보기
function upload_preview(input, callback) {
    var id = input.getAttribute('id');
    if (!id) { id = $(input)[0].getAttribute('id'); }
    if (!id) { id = $(input).attr('id'); }

    if (input.files && input.files[0]) {

        var file_size = $(input).data('size') ? $(input).data('size') : 5;
        if (file_size) {
            if (input.files[0].size > (file_size*1024*1024)) {
                jalert(file_size+"MB 이하만 업로드 가능합니다.");
                return;
            }
        }

        var reader = new FileReader();
        reader.onload = function (e) {

            var image = new Image();
            image.src = e.target.result;
            image.onload = function () {
                if ($("#"+id+"_box").length) {
                    //$("#"+id+"_box").html('<img src="'+e.target.result+'" />');
                    //$("#"+id+"_on").val(e.target.result);
                    // if (id === 'st_image1' || id === 'mt_image1' || id === 'st_bg_image1' || id === 'mt_bg_image1') {
                    //     $("#"+id+"_box").html('').css({'background-image': "url('"+e.target.result+"')"});
                    // } else {
                    $("#"+id+"_box").html('').css({'background-image': "url('"+e.target.result+"')", 'border': '0'});
                    // }
                }
                $("#"+id).parent().find('.btn_remove').show();

                $(document).find('#'+id+'_chk').val('Y');
                if ($(document).find('.file_cnt[data-id="'+id+'"]').length) {
                    var file_cnt = $(document).find('.file_cnt[data-id="'+id+'"]');
                    var file_ele = $(document).find('input[type="file"][name^="'+id+'"]');
                    var cnt = 0;
                    file_ele.each(function(){
                        if ($(this).val()) {
                            cnt++;
                        }
                    });
                    file_cnt.text('('+cnt+'/'+file_ele.length+')');
                }

                // if (callback) {
                //     callback('Y');
                // }
                if ($('span[id="'+id+'_chk-error"]').length) {
                    $('span[for="'+id+'_chk"]').html('');
                }
            }
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        if ($("#"+id+"_box").length) {
            //$("#"+id+"_on").val('');
            // if (id === 'st_image1') {
            //     $("#"+id+"_box").html('<img src="./../../design/sellers_adm/img/logo_bg.png" alt="" />').css({'background-image': 'url()'});
            // } else if (id === 'mt_image1') {
            //     $("#"+id+"_box").html('<img src="./../../design/sellers/img/bb_img.png" alt="" />').css({'background-image': 'url()'});
            // } else if (id === 'st_bg_image1' || id === 'mt_bg_image1') {
            //     $("#"+id+"_box").html('').css({'background-image': 'url()'});
            // } else {
            $("#"+id+"_box").html('<i class="xi-plus"></i>').css({'background-image': 'url()', 'border': '1px dashed #B2B2B1'});
            // }
        }
        $("#"+id).parent().find('.btn_remove').hide();

        $(document).find('#'+id+'_chk').val('');
        if ($(document).find('.file_cnt[data-id="'+id+'"]').length) {
            var file_cnt = $(document).find('.file_cnt[data-id="'+id+'"]');
            var file_ele = $(document).find('input[type="file"][name^="'+id+'"]');
            var cnt = 0;
            file_ele.each(function(){
                if ($(this).val()) {
                    cnt++;
                }
            });
            file_cnt.text('('+cnt+'/'+file_ele.length+')');
        }

        // if (callback) {
        //     callback('');
        // }
    }
}

var uploadFiles = [];
function preview_multiple(event, obj, style_gubun) {
    var files = event.target.files;
    var filesArr = Array.prototype.slice.call(files);
    var file_size = $('#multi_img'+obj+'_upload_box').data('size') ? $('#multi_img'+obj+'_upload_box').data('size') : 5;
    var max_file = $('#multi_img'+obj+'_upload_box').data('max');
    var qcnt = files.length*1;

    if(qcnt>max_file) {
        jalert(max_file+'개까지 등록이 가능합니다.');

        return false;
    } else {
        var qimg1_cnt = 0;
        $('#multi_img'+obj+'_upload_list .filebox').each(function(i, obj) {
            qimg1_cnt++;
        });
        qimg_cnt = qimg1_cnt+qcnt;

        if(qimg_cnt>max_file) {
            jalert(max_file+'개까지 등록이 가능합니다.');

            return false;
        } else {
            filesArr.forEach(function(f,i) {
                if(!f.type.match("image.*")) {
                    jalert("이미지 확장자만 업로드 가능합니다.");
                    return;
                }

                if(f.size > (file_size*1024*1024)) {
                    jalert(file_size+"MB 이하만 업로드 가능합니다.");
                    return;
                }

                var reader = new FileReader();
                reader.onload = function(e) {
                    // uploadFiles.push(new File([f], f.name, {name: f.name, type: f.type, data: e.target.result}));
                    uploadFiles.push(f);
                    $('#multi_img'+obj+'_upload_list').append('<div class="filebox image_upload '+style_gubun+'">'
                        +'<div class="upload_box">'
                        +'<label for="" class="file_box"><div class="rect"><img src="'+e.target.result+'" /></div></label>'
                        +'<button type="button" class="btn upload_del btn_remove d-block" onclick="preview_multiple_del(this, '+obj+');" data-fname="'+f.name+'"><img src="'+get_url+'/design/img/img_del.png" /></button>'
                        +'<input type="hidden" name="image_order['+f.name+']" value="'+(qimg1_cnt+i)+'" />'
                        +'</div>'
                        +'</div>');

                    /*//-- 피드등록
					if ($('#swiper'+obj+'_upload_list').length) {
                        $('#swiper'+obj+'_upload_list').append('<div class="swiper-slide"><img src="'+e.target.result+'" /></div>');
                    }
                    //-- 피드등록*/
                }
                reader.readAsDataURL(f);
            });

            $('#photo'+obj+'_cnt').text('사진 '+qimg_cnt+'/'+max_file+'');
            $('#photo'+obj+'_img_cnt').val(qimg_cnt);
            $('#photo'+obj+'_chk').val(qimg_cnt > 0 ? 'Y' : 'N');

            setTimeout(function () {
                $('#multi_img'+obj+'_upload_list .filebox').each(function(i, item) {
                    $(item).find('input[name^=image_order]').val(i+1);
                });

                /*//-- 피드등록
                if ($('#thumbnail'+obj+'_upload').length) {
                    setTimeout(function () {
                        const src = $('#multi_img'+obj+'_upload_list').find('.image_upload:eq(0) img').attr('src');
                        if (src) {
                            $('#thumbnail'+obj+'_upload').html('<p class="thumbnail">썸네일</p><img src="'+src+'" />');
                        } else {
                            $('#thumbnail'+obj+'_upload').html('<p class="thumbnail">썸네일</p><img src="'+ct_no_img_url+'" />');
                        }
                    }, 500);
                }
                //-- 피드등록*/
            }, 300);
        }
    }
}

function preview_multiple_del(e, obj, qidx="", pidx="", url="") {
    if(qidx) {
        $('#multi_img'+obj+'_del').val($('#multi_img'+obj+'_del').val() + ',' + qidx);

        preview_multiple_del_set(e, obj, qidx, pidx, url);

        /*$.confirm({
            title: '',
            content: '삭제하시겠습니까? 삭제된 자료는 복구되지 않습니다.',
            buttons: {
                confirm: {
                    text: '확인',
                    action: function() {
                        if(qidx) {
                            $.post(url, {act: 'photo_delete', photo_type: obj, photo_idx: qidx, idx: pidx}, function (data) {
                                if(data=='Y') {
                                    preview_multiple_del_set(e, obj, qidx, pidx, url);
                                }
                            });
                        }
                    }
                },
                cancel: {
                    text: '취소',
                    //close
                },
            }
        });*/
    } else {
        preview_multiple_del_set(e, obj, qidx, pidx, url);
    }
}
function preview_multiple_del_set(e, obj, qidx="", pidx="", url="") {
    var max_file = $('#multi_img'+obj+'_upload_box').data('max');

    if (uploadFiles) {
        if (uploadFiles.length) {
            let fname = $(e).data('fname');
            for (let i = 0; i < uploadFiles.length; i++) {
                if (uploadFiles[i].name === fname) {
                    uploadFiles.splice(i, 1);
                    break;
                }
            }
        }
    }

    e.closest('.filebox').remove();
    var cnt = $('#multi_img'+obj+'_upload_list .filebox').length;
    $('#photo'+obj+'_cnt').text('사진 '+cnt+'/'+max_file+'');
    $('#photo'+obj+'_img_cnt').val(cnt);
    $('#photo'+obj+'_chk').val(cnt > 0 ? 'Y' : 'N');

    $('#multi_img'+obj+'_upload_list .filebox').each(function(i, item) {
        $(item).find('input[name^=image_order]').val(i+1);
    });

    /*//-- 피드등록
    if (cnt > 0) {
        if ($('#swiper'+obj+'_upload_list').length) {
            $('#swiper'+obj+'_upload_list').html('');
            $('#multi_img'+obj+'_upload_list .image_upload').each(function (index, item) {
                const src = $(item).find('img').attr('src');
                if (src) {
                    $('#swiper'+obj+'_upload_list').append('<div class="swiper-slide"><img src="'+src+'" /></div>');
                }
            });
        }
        if ($('#thumbnail'+obj+'_upload').length) {
            setTimeout(function () {
                const src = $('#multi_img'+obj+'_upload_list').find('.image_upload:eq(0) img').attr('src');
                if (src) {
                    $('#thumbnail'+obj+'_upload').html('<p class="thumbnail">썸네일</p><img src="'+src+'" />');
                } else {
                    $('#thumbnail'+obj+'_upload').html('<p class="thumbnail">썸네일</p><img src="'+ct_no_img_url+'" />');
                }
            }, 500);
        }
    } else {
        if ($('#swiper'+obj+'_upload_list').length) {
            $('#swiper'+obj+'_upload_list').html('');
        }
        if ($('#thumbnail'+obj+'_upload').length) {
            $('#thumbnail'+obj+'_upload').html('<p class="thumbnail">썸네일</p><img src="'+ct_no_img_url+'" />');
        }
    }
    //-- 피드등록*/
}

function openPrint() {
    $('#print_wrap').printThis({
        importCSS: false,
        loadCSS: get_url+"/css/print_this.css",
    });
}
//--------------------------------------------------------------------------------------------------
function clearCanvas(chart, h, canvas_parent){
    // canvas
    var cnvs = document.getElementById(chart);
    // context
    var ctx = cnvs.getContext('2d');

    // 픽셀 정리
    ctx.clearRect(0, 0, cnvs.width, cnvs.height);
    // 컨텍스트 리셋
    ctx.beginPath();

    var graph_wrap = $('#'+chart).closest('.canvas_wrap');

    graph_wrap.find('#'+chart).remove(); // this is my <canvas> element
    graph_wrap.find('.chartjs-size-monitor').remove();
    if (h) {
        if (canvas_parent) {
            graph_wrap.find(canvas_parent).append('<canvas id="'+chart+'" height="'+h+'"><canvas>');
        } else {
            graph_wrap.append('<canvas id="'+chart+'" height="'+h+'"><canvas>');
        }
    } else {
        if (canvas_parent) {
            graph_wrap.find(canvas_parent).append('<canvas id="'+chart+'"><canvas>');
        } else {
            graph_wrap.append('<canvas id="'+chart+'"><canvas>');
        }
    }
}
//--------------------------------------------------------------------------------------------------
$.fn.serializeObject = function() {
    "use strict"
    var result = {}
    var extend = function(i, element) {
        var node = result[element.name]
        if ("undefined" !== typeof node && node !== null) {
            if ($.isArray(node)) {
                node.push(element.value)
            } else {
                result[element.name] = [node, element.value]
            }
        } else {
            result[element.name] = element.value
        }
    }

    $.each(this.serializeArray(), extend)
    return result
}

$(document).ready(function () {
    $('form select[name*=_bank]').on("change", function(){
        $('span[for="bank_account_chk_msg"]').html('');
        $('#bank_account_chk').val('');
    });
    $('form input[name*=_bank_account], form input[name*=_bank_name]').on("keyup", function(){
        $('span[for="bank_account_chk_msg"]').html('');
        $('#bank_account_chk').val('');
    });
    /*$('form input[name^=pt_weight]').on("keyup change", function(){
        //$(this).attr('step', 100);
        //$(this).attr('min', 100);
        var v = Math.max(100, Math.round($(this).val() / 100) * 100);
        console.log('v..', v, parseInt($(this).val()));
        $(this).val(v);
    });*/
});
function f_check_bank_account(obj, t_bank, t_bank_account, t_bank_name) {
    if (t_bank && t_bank_account && t_bank_name) {
        if (!$(obj).find('img').length) {
            $(obj).append('<img src="'+get_url+'/images/loading.gif" alt="" style="width: 20px;" />');
        }
        $.post(get_url+'/proc.php', {act: 'check_bank_account', t_bank: t_bank, t_bank_account: t_bank_account, t_bank_name: t_bank_name}, function (args) {
            args = JSON.parse(args);
            //console.log(args);
            if (args.msg) {
                $('span[for="bank_account_chk_msg"]').html('<span id="bank_account_chk_msg-error" class="errText">'+args.msg+'</span>');
                $('#bank_account_chk').val('');
                f_toast_show(toast_check, 'toast_check', args.msg);
            } else {
                if ($('span[id="bank_account_chk-error"]').length) {
                    $('span[for="bank_account_chk"]').html('');
                }
                if ($('#bank_account_chk.errText').length) {
                    $('#bank_account_chk').removeClass('errText');
                }
                $('span[for="bank_account_chk_msg"]').html('<span id="bank_account_chk_msg-error" class="scsText d-block">계좌 확인되었습니다.</span>');
                $('#bank_account_chk').val('Y');
            }
            $(obj).find('img').remove();
        });
    } else {
        $(obj).find('img').remove();
        var message = '계좌정보를 입력해 주세요.';
        f_toast_show(toast_check, 'toast_check', message);
        $('span[for="bank_account_chk_msg"]').html('');
        $('#bank_account_chk').val('');
    }
}

function f_submit_return(args, button_ele, callback) {
    if (args) {
        args = JSON.parse(args);
        console.log('submit return..', args);

        $('#splinner_modal').modal('hide');

        if (args.msg) {
            if (args.resCode==='0000') {
                f_toast_show(toast_success, 'toast_success', args.msg);
                setTimeout(function () {
                    if (callback) {
                        if (typeof callback === 'function') {
                            callback(args);
                        }
                    } else {
                        if (args.link) { if (args.link=='back') { history.back(); } else { location.replace(args.link); } } else { location.reload(); }
                    }
                }, 600); // 전송완료리턴
            } else {
                f_toast_show(toast_check, 'toast_check', args.msg);
                $(button_ele).prop('disabled', false);
            }
            return false;
        } else {
            if (args.resCode==='0000') {
                if (callback) {
                    if (typeof callback === 'function') {
                        callback(args);
                    }
                } else {
                    if (args.link) { if (args.link=='back') { history.back(); } else { location.replace(args.link); } } else { location.reload(); }
                }
            } else {
                $(button_ele).prop('disabled', false);
                jalert('잘못된 접근입니다.');
                return false;
            }
        }
    }
}

function formatTimeUnit(value) {
    // 값이 10 미만인 경우 앞에 0을 붙입니다.
    return value < 10 ? "0" + value : value;
}

function Unix_timestampConv() {
    return Math.floor(new Date().getTime() / 1000);
}

function Unix_timestamp(t) {
    var date = new Date(t * 1000);
    var year = date.getFullYear();
    var month = "0" + (date.getMonth() + 1);
    var day = "0" + date.getDate();
    var hour = "0" + date.getHours();
    var minute = "0" + date.getMinutes();
    var second = "0" + date.getSeconds();
    return year + "-" + month.substr(-2) + "-" + day.substr(-2) + " " + hour.substr(-2) + ":" + minute.substr(-2) + ":" + second.substr(-2);
}

// 상태 메시지 표시
function showStatus(message, bgColor = '#22212f') {
    const status = document.getElementById('statusMessage');
    if (!status) return;

    status.textContent = message;
    status.style.display = 'block';
    status.style.backgroundColor = bgColor; // 배경색 설정

    clearTimeout(statusTimeout);
    statusTimeout = setTimeout(() => {
        status.style.display = 'none';
    }, 2000);
}

/* 기본 테이블 위/아래 이동 함수 */
// 전역 변수
let statusTimeout;
let draggedRows = [];

// 테이블 초기화 함수
function initializeTable(id='listTable') {
    const table = document.getElementById(id);
    if (!table) {
        console.error('Table not found');
        return;
    }

    const tbody = table.querySelector('tbody');
    setupEventListeners(table, tbody);
    updateButtonStates();
}

// 이벤트 리스너 설정
function setupEventListeners(table, tbody) {
    // 전체 선택 체크박스
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.rowCheckbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateButtonStates();
        });
    }

    // 개별 체크박스
    document.querySelectorAll('.rowCheckbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateButtonStates);
    });

    // 드래그 앤 드롭 이벤트
    // setupDragAndDrop(table, tbody);
}

// 드래그 앤 드롭 설정
function setupDragAndDrop(table, tbody) {
    table.addEventListener('dragstart', (event) => {
        const targetRow = event.target.closest('tr');
        if (targetRow) {
            draggedRows = Array.from(document.querySelectorAll('.rowCheckbox:checked'))
                .map(checkbox => checkbox.closest('tr'));

            if (!draggedRows.includes(targetRow)) {
                draggedRows = [targetRow];
                targetRow.querySelector('.rowCheckbox').checked = true;
            }

            draggedRows.forEach(row => row.classList.add('dragging'));
        }
    });

    table.addEventListener('dragend', () => {
        document.querySelectorAll('.drag-over').forEach(row => {
            row.classList.remove('drag-over');
        });
        draggedRows.forEach(row => row.classList.remove('dragging'));
        reorderNumbers();
        showStatus('항목이 이동되었습니다.');
        draggedRows = [];
    });

    table.addEventListener('dragover', (event) => {
        event.preventDefault();
        const targetRow = event.target.closest('tr');

        // tbody 외부는 드래그 표시도 안 함
        if (!targetRow || !tbody.contains(targetRow)) {
            return;
        }

        document.querySelectorAll('.drag-over').forEach(row => {
            row.classList.remove('drag-over');
        });

        if (targetRow && !draggedRows.includes(targetRow)) {
            targetRow.classList.add('drag-over');
        }
    });

    table.addEventListener('drop', (event) => {
        event.preventDefault();
        const targetRow = event.target.closest('tr');

        // 드롭 대상이 없거나 tbody 영역 밖이면 무시
        if (
            !targetRow ||
            !tbody.contains(targetRow) // <tbody> 내부 요소인지 확인
        ) {
            return;
        }

        if (targetRow && draggedRows.length > 0) {
            draggedRows.forEach(row => {
                if (row !== targetRow) {
                    targetRow.parentNode.insertBefore(row, targetRow);
                }
            });
        }
    });
}

// 번호 재정렬
function reorderNumbers() {
    const table = document.getElementById('listTable');
    if (!table) return;

    const tbody = table.querySelector('tbody');
    const rows = tbody.querySelectorAll('tr');
    const obj_pg = document.getElementById('obj_pg').value;
    const obj_limit_num = document.getElementById('obj_limit_num').value;
    // 토탈 카운트 추가
    const totalItems = parseInt(document.getElementById('total').value);
    const baseNumber = (obj_pg - 1) * obj_limit_num + 1;

    rows.forEach((row, index) => {
        const numberCell = row.querySelector('td:nth-child(2)');
        if (numberCell) {
            // 내림차순으로 변경
            const number = totalItems - ((obj_pg - 1) * obj_limit_num + index);
            numberCell.textContent = number;
            // numberCell.textContent = baseNumber + index;
        }
    });
}

// 버튼 상태 업데이트
function updateButtonStates() {
    const checkedRows = document.querySelectorAll('.rowCheckbox:checked');
    const hasChecked = checkedRows.length > 0;

    ['moveTopBtn', 'moveUpBtn', 'moveDownBtn', 'moveBottomBtn'].forEach(id => {
        const button = document.getElementById(id);
        if (button) {
            button.disabled = !hasChecked;
        }
    });
}

// 이동 함수들
function moveToTop() {
    const table = document.getElementById('listTable');
    if (!table) return;

    const tbody = table.querySelector('tbody');
    const checkedRows = Array.from(document.querySelectorAll('.rowCheckbox:checked'))
        .map(checkbox => checkbox.closest('tr'));

    checkedRows.reverse().forEach(row => {
        tbody.insertBefore(row, tbody.firstChild);
    });
    reorderNumbers();
    showStatus('항목을 최상단으로 이동했습니다.');
}

function moveUp() {
    const table = document.getElementById('listTable');
    if (!table) return;

    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const checkedRows = rows.filter(row => row.querySelector('.rowCheckbox').checked);

    let moved = false;
    checkedRows.sort((a, b) => rows.indexOf(a) - rows.indexOf(b))
        .forEach(row => {
            const index = rows.indexOf(row);
            if (index > 0 && !rows[index - 1].querySelector('.rowCheckbox').checked) {
                tbody.insertBefore(row, rows[index - 1]);
                moved = true;
            }
        });

    if (moved) {
        reorderNumbers();
        showStatus('항목을 위로 이동했습니다.');
    }
}

function moveDown() {
    const table = document.getElementById('listTable');
    if (!table) return;

    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const checkedRows = rows.filter(row => row.querySelector('.rowCheckbox').checked);

    let moved = false;
    checkedRows.sort((a, b) => rows.indexOf(b) - rows.indexOf(a))
        .forEach(row => {
            const index = rows.indexOf(row);
            if (index < rows.length - 1 && !rows[index + 1].querySelector('.rowCheckbox').checked) {
                tbody.insertBefore(rows[index + 1], row);
                moved = true;
            }
        });

    if (moved) {
        reorderNumbers();
        showStatus('항목을 아래로 이동했습니다.');
    }
}

function moveToBottom() {
    const table = document.getElementById('listTable');
    if (!table) return;

    const tbody = table.querySelector('tbody');
    const checkedRows = Array.from(document.querySelectorAll('.rowCheckbox:checked'))
        .map(checkbox => checkbox.closest('tr'));

    checkedRows.forEach(row => {
        tbody.appendChild(row);
    });
    reorderNumbers();
    showStatus('항목을 최하단으로 이동했습니다.');
}


function f_retire_mem(idx) {
    let memo = $('#mt_retire_memo').val();
    $.confirm({
        title: '회원탈퇴',
        content: "정말 탈퇴처리하겠습니까?",
        buttons: {
            cancel: {
                text: "취소",
                btnClass: "btn-outline-light",
            },
            confirm: {
                text: "확인",
                btnClass: "btn-primary",
                action: function () {
                    $.post('./update.php', {act: 'retire', mt_idx_t: idx, mt_retire_memo: memo}, function (data) {
                        console.log(data);
                        if(data=='Y'){
                            app.toastr.showSuccess('관리자권한 회원탈퇴 처리되었습니다.', 'reload');
                        } else {
                            app.toastr.showError('회원탈퇴실패!');
                        }
                    });
                },
            },
        },
    });

    return false;
}

function f_restoration_mem(idx) {
    $.confirm({
        title: '회원복구',
        content: "정말 복구처리하겠습니까?",
        buttons: {
            cancel: {
                text: "취소",
                btnClass: "btn-outline-light",
            },
            confirm: {
                text: "확인",
                btnClass: "btn-primary",
                action: function () {
                    $.post('./update.php', {act: 'restoration', mt_idx_t: idx}, function (data) {
                        console.log(data);
                        if(data=='Y'){
                            app.toastr.showSuccess('관리자권한 회원복구 처리되었습니다.', './list.php');
                        } else {
                            app.toastr.showError('회원복구실패!');
                        }
                    });
                },
            },
        },
    });

    return false;
}

function f_restoration_board(idx) {
    $.confirm({
        title: '게시글 복구',
        content: "정말 복구처리하겠습니까?",
        buttons: {
            cancel: {
                text: "취소",
                btnClass: "btn-outline-light",
            },
            confirm: {
                text: "확인",
                btnClass: "btn-primary",
                action: function () {
                    $.post('./update.php', {act: 'restoration', idx_t: idx}, function (data) {
                        console.log(data);
                        if(data=='Y'){
                            app.toastr.showSuccess('관리자권한 게시글 복구 처리되었습니다.', './list.php');
                        } else {
                            app.toastr.showError('게시글 복구실패!');
                        }
                    });
                },
            },
        },
    });

    return false;
}
