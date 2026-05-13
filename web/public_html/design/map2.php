<?
$_SUB_HEAD_TITLE = "회원가입"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
    <div class="sub_pg ">
        <div class="map_wrap">
            <div id="mapArea"></div>
            <button class="map_marker" data-store="바다마을 해물칼국수[성수점]">
                <img src="icon-marker.png" alt="">
            </button>
            <!-- 지도보기 버튼 -->
            <button class="map_btn" id="closeSheet">지도보기</button>

            <!-- 바텀시트 -->
            <div class="bottom_sheet" id="sheet">
                <div class="handler" id="handler"></div>

                <div class="sheet_content">
                    <div class="store_preview" id="storePreview">
                        <!-- ➜ 2번 상태에서 보이는 '한 개 블록' -->
                        <div class="store_item">여기에 매장 미리보기(마커 클릭 시 변경)</div>
                    </div>

                    <div class="store_list" id="storeList">
                        <!-- ➜ 3번 상태에서 보이는 전체 목록 -->
                        <div class="store_item">매장1</div>
                        <div class="store_item">매장2</div>
                        <div class="store_item">매장3</div>
                        <div class="store_item">매장4</div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
<script>
   const sheet = document.getElementById('sheet');
const handler = document.getElementById('handler');
const closeSheetBtn = document.getElementById('closeSheet');
const preview = document.getElementById('storePreview');
const list = document.getElementById('storeList');

let sheetState = 0; // 0=닫힘, 1=중간, 2=풀

const SHEET_HEIGHT = {
    0: 0,
    1: 240, 
    2: window.innerHeight * 0.8
};

function setSheet(state) {
    sheetState = state;
    sheet.style.height = SHEET_HEIGHT[state] + "px";

    if (state === 1) {
        preview.style.display = "block";
        list.style.display = "none";
        closeSheetBtn.style.display = "block";

    } else if (state === 2) {
        preview.style.display = "none";
        list.style.display = "block";
        closeSheetBtn.style.display = "block";

    } else {
        closeSheetBtn.style.display = "none";
    }
}

// -------------------------------
// ① 마커 클릭 시 자동으로 2번 상태로
// -------------------------------
document.querySelectorAll(".map_marker").forEach(marker => {
    marker.addEventListener("click", function () {

        preview.innerHTML = `
            <div class="store_item">${this.dataset.store}</div>
        `;

        // 중간 단계(2번 화면)
        setSheet(1);
    });
});

// -------------------------------
// ② 드래그 핸들러
// -------------------------------
let startY, startHeight;

handler.addEventListener("mousedown", start);
handler.addEventListener("touchstart", start);

function start(e) {
    startY = e.touches ? e.touches[0].clientY : e.clientY;
    startHeight = parseInt(getComputedStyle(sheet).height);

    document.addEventListener('mousemove', move);
    document.addEventListener('mouseup', end);
    document.addEventListener('touchmove', move);
    document.addEventListener('touchend', end);
}

function move(e) {
    const y = e.touches ? e.touches[0].clientY : e.clientY;
    const dy = startY - y;

    let newH = startHeight + dy;
    if (newH < 0) newH = 0;
    if (newH > SHEET_HEIGHT[2]) newH = SHEET_HEIGHT[2];

    sheet.style.height = newH + "px";
}

function end() {
    const current = parseInt(getComputedStyle(sheet).height);

    if (current < 120) setSheet(0);
    else if (current < 350) setSheet(1);
    else setSheet(2);

    document.removeEventListener('mousemove', move);
    document.removeEventListener('mouseup', end);
    document.removeEventListener('touchmove', move);
    document.removeEventListener('touchend', end);
}

// -------------------------------
// ③ 지도보기 버튼 → 완전 닫기
// -------------------------------
closeSheetBtn.addEventListener("click", () => setSheet(0));

</script>

<? include_once("./inc/tail.php"); ?>