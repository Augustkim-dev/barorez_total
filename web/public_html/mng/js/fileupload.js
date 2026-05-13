/* 멀티 */
function createFileUploader(options = {}) {
    // 기본 설정
    const config = {
        container: options.container || '.upload-container',
        trigger: options.trigger || '#uploadTrigger',
        filepondElement: options.filepondElement || '.filepond',
        maxFiles: options.maxFiles || 5,
        maxFileSize: options.maxFileSize || '5MB',
        allowedFileTypes: options.allowedFileTypes || ['image/jpeg', 'image/png', 'image/gif'],
        imageMinWidth: options.imageMinWidth || 100,
        imageMinHeight: options.imageMinHeight || 100,
        imageMaxWidth: options.imageMaxWidth || 4000,
        imageMaxHeight: options.imageMaxHeight || 3000,
        ajaxUrl: options.ajaxUrl || './category_upjong_update.php'
    };

    // 요소 선택
    const container = document.querySelector(config.container);
    const uploadTrigger = document.querySelector(config.trigger);
    let pond = null;
    let hasShownMaxFileMessage = false;
    let hasShownFileSizeMessage = false;

    // 순서변경
    let imageOrder = [];

    // 삭제된 파일 추적을 위한 배열
    let removedFiles = [];

    // 에러 메시지
    const ERROR_MESSAGES = {
        fileSize: `파일 크기는 ${config.maxFileSize}를 초과할 수 없습니다.`,
        fileType: '허용된 파일 형식: JPG, PNG, GIF',
        imageSize: `이미지 크기는 최소 ${config.imageMinWidth}x${config.imageMinHeight}px, 최대 ${config.imageMaxWidth}x${config.imageMaxHeight}px 이내여야 합니다.`,
        maxFiles: `최대 ${config.maxFiles}개의 파일만 업로드할 수 있습니다.`
    };

    function init() {
        FilePond.registerPlugin(FilePondPluginImagePreview);
        initFilePond();
        initSortable();
        initUploadTrigger();
    }

    function initFilePond() {
        pond = FilePond.create(document.querySelector(config.filepondElement), {
            allowMultiple: true,
            maxFiles: config.maxFiles,
            acceptedFileTypes: config.allowedFileTypes,
            maxFileSize: config.maxFileSize,
            beforeAddFile: handleBeforeAddFile,
            onaddfile: handleAddFile,
            onremovefile: handleRemoveFile
        });
    }

    function initSortable() {
        if (container) {
            new Sortable(container, {
                animation: 150,
                handle: '.preview-box',
                filter: '.filepond--root, #uploadTrigger',
                onEnd: updateImageOrder
            });
        }
    }

    function initUploadTrigger() {
        if (!uploadTrigger) return;

        uploadTrigger.addEventListener('click', () => {
            const currentFiles = document.querySelectorAll('.preview-box').length;

            if (currentFiles >= config.maxFiles) {
                alert(ERROR_MESSAGES.maxFiles);
                return;
            }

            const input = document.createElement('input');
            input.type = 'file';
            input.multiple = true;
            input.accept = config.allowedFileTypes.join(',');

            input.onchange = e => {
                const selectedFiles = Array.from(e.target.files);
                const remainingSlots = config.maxFiles - currentFiles;

                if (selectedFiles.length > remainingSlots) {
                    alert(ERROR_MESSAGES.maxFiles);
                }

                const filesToAdd = selectedFiles.slice(0, remainingSlots);
                filesToAdd.forEach(file => pond.addFile(file));
            };

            input.click();
        });
    }

    function handleBeforeAddFile(item) {
        return new Promise((resolve, reject) => {
            if (!config.allowedFileTypes.includes(item.fileType)) {
                reject(ERROR_MESSAGES.fileType);
                return;
            }

            if (item.file.size > parseMaxFileSize()) {
                reject(ERROR_MESSAGES.fileSize);
                return;
            }

            const img = new Image();
            img.onload = () => {
                if (img.width < config.imageMinWidth ||
                    img.height < config.imageMinHeight ||
                    img.width > config.imageMaxWidth ||
                    img.height > config.imageMaxHeight) {
                    reject(ERROR_MESSAGES.imageSize);
                    return;
                }
                resolve(true);
            };
            img.onerror = () => reject('이미지를 로드할 수 없습니다.');
            img.src = URL.createObjectURL(item.file);
        });
    }

    function handleAddFile(error, file) {
        if (error) {
            console.error('Error adding file:', error);
            return;
        }

        const previewBox = createPreviewElement(file);
        container.insertBefore(previewBox, uploadTrigger);

        updateImageOrder(); // 순서 업데이트

        const currentFiles = document.querySelectorAll('.preview-box').length;
        if (currentFiles >= config.maxFiles) {
            uploadTrigger.classList.add('hidden');
        }

        Fancybox.bind('[data-fancybox="gallery"]', {
            groupAll: true
        });
    }

    function createPreviewElement(file) {
        const previewBox = document.createElement('div');
        previewBox.className = 'preview-box';
        previewBox.setAttribute('data-filepond-file-id', file.id);

        const link = document.createElement('a');
        link.href = URL.createObjectURL(file.file);
        link.setAttribute('data-fancybox', 'gallery');

        const img = document.createElement('img');
        img.src = URL.createObjectURL(file.file);
        link.appendChild(img);

        const removeBtn = document.createElement('button');
        removeBtn.className = 'remove-btn';
        removeBtn.innerHTML = '×';
        removeBtn.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            pond.removeFile(file.id);

        };

        previewBox.appendChild(link);
        previewBox.appendChild(removeBtn);



        return previewBox;
    }

    function handleRemoveFile(error, file) {
        if (error) return;

        console.log(file.id)
        const previewBox = document.querySelector(`[data-filepond-file-id="${file.id}"]`);
        if (previewBox) {
            // FilePond에서 삭제된 파일의 번호 추출
            // const fileMatch = file.filename?.match(/ct_img_\d+_(\d+)/);
            // if (fileMatch && fileMatch[1]) {
            //     removedFiles.push(parseInt(fileMatch[1]));
            // }

            removedFiles.push(file.id)
            previewBox.remove();

            updateImageOrder(); // 순서 업데이트
        }

        if (pond.getFiles().length < config.maxFiles) {
            uploadTrigger.classList.remove('hidden');
            hasShownMaxFileMessage = false;
            hasShownFileSizeMessage = false;
        }
    }

    function loadImages(ct_idx) {
        $.ajax({
            url: config.ajaxUrl,
            type: 'POST',
            dataType:'json',
            data: {
                act: 'loadimage',
                ct_idx: ct_idx
            },
            beforeSend: () => {
                $('#splinner_modal').modal('show');
            },
            success: handleLoadImagesSuccess,
            error: handleLoadImagesError
        });
    }

    function handleLoadImagesSuccess(response) {
        $('#splinner_modal').modal('hide');

        if(response.success) {
            document.querySelectorAll('.preview-box').forEach(box => box.remove());

            Object.entries(response.data).forEach(([imgKey, imgData]) => {
                if(imgData.exists) {
                    createPreviewBox(imgData.url, imgKey);
                }
            });

            updateUploadTriggerVisibility();
        } else if(!response.success) {
            console.error(response.message)
        } else {
            alert(response.message || '이미지 로드 중 오류가 발생했습니다.');
        }
    }

    function handleLoadImagesError(jqXHR, textStatus, errorThrown) {
        $('#splinner_modal').modal('hide');
        // console.error("Status:", textStatus);
        // console.error("Error Thrown:", errorThrown);
        // console.error("Response Text:", jqXHR.responseText);
        alert('이미지 로드 중 오류가 발생했습니다.');
    }

    function createPreviewBox(imageUrl, imageId) {
        const previewBox = document.createElement('div');
        previewBox.className = 'preview-box';
        previewBox.setAttribute('data-image-id', imageId);

        const link = document.createElement('a');
        link.href = imageUrl;
        link.setAttribute('data-fancybox', 'gallery');

        const img = document.createElement('img');
        img.src = imageUrl;
        link.appendChild(img);

        const removeBtn = document.createElement('button');
        removeBtn.className = 'remove-btn';
        removeBtn.innerHTML = '×';
        removeBtn.onclick = (e) => handleRemoveButtonClick(e, previewBox, imageId);

        previewBox.appendChild(link);
        previewBox.appendChild(removeBtn);
        container.insertBefore(previewBox, uploadTrigger);

        updateImageOrder(); // 순서 업데이트

        Fancybox.bind('[data-fancybox="gallery"]', {
            groupAll: true
        });
    }

    function handleRemoveButtonClick(e, previewBox, imageId) {
        e.preventDefault();
        e.stopPropagation();

        if(confirm('이미지를 삭제하시겠습니까?')) {

            removedFiles.push(imageId);

            previewBox.remove();
            const hiddenInput = document.getElementById(imageId);
            if(hiddenInput) hiddenInput.value = '';

            updateUploadTriggerVisibility();
        }
    }

    function updateUploadTriggerVisibility() {
        const currentFiles = document.querySelectorAll('.preview-box').length;
        if (currentFiles >= config.maxFiles) {
            uploadTrigger.classList.add('hidden');
        } else {
            uploadTrigger.classList.remove('hidden');
        }
    }

    function updateImageOrder() {
        const previewBoxes = document.querySelectorAll('.preview-box');
        imageOrder = [];

        previewBoxes.forEach((box, index) => {
            const filepondId = box.getAttribute('data-filepond-file-id');
            const imageId = box.getAttribute('data-image-id');

            if (filepondId) {
                imageOrder.push({
                    type: 'new',
                    id: filepondId,
                    position: index + 1
                });
            } else if (imageId) {
                imageOrder.push({
                    type: 'existing',
                    id: imageId,
                    position: index + 1
                });
            }
        });

        // console.log('Updated Image Order:', imageOrder);
    }

    function parseMaxFileSize() {
        const size = config.maxFileSize;
        const unit = size.replace(/[0-9]/g, '').toUpperCase();
        const number = parseInt(size);

        switch(unit) {
            case 'KB': return number * 1024;
            case 'MB': return number * 1024 * 1024;
            case 'GB': return number * 1024 * 1024 * 1024;
            default: return number;
        }
    }

    // 삭제된 파일 목록 반환
    function getRemovedFiles() {
        return removedFiles;
    }

    // 삭제된 파일 목록 초기화
    function clearRemovedFiles() {
        removedFiles = [];
    }

    // 이미지 순서 변경
    function getImageOrder() {
        return imageOrder;
    }

    // 초기화 실행
    init();

    // 공개 메서드 반환
    return {
        options: config,
        loadImages,
        getPond: () => pond,
        getRemovedFiles,
        clearRemovedFiles,
        getImageOrder
    };
}

