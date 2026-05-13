<?
$_SUB_HEAD_TITLE = "메인화면";
$_GET['hd_pc'] = '1'; //PC hd  로그인시공백 / 로그아웃시 logout
$hd_num = 'revenue'; //1차메뉴
$hd_left = 'reserve_hst'; //왼쪽메뉴 on 땜시 만듬
include_once("./inc/head.php");
?>
<!-- 왼쪽 메뉴-->
<? include_once("./inc/left_menu.php"); ?>

<style>
   .wrap {
      background-color: #fff;
   }
</style>


<div class="sub_pg bg-white">
   <div class="rev_list_wr">
      <div class="rev_list">
         <div class="rev_list_box">
            <h2 class="tit_st2 mt_50">예약관리</h2>
            <div class="calendar_wp">
               <div class="wp_l flex-fill">
                  <div class="calendar calendar_tutor">
                     <div class="calendar-header">
                        <a href="#" class="arrow mr-3" id="prevMonth"><img src="../img/pg_prev.svg"></a>
                        <p id="calendarMonthYear"> 2025. 09 </p>
                        <a href="#" class="arrow  " id="nextMonth"><img src="../img/pg_next.svg"></a>

                     </div>
                     <table>
                        <thead>
                           <tr>
                              <th>일</th>
                              <th>월</th>
                              <th>화</th>
                              <th>수</th>
                              <th>목</th>
                              <th>금</th>
                              <th>토</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td><input type="radio" name="date" id="d1"><label for="d1">1</label></td>
                              <td><input type="radio" name="date" id="d2"><label for="d2">2</label></td>
                           </tr>
                           <tr>
                              <td class="sunday"><input type="radio" name="date" id="d3"><label for="d3">3</label></td>
                              <td><input type="radio" name="date" id="d4" checked><label for="d4">4</label></td>
                              <td><input type="radio" name="date" id="d5"><label for="d5">5</label></td>
                              <td><input type="radio" name="date" id="d6"><label for="d6">6</label></td>
                              <td><input type="radio" name="date" id="d7"><label for="d7">7</label></td>
                              <td><input type="radio" name="date" id="d8" class="rev_date"><label for="d8">8</label>
                                 <div class="resv_point">
                                    <span class="point_num">+3</span>
                                 </div>
                              </td>
                              <td><input type="radio" name="date" id="d9"><label for="d9">9</label></td>
                           </tr>
                           <tr>
                              <td class="sunday"><input type="radio" name="date" id="d10" class="rev_date"><label for="d10">10</label>
                                 <div class="resv_point">
                                    <span class="point_num">+1</span>
                                 </div>
                              </td>
                              <td><input type="radio" name="date" id="d11"><label for="d11">11</label></td>
                              <td><input type="radio" name="date" id="d12"><label for="d12">12</label></td>
                              <td><input type="radio" name="date" id="d13"><label for="d13">13</label></td>
                              <td><input type="radio" name="date" id="d14"><label for="d14">14</label></td>
                              <td><input type="radio" name="date" id="d15"><label for="d15">15</label></td>
                              <td><input type="radio" name="date" id="d16"><label for="d16">16</label></td>
                           </tr>
                           <tr>
                              <td class="sunday"><input type="radio" name="date" id="d17"><label for="d17">17</label></td>
                              <td><input type="radio" name="date" id="d18"><label for="d18">18</label></td>
                              <td><input type="radio" name="date" id="d19"><label for="d19">19</label></td>
                              <td><input type="radio" name="date" id="d20"><label for="d20">20</label></td>
                              <td><input type="radio" name="date" id="d21"><label for="d21">21</label></td>
                              <td><input type="radio" name="date" id="d22"><label for="d22">22</label></td>
                              <td><input type="radio" name="date" id="d23"><label for="d23">23</label></td>
                           </tr>
                           <tr>
                              <td class="sunday"><input type="radio" name="date" id="d24"><label for="d24">24</label></td>
                              <td><input type="radio" name="date" id="d25"><label for="d25">25</label></td>
                              <td><input type="radio" name="date" id="d26"><label for="d26">26</label></td>
                              <td><input type="radio" name="date" id="d27"><label for="d27">27</label></td>
                              <td><input type="radio" name="date" id="d28"><label for="d28">28</label></td>
                              <td><input type="radio" name="date" id="d29"><label for="d29">29</label></td>
                              <td><input type="radio" name="date" id="d30" disabled><label for="d30">30</label></td>
                           </tr>
                           <!-- 나머지 날짜도 동일하게 반복 -->
                        </tbody>
                     </table>
                     <div class="point_ex">
                        <p class=""><span class="point_ico mr-2"></span>예약날</p>

                     </div>
                  </div>
               </div>

            </div>
         </div>

      </div>
      <div class="rev_list_dtl">
         <div class="rev_list_hd mt_30  ">
            <div class="  ml-auto   pl-2">
               <form class="sch_ip border align-items-center">
                  <input type="search" class="form-control   flex-fill border-0" placeholder="예약자명 검색">
                  <button class="btn btn-icon flex-shrink-0"><img src="./img/ic_ip_sch.svg"></button>
               </form>
            </div>
            <div class="d-flex align-items-end flex-fill   ">
               <div class="  btn-group-toggle rev_btn_g " data-toggle="buttons">
                  <label class="btn btn-outline-secondary   active">
                     <input type="radio" name="options" id="option1" checked=""> 오늘 예약 <span class="ml-2"> 4건</span>
                  </label>
                  <label class="btn btn-outline-secondary  ">
                     <input type="radio" name="options" id="option2"> 확정예약 <span class="ml-2"> 5건</span>
                  </label>
                  <label class="btn btn-outline-secondary  ">
                     <input type="radio" name="options" id="option3"> 대기중 <span class="ml-2"> 2건</span>
                  </label>
               </div>
            </div>

         </div>
         <section class="rev_card_list">
            <div class="card">
               <div class="card-header">
                  <h4 class="tit_st4 d-flex align-items-center"> <img src="./img/ico_calender3.svg" class="mr-2"> 12월 9일(화) 12:00</h4>
               </div>
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div class="mr-2">

                        <p class="tit_st4"> <span class="text-primary mr-2">예약대기</span> <span class="d-inline-block">김이름 외 3명(010-1234-5678)</span></p>
                        <p class="fs_16 tg_500 mt-3">해물칼국수 1개, 키토만땅하는 계란 왕창 김밥1개,해물칼국수 1개, 키토만땅하는 계란 왕창 김밥1개</p>
                        <a href="" class="item_link" data-toggle="modal" data-target="#modal_rev1"></a>
                     </div>
                     <button type="button" class="btn btn-gray ml-auto rev_btn">예약 거절</button>
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-header">
                  <h4 class="tit_st4 d-flex align-items-center"> <img src="./img/ico_calender3.svg" class="mr-2"> 12월 9일(화) 12:00</h4>
               </div>
               <div class="card-body">
                  <div class=" card_wr d-flex align-items-center">
                     <div class="mr-2">
                        <p class="tit_st4"> <span class="text-blue mr-2">예약확정</span> <span class="d-inline-block">김이름 외 3명(010-1234-5678)</span></p>
                        <p class="fs_16 tg_500 mt-3">후불결제입니다.</p>
                        <a href="" class="item_link" data-toggle="modal" data-target="#modal_rev2"></a>
                     </div>
                     <button type="button" class="btn btn-primary ml-auto rev_btn">도착 확인</button>
                  </div>
                  <div class="card_wr d-flex align-items-center">
                     <div class="mr-2">
                        <p class="tit_st4"> <span class="text-blue mr-2">예약확정</span> <span class="d-inline-block">김이름 외 3명(010-1234-5678)</span></p>
                        <p class="fs_16 tg_500 mt-3">해물칼국수 1개, 키토만땅하는 계란 왕창 김밥1개</p>
                        <a href="" class="item_link" data-toggle="modal" data-target="#modal_rev2"></a>
                     </div>
                     <button type="button" class="btn btn-outline-secondary ml-auto rev_btn">예약 취소</button>
                  </div>
               </div>

            </div>

         </section>


      </div>
   </div>

