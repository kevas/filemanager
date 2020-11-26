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
        form.trigger('submit');
    }

    let isValueValid = function(value){
        if(!value) {
            return false;
        }

        let rg1 = /^[^\\/:\*\?"<>\|]+$/;
        let rg2 = /^\./;
        let rg3 = /^(nul|prn|con|lpt[0-9]|com[0-9])(\.|$)/i;

        return rg1.test(value)&&!rg2.test(value)&&!rg3.test(value);
    }

    let validateName = function(value, $form){

        if(!value) {
            alert(__filemanagerMessages.notEmpty);
        } else {

            if(isValueValid(value)) {
                $form.off('submit');
                $form.trigger('submit');
            } else {
                alert(__filemanagerMessages.illegalCharacters);
            }
        }
    }

    let newFolder = function(){
        let $newNameFolder = $('.new-folder-box input[type="text"]');
        let $newFolderForm = $('.new-folder-box form');

        $('.top-bar .new-folder, .empty-content .new-folder').on('click', function () {
            $('.new-folder-box, .overlay').show();
            $newNameFolder.trigger('focus');
        })

        $newFolderForm.on('submit',function(e) {
            e.preventDefault();
            validateName($newNameFolder.val(), $newFolderForm);
        });

        $('.new-folder-box .fa-times').on('click', function () {
            $('.new-folder-box, .overlay').hide();
        })
    }

    let actionOnRow = function () {

        let $row = $('.filemanager .row.file, .filemanager .row.folder');
        let $blocks = $('.edit-box, .overlay');

        $row.on('mouseover', function () {
            $(this).find('.action').show();
        });

        $row.on('mouseout', function () {
            $(this).find('.action').hide();
        });

        $('.filemanager .action i.edit').on('click', function (e) {
            e.preventDefault();

            let $name = $(this).parents('.row').find('.filename .name');

            $blocks.show();

            let name = $name.attr('data-name');
            let ext = $name.attr('data-ext');
            $('.edit-box input[name="editName"], .edit-box input[name="oldEditName"]').val(name);
            $('.edit-box input[name="ext"]').val(ext);
        })

        let $editForm = $('.edit-box form');
        $editForm.on('submit',function(e) {
            e.preventDefault();
            validateName($('.edit-box input[type="text"]').val(), $editForm);
        });

        $('.edit-box .fa-times').on('click', function () {
            $blocks.hide();
        })

        $('.filemanager .action i.remove').on('click', function (e) {
            e.preventDefault();

            let $name = $(this).parents('.row').find('.filename .name');

            let result = confirm(__filemanagerMessages.sure);
            if (result) {
                let name = $name.attr('data-name');
                let ext = $name.attr('data-ext');

                name = (ext ? name + '.' + ext : name);

                sendPost($('.filemanager').attr('data-path'), {'removeName': name});

            }
        })

    };

    let dropzone = function () {

        $('.top-bar .upload, .empty-content .upload').on('click', function () {
            $('.dropzone, .overlay').show();
        })

        $('.dropzone .fa-times').on('click', function () {
            $('.dropzone, .overlay').hide();
        })

    }

    scope.filemanager = function () {
        dropzone();
        newFolder();
        actionOnRow();
    };

    scope.insert = function () {

        $('.filemanager.insertFile .row.file .choose-file').on('click',function(e) {
            e.preventDefault();
            let dataFile = JSON.parse($(this).parents('.row').attr('data-file'));
            $(document).trigger('filemanagerInsertFile', [$(this), dataFile]);
        });

        $('.filemanager.insertFolder .row.folder .choose-folder').on('click',function(e) {
            e.preventDefault();
            let dataFolder = JSON.parse($(this).parents('.row').attr('data-folder'));
            $(document).trigger('filemanagerInsertFolder', [$(this), dataFolder]);
        });

    };

    return scope;

}(filemanager.main || {}));

Dropzone.autoDiscover = false;
$(function() {

    filemanager.main.filemanager();
    filemanager.main.insert();

    let $dropzoneBox = $('#dropzoneBox');

    if($dropzoneBox.length > 0) {
        let dropzone = new Dropzone('#dropzoneBox');
        dropzone.on('queuecomplete', function () {
            location.reload();
        });
    }

});