/* 싱글 */
function createImageUploader(config) {
    // 기본 제약 조건 설정
    const defaultConstraints = {
        maxFileSize: 5 * 1024 * 1024,
        allowedFileTypes: ['image/jpeg', 'image/png', 'image/gif'],
        imageMinWidth: 100,
        imageMinHeight: 100,
        imageMaxWidth: 4000,
        imageMaxHeight: 3000,
        errorMessages: {
            fileSize: '이미지는 5MB를 초과할 수 없습니다.',
            fileType: '허용된 파일 형식: JPG, PNG, GIF',
            imageSize: '이미지 크기는 100x100px에서 4000x3000px 사이여야 합니다.'
        }
    };

    // 설정된 제약 조건과 기본 제약 조건 병합
    const UPLOAD_CONSTRAINTS = {};
    const FILE_PATTERNS = config.filePatterns || {}; // 파일 패턴 설정 추가

    for (const [key, value] of Object.entries(config.constraints || {})) {
        UPLOAD_CONSTRAINTS[key] = { ...defaultConstraints, ...value };
    }

    // 입력 ID에서 접두사와 인덱스를 추출하는 함수
    function parseInputId(inputId) {
        const matches = inputId.match(/^(.+?)(\d*)$/);
        if (matches) {
            const prefix = matches[1];
            const index = matches[2] || '';
            return { prefix, index };
        }
        return { prefix: inputId, index: '' };
    }

    // 입력 ID에 해당하는 트리거 ID를 생성하는 함수
    function getTriggerIdFromInputId(inputId) {
        const { prefix, index } = parseInputId(inputId);

        // FILE_PATTERNS에서 패턴 찾기
        //const pattern = FILE_PATTERNS[prefix] || {
        //    triggerPrefix: 'upload' + prefix.charAt(0).toUpperCase() + prefix.slice(1) + 'Trigger'
        //};

        // FILE_PATTERNS에서 패턴 찾기
        const pattern = FILE_PATTERNS[prefix] || {
            // 수정된 부분: 언더스코어를 제거하고 카멜 케이스로 변환
            triggerPrefix: 'upload' + prefix.replace(/_([a-z])/g, function(g) { return g[1].toUpperCase(); }).charAt(0).toUpperCase() + prefix.replace(/_([a-z])/g, function(g) { return g[1].toUpperCase(); }).slice(1) + 'Trigger'
        };

        return pattern.triggerPrefix + index;
    }

    // 트리거 ID에서 입력 ID를 추출하는 함수
    function getInputIdFromTriggerId(triggerId) {
        // FILE_PATTERNS의 역방향 매핑 생성
        const reversePatterns = {};
        Object.entries(FILE_PATTERNS).forEach(([inputPrefix, pattern]) => {
            reversePatterns[pattern.triggerPrefix] = inputPrefix;
        });

        // triggerId에서 패턴 찾기
        for (const [triggerPrefix, inputPrefix] of Object.entries(reversePatterns)) {
            if (triggerId.startsWith(triggerPrefix)) {
                const index = triggerId.slice(triggerPrefix.length);
                return inputPrefix + index;
            }
        }

        // 기본 패턴: triggerPrefix에서 input prefix 추출
        const matches = triggerId.match(/^upload(.+?)Trigger(\d*)$/);
        if (matches) {
            const prefix = matches[1].charAt(0).toLowerCase() +
                matches[1].slice(1).replace(/[A-Z]/g, letter => '_' + letter.toLowerCase());
            return prefix + (matches[2] || '');
        }

        return triggerId;
    }

    // 파일 유효성 검사 함수
    async function validateFile(file, constraints) {
        if (file.size > constraints.maxFileSize) {
            throw new Error(constraints.errorMessages.fileSize);
        }

        if (!constraints.allowedFileTypes.includes(file.type)) {
            throw new Error(constraints.errorMessages.fileType);
        }

        return new Promise((resolve, reject) => {
            const img = new Image();
            img.src = URL.createObjectURL(file);

            img.onload = () => {
                URL.revokeObjectURL(img.src);
                if (img.width < constraints.imageMinWidth ||
                    img.height < constraints.imageMinHeight ||
                    img.width > constraints.imageMaxWidth ||
                    img.height > constraints.imageMaxHeight) {
                    reject(new Error(constraints.errorMessages.imageSize));
                }
                resolve(true);
            };

            img.onerror = () => {
                URL.revokeObjectURL(img.src);
                reject(new Error(constraints.errorMessages.fileType));
            };
        });
    }

    function showError(message) {
        alert(message);
    }

    // 트리거와 입력 필드의 관계를 저장할 매핑
    const triggerToInputMap = {};

    function connectUploadBox(triggerId, inputId) {
        const trigger = document.getElementById(triggerId);
        const input = document.getElementById(inputId);

        // 필수 요소 존재 여부 확인
        if (!trigger || !input) {
            console.error(`필수 요소를 찾을 수 없습니다: ${triggerId} 또는 ${inputId}`);
            return;
        }

        // 입력 필드에 대한 파일 패턴 확인
        if (!FILE_PATTERNS[inputId]) {  // inputId를 직접 사용
            console.error(`${inputId}에 대한 파일 패턴이 정의되지 않았습니다.`);
            return;
        }


        // 매핑 정보 저장
        triggerToInputMap[triggerId] = inputId;

        const constraints = UPLOAD_CONSTRAINTS[inputId] || defaultConstraints;
        const removeBtn = trigger.querySelector('.remove-btn');

        // 삭제 버튼 이벤트 처리
        if (removeBtn) {
            removeBtn.style.display = 'none';
            removeBtn.removeAttribute('onclick');
            removeBtn.addEventListener('click', function(event) {
                const { prefix, index } = parseInputId(inputId);
                removeImage(prefix, index, event);
            });
        }

        // 기존 이미지 처리
        const existingImageUrl = trigger.dataset.existingImage;
        if (existingImageUrl && existingImageUrl !== '') {
            const img = document.createElement('img');
            img.src = existingImageUrl;
            img.classList.add('preview-image');

            // Fancybox 속성 추가
            img.setAttribute('data-fancybox', '');
            img.setAttribute('data-src', existingImageUrl);

            trigger.appendChild(img);
            trigger.classList.add('preview-box');

            if (removeBtn) {
                removeBtn.style.display = 'flex';
            }

            // 업로드 콘텐츠 숨기기
            const uploadContent = trigger.querySelector('.upload-content');
            if (uploadContent) {
                uploadContent.style.display = 'none';
            }

            // Fancybox 초기화
            if (typeof Fancybox !== 'undefined') {
                Fancybox.bind(`[data-fancybox]`, {
                    dragToClose: false,
                });
            }
        }

        // 트리거 클릭 이벤트
        trigger.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-btn') ||
                e.target.closest('.remove-btn') ||
                e.target.classList.contains('preview-image')) {
                return;
            }
            input.click();
        });

        // 파일 변경 이벤트
        input.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (file) {
                try {
                    // 삭제 플래그 초기화
                    const deleteFlag = document.getElementById(`${inputId}_delete`);
                    if (deleteFlag) {
                        deleteFlag.value = 'N';
                    }

                    await validateFile(file, constraints);

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const existingImg = trigger.querySelector('.preview-image');
                        if (existingImg) {
                            existingImg.remove();
                        }

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.classList.add('preview-image');

                        // Fancybox 속성 추가
                        img.setAttribute('data-fancybox', '');
                        img.setAttribute('data-src', e.target.result);

                        trigger.appendChild(img);
                        trigger.classList.add('preview-box');

                        // 업로드 콘텐츠 숨기기
                        const uploadContent = trigger.querySelector('.upload-content');
                        if (uploadContent) {
                            uploadContent.style.display = 'none';
                        }

                        if (removeBtn) {
                            removeBtn.style.display = 'flex';
                        }

                        // Fancybox 초기화
                        if (typeof Fancybox !== 'undefined') {
                            Fancybox.bind(`[data-fancybox]`, {
                                dragToClose: false,
                            });
                        }
                    };
                    reader.readAsDataURL(file);

                } catch (error) {
                    showError(error.message);
                    input.value = '';
                }
            }
        });
    }


    // 이미지 삭제 함수
    function removeImage(prefix, index, event) {
        // 이벤트 기본 동작과 전파 중지
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        // 이미지 필드 ID 확인
        const inputId = `${prefix}${index}`;
        const triggerId = getTriggerIdFromInputId(inputId);

        const trigger = document.getElementById(triggerId);
        const input = document.getElementById(inputId);
        const deleteFlag = document.getElementById(`${inputId}_delete`);

        if (!trigger || !input) {
            console.error(`삭제할 요소를 찾을 수 없습니다: ${triggerId} 또는 ${inputId}`);
            return;
        }

        const removeBtn = trigger.querySelector('.remove-btn');
        const img = trigger.querySelector('.preview-image');

        if (img) {
            // Fancybox 바인딩 제거 시도
            if (typeof Fancybox !== 'undefined') {
                try {
                    Fancybox.close(); // 열려있는 Fancybox 닫기
                } catch (e) {
                    console.log('Fancybox 닫기 오류:', e);
                }
            }
            img.remove();
        }

        // 삭제 플래그 설정
        if (deleteFlag) {
            deleteFlag.value = 'Y';
        }

        trigger.classList.remove('preview-box');
        input.value = '';

        if (removeBtn) {
            removeBtn.style.display = 'none';
        }

        // Upload 텍스트와 + 기호 표시
        const uploadContent = trigger.querySelector('.upload-content');
        if (uploadContent) {
            uploadContent.style.display = 'flex';
        }
    }


    // 모든 Fancybox 초기화
    function initAllFancybox() {
        if (typeof Fancybox !== 'undefined') {
            Fancybox.bind('[data-fancybox]', {
                dragToClose: false,
            });
        } else {
            console.warn('Fancybox가 로드되지 않았습니다.');
        }
    }

    // 초기화 함수
    function init() {
        // 모든 업로드 박스 연결
        for (const [prefix, pattern] of Object.entries(FILE_PATTERNS)) {
            const inputId = prefix;
            const triggerId = pattern.triggerPrefix;

            const trigger = document.getElementById(triggerId);
            const input = document.getElementById(inputId);

            if (trigger && input) {
                connectUploadBox(triggerId, inputId);
            } else {
                console.warn(`요소를 찾을 수 없습니다: ${triggerId} 또는 ${inputId}`);
            }
        }

        // 모든 Fancybox 초기화
        initAllFancybox();
    }


    // 외부에서 사용할 수 있는 메서드들 반환
    return {
        init,
        removeImage,
        connectUploadBox,
        initFancybox: initAllFancybox
    };
}

//파일 업로드
function updateFileName(inputId, labelId) {
    var fileInput = document.getElementById(inputId);
    var label = document.getElementById(labelId);

    fileInput.addEventListener('change', function(event) {
        var fileName = fileInput.files.length > 0 ? fileInput.files[0].name : "파일 선택";
        if (label) {
            label.textContent = fileName;
        } else {
            console.error("Label element not found for", labelId);
        }
    });
}