</div>

<!-- data-toggle="modal" data-target="#modal_rev1"D-2 예약 상세(예약접수)(모달)-->
<div class="modal modal_rr fade" id="modal_rev1" tabindex="-1" aria-hidden="true">
   <div class="modal-dialog ">
      <div class="modal-content">
         <button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="./img/ic_close.png" alt="닫기"></button>

         <div class="modal-body">
            <div class="d-flex align-items-center justify-content-between">
               <span class="status status_01">예약대기</span>
               <p class="d-flex align-items-center justify-content-center fs_16 tg_500">
                  <span class="mr-1"><img src="./img/ico_time.svg" alt=" "></span>
                  <span>1분 전</span>
               </p>
            </div>
            <div class=" detail_hd mt-4">
               <div>
                  <h3 class="tit_st1">예약주문</h3>
                  <p class="mt-2 fw_600">메뉴3개 ㆍ224,100원</p><!-- 예약에서 메뉴 주문 안하면 이곳은 없음-->
               </div>
               <div>
                  <button type="button" class="btn btn-primary  mr-3">접수</button><button type="button" class="btn btn-light ">거절</button>
               </div>
            </div>
            <section class="bill_wr">
               <div class="py-4 border-bottom-dot mb-4">
                  <span class="mr-4">예약 번호 : No.00000001</span>
                  <span>예약일시 : 2025년 08월 09일 15:00</span>
               </div>
               <ul class="bill_list mb-5">
                  <li class="d-flex align-items-center justify-content-between ">
                     <p class="tit_st3">예약정보</p>
                     <button type="button" class="btn btn-md btn-outline-light bg-light rounded-pill px-4 rev_date_btn"><span class="mr-2"><img src="./img/ico_edit.svg" alt=" "></span>예약일시 변경</button>
                     <!-- 예약일시 변경을 누르면 변경할수있는 .rev_date_div가 나타납니다. -->
                  </li>

                  <script>
                     document.querySelector('.rev_date_btn').addEventListener('click', function() {
                        document.querySelector('.rev_date_div').classList.toggle('on');
                     });
                  </script>

                  <li class=" ">
                     <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class=" ">예약일시</p>
                        <p class="fw_700 fs_20 ">2025.07.16 13:00</p>
                     </div>
                     <div class="d-flex align-items-center justify-content-between  mb-2">
                        <p class=" ">예약자</p>
                        <p class="fw_700 fs_20 ">김이름(010-1234-5678)</p>
                     </div>
                     <div class="d-flex align-items-center justify-content-between  mb-2">
                        <p class=" ">예약인원</p>
                        <p class="fw_700 fs_20 ">3명</p>
                     </div>
                  </li>
                  <li class="rev_date_div">
                     <div class="  mb-5">
                        <p class="d-flex align-content-center mb-4 fw_700 "><img src="./img/img_mark2.svg" class="mr-2" alt=" "> 예약일시는 꼭 예약자와 상담 후 바꿔주세요</p>
                        <p class="fw_700"><input type="date" class="form-control   "></p>
                     </div>
                     <div class="  mb-4">
                        <p class=" mb-2  fw_700 " style="width: 8rem;">예약 시간</p>
                        <div id=" " class=" ">
                           <div class="  btn-group-toggle btn_gp_st2  " data-toggle="buttons">
                              <label class="btn btn-outline-light   btn-md rounded-pill active">
                                 <input type="radio" name="options" id="option1" checked=""> 12:00
                              </label>
                              <label class="btn btn-outline-light   btn-md rounded-pill disabled">
                                 <input type="radio" name="options" id="option2"> 13:00
                              </label>
                              <label class="btn btn-outline-light   btn-md rounded-pill">
                                 <input type="radio" name="options" id="option3"> 14:00
                              </label>
                              <label class="btn btn-outline-light   btn-md rounded-pill">
                                 <input type="radio" name="options" id="option4"> 15:00
                              </label>
                              <label class="btn btn-outline-light   btn-md rounded-pill">
                                 <input type="radio" name="options" id="option5"> 16:00
                              </label>
                              <label class="btn btn-outline-light   btn-md rounded-pill">
                                 <input type="radio" name="options" id="option6"> 17:00
                              </label>

                           </div>
                        </div>
                     </div>
                     <button type="button" class="btn btn-secondary btn-block">예약일시 변경완료</button>
                  </li>

                  <li class="border-bottom border-dark">
                  </li>

               </ul>
               <ul class="bill_list">
                  <li class="d-flex align-items-center justify-content-between ">
                     <p class="tit_st3">주문메뉴</p>
                     <button type="button" class="btn btn-md btn-outline-light bg-light rounded-pill px-4 " data-toggle="modal" data-target="#modal_tbl2" data-dismiss="modal"><span class="mr-2"><img src="./img/ico_edit.svg" alt=" "></span>주문 변경</button>
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
               <p>홍길동 &#40;010-1234-5678&#41;</p>
            </div>

         </div>

      </div>
   </div>
