
    <div class="wrap">
        <div class="sub_pg ">
            <div class="container">

                <div class="find_wp_top text-center">
                    <p><img src="<?=DESIGN_HTTP?>/img/find_idimg.png" style="width:150px"></p>
                    <h2 class="tit_st3 mt-3">회원님의 아이디는<br>
                        <span class="text-primary">ID_<?= htmlspecialchars($memberId, ENT_QUOTES, 'UTF-8') ?> </span>입니다
                    </h2>
                </div>
                <p class="mt-5"><button type="button" class="btn btn-primary btn-block" onclick="location.href='<?=AUTH_PAGE?>/login.php'">로그인</button></p>
                <p class="mt-3"><button type="button" class="btn btn-outline-light btn-block" onclick="location.href='<?=AUTH_PAGE?>/find_pw.php'">비밀번호 찾기</button></p>


            </div>

        </div>
    </div>
