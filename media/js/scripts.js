/*! bootstrap-progressbar v0.9.0 | Copyright (c) 2012-2015 Stephan Groß | MIT license | http://www.minddust.com */ ! function(t) {
    "use strict";


    var e = function(n, s) {
        this.$element = t(n), this.options = t.extend({}, e.defaults, s)
    };
    e.defaults = {
        transition_delay: 300,
        refresh_speed: 50,
        display_text: "none",
        use_percentage: !0,
        percent_format: function(t) {
            return t + "%"
        },
        amount_format: function(t, e) {
            return t + " / " + e
        },
        update: t.noop,
        done: t.noop,
        fail: t.noop
    }, e.prototype.transition = function() {
        var n = this.$element,
            s = n.parent(),
            a = this.$back_text,
            r = this.$front_text,
            i = this.options,
            o = parseInt(n.attr("data-transitiongoal")),
            h = parseInt(n.attr("aria-valuemin")) || 0,
            d = parseInt(n.attr("aria-valuemax")) || 100,
            f = s.hasClass("vertical"),
            p = i.update && "function" == typeof i.update ? i.update : e.defaults.update,
            u = i.done && "function" == typeof i.done ? i.done : e.defaults.done,
            c = i.fail && "function" == typeof i.fail ? i.fail : e.defaults.fail;
        if (isNaN(o)) return void c("data-transitiongoal not set");
        var l = Math.round(100 * (o - h) / (d - h));
        if ("center" === i.display_text && !a && !r) {
            this.$back_text = a = t("<span>").addClass("progressbar-back-text").prependTo(s), this.$front_text = r = t("<span>").addClass("progressbar-front-text").prependTo(n);
            var g;
            f ? (g = s.css("height"), a.css({
                height: g,
                "line-height": g
            }), r.css({
                height: g,
                "line-height": g
            }), t(window).resize(function() {
                g = s.css("height"), a.css({
                    height: g,
                    "line-height": g
                }), r.css({
                    height: g,
                    "line-height": g
                })
            })) : (g = s.css("width"), r.css({
                width: g
            }), t(window).resize(function() {
                g = s.css("width"), r.css({
                    width: g
                })
            }))
        }
        setTimeout(function() {
            var t, e, c, g, _;
            f ? n.css("height", l + "%") : n.css("width", l + "%");
            var x = setInterval(function() {
                f ? (c = n.height(), g = s.height()) : (c = n.width(), g = s.width()), t = Math.round(100 * c / g), e = Math.round(h + c / g * (d - h)), t >= l && (t = l, e = o, u(n), clearInterval(x)), "none" !== i.display_text && (_ = i.use_percentage ? i.percent_format(t) : i.amount_format(e, d, h), "fill" === i.display_text ? n.text(_) : "center" === i.display_text && (a.text(_), r.text(_))), n.attr("aria-valuenow", e), p(t, n)
            }, i.refresh_speed)
        }, i.transition_delay)
    };
    var n = t.fn.progressbar;
    t.fn.progressbar = function(n) {
        return this.each(function() {
            var s = t(this),
                a = s.data("bs.progressbar"),
                r = "object" == typeof n && n;
            a && r && t.extend(a.options, r), a || s.data("bs.progressbar", a = new e(this, r)), a.transition()
        })
    }, t.fn.progressbar.Constructor = e, t.fn.progressbar.noConflict = function() {
        return t.fn.progressbar = n, this
    }
}(window.jQuery);
var FILES_BUFFER = [];//буффер файлов
var LOADER = true;//показать лоадер
var LANG = $(document).find('html').attr('lang') ? $(document).find('html').attr('lang') : 'en'
var Q4U = window.Q4U || {};
var MAX_FILE_SIZE = Math.min.apply(null, $(document).find('[name=fps]').attr('content').split('-'))
var AJAX_LOADS = false;
$(document).ready(function() {
    $('.tab-anchor').on('click', function() {
        if ($(this).hasClass('disabled')) return;
        if (($(this).siblings('.tab-content').css('display') == 'none') || $(this).siblings('.tab-content').hasClass('hidden')) {
            $('.tabs .tab .tab-content').slideUp();
            $(this).siblings('.tab-content').slideDown();
            $(this).siblings('.tab-content').removeClass('hidden')
        }
    });
    if (typeof SHOW_PROFILE != 'undefined' && SHOW_PROFILE && typeof INTERVAL == 'undefined') {
        $(document).find('.get-user-profile').trigger('click');
    }
});
$(document).ajaxStart(function() {
    if(LOADER!=undefined && LOADER)
        $(document).find('.loader_backdrop').show();
});
$(document).ajaxComplete(function() {
    $(document).find('.loader_backdrop').hide();
    AJAX_LOADS =  false;
});
$(document).on('click', 'form a.submit', function(e) {
    e.preventDefault();
    $(this).parents('form').submit();
});
function getFormData(form){
    var unindexed_array = form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}

$(document).on('click', 'form a.q4-form-submit', function(e) {
    e.preventDefault();
    var self = $(this);
    var urlPost = self.closest('form').attr('action');
    var currentForm = self.closest('form');

    var valid = $.fn.utilities('validateForm', self.closest('form'));
console.log('valid', valid);

    if(valid.valid){

        if(!$.isEmptyObject(FILES_BUFFER)){
            var formData = new FormData();
            var data = getFormData(currentForm);
            data["csrf"] = Q4U.getCsrfToken();
            formData.append("Data", JSON.stringify(data));

            for(var key in FILES_BUFFER){
                formData.append('images[]',FILES_BUFFER[key]);
            }
            $.ajax({
                url: urlPost,
                type: 'POST',
                data:formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    if(data.errors){
                        Q4U.alert(__(data.errors), {
                            type: "danger",
                            confirmText: __("OK")
                        });

                    }else if(data.struct){
                        $(document).find('.property-tab-content').html(data.struct);
                         normalizeObjectStruct();
                        var allFloors = $(document).find('.wrap-property-structure-list').data('floor');

                        var middleFloor = parseInt(allFloors/2);

                        $('.wrap-property-structure-list').jCarouselLite({
                            btnNext: ".next",
                            btnPrev: ".prev",
                            vertical: true,
                            visible: 1,
                            circular:false,
                            start: middleFloor
                        });
                        $('.wrap-property-structure-list').css('overflow', 'hidden');
                        if(allFloors < 2){
                            $('.wrap-property-structure-list')
                                .closest('.property-struct')
                                .find('.property-floors-arrow')
                                .addClass('disabled');
                        }
                        $.fn.utilities('setCarouselDirection', ".q4-owl-carousel", 0);
                        $.fn.utilities('owlPagination', '.q4-owl-carousel');
                        $('[data-toggle="table"]').bootstrapTable();
                        FILES_BUFFER = [];
                        $(document).find('.modal').modal('hide');
                        $('[data-toggle="table"]').bootstrapTable();
                    }
                }
            });

        }else{

             currentForm.submit();

        }
    }else{

       // Q4U.alert(valid.error, {
       //      type: "danger",
       //      confirmText: __("OK")
       //  });

    }


});
$(document).on('click', '.get-modal', function(e) {
    e.preventDefault();
    $(document).find('.modal').remove();
    $(document).find('.modal-backdrop').remove();
    var url = $(this).data('url');
    $.ajax({
        url: url,
        method: 'GET',
        type: 'HTML',
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
            var fmResp = Q4U.getAjaxResponse(response);
            $(document).find('.modal').remove();
            $(document).find('.modal-backdrop').remove();
            var modal = fmResp.getData().modal;
            $('body').append(modal);
            $(document).find('.modal').modal('show');
            normalizeObjectStruct();
        }
    });
});
Q4U.ajaxGetRequest = function(url, params) {
    var p = {
        url: url,
        data: {},
        successCallback: function(data) {},
        errorCallback: function(data) {},
        ajaxSuccessCallback: function(data) {},
        ajaxErrorCallback: function(event, jqxhr, settings, thrownError) {}
    };
    if (params != undefined) {
        $.extend(p, params);
    }
    $.ajax({
        url: url,
        method: 'GET',
        type: 'HTML',
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response != undefined) {
                var data = Q4U.getAjaxResponse(response);
                if (!data.hasErrors()) {
                    p.successCallback(data);
                } else {
                    p.errorCallback(data);
                }
                p.ajaxSuccessCallback(data);
            }
        },
        error: function(event, jqxhr, settings, thrownError) {
            p.ajaxErrorCallback(event, jqxhr, settings, thrownError);
        }
    });
};
/**
 * [confirm description] Custom confirm modal window
 */