</div>

<!-- data-toggle="modal" data-target="#modal_tbl2" B-3 주문수정(모달) -->
<div class="modal modal_rr fade" id="modal_tbl2" tabindex="-1" aria-hidden="true">
   <div class="modal-dialog ">
      <div class="modal-content">
         <button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="./img/ic_close.png" alt="닫기"></button>
         <div class="modal-body">

            <div class=" detail_hd mt-4">
               <h2 class="tit_st1 d-flex align-items-center"><a href="#" data-toggle="modal" data-target="#modal_tbl1" data-dismiss="modal" class="mr-4 line_h0 "><img src="./img/ico_back.svg" alt=" 뒤로가기"></a><span>주문 변경</span></h2>
               <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_tbl1" data-dismiss="modal">변경 완료</button>
            </div>
            <section class="py-5 border-top border-dark">

               <ul class="bill_list wide_gap">

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
                     <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="d-flex">
                           <div class="item_opt_counter mr-2">
                              <button type="button" class="btn item_opt_counter_btn pl-1" disabled=""><!-- 수량이 0일때 -->
                                 <img src="./img/ico_decrease.svg" alt="감소">
                              </button>
                              <input type="text" class="quantity" value="255">
                              <button type="button" class="btn item_opt_counter_btn pr-1">
                                 <img src="./img/ico_increase.svg" alt="증가">
                              </button>
                           </div>
                           <button type="button" class="btn btn-outline-light  " data-toggle="modal" data-target="#modal_tbl3" data-dismiss="modal">옵션 변경</button>
                        </div>
                        <div>
                           <button type="button" class="btn btn-outline-secondary  ">메뉴 삭제</button>
                        </div>
                     </div>
                  </li>
                  <li class="border-bottom"></li>
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
                     <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="d-flex">
                           <div class="item_opt_counter mr-2">
                              <button type="button" class="btn item_opt_counter_btn pl-1" disabled=""><!-- 수량이 0일때 -->
                                 <img src="./img/ico_decrease.svg" alt="감소">
                              </button>
                              <input type="text" class="quantity" value="255">
                              <button type="button" class="btn item_opt_counter_btn pr-1">
                                 <img src="./img/ico_increase.svg" alt="증가">
                              </button>
                           </div>
                           <button type="button" class="btn btn-outline-light  " data-toggle="modal" data-target="#modal_tbl3" data-dismiss="modal">옵션 변경</button>
                        </div>
                        <div>
                           <button type="button" class="btn btn-outline-secondary  ">메뉴 삭제</button>
                        </div>
                     </div>
                  </li>
                  <li class="border-bottom"></li>

               </ul>
            </section>
         </div>

      </div>
   </div>
