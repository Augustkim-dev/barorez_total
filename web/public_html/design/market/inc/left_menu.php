<section class="left_menu">
	<ul class="left_menu_nav">
		<li class="<?= ($hd_left === 'index') ? 'on' : '' ?>">
			<a href="./index.php" class="  ">
				<p class="img_off"><img src="./img/navi_qr_off.png" alt="테이블 아이콘" class=""><span class=" d-block mt-2">테이블</span></p>
				<p class="img_on"><img src="./img/navi_qr_on.png" alt="테이블 아이콘" class=""><span class=" d-block mt-2">테이블</span></p>
			</a>
		</li>
		<li class="navi_alim <?= ($hd_left === 'pck_dtl') ? 'on' : '' ?> " >
			<a href="./pck_dtl.php" class="">
				<p class="img_off"><img src="./img/navi_pack_off.png" alt=" 아이콘" class=""><span class=" d-block mt-2">포장</span></p>
				<p class="img_on"><img src="./img/navi_pack_on.png" alt=" 아이콘" class=""><span class=" d-block mt-2">포장</span></p>
			</a>
		</li>
		<li class="navi_alim <?= ($hd_left === 'reserve_hst') ? 'on' : '' ?> ">
			<a href="./reserve_hst.php" class="<?= ($hd_left === 'reserve_hst') ? 'on' : '' ?> ">
				<p class="img_off"><img src="./img/navi_cal_off.png" alt=" 아이콘" class=""><span class=" d-block mt-2">예약</span></p>
				<p class="img_on"><img src="./img/navi_cal_on.png" alt=" 아이콘" class=""><span class=" d-block mt-2">예약</span></p>
			</a>
		</li>
		 <li class="<?= ($hd_left === 'cmp_list') ? 'on' : '' ?>">
			<a href="./cmp_list.php" class="  ">
				<p class="img_off"><img src="./img/navi_cancle_off.png" alt=" 아이콘" class=""><span class=" d-block mt-2">완료/취소</span></p>
				<p class="img_on"><img src="./img/navi_cancle_on.png" alt=" 아이콘" class=""><span class=" d-block mt-2">완료/취소</span></p>
			</a>
		</li>
	
	</ul>
</section>