<?
if (empty($_SESSION['mng']['mt_level']) && $_SESSION['mng']['mt_level'] !== 5 && $_SERVER['PHP_SELF'] != "./login.php" ) {
    alert("관리자만 접근할 수 있습니다.", "/market/login.php");
}
$myadmin = get_mem_info('idx', $_SESSION['mng']['mt_idx']);
$lastVisit = lastVisitKorean($myadmin['mt_ldate']);
?>
<div class="page-aside invert" id="page-aside">
    <div class="scroll" style="max-height: 100%">
        <div class="navigation navigation--condensed" id="navigation-default">

            <div class="user user--bordered user--lg user--w-lineunder user--controls">
                <div class="user__name">
                    <strong><?php echo $_SESSION['mng']['mt_name']?></strong><br>
                    <span class="text-muted"><?php echo $_SESSION['mng']['mt_id']?></span>
                </div>
                <div class="user__lineunder">
                    <div class="text">
                        <?php echo $lastVisit ?> 로그인 되었습니다.
                    </div>
                    <div class="buttons">
                        <div class="button button-minimize" data-action="aside-minimize" data-toggle="tooltip" data-placement="top"
                             data-original-title="Minimize navigation">
                        </div>
                    </div>
                </div>
            </div>

            <ul>

                    <li class="<? if ($chk_menu === 0) { ?>open active<? } ?>"><a href="<?php echo MARKET_HTTP?>"><span
                                    class="icon li-users"></span><span class="text">대시보드</span></a>
                    </li>
                    <li class="<? if ($chk_menu === 1) { ?>open active<? } ?>"><a href="<?php echo MARKET_HTTP?>/order/list.php"><span
                                    class="icon li-users"></span><span class="text">실시간 주문</span><span id="dotTable" class="dot"></span></a>
                    </li>

                    <li class="<? if ($chk_menu == 2) { ?>open active<? } ?>"><a href="<?php echo MARKET_HTTP?>/table/list.php"><span class="icon li-users"></span><span class="text">테이블 관리</span></a>
                    </li>
                    <li class="<? if ($chk_menu == 3) { ?>open active<? } ?>"><a href="<?php echo MARKET_HTTP?>/menu/list.php"><span
                                    class="icon li-users"></span><span class="text">메뉴 관리</span></a></li>
                    <li class="<? if ($chk_menu == 4) { ?>open active<? } ?>"><a href="<?php echo MARKET_HTTP?>/reserve/list.php"><span
                                class="icon li-users"></span><span class="text">예약 관리</span><span id="dotReserve" class="dot"></span></a></li>
                <li class="<? if ($chk_menu == 5) { ?>open active<? } ?>"><a href="<?php echo MARKET_HTTP?>/packag/list.php"><span
                                class="icon li-users"></span><span class="text">포장 관리</span><span id="dotPack" class="dot"></span></a></li>
                <li class="<? if ($chk_menu == 6) { ?>open active<? } ?>"><a href="<?php echo MARKET_HTTP?>/settlement/list.php"><span
                                class="icon li-users"></span><span class="text">정산 관리</span></a></li>
                    <li class="<? if ($chk_menu == 7) { ?>open active<? } ?>"><a href="<?php echo MARKET_HTTP?>/statistics/list.php"><span
                                class="icon li-users"></span><span class="text">통계</span></a></li>
                    <li class="openable <? if ($chk_menu == 99) { ?>open active<? } ?>"><a href="#"><span
                                    class="icon li-users"></span><span class="text">설정</span></a>
                        <ul>
                            <li class="<? if ($chk_menu === 99 && $chk_sub_menu === 1) { ?>active<? } ?>"><a
                                        href="<?php echo MARKET_HTTP?>/setting/list.php" class="no-icon"><span class="text">내정보관리</span></a></li>
                            <li class="<? if ($chk_menu === 99 && $chk_sub_menu === 2) { ?>active<? } ?>"><a
                                        href="<?php echo MARKET_HTTP?>/qa/list.php" class="no-icon"><span class="text">1:1문의관리</span></a></li>
                        </ul>
                    </li>
            </ul>
        </div>
    </div>
</div>
<!-- //END PAGE ASIDE PANEL -->
<script>
    console.log('[badge] polling init');

    (function () {
        var elTable = document.getElementById('dotTable');
        var elPack = document.getElementById('dotPack');
        var elReserve = document.getElementById('dotReserve');

        if (!elTable && !elPack && !elReserve) {
            console.log('[badge] no dot elements -> skip');
            return;
        }

        if (window.__badge_poll_started) {
            console.log('[badge] already started -> skip');
            return;
        }
        window.__badge_poll_started = true;

        var currentShIdx = <?= (int)($_SESSION['current_sh_idx'] ?? 0) ?>;
        console.log('[badge] currentShIdx:', currentShIdx);

        function setDot(el, cnt) {
            if (!el) return;
            el.style.display = (cnt > 0) ? 'inline-block' : 'none';
            console.log('[badge] setDot', el.id, cnt);
        }

        function poll() {
            console.log('[badge] polling...');
            if (!currentShIdx) return;

            $.ajax({
                url: '<?=MARKET_HTTP?>/badge_poll.php',
                type: 'POST',
                dataType: 'json',
                data: { act: 'check_badges', sh_idx: currentShIdx },
                success: function (res) {
                    console.log('[badge] res:', res);
                    if (!res || !res.success) return;

                    setDot(elTable, parseInt(res.table, 10) || 0);
                    setDot(elPack, parseInt(res.pack, 10) || 0);
                    setDot(elReserve, parseInt(res.reserve, 10) || 0);
                },
                error: function (xhr, status, error) {
                    console.log('[badge] error status:', status);
                    console.log('[badge] error:', error);
                    console.log('[badge] responseText:', xhr.responseText);
                }
            });
        }

        poll();
        setInterval(poll, 5000);
    })();
</script>
