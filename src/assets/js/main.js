function d(s) {
    console.log(s);
}

let filemanager;
filemanager = filemanager || {};

filemanager.main = (function(scope) {

    let sendPost = function(path, parameters) {
        let form = $('<form></form>');

        form.attr("method", "post");
        form.attr("action", path);

        $.each(parameters, function(key, value) {
            let field = $('<input></input>');

            field.attr("type", "hidden");
            field.attr("name", key);
            field.attr("value", value);

            form.append(field);
        });

        $(document.body).append(form);
        form.submit();
    }

    scope.topBar = function () {
        $('.top-bar .upload, .empty-content .upload').on('click', function () {
            $('.dropzone, .overlay').show();
        })

        $('.dropzone .fa-times').on('click', function () {
            $('.dropzone, .overlay').hide();
        })

        $('.top-bar .new-folder, .empty-content .new-folder').on('click', function () {
            $('.new-folder-box, .overlay').show();
        })

        $('.new-folder-box .fa-times').on('click', function () {
            $('.new-folder-box, .overlay').hide();
        })
    };

    scope.filemanager = function () {
        let $row = $('.filemanager .row.file, .filemanager .row.folder');

        $row.on('mouseover', function () {
            $(this).find('.action').show();
        });

        $row.on('mouseout', function () {
            $(this).find('.action').hide();
        });

        $('.filemanager .action i.edit').on('click', function (e) {
            e.preventDefault();
            $('.edit-box, .overlay').show();

            let name = $(this).parents('.row').find('.filename .name').attr('data-name');
            let ext = $(this).parents('.row').find('.filename .name').attr('data-ext');
            $('.edit-box input[name="editName"], .edit-box input[name="oldEditName"]').val(name);
            $('.edit-box input[name="ext"]').val(ext);
        })

        $('.edit-box .fa-times').on('click', function () {
            $('.edit-box, .overlay').hide();
        })

        $('.filemanager .action i.remove').on('click', function (e) {
            e.preventDefault();

            let result = confirm('Are you sure');
            if (result) {
                let name = $(this).parents('.row').find('.filename .name').attr('data-name');
                let ext = $(this).parents('.row').find('.filename .name').attr('data-ext');

                name = (ext ? name + '.' + ext : name);

                sendPost($('.filemanager').attr('data-path'), {'removeName': name});

            }
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

Dropzone.autoDiscover = false;

$(function() {

    filemanager.main.topBar();
    filemanager.main.filemanager();
    filemanager.main.insertFile();

    let $dropzoneBox = $('#dropzoneBox');

    if($dropzoneBox.length > 0) {
        let dropzone = new Dropzone('#dropzoneBox');
        dropzone.on('queuecomplete', function () {
            location.reload();
        });
    }

});