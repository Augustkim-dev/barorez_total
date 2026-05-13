

$(document).ready(function () {
	//테이블 가로스크롤 마우스로 터치
	window.onload = function () {
		slider = document.querySelectorAll('.touch_scroll');
		for ( var i = 0; i < slider.length; i++ ) {
				touchScroll(slider[i]);
    }
	};

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
});// 한페이지에 touch_scroll가 2개 이상 있을때 1개만 작동 하고 나머지는 작동을 안합니다.



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


//스위치 토글버튼 custom-switch
function initSwitchOutside(root = document) {
  root.querySelectorAll('.custom-switch.switch-outside').forEach(function (wrap) {
    if (wrap.dataset.init) return;
    wrap.dataset.init = 'true';

    const input = wrap.querySelector('.custom-control-input');
    const state = wrap.querySelector('.switch-state');

    const onText = input.dataset.on || 'ON';
    const offText = input.dataset.off || 'OFF';

    const updateText = () => {
      if (input.checked) {
        state.textContent = onText;
        state.classList.add('is-on');
        state.classList.remove('is-off');
      } else {
        state.textContent = offText;
        state.classList.add('is-off');
        state.classList.remove('is-on');
      }
    };

    updateText();
    input.addEventListener('change', updateText);
  });
}

document.addEventListener('DOMContentLoaded', function () {
  initSwitchOutside();
});


//커스텀 셀렉트박스
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.custom-sel').forEach(select => {
    const trigger = select.querySelector('.select-trigger');
    const options = select.querySelectorAll('.select-options li');
    const hiddenInput = select.querySelector('input[type="hidden"]');

    /* 트리거 클릭 */
    trigger.addEventListener('click', e => {
      e.stopPropagation();
      closeAll();
      select.classList.toggle('open');
    });

    /* 옵션 선택 */
    options.forEach(option => {
      option.addEventListener('click', e => {
        e.stopPropagation();

        /* disabled 옵션은 무시 */
        if (option.classList.contains('is-disabled')) return;

        options.forEach(o => o.classList.remove('is-selected'));
        option.classList.add('is-selected');

        trigger.textContent = option.textContent;
        hiddenInput.value = option.dataset.value;

        select.classList.remove('open');
      });
    });
  });

  /* 바깥 클릭 닫기 */
  document.addEventListener('click', closeAll);

  function closeAll() {
    document.querySelectorAll('.custom-sel.open').forEach(select => {
      select.classList.remove('open');
    });
  }
});
