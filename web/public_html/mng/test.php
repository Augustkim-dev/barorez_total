<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='9';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

?>
    <!-- PAGE CONTENT CONTAINER -->
    <div class="content" id="content">
        <!-- PAGE HEADING -->
        <div class="page-heading">
            <div class="page-heading__container">
                <div class="icon">
                    <span class="li-picture3"></span>
                </div>
                <h1 class="title">카테고리</h1>
                <p class="caption">
                    차량 카테고리 등록, 수정, 삭제 등을 할 수 있습니다.
                </p>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">카테고리관리</a></li>
                    <li class="breadcrumb-item active">카테고리</li>
                </ol>
            </nav>
        </div>
        <!-- //END PAGE HEADING -->
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">
                    <!-- 순서 변경 버튼과 검색 폼을 포함하는 컨테이너 -->
                    <div class="form-row">
                        <div class="col-6 col-lg-3">
                            <form method="post" enctype="multipart/form-data" action="./test_update.php">
                                <input type="file" name="excel" accept=".xls,.xlsx" required>
                                <button type="submit">업로드</button>
                            </form>
                        </div>
                    </div>

                    <style>
                        .depth-container { display: flex; gap: 10px; min-height: 500px; overflow: auto}
                        .depth-column {
                            flex: 1;
                            min-width: 300px;
                            border: 1px solid #ccc;
                            padding: 10px;
                        }
                        .depth-title {
                            font-weight: bold;
                            margin-bottom: 10px;
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            padding-bottom: 10px;
                            border-bottom: 1px dotted #ccc;
                        }
                        .item {
                            padding: 5px;
                            border: 1px solid #ddd;
                            margin-bottom: 5px;
                            cursor: pointer;
                            background-color: #f9f9f9;
                            display: flex;
                            flex-direction: column;
                            justify-content: space-between;
                            align-items: flex-start;
                        }
                        .item.selected {
                            background-color: #d0ebff;
                            font-weight: bold;
                        }
                        .item-buttons button {
                            margin-top: 5px;
                        }
                        .add-btn {
                            margin-top: 10px;
                            color: blue;
                            cursor: pointer;
                        }
                        pre {
                            background: #f4f4f4;
                            padding: 10px;
                            max-height: 300px;
                            overflow: auto;
                        }
                    </style>

<!--                    <h3>뎁스 개수 입력</h3>-->
                    <input type="hidden" id="depthCount" value="5" min="1" max="10" />
