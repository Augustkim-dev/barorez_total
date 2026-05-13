<?php
if ($editor_upload=="Y"){
    ?>

    <script src="<?=CDN_HTTP?>/lib/ckeditor5-41.3.0/build/ckeditor.js"></script>
    <!--<script src="https://cdn.ckeditor.com/ckeditor5/37.1.0/classic/ckeditor.js"></script>-->
    <script>
        var cdn_url       = "<?php echo CDN_HTTP ?>";
    </script>
    <script src="<?=CDN_HTTP?>/lib/ckeditor5-41.3.0/build/config.js?v=<?=$v_txt?>"></script>
<? } ?>
<textarea name="<?=$editor_name?>" id="<?=$editor_name?>" class="form-control"><?=$row[$editor_name]?></textarea>
<style>
    .dark-mode .ck-sticky-panel__content{
        background-color: rgba(0, 0, 0, 0.1) !important;
    }

    .dark-mode .ck-editor__editable_inline {
        background-color: rgba(0, 0, 0, 0.1) !important;
        border-left: rgba(255, 255, 255, 0.1) solid 0px;
        border-bottom: rgba(255, 255, 255, 0.1) solid 1px;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-bottom-right-radius:7px !important;
        border-bottom-left-radius:7px !important;
    }

    .dark-mode .ck-editor__editable_inline:focus {
        color: #fff !important;
        background-color: #2e2e4a !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-bottom-right-radius:7px !important;
        border-bottom-left-radius:7px !important;
    }

    .ck-editor__editable_inline {
        padding: 20px;
        background-color: #FFFFFF;
        border-left: #FFFFFF solid 2px;
        height: 300px;
        outline: none;
        overflow-y: scroll;
        overflow-x: auto;
        border-bottom-right-radius:7px !important;
        border-bottom-left-radius:7px !important;
    }

    .ck.ck-balloon-panel.ck-powered-by-balloon .ck.ck-powered-by a {
        display: none !important;
    }
</style>
<script>
    ClassicEditor
        .create( document.querySelector( '#<?=$editor_name?>' ), {
            mediaEmbed: { previewsInData: true, removeProviders: ["instagram", "twitter", "googleMaps", "flickr", "facebook"] },

            language: "ko",
            extraPlugins: [ MyCustomUploadAdapterPlugin ],
            fontFamily: {
                options: [
                    'default',
                    'Arial',
                    '궁서체',
                    '바탕',
                    '돋움',
                    '굴림',
                    '맑은고딕',
                    '나눔고딕'
                ],
                supportAllValues: true
            },
            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells',
                    'tableCellProperties',
                    'tableProperties'
                ]
            },
            licenseKey: '',

        } )
        .then( newEditor => {
            <?=$editor_name?> = newEditor;
        } )
        .catch( error => {
            console.error( error );
        } );
</script>