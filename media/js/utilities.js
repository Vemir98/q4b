
(function($) {
    var _current_lang = $(document).find('html').attr('lang') ? $(document).find('html').attr('lang') : "en";

    var empty_select ={
        en : 'Please select',
        he : 'בבקשה תבחר'
    }
    var empty_input ={
        en : 'Empty field',
        he : 'שדה ריק'
    }
    var incorrect_email ={
        en : 'Incorrect Email',
        he : 'דוא"ל שגוי'
    }
    var incorrect_url ={
        en : 'URL is not valid',
        he : 'כתובת האתר אינה חוקית'
    }
    var password_match ={
        en : 'Passwords do not match',
        he : 'סיסמאות לא תואמות'
    }
    var short_password ={
        en : 'Password should be minimum 8 characters',
        he : 'סיסמה צריכה לכלול תווים לפחות 8'
    }

    var methods =
    {
        //функция вызывается при поиске по фильтру на странице calendar и обновляет текущий календарь на заданный месяц

        measureHiddenTable: function(selector,add) {
            var option = add!=undefined && add ? true:false;
            var clone = $(selector).clone();
            clone.css("visibility","hidden");
            $('body').append(clone);
            var widthT = clone.innerWidth();
            clone.remove();
            var ua = navigator.userAgent.toLowerCase();
            if(widthT > 800){

                if (ua.indexOf('safari') != -1) {
                    if (ua.indexOf('chrome') > -1 && option) {
                        return widthT-800;
                    }
                    else if(ua.indexOf('chrome') > -1 && !option){
                        return widthT-320;
                    }
                    else {
                        if(window.fullScreen || window.innerWidth == screen.width) {
                            return widthT + 60;
                        } else {
                            return widthT + 60;
                        }

                    }

                } else {
                    return widthT-800;
                }
            }
            else{
                return '100%'
            }

        },


        validateForm:function (form){

            var is_valid = true;
            var errorText = '';

            form.find('.q4_required').each(function() {
                var element = $(this);
                if (element.val().length == 0) {
                    element.addClass('error');
                    errorText = empty_input[_current_lang];
                    methods.addErrorMessages(element, errorText);
                    is_valid = false;


                } else {
                    element.removeClass('error');
                    methods.removeErrorMessages(element);
                }

            });

            form.find('input.q4_email').each(function() {
                var email_fo = $(this);
                if (email_fo.val() == '') {
                    email_fo.addClass('error');
                    errorText = empty_input[_current_lang];
                    methods.addErrorMessages(email_fo, errorText);
                    is_valid = false;
                } else {
                    email_fo.removeClass('error');
                    methods.removeErrorMessages(email_fo);
                }

            });

            form.find('select.q4_select').each(function() {
                var element = $(this);
                if (!element.val()) {
                    element.addClass('error');
                    errorText = empty_select[_current_lang];
                    methods.addErrorMessages(element, errorText)
                    is_valid = false;
                } else {
                    element.removeClass('error');
                    methods.removeErrorMessages(element);
                }
            });

            form.find('input.q4_url').each(function () {
                var form_url = $(this);

                if (form_url.val() != '' && !url_rgx.test(form_url.val())) {
                    form_url.addClass('error');
                    errorText = incorrect_url[_current_lang];
                    methods.addErrorMessages(form_url, errorText);
                    is_valid = false;
                } else {
                    form_url.removeClass('error');
                    methods.removeErrorMessages(form_url);
                }
            });
            var result = {};
            result.valid = is_valid;
            result.error = errorText;

            return result;

        },

        addErrorMessages:function(element, message){

            if(element.closest('.form-group').length == 1){
                element.closest('.form-group').append('<div class="q4_error_message">' +
                    message +
                '</div>');
            }
            if(element.closest('td').length == 1){
                element.closest('td').append('<div class="q4_error_message">' +
                    message +
                '</div>');
            }
            if(element.siblings('ul').length==1){
                element.parent().append('<div class="q4_error_message">' +
                message +
                '</div>');
            }
        },


        /**
         * Remove error messages from form fields
         **/
        removeErrorMessages:function(element){

            if(element.closest('.form-group').length == 1){
                element.closest('.form-group').find('.q4_error_message').remove('.q4_error_message');
            }
            if(element.closest('td').length == 1){
                element.closest('td').find('.q4_error_message').remove('.q4_error_message');
            }
            if(element.siblings('ul').length==1){
                element.parent().find('.q4_error_message').remove('.q4_error_message');;
            }
        },





        setScrollBarWidth: function(selector, windowWidth) {

            var sidebarWidth = $('.sidebar').is(":visible") ? 315 : 90;
            var tabWidthOnLoad = windowWidth - sidebarWidth;

            selector.width(tabWidthOnLoad);

        },
        in_array:function (value, array) {
            for(var i=0; i<array.length; i++){
                if(value == array[i]) return true;
            }
            return false;
        },
        checkCurrentProffesion: function(proffessionId,checked){
            var selected = $.map(CHECKED_PLANS, function(n, i) { return n; })

            setTimeout(function(){
                if(checked){
                    $(document).find('.enable-plan-action').find("input[type=checkbox]").closest('td').addClass('disabled-input');
                    $(document).find('.plans-list-layout [data-professionid='+proffessionId+']').each(function(i,el){
                        var self = $(el);
                        var planId = self.data('planid');

                        self.closest('tr').find('input[type=checkbox]').closest('td').removeClass('disabled-input')


                        if(planId!=undefined && methods.in_array(planId,selected)){

                            self.closest('tr').find('input[type=checkbox]').prop('checked',true)
                        }

                    })
                }else{
                    $(document).find('.enable-plan-action').find("input[type=checkbox]").closest('td').removeClass('disabled-input');
                }
            }, 200);
        },
        getObjectLength : function (object) {
           return $.map(object, function(n, i) { return i; }).length;
        },

        setCarouselDirection: function (selector, margin,number) {
            var currentPosition = $(selector).find('.q4-owl-nav .owl-start-number').text()
            var startPosition = currentPosition ? currentPosition-1 : 0;
            startPosition = number ? number : startPosition;
            $(selector).trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
            $(selector).find('.owl-stage-outer').children().unwrap();
            $(selector).find('.owl-stage-outer').remove();
            $(selector).find('.owl-stage').remove();
            $(selector).each(function(){
                self = $(this);
                self.data('structurecount',self.find('.item:visible').length)
            })
            if ($(document).find('html').hasClass('rtl')) {

                $(document).find(selector).owlCarousel({
                    margin: margin,
                    rtl: true,
                    items: 1,
                    dots: false,
                    touchDrag: true,
                    startPosition:startPosition,
                    stagePadding: 0,
                    nav: true,
                    navText: ['<i class="q4bikon-arrow_right"></i>', '<i class="q4bikon-arrow_left"></i>'],
                });

            } else {
                $(document).find(selector).owlCarousel({
                    margin: margin,
                    items: 1,
                    dots: false,
                    touchDrag: true,
                    startPosition:startPosition,
                    stagePadding: 0,
                    nav: true,
                    navText: ['<i class="q4bikon-arrow_left"></i>', '<i class="q4bikon-arrow_right"></i>'],
                });

            }

        },

        setCarouselWidth: function(selector, winWidth) {

            winWidth = winWidth != undefined ? winWidth: '';
            var sidebarWidth = $('.sidebar').is(":visible") ? 335 : 90;
            var carouselWrapWidth = winWidth - sidebarWidth;
            $(document).find(selector).width(carouselWrapWidth);

        },
        setModalCarouselWidth: function(selector, modalWidth) {


            modalWidth = modalWidth != undefined ? modalWidth: '';
            var sidebarWidth = 60;
            var carouselWrapWidth = modalWidth - sidebarWidth;
            $(document).find(selector).width(carouselWrapWidth);

        },

        setCarouselTableWrap: function() {

            var carouselTabWidth = $(window).innerWidth();

            var carouselWrapWidth = carouselTabWidth - 90;

            $(document).find('.q4-carousel-table-wrap').width(carouselWrapWidth);
        },

        owlPagination: function(selector,number){
            var selected = number ? number : 1;
            $(document).find(selector).each(function (i, el) {
                var self  =  $(el);
                var start  =  '<span class="owl-start-number">' + selected + '</span>';
                var length = '<span class="owl-length-number">' + self.data('structurecount') + '</span>';
                self.find('.owl-controls .q4-owl-nav').remove();
                self.find('.owl-controls').append('<div class="q4-owl-nav">' + start + '<span class="q4-owl-nav-divider">/</span>' + length + '</div>');


            });

        },

        imageToBase64: function(selector,file){

            var dataSrc = 'new';
            var reader = new FileReader();
            reader.onload = function() {
                dataSrc = reader.result;
                $(document).find('.call-lit-plugin.'+selector).data('url',dataSrc);
            }
            reader.readAsDataURL(file);
        },

        pinchHandler: function(element){
            // TO DO
            var canvas = $(document).find('canvas')[0];

            $(canvas).attr('id','zoom-handler-canvas')
            var selector = document.getElementById('zoom-handler-canvas');
            selector.addEventListener('gestureend', function(e) {
                if (e.scale < 1.0) {
                    element.zoom(0.5)
                } else if (e.scale > 1.0) {
                    element.zoom(1.5)
                }
            }, false);
        },

        updateContentOnChange: function(){

            methods.setCarouselDirection(".q4-owl-carousel", 10);
            methods.setCarouselDirection(".q4-carousel-table", 10);
            methods.owlPagination('.q4-carousel-table');
            methods.owlPagination('.q4-owl-carousel');
        },
        updateContentOnChangeNew: function(modalWidth){

            var windowWidth = window.innerWidth;
            var width = modalWidth != undefined ? modalWidth : windowWidth;
            methods.setCarouselWidth('.q4-carousel-table-wrap', windowWidth);
            methods.setModalCarouselWidth('.q4-wrap-mobile', width);
            methods.setModalCarouselWidth('.q4-carousel-table-wrap', width);
            methods.setCarouselWidth('.q4-wrap-mobile', windowWidth);
            methods.setCarouselDirection(".q4-list-items-mobile", 10);
            methods.setScrollBarWidth($(document).find('.tab_panel').find('.panel_content.open .scrollable-table'), windowWidth);
            var widthT = methods.measureHiddenTable($(document).find('.tab_panel').find('.panel_content.open table.table'),true);
            $(document).find('.panel_content.open .tab_panel').find('.mCSB_container').width(widthT);
            setTimeout(function() {
                methods.setCarouselDirection(".tasks-full-description-mobile", 10);
                methods.setCarouselDirection(".q4-owl-carousel", 10);
                methods.updateContentOnChange();
            }, 500);
        },
        updateCurrentOnChange: function(selector){

            methods.setCarouselDirection(selector, 10);
            methods.owlPagination(selector);
            methods.setScrollbarDirection('.scrollable-table');
        },
        utf8_encode: function (str_data) { // Encodes an ISO-8859-1 string to UTF-8

            str_data = str_data.replace(/\r\n/g,"\n");
            var utftext = "";

            for (var n = 0; n < str_data.length; n++) {
                var c = str_data.charCodeAt(n);
                if (c < 128) {
                    utftext += String.fromCharCode(c);
                } else if((c > 127) && (c < 2048)) {
                    utftext += String.fromCharCode((c >> 6) | 192);
                    utftext += String.fromCharCode((c & 63) | 128);
                } else {
                    utftext += String.fromCharCode((c >> 12) | 224);
                    utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
            }

            return utftext;
        },


        base64_decode: function(data) { // Decodes data encoded with MIME base64

            var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
            var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
                enc = '';

            do { // unpack four hexets into three octets using index points in b64
                h1 = b64.indexOf(data.charAt(i++));
                h2 = b64.indexOf(data.charAt(i++));
                h3 = b64.indexOf(data.charAt(i++));
                h4 = b64.indexOf(data.charAt(i++));

                bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;

                o1 = bits >> 16 & 0xff;
                o2 = bits >> 8 & 0xff;
                o3 = bits & 0xff;

                if (h3 == 64) enc += String.fromCharCode(o1);
                else if (h4 == 64) enc += String.fromCharCode(o1, o2);
                else enc += String.fromCharCode(o1, o2, o3);
            } while (i < data.length);

            return enc;
        },

        base64_encode: function ( data ) {    // Encodes data with MIME base64
            //
            // +   original by: Tyler Akins (http://rumkin.com)
            // +   improved by: Bayron Guevara

            var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
            var o1, o2, o3, h1, h2, h3, h4, bits, i=0, enc='';

            do { // pack three octets into four hexets
                o1 = data.charCodeAt(i++);
                o2 = data.charCodeAt(i++);
                o3 = data.charCodeAt(i++);

                bits = o1<<16 | o2<<8 | o3;

                h1 = bits>>18 & 0x3f;
                h2 = bits>>12 & 0x3f;
                h3 = bits>>6 & 0x3f;
                h4 = bits & 0x3f;

                // use hexets to index into b64, and append result to encoded string
                enc += b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
            } while (i < data.length);

            switch( data.length % 3 ){
                case 1:
                    enc = enc.slice(0, -2) + '==';
                break;
                case 2:
                    enc = enc.slice(0, -1) + '=';
                break;
            }

            return enc;
        },


        setImageLink: function (input, element) {

            /****************************************
             * style input type file
             ****************************************/

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                $(element).closest('.hide-upload').siblings(".camera-bg").hide();
                $(element).closest('.hide-upload').siblings(".preview-user-image").removeClass('hidden');

                reader.onload = function(e) {
                    $(element).closest('.hide-upload').siblings(".preview-user-image").attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);

            }

            if ($(element).closest('.upload-logo').hasClass('error')) {
                $(element).removeClass('error');
                $(element).closest('.upload-logo').removeClass('error');
            }


        },

        modalLoadImages: function (input, e, index) {
            var modalId = $(input).closest('.modal').attr('id');
            var inputId = $(input).data('id');
            if (input.files && input.files[0]) {
                var lengthFIleInput = $('#' + modalId).find('.load-images-input[type=file]').length;
                var standardFiles = $('#' + modalId).find('.load-images-input[type=file]')[lengthFIleInput - 1].files;
                var tmpPath = '';
                // get current date
                var tasksToday = new Date();
                var tasksDay = tasksToday.getDate();
                var tasksMonth = tasksToday.getMonth() + 1; //January is 0!
                var tasksYear = tasksToday.getFullYear();
                if (tasksDay < 10) {
                    tasksDay = '0' + tasksDay
                }
                if (tasksMonth < 10) {
                    tasksMonth = '0' + tasksMonth
                }
                tasksToday = tasksMonth + '.' + tasksDay + '.' + tasksYear;
                var tasksImageNumber = 0;
                var imagesImageNumber = 0;
                tasksImageNumber = $('#' + modalId).find('.modal-images-list-table table').find('tr').length;
                imagesImageNumber = $('#' + modalId).find('.qc-image-list-mobile .item').length;
                $('.qc-image-list-mobile').trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
                $('.qc-image-list-mobile').find('.owl-stage-outer').children().unwrap();
                $('.qc-image-list-mobile').find('.owl-stage').remove();
                for (var i = 0; i < standardFiles.length; i++) {
                    tmpPath = URL.createObjectURL(standardFiles[i]);
                    var file_ext = standardFiles[i].type.split('/')[1].toLowerCase();
                    if ($.inArray(file_ext, ['png', 'jpg', 'jpeg', 'pdf']) == -1) {
                        alert('invalid extension!');
                    } else {
                        //TO DO добавить функционал для планов (plugin)
                        var classPlugin = modalId.indexOf('plan') !=-1 ? '' : 'call-lit-plugin';
                        var selector = "id" + Math.random().toString(9).replace('.', '');
                        $.fn.utilities('imageToBase64', selector, standardFiles[i]);
                        $(input).closest('.modal').find('.modal-images-list-table table tbody')
                            .prepend('<tr class="dynamically-appended">' +
                                       '<td>' +
                                            '<a data-url="' +'" title="' + standardFiles[i].name + '" data-controller="add_quality_control_image_from_raw_data" data-index="'+i+'" data-inputid="'+ inputId +'" class="' + selector + ' '+classPlugin + '" >' +
                                                '<span class="modal-tasks-image-number">' + (++tasksImageNumber) + '.</span>'+
                                                '<span class="modal-tasks-image-name"> ' + standardFiles[i].name + '</span>' +
                                                '<span class="modal-img-upload-date">(' + __('uploaded') +':' + tasksToday + ' )</span>' +
                                            '</a>' +
                                       '</td>' +
                                        '<td class="modal-tasks-image-option">' +
                                            '<a href="' + tmpPath + '" class="download_file" download="' + standardFiles[i].name + '">' +
                                                '<i class="q4bikon-download"></i>' +
                                            '</a>' +
                                       '</td>' +
                                       '<td class="modal-tasks-image-option">' +
                                            '<span>' +
                                                '<a href="#" class="delete-image-row delete_row" data-inputid="'+ inputId +'" data-index="'+i+'" ><i class="q4bikon-delete"></i></a>' +
                                            '</span>' +
                                       '</td>' +
                                '</tr>');
                        $(input).closest('.modal').find('.qc-image-list-mobile')
                            .prepend('<div class="item qc-image-list-mobile-item">' +
                                    '<a data-url="'+'" title="' + standardFiles[i].name + '" data-controller="add_quality_control_image_from_raw_data" data-index="'+i+'" data-inputid="'+ inputId +'" class="' + selector +' '+classPlugin + '" >' +
                                        '<span class="modal-tasks-image-number">' + (++imagesImageNumber) + '.</span>' +
                                        '<span class="modal-tasks-image-name"> ' + standardFiles[i].name +'</span>'  +
                                        '<span class="modal-img-upload-date">(' + __('uploaded') +':' + tasksToday + ' )</span>' +
                                    '</a>' +
                                    '<div class="qc-image-list-mobile-item-options">' +
                                        '<span class="circle-sm red delete-image-row" data-index="'+i+'" data-inputid="'+ inputId +'">' +
                                            '<i class="q4bikon-delete"></i>' +
                                        '</span>' +
                                    '</div>' +
                                '</div>');
                        var elementCount = $(input).closest('.modal').find('.qc-image-list-mobile').closest('.q4-owl-carousel')
                        var length = elementCount.data('structurecount')
                        elementCount.data('structurecount', parseFloat(length) + 1)
                    }
                }
                $('#' + modalId).find('.modal-images-list-table table').find('tr').each(function(i,el){
                    var self = $(el);
                    self.find('.modal-tasks-image-number').text(i + 1 + '.');
                })
                $('#' + modalId).find('.qc-image-list-mobile .item').each(function(i,el){
                    var self = $(el);
                    self.find('.modal-tasks-image-number').text(i + 1 + '.');
                })
                $.fn.utilities('setCarouselDirection', ".qc-image-list-mobile", 10);
                $.fn.utilities('owlPagination', '.q4-owl-carousel');
                var index = $('#' + modalId).find('.load-images-input').length;
                var newInputId = "id" + Math.random().toString(9).replace('.', '');
                var newFileInput = '<input type="file" multiple data-id="'+newInputId+'" data-in="' + ++index + '" class="load-images-input" name="images[]">';
                $('.hide-upload').append(newFileInput);

                var countFiles = $(input).closest('.modal').find('.modal-images-list-table table tbody tr').length;
                $(input).closest('.modal').find('.count-fl-list').text(countFiles);


                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
            }
        },
        modalLoadPlans: function (input, e,buffer) {
            var modalId = $(input).closest('.modal').attr('id');
            var inputId = $(input).data('id');
            if (input.files && input.files[0]) {
                var lengthFIleInput = $('#' + modalId).find('.load-images-input[type=file]').length;
                var standardFiles = $('#' + modalId).find('.load-images-input[type=file]')[lengthFIleInput - 1].files;
                var tmpPath = '';
                // get current date
                var tasksToday = new Date();
                var tasksDay = tasksToday.getDate();
                var tasksMonth = tasksToday.getMonth() + 1; //January is 0!
                var tasksYear = tasksToday.getFullYear();
                if (tasksDay < 10) {
                    tasksDay = '0' + tasksDay
                }
                if (tasksMonth < 10) {
                    tasksMonth = '0' + tasksMonth
                }
                tasksToday = tasksMonth + '.' + tasksDay + '.' + tasksYear;
                var tasksImageNumber = 0;
                var imagesImageNumber = 0;
                tasksImageNumber = $('#' + modalId).find('.upload-plans-scroll ul').find('li').length;

                for (var i = 0; i < standardFiles.length; i++) {
                    var tooLarge = standardFiles[i].size >= MAX_FILE_SIZE*1024*1024 ? '<span class="too-large-file">'+ __('Too large to upload') +'</span>' : '';
                    var fileClass = tooLarge ? ' too-large-file-name' : 'plans-file-name';
                    var bufferId = "id" + Math.random().toString(9).replace('.', '');
                    if(!tooLarge)
                        buffer[bufferId] = standardFiles[i];
                    var file_ext = standardFiles[i].type.split('/')[1].toLowerCase();
                    var src,alt = '';
                    var extArray =  [
                        'png',
                        'jpg',
                        'jpeg',
                        'pdf',
                        'doc',
                        'xls',
                        'vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'vnd.openxmlformats-officedocument.wordprocessingml.document'];
                    var filesArray =  {
                        'png':'png',
                        'jpg':'jpg',
                        'jpeg':'jpg',
                        'pdf':'pdf',
                        'doc':'doc',
                        'xls':'xls',
                        'vnd.openxmlformats-officedocument.spreadsheetml.sheet':'excel',
                        'vnd.openxmlformats-officedocument.wordprocessingml.document':'doc'};

                    if ($.inArray(file_ext,  extArray) == -1) {
                        src = "/media/img/choose-format/format-unknown.png";
                        alt = "unknown.png";
                    }else{
                        src = '/media/img/choose-format/format-'+filesArray[file_ext]+'.png';
                        alt = filesArray[file_ext]+'.png';
                    }

                        //TO DO добавить функционал для планов (plugin)
                    var selector = "id" + Math.random().toString(9).replace('.', '');
                    $.fn.utilities('imageToBase64', selector, standardFiles[i]);
                    $(input).closest('.modal').find('.upload-plans-scroll ul')
                        .prepend('<li class="'+bufferId+'">'+
                                    '<span class="plans-file-ext">'+
                                        '<img src='+src+' alt="'+alt+' file">'+
                                    '</span>'+
                                    '<span class="' + fileClass + '"> '+standardFiles[i].name+'</span>'+
                                    tooLarge +
                                '</li>');

                }
                var index = $('#' + modalId).find('.load-images-input').length;
                var newInputId = "id" + Math.random().toString(9).replace('.', '');
                var newFileInput = '<input type="file" multiple data-id="'+newInputId+'" data-in="' + ++index + '" class="load-images-input" name="images[]">';
                $('.hide-upload').append(newFileInput)
                var plansCount = $('#' + modalId).find('.upload-plans-scroll ul').find('li').length;
                $('.upload-plans-title').find('.q4-plans-count').html('('+plansCount+')')
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
            }
        },

        handleTabs: function (self) {

            self.parent().siblings().find('.panel_content').slideUp();

            var $headerIcon = self.find('i.panel_header_icon');
            var $siblingsLi = self.closest('li').siblings('li');

            $siblingsLi.find('i.panel_header_icon').removeClass('q4bikon-minus').addClass('q4bikon-plus');
            $siblingsLi.find('.panel_header').removeClass('open');

            if (self.hasClass('open')) {

                self.removeClass('open');
                $headerIcon.removeClass('q4bikon-minus').addClass('q4bikon-plus');
            } else {
                self.addClass('open');
                $headerIcon.removeClass('q4bikon-plus').addClass('q4bikon-minus');

                setFloor(self);

                if($(document).find('html').hasClass('rtl')){
                    $(document).find('body').removeAttr('style');
                }

            }

            self.siblings('.panel_content').slideToggle(300);
        },


    };

    $.fn.utilities = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist!');
            return false;
        }
    };

})(jQuery);

