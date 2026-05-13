<?
$_SUB_HEAD_TITLE = "FAQ";//헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '3';//모바일 hd 1~n까지 있음
$_GET['bt_menu'] = '';//모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
    <div class="sub_pg faq_pg container px-0 pb-0">
		<div class="container border-top">
			<div class="pb_100 pt-5 ">
				<div class="tit_h3 mb_20">자주하는 질문</div>

				<form class="sch_ip sch_gray align-items-center  rounded-pill">
					<input type="search" class="form-control fs_14 flex-fill border-0" placeholder="검색어를 입력해주세요">
					<button class="btn btn-icon flex-shrink-0"><img src="./img/ic_sch_gray.png" style="width:2.0rem;"></button>
				</form>

				<!-- 카테고리 네비게이션 -->
				<div class="mt_20 mb-4 px_16 mx_n16">
					<div class="touch_scroll scroll_bar_none flex-fill mx_n16">
						<div class="btn-group btn-group-toggle px_16">
							<a class="btn btn-outline-light btn-md rounded-pill nav-link active">전체</a>
							<a class="btn btn-outline-light btn-md rounded-pill nav-link">카테고리1</a>
							<a class="btn btn-outline-light btn-md rounded-pill nav-link">카테고리1</a>
							<a class="btn btn-outline-light btn-md rounded-pill nav-link">카테고리1</a>
							<a class="btn btn-outline-light btn-md rounded-pill nav-link">카테고리1</a>
							<a class="btn btn-outline-light btn-md rounded-pill nav-link">카테고리1</a>
							<a class="btn btn-outline-light btn-md rounded-pill nav-link">카테고리1</a>
							<a class="btn btn-outline-light btn-md rounded-pill nav-link">카테고리1</a>
						</div>
					</div>
				</div>

				<div class="customer_list border-top mt-4">
					<ul id="faq_wr" class="faq_cont">
						<li id="faq_hd01">
							<div class="item d-flex align-items-center" data-toggle="collapse" data-target="#collapse01" aria-expanded="false" aria-controls="collapse01">
								<div class="flex-fill">
									<div class="text-gray">카테고리1</div>
									<div class="line1_text fs_15 fw_500 flex-fill my-2">문의 제목입니다.문의 제목입니다.</div>
									<div class="line1_text">사이트에서 판매하고 있는 상품은 라운즈 플래그십스토어(...</div>
								</div>
								<img class="flex-shrink-0" src="./img/ic_select03.png" style="width:2.0rem;">
							</div>

							<div id="collapse01" class="collapse " aria-labelledby="collapse01" aria-labelledby="faq_hd01" data-parent="#faq_wr">
								<div class="line_h1_5 wh_pre p-4 bg-light mb_20">삼겹살 한 조각에는 고기 특유의 담백한 맛과 함께 미지근한 화덕에서 나오는 향긋한 향이 어우러져, 먹는 즐거움이 두 배로 느껴집니다. 또한, 삼겹살은 다양한 양념과 함께 즐기기도 좋아서, 각종 소스나 쌈재료와 함께 먹으면 더욱 풍부한 맛을 느낄 수 있습니다.
								
								한돈 삼겹살은 한국의 대표적인 고기 메뉴 중 하나로, 가족, 친구들과 함께 나누는 식사나 소소한 모임에 적합합니다. 삼겹살 구워먹는 과정에서 함께 즐기는 소주나 맥주도 더욱 특별한 시간을 만들어 줄 것입니다.</div>
							</div>
						</li>
						<li id="faq_hd02">
							<div class="item d-flex align-items-center" data-toggle="collapse" data-target="#collapse02" aria-expanded="false" aria-controls="collapse02">
								<div class="flex-fill">
									<div class="text-gray">카테고리1</div>
									<div class="line1_text fs_15 fw_500 flex-fill my-2">문의 제목입니다.문의 제목입니다.</div>
									<div class="line1_text">사이트에서 판매하고 있는 상품은 라운즈 플래그십스토어(...</div>
								</div>
								<img class="flex-shrink-0" src="./img/ic_select03.png" style="width:2.0rem;">
							</div>

							<div id="collapse02" class="collapse " aria-labelledby="collapse02" aria-labelledby="faq_hd01" data-parent="#faq_wr">
								<div class="line_h1_5 wh_pre p-4 bg-light mb_20">삼겹살 한 조각에는 고기 특유의 담백한 맛과 함께 미지근한 화덕에서 나오는 향긋한 향이 어우러져, 먹는 즐거움이 두 배로 느껴집니다. 또한, 삼겹살은 다양한 양념과 함께 즐기기도 좋아서, 각종 소스나 쌈재료와 함께 먹으면 더욱 풍부한 맛을 느낄 수 있습니다.
								
								한돈 삼겹살은 한국의 대표적인 고기 메뉴 중 하나로, 가족, 친구들과 함께 나누는 식사나 소소한 모임에 적합합니다. 삼겹살 구워먹는 과정에서 함께 즐기는 소주나 맥주도 더욱 특별한 시간을 만들어 줄 것입니다.</div>
							</div>
						</li>
					</ul>
				</div>

				<article class="my-5">
					<ul class="pagination fs_16">
						<li class=""><a href="#" class="disabled arrow"><img src="./img/pg_prev_prev.svg"></a></li>
						<li class=""><a href="#" class="disabled arrow"><img src="./img/pg_prev.svg"></a></li>
						<li class=""><a href="#" class="on">1</a></li>
						<li class=""><a href="#">2</a></li>
						<li class=""><a href="#">3</a></li>
						<li class=""><a href="#">4</a></li>
						<li class=""><a href="#">5</a></li>
						<li class=""><a href="#" class="arrow"><img src="./img/pg_next.svg"></a></li>
						<li class=""><a href="#" class="arrow"><img src="./img/pg_next_next.svg"></a></li>
					</ul>
				</article>

			<!-- <div class="d-flex" style="height:25rem;">
					<div class="no_data text-center flex-fill">
						<div class="mb-4"><img src="./img/no_data.png" style="width:10rem;"></div>
						<p class="fs_16 text-gray line_h1_4">등록된 FAQ가 없습니다.</p>
					</div>
				</div> -->
				<!-- 데이터가 없을경우 d-flex를 d-none로 변경 -->
			</div>
		</div>
	</div>
</div>


<? include_once("./inc/tail.php"); ?>
