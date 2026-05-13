<?php

if(!isset($_SESSION['mng']['mt_level']) && $_SERVER['PHP_SELF'] != "./login.php" ) {
    alert("관리자만 접근할 수 있습니다.", "/mng/login.php");
} else if (isset($_SESSION['mng']['mt_level']) && $_SESSION['mng']['mt_level'] !== 9 && $_SERVER['PHP_SELF'] != "./login.php" ) {
    alert("관리자만 접근할 수 있습니다.", "/mng/login.php");
}

$myadmin = get_mem_info('idx', $_SESSION['mng']['mt_idx']);
$lastVisit = lastVisitKorean($myadmin['mt_ldate']);
?>
<style>
    /* 스크롤바(선택) - 너무 튀면 빼도 됨 */
    /*#page-aside .scroll::-webkit-scrollbar { width: 6px; }*/
    /*#page-aside .scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.35); border-radius: 999px; }*/
    /*#page-aside .scroll::-webkit-scrollbar-track { background: rgba(0,0,0,.05); }*/

    /* 메뉴 리스트 기본 */
    #mngSidebarNav,
    #navigation-default > ul {
        /*padding: 14px 10px;*/
    }

    /* 1뎁스 li */
    #mngSidebarNav > li,
    #navigation-default > ul > li {
        /*margin: 6px 0;*/
        /*border-radius: 14px;*/
    }

    /* 1뎁스 링크 */
    #mngSidebarNav > li > a,
    #navigation-default > ul > li > a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 12px;
        border-radius: 15px;
        color:#495057;
        text-decoration: none;
        transition: background .15s ease, transform .15s ease;
    }

    /* 아이콘 통일감 */
    #mngSidebarNav > li > a .icon,
    #navigation-default > ul > li > a .icon {
        font-size: 18px;
        opacity: .6;
    }

    /* 텍스트 */
    #mngSidebarNav > li > a .text,
    #navigation-default > ul > li > a .text {
        font-weight: 700;
        font-size: 14px;
        letter-spacing: -0.2px;
    }

    /* Hover */
    #mngSidebarNav > li > a:hover,
    #navigation-default > ul > li > a:hover {
        /*background: rgba(255,255,255,.16);*/
        background: none;
        color: var(--primary)
    }

    /* Active(선택된 메뉴) - 가맹점주 느낌의 “화이트 카드” */
    #mngSidebarNav > li.active > a,
    #navigation-default > ul > li.active > a {
        color: var(--primary) ;
        background: #FEF4E9;
    }

    /* Active일 때 아이콘/텍스트 톤 */
    #mngSidebarNav > li.active > a .icon,
    #navigation-default > ul > li.active > a .icon {
        color: var(--primary);
        opacity: 1;
    }

    #mngSidebarNav > li.openable.active:has(> ul > li.active) > a,
    #navigation-default > ul > li.openable.active:has(> ul > li.active) > a{

    }

    /* 아이콘도 같이 흰색(원하면) */
    #mngSidebarNav > li.openable.active:has(> ul > li.active) > a .icon,
    #navigation-default > ul > li.openable.active:has(> ul > li.active) > a .icon{

    }

    /* openable(하위 메뉴 있는 항목) 오른쪽 화살표 */
    #mngSidebarNav > li.openable > a,
    #navigation-default > ul > li.openable > a {
        position: relative;
        padding-right: 34px;
    }

    /*#mngSidebarNav > li.openable > a:after,*/
    /*#navigation-default > ul > li.openable > a:after {*/
    /*    content: "▾";*/
    /*    position: absolute;*/
    /*    right: 12px;*/
    /*    top: 50%;*/
    /*    transform: translateY(-50%);*/
    /*    font-size: 12px;*/
    /*    opacity: .85;*/
    /*    transition: transform .15s ease, opacity .15s ease;*/
    /*}*/

    /* open 상태면 화살표 회전 */
    #mngSidebarNav > li.openable.open > a:after,
    #navigation-default > ul > li.openable.open > a:after {
        transform: translateY(-50%) rotate(180deg);
        opacity: 1;
    }

    /* 2뎁스(서브 메뉴) 박스 */
    #mngSidebarNav > li.openable > ul,
    #navigation-default > ul > li.openable > ul {
        /*margin-top: 8px;*/
        /*padding: 8px;*/
        /*border-radius: 14px;*/
        /*background: rgba(0,0,0,.10);*/
    }

    /* 2뎁스 링크 */
    #mngSidebarNav > li.openable > ul > li > a,
    #navigation-default > ul > li.openable > ul > li > a {
        display: block;
        padding: 10px 10px;
        /*border-radius: 12px;*/
        background: none;
        /*border-bottom: 1px solid rgba(255,255,255,.3);*/
        color: #222;     background: #f5f5f5;
        text-decoration: none;
        font-size: 13px;
        /*transition: background .15s ease;*/
    }

    /* 2뎁스 hover */
    #mngSidebarNav > li.openable > ul > li > a:hover,
    #navigation-default > ul > li.openable > ul > li > a:hover {
        /*background: rgba(255,255,255,.12);*/

        color: var(--primary)
    }

    /* 2뎁스 active(선택된 서브메뉴) */
    #mngSidebarNav > li.openable > ul > li.active > a,
    #navigation-default > ul > li.openable > ul > li.active > a {

        color: var(--primary);
        font-weight: 700;
    }
