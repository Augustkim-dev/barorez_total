<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='00';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";

$tbl_name = "guesthouse_t";

if ($_GET['id']) {
    $DB->where('id', $_GET['id']);
    $row = $DB->getOne($tbl_name);
}
?>
    <!-- PAGE CONTENT CONTAINER -->
    <div class="content" id="content">
        <?php include_once "./pheading.php";?>
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">

                    <h5 class="mb-4">외국인관광도시민박업 상세정보</h5>

                    <!-- 기본정보 -->
                    <div class="form-group row align-items-center">
                        <label class="col-sm-2 col-form-label">사업장명</label>
                        <div class="col-sm-2 col-form-label">
                            <span><?=$row['business_name'] ?? '-'?></span>
                        </div>
                        <label class="col-sm-2 col-form-label">인허가일자</label>
                        <div class="col-sm-2 col-form-label">
                            <span><?=$row['license_date'] ?? '-'?></span>
                        </div>
                        <label class="col-sm-2 col-form-label">인허가취소일자</label>
                        <div class="col-sm-2 col-form-label">
                            <span><?=$row['license_cancel_date'] ?? '-'?></span>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-2 col-form-label">영업상태구분코드</label>
                        <div class="col-sm-4 col-form-label">
                            <span><?=$row['status_change_code'] ?? '-'?></span>
                        </div>
                        <label class="col-sm-2 col-form-label">상세영업상태코드</label>
                        <div class="col-sm-4 col-form-label">
                            <span><?=$row['business_status_code'] ?? '-'?></span>
                        </div>
                    </div>

                    <!-- 주소 -->
                    <div class="form-group row align-items-center">
                        <label class="col-sm-2 col-form-label">우편번호</label>
                        <div class="col-sm-2 col-form-label">
                            <span><?=$row['addr_postcode'] ?? '-'?></span>
                        </div>
                        <label class="col-sm-2 col-form-label">도로명주소</label>
                        <div class="col-sm-2 col-form-label">
                            <span><?=$row['addr_road'] ?? '-'?></span>
                        </div>
                        <label class="col-sm-2 col-form-label">지번주소</label>
                        <div class="col-sm-2 col-form-label">
                            <span><?=$row['addr_jibeon'] ?? '-'?></span>
                        </div>
                    </div>

                    <!-- 건물 정보 -->
                    <div class="form-group row align-items-center">
                        <label class="col-sm-2 col-form-label">건물용도</label>
                        <div class="col-sm-4 col-form-label">
                            <span><?=$row['building_use'] ?? '-'?></span>
                        </div>
                        <label class="col-sm-2 col-form-label">용도지구</label>
                        <div class="col-sm-4 col-form-label">
                            <span><?=$row['zoning_district'] ?? '-'?></span>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-2 col-form-label">시설면적</label>
                        <div class="col-sm-4 col-form-label">
                        <span><?=$row['facility_area'] ? $row['facility_area'].' ㎡' : '-'?>
                        (<?=round($row['facility_area']/3.3058, 1)?> 평)</span>
                        </div>
                        <label class="col-sm-2 col-form-label">객실수</label>
                        <div class="col-sm-4 col-form-label">
                            <span><?=$row['room_count'] ?? 0?></span>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-2 col-form-label">총층수</label>
                        <div class="col-sm-2 col-form-label">
                            <span><?=$row['total_floors'] ?? 0?></span>
                        </div>
                        <label class="col-sm-2 col-form-label">지상층수</label>
                        <div class="col-sm-2 col-form-label">
                            <span><?=$row['floor_above'] ?? 0?></span>
                        </div>
                        <label class="col-sm-2 col-form-label">지하층수</label>
                        <div class="col-sm-2 col-form-label">
                            <span><?=$row['floor_below'] ?? 0?></span>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-2 col-form-label">좌표정보 (X)</label>
                        <div class="col-sm-4 col-form-label">
                            <span><?=$row['coord_x'] ?? '-'?></span>
                        </div>
                        <label class="col-sm-2 col-form-label">좌표정보 (Y)</label>
                        <div class="col-sm-4 col-form-label">
                            <span><?=$row['coord_y'] ?? '-'?></span>
                        </div>
                    </div>

                    <!-- 지도 표시 -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">위치 지도</label>
                        <div class="col-sm-10">
                            <div id="map" style="width:100%;height:400px;"></div>
                        </div>
                    </div>

                    <div class="form-group row justify-content-center margin-top-30">
                        <button type="button" onclick="history.go(-1);" class="btn btn-outline-secondary mx-1">목록</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

<script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=64f38569c1ccd867386473424a09f296"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.8.0/proj4.js"></script>
<script>
    proj4.defs("EPSG:5175","+proj=tmerc +lat_0=38 +lon_0=127 +k=1 +x_0=200000 +y_0=500000 +ellps=GRS80 +units=m +no_defs");

    let x = <?=$row['coord_x']?>;
    let y = <?=$row['coord_y']?>;

    // 변환: EPSG:5175 → WGS84
    var wgs84 = proj4('EPSG:5175','EPSG:4326',[x,y]);
    console.log('x',x);
    console.log('y',y);
    console.log("위도:", wgs84[1], "경도:", wgs84[0]);
    // 이미지 지도에 표시할 마커입니다
    var marker = {
        position: new kakao.maps.LatLng(wgs84[1], wgs84[0]),
        text: '<?=$row['business_name']?>' // text 옵션을 설정하면 마커 위에 텍스트를 함께 표시할 수 있습니다
    };

    var staticMapContainer  = document.getElementById('map'), // 이미지 지도를 표시할 div
        staticMapOption = {
            center: new kakao.maps.LatLng(wgs84[1], wgs84[0]),
            level: 3, // 이미지 지도의 확대 레벨
            marker: marker // 이미지 지도에 표시할 마커
        };

    // 이미지 지도를 생성합니다
    var staticMap = new kakao.maps.StaticMap(staticMapContainer, staticMapOption);

</script>

<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
