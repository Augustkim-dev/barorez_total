


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
		$('.detail_cont').toggleClass('show');
	});
});


/*얼랏창 띄우기 스크립트*/
$(document).on('click', '.btn-alert', function () {
  var message = $(this).data('message') || '알림 메시지';

  var uid = 'alert-' + Date.now() + '-' + Math.floor(Math.random() * 1000);

  var $alert = $('<div>', {
    id: uid,
    class: 'alert alert-warning alert-dismissible fade show',
    role: 'alert',
    html: message
  });

  var $closeBtn = $('<button>', {
    type: 'button',
    class: 'close',
    'data-dismiss': 'alert',
    'aria-label': 'Close',
    html: '<span aria-hidden="true">&times;</span>'
  });

  $alert.append($closeBtn);

  // 새 알럿을 위쪽에 쌓기
  $('#alertContainer').prepend($alert);

  // 3초 후 자동 닫기
  setTimeout(function () {
    $('#' + uid).alert('close');
  }, 3000);
});


//터치스크롤
window.onload = function () {
    touchScroll(".scroll_mouse");
};

function touchScroll(selector = "") {
    const sliders = document.querySelectorAll(selector);
    if (!sliders.length) return;

    sliders.forEach((slider) => {
        let isDown = false;
        let startX;
        let scrollLeft;
        let velX = 0;
        let momentumID;

        slider.addEventListener("mousedown", (e) => {
            isDown = true;
            slider.classList.add("active");
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
            cancelMomentumTracking();
        });

        slider.addEventListener("mouseleave", () => {
            isDown = false;
            slider.classList.remove("active");
        });

        slider.addEventListener("mouseup", () => {
            isDown = false;
            slider.classList.remove("active");
            beginMomentumTracking();
        });

        slider.addEventListener("mousemove", (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 3;
            let prevScrollLeft = slider.scrollLeft;
            slider.scrollLeft = scrollLeft - walk;
            velX = slider.scrollLeft - prevScrollLeft;
        });

        slider.addEventListener("wheel", () => {
            cancelMomentumTracking();
        });

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
    });
}