Q4U.confirm = function(msg, params) {
    function clearModal() {
        $(document).find('.q4u-modal-cancel-' + p.id).unbind('click');
        $(document).find('.q4u-modal-confirm-' + p.id).unbind('click');
        $(document).find('#confirmation-modal-' + p.id).remove();
    }
    var p = {
        id: 'confirmation-modal-' + Q4U.timestamp(),
        msgClass: "blue",
        type: "normal",
        confirmText: __("Confirm"),
        cancelText: __("Cancel"),
        cancelURL: '#',
        confirmURL: '#',
        cancelDataURL: '',
        confirmDataURL: '',
        hilight: '',
        confirmCallback: function(el, params) {
            clearModal();
        },
        cancelCallback: function(el, params) {
            clearModal()
        },
        custom: {}
    };
    if (params != undefined) {
        $.extend(p, params);
    }
    if (p.hilight.length) {
        msg = msg.replace(new RegExp('(' + p.hilight.replace(new RegExp(/(\(|\))/,'g'),'\\$1') + ')', 'g'), '<span class="confirmation-object">"$1"</span>');
    }
    switch (p.type) {
        case "normal":
            p.msgClass = "blue";
            break;
        case "danger":
            p.msgClass = "red";
            break;
    }
    var cancelDURL = p.cancelDataURL.length ? 'data-url="' + p.cancelDataURL + '"' : '';
    var confirmDURL = p.confirmDataURL.length ? 'data-url="' + p.confirmDataURL + '"' : '';
    msg = '<p class="' + p.msgClass + '">' + msg + '</p>';
    var modal = '<div id="' + p.id + '" class="modal" data-backdrop="static" data-keyboard="false" role="dialog">' + '<div class="modal-dialog q4_project_modal confirmation-dialog">' + '<div class="modal-content">' + '<div class="confirmation-modal-header">' + '<button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>' + '<div class="clear"></div>' + '</div>' + '<div class="confirmation-modal-body text-center">%msg%</div>' + '<div class="confirmation-modal-footer">' + '<a href="' + p.cancelURL + '" ' + cancelDURL + ' class="btn btn-cancel q4u-modal-cancel-' + p.id + '" data-dismiss="modal">' + p.cancelText + '</a>' + '<a href="' + p.confirmURL + '" ' + confirmDURL + ' class="btn btn-confirm q4u-modal-confirm-' + p.id + ' ' + p.msgClass + '" data-dismiss="modal">' + p.confirmText + '</a>' + '</div>' + '</div>' + '</div>' + '</div>';
    modal = modal.replace(/%msg%/g, msg);
    /**
     * добавил чтобы показывало модальное окно(SERGEY)
     */
    setTimeout(function() {
        $('body').append(modal);
        $(document).find('#' + p.id).modal('show');
    }, 400)
    /**********************/
    $(document).on('click', '.q4u-modal-cancel-' + p.id, function(e) {
        e.preventDefault();
        clearModal();
        p.cancelCallback(this, p);
    });
    $(document).on('click', '.q4u-modal-confirm-' + p.id, function(e) {
        e.preventDefault();
        clearModal();
        p.confirmCallback(this, p);
    });
    $(document).find('#' + p.id).on('hidden.bs.modal', function(e) {
        clearModal();
    });
};
/**
 * [alert description] Custom alert modal window
 */
Q4U.alert = function(msg, params) {
    function clearModal() {
        $(document).find('#alert-modal-' + p.id).remove();
    }
    var p = {
        id: 'alert-modal-' + Q4U.timestamp(),
        msgClass: "blue",
        type: "normal",
        confirmText: __("Ok"),
        cancelURL: '#',
        confirmURL: '#',
        cancelDataURL: '',
        confirmDataURL: '',
        hilight: '',
        confirmCallback: function(el, params) {
            clearModal();
        },
        custom: {}
    };
    if (params != undefined) {
        $.extend(p, params);
    }
    if (p.hilight.length) {
        msg = msg.replace(new RegExp('(' + p.hilight.replace(new RegExp(/(\(|\))/,'g'),'\\$1') + ')', 'g'), '<span class="confirmation-object">"$1"</span>');
    }
    switch (p.type) {
        case "normal":
            p.msgClass = "blue";
            break;
        case "danger":
            p.msgClass = "red";
            break;
    }
    var confirmDURL = p.confirmDataURL.length ? 'data-url="' + p.confirmDataURL + '"' : '';
    msg = '<p class="' + p.msgClass + '">' + msg + '</p>';
    var modal = '<div id="' + p.id + '" class="modal" data-backdrop="static" data-keyboard="false" role="dialog">' + '<div class="modal-dialog q4_project_modal confirmation-dialog">' + '<div class="modal-content">' + '<div class="confirmation-modal-header">' + '<div class="clear"></div>' + '</div>' + '<div class="confirmation-modal-body text-center">%msg%</div>' + '<div class="confirmation-modal-footer">' + '<a href="' + p.confirmURL + '" ' + confirmDURL + ' class="btn btn-confirm q4u-modal-confirm-' + p.id + ' ' + p.msgClass + '" data-dismiss="modal">' + p.confirmText + '</a>' + '</div>' + '</div>' + '</div>' + '</div>';
    modal = modal.replace(/%msg%/g, msg);
    $('body').append(modal);
    $(document).on('click', '.q4u-modal-confirm-' + p.id, function(e) {
        e.preventDefault();
        clearModal();
        p.confirmCallback(this, p);
    });
    $(document).find('#' + p.id).modal('show');
    $(document).find('#' + p.id).on('hidden.bs.modal', function(e) {
        clearModal();
    });
};

var imageNumber = 0;

