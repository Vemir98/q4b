(function () {
    var element = null;
    var parent = null;
    var url;
    var format;
    var editorModalEl = null;
    var inputFieldDataId = null;
    var countInput = null;
    var modalId = null;
    var index = null;
    var imageName = null;
    var image;
    var planName;
    var planId;
    var editor;
    function init() {
        var config = {
            onLoad: (editor) => {
                editor.markup.appendHtml('<button id="close" class="editor-top-buttons editor-close-button">' + __('Close') + '</button>','TOPBAR_RIGHT',() => {
                    document.querySelector('#close').addEventListener('click',(e) => {
                        closePixpModal();
                    })
                })
                editor.markup.appendHtml('<button id="save" class="editor-top-buttons editor-save-button">' + __('Save') + '</button>','TOPBAR_RIGHT',() => {
                    document.querySelector('#save').addEventListener('click',(e) => {
                        saveImage();
                    })
                })
            }
        };
        Object.assign(config, window.EDITOR_CONFIG )
        editor = new PixelPerfect('#pixel-perfect-editor', config);
    }
    function validURL() {
        // var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        //     '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
        //     '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        //     '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
        //     '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
        //     '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
        // return !!pattern.test(url);
        return url && url.includes('/');
    }
    function openPixpModal(event){
        editor.resetCanvas(true);
        editor.setActiveTool('select')
        document.getElementsByTagName('body')[0].classList.add("hide_body_scroll");
        var updateUrl = parent.dataset.updateUrl;
        var modalById = document.getElementById(modalId);
        var addUrl = modalById && modalById.dataset.addImageUrl

        url = updateUrl ? updateUrl :  addUrl ? addUrl : null;

        var imageSrc = $(event.target).parent().data('url');
        editorModalEl.classList.remove('hidden')
        setTimeout( () => {
            format = imageSrc && imageSrc.indexOf(";base64") !== -1 ? imageSrc.substring("data:image/".length, imageSrc.indexOf(";base64")) : event.target.parentElement.dataset.ext && event.target.parentElement.dataset.ext.split('/')[1] ? event.target.parentElement.dataset.ext.split('/')[1] : 'jpeg';
            editor.loadBackgroundImageFromUrl(imageSrc ,function (error) {
                if(error){
                    closePixpModal();
                }
            })
        }, 150)
    }
    function closePixpModal(){
        document.getElementsByTagName('body')[0].classList.remove("hide_body_scroll");
        editorModalEl.classList.add('hidden')
        // init();
    }

    function sendToServer() {
        var p = ".hide-upload input[data-remove='" + inputFieldDataId + "_" + countInput + "']";
        var modalById = document.getElementById(modalId);
        var toRemoveElement = document.querySelector(p);
        if(toRemoveElement){
            toRemoveElement.remove();
        }
        var image = getImage();
        if(parent.dataset.controller !== 'update_quality_control_plan_image'){
            parent.dataset.url = image;
            //toDo js not working
            $(parent).data('url', image);
        }
        window.qfetchOld(window.location.origin + url, {
            method: 'POST',
            headers: {
                'Content-Type': 'text/plain;charset=UTF-8',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: {
                csrf: Q4U.getCsrfToken(),
                "x-form-secure-tkn": '',
                source: image,
                name: imageName,
            }
        }).then( (data) => {
            var content = data
            var controller = parent.dataset.controller
            if (content != undefined && content.errors == undefined) {
                if (controller.indexOf('plan') != -1) {
                    if(parent.dataset.controller !== 'update_quality_control_plan_image') {
                        var modalContent = content.images;
                        document.querySelector('.modal .modal-images-list-table').outerHTML = modalContent;
                        document.querySelector('.modal .qc-image-list-mobile').outerHTML = modalContent;
                        //toDo change to js
                        $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
                        $.fn.utilities('setCarouselDirection', ".q4-owl-carousel", 10);
                        $.fn.utilities('owlPagination', '.q4-owl-carousel');
                        //toDo change to js
                    }else {
                       addAddedPlanImage(content, modalById)
                    }
                } else if (controller == 'add_quality_control_image_from_raw_data') {
                    parent.dataset.url = content.filePath;
                    parent.dataset.controller = 'update_quality_control_image';
                    parent.dataset.fileid = content.id;
                    if(modalById && modalById.dataset.updateImageUrl){
                        parent.dataset.updateUrl = modalById.dataset.updateImageUrl + '/' + content.id;
                    }
                }
            }
            closePixpModal()
        }).catch( (error) => {
            closePixpModal()
        })

    }

    function saveImageToInput() {
        image = getImage();
        planName = parent.title;
        planId = parent.dataset.fileid;
        //toDo js not working
        $(parent).data('url', image);
        if (parent.dataset.controller === 'add_quality_control_image_from_raw_plan') {
            addRawPlanImage();
        }else {
            var p = ".hide-upload input[data-remove='" + inputFieldDataId + "_" + countInput + "']";
            var toRemoveElement = document.querySelector(p);
            if(toRemoveElement){
                toRemoveElement.remove();
            }
            var hiddenInput = '<input type="hidden" value="' + image + '" data-remove="' + inputFieldDataId + '_' + countInput + '" class="load-images-input" name="images_' + index + '_source">' + '<input type="hidden" value="' + imageName + '" data-remove="' + inputFieldDataId + '_' + countInput + '" class="load-images-input" name="images_' + index + '_name">';
            document.querySelector('#' + modalId + ' .hide-upload').insertAdjacentHTML('beforeend', hiddenInput);
        }
        closePixpModal();
    }

    function getImage() {
        format = format  === 'pdf' ? 'jpeg': format
        return editor.getCanvasDataAs(format)
    }

    function saveImage() {
        editor.tool('crop').cancel();
        var currentInput = document.querySelector('.hide-upload input[data-id="' + inputFieldDataId + '"]');
        if(currentInput){
            currentInput.remove()
        }
        if(validURL()){
            sendToServer()
        }else {
            saveImageToInput()
        }
    }

    editorModalEl = document.querySelector('#image-editor');

    if(editorModalEl !== null){
        document.addEventListener('click', function(e){
            if(e.target.parentElement && e.target.parentElement.classList.contains('call-lit-plugin')){
                if(e.preventDefault){
                    e.preventDefault();
                }else{
                    e.stopPropagation();
                }
                element  = e.target;
                parent = element.parentElement;
                inputFieldDataId = parent.dataset.inputid;
                countInput = parent.dataset.index;
                modalId = parent.closest('.modal') ? parent.closest('.modal').id : parent.closest('.labtest_edit_container') ? parent.closest('.labtest_edit_container').id : false;
                index = Q4U.timestamp();
                imageName = element.innerText ? element.innerText : Q4U.timestamp() + parent.getAttribute('title');
                openPixpModal(e);
            }
        });
    }

    function getPrependContent(planName,imageBase64){
        return '<tr class="plan-raw-tr">' +
            '<td>' +
            '<a data-url="' + imageBase64 + '" title="' + planName + '" data-controller="add_quality_control_image_from_raw_data"  class="call-lit-plugin">' +
            '<span class="modal-tasks-image-number"></span>' +
            '<span class="modal-tasks-image-name"> ' + planName + '</span>' +
            '<span class="modal-img-upload-date"></span>' +
            '</a>' +
            '</td>' +
            '<td class="modal-tasks-image-option">' +
            '<a class="download_file disabled-gray-button" download="' + planName + '">' +
            '<i class="q4bikon-download"></i>' +
            '</a>' +
            '</td>' +
            '<td class="modal-tasks-image-option">' +
            '<span>' +
            '<a href="#" class="delete-image-row delete_row disabled-gray-button"><i class="q4bikon-delete"></i></a>' +
            '</span>' +
            '</td>' +
            '</tr>';
    }

    function getPrependContentMobile(planName,imageBase64){
        return '<div class="item qc-image-list-mobile-item">' +
            '<a data-url="' + imageBase64 + '" title="' + planName + '" data-controller="add_quality_control_image_from_raw_data" class="call-lit-plugin">' +
            '<span class="modal-tasks-image-number"></span>' +
            '<span class="modal-tasks-image-name"> ' + planName +
            '</span>' +
            '<span class="modal-img-upload-date"></span>' +
            '</a>' +
            '<div class="qc-image-list-mobile-item-options">' +
            '<span class="circle-sm red delete-image-row disabled-gray-button">' +
            '<i class="q4bikon-delete"></i>' +
            '</span>' +
            '</div>' +
            '</div>';
    }

    function addRawPlanImage() {


        //toDo change to js
        $('.qc-image-list-mobile').trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
        $('.qc-image-list-mobile').find('.owl-stage-outer').children().unwrap();
        $('.qc-image-list-mobile').find('.owl-stage').remove();
        //toDo change to js

        if(document.querySelector('#' + modalId + ' .modal-images-list-table table tbody')){
            document.querySelector('#' + modalId + ' .modal-images-list-table table tbody').insertAdjacentHTML('beforeend', getPrependContent(planName, image));
        }
        if(document.querySelector('#' + modalId + ' .qc-image-list-mobile')){
            document.querySelector('#' + modalId + ' .qc-image-list-mobile').insertAdjacentHTML('beforeend', getPrependContentMobile(planName, image));
        }
        document.querySelector('#' + modalId + ' .hide-upload').insertAdjacentHTML('beforeend', '<input type="hidden" value="' + image + '" class="plan-raw-val" name="images_' + index + '_source">' + '<input type="hidden" value="' + planId + '" class="plan-raw-val" name="images_' + index + '_id">');

        $('#' + modalId + ' .modal-images-list-table table tr').each((i, el) => {
            el.querySelector('.modal-tasks-image-number').innerHTML = i + 1 + '.';
        });

        $('#' + modalId + ' .qc-image-list-mobile .item tr').each((i, el) => {
            el.querySelector('.modal-tasks-image-number').innerHTML = i + 1 + '.';
        });

        //toDo change to js
        $.fn.utilities('setCarouselDirection', ".qc-image-list-mobile", 10);
        $.fn.utilities('owlPagination', '.q4-owl-carousel');
        //toDo change to js
    }
    function addAddedPlanImage(content, modalById) {


        //toDo change to js
        $('.qc-image-list-mobile').trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
        $('.qc-image-list-mobile').find('.owl-stage-outer').children().unwrap();
        $('.qc-image-list-mobile').find('.owl-stage').remove();
        //toDo change to js
        var fullName = content.filePath;
        var fullNameArray = content.filePath.split('/');
        var name = fullNameArray[fullNameArray.length - 1];
        var ext = fullNameArray[fullNameArray.length - 1].split('.')[1] && fullNameArray[fullNameArray.length - 1].split('.')[1] !== 'jpg' ? fullNameArray[fullNameArray.length - 1].split('.')[1] : 'jpeg';
        var id = content.id
        var updateUrl;
        if(modalById && modalById.dataset.updateImageUrl){
            updateUrl = modalById.dataset.updateImageUrl + '/' + content.id;
        }

        if(document.querySelector('#' + modalId + ' .modal-images-list-table table tbody')){
            document.querySelector('#' + modalId + ' .modal-images-list-table table tbody').insertAdjacentHTML('beforeend', getAddedPlanContent(fullName, name, ext, id, updateUrl));
        }
        if(document.querySelector('#' + modalId + ' .qc-image-list-mobile')){
            document.querySelector('#' + modalId + ' .qc-image-list-mobile').insertAdjacentHTML('beforeend', getAddedPlanMobileContent(fullName, name, ext, id, updateUrl));
        }

        $('#' + modalId + ' .modal-images-list-table table tr').each((i, el) => {
            el.querySelector('.modal-tasks-image-number').innerHTML = i + 1 + '.';
        });

        $('#' + modalId + ' .qc-image-list-mobile .item tr').each((i, el) => {
            el.querySelector('.modal-tasks-image-number').innerHTML = i + 1 + '.';
        });

        //toDo change to js
        $.fn.utilities('setCarouselDirection', ".qc-image-list-mobile", 10);
        $.fn.utilities('owlPagination', '.q4-owl-carousel');
        //toDo change to js
    }
    function getAddedPlanContent(fullName, name, ext, id, updateUrl){
        return '<tr>' +
            '<td data-th="Image">' +
                '<span class="modal-tasks-image-action">' +
                    '<a data-url="' + fullName + '" data-controller="update_quality_control_image" data-ext="image/' + ext + '" data-fileid="' + id + '" title="' + name + '" class="call-lit-plugin" data-update-url="' + updateUrl + '">' +
                    '<span class="modal-tasks-image-number"></span>' +
                    '<span class="modal-tasks-image-name">' + name + '</span>' +
                    '<span class="modal-img-upload-date"></span></a>' +
                '</span>' +
            '</td>' +
            '<td data-th="Download" class="modal-tasks-image-option">' +
                '<span class="modal-tasks-image-action">' +
                    '<a href="/media/data/projects/52/quality_control/60c875e2bd61f.jpg" class="download_file disabled-gray-button" download="60c875e2bd61f.jpg" data-url="">' +
                    '<i class="q4bikon-download"></i>' +
                    '</a>' +
                '</span>' +
            '</td>' +
            '<td data-th="Delete" class="modal-tasks-image-option">' +
                '<span class="modal-tasks-image-action">' +
                '<span class="delete_row disabled-gray-button" ><i class="q4bikon-delete"></i></span>' +
                '</span>' +
            '</td>' +
        '</tr>';
    }
    function getAddedPlanMobileContent(fullName, name, ext, id, updateUrl){
        return '<div class="item qc-image-list-mobile-item">' +
            '<a data-url="' + fullName + '" title="' + name + '" data-ext="image/' + ext + '" data-controller="update_quality_control_image" class="call-lit-plugin" data-update-url="' + updateUrl + '">' +
            '<span class="modal-tasks-image-number"></span>' +
            '<span class="modal-tasks-image-name"> ' + name +
            '</span>' +
            '<span class="modal-img-upload-date"></span>' +
            '</a>' +
            '<div class="qc-image-list-mobile-item-options">' +
            '<span class="circle-sm red delete-image-row disabled-gray-button">' +
            '<i class="q4bikon-delete"></i>' +
            '</span>' +
            '</div>' +
            '</div>';
    }
    init();
})()