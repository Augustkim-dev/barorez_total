<?
$_SUB_HEAD_TITLE = "공지사항"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
	<div class="sub_pg  ">

		<div class="bg-light py-3">
			<div class="container">
				<form class="sch_ip   align-items-center">
					<input type="search" class="form-control fs_14 flex-fill border-0" placeholder="검색어를 입력해주세요">
					<button class="btn btn-icon flex-shrink-0"><img src="./img/ic_sch_gray.png" style="width:2.0rem;"></button>
				</form>
			</div>
		</div>


		<div class="notice_list border-top fs_15 fw_500">
			<ul>
				<li>
					<a class="item d-flex align-items-center" href="./notice_detail.php">
						<div class="flex-fill">
							<div class="line1_text flex-fill">문의 제목입니다.문의 제목입니다.</div>
							<div class="tg_400 fs_13 mt-2">2023.12.01</div>
						</div>
						<img class="flex-shrink-0" src="./img/ic_more02.png" style="width:2.0rem;">
					</a>
				</li>
				<li>
					<a class="item d-flex align-items-center" href="./notice_detail.php">
						<div class="flex-fill">
							<div class="line1_text flex-fill">문의 제목입니다.문의 제목입니다.</div>
							<div class="tg_400 fs_13  mt-2">2023.12.01</div>
						</div>
						<img class="flex-shrink-0" src="./img/ic_more02.png" style="width:2.0rem;">
					</a>
				</li>
			</ul>
		</div>
				<!-- 공지사항 없을때 문구
		
		<div class="no_data  ">
			<img src="./img/img_mark.png">
			<p class="   line_h1_4 mt-3 fs_15  ">등록된 공지사항이 없습니다.</p>
		</div> -->
		<article class="my-5">
			<ul class="pagination fs_16">
				<li class=""><a href="#" class="disabled arrow"><img src="./img/pg_prev.svg"></a></li>
				<li class=""><a href="#" class="on">1</a></li>
				<li class=""><a href="#">2</a></li>
				<li class=""><a href="#">3</a></li>
				<li class=""><a href="#">4</a></li>
				<li class=""><a href="#">5</a></li>
				<li class=""><a href="#" class="arrow"><img src="./img/pg_next.svg"></a></li>

			</ul>
		</article>

		
	</div>
</div>


<? include_once("./inc/tail.php"); ?>