<?
$_SUB_HEAD_TITLE = "예약하기"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
    <div class="sub_pg pb_lg">
        <div class="container pb-5">
            <p class="fs_18 fw_600 mt-5">예약 날짜를 선택하세요</p>
            <div class="calendar border">
                <div class="calendar-header">
                    <a href="#" class="arrow mr-2" id="prevMonth"><img src="./img/pg_prev.png" width="17px"></a>
                    <span id=" " class="mx-3">2025. 11</span>
                    <a href="#" class="arrow" id="nextMonth"><img src="./img/pg_next.png" width="17px"></a>

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
                    <tbody id="calendarBody">
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="saturday">
                                <input type="radio" name="calendar_date" id="cal_1" value="2025-11-01">
                                <label for="cal_1">1</label>
                            </td>
                        </tr>
                        <tr>
                            <td class="sunday">
                                <input type="radio" name="calendar_date" id="cal_2" value="2025-11-02">
                                <label for="cal_2">2</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_3" value="2025-11-03">
                                <label for="cal_3">3</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_4" value="2025-11-04">
                                <label for="cal_4">4</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_5" value="2025-11-05" disabled="">
                                <label for="cal_5">5</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_6" value="2025-11-06" checked>
                                <label for="cal_6">6</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_7" value="2025-11-07">
                                <label for="cal_7">7</label>
                            </td>
                            <td class="saturday">
                                <input type="radio" name="calendar_date" id="cal_8" value="2025-11-08">
                                <label for="cal_8">8</label>
                            </td>
                        </tr>
                        <tr>
                            <td class="sunday">
                                <input type="radio" name="calendar_date" id="cal_9" value="2025-11-09">
                                <label for="cal_9">9</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_10" value="2025-11-10">
                                <label for="cal_10">10</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_11" value="2025-11-11">
                                <label for="cal_11">11</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_12" value="2025-11-12">
                                <label for="cal_12">12</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_13" value="2025-11-13">
                                <label for="cal_13">13</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_14" value="2025-11-14">
                                <label for="cal_14">14</label>
                            </td>
                            <td class="saturday">
                                <input type="radio" name="calendar_date" id="cal_15" value="2025-11-15">
                                <label for="cal_15">15</label>
                            </td>
                        </tr>
                        <tr>
                            <td class="sunday">
                                <input type="radio" name="calendar_date" id="cal_16" value="2025-11-16">
                                <label for="cal_16">16</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_17" value="2025-11-17">
                                <label for="cal_17">17</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_18" value="2025-11-18">
                                <label for="cal_18">18</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_19" value="2025-11-19">
                                <label for="cal_19">19</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_20" value="2025-11-20">
                                <label for="cal_20">20</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_21" value="2025-11-21">
                                <label for="cal_21">21</label>
                            </td>
                            <td class="saturday">
                                <input type="radio" name="calendar_date" id="cal_22" value="2025-11-22">
                                <label for="cal_22">22</label>
                            </td>
                        </tr>
                        <tr>
                            <td class="sunday">
                                <input type="radio" name="calendar_date" id="cal_23" value="2025-11-23">
                                <label for="cal_23">23</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_24" value="2025-11-24">
                                <label for="cal_24">24</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_25" value="2025-11-25">
                                <label for="cal_25">25</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_26" value="2025-11-26">
                                <label for="cal_26">26</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_27" value="2025-11-27">
                                <label for="cal_27">27</label>
                            </td>
                            <td class="">
                                <input type="radio" name="calendar_date" id="cal_28" value="2025-11-28">
                                <label for="cal_28">28</label>
                            </td>
                            <td class="saturday">
                                <input type="radio" name="calendar_date" id="cal_29" value="2025-11-29">
                                <label for="cal_29">29</label>
                            </td>
                        </tr>
                        <tr>
                            <td class="sunday">
                                <input type="radio" name="calendar_date" id="cal_30" value="2025-11-30">
                                <label for="cal_30">30</label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="container border-top py-5">
            <p class="fs_18 fw_600">예약 시간 선택하세요</p>

            <div class="mx_n16 mt-4">
                <div id=" " class="scroll_bar_none scroll_mouse">
                    <div class="btn-group btn-group-toggle px_16" data-toggle="buttons">
                        <label class="btn btn-outline-primary btn-md rounded-pill active">
                            <input type="radio" name="options" id="option1" checked=""> 12:00
                        </label>
                        <label class="btn btn-outline-primary btn-md rounded-pill disabled">
                            <input type="radio" name="options" id="option2"> 13:00
                        </label>
                        <label class="btn btn-outline-primary btn-md rounded-pill">
                            <input type="radio" name="options" id="option3"> 14:00
                        </label>
                        <label class="btn btn-outline-primary btn-md rounded-pill"  >
                            <input type="radio" name="options" id="option4" disabled> 15:00
                        </label>
                        <label class="btn btn-outline-primary btn-md rounded-pill">
                            <input type="radio" name="options" id="option5"> 16:00
                        </label>
                        <label class="btn btn-outline-primary btn-md rounded-pill">
                            <input type="radio" name="options" id="option6"> 17:00
                        </label>
                    
                    </div>
                </div>
            </div>

        </div>
        <div class="container border-top py-5">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="fs_18 fw_600">인원을 선택하세요</p>
                    <p class="fs_13 tg_400 mt-2">성인, 유아 구분없이 전체 인원 선택</p>
                </div>
                <div>
                    <div class="item_opt_counter">
                        <button type="button" class="btn item_opt_counter_btn  ">
                            <img src="./img/ico_decrease.svg" alt="감소">
                        </button>
                        <input type="text" class="quantity" value="1">
                        <button type="button" class="btn item_opt_counter_btn ">
                            <img src="./img/ico_increase.svg" alt="증가">
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="bar">
        </div>
        <div class="container py-5">
            <p class="fs_18 fw_600">예약자 정보</p>
            <div class="form_wr mt_20 ">
                <div class="ip_tit">
                    <h5>예약자명</h5>
                </div>
                <input type="text" class="form-control" placeholder="이름을 입력하세요">
            </div>
            <div class="form_wr mt_20 ">
                <div class="ip_tit">
                    <h5>휴대폰번호</h5>
                </div>
                <input type="text" class="form-control" placeholder="'-' 없이 숫자만 입력">
            </div>
        </div>
        <div class="bar">
        </div>
        <div class="container py-5">
            <p class="fs_18 fw_600"><img src="./img/ico_alim.png" width="31px"> 예약 전 반드시 확인하세요!</p>
            <p class="fw_600 mt-4">노쇼 / 당일 예약취소는 환불이 불가능합니다.</p>
            <ul class="list_style_2 fs_15 mt-3">
                <li>건전한 예약문화를 위해 노쇼 / 당일 예약취소 건에 대해서는 환불이 불가능합니다.</li>
                <li>예약관련에 대한 경고 관리글이 있으면 좋을거같아요 (관리자가 공통 작성)</li>
            </ul>
            <p class="mt-3 fw_600">예약완료 후 업체의 확인연락 후 예약이 확정됩니다.</p>
        </div>



        <div class="bottom_btn tg_600 ">
            <p class="mb-3 text-center fs_15">모든 내용을 확인하셨나요?</p>
            <div class="form-row">
                <div class="col-12"><button type="button" class="btn btn-primary btn-block btn-lg" onclick="location.href='./rsrv_cmp.php'">즉시 예약</button></div>
            </div>
        </div>
    </div>
</div>


<? include_once("./inc/tail.php"); ?>