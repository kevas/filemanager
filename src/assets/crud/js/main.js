function d(s) {
    console.log(s);
}

let crud;
crud = crud || {};

crud.main = (function(scope) {

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

    return scope;

}(crud.main || {}));

$(function() {

    crud.main.topBar();

    let dropzone = new Dropzone(".dropzone-box");
    dropzone.on('queuecomplete', function() {
        location.reload();
    });

});