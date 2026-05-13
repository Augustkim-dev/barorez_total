<?
$_SUB_HEAD_TITLE = "완료 내역";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ' '; //왼쪽메뉴 active 땜시 만듬
$hd_left = 'cmp_list'; //왼쪽메뉴 on 땜시 만듬
include_once("../inc/head.php");
include_once("../inc/header.php");
include_once("../inc/modal.php");
?>

    <!-- 왼쪽 메뉴-->
<? include_once("../inc/left_menu.php"); ?>

    <div class="sub_pg ">
        <div class="sub_wr">
            <div class="hd_tit2 fs_16">
                <div class="flex-shrink-0 ml-auto   d-flex align-items-end">
                    <p class="d-flex align-content-center mb-4 mb-lg-0"><img src="<?=DESIGN_HTTP?>/market/img/img_mark2.svg" class="mr-2" alt=" "> 주문내역 클릭시 주문 상세보기가 나타납니다.</p>
                </div>
                <div class="d-flex align-items-end flex-wrap">
                    <h2 class="tit_st1 d-flex align-items-center mr-5"><a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 "><img src="<?=DESIGN_HTTP?>/market/img/ico_back.svg" alt=" 뒤로가기"></a><span>완료/취소</span></h2>

                    <div class="btn-group btn-group-toggle gr_st1" data-toggle="buttons">
                        <label class="btn mr-4 active" id="tab_table">
                            <input type="radio" name="options" id="option1" checked=""> 테이블
                        </label>
                        <label class="btn mr-4" id="tab_pack">
                            <input type="radio" name="options" id="option2"> 포장
                        </label>
                        <label class="btn mr-4" id="tab_rv">
                            <input type="radio" name="options" id="option3"> 예약
                        </label>
                    </div>
                </div>

            </div>
            <div class="card cmp_box">
                <div class="card-header">
                    <div class=" btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-light active" id="day_yesterday">
                            <input type="radio" name="cmp_day" value="YESTERDAY" checked> 어제
                        </label>
                        <label class="btn btn-outline-light" id="day_today">
                            <input type="radio" name="cmp_day" value="TODAY"> 오늘
                        </label>
                    </div>
                    <div class="d-flex">
                        <input type="date" class="form-control" id="sdate">
                        <p>~</p>
                        <input type="date" class="form-control" id="edate">
                    </div>
                    <div class="d-flex">
                        <input type="text" class="form-control" id="kw" placeholder="주문번호, 주문자 검색"> <button type="button" class="btn btn-secondary" id="btn_search">검색</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between  ">
                        <h3 class="tit_st2 pr-3" id="cmp_date_title">2025년 12월 09일 주문내역</h3>
                        <div class=" ">
                            <div class="btn-group btn-group-toggle btn_toggle_primary" data-toggle="buttons">
                                <label class="btn btn-outline-light active" id="st_all">
                                    <input type="radio" name="cmp_status" value="ALL" checked>  전체
                                </label>
                                <label class="btn btn-outline-light" id="st_done">
                                    <input type="radio" name="cmp_status" value="DONE">  완료
                                </label>
                                <label class="btn btn-outline-light" id="st_cancel">
                                    <input type="radio" name="cmp_status" value="CANCEL"> 취소
                                </label>
                            </div>
                        </div>
                    </div>

                    <section class="table_scroll mt-4">
                        <table class="table_01" summary=" ">
                            <caption>
                                주문내역 리스트
                            </caption>
                            <colgroup>
                                <col width="*">
                                <col width="*">
                                <col width="*">
                                <col width="*">
                                <col width="*">
                                <col width="25%">
                                <col width="*">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>번호</th>
                                <th>주문번호</th>
                                <th>주문상태</th>
                                <th id="table_time">주문시간</th>
                                <th id="table_name">테이블명</th>
                                <th>주문내역</th>
                                <th>결제금액</th>
                            </tr>
                            </thead>
                            <tbody id="cmp_tbody">
                            <tr>
                                <td>3</td>
                                <td><a href="" class="txt_under_link" data-toggle="modal" data-target="#modal_tbl_list">No.00000001</a></td>
                                <td>
                                    <p>완료</p>
                                </td>
                                <td>2025.12.12 11:03</td>
                                <td>1</td>
                                <td>
                                    <p class="line1_text">칼국수 1개, 콜라 1개, 김치볶음밥칼국수 1개, 콜라 1개, 김치볶음밥</p>
                                </td>
                                <td class="text-right"><b>22,000원</b></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><a href="" class="txt_under_link" data-toggle="modal" data-target="#modal_tbl_list">No.00000001</a></td>
                                <td>
                                    <p class="text-danger">취소</p>
                                </td>
                                <td>2025.12.12 11:03</td>
                                <td>1</td>
                                <td>
                                    <p class="line1_text">칼국수 1개</p>
                                </td>
                                <td class="text-right"><b>8,000원</b></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td><a href="" class="txt_under_link" data-toggle="modal" data-target="#modal_tbl_list">No.00000001</a></td>
                                <td>
                                    <p>완료</p>
                                </td>
                                <td>2025.12.12 11:03</td>
                                <td>8</td>
                                <td>
                                    <p class="line1_text">칼국수 1개, 콜라 1개, 김치볶음밥칼국수 1개, 콜라 1개, 김치볶음밥</p>
                                </td>
                                <td class="text-right"><b>1,122,000원</b></td>
                            </tr>


                            </tbody>
                        </table>
                    </section>


                </div>
            </div>



        </div>
        <div class="modal modal_rr fade" id="modal_tbl_list" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="<?=DESIGN_HTTP?>/market/img/ic_close.png" alt="닫기"></button>
                    <div class="modal-body">
                        <div class="d-flex  ">
                            <!-- 완료일때와 취소일때-->
                            <span class="status status_04" id="modal_status">완료내역</span>
