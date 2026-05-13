<?
$_SUB_HEAD_TITLE = "퍼블 공지";
$_GET['hd_pc'] = '1'; //PC hd 메뉴있음1, 메뉴없음 공백
$_GET['hd_num'] = ' '; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ' '; //모바일 하단메뉴 있음1, ft 없음 공백
$_GET['ft_none'] = ' '; //모바일 ft 있음1, ft 없음 공백
include_once("./inc/head.php");
?>

<div class="wrap">
    <div class="sub_pg pb_lg">
        <div class="container pt-5">

            <div class="mb-5">
                <p>스타일참고</p>
                <p>form.php</p>
            </div>

            <div class="mb-5">
                <p class="mb-3">form을 다 작성하지 않을경우 disable버튼으로 보이게</p>
                <button type="button" class="btn btn-primary btn-block" disabled>disabled </button>
                <button type="button" class="btn btn-primary btn-block">완료</button>
            </div>

            <div class="mb-5">
                <p class="mb-3">인풋</p>
                <div class="form_wr   ip_valid">
                    <div class="ip_tit required">
                        <h5>ip_valid</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="입력해주세요.">
                    <div class="form-text ip_valid">확인되었습니다.</div>
                    <div class="form-text ip_invalid">아이디를 다시 확인해주세요</div>
                </div>
                <div class="form_wr mt-5 ip_invalid">
                    <div class="ip_tit">
                        <h5>ip_invalid</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="입력해주세요.">
                    <div class="form-text ip_valid">확인되었습니다.</div>
                    <div class="form-text ip_invalid">아이디를 다시 확인해주세요</div>
                </div>
            </div>

            <section>
                <p class="mt-5">alat</p>
                <button type="button" class="btn btn-info btn-sm mb-3"
                    onclick="testAdminModal()">
                    Alert / Confirm 테스트
                </button>
                <script>
                    function testAdminModal() {

                        // alert 테스트
                        g5Alert('이 메시지가 Bootstrap 모달로 보이면 alert 정상입니다.', 'Alert 테스트');

                        // confirm 테스트 (alert 닫은 뒤 실행)
                        setTimeout(function() {
                            g5Confirm('확인 버튼을 누르면 콘솔에 로그가 찍힙니다.', function() {
                                console.log('Confirm 정상 동작');
                                g5Alert('Confirm 콜백까지 정상입니다.', '완료');
                            }, 'Confirm 테스트');
                        }, 500);
                    }
                </script>
            </section>

            <section>
                <p class="mt-5">select 박스 대체</p>

                <div class="custom-sel">
                    <button type="button" class="select-trigger">
                        옵션 선택
                    </button>

                    <ul class="select-options">
                        <li data-value="1">옵션 1</li>
                        <li data-value="2">옵션 2</li>
                        <li class="is-disabled" data-value="3">옵션 3 (선택불가)</li>
                        <li data-value="4">옵션 4</li>
                        <li data-value="5">옵션 5</li>
                        <li data-value="6">옵션 6</li>
                        <li data-value="7">옵션 7</li>
                        <li data-value="8">옵션 8</li>
                    </ul>

                    <input type="hidden" name="option">
                </div>

            </section>

            <!-- 하단 플로팅 버튼을 사용할경우 .sub_pg옆에 .pb_lg 붙해기 : 하단여백을 위해-->
            <div class="bottom_btn  ">
                <div class="form-row">
                    <div class="col-12"><button type="button" class="btn btn-primary btn-block bnt-lg" onclick="location.href='./sign01.php'">하단 플로팅버튼</button></div>
                </div>
            </div>
        </div>

    </div>
</div>


<? include_once("./inc/tail.php"); ?>