</div>


<!-- data-toggle="modal" data-target="#modal_rev2" D-3 예약 상세(예약확정)(모달) -->
<div class="modal modal_rr fade" id="modal_rev2" tabindex="-1" aria-hidden="true">
   <div class="modal-dialog ">
      <div class="modal-content">
         <button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="./img/ic_close.png" alt="닫기"></button>

         <div class="modal-body">
            <div class="d-flex align-items-center justify-content-between">
               <span class="status status_02">예약확정</span>
               <p class="d-flex align-items-center justify-content-center fs_16 tg_500">
                  <span class="mr-1"><img src="./img/ico_time.svg" alt=" "></span>
                  <span>1분 전</span>
               </p>
            </div>
            <div class=" detail_hd mt-4">
               <div>
                  <h3 class="tit_st1">예약주문</h3>
                  <p class="mt-2 fw_600">메뉴3개 ㆍ224,100원</p><!-- 예약에서 메뉴 주문 안하면 이곳은 없음-->
               </div>
               <button type="button" class="btn btn-gray">예약취소</button>
            </div>
            <section class="bill_wr">
               <div class="py-4 border-bottom-dot mb-4">
                  <span class="mr-4">예약 번호 : No.00000001</span>
                  <span>예약일시 : 2025년 08월 09일 15:00</span>
               </div>
               <ul class="bill_list mb-5">
                  <li class="d-flex align-items-center justify-content-between ">
                     <p class="tit_st3">예약정보</p>
                     <button type="button" class="btn btn-md btn-outline-light bg-light rounded-pill px-4 rev_date_btn"><span class="mr-2"><img src="./img/ico_edit.svg" alt=" "></span>예약일시 변경</button>
                     <!-- 예약일시 변경을 누르면 변경할수있는 .rev_date_div가 나타납니다. -->
                  </li>

                  <script>
                     document.querySelector('.rev_date_btn').addEventListener('click', function() {
                        document.querySelector('.rev_date_div').classList.toggle('on');
                     });
                  </script>

                  <li class=" ">
                     <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class=" ">예약일시</p>
                        <p class="fw_700 fs_20 ">2025.07.16 13:00</p>
                     </div>
                     <div class="d-flex align-items-center justify-content-between  mb-2">
                        <p class=" ">예약자</p>
                        <p class="fw_700 fs_20 ">김이름(010-1234-5678)</p>
                     </div>
                     <div class="d-flex align-items-center justify-content-between  mb-2">
                        <p class=" ">예약인원</p>
                        <p class="fw_700 fs_20 ">3명</p>
                     </div>
                  </li>
                  <li class="rev_date_div">
                     <div class="  mb-5">
                        <p class="d-flex align-content-center mb-4 fw_700 "><img src="./img/img_mark2.svg" class="mr-2" alt=" "> 예약일시는 꼭 예약자와 상담 후 바꿔주세요</p>
                        <p class="fw_700"><input type="date" class="form-control   "></p>
                     </div>
                     <div class="  mb-4">
                        <p class=" mb-2  fw_700 " style="width: 8rem;">예약 시간</p>
                        <div id=" " class=" ">
                           <div class="  btn-group-toggle btn_gp_st2  " data-toggle="buttons">
                              <label class="btn btn-outline-light   btn-md rounded-pill active">
                                 <input type="radio" name="options" id="option1" checked=""> 12:00
                              </label>
                              <label class="btn btn-outline-light   btn-md rounded-pill disabled">
                                 <input type="radio" name="options" id="option2"> 13:00
                              </label>
                              <label class="btn btn-outline-light   btn-md rounded-pill">
                                 <input type="radio" name="options" id="option3"> 14:00
                              </label>
                              <label class="btn btn-outline-light   btn-md rounded-pill">
                                 <input type="radio" name="options" id="option4"> 15:00
                              </label>
                              <label class="btn btn-outline-light   btn-md rounded-pill">
                                 <input type="radio" name="options" id="option5"> 16:00
                              </label>
                              <label class="btn btn-outline-light   btn-md rounded-pill">
                                 <input type="radio" name="options" id="option6"> 17:00
                              </label>

                           </div>
                        </div>
                     </div>
                     <button type="button" class="btn btn-secondary btn-block">예약일시 변경완료</button>
                  </li>

                  <li class="border-bottom border-dark">
                  </li>

               </ul>
               <ul class="bill_list">
                  <li class="d-flex align-items-center justify-content-between ">
                     <p class="tit_st3">주문메뉴</p>
                     <button type="button" class="btn btn-md btn-outline-light bg-light rounded-pill px-4 " data-toggle="modal" data-target="#modal_tbl2" data-dismiss="modal"><span class="mr-2"><img src="./img/ico_edit.svg" alt=" "></span>주문 변경</button>
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

            <button type="button" class="btn btn-secondary btn-block mt-4">결제 취소</button>
            <div class="pay_cncl">
               <div class="form_wr  ">
                  <div class="ip_tit  ">
                     <h5 class="   text-white">결제취소/환불 금액(원)</h5>
                  </div>
                  <div class="form-row ">
                     <div class="col-6">
                        <input type="text" class="form-control" placeholder="0">
                     </div>
                     <div class="col-3">
                        <button type="button" class="btn btn-primary btn-block px-1">확인</button>
                     </div>
                     <div class="col-3">
                        <button type="button" class="btn btn-outline-light btn-block px-1">취소</button>
                     </div>
                  </div>

               </div>
            </div>
            <div class="mt-4 d-flex align-items-center justify-content-between bg-light p-5 rounded">
               <p class="fw_600">고객정보</p>
               <p>홍길동 &#40;010-1234-5678&#41;</p>
            </div>

         </div>

      </div>
   </div>
</div>





<? include_once("./inc/tail.php"); ?>