<!--                            <span class="status status_04">취소내역</span>-->
                        </div>
                        <div class=" detail_hd mt-4">
                            <div>
                                <h3 class="tit_st1" id="modal_title">테이블번호 1</h3>
                                <p class="mt-2" id="modal_sub">메뉴3개 ㆍ224,100원ㆍ4인석</p>
                            </div>
                        </div>
                        <section class="bill_wr">
                            <div class="py-4 border-bottom-dot mb-4">
                                <span class="mr-4" id="modal_ot_number">주문 번호 : No.00000001</span>
                                <span id="modal_ot_date">주문일시 : 2025년 08월 09일 15:00</span>
                            </div>

                            <ul class="bill_list" id="modal_bill_list">
                                <li class="d-flex align-items-center justify-content-between ">
                                    <p class="tit_st3">주문메뉴</p>
                                </li>
                                <li>
                                    <div class="bill_box">
                                        <div class="flex-fill">
                                            <div>
                                                <div class="d-flex   justify-content-between ">
                                                    <p class="fw_600 fs_20">(대표메뉴)해물칼국수 </p>
                                                    <p class="  flex-shrink-0  ml-4">1개</p>
                                                </div>
                                                <ul class="dot_list tg_500 mt-4">
                                                    <li>맵기선택 : 1단계</li>
                                                    <li>선택옵션 2 : 라면사리 (+1,000)</li>
                                                    <li>선택옵션 3 : 라면사리 (+1,000)</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="bill_money">
                                            8,500원
                                        </div>
                                    </div>
                                </li>
                                <li class="border-bottom-dot"></li>
                                <li>
                                    <div class="bill_box">
                                        <div class="flex-fill">
                                            <div>
                                                <div class="d-flex  justify-content-between ">
                                                    <p class="fw_600 fs_20">메뉴명이 길때는 이런식으로 나옵니다 메뉴명이 길때는 이런식으로 나옵니다 </p>
                                                    <p class="  flex-shrink-0 ml-4">1개</p>
                                                </div>
                                                <ul class="dot_list tg_500 mt-4">
                                                    <li>맵기선택 : 1단계</li>
                                                    <li>선택옵션 2 : 라면사리 (+1,000)</li>
                                                    <li>선택옵션 3 : 라면사리 (+1,000)선택옵션 3 : 라면사리 (+1,000)선택옵션 3 : 라면사리 (+1,000)선택옵션 3 : 라면사리 (+1,000)</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="bill_money">
                                            8,500원
                                        </div>
                                    </div>
                                </li>
                                <li class="border-bottom-dot"></li>
                                <li>
                                    <div class="bill_box">
                                        <div class="flex-fill">
                                            <div>
                                                <div class="d-flex  justify-content-between ">
                                                    <p class="fw_600 fs_20">옵션이 없을때 </p>
                                                    <p class="  flex-shrink-0 ml-4">1개</p>
                                                </div>
                                                <!-- <ul class="dot_list tg_500 mt-4">
                                                    <li>맵기선택 : 1단계</li>
                                                </ul> -->
                                            </div>
                                        </div>
                                        <div class="bill_money">
                                            8,500원
                                        </div>
                                    </div>
                                </li>
                                <li class="border-bottom">
                                </li>
                                <li class=" ">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <p class=" ">쿠폰 할인</p>
                                        <p class="fw_700 fs_20 ">-3,500원</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between  mb-2">
                                        <p class=" ">결제 수단</p>
                                        <p class="fw_700 fs_20 ">카드 결제</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between  mb-2">
                                        <p class=" ">총 주문 금액</p>
                                        <p class="fw_700 fs_20 ">23,500원</p>
                                    </div>
                                </li>
                                <li class="border-bottom border-dark">
                                </li>
                                <li class=" ">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <p class="fw_600">결제 완료 금액</p>
                                        <p class="fw_700 fs_24 text-primary ">32,000원</p>
                                    </div>
                                </li>
                            </ul>
                        </section>
                        <div class="mt-4 d-flex align-items-center justify-content-between bg-light p-5 rounded">
                            <p class="fw_600">고객정보</p>
                            <p id="modal_customer">홍길동 (010-1234-5678)</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