<!--                    <button onclick="createDepthColumns()">생성</button>-->
                    <button onclick="fetchDummyData()">더미 데이터 불러오기</button>

                    <h3>카테고리 관리</h3>
                    <div id="categoryArea" class="depth-container"></div>

                    <h3>데이터 구조 (콘솔용)</h3>
                    <pre id="output"></pre>

                    <script>
                        let categoryData = [];
                        let selectedItems = {};
                        const depthNames = ['제조사', '모델', '서브모델', '등급', '서브등급'];
                        function createDepthColumns() {
                            const count = parseInt(document.getElementById('depthCount').value);
                            const area = document.getElementById('categoryArea');
                            area.innerHTML = '';
                            categoryData = [];
                            selectedItems = {};
                            for (let i = 0; i < count; i++) {
                                categoryData.push([]);
                                const column = document.createElement('div');
                                column.className = 'depth-column';
                                column.dataset.depth = i;
                                column.innerHTML = `
      <div class="depth-title">
        <span>${depthNames[i] || `${i + 1} 뎁스`}</span>
        <div>
            <button class="btn py-1" onclick="sortItems(${i})">정렬</button>
            <div class="btn btn-info margin-right-1 py-1" onclick="addItem(${i})">+ 카테고리 추가</div>
        </div>
      </div>
      <div class="depth-list" id="depthList${i}"></div>
<!--      <div class="add-btn btn btn-info margin-right-5" onclick="addItem(${i})">+ 카테고리 추가</div>-->
    `;
                                area.appendChild(column);
                            }
                            updateOutput();
                        }

                        function generateId() {
                            return Date.now() + Math.random().toString(36).substr(2, 5);
                        }

                        function addItem(depth) {
                            if (depth > 0 && !selectedItems[depth - 1]) {
                                alert(`${depthNames[depth - 1] || depth + ' 뎁스'}를 먼저 선택하세요.`);
                                return;
                            }

                            let name = prompt(`${depthNames[depth] || depth + 1 + ' 뎁스'} 항목명을 입력하세요`);
                            if (!name) return;

                            let startYear = null;
                            let endYear = null;
                            if (depth === 2) { // 서브모델
                                startYear = prompt("연식 시작년도 (예: 2015)");
                                if (!startYear) return;
                                endYear = prompt("연식 종료년도 (예: 2020)");
                                if (!endYear) return;
                                name += ` (${startYear} ~ ${endYear})`;
                            }

                            const parentId = depth === 0 ? null : selectedItems[depth - 1].id;
                            const id = generateId();
                            const newItem = { id, name, parentId, startYear, endYear };
                            console.log('item',newItem);
                            categoryData[depth].push(newItem);
                            renderDepth(depth);
                            updateOutput();
                        }

                        function editItem(depth, id) {
                            const item = categoryData[depth].find(i => i.id === id);
                            if (!item) return;

                            if (depth === 2) {
                                // 기존 카테고리명에서 연식 정보 제거 (정규식)
                                const nameOnly = item.name.replace(/\s*\(\d{4}\s*~\s*\d{4}\)/, '').trim();
                                const mvStart = item.mv_start || '';
                                const mvEnd = item.mv_end || '';

                                const newName = prompt('수정할 항목명을 입력하세요', nameOnly);
                                if (!newName) return;

                                const newStart = prompt('연식 시작년도 (예: 2018)', mvStart);
                                const newEnd = prompt('연식 종료년도 (예: 2024)', mvEnd);
                                if (!newStart || !newEnd) {
                                    alert("연식 정보가 누락되었습니다.");
                                    return;
                                }

                                item.name = `${newName} (${newStart} ~ ${newEnd})`;
                                item.mv_start = newStart;
                                item.mv_end = newEnd;
                            } else {
                                const newName = prompt('수정할 항목명을 입력하세요', item.name);
                                if (!newName) return;
                                item.name = newName;
                            }

                            console.log('item',item);
                            renderDepth(depth);
                            updateOutput();
                        }


                        function renderDepth(depth) {
                            const listEl = document.getElementById(`depthList${depth}`);
                            listEl.innerHTML = '';

                            const items = categoryData[depth];
                            const parentId = depth === 0 ? null : selectedItems[depth - 1]?.id;

                            items.forEach(item => {
                                if (item.parentId !== parentId) return;

                                const itemEl = document.createElement('div');
                                itemEl.className = 'item';
                                itemEl.innerHTML = `
      <span>${item.name}</span>
      <div class="item-buttons">
        <button onclick="editItem(${depth}, '${item.id}')" class="btn btn-secondary py-1">수정</button>
        <button onclick="deleteItem(${depth}, '${item.id}')" class="btn btn-gray py-1">삭제</button>
      </div>
    `;
                                if (selectedItems[depth]?.id === item.id) {
                                    itemEl.classList.add('selected');
                                }

                                itemEl.onclick = () => {
                                    selectedItems[depth] = item;
                                    for (let i = depth + 1; i < categoryData.length; i++) {
                                        delete selectedItems[i];
                                        renderDepth(i);
                                    }
                                    renderDepth(depth);
                                };

                                listEl.appendChild(itemEl);
                            });
                        }

                        function deleteItem(depth, id) {
                            const hasChild = categoryData[depth + 1]?.some(i => i.parentId === id);
                            if (hasChild) {
                                alert('하위 항목이 존재하여 삭제할 수 없습니다.');
                                return;
                            }
                            categoryData[depth] = categoryData[depth].filter(i => i.id !== id);
                            if (selectedItems[depth]?.id === id) {
                                delete selectedItems[depth];
                            }
                            renderDepth(depth);
                            updateOutput();
                        }

                        function sortItems(depth) {
                            categoryData[depth].sort((a, b) => a.name.localeCompare(b.name, 'ko-KR'));
                            renderDepth(depth);
                            updateOutput();
                        }

                        function updateOutput() {
                            const allData = categoryData.flat();
                            document.getElementById('output').textContent = JSON.stringify(allData, null, 2);
                        }

                        function fetchDummyData() {
                            const dummyRaw = [
                                { name: "현대", parentName: null },
                                { name: "기아", parentName: null },
                                { name: "아반떼", parentName: "현대" },
                                { name: "쏘렌토", parentName: "기아" },
                                { name: "아반떼 N", parentName: "아반떼", mv_start: "2018", mv_end: "2022" },
                                { name: "쏘렌토 하이브리드", parentName: "쏘렌토", mv_start: "2020", mv_end: "2024" },
                                { name: "가솔린", parentName: "쏘렌토 하이브리드" }
                            ];

                            categoryData = [];
                            selectedItems = {};

                            const depthCount = parseInt(document.getElementById('depthCount').value);
                            for (let i = 0; i < depthCount; i++) {
                                categoryData.push([]);
                            }

                            const nameToId = {};
                            const nameToDepth = {};

                            dummyRaw.forEach(item => {
                                const id = generateId();
                                nameToId[item.name] = id;

                                let depth = 0;
                                let parentId = null;

                                if (item.parentName) {
                                    parentId = nameToId[item.parentName];
                                    for (let d = 0; d < categoryData.length; d++) {
                                        if (categoryData[d].some(it => it.id === parentId)) {
                                            depth = d + 1;
                                            break;
                                        }
                                    }
                                }

                                if (depth < categoryData.length) {
                                    let displayName = item.name;

                                    if (depth === 2 && item.mv_start && item.mv_end) {
                                        displayName = `${item.name} (${item.mv_start} ~ ${item.mv_end})`;
                                    }

                                    const newItem = {
                                        id,
                                        name: displayName,
                                        parentId
                                    };

                                    // 연식 정보는 따로 저장
                                    if (depth === 2) {
                                        newItem.mv_start = item.mv_start;
                                        newItem.mv_end = item.mv_end;
                                    }

                                    categoryData[depth].push(newItem);
                                }
                            });

                            for (let i = 0; i < categoryData.length; i++) {
                                renderDepth(i);
                            }
                            updateOutput();
                        }
                        // 초기 실행
                        createDepthColumns();
                    </script>
            </div>

        </div>
    </div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
