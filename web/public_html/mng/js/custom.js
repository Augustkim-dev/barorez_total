$(document).ready(function () {
	//테이블 가로스크롤 마우스로 터치
	window.onload = function () {
		slider = document.querySelectorAll('.touch_scroll');
		for ( var i = 0; i < slider.length; i++ ) {
			touchScroll(slider[i]);
		}
	};
});// 한페이지에 touch_scroll가 2개 이상 있을때 1개만 작동 하고 나머지는 작동을 안합니다.

function touchScroll(slider = "") {
	// const slider = document.querySelector($bind);
	let isDown = false;
	let startX;
	let scrollLeft;

	slider.addEventListener("mousedown", (e) => {
		e.preventDefault();
		isDown = true;
		slider.classList.add("active");
		startX = e.pageX - slider.offsetLeft;
		scrollLeft = slider.scrollLeft;
		cancelMomentumTracking();
	}, { passive: false });

	slider.addEventListener("mouseleave", (e) => {
		e.preventDefault();
		isDown = false;
		slider.classList.remove("active");
	}, { passive: false });

	slider.addEventListener("mouseup", (e) => {
		e.preventDefault();
		isDown = false;
		slider.classList.remove("active");
		beginMomentumTracking();
	}, { passive: false });

	slider.addEventListener("mousemove", (e) => {
		e.preventDefault();
		if (!isDown) return;
		e.preventDefault();
		const x = e.pageX - slider.offsetLeft;
		const walk = (x - startX) * 3; //scroll-fast
		var prevScrollLeft = slider.scrollLeft;
		slider.scrollLeft = scrollLeft - walk;
		velX = slider.scrollLeft - prevScrollLeft;
	}, { passive: false });

	slider.addEventListener("wheel", (e) => {
		e.preventDefault();
		cancelMomentumTracking();
	}, { passive: false });

	var velX = 0;
	var momentumID;

	function beginMomentumTracking() {
		cancelMomentumTracking();
		momentumID = requestAnimationFrame(momentumLoop);
	}
	function cancelMomentumTracking() {
		cancelAnimationFrame(momentumID);
	}
	function momentumLoop() {
		slider.scrollLeft += velX;
		velX *= 0.95;
		if (Math.abs(velX) > 0.5) {
			momentumID = requestAnimationFrame(momentumLoop);
		}
	}
}




$(document).ready(function () {
	$(window).scroll(function () {
		if ($(this).scrollTop() > 200) {
			$('.go_top2').addClass('show');
		} else {
			$('.go_top2').removeClass('show');
		}
	});

	$('.go_top2').click(function () {
		$('html, body').animate({ scrollTop: 0 }, 300);
		return false;
	});
});


document.addEventListener('DOMContentLoaded', function() {
	const testElements = document.querySelectorAll('.line1_text, .line2_text, .line3_text');

	testElements.forEach(function(element) {
		if (element.textContent.includes(' ')) {
			element.style.wordBreak = 'break-word'; // 띄어쓰기가 있으면 줄바꿈 유지
		} else {
			element.style.wordBreak = 'break-all'; // 띄어쓰기가 없으면 자동 줄바꿈
		}
	});
});//라인 자르기


document.addEventListener('DOMContentLoaded', function() {
	const testElements = document.querySelectorAll('.text_dynamic');

	testElements.forEach(function(element) {
		if (element.textContent.includes(' ')) {
			element.style.whiteSpace = 'pre-line'; // 띄어쓰기가 있으면 줄바꿈 유지
		} else {
			element.style.wordBreak = 'break-all'; // 띄어쓰기가 없으면 자동 줄바꿈
		}
	});
});//라인 자르기2


$(document).ready(function () {
	/*상품더보기 버튼*/
	$('.show_btn').on('click',function(){
		$('.editor_cont_wr').toggleClass('show');
	});
});


$(document).ready(function () {
	// 모바일 메뉴
	$('.hd_menu_btn').on('click', function() {
		$('body').addClass('menu_on');
		const scrollY = window.scrollY;
		$('body').css({
			position: 'fixed',
			top: `-${scrollY}px`,
			overflowY: 'scroll',
			width: '100%'
		});
	});

	// 모바일 메뉴 닫기
	$('.close_btn_wr, .menu_bg').on('click', function() {
		$('body').removeClass('menu_on');
		const scrollY = $('body').css('top');
		$('body').removeAttr('style');
		window.scrollTo(0, parseInt(scrollY || '0', 10) * -1);
	});


	// 모바일 메뉴 내부
	$('.m_nav .nav_a').on('click',function(){
		// 2차메뉴가 있을경우
		if($(this).siblings('.nav_ul2').length){
			// 2차 메뉴가 열려있을경우
			if($(this).siblings('.nav_ul2').hasClass('on')){

			} else{
				$('.nav_ul2').slideUp();
				$('.nav_ul2').removeClass('on');
			}
			$(this).siblings('.nav_ul2').slideToggle().toggleClass('on');
		}
	});
});


$(document).ready(function () {
	// 모달 열릴 때
	$('.modal').on('show.bs.modal', function() {
		const scrollY = window.scrollY;
		$('body').addClass('modal-open');
		$('body').css({
			position: 'fixed',
			top: `-${scrollY}px`,
			overflowY: 'scroll',
			width: '100%'
		});
	});

	// 모달 닫힐 때
	$('.modal').on('hide.bs.modal', function() {
		$('body').removeClass('modal-open');
		const scrollY = $('body').css('top');
		$('body').removeAttr('style');
		window.scrollTo(0, parseInt(scrollY || '0', 10) * -1);
	});
});


//검색창 클리어 버튼
$(document).ready(function() {
	$('.sch_ip').each(function(){
		var inputSch = $(this).find('input[type="search"]');
		var clearBtn = $(this).find('.sch_clear_btn');

		inputSch.on('keyup', function() {
			// 입력된 값이 있으면 삭제 버튼을 보이게 하고, 없으면 숨김
			if ($(this).val().length > 0) {
				clearBtn.show();
			} else {
				clearBtn.hide();
			}
		});

		// 삭제 버튼 클릭 시 입력 필드 값 초기화
		clearBtn.on('click', function(event) {
			event.preventDefault(); // 기본 폼 제출 방지
			inputSch.val(''); // 입력 필드 값 초기화
			clearBtn.hide(); // 삭제 버튼 숨김
		});
	});
});


//찜하기 버튼
$(document).ready(function() {
	$('.like_btn').each(function(){
		$(this).on('click', function(){
			$(this).toggleClass('active');
		});
	});
});


//리뷰 내용 보이기
$(document).ready(function() {
	$('.review_text').each(function(){
		$(this).on('click', function(){
			$(this).find('p').toggleClass('show');
		});
	});
});


//알림 내용 보이기
$(document).ready(function() {
	$('.push_list').each(function(){
		$(this).on('click', function(){
			$(this).toggleClass('show');
		});
	});
});


//매장 설명 보이기
$(document).ready(function() {
	$('.store_info_text .more_btn').each(function(){
		$(this).on('click', function(){
			$('.store_intro').toggleClass('show');
			updateButtonText(); // 버튼 텍스트 업데이트
		});

		function updateButtonText() {
			if ($(".store_intro").hasClass("show")) {
				$(".more_btn").html("접기");
			} else {
				$(".more_btn").html("더보기");
			}
		}
	});
});