<script>
    let CMP = {
        type: 'TABLE',      // TABLE | PACK | RV
        dayPreset: 'YESTERDAY', // YESTERDAY | TODAY | RANGE
        status: 'ALL',      // ALL | DONE | CANCEL
        sdate: '',
        edate: '',
        kw: ''
    };

    function pad2(n){ return String(n).padStart(2,'0'); }
    function fmtYmd(d){
        return d.getFullYear() + '-' + pad2(d.getMonth()+1) + '-' + pad2(d.getDate());
    }
    function setActiveLabel($label){
        $label.closest('.btn-group, .btn-group-toggle').find('label').removeClass('active');
        $label.addClass('active');
    }

    function getFiltersFromUI(){
        CMP.sdate = ($('#sdate').val() || '').trim();
        CMP.edate = ($('#edate').val() || '').trim();
        CMP.kw    = ($('#kw').val() || '').trim();
    }

    function updateTableHeader(){
        if(CMP.type === 'RV'){
            $('#table_name').text('예약자명 (유형)');
            $('#table_time').text('예약시간');
        } else if(CMP.type === 'TABLE'){
            $('#table_name').text('테이블명');
            $('#table_time').text('주문시간');
        } else {
            $('#table_name').text('주문자명');
            $('#table_time').text('주문시간');
        }
    }

    function renderList(rows, dateTitle){
        $('#cmp_date_title').text(dateTitle || '주문내역');
        const $tb = $('#cmp_tbody');
        $tb.empty();

        if(!rows || rows.length === 0){
            $tb.append(`
      <tr>
        <td colspan="7" class="text-center py-5">내역이 없습니다.</td>
      </tr>
    `);
            return;
        }

        rows.forEach(function(r, idx){
            const stText  = r.status_label || '-';
            const stClass = r.status_class || '';
            const linkTxt = r.number_label || '-';
            const tableNm = r.table_label || '-';
            const summary = r.summary || '-';
            const payLbl  = r.pay_label || '0원';

            $tb.append(`
      <tr data-kind="${r.kind}" data-id="${r.id}">
        <td>${r.no}</td>
        <td><a href="#" class="txt_under_link js-open-detail" data-toggle="modal" data-target="#modal_tbl_list">${linkTxt}</a></td>
        <td><p class="${stClass}">${stText}</p></td>
        <td>${r.datetime_label || '-'}</td>
        <td>${tableNm}</td>
        <td><p class="line1_text">${summary}</p></td>
        <td class="text-right"><b>${payLbl}</b></td>
      </tr>
    `);
        });
    }

    function fetchList(){
        getFiltersFromUI();

        // console.log('[cmp_list payload]', {
        //     type: CMP.type,
        //     day_preset: CMP.dayPreset,
        //     sdate: CMP.sdate,
        //     edate: CMP.edate,
        //     status: CMP.status,
        //     kw: CMP.kw
        // });

        if(CMP.type === 'TABLE'){
            $('#table_name').text('테이블명');
        }else{
            $('#table_name').text('주문자명');
        }

        $.ajax({
            url: './update.php',
            type: 'POST',
            dataType: 'json',
            data: {
                act: 'cmp_list',
                type: CMP.type,
                day_preset: CMP.dayPreset,
                sdate: CMP.sdate,
                edate: CMP.edate,
                status: CMP.status,
                kw: CMP.kw
            },
            success: function(res){
                if(!res || !res.success){
                    alert((res && res.message) ? res.message : '조회 실패');
                    return;
                }
                renderList(res.data.rows || [], res.data.date_title || '');
            },
            error: function(){
                alert('서버 통신 오류');
            }
        });
    }

    function renderDetailModal(d){
        $('#modal_status').text(d.status_title || '');
        // status class는 UI 유지 원칙상 class 추가는 최소로: 텍스트만 변경 (원하면 class도 추가해줄게)
        $('#modal_title').text(d.title || '');
        $('#modal_sub').text(d.sub || '');

        $('#modal_ot_number').text('주문 번호 : ' + (d.number_label || '-'));
        $('#modal_ot_date').text('주문일시 : ' + (d.datetime_label || '-'));

        $('#modal_bill_list').html(d.bill_html || '');

        $('#modal_customer').text(d.customer_label || '-');
    }

    function fetchDetail(kind, id){
        $.ajax({
            url: './update.php',
            type: 'POST',
            dataType: 'json',
            data: {
                act: 'cmp_detail',
                kind: kind, // TABLE|PACK|RV
                id: id
            },
            success: function(res){
                if(!res || !res.success){
                    alert((res && res.message) ? res.message : '상세 조회 실패');
                    return;
                }
                renderDetailModal(res.data || {});
            },
            error: function(){
                alert('서버 통신 오류');
            }
        });
    }

    function initDefaults(){
        // 기본: 어제 + 테이블 + 전체
        const now = new Date();
        const y = new Date(now.getFullYear(), now.getMonth(), now.getDate()-1);

        $('#sdate').val(fmtYmd(y));
        $('#edate').val(fmtYmd(y));
    }

    $(function(){

        initDefaults();
        fetchList();

        // 탭
        $(document).on('click', '#tab_table', function(){ setActiveLabel($(this)); CMP.type='TABLE'; fetchList(); updateTableHeader();});
        $(document).on('click', '#tab_pack',  function(){ setActiveLabel($(this)); CMP.type='PACK';  fetchList(); updateTableHeader();});
        $(document).on('click', '#tab_rv',    function(){ setActiveLabel($(this)); CMP.type='RV';    fetchList(); updateTableHeader();});

        // 날짜 프리셋
        $(document).on('click', '#day_yesterday', function(){
            setActiveLabel($(this));
            CMP.dayPreset='YESTERDAY';
            const now = new Date();
            const y = new Date(now.getFullYear(), now.getMonth(), now.getDate()-1);
            $('#sdate').val(fmtYmd(y));
            $('#edate').val(fmtYmd(y));
            fetchList();
        });

        $(document).on('click', '#day_today', function(){
            setActiveLabel($(this));
            CMP.dayPreset='TODAY';
            const now = new Date();
            $('#sdate').val(fmtYmd(now));
            $('#edate').val(fmtYmd(now));
            fetchList();
        });

        // 기간 직접 변경
        $(document).on('change', '#sdate, #edate', function(){
            CMP.dayPreset='RANGE';
        });

        // 상태 토글
        $(document).on('click', '#st_all', function(){ setActiveLabel($(this)); CMP.status='ALL'; fetchList(); });
        $(document).on('click', '#st_done', function(){ setActiveLabel($(this)); CMP.status='DONE'; fetchList(); });
        $(document).on('click', '#st_cancel', function(){ setActiveLabel($(this)); CMP.status='CANCEL'; fetchList(); });

        // 검색
        $(document).on('click', '#btn_search', function(){
            CMP.dayPreset='RANGE';
            fetchList();
        });
        $(document).on('keydown', '#kw', function(e){
            if(e.key === 'Enter'){ e.preventDefault(); $('#btn_search').click(); }
        });

        // 상세 모달 열기
        $(document).on('click', '.js-open-detail', function(e){
            e.preventDefault();
            const $tr = $(this).closest('tr');
            const kind = String($tr.data('kind') || '');
            const id   = parseInt($tr.data('id') || '0', 10);
            if(!kind || !id){ alert('키가 없습니다.'); return; }
            fetchDetail(kind, id);
        });

    });
</script>

<? include_once("./inc/tail.php"); ?>