$(document).ready(function() {
    // $(document).find('.loader_backdrop').show();
    //$('html').css('direction', 'ltr');
    //$('body').attr('dir', 'rtl');



    $(document).find('.object-general-select').on('change', function(){

        var $this = $(this).val();

    });

    function formatDate(date) {
        var monthNames = [
            __("January"), __("February"), __("March"),
            __("April"), __("May"), __("June"), __("July"),
            __("August"), __("September"), __("October"),
            __("November"), __("December")
        ];
        var day = date.getDate();
        var monthIndex = date.getMonth();
        var year = date.getFullYear();
        // var slice = date.getHours() > 9 ? 5:5;
        var time = date.toLocaleTimeString("en", {
            hour12: false
        }).slice(0, 5) ;

        return day + ' ' + monthNames[monthIndex] + ', ' + year + ' ' + time;
    }
    $(document).find('.current-date').text(formatDate(new Date()))
    if ($(document).find('#licence-agreement-modal').length > 0) {
        $('#licence-agreement-modal').modal('show');
    }
    $(document).on('click', '#licence-agreement-modal .agree-with-terms', function() {

        $.ajax({
            url: '/user/agree_terms',
            method: 'POST',
            data: JSON.stringify({
                csrf: Q4U.getCsrfToken(),
                "x-form-secure-tkn": ''
            }),
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {

                if (typeof SHOW_PROFILE != 'undefined' && SHOW_PROFILE) {
                    $(document).find('.modal').modal('hide');
                    $(document).find('.get-user-profile').trigger('click');
                }
                if (typeof INTERVAL != 'undefined') {
                    $(document).find('.modal').modal('hide');
                    clearInterval(INTERVAL)

                }
            },
        });
    })
    $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
        if (jqxhr.status == '403') {
            Q4U.alert(__('Access Denied'), {
                type: "danger",
                confirmText: __("OK")
            });
        }

    });
    $(document).on('termsAgreed', function(e, data) {
        $('#licence-agreement-modal').modal('hide');
        $('#licence-agreement-modal-mobile').modal('hide');

        $(document).find('body').removeClass('modal-open');
        $('.modal-backdrop').remove()

    })
    $(document).on('qualityControlUpdated', function(e, data) {
        $('#quality-control-modal').modal('hide');
        $('#quality-control-modal-mobile').modal('hide');
        $('#choose-plan-modal').remove();
        $('#choose-plan-modal-mobile').remove();

        if($(document).find('.modal').length <1){
            $(document).find('body').removeClass('modal-open');
            $('.modal-backdrop').remove()
        }
    })
    //event when owl-carousel changed for number showing
    $(document).on('changed.owl.carousel', '.q4-owl-carousel, .q4-carousel-table', function(event) {
        var current = event.relatedTarget._current;
        $(this).find('.owl-controls').find('.owl-start-number').text(current + 1);
    });
    $(document).on('hidden.bs.modal', '.modal', function() {
        var i = 0;
        var alert = false;
        var modalId = $(this).attr('id')
        if (modalId != undefined) {
            alert = $(this).attr('id').indexOf("alert") != -1 ? true : ($(this).attr('id').indexOf("confirm") != -1 ? true : false);
        }


        if (alert) {
            $(this).remove();
            if($(document).find('.modal').length <1){
                $(document).find('body').removeClass('modal-open');
                $(document).find('.modal-backdrop').remove();
            }

        }
        $(document).find('.modal').each(function() {
            if ($(this).is(':visible')) {
                i++;
            }
        });
        if (!i || $('.modal').length < 1) {
            $('.modal').remove()
            $(document).find('body').removeClass('modal-open');
            if($(document).find('.modal').length <1){
                $(document).find('body').removeClass('modal-open');
                $(document).find('.modal-backdrop').remove();
            }
        } else {
            $(document).find('body').addClass('modal-open');
        }
    });
    $(document).on('shown.bs.modal', '.modal', function() {
        $(document).find('html').removeAttr('style');
        if ($(document).find('html').hasClass('rtl')) {
            $(document).find('body').removeAttr('style');
            $(document).find('body').css('padding-right', '17px');
        }
        var modalId = $(this).attr('id')
        if (modalId != undefined) {
            alert = $(this).attr('id').indexOf("alert") != -1 ? true : ($(this).attr('id').indexOf("confirm") != -1 ? true : false);
        }
        if (!$(this).hasClass('literally-canvas-modal') && !$(this).hasClass('quality-control-modal-mobile') && !alert) {

            $.fn.utilities('updateContentOnChange', $(this).find('.modal-dialog').width());
            // $.fn.utilities('tableScrollableContent');
        }
    });
    /******* Show/Hide users profile drop down menu ******/
    $('.header-profile-name').on('click', function() {
        $(this).closest('.header-profile-settings').find('.header-profile-drop-list').toggle();
    });
    $(document).on('click', '.profile-current-lang', function() {
        var self = $(this);
        self.closest('.profile-lang').find('.profile-lang-list').toggle();
    });
    $(document).on('click', '.delete-image-row', function() {
        var self = $(this);
        var currentModal = self.closest('.modal');
        var currentDataId = self.data('inputid');
        var currentIndex = self.data('index');

        var currentInput = $(document).find('input[data-id='+currentDataId+']');

        if(currentInput.length > 0){

            var filesArray = currentInput[0].files;
            $.each(filesArray, function(key, value) {

                if(key!=currentIndex){
                    FILES_BUFFER[currentDataId +'_' + key] = value;
                }
            });
            currentInput.remove()
        }
        else{
            delete FILES_BUFFER[currentDataId + '_' + currentIndex]
        }
        var row = self.closest('tr');
        var item = self.closest('.owl-item');
        if (self.attr('data-url') != undefined) {
                Q4U.ajaxGetRequest(self.data('url'), {
                    successCallback: function(data) {
                        row.remove();
                        item.remove();

                        var countFiles = currentModal.find('.modal-images-list-table table tbody tr').length;
                        currentModal.find('.count-fl-list').text(countFiles);

                        $.fn.utilities('updateCurrentOnChange','.qc-image-list-mobile');
                    }
                });
            } else {
                row.remove();
                item.remove();
                var countFiles = currentModal.find('.modal-images-list-table table tbody tr').length;
                currentModal.find('.count-fl-list').text(countFiles);

                $.fn.utilities('updateCurrentOnChange','.qc-image-list-mobile');

            }

        self.closest('tr').remove();

        var countFiles = currentModal.find('.modal-images-list-table table tbody tr').length; //modal-images-list-table
        currentModal.find('.count-fl-list').text(countFiles);

    });
    $(document).on('click', '.header-current-lang', function() {
        var isVisible = $(this).closest('.keep-langs').find('.header-lang-list').is(':visible');
        if (!isVisible) {
            $(this).closest('.keep-langs').find('.header-lang-list').show();
        } else {
            $(this).closest('.keep-langs').find('.header-lang-list').hide();
        }
    });
    $(document).on('click', function(event) {
        // define the class of clicked(targeted) element
        var clickTarget = $(event.target);
        var clickedClass = clickTarget.attr('class'); //
        var clickedId = clickTarget.attr('id'); //
        if (/\s/.test(clickedClass)) {
            // It has only spaces, or is empty
            var arrow_bottom = clickedClass.split(' ')[0];
        }

        // check if the class of clicked(targeted) element does not belong to
        // header-profile-name or it's descendants
        if (clickedClass != 'header-profile-name' && clickedClass != 'full_name' && clickedClass != 'f_name' && clickedClass != 'l_name' && arrow_bottom != 'q4bikon-arrow_bottom') {
            if ($(".header-profile-drop-list").length > 0) { // check if dropdown is open then hide it
                $(".header-profile-drop-list").hide();
            }
        }
        var langBox = ['header-current-lang', 'header-lang-list', 'q4_flag', 'q4bikon-arrow_bottom'];
        if (langBox.indexOf(clickedClass) == -1) {
            if ($(".header-lang-list").length > 0) { // check if dropdown is open then hide it
                $(".header-lang-list").hide();
            }
        }
        var selectBox = [
            'multi-select-box',
            'over-select',
            'select-imitation',
            'checkbox-wrapper-comma',
            'checkbox-wrapper-comma checked',
            'over-select-comma',
            'mCSB_dragger',
            'mCSB_dragger_bar',
            'mCSB_draggerRail',
            'checkbox-wrapper',
            'checkbox-replace',
            'checkbox-list-row',
            'checkbox-text',
            'checkbox-list-tick q4bikon-tick'
        ];

        if (clickedClass!= undefined && selectBox.indexOf(clickedClass.trim()) == -1) {

            if ($(".multi-select-box").find(" .checkbox-list").length > 0) { // check if dropdown is open then hide it
                $(".multi-select-box").find(".checkbox-list").hide();
                $(".multi-select-box.comma").find(".checkbox-list").hide();
            }
            if ($(".multi-select-box").find(" .checkbox-list-no-scroll").length > 0) { // check if dropdown is open then hide it
                $(".multi-select-box").find(".checkbox-list-no-scroll").addClass('hidden');
                $(".multi-select-box.comma").find(".checkbox-list-no-scroll").addClass('hidden');
            }
            if ($(".bottom-hidden-select").length > 0) { // check if dropdown is open then hide it
                $(".bottom-hidden-select").hide();
            }
            if ($(".multi-select-box.comma").find(".checkbox-list").length > 0) { // check if dropdown is open then hide it
                $(".multi-select-box.comma").find(".checkbox-list").hide();
                $(".multi-select-box").find(".checkbox-list").hide();
            }
            if ($(".multi-select-box.comma").find(".checkbox-list-no-scroll").length > 0) { // check if dropdown is open then hide it
                $(".multi-select-box.comma").find(".checkbox-list-no-scroll").addClass('hidden');
                $(".multi-select-box").find(".checkbox-list-no-scroll").addClass('hidden');
            }
        }
        if (clickedId != undefined) {
            $(document).find("div.over-select").not("#" + clickedId).closest(".multi-select-box").find(".checkbox-list").hide();
        }
        var classArray = clickedClass != undefined ? clickedClass.split(' ') : '';
        var textareaClicked = false;
        for (var k = 0; k < classArray.length; k++) {
            if (classArray[k] == "stretched-textarea") {
                textareaClicked = true;
            }
        }
        if (!textareaClicked) {
            $(document).find(".stretched-textarea").each(function(i, el) {
                if ($(el).hasClass('expanded')) {
                    $(el).removeClass('expanded');
                    $(el).addClass('collapsed');
                    $(el).animate({
                        height: '30px'
                    }, 200);
                }
            })
        }

        //**********************************************************
    });
    /************************************************************************
     ****************** jQuery Literally Canvas Plugin *****************
     ***********************************************************************/
    $(document).on('click', '.call-lit-plugin', function(e) {
        e.preventDefault();
        var self = $(this);
        var isCreate = self.closest('.modal').hasClass('create-modal') ? "create" : false;
        var inputDataId = self.data('inputid');
        var countInput = self.data('index')
        var type = self.closest('tr').hasClass('dynamically-appended') ? "dynamic" : false;
        var imageName = self.find('.modal-tasks-image-name').text();
        var planName = self.attr('title');
        var modalId = self.closest('.modal').attr('id');
        var qcId = $('#' + modalId).data('qcid') ? '/' + $('#' + modalId).data('qcid') : '';
        var ext = self.data('ext');
        var fileId = self.data('fileid') ? '/' + self.data('fileid') : '';
        var controller = self.data('controller')
        var currentClass = self.attr('class').split(' ')[0];
        var planId = self.data('fileid');
        currentClass = (currentClass != 'call-lit-plugin') ? currentClass :'';
        $(document).find('.literally-canvas-modal').remove();
        var imageSrc = self.data('url');
        console.log("imageSrc", imageSrc);

        var setModalHeight = $(window).height()*0.70;

        var modalLiterallyCanvas =
            '<div id="literally-canvas-modal" data-backdrop="static" data-keyboard="false" class="modal no-delete fade literally-canvas-modal" role="dialog">' +
                '<div id="sketch-image-dialog" class="modal-dialog q4_project_modal literally-canvas-dialog">' +
                    '<div class="modal-content">' +
                        '<div class="modal-header q4_modal_header">' +
                            '<div class="q4_modal_header-top">' +
                                '<button type="button" class="close q4-close-modal" data-dismiss="modal">' +
                                    '<i class="q4bikon-close"></i>' +
                                '</button>' +
                                '<div class="clear"></div>' +
                            '</div>' +
                            '<div class="q4_modal_sub_header">' +
                                '<h3>'+ __('Edit image') + '</h3>' +
                            '</div>' +
                        '</div>' +
                        '<div class="modal-body sketchpad-modal-body" style="height: ' + setModalHeight + 'px; ">' +
                            '<div class="wrap-literally-canvas"></div>' +
                        '</div>' +
                        '<div class="modal-loader" style="height:' + setModalHeight + '">' +
                            '<div class="loader" ></div>'+
                        '</div>' +
                        '<div class="modal-footer text-align">' +
                            '<a href="#" class="btn btn-primary save-sketch export-canvas-button" data-ext="' + ext + '" data-url="/projects/' + controller +  qcId +  fileId + '">' + __('Save') + '</a>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
        var backgroundImage = new Image();
        backgroundImage.src = imageSrc.indexOf('base64') != -1 ? imageSrc : imageSrc + '?' + Q4U.timestamp();
        backgroundImage.addEventListener('load', loadImage, false);

        $(document).find('body').append(modalLiterallyCanvas);

        $(document).find('.literally-canvas-modal').modal('show');

        function loadImage(){

        setTimeout(function(){
            var currentImageWidth = backgroundImage.width;
            console.log("backgroundImage.width", backgroundImage.width);
            var currentImageHeight = backgroundImage.height;
            console.log("backgroundImage.height", backgroundImage.height);
            var imageSize = {width:currentImageWidth,height:currentImageHeight};
            var currentModalWidth = $(document).find('.literally-canvas-modal .modal-dialog').width()-63;
            var hRatio = currentModalWidth/currentImageWidth;
            var vRatio = setModalHeight/currentImageHeight;

            var ratio = Math.min(hRatio,vRatio);

            $('#literally-canvas-modal').find('.modal-loader').hide();
            $('#literally-canvas-modal').find('.modal-body').css('height','auto').removeClass("hidden");
            var lc = LC.init(document.getElementsByClassName('wrap-literally-canvas')[0], {
                imageURLPrefix: '/media/img/literallycanvasimg',
                imageSize: imageSize,
                backgroundShapes: [
                    LC.createShape('Image', {
                        x: 0,
                        y: 0,
                        image: backgroundImage,
                        scale:1.0,
                        zoomStep:0.1,

                    })
                ],

                defaultStrokeWidth:10,
                secondaryColor: 'transparent',
                primaryColor: '#ff0000',
                tools: [
                    LC.tools.Pan,
                    LC.tools.Pencil,
                    LC.tools.Eraser,
                    LC.tools.Line,
                    LC.tools.Rectangle,
                    LC.tools.Text,
                    LC.tools.Polygon,
                    LC.tools.Ellipse
                ]
            });

            lc.setZoom(ratio)
            lc.setPan(0,0);


            $('#literally-canvas-modal .wrap-literally-canvas .literally').css('height', setModalHeight);
            window.dispatchEvent(new Event('resize'));
            var canvasWidth = $('.literally-canvas-modal .lc-drawing canvas').attr('width');
            var canvasHeight = $('.literally-canvas-modal .lc-drawing canvas').attr('height');


            $(document).find('.export-canvas-button').on('click', function(e) {
                e.preventDefault();
                $('#' + modalId).find('.hide-upload').find("input[data-remove="+inputDataId+"_"+countInput+"]").remove();
                 var ext = $(this).data('ext')&& $(this).data('ext')!='application/pdf' ? $(this).data('ext') : 'image/jpeg';
                var url = $(this).data('url');
                var scale = lc.getRenderScale();
                var canCoor = lc.clientCoordsToDrawingCoords(0,0);
                var canX = canCoor.x;
                var canY = canCoor.y;

                var imageBounds = {
                  x: canX, y: canY, width: canvasWidth/scale, height: canvasHeight/scale
                };
                var imageBase64 = lc.getImage({rect: imageBounds}).toDataURL(ext);
                $('#literally-canvas-modal').find('.modal-body').addClass("hidden")
                $('#literally-canvas-modal').find('.modal-loader').show()
                var currentInput = $(document).find('input[data-id='+inputDataId+']');
                if(isCreate){
                    var currentDataId = inputDataId;
                    var currentIndex = countInput;
                    if(currentInput.length > 0){
                        var filesArray = currentInput[0].files;
                        $.each(filesArray, function(key, value) {
                            if(key!=currentIndex){
                                FILES_BUFFER[currentDataId +'_' + key] = value;
                            }
                        });
                        currentInput.remove()
                    }
                    else{
                        delete FILES_BUFFER[currentDataId + '_' + currentIndex]
                    }

                    var index = Q4U.timestamp();
                    self.data("url",imageBase64);
                    if(controller == 'add_quality_control_image_from_raw_plan'){
                        $('.qc-image-list-mobile').trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
                        $('.qc-image-list-mobile').find('.owl-stage-outer').children().unwrap();
                        $('.qc-image-list-mobile').find('.owl-stage').remove();


                        $('#' + modalId).find('.modal-images-list-table table tbody')
                        .prepend('<tr class="plan-raw-tr">' +
                                   '<td>' +
                                        '<a data-url="'+imageBase64 +'" title="' + planName + '" data-controller="add_quality_control_image_from_raw_data"  class="call-lit-plugin">' +
                                            '<span class="modal-tasks-image-number"></span>'+
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
                                            '<a href="#" class="delete-image-row disabled-gray-button"><i class="q4bikon-delete"></i></a>' +
                                        '</span>' +
                                   '</td>' +
                            '</tr>');
                        $('#' + modalId).find('.qc-image-list-mobile')
                            .prepend('<div class="item qc-image-list-mobile-item">' +
                                    '<a data-url="'+imageBase64+'" title="' + planName + '" data-controller="add_quality_control_image_from_raw_data" class="call-lit-plugin">' +
                                        '<span class="modal-tasks-image-number"></span>' +
                                        '<span class="modal-tasks-image-name"> ' + planName +'</span>'  +
                                        '<span class="modal-img-upload-date"></span>' +
                                    '</a>' +
                                    '<div class="qc-image-list-mobile-item-options">' +
                                        '<span class="circle-sm red delete-image-row">' +
                                            '<i class="q4bikon-delete"></i>' +
                                        '</span>' +
                                    '</div>' +
                                '</div>');

                        $('#' + modalId).find('.hide-upload').append(
                            '<input type="hidden" value="'+imageBase64+'" class="plan-raw-val" name="images_'+index+'_source">'+
                            '<input type="hidden" value="'+planId+'" class="plan-raw-val" name="images_'+index+ '_id">'
                        );
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
                    }else{
                        $('#' + modalId).find('.hide-upload').append(
                            '<input type="hidden" value="'+imageBase64+'" data-remove="'+inputDataId+'_'+ countInput+'" class="load-images-input" name="images_'+index+'_source">'+
                            '<input type="hidden" value="'+ imageName +'" data-remove="'+inputDataId+'_'+ countInput+'" class="load-images-input" name="images_'+index+'_name">'
                        );
                    }
                    $('#literally-canvas-modal').modal('hide');
                    $('#literally-canvas-modal').remove();
                }else{

                    $('#literally-canvas-modal').modal('hide');
                    $('#literally-canvas-modal').remove();
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: JSON.stringify({
                            csrf: Q4U.getCsrfToken(),
                            "x-form-secure-tkn": '',
                            source: imageBase64,
                            name:imageName,
                        }),
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            var content = JSON.parse(response)

                            if(content != undefined && content.errors == undefined){
                                delete FILES_BUFFER[inputDataId + '_' + countInput]

                                currentInput.remove()
                                if (controller.indexOf('plan') != -1) {
                                    var modalContent = content.images;
                                    $(document).find('.modal .modal-images-list-table').replaceWith(modalContent);
                                    $(document).find('.modal .qc-image-list-mobile').replaceWith(modalContent)

                                    $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
                                    $.fn.utilities('setCarouselDirection', ".q4-owl-carousel", 10);
                                    $.fn.utilities('owlPagination', '.q4-owl-carousel');
                                }
                                else if(controller == 'add_quality_control_image_from_raw_data'){
                                    if(currentClass){
                                        $(document).find('a.' + currentClass).data('url',content.filePath);
                                        $(document).find('a.' + currentClass).data('controller','update_quality_control_image');
                                        $(document).find('a.' + currentClass).data('fileid',content.id);
                                    }
                                }
                            }
                        },
                    });

                }

            });

            },1000)
        }

    });
    /************************************************************************
     ****************** End of jQuery Literally Canvas Plugin *****************
     ***********************************************************************/
    $('.mobile-current-lang').on('click', function() {
        $(this).closest('.flag-icon').find('.mobile-lang-list').slideToggle();
    });
    $('.sidebar-profile-options').on('click', function() {
        $(this).closest('.search-mobile').find('.profile-drop-list').slideToggle();
    });
    /* responsive sidebar */
    $('.sidebar_btn').on('click', function() {
        $('.sidebar_mobile').addClass('open');
    });
    $('.close_mobile_sidebar').on('click', function() {
        $(this).closest('.sidebar_mobile').removeClass('open');
    });
    var windowLoadWidth = $(this).width();

    function myCustomFn(el) {
        $('.bootstrap-datetimepicker-widget').hide();
    }
    $(document).on('click', '.modal .delete_row', function(e) {
        e.preventDefault();
        var self = $(this);
        var currentModal = self.closest('.modal');

            var row = self.closest('tr');
            var item = self.closest('.owl-item');

            if (self.attr('data-url') != undefined) {
                Q4U.ajaxGetRequest(self.data('url'), {
                    successCallback: function(data) {
                        row.remove();
                        item.remove();

                        var countFiles = currentModal.find('.modal-images-list-table table tbody tr').length;
                        currentModal.find('.count-fl-list').text(countFiles);

                        $.fn.utilities('updateCurrentOnChange','.qc-image-list-mobile');
                    }
                });
            } else {
                row.remove();
                item.remove();
                var countFiles = currentModal.find('.modal-images-list-table table tbody tr').length;
                currentModal.find('.count-fl-list').text(countFiles);

                $.fn.utilities('updateCurrentOnChange','.qc-image-list-mobile');

            }





    });

    $(window).load(function() {

        var count = $(document).find('.plans-list-layout').find('.pagination a').length;
        $(document).find('.plans-list-layout').find('.pagination a').each(function(i,el){
            var self = $(el);
            var href = self.attr('href');
            var page = i <= 1 ? '/plans_list/' : '/plans_list/page/' + i;
            if(i == count-1){
                page = '/plans_list/page/' + 2;
            }
            if(i <= 1 ){
                self.addClass('disabled-input');
            }else{

                href = href.replace('update/','');
                self.attr('href',href + page);
            }

        });

        $.fn.utilities('updateContentOnChange');
        $(document).find(".progress-bg" ).ready(function(){
            window.dispatchEvent(new Event('resize'));

           $(document).find('.loader_backdrop').delay(100).hide();
        });


    });

    $(document).on('click', '.tab_panel .panel_header', function() {

        if ($(this).parents('.tab_panel').hasClass('disabled')) {
            return;
        }
        var self = $(this);
        $.fn.utilities('handleTabs', self);
        var windowWidth = window.innerWidth;
        $(window).trigger('resize');

        $.fn.utilities('updateContentOnChange');

        if(self.hasClass('open') && self.find('h2').text() ==__("Plans")){

            self.closest('.tab_panel').find('.plans-list-layout .filter-plans').trigger('click')
        }

    });


    if($( "#plans-list-layout" ).length > 0){

        console.log('.plans-list-layout 3 ');
        $.fn.utilities('updateContentPlans');
    }


    $(window).on('resize', function() {

        var windowWidth = $(this).width();
        if ($('.panel_header').hasClass('open')) {
            // $.fn.utilities('setScrollbarDirection', '.scrollable-table');
            if (windowWidth > 768) {
                var self = $('.panel_header.open');
                $.fn.utilities('setScrollBarWidth', self.closest('.tab_panel').find('.scrollable-table'), windowWidth);
                // $('.scrollable-table').mCustomScrollbar("update");
            } else {
                var self = $('.panel_header.open');
                var $panelContent = self.closest('.tab_panel');
                $panelContent.find('.scrollable-table').width(100 + "%");
            }
        }
        if ($('#quality-control-modal').is(':visible')) {
            var modalWidth = $('#quality-control-modal').find('.modal-dialog').width();
            var tasksItemCount = $('#quality-control-modal').find('.tasks-full-description li:visible').length;
            var tasksItemsWidth = tasksItemCount * (350 + 40) + 20;

            var $resizedSlider = $('#quality-control-modal').find('.tasks-full-description-box');
            $('.tasks-full-description-box').width(modalWidth - 40);
            $('.tasks-full-description').width(tasksItemsWidth);
        }

        var jCarusel = $('.wrap-property-structure-list');
        if(jCarusel.length > 0){
            $.fn.utilities('setScrollBarWidth',$('.wrap-property-structure-list li'),windowWidth+30);
        }

        var $modalScrollableTable = $('#modal-report-crafts').find('.scrollable-table');
        if (windowWidth > 768) {
            var modalWidthOnLoad = windowWidth - 40;
            $modalScrollableTable.find('.scrollable-table').width(modalWidthOnLoad);
        }
        if($(document).find('.modal.in').length > 0){
            var width = $(document).find('.modal.in .modal-dialog').last().width();
            $.fn.utilities('setModalCarouselWidth', '.q4-carousel-table-wrap', width);
            $.fn.utilities('setModalCarouselWidth', '.q4-wrap-mobile', width);
        }else{
            $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', windowWidth);
            $.fn.utilities('setCarouselWidth', '.q4-wrap-mobile', windowWidth);
        }

        $.fn.utilities('updateContentPlans');

    });


    $(document).on('click', '.trigger-image-upload', function(e) {

        console.log('set-image-link ');

        e.stopPropagation();
        e.preventDefault();

        $(this).closest('.q4-file-upload').find('.upload-user-logo').trigger('click');

    });

    $(document).on('change','.q4-file-upload .upload-user-logo', function(e) {
        e.stopImmediatePropagation();
        $.fn.utilities('setImageLink', this, $(this));

    });

    /* Styling Radio buttons */
    $(document).on('click', 'label.label_unchecked', function(e) {
        // prevent label from being called twice
        e.stopPropagation();
        e.preventDefault();
        e.stopImmediatePropagation();
        if ($(this).closest('.toggle_container').hasClass('disabled')) {
            $(this).closest('.toggle_container').removeClass('disabled');
            /* Enable rows in table */
            $(this).closest('.hidden_status').prevAll('td').removeClass('disable-text');
        } else {
            $(this).closest('.toggle_container').addClass('disabled');
            /* Disable rows in table */
            $(this).closest('.hidden_status').prevAll('td').addClass('disable-text');
        }
        if ($(this).hasClass('label_unchecked')) {
            $(this).removeClass('label_unchecked').addClass('label_checked');
            $(this).siblings('label').addClass('label_unchecked').removeClass('label_checked');
            $(this).find('input').attr('checked', 'checked');
            $(this).siblings('label').find('input').removeAttr('checked');
        }
    });
    /* File uplad Modal */
    $('#modal_file_upload').modal('hide');
    /*****************************************
     *   Filter page in mobile
     ****************************************/

    $(document).on('click', '.q4-inside-select-filter', function() {
        $(this).siblings('.inside-filters-list-mobile').slideToggle();
    });
    $(document).on('click', '.inside-filter-button-mobile', function() {
        var url = $(this).attr('data-url');
        var html = $(this).html();
        $(this).closest('.inside-filters-list-mobile').siblings('.q4-inside-select-filter').html(html);
        $(this).parents('.inside-filters-list-mobile').slideUp();
        //return false;
    });
    /*****************************************
     *   end of filter page in mobile
     ****************************************/
    /* Remove Row in Modal Window */
    $('.list_uploaded_files').on('click', '.remove_file', function() {
        var selfUl = $(this).closest('.list_uploaded_files');
        $(this).closest('li').remove();
        if (selfUl.find('li').length == 0) {
            selfUl.siblings('.files-default-text').show();
        }
        return false;
    });

    $(document).on('click', '.over-select', function() {
        var self = $(this);
        self.closest('tr').siblings().find('.checkbox-list').hide();
        var checkboxes = self.closest('.select-imitation').siblings(".checkbox-list");
        var checkboxesNoscoll = self.closest('.select-imitation').siblings(".checkbox-list-no-scroll");
        checkboxes.toggle();
        checkboxesNoscoll.toggleClass('hidden');
    });

    $(document).on('change', '.qc-craft', function() {
        var craftVal = $(document).find('.qc-craft').val();
        var selectedCrafts = $(document).find('.qc-craft').data('selected-crafts');
        //console.log(craftVal)


        $(document).find('select[name=tasks] option').each(function() {
            var crafts = ($(this).data('crafts')).toString().split(',');

            var usedCrafts = ($(this).data('usedcrafts')).toString().split(',')
            var el = $('.qc-tasks-list a[data-id=' + $(this).val() + ']');
            var elMobile = $('.qc-tasks-list-mobile a[data-id=' + $(this).val() + ']');
            if ((crafts.indexOf(craftVal) == -1)) {
                $(this).removeAttr('selected');
                this.selected = false;
                if(!el.parents('li').hasClass('used-task')){

                    el.parents('li').removeClass('selected');
                }
                if(!elMobile.closest('div.item').hasClass('used-task')){

                    // el.parents('li').removeClass('selected');
                    elMobile.closest('div.item').removeClass('selected')
                }
                el.parents('li').addClass('hidden')
                elMobile.closest('div.item').addClass('hidden')
            } else {
                console.log(crafts,el);
                el.parents('li').removeClass('hidden');
                elMobile.parents('div.item').removeClass('hidden');
            }
            if ((usedCrafts.indexOf(craftVal) == -1)) {
                $(this).removeAttr('selected');
                this.selected = false;


                //el.parents('li').removeClass('selected');
                el.parents('li').removeClass('reusable');
                el.parents('li').removeClass('used-task');

                // el.parents('li').removeClass('selected');
                //elMobile.closest('div.item').removeClass('selected')
                elMobile.closest('div.item').removeClass('reusable')
                elMobile.closest('div.item').removeClass('used-task')

                // el.parents('li').addClass('hidden')
                // elMobile.closest('div.item').addClass('hidden')
            } else {
                // el.parents('li').removeClass('hidden');
                el.parents('li').addClass('reusable');
                el.parents('li').addClass('used-task');
                //el.parents('li').addClass('selected');
                // elMobile.parents('div.item').removeClass('hidden');
                elMobile.parents('div.item').addClass('reusable');
                elMobile.parents('div.item').addClass('used-task');
                //elMobile.parents('div.item').addClass('selected');

            }
        });


        var itemCount = 0;
        $(document).find('#choose-plan-modal table.responsive_table tbody tr').each(function(i,el){
            var selfTr = $(el);
            // console.log(selfTr.data('crafts'))
            var craftsArray = JSON.parse('"' + selfTr.data('crafts') + '"');
            // console.log(craftsArray)
            if(craftsArray.length && craftsArray.indexOf(craftVal) == -1){
                selfTr.addClass('hidden');

            }else{
                selfTr.removeClass('hidden');
                itemCount++;
            }
        })
        // console.log('desktop',itemCount);
        itemCount = 0;
        $(document).find('#choose-plan-modal-mobile .q4-carousel-table .item').each(function(i,el){
            var selfTr = $(el);
            // console.log(selfTr.data('crafts'))
            var craftsArray = JSON.parse('"' + selfTr.data('crafts') + '"');
            if(craftsArray.length && craftsArray.indexOf(craftVal) == -1){
                selfTr.addClass('hidden');

            }else{
                selfTr.removeClass('hidden');
                itemCount++;
            }
        })
        // $(document).find('#choose-plan-modal-mobile .q4-carousel-table').data('structurecount',itemCount)
        // console.log('mobile',itemCount);
        var self = $('#quality-control-modal');
        //self.find('.tasks-full-description-box').mCustomScrollbar("destroy");
        var modalWidth = $('#quality-control-modal').find('.modal-dialog').width();
        $('.qc-tasks-list-mobile').trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
        $('.qc-tasks-list-mobile').find('.owl-stage-outer').children().unwrap();
        $('.qc-tasks-list-mobile').find('.owl-stage-outer').remove()
        var tasksItemCount = $('.tasks-full-description li:visible').length;

        var tasksItemsWidth = (tasksItemCount+1) * (350 + 30);
        // Add scroll to tasks
        $('.tasks-full-description-box').width(modalWidth - 60);
        $('.tasks-full-description').width(tasksItemsWidth);

        if(craftVal == selectedCrafts){
            var selectedCrafts = self.find('.qc-tasks-list .hidden-select').data('selected-tasks').split(',');
            console.log('selectedCrafts', selectedCrafts);
            self.find('.qc-tasks-list .hidden-select').val(selectedCrafts);
            selfMobile.find('.qc-tasks-list-mobile .hidden-select');
        }
        $.fn.utilities('updateCurrentOnChange','.qc-tasks-list-mobile');
        $(window).trigger('resize');


        // self.find('.tasks-full-description-box').mCustomScrollbar({ axis: "x" });
        // self.find('.tasks-full-description .task-item-txt').mCustomScrollbar({ axis: "y" });
        // self.find('.tasks-full-description-mobile .task-item-txt').mCustomScrollbar({ axis: "y" });

        // $.fn.utilities('updateContentOnChange');

    });

    $(document).on('click', '.qc-tasks-list li', function(e) {
        e.preventDefault();
        var el = $(document).find('.modal').find('select[name=tasks] option[value=' + $(this).children('a').data('id') + ']');

        console.log('el ', el);
        if (el.is(':selected')) {
            console.log('selected');
            if($(this).hasClass('used-task')){
                $(this).addClass('reusable');
            }
            $(this).removeClass('selected');
            el.prop('selected',false);
        }else {
            console.log('NOT selected');
            if($(this).hasClass('used-task')){
                $(this).removeClass('reusable');
            }
            $(this).addClass('selected');
            el.prop('selected',true);
        }
    });
    $(document).on('click', '.qc-tasks-list-mobile .item', function(e) {
        e.preventDefault();
        var el = $(document).find('.modal').find('select[name=tasks] option[value=' + $(this).children('a').data('id') + ']');
        console.log($(document).find('.modal').find('select[name=tasks]').val())
        if (el.is(':selected')) {
            if($(this).hasClass('used-task')){
                $(this).addClass('reusable');
            }
            $(this).removeClass('selected');
            el.prop('selected',false);
        }else {
            console.log("else")
            if($(this).hasClass('used-task')){
                $(this).removeClass('reusable');
            }
            $(this).addClass('selected');
            el.prop('selected',true);
        }
    });

    // $(document).on('change', '.qc-craft', function() {
    //     var attrs = ($(this).find('option:selected').data('professions')).toString().split(',');
    //     var selectedCraftId = $(this).val();
    //     var needReselect = true;
    //     $.each(attrs, function(key, val) {
    //         if (selectedCraftId == val) {
    //             needReselect = false;
    //         }
    //     });
    //     if (needReselect) {
    //         $(document).find('.qc-profession').val(attrs[0]);
    //     }
    //
    //     $(document).find('.qc-profession option').each(function() {
    //         var crafts = ($(this).data('crafts')).toString().split(',');
    //         if (crafts.indexOf($('.qc-craft').val()) == -1) {
    //             $(this).css('display', 'none');
    //         } else {
    //             $(this).css('display', 'block');
    //         }
    //
    //     });
    //
    // });



    /*
    $(document).on('change', '.qc-craft', function() {

        var craftVal = $(document).find('.qc-craft').val();
        var selectedCrafts = $(document).find('.qc-craft').data('selected-crafts');

        $(document).find('select[name=tasks] option').each(function() {

            var crafts = ($(this).data('crafts')).toString().split(',');
            var el = $('.qc-tasks-list a[data-id=' + $(this).val() + ']');
            var elMobile = $('.qc-tasks-list-mobile a[data-id=' + $(this).val() + ']');
            if ((crafts.indexOf(craftVal) == -1)) {
                $(this).removeAttr('selected');
                this.selected = false;
                // el.parents('li').removeClass('selected');
                // elMobile.closest('div.item').removeClass('selected')
                el.parents('li').addClass('hidden');
                elMobile.closest('div.item').addClass('hidden');
            } else {
                el.parents('li').removeClass('hidden');
                elMobile.parents('div.item').removeClass('hidden');
            }
        });


        var itemCount = 0;
        $(document).find('#choose-plan-modal table.responsive_table tbody tr').each(function(i,el){
            var selfTr = $(el);
            var craftsArray = JSON.parse('"' + selfTr.data('crafts') + '"');
            if(craftsArray.length && craftsArray.indexOf(craftVal) == -1){
                selfTr.addClass('hidden');

            }else{
                selfTr.removeClass('hidden');
                itemCount++;
            }
        });

        itemCount = 0;
        $(document).find('#choose-plan-modal-mobile .q4-carousel-table .item').each(function(i,el){
            var selfTr = $(el);
            var craftsArray = JSON.parse('"' + selfTr.data('crafts') + '"');
            if(craftsArray.length && craftsArray.indexOf(craftVal) == -1){
                selfTr.addClass('hidden');

            }else{
                selfTr.removeClass('hidden');
                itemCount++;
            }
        });

        var self = $('#quality-control-modal');
        var selfMobile = $('#quality-control-modal-mobile');
        var modalWidth = self.find('.modal-dialog').width();
        $('.qc-tasks-list-mobile').trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
        $('.qc-tasks-list-mobile').find('.owl-stage-outer').children().unwrap();
        $('.qc-tasks-list-mobile').find('.owl-stage-outer').remove()
        var tasksItemCount = $('.tasks-full-description li:visible').length;

        var tasksItemsWidth = tasksItemCount * (350 + 30)+20;
        // Add scroll to tasks
        $('.tasks-full-description-box').width(modalWidth - 40);
        $('.tasks-full-description').width(tasksItemsWidth);



        if(craftVal == selectedCrafts){
            var selectedCrafts = self.find('.qc-tasks-list .hidden-select').data('selected-tasks').split(',');
            console.log('selectedCrafts', selectedCrafts);
            self.find('.qc-tasks-list .hidden-select').val(selectedCrafts);
            selfMobile.find('.qc-tasks-list-mobile .hidden-select');
        }

        $.fn.utilities('updateCurrentOnChange','.qc-tasks-list-mobile');
        $(window).trigger('resize');

    }); */
