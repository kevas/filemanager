function d(s) {
    console.log(s);
}

let filemanager;
filemanager = filemanager || {};

filemanager.main = (function(scope) {

    scope.topBar = function () {
        $('.top-bar .upload, .empty-content .upload').on('click', function () {
            $('.dropzone-box, .dropzone-bg').show();
        })

        $('.dropzone-box .fa-times').on('click', function () {
            $('.dropzone-box, .dropzone-bg').hide();
        })

        $('.top-bar .new-folder, .empty-content .new-folder').on('click', function () {
            $('.new-folder-bg, .new-folder-box').show();
        })

        $('.new-folder-box .fa-times').on('click', function () {
            $('.new-folder-bg, .new-folder-box').hide();
        })
    };

    scope.insertFile = function () {
        $('.filemanager.insertFile .row.file').on('dblclick',function(e) {
            let dataFile = JSON.parse( $(this).attr('data-file'));
            $(document).trigger('filemanagerInsertFile', [$(this), dataFile]);
        });
    };

    return scope;

}(filemanager.main || {}));

$(function() {

    filemanager.main.topBar();
    filemanager.main.insertFile();

    let $dropzoneBox = $('.dropzone-box');

    if($dropzoneBox.length > 0) {
        let dropzone = new Dropzone('.dropzone-box');
        dropzone.on('queuecomplete', function () {
            location.reload();
        });
    }

});