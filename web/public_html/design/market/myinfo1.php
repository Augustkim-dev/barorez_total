<?
$_SUB_HEAD_TITLE = "내정보 수정 확인";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 / 로그아웃시 logout
$hd_num = 'setting'; //1차메뉴
$hd_num2 = 'setting1'; //2차메뉴
$hd_left = ' '; //왼쪽메뉴 on 땜시 만듬
include_once("./inc/head.php");
?>

<!-- 왼쪽 메뉴-->
<? include_once("./inc/left_menu.php"); ?>

<div class="sub_pg ">
	<div class="sub_wr">
		<div class="hd_tit2 fs_16 flex-row">
			<!-- <div class="flex-shrink-0 ml-auto   d-flex align-items-end">
				<p class="d-flex align-content-center mb-4 mb-lg-0"><img src="./img/img_mark2.svg" class="mr-2" alt=" "> 주문내역 클릭시 주문 상세보기가 나타납니다.</p>
			</div> -->
			<h2 class="tit_st1 d-flex align-items-center mr-5 "> <span>내정보 수정</span></h2>
		</div>
		<div class="card join_form">
			<h3 class="text-center tit_st3">개인정보 보호를 위해<br>
					내정보 수정 전 <span class=" text-primary">비밀번호를 한 번 더 확인</span>합니다.</h3>
			<div class="join_box">
				
				<div class="form_wr mt-5">
					<div class="ip_tit required">
						<h5>비밀번호</h5>
					</div>

					<div class="form-row">
						<div class="col">
							<input type="text" class="form-control" placeholder="비밀번호 입력">
						</div>

					</div>


					<button type="button" class="btn btn-secondary btn-lg btn-block mt-4"  onclick="location.href='./myinfo2.php' ">확인</button>
				</div>

			</div>
		</div>
	</div>
</div>


<? include_once("./inc/tail.php"); ?>