/*
    $(document).on('click', '.qc-tasks-list li', function(e) {
        e.preventDefault();
        var el = $(document).find('.modal').find('select[name=tasks] option[value=' + $(this).children('a').data('id') + ']');
        if (el.is(':selected')) {
            $(this).removeClass('selected');
            el.prop('selected',false);
        }else {
            $(this).addClass('selected');
            el.prop('selected',true);
        }
    }); */
/*
    $(document).on('click', '.qc-tasks-list-mobile .item', function(e) {
        e.preventDefault();
        var el = $(document).find('.modal').find('select[name=tasks] option[value=' + $(this).children('a').data('id') + ']');

        if (el.is(':selected')) {
            $(this).removeClass('selected');
            el.prop('selected',false);
        }else {
            $(this).addClass('selected');
            el.prop('selected',true);
        }
    }); */

    $(document).on('change', '.qc-craft', function() {
        var attrs = ($(this).find('option:selected').data('professions')).toString().split(',');
        var selectedCraftId = $(this).val();
        var needReselect = true;
        $.each(attrs, function(key, val) {
            if (selectedCraftId == val) {
                needReselect = false;
            }
        });
        if (needReselect) {
            $(document).find('.qc-profession').val(attrs[0]);
        }

        $(document).find('.qc-profession option').each(function() {
            var crafts = ($(this).data('crafts')).toString().split(',');
            if (crafts.indexOf($('.qc-craft').val()) == -1) {
                $(this).css('display', 'none');
            } else {
                $(this).css('display', 'block');
            }

        });

    });

    var arrayNumbers = [];
    var arrayTexts = [];
    $(document).on('click', '.checkbox-list-row', function() {
        var self = $(this);

        if (self.closest('.multi-select-box').hasClass('comma')) {
            var val = self.find('.checkbox-wrapper-multiple').data('val');
            var option = self.closest('.multi-select-box').find('.hidden-select option[value="' + val + '"]');
            option['0'].selected = !option['0'].selected;
            var select = self.closest('.multi-select-box').find('.hidden-select');
            var texts = [];
            self.closest('.checkbox-list').find('.checkbox-wrapper-multiple').each(function() {
                var opt = select.find('option[value="' + $(this).data('val') + '"]');
                if (opt[0].selected) {
                    arrayNumbers.push(opt.text());
                    $(this).addClass('checked');
                } else {
                    $(this).removeClass('checked');
                }
            });
            //no scrollplugin version
            self.closest('.checkbox-list-no-scroll').find('.checkbox-wrapper-multiple').each(function() {
                var opt = select.find('option[value="' + $(this).data('val') + '"]');
                if (opt[0].selected) {
                    arrayNumbers.push(opt.text());
                    $(this).addClass('checked');
                } else {
                    $(this).removeClass('checked');
                }
            });
            var arrayCurrent = [];
            for (var j = 0; j < arrayNumbers.length; j++) {
                var diffs = 0;
                if (j > 0) {
                    diffs = arrayNumbers[j] - arrayNumbers[j - 1];
                } else {
                    arrayCurrent.push('<span class="bidi-override">' + arrayNumbers[0] + '</span>')
                }
                if (diffs == 1) {
                    arrayCurrent.push('<span class="bidi-override">' + arrayNumbers[j] + '</span>');
                } else if (arrayCurrent.length >= 3 && diffs > 1) {
                    arrayTexts.push('<span class="bidi-override">(' + arrayCurrent[0] + ')</span>-' + '<span class="bidi-override">(' + arrayCurrent[arrayCurrent.length - 1] + ')</span>');
                    arrayCurrent = [arrayNumbers[j]];
                } else if (arrayCurrent.length < 3 && diffs > 1) {
                    var comma = arrayCurrent.join(', ');
                    arrayTexts.push(comma);
                    arrayCurrent = [arrayNumbers[j]];
                }
                if (j == arrayNumbers.length - 1) {
                    if (arrayCurrent.length >= 3) {
                        var arrayContent = '<span class="bidi-override">(' + arrayCurrent[0] + ')</span>-' + '<span class="bidi-override">(' + arrayCurrent[arrayCurrent.length - 1] + ')</span>';
                        arrayTexts.push(arrayContent);
                    } else {
                        var comma = arrayCurrent.join(', ');
                        arrayTexts.push(comma);
                    }
                }
            }
            arrayNumbers = [];
            var result = arrayTexts.join(', ');
            if (result.length <= 0) {
                result = "<span class='select-def-text'>" + __('Please select') + "</span>";
            }
            $(this).closest('.multi-select-box').find('.select-imitation .select-imitation-title').html(result);
            arrayTexts = [];
        } else {

            var tableID = self.closest('.bottom-hidden-select').data('table-element');
            var tableRowId = self.closest('.bottom-hidden-select').data('table-row');
            var val = self.find('.checkbox-wrapper-multiple').data('val');
            var option = self.closest('.multi-select-box').find('.hidden-select option[value="' + val + '"]');
            option['0'].selected = !option['0'].selected;
            var select = self.closest('.multi-select-box').find('.hidden-select');
            var texts = [];
            $(this).closest('.checkbox-list').find('.checkbox-wrapper-multiple').each(function() {
                var opt = select.find('option[value="' + $(this).data('val') + '"]');
                if (opt[0].selected) {
                    texts.push(opt.text());
                    $(this).addClass('checked');
                } else {
                    $(this).removeClass('checked');
                }
            });
            //no scrollplugin version
            $(this).closest('.checkbox-list-no-scroll').find('.checkbox-wrapper-multiple').each(function() {
                var opt = select.find('option[value="' + $(this).data('val') + '"]');
                if (opt[0].selected) {
                    texts.push(opt.text());
                    $(this).addClass('checked');
                } else {
                    $(this).removeClass('checked');
                }
            });



            var result = texts.join(', ');
            self.closest('.advanced-reports').find('.obj-floors .over-select').removeClass('disabled-input');
            var submit = $(document).find('.q4_form_submit');
            submit.removeClass('disabled-input')
            submit.removeClass('disabled-gray-button')
            if (result.length <= 0) {
                result = "<span class='select-def-text'>" + __('Please select') + "</span>";
                self.closest('.advanced-reports').find('.obj-floors .over-select').addClass('disabled-input');
                $(document).find('.generate-reports .q4_form_submit').addClass('disabled-input')
            }
            if (self.parents().hasClass('bottom-hidden-select')) {
                $('#' + tableID).find('[data-row-id="' + tableRowId + '"]').find('.select-imitation-title').html(result);
            } else {
                self.closest('.checkbox-list').siblings('.select-imitation').find('.select-imitation-title').html(result);
                self.closest('.checkbox-list-no-scroll').siblings('.select-imitation').find('.select-imitation-title').html(result);
            }

        }
    });
    /* Get sorted columns name */

    /* Bootstrap Datepicker */
    $(document).find('.date').datetimepicker({
        locale: $(document).find('html').attr('lang')
    }).show();


    /* Bootstrap Datepicker for scrollable tables */
    $('.scrollable-table').find('.scrollable-date').each(function() {
        $(this).datetimepicker({
            locale: LANG,
            widgetParent: 'body'
        }).on('dp.show', function() {
            var top = ($(this).offset().top - 270);
            var left = $(this).offset().left;
            if ($(this).offset().top - 400 <= 0) { //display below if not enough room above
                top = $(this).offset().top + $(this).height() + 10;
            }
            $('.bootstrap-datetimepicker-widget').css({
                'top': top + 'px',
                'left': left + 'px',
                'bottom': 'auto',
                'right': 'auto'
            });
        });
    });
    $(document).on('click', '.check-all-links', function() {
        var self = $(this);

        if (self.html() == self.data('unseltxt')) {
            self.closest('.multi-select-col').find('.checkbox-wrapper-multiple.checked').each(function() {
                if ($(this).hasClass('checked')) $(this).trigger('click');
            });
            self.html(self.data('seltxt'));
        } else {
            self.closest('.multi-select-col').find('.checkbox-wrapper-multiple').each(function() {
                if (!$(this).hasClass('checked')) $(this).trigger('click');
            });
            self.html(self.data('unseltxt'));
        }
    });
    $(document).on('click', '.select-language', function(e) {

        var self = $(this).closest('.select-language');
        self.find('.options').toggle();
        e.stopPropagation();
        e.preventDefault();
    });

    $(document).on('click', '.select-language .option', function(e) {
        var $this = $(this);
        var self = $this.closest('.select-language');
        var $defaultOption = $('.select-language .default-option');
        var option = $this.html();
        var optionNumber = $this.index() - 1;
        var defOption = $defaultOption.find('.option:first-child').html();
        self.find('.option').each(function() {
            $(this).find('input[type=radio]').removeAttr('checked');
        });
        $(this).find('input[type=radio]').attr('checked', 'checked');
        $defaultOption.html(option);
        self.find('.option:nth-child(' + optionNumber + ')').html(defOption);
    });
    $(document).on('click', '.modal .close', function() {
        $(this).closest('.modal').modal('hide');
        if (!$(this).closest('.modal').hasClass('no-delete')) {
            $('.modal').remove();
            $('body').removeClass('modal-open');
            $('body').removeAttr('style');
            $(document).find('.modal-backdrop').remove();
        }
    });
    window.onclick = function(event) {
        if (event.target !== $('.modal')[0] && event.target.closest('.modal') == undefined) {}
    }

    /* Choose view format */
    $(document).on('click', '.choose-view-format-list >  li > a', function() {
        $(this).closest('li').siblings('li').find('a').removeClass('active');
        $(this).addClass('active');
        // return false;
    });

    $(document).on('click', ".stretched-textarea", function() {
        var self = $(this);
        self.closest('tr').siblings().find('.stretched-textarea').each(function(key, el) {
            var txt = $(el);
            if (txt.hasClass('expanded')) {
                txt.removeClass('expanded');
                txt.addClass('collapsed');
                txt.animate({
                    height: '30px'
                });
            }
        });
        if ($(this).hasClass('collapsed')) {
            $(this).removeClass('collapsed');
            $(this).addClass('expanded');
            $(this).animate({
                height: '85px'
            });
        } else {
            $(this).removeClass('expanded');
            $(this).addClass('collapsed');
            $(this).animate({
                height: '30px'
            });
        }
    });

    /***** Quality Control modal, load images *****/

    $(document).on('click', '.modal-load-images', function(e) {
        e.preventDefault();
       var currentInput = $(this).closest('.modal-images-list-box').find('.hide-upload').find('.load-images-input[type=file]').last().trigger('click');

        currentInput.on('change', function(e) {
            $.fn.utilities('modalLoadImages', this, e, imageNumber);
            $(this).closest('form.cert-form').submit();
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
        })
    });
    /*****************************************
     *   end of Quality Control modal
     ****************************************/


    $(document).on('click','.load-file-form',function(e){
        e.preventDefault();
        $(this).closest('.file_container').find('.input-file-form').trigger('click');
    });

    $(document).on('change', '.q4_form .input-file-form', function() {
        $(this).closest('form').submit();

    });


    $(document).on('click', '.confirm-plan', function(e) {
        e.preventDefault();
        var html = $(this).closest('.modal').find('input[name=plan]:checked').closest('td').find('.pln-data').html();

        $(document).find('#quality-control-modal .property-quality-control-name').html(html);
        $(document).find('#quality-control-modal .property-quality-control-name').removeClass('hide');
        $(document).find('#quality-control-modal .property-quality-control-name .hide').removeClass('hide');
        $(document).find('.qc-choose-plan').hide();
        $(document).find('#choose-plan-modal').modal('hide');
    });


    $(document).on('click', '.confirm-plan-mobile', function(e) {
        e.preventDefault();
        var html = $(this).closest('.modal').find('input[name=plan]:checked').closest('.item').find('.pln-data').html();

        if(html){
            $(document).find('.modal').find('.property-quality-control-name').html(html);
            $(document).find('.qc-choose-plan').hide();
            $(document).find('.qc-plan-details').show()
        }
        $(document).find('#choose-plan-modal-mobile').modal('hide');

    });

    $(document).on('click', '.q4-delete-qc', function(e) {
        e.preventDefault();
        var url = $(this).data('url')
        Q4U.confirm(__('Are you sure, you want') + ' ' +  __('delete')+' '+ __('QC') + '?', {
            confirmCallback: function(el, params) {
                Q4U.ajaxGetRequest(url, {
                    successCallback: function(data) {
                        $(document).find('.modal').modal('hide');
                        $(document).find('.quality-control-tab .filter-settings-button.active').trigger('click')
                    }
                });
            },
            type: "danger",
            confirmText:  __('Delete'),
            custom: {
                el: this
            }
        });
    });



    /***************************************
     *   Send Email
     ***************************************/
    $(document).on('click', '.send-reports', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var id = $(this).data('id');
        var dir = $(document).find('html').hasClass('rtl') ? "dir:'rtl'," : '';

        $(document).find('#choose-sender-modal').remove()
        $(document).find('#qc-list-users-modal').remove()
        Q4U.ajaxGetRequest(url, {
            successCallback: function(data) {
                if (data.getData().modal) {

                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('#choose-sender-modal').find('form').attr('action', url);
                    $(document).find('#choose-sender-modal').find('form').append('<input type="hidden" name="project_id" value="'+id+'">')
                    $("#users-mails").select2({
                        width: "100%",
                        tags: true,
                        minimumInputLength:1,
                        // allowClear: true,
                        placeholder: __("Add email address"),
                        language:{
                            inputTooShort: function () {
                                return __("Start to write email");
                              }
                        },
                        dir,
                        dropdownParent:$('.choose-icons-search'),
                        escapeMarkup: function(m) { return m; }
                    });
                    $(document).find('#choose-sender-modal').modal('show');
                    // $(document).find('.qc-list-users-scroll').mCustomScrollbar();
                }
            }
        });
    });

    $(document).on('select2:select','#users-mails', function (e) {

        var userName = e.params.data.id;

        if (userName != undefined && userName != __('Add email address') && userName != '') {

            $('.send-form')
                .find('.send-sender-block').append(
                '<span class="send-email-block">' +
                    '<input  type=hidden name=emails_'+Q4U.timestamp()+' value="' + userName + '">' +
                    '<span class="send-email-block-txt">' + userName + '</span><i class="q4bikon-close close-email-block"></i>' +
                '</span>');
        }
        $('#users-mails').val('').trigger('change');
    });

    $(document).on('click', '.close-email-block', function() {

        $(this).closest('.send-email-block').remove();

    });


    $(document).on('keypress', '.qc-id-to-show', function(e) {
       var val =  $(this).val();
       var keycode = e.keyCode || e.which
       var submit = $(this).closest('div').find('.qc-id-submit');
        if (keycode ==13) {
            if(val.length && !isNaN(val)){
                if(!AJAX_LOADS )
                    submit.trigger('click')
                AJAX_LOADS =  true;
                $(this).blur()
            }
            if(e.preventDefault){
                e.preventDefault();
            }else{

                return false;
            }

       }
    });

    $(document).on('keyup', '.qc-id-to-show', function(e) {
       var val =  $(this).val();
       var keycode = e.keyCode || e.which
       var submit = $(this).closest('div').find('.qc-id-submit');
        if (keycode !=13) {
            if(val.length && !isNaN(val)){
              submit.removeClass('disabled-input')
           }else{
                submit.addClass('disabled-input')
           }

       }

    });

    $(document).on('click', '.qc-id-submit', function(e) {

        e.preventDefault();
        var modalId = $(this).data('modalid');
        var qcId = $(this).closest('div').find('.qc-id-to-show').val();
        var url = $(this).data('url') +'/'  + qcId;

        Q4U.ajaxGetRequest(url, {
            successCallback: function(data) {

                if (data.getData().modal) {
                    $(document).find('.modal').modal('hide');
                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('.modal').each(function() {
                        if ($(this).attr('id') != 'choose-plan-modal' && $(this).attr('id') != 'choose-plan-modal-mobile') {
                            $(this).modal('show');
                        }
                    });

                    $('.date').datetimepicker({
                            locale: $(document).find('html').attr('lang')
                        }).show();
                    setTimeout(function() {
                        $(window).trigger('resize');
                        var self = $(document).find('#' + modalId);
                        var modalWidth = self.find('.modal-dialog').width();

                        var tasksItemCount = $('.tasks-full-description li:visible').length;
                        var tasksItemsWidth = tasksItemCount * (350 + 40);

                        // Add scroll to tasks
                        self.find('.tasks-full-description-box').width(modalWidth - 40);
                        self.find('.tasks-full-description').width(tasksItemsWidth);


                    }, 300)

                }

            },
            errorCallback: function(data){

                console.log(data)
            },
            ajaxErrorCallback:function(event, jqxhr, settings, thrownError){

                Q4U.alert(__('Not found'), {
                    type: "danger",
                    confirmText: __("OK")
                });
            },
        });

    });

    $(document).on('click', '.qc-to-print-btn', function() {
        var printable = $(document).find('.print-quality-control');
        $('.modal').modal('hide');
        setTimeout(function () {
            $('body').append(printable);

            window.print();

            $('body>div.print-quality-control').remove();
        },400);

    });



    $(document).on('change', '.qc-status', function() {

        var value = $(this).val();

        if (value == 'repaired') {
            if($(document).find('.modal .q4-status-select').val()=="for_repair"){
                $(document).find(".q4-status-select option[value='approved']").remove();
                $(document).find('.q4-status-select').append('<option class="q4-status-approved" value="approved">'+__('approved')+'</option>');
                $(document).find('.modal .q4-status-select').val('approved').trigger('change');
            }
        }else{
            var status = $(document).find('.modal .q4-status-select').data('status');
            $(document).find('.modal .q4-status-select').val(status).trigger('change');
        }

        $(document).find(".q4-status-select option[value='approved']").remove();
        if (value == 'invalid') {
            $(document).find('.property-quality-control-conditions .q4-form-input').removeClass('disabled-input');
            //$(document).find('.q4-status-select').addClass('disabled-input');
        } else {

            $(document).find('.property-quality-control-conditions .q4-form-input').addClass('disabled-input');

            $(document).find('.q4-status-select').append('<option class="q4-status-approved" value="approved">'+__('approved')+'</option>');
            $(document).find('.modal .q4-status-select').val('approved').trigger('change');
        }


    });
    $(document).on('change', '.q4-status-select', function() {
        var self = $(this);
        var value = self.val();
        self.removeClass('q4-status-waiting').removeClass('q4-status-for_repair').removeClass('q4-status-approved');
        self.addClass('q4-status-'+value);
    });



    /**** LOAD SINGLE IMAGE ***/
    $(document).on('click', '.modal-load-single-image', function (e) {

        e.preventDefault();

        var self = $(this);
        self.closest('.modal-images-list-box').find('.load-single-image-input').trigger('click');

    });

    $(document).on('change', '.load-single-image-input', function () {

        var input = $(this);

        $(document).find('.modal').find('a.q4_form_submit').removeClass('disabled-gray-button');
        if(input[0].files != undefined && input[0].files[0] != undefined){

            var fileName = input[0].files[0].name;
            input.closest('.modal').find('.modal-file-name').replaceWith('<span class="modal-file-name">' + fileName + '</span>');
        }
    });



    $(document).on('profileUpdated', function(e, data) {

        $(document).find('.send-email-ad').addClass('not-active');

        $(document).find("#user-prf-modal .modal-footer").prepend("<span class='user-prf-message'>" +
            "<span class='user-prf-m-tick'><i class='q4bikon-tick'></i></span>" +
            "<span class='user-prf-m-txt'>"+__('Profile updated successfully')+"</span>" +
        "</span>" );

    });



});

