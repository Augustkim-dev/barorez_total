function MyCustomUploadAdapterPlugin( editor ) {
	editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
		return new MyUploadAdapter( loader );
	};
}

class MyUploadAdapter {
    constructor( loader ) {
        // CKEditor 5's FileLoader instance.
        this.loader = loader;
        console.log(loader);
        // URL where to send files.
        this.url = cdn_url+"/lib/ckeditor5-41.3.0/imageUpload/upload.php";
    }

    // Starts the upload process.
    upload() {
        return this.loader.file.then( file => new Promise(((resolve, reject) => {
            this._initRequest();
            this._initListeners( resolve, reject, file );
            this._sendRequest( file );
        })))
    }

    // Aborts the upload process.
    abort() {
        if ( this.xhr ) {
            this.xhr.abort();
        }
    }

    // Example implementation using XMLHttpRequest.
    _initRequest() {
        const xhr = this.xhr = new XMLHttpRequest();
        xhr.open( 'POST', this.url, true );
        xhr.responseType = 'json';
    }

    // Initializes XMLHttpRequest listeners.
    _initListeners(resolve, reject, file) {
        const xhr = this.xhr;
        const loader = this.loader;
        const genericErrorText = '파일을 업로드 할 수 없습니다.'

        console.log(xhr)
        xhr.addEventListener('error', () => {reject(genericErrorText)})
        xhr.addEventListener('abort', () => reject())
        xhr.addEventListener('load', () => {
            const response = xhr.response

            if(!response || response.error) {
               return reject( response && response.error ? response.error.message : genericErrorText );
            }

            resolve({
                default: response.url //업로드된 파일 주소
            })
        })
    }

    _sendRequest(file) {
        const data = new FormData()
        data.append('upload',file)
        this.xhr.send(data)
    }
}