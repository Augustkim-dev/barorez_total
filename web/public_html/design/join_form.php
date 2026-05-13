<?
$_SUB_HEAD_TITLE = "필수정보 입력"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
	<div class="sub_pg   pb_lg  ">
		<div class="container">
			<div class="mt-5">
				<form>
					<div class="form_wr ip_invalid">
						<div class="ip_tit required">
							<h5>아이디</h5>
						</div>
						<div class="form-row">
							<div class="col">
								<input type="text" class="form-control" placeholder="영소문, 숫자 포함 6~16자">
							</div>
							<div class="col-3">
								<button type="button" class="btn btn-outline-primary btn-block  ">중복체크</button>
							</div>
						</div>
						<div class="form-text ip_invalid">5~15자의 영문 소문자, 숫자로만 입력해 주세요</div>
					</div>

					<div class="form_wr mt-5 ip_valid">
						<div class="ip_tit required">
							<h5>비밀번호</h5>
						</div>

						<div class=" ">
							<input type="password" class="form-control" placeholder="비밀번호  입력(영소문, 숫자 포함 8~16자)">
						</div>
						<div class="mt-2">
							<input type="password" class="form-control" placeholder="비밀번호 재입력">
						</div>

						<div class="form-text ip_invalid">비밀번호가 일치하지 않습니다.</div>
					</div>

					<div class="form_wr mt-5 ip_invalid">
						<div class="ip_tit required">
							<h5>이름</h5>
						</div>
						<div class="form-row">
							<div class="col">
								<input type="text" class="form-control" placeholder="이름 입력">
							</div>
						</div>
						<div class="form-text ip_invalid">이름을 입력하세요</div>
					</div>

					<div class="form_wr mt-5 ip_invalid">
						<div class="ip_tit required">
							<h5>휴대폰번호</h5>
						</div>
						<div class="form-row">
							<div class="col">
								<input type="text" class="form-control" placeholder="‘-’ 없이 숫자만 입력">
							</div>
							<div class="col-3">
								<button type="button" class="btn btn-outline-primary btn-block  ">인증요청</button>
							</div>
						</div>
						<div class="form-row">
							<div class="col mt-3 position-relative">
								<p class="time_lim">04:25</p>
								<input type="text" class="form-control" placeholder="인증번호 입력">
							</div>
							<div class="col-3 mt-3">
								<button type="button" class="btn btn-primary btn-block  " disabled>확인</button>
							</div>
						</div>
						<div class="form-text ip_invalid">오류 텍스트</div>
					</div>


					 

					<div class="bottom_btn bg-white">
						<div class="form-row">
							<div class="col-12"><button type="button" class="btn btn-primary btn-block btn-lg" onclick="location.href='./join_cmp.php'">회원가입</button></div>
						</div>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>


<? include_once("./inc/tail.php"); ?>