function normalizeObjectStruct() {

    /***************************************
     *   scroll apartments Structure (Property tab)
     ***************************************/
    var scrollWidth = $(document).find('.property-structure-list').width();
    var innerWidth = 0;
    $('[data-toggle="table"]').bootstrapTable();
    var structureId = 0;
    $(document).find('[id^="structure-"]').each(function(i, el) {
        structureId = $(this).attr('id');
        $(document).find('.property-tab-content').find('#' + structureId + ' li').each(function(i, el) {
            var self = $(el);
            innerWidth += self.width() + 30;
            $(this).parent().width(innerWidth);
        });
        innerWidth = 0;
    });
    $(document).find('.property-structure-list').width(scrollWidth);

    var windowWidth = $(window).width();
    if (windowWidth > 992) {
        $(document).find('.property-structure-actions .copy-element').tooltip({
            title: "Copy Element",
            placement: "top"
        });
        $(document).find('.property-structure-actions .edit-element').tooltip({
            title: "Edit Element",
            placement: "top"
        });
        $(document).find('.property-structure-actions .delete_row').tooltip({
            title: "Delete Element",
            placement: "top"
        });
    }
}

function setFloor(self) {

    self.siblings('.panel_content').find('.set-floor').each(function(key, el) {

        var self = $(el);
        var setFloor = self.val();
        if (setFloor == 0) {
            self.closest('tr').find('.multi-select-box.comma').find('.select-imitation').addClass('disabled-input');
        }
    });
}


/**
 * Send by emeil in reports and dashboard
 *
 */
// clip long paragraph and add dots ( mobile Layout ) /
var projectsDiv = $('.q4-list-item-mobile-desc');
var projectsDesc = projectsDiv.find('p');
projectsDiv.each(function() {
    projectsDesc = $(this).find('p').text();
    if (projectsDesc.length >= 230) {
        projectsDesc = projectsDesc.substr(0, 215) + ' ...';
        $(this).find('p').text(projectsDesc);
    }
});
// clip long paragraph and add dots ( Desktop Layout ) /
var projectsDivDesktop = $('.q4-list-item-desc');
var projectsDescDesktop = projectsDivDesktop.find('p');
projectsDivDesktop.each(function() {
    projectsDescDesktop = $(this).find('p').text();
    if (projectsDescDesktop.length >= 230) {
        projectsDescDesktop = projectsDescDesktop.substr(0, 230) + ' ...';
        $(this).find('p').text(projectsDescDesktop);
    }
});