<?
$_SUB_HEAD_TITLE = "마이페이지"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '6'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = '1'; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
	<div class="sub_pg my_pg container px-0 pb-0">
		<div class="container pb_20">
			<div class="d-flex align-items-center fs_18 fw_300 mt-5">
				<p><span class="fw_600">김이름님</span> 안녕하세요!</p>
				<p><a href="./myinfo1.php"><img src="./img/ico_arrow1.png" alt="내정보 수정" class="ml-3" style="width: 2.5rem;"></a></p>
			</div>
			<div class="mt-4 bg-primary rounded-md px-4 py-3 text-white d-flex justify-content-between ">
				<div class="d-flex align-items-center">
					<p><img src="./img/my_coupon.png" alt="쿠폰" class="mr-2" style="width: 2.4rem;"></p>
					<p>나의 쿠폰</p>
					<p class="fw_700 t_yellw ml-3">3</p>
				</div>
				<div>
					<button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw_600"  onclick="location.href='./coupon.php' ">쿠폰내역</button>
				</div>
			</div>
		</div>
		<div class="bar">

		</div>

		<div class=" ">
			<ul class="mypage_list">
				<li>
					<a class="d-flex align-items-center" href="./order_history.php">
						<p><img src="./img/my_wallet.png" alt="꾸밈이미지" class="mr-4" style="width: 2rem;"></p>
						<div>주문 내역</div>
						<img class="ml-auto flex-shrink-0" src="./img/ic_more02.png" style="width:1.6rem;">
					</a>
				</li>
				<li>
					<a class="d-flex align-items-center" href="./customer.php">
						<p><img src="./img/my_profile.png" alt="꾸밈이미지" class="mr-4" style="width: 2rem;"></p>
						<div>사업자 정보</div>
						<img class="ml-auto flex-shrink-0" src="./img/ic_more02.png" style="width:1.6rem;">
					</a>
				</li>
				<li>
					<a class="  d-flex align-items-center" href="./notice.php">
						<p><img src="./img/my_note.png" alt="꾸밈이미지" class="mr-4" style="width: 2rem;"></p>
						<div>공지사항</div>
						<img class="ml-auto flex-shrink-0" src="./img/ic_more02.png" style="width:1.6rem;">
					</a>
				</li>
				<li>
					<a class=" d-flex align-items-center" href="./term.php">
						<p><img src="./img/my_term.png" alt="꾸밈이미지" class="mr-4" style="width: 2rem;"></p>
						<div>이용약관 및 정책</div>
						<img class="ml-auto flex-shrink-0" src="./img/ic_more02.png" style="width:1.6rem;">
					</a>
				</li>
				
				<li>
					<a class="  d-flex align-items-center" href=" ">
						<p><img src="./img/my_logout.png" alt="꾸밈이미지" class="mr-4" style="width: 2rem;"></p>
						<div>로그아웃</div>
						<img class="ml-auto flex-shrink-0" src="./img/ic_more02.png" style="width:1.6rem;">
					</a>
				</li>

			</ul>
		</div>


	</div>
</div>


<? include_once("./inc/tail.php"); ?>