</style>
<div class="page-aside invert" id="page-aside">
  <div class="scroll" style="max-height: 100%">
    <div class="navigation navigation--condensed" id="navigation-default">
      <ul id="mngSidebarNav">
        <?php if($_SESSION['mng']['mt_level'] >= 9){?>
        <li class="<? if ($chk_menu == 0) { ?>open active<? } ?>"><a href="<?php echo MNG_HTTP?>"><span
                        class="icon li-home"></span><span class="text">대시보드</span></a>
        </li>
        <li class="openable <? if ($chk_menu == 1) { ?>open active<? } ?>"><a href="#"><span
              class="icon li-user"></span><span class="text">회원 관리</span></a>
          <ul>
            <li class="<? if ($chk_menu === 1 && $chk_sub_menu === 1) { ?>active<? } ?>"><a
                href="<?php echo MNG_HTTP?>/member/list.php" class="no-icon"><span class="text">일반회원</span></a></li>
            <li class="<? if ($chk_menu === 1 && $chk_sub_menu === 2) { ?>active<? } ?>"><a
                href="<?php echo MNG_HTTP?>/member/list.php?type=secession" class="no-icon"><span class="text">탈퇴회원</span></a></li>
          </ul>
        </li>

            <li class="openable <? if ($chk_menu == 2) { ?>open active<? } ?>"><a href="#"><span
                            class="icon li-briefcase"></span><span class="text">가맹점주 관리</span></a>
                <ul>
                    <li class="<? if ($chk_menu === 2 && $chk_sub_menu === 1) { ?>active<? } ?>"><a
                                href="<?php echo MNG_HTTP?>/manager/list.php" class="no-icon"><span class="text">가맹점주 회원</span></a></li>
                    <li class="<? if ($chk_menu === 2 && $chk_sub_menu === 2) { ?>active<? } ?>"><a
                                href="<?php echo MNG_HTTP?>/manager/list.php?type=approval" class="no-icon"><span class="text">승인관리</span></a></li>
                    <li class="<? if ($chk_menu === 2 && $chk_sub_menu === 3) { ?>active<? } ?>"><a
                                href="<?php echo MNG_HTTP?>/manager/list.php?type=secession" class="no-icon"><span class="text">탈퇴회원</span></a></li>
                </ul>
            </li>
            <li class="<? if ($chk_menu == '3') { ?>open active<? } ?>"><a href="<?php echo MNG_HTTP?>/shop/list.php"><span
                            class="icon li-city"></span><span class="text">매장 관리</span></a>
            <li class="<? if ($chk_menu == '6') { ?>open active<? } ?>"><a href="<?php echo MNG_HTTP?>/adjustment/list.php"><span
                            class="icon li-credit-card"></span><span class="text">정산 관리</span></a>
<!--            <li class="--><?// if ($chk_menu == '7') { ?><!--open active--><?// } ?><!--"><a href="--><?php //echo MNG_HTTP?><!--/qa/list.php"><span-->
<!--                            class="icon li-bubble"></span><span class="text">1:1 문의</span></a>-->
            <li class="<? if ($chk_menu == '8') { ?>open active<? } ?>"><a href="<?php echo MNG_HTTP?>/notice/list.php"><span
                            class="icon li-paper-plane"></span><span class="text">공지 사항</span></a>
            <li class="<? if ($chk_menu == '9') { ?>open active<? } ?>"><a href="<?php echo MNG_HTTP?>/coupon/list.php"><span
                            class="icon li-ticket"></span><span class="text">쿠폰 관리</span></a>

          <?php if($_SESSION['mng']['mt_level'] >= 9){?>
          <li class="openable <? if ($chk_menu == '99') { ?>open active<? } ?>"><a href="#"><span
                          class="icon li-cog"></span><span class="text">설정</span></a>
              <ul>
                  <li class="<? if ($chk_menu == '99' && $chk_sub_menu == '2') { ?>active<? } ?>">
                      <a href="<?php echo MNG_HTTP?>/setup_form.php"
                         class="no-icon"><span
                                  class="text">기본설정</span></a></li>
                  <li class="<? if ($chk_menu == '99' && $chk_sub_menu == '3') { ?>active<? } ?>"><a href="<?php echo MNG_HTTP?>/agree.php"
                                                                                                     class="no-icon"><span
                                  class="text">약관설정</span></a></li>
              </ul>
          </li>
<!--        <li class="openable --><?// if ($chk_menu == '11') { ?><!--open active--><?// } ?><!--"><a href="#"><span class="icon li-pie-chart"></span><span class="text">통계</span></a>-->
<!--          <ul>-->
<!--            <li class="--><?// if ($chk_menu == '11' && $chk_sub_menu == '1') { ?><!--active--><?// } ?><!--"><a href="--><?php //echo MNG_HTTP?><!--/visit/list.php" class="no-icon"><span class="text">방문자 통계</span></a></li>-->
<!--          </ul>-->
<!--        </li>-->
          <?php }
        }?>
      </ul>
    </div>
  </div>
</div>
<!-- //END PAGE ASIDE PANEL -->
