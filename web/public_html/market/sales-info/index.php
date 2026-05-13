<?
$_SUB_HEAD_TITLE = "내정보 수정 확인";
$_GET['hd_pc'] = '1';
$hd_num = 'revenue';
$hd_num2 = 'revenue1';
$hd_left = ' ';
include_once("../inc/head.php");
include_once("../inc/header.php");
include_once("../inc/modal.php");
?>

<!-- 왼쪽 메뉴-->
<? include_once("../inc/left_menu.php"); ?>

<div class="sub_pg ">
    <div class="sub_wr">
        <div class="hd_tit2 flex-row">
            <div class="d-flex align-items-end flex-wrap">
                <h3 class="tit_st1 mr-5">정산내역</h3>
            </div>
        </div>
        <section class="card">
            <div class="card-body">
                <form>
                    <div class=" ">
                        <div class="pb-5">
                            <p class="tit_st3 "><img src="<?=DESIGN_HTTP?>/market/img/join_ico3.svg" alt=" 이미지" class="mr-3">정산 정보</p>
                            <div class="row">
                                <div class="col-md-6 mt-5">
                                    <div class="form_wr ip_invalid" id="id_div">
                                        <div class="ip_tit required  ">
                                            <h5>정산번호</h5>
                                        </div>
                                        <div class="form-row ">
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="st_number" placeholder="아이디 입력" value="" disabled >
                                            </div>

                                        </div>

                                    </div>

                                    <div class="form_wr  mt-5 ip_valid">
                                        <div class="ip_tit required">
                                            <h5>정산예정일</h5>
                                        </div>
                                        <input type="text" class="form-control" id="st_plan_date" placeholder="비밀번호 입력" disabled>
                                        <div class="form-text ip_invalid" style="display:none;">&nbsp;</div>
                                    </div>
                                    <div class="form_wr  mt-5 ip_valid">
                                        <div class="ip_tit required">
                                            <h5>총 매출액</h5>
                                        </div>
                                        <input type="text" class="form-control" id="st_total_amount" placeholder="비밀번호 재입력" disabled>
                                        <div class="form-text ip_invalid" style="display:none;">&nbsp;</div>
                                    </div>
                                    <div class="form_wr  mt-5 ip_valid">
                                        <div class="ip_tit required">
                                            <h5>정산 금액</h5>
                                        </div>
                                        <input type="text" class="form-control" id="st_final_amount" placeholder="비밀번호 재입력" disabled>
                                        <div class="form-text ip_invalid" style="display:none;">&nbsp;</div>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-5">
                                    <div class="form_wr ip_valid">
                                        <div class="ip_tit required">
                                            <h5>정산 기간</h5>
                                        </div>
                                        <input type="text" class="form-control" id="settle_period" placeholder="이름 입력" disabled>
                                        <div class="form-text ip_invalid" style="display:none;">&nbsp;</div>
                                    </div>
                                    <div class="form_wr  mt-5 ip_valid">
                                        <div class="ip_tit required">
                                            <h5>정산 완료일</h5>
                                        </div>
                                        <input type="text" class="form-control" id="st_done_date" placeholder="이름 입력" disabled>
                                        <div class="form-text ip_invalid" style="display:none;">&nbsp;</div>
                                    </div>
                                    <div class="form_wr  mt-5 ip_valid">
                                        <div class="ip_tit required">
                                            <h5>서비스 수수료</h5>
                                        </div>
                                        <input type="text" class="form-control" id="st_service_fee" placeholder="이름 입력" disabled>
                                        <div class="form-text ip_invalid" style="display:none;">&nbsp;</div>
                                    </div>
                                    <div class="form_wr  mt-5 ip_valid">
                                        <div class="ip_tit required">
                                            <h5>정산 계좌</h5>
                                        </div>
                                        <input type="text" class="form-control" id="settle_account" placeholder="이름 입력" disabled>
                                        <div class="form-text ip_invalid" style="display:none;">&nbsp;</div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form_wr  mt-5 ip_valid">
                                        <div class="ip_tit required">
                                            <h5>관리자 메모</h5>
                                        </div>
                                        <textarea type="text" class="form-control" id="st_admin_memo" placeholder="관리자 메모" disabled></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 border-top pt-5 pb-5">
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="tit_st3   "><img src="<?=DESIGN_HTTP?>/market/img/join_ico3.svg" alt="이미지" class="mr-3">정산 주문 내역</p>
                                <p id="settle_status_text">정산 예정</p>
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
                                        <col width="*">
                                        <col width="*">
                                        <col width="*">
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>결제일시</th>
                                        <th>주문번호</th>
                                        <th>상품 정보</th>
                                        <th>총 상품 금액</th>
                                        <th>서비스 수수료</th>
                                        <th>정산 금액</th>
                                    </tr>
                                    </thead>
                                    <tbody id="order_list_body">
                                    <!-- 동적으로 채워짐 -->
                                    </tbody>
                                </table>
                            </section>
                        </div>
                    </div>
                </form>
            </div>
        </section>


        <div class="d-flex  justify-content-center mt_40 btn_group">
            <!--메뉴를 삭제하시겠습니까? 알림창으로 한번더 물어보기 -->
            <button type="button" class="btn btn-outline-light btn-lg btn-w2" onclick="history.back()">목록</button>



        </div>


    </div>
