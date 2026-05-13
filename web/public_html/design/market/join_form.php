<?
$_SUB_HEAD_TITLE = "회원가입";
$_GET['hd_pc'] = 'logout'; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ' '; //왼쪽메뉴 active 땜시 만듬
include_once("./inc/head.php");
?>


<div class="sub_pg pl-0 ">
    <div class="join_form_wr">
        <div class="hd_tit">
            <h2 class="tit_st1 d-flex align-items-center"><a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 "><img src="./img/ico_back.svg" alt=" 뒤로가기"></a><span>회원가입</span></h2>
        </div>
        <form>
            <div class="join_form">
                <div class="pb-5">
                    <p class="tit_st3 "><img src="./img/join_ico1.svg" alt=" 이미지" class="mr-3">기본정보</p>
                    <div class="row">
                        <div class="col-md-6 mt-5">
                            <div class="form_wr ip_invalid" id="id_div">
                                <div class="ip_tit required  ">
                                    <h5>아이디</h5>
                                </div>
                                <div class="form-row ">
                                    <div class="col-8">
                                        <input type="text" class="form-control" placeholder="아이디 입력">
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn btn-secondary btn-block px-1">중복 확인</button>
                                    </div>
                                </div>
                                <div class="form-text ip_invalid">사용할수없는 아이디입니다.</div>
                            </div>

                            <div class="form_wr  mt-5 ip_valid">
                                <div class="ip_tit required">
                                    <h5>비밀번호</h5>
                                </div>
                                <input type="text" class="form-control" placeholder="비밀번호 입력">
                                <div class="form-text ip_invalid">비밀번호를 입력해주세요</div>
                            </div>
                            <div class="form_wr  mt-5 ip_valid">
                                <div class="ip_tit required">
                                    <h5>비밀번호 재입력 </h5>
                                </div>
                                <input type="text" class="form-control" placeholder="비밀번호 재입력">
                                <div class="form-text ip_invalid">비밀번호가 일치하지않습니다.</div>
                            </div>

                        </div>
                        <div class="col-md-6 mt-5">
                            <div class="form_wr">
                                <div class="ip_tit required">
                                    <h5>이름 </h5>
                                </div>
                                <input type="text" class="form-control" placeholder="이름 입력">
                                <div class="form-text ip_invalid">반대문구</div>
                            </div>
                            <div class="form_wr mt-5 ip_invalid">
                                <div class="ip_tit required">
                                    <h5>휴대폰번호</h5>
                                </div>
                                <div class="form-row">
                                    <div class="col">
                                        <input type="text" class="form-control" placeholder="‘-’ 없이 숫자만 입력">
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn btn-secondary btn-block  px-1">인증 요청</button>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col mt-3 position-relative">
                                        <p class="time_lim">04:25</p>
                                        <input type="text" class="form-control" placeholder="인증번호 입력">
                                    </div>
                                    <div class="col-4 mt-3">
                                        <button type="button" class="btn btn-primary btn-block  " disabled="">확인</button>
                                    </div>
                                </div>
                                <div class="form-text ip_invalid">오류 텍스트</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 border-top pt-5 pb-5">
                    <p class="tit_st3   "><img src="./img/join_ico2.svg" alt="이미지" class="mr-3">사업자(매장) 정보</p>

                    <div class="row">
                        <div class="col-md-6 mt-5">
                            <div class="form_wr">
                                <div class="ip_tit required">
                                    <h5>상호(법인명) </h5>
                                </div>
                                <input type="text" class="form-control" placeholder="사업자등록증에 기재된 상호(법인명) 입력">
                                <div class="form-text ip_invalid">반대문구</div>
                            </div>
                            <div class="form_wr mt-5">
                                <div class="ip_tit required">
                                    <h5>사업자등록번호 </h5>
                                </div>
                                <input type="text" class="form-control" placeholder="입력하세요">
                                <div class="form-text ip_invalid">반대문구</div>
                            </div>
                            <div class="form_wr mt-5">
                                <div class="ip_tit required">
                                    <h5>매장명</h5>
                                </div>
                                <input type="text" class="form-control" placeholder="매장명 입력">
                                <div class="form-text ip_invalid">반대문구</div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-5">
                            <div class="form_wr  ">
                                <div class="ip_tit required">
                                    <h5>대표자명</h5>
                                </div>
                                <input type="text" class="form-control" placeholder="대표자명 입력">
                                <div class="form-text ip_invalid">반대문구</div>
                            </div>

                            <div class="form_wr mt-5 ip_invalid">
                                <div class="ip_tit required">
                                    <h5>주소</h5>
                                </div>
                                <div class="form-row">
                                    <div class="col">
                                        <input type="text" class="form-control" placeholder="우편번호 검색시 자동등록" disabled>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn btn-secondary btn-block  px-1">우편번호 검색</button>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <input type="text" class="form-control" placeholder="우편번호 검색시 자동등록" disabled>
                                </div>
                                <div class="mt-3">
                                    <input type="text" class="form-control" placeholder="상세주소">
                                </div>
                                <div class="form-text ip_invalid">오류 텍스트</div>
                            </div>
                            <div class="form_wr  mt-5">
                                <div class="ip_tit required">
                                    <h5>사업자등록증 </h5>
                                </div>
                                <div class="d-flex">
                                    <div class="image_upload">
                                        <input id="ip_file" type="file" class="d-none">
                                        <label for="ip_file" class="upload_box">
                                            <div class="rect">
                                            </div>
                                            <p class="max_img">사진 1/1</p>
                                        </label>
                                        <button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
                                    </div>
                                    <div class="image_upload on">
                                        <input id="ip_file" type="file" class="d-none">
                                        <label for="ip_file" class="upload_box">
                                            <div class="rect">
                                                <img src="./img/pr_sample01.jpg">
                                            </div>
                                        </label>
                                        <button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
                                    </div>

                                </div>
                                <div class="form-text ip_invalid">반대문구</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 border-top pt-5 pb-5">
                    <p class="tit_st3   "><img src="./img/join_ico3.svg" alt="이미지" class="mr-3">정산 정보</p>

                    <div class="row">
                        <div class="col-md-6 mt-5">
                            <div class="form_wr   ">
                                <div class="ip_tit required">
                                    <h5>통장사본 파일첨부 </h5>
                                </div>
                                <div class="d-flex">
                                    <div class="image_upload">
                                        <input id="ip_file" type="file" class="d-none">
                                        <label for="ip_file" class="upload_box">
                                            <div class="rect">
                                            </div>
                                            <p class="max_img">사진 1/1</p>
                                        </label>
                                        <button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
                                    </div>
                                    <div class="image_upload on">
                                        <input id="ip_file" type="file" class="d-none">
                                        <label for="ip_file" class="upload_box">
                                            <div class="rect">
                                                <img src="./img/pr_sample01.jpg">
                                            </div>
                                        </label>
                                        <button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
                                    </div>

                                </div>
                                <div class="form-text ip_invalid">반대문구</div>
                            </div>

                        </div>
                        <div class="col-md-6 mt-5">
                            <div class="form_wr  ">
                                <div class="ip_tit required  ">
                                    <h5>정산 받을계좌 </h5>
                                </div>
                                <div class="form-row ">
                                    <div class="col-12 mb-3">
                                        <div class="custom-sel">
                                            <button type="button" class="select-trigger">
                                               은행선택
                                            </button>
                                            <ul class="select-options">
                                                <li data-value="1">옵션 1</li>
                                                <li data-value="2">옵션 2</li>
                                                <li data-value="3">옵션 3 </li>
                                                <li data-value="4">옵션 4</li>
                                                <li data-value="5">옵션 5</li>
                                                <li data-value="6">옵션 6</li>
                                                <li data-value="7">옵션 7</li>
                                                <li data-value="8">옵션 8</li>
                                            </ul>

                                            <input type="hidden" name="option">
                                        </div>
                                    </div>
                                    <div class="col-12  mb-3">
                                        <input type="text" class="form-control" placeholder="예금주 입력">
                                    </div>
                                    <div class="col-12  mb-3">
                                        <input type="text" class="form-control" placeholder="‘-’ 없이 계좌번호 입력해주세요.">
                                    </div>
                                </div>
                                <div class="form-text ip_invalid">계좌정보를 입력하세요</div>
                            </div>
                        

                        </div>

                        <div class="col-12 mt-5">
                            <div class="form_wr mt-5  ">
                                <div class="ip_tit required">
                                    <h5>약관 동의 </h5>
                                </div>

                                <div class="p-4 bg-light rounded mb-4">
                                    <div class="checks w-100 m-0 d-flex justify-content-between align-items-center">
                                        <label class="d-flex  align-items-center w-100">
                                            <input type="checkbox" name="chk1">
                                            <span class="ic_box"></span>
                                            <div class="  fw_600">
                                                <p>전체 동의합니다.</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div id="terms_wr" class="terms_checks pl-4">
                                    <ul>
                                        <li id="terms_hd01">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="checks_wr mb-0">
                                                    <div class="checks mb-0">
                                                        <label>
                                                            <input type="checkbox" name="chk1">
                                                            <span class="ic_box"></span>
                                                            <div class="chk_p  ">
                                                                <p>개인정보처리방침 (필수) </p>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-link   collapse_bt" data-toggle="collapse" data-target="#terms01">
                                                    <div class=""></div>
                                                </button>
                                            </div>
                                            <div id="terms01" class="collapse " data-parent="#terms_wr">
                                                <div class="terms_cont bg-light   rounded mt-3">
                                                    <div class="edit_style   ">
                                                        <h4 class="tit_st4">제1조 (목적)밥을 동산에는 것은 천하를 사막이다.</h4>
                                                        <p><br></p>
                                                        <p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.제1조 (목적)밥을 동산에는 것은 천하를 사막이다.
                                                        </p>
                                                        <p><br></p>
                                                        <p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.<br></p>
                                                    </div>

                                                </div>
                                            </div>
                                        </li>
                                        <li id="terms_hd02" class="mt_20 ">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="checks_wr mb-0">
                                                    <div class="checks mb-0">
                                                        <label>
                                                            <input type="checkbox" name="chk1">
                                                            <span class="ic_box"></span>
                                                            <div class="chk_p  ">
                                                                <p>이용약관 (필수) </p>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-link   collapse_bt" data-toggle="collapse" data-target="#terms02">
                                                    <div class=""></div>
                                                </button>
                                            </div>
                                            <div id="terms02" class="collapse" data-parent="#terms_wr">
                                                <div class="terms_cont bg-light  rounded mt-3">
                                                    <div class="edit_style  ">
                                                        <h4 class="tit_st4">제1조 (목적)밥을 동산에는 것은 천하를 사막이다.</h4>
                                                        <p><br></p>
                                                        <p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.제1조 (목적)밥을 동산에는 것은 천하를 사막이다.
                                                        </p>
                                                        <p><br></p>
                                                        <p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.<br></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                    </ul>
                                </div>


                                <div class="text-center mt_50">
                                    <button type="button" class="btn btn-primary btn-lg btn-w1" onclick="location.href='./join_cmp.php'">회원가입</button>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

</div>





<? include_once("./inc/tail.php"); ?>