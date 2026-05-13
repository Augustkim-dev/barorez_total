<? if($_ADMIN_HEADER != false){ ?>
    </div>
    <!-- //END PAGE CONTENT -->
    </div>
    <!-- //END PAGE WRAPPER -->
<?php } ?>
    <!-- IMPORTANT SCRIPTS -->

    <script type="text/javascript" src="<?=MNG_HTTP?>/js/vendors/jquery/jquery-migrate.min.js"></script>
    <script type="text/javascript" src="<?=MNG_HTTP?>/js/vendors/bootstrap/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="<?=MNG_HTTP?>/js/vendors/mcustomscrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <!-- END IMPORTANT SCRIPTS -->

    <!-- THIS PAGE SCRIPTS ONLY -->
    <script type="text/javascript" src="<?=MNG_HTTP?>/js/vendors/moment/moment-with-locales.min.js"></script>
    <!--<script type="text/javascript" src="<?=MNG_HTTP?>/js/vendors/echarts/echarts.min.js"></script>-->
    <script type="text/javascript" src="<?=MNG_HTTP?>/js/vendors/select2/select2.min.js"></script>
    <script type="text/javascript" src="<?=MNG_HTTP?>/js/vendors/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript" src="<?=MNG_HTTP?>/js/vendors/raty/jquery.raty.js"></script>
    <!-- //END THIS PAGE SCRIPTS ONLY -->

    <!-- TEMPLATE SCRIPTS -->
    <script type="text/javascript" src="<?=MNG_HTTP?>/js/app.js?v=<?=$v_txt ?>"></script>
    <script type="text/javascript" src="<?=MNG_HTTP?>/js/plugins.js"></script>
    <script type="text/javascript" src="<?=MNG_HTTP?>/js/demo.js"></script>

    <!--<script type="text/javascript" src="<?=MNG_HTTP?>/js/settings.js?v=3"></script>-->
    <!-- END TEMPLATE SCRIPTS -->

    <script src="<?=MNG_HTTP?>/js/jquery.validate.min.js"></script>
    <!--<script type="text/javascript" src="<?=MNG_HTTP?>/js/demo_dashboard.js"></script>-->

    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Fancybox 스타일시트 -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">

    <iframe src="about:blank" name="hidden_ifrm" id="hidden_ifrm" style="width:100%;height:100px;display:none;"></iframe>