</div>


<? include_once("./inc/tail.php"); ?>

<script>
    $(document).ready(function() {
        // URL에서 정산번호 가져오기 (?no=ST20260206-0001)
        const urlParams = new URLSearchParams(window.location.search);
        const settleNo = urlParams.get('no');

        if (!settleNo) {
            alert('잘못된 접근입니다. 정산번호(no)가 필요합니다.');
            history.back();
            return;
        }

        loadSettleDetail(settleNo);
    });

    function loadSettleDetail(stNumber) {
        $.ajax({
            url: './update.php',
            type: 'POST',
            dataType: 'json',
            data: {
                act: 'get_settle_detail',
                st_number: stNumber
            },
            success: function(res) {
                if (res.success) {
                    const data = res.data;

                    // 정산번호
                    $('#st_number').val(data.st_number || '-');

                    // 상태 표시
                    let statusText = '알수없음';
                    let statusColor = '';
                    if (data.st_status === 'DONE') {
                        statusText = '정산완료';
                        statusColor = '';
                    } else if (data.st_status === 'PLANNED') {
                        statusText = '정산예정';
                        statusColor = 'text-success';
                    } else if (data.st_status === 'READY') {
                        statusText = '미정산';
                        statusColor = 'text-danger';
                    }
                    $('#settle_status_text').addClass(statusColor).text(statusText);

                    // 정산 정보
                    $('#st_plan_date').val(data.st_plan_date ? data.st_plan_date.replace(/-/g, '.') : '-');
                    $('#st_done_date').val(data.st_done_date ? data.st_done_date : '-');

                    const period = (data.st_start_date && data.st_end_date)
                        ? data.st_start_date.replace(/-/g, '.') + ' ~ ' + data.st_end_date.replace(/-/g, '.')
                        : '-';
                    $('#settle_period').val(period);

                    $('#st_total_amount').val(Number(data.st_total_amount || 0).toLocaleString() + '원');
                    $('#st_service_fee').val(Number(data.st_service_fee || 0).toLocaleString() + '원');
                    $('#st_final_amount').val(Number(data.st_final_amount || 0).toLocaleString() + '원');

                    // 정산 계좌
                    let accountText = '-';
                    if (data.sh_bank || data.sh_bank_account || data.sh_bank_holder) {
                        accountText = [
                            data.sh_bank || '',
                            data.sh_bank_account || '',
                            data.sh_bank_holder || ''
                        ].filter(Boolean).join(' / ');
                    }
                    $('#settle_account').val(accountText);

                    // 관리자 메모
                    $('#st_admin_memo').val(data.st_admin_memo || '');

                    // 정산 주문 내역 렌더링 (ct_snapshot 파싱 포함)
                    renderOrderList(data.orders || []);
                } else {
                    alert(res.message || '정산 내역을 불러올 수 없습니다.');
                    history.back();
                }
            },
            error: function() {
                alert('서버와의 연결에 문제가 발생했습니다.');
                history.back();
            }
        });
    }

    // 정산 주문 내역 렌더링 + ct_snapshot 파싱
    function renderOrderList(orders) {
        const $body = $('#order_list_body');
        $body.empty();

        if (!orders || orders.length === 0) {
            $body.html('<tr><td colspan="6" class="text-center py-4 text-muted">해당 정산에 포함된 주문 내역이 없습니다.</td></tr>');
            return;
        }

        orders.forEach(order => {
            let productInfo = '상품 정보 없음';

            // ct_snapshot JSON 파싱
            if (order.ct_snapshot) {
                try {
                    const snapshot = JSON.parse(order.ct_snapshot);
                    if (snapshot.items && Array.isArray(snapshot.items)) {
                        const items = snapshot.items.map(item => {
                            return `${item.menu_name} ${item.quantity}개`;
                        });
                        productInfo = items.join(', ');
                        if (productInfo.length > 50) {
                            productInfo = productInfo.substring(0, 50) + '...';
                        }
                    }
                } catch (e) {
                    productInfo = 'JSON 파싱 오류';
                }
            }

            const payDate = order.ot_pay_date ? order.ot_pay_date : '-';

            const row = `
            <tr>
                <td>${payDate}</td>
                <td>${order.ot_number || '-'}</td>
                <td>${productInfo}</td>
                <td class="text-end">${Number(order.ot_total_price || 0).toLocaleString()}원</td>
                <td class="text-end">${Number(order.service_fee || 0).toLocaleString()}원</td>
                <td class="text-end">${Number(order.settle_amount || 0).toLocaleString()}원</td>
            </tr>
        `;
            $body.append(row);
        });
    }
</script>