<? if($chk_post_code=="Y") { ?>
    <!-- 카카오맵 API -->
    <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=<?=KAKAO_JAVASCRIPT_KEY?>&libraries=services"></script>
    <script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
    <script>
        // 우편번호 찾기 찾기 화면을 넣을 element
        function foldDaumPostcode(obj_div) {
            var element_wrap = document.getElementById(obj_div);
            // iframe을 넣은 element를 안보이게 한다.
            element_wrap.style.display = 'none';
        }

        function DaumPostcode(obj_zip, obj_add1, obj_add2, obj_div, options) {
            var element_wrap = document.getElementById(obj_div);
            options = options || {}; // 옵션이 없으면 빈 객체로 초기화
            // 현재 scroll 위치를 저장해놓는다.
            var currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);

            new daum.Postcode({
                oncomplete: function(data) {
                    // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
                    //console.log(data);

                    // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                    // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                    var fullAddr = data.address; // 최종 주소 변수
                    var extraAddr = ''; // 조합형 주소 변수

                    // 기본 주소가 도로명 타입일때 조합한다.
                    if(data.addressType === 'R'){
                        //법정동명이 있을 경우 추가한다.
                        if(data.bname !== ''){
                            extraAddr += data.bname;
                        }
                        // 건물명이 있을 경우 추가한다.
                        if(data.buildingName !== ''){
                            extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                        }
                        // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                        fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                    }

                    // 우편번호와 주소 정보를 해당 필드에 넣는다.
                    document.getElementById(obj_zip).value = data.zonecode; //5자리 새우편번호 사용
                    document.getElementById(obj_add1).value = fullAddr;
                    document.getElementById(obj_add2).focus();


                    // 지번 주소 저장 (jibunAddress가 있으면 사용, 없으면 autoJibunAddress 사용)
                    var jibunAddr = data.jibunAddress || data.autoJibunAddress || '';

                    // 옵션에 따라 추가 필드 처리
                    if (options.hiddenFields) {
                        // 지번 주소 필드
                        if (options.hiddenFields.jibeon && document.getElementById(options.hiddenFields.jibeon)) {
                            document.getElementById(options.hiddenFields.jibeon).value = jibunAddr;
                        }

                        // 시도 필드
                        if (options.hiddenFields.sido && document.getElementById(options.hiddenFields.sido)) {
                            document.getElementById(options.hiddenFields.sido).value = data.sido || '';
                        }

                        // 구군 필드
                        if (options.hiddenFields.gugun && document.getElementById(options.hiddenFields.gugun)) {
                            document.getElementById(options.hiddenFields.gugun).value = data.sigungu || '';
                        }

                        // 동 필드
                        if (options.hiddenFields.dong && document.getElementById(options.hiddenFields.dong)) {
                            document.getElementById(options.hiddenFields.dong).value = data.bname || '';
                        }

                        // 행정동 필드
                        if (options.hiddenFields.hdong && document.getElementById(options.hiddenFields.hdong)) {
                            document.getElementById(options.hiddenFields.hdong).value = data.hname || data.bname || '';
                        }

                        // 위도 및 경도 필드가 있고 카카오맵 API가 로드된 경우
                        if ((options.hiddenFields.lat && document.getElementById(options.hiddenFields.lat)) ||
                            (options.hiddenFields.lng && document.getElementById(options.hiddenFields.lng))) {

                            try {
                                // 카카오맵 API가 로드되었는지 확인
                                if (typeof kakao !== 'undefined' && kakao.maps && kakao.maps.services) {
                                    // 주소로 좌표 검색
                                    var geocoder = new kakao.maps.services.Geocoder();

                                    // 주소로 좌표 검색 (도로명 주소 우선, 없으면 지번 주소 사용)
                                    var searchAddr = data.roadAddress || jibunAddr;
                                    geocoder.addressSearch(searchAddr, function(result, status) {
                                        if (status === kakao.maps.services.Status.OK) {
                                            // 위도 필드가 있으면 설정
                                            if (options.hiddenFields.lat && document.getElementById(options.hiddenFields.lat)) {
                                                document.getElementById(options.hiddenFields.lat).value = result[0].y;
                                            }

                                            // 경도 필드가 있으면 설정
                                            if (options.hiddenFields.lng && document.getElementById(options.hiddenFields.lng)) {
                                                document.getElementById(options.hiddenFields.lng).value = result[0].x;
                                            }

                                            console.log("좌표 정보 저장 완료:", result[0].y, result[0].x);

                                            // 좌표 정보가 업데이트된 후 지도 초기화
                                            if (typeof initMap === 'function') {
                                                setTimeout(initMap, 100); // 약간의 지연 후 지도 초기화
                                            }

                                        } else {
                                            console.error("좌표 변환 실패:", status);
                                        }
                                    });
                                } else {
                                    console.warn("카카오맵 API가 로드되지 않았습니다.");
                                }
                            } catch (e) {
                                console.error("좌표 변환 중 오류 발생:", e);
                            }
                        }
                    }



                    // iframe을 넣은 element를 안보이게 한다.
                    // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
                    element_wrap.style.display = 'none';

                    // 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
                    document.body.scrollTop = currentScroll;
                },
                // 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분. iframe을 넣은 element의 높이값을 조정한다.
                onresize : function(size) {
                    element_wrap.style.height = size.height+'px';
                },
                width : '100%',
                height : '100%'
            }).embed(element_wrap);

            // iframe을 넣은 element를 보이게 한다.
            element_wrap.style.display = 'block';
        }
    </script>
<? } ?>

    </body>
    </html>
<?php
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>
