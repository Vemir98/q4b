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
var FILES_BUFFER = []; //буффер файлов
var LOADER = true; //показать лоадер
var LANG = $(document).find('html').attr('lang') ? $(document).find('html').attr('lang') : 'en'
var Q4U = window.Q4U || {};
var MAX_FILE_SIZE = Math.min.apply(null, $(document).find('[name=fps]').attr('content').split('-'))
var AJAX_LOADS = false;
var AUTH_TOKEN = $('meta[name="u_token"]').attr('content');
var API_PATH = $('meta[name="api_path"]').attr('content');
var SITE_URL = $('meta[name="site_url"]').attr('content');


window.qfetch = function (url, options){
    if (!url.includes('http://') && !url.includes('https://')) {
        url = `${SITE_URL}${API_PATH}${url}`
    }
    if (options.method === 'PUT') {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(options.body);
    }
    if (options.body && !(options.body instanceof FormData) && options.method === 'POST') {
        var optionsFormatter = {
            initialFormData: new FormData(),
            showLeafArrayIndexes: true,
            includeNullValues: false,
            mapping: function(value) {
                if (typeof value === 'boolean') {
                    return +value ? '1': '0';
                }
                return value;
            }
        };

        options.body = window.jsonToFormData(options.body, optionsFormatter);
    }
    return fetch(url,options)
        .then(response => {
            return response.json().then(data => {
                if (response.ok) {
                    return data;
                } else {
                    return Promise.reject({status: response.status, data});
                }
            });
        });
}
window.qfetchOld = function (url, options){
    if (!url.includes('http://') && !url.includes('https://')) {
        url = `${SITE_URL}${API_PATH}${url}`
    }
    options.headers['Content-Type'] = !options.headers['Content-Type'] ? 'application/json' : options.headers['Content-Type'];
    options.body = options.body ? JSON.stringify(options.body) : null;
    return fetch(url,options)
        .then(response => {
            return response.json().then(data => {
                if (response.ok) {
                    return data;
                } else {
                    return Promise.reject({status: response.status, data});
                }
            });
        });
}
function normalizeTables(){
    $(document).find('table.scrollable-tbody-content').each(function () {
        var hasScroll = false;
        var tbody = $(this).find('tbody');

        if (tbody.scrollTop()) {
            // Element is already scrolled, so it is scrollable
            hasScroll = true;
        } else {
            // Test by actually scrolling
            tbody.scrollTop(1);

            if (tbody.scrollTop()) {
                // Scroll back
                tbody.scrollTop(0);
                hasScroll = true;
            }
        }

        if(!hasScroll){console.log(hasScroll);
            $(this).find('thead').css({
                width: ('100%')
            })
        }
    });
}

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
    normalizeTables();
});
$(document).ajaxStart(function() {
    if (LOADER != undefined && LOADER) $(document).find('.loader_backdrop').show();
});
$(document).ajaxComplete(function() {
    $(document).find('.loader_backdrop').hide();
    AJAX_LOADS = false;
    $(window).trigger('resize');
});
$(document).on('click', 'form a.submit', function(e) {
    e.preventDefault();
    $(this).parents('form').submit();
});

function getFormData(form) {
    var unindexed_array = form.serializeArray();
    var indexed_array = {};
    $.map(unindexed_array, function(n, i) {
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
    if (valid.valid) {
        if (!$.isEmptyObject(FILES_BUFFER)) {
            var formData = new FormData();
            var data = getFormData(currentForm);
            data["csrf"] = Q4U.getCsrfToken();
            formData.append("Data", JSON.stringify(data));
            for (var key in FILES_BUFFER) {
                formData.append('images[]', FILES_BUFFER[key]);
            }
            $.ajax({
                url: urlPost,
                type: 'POST',
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.errors) {
                        Q4U.alert(__(data.errors), {
                            type: "danger",
                            confirmText: __("OK")
                        });
                    } else if (data.struct) {
                        $(document).find('.property-tab-content').html(data.struct);
                        normalizeObjectStruct();
                        var allFloors = $(document).find('.wrap-property-structure-list').data('floor');
                        var middleFloor = parseInt(allFloors / 2);
                        $('.wrap-property-structure-list').jCarouselLite({
                            btnNext: ".next",
                            btnPrev: ".prev",
                            vertical: true,
                            visible: 1,
                            circular: false,
                            start: middleFloor
                        });
                        $('.wrap-property-structure-list').css('overflow', 'hidden');
                        if (allFloors < 2) {
                            $('.wrap-property-structure-list').closest('.property-struct').find('.property-floors-arrow').addClass('disabled');
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
        } else {
            currentForm.submit();
        }
    } else {
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
        msg = msg.replace(new RegExp('(' + p.hilight.replace(new RegExp(/(\(|\))/, 'g'), '\\$1') + ')', 'g'), '<span class="confirmation-object">"$1"</span>');
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
        msg = msg.replace(new RegExp('(' + p.hilight.replace(new RegExp(/(\(|\))/, 'g'), '\\$1') + ')', 'g'), '<span class="confirmation-object">"$1"</span>');
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
    $(document).find('.object-general-select').on('change', function() {
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
        }).slice(0, 5);
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
        if ($(document).find('.modal').length < 1) {
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
        var doDelete = false;
        var i = 0;
        var alert = false;
        var modalId = $(this).attr('id')
        if (modalId != undefined) {
            alert = $(this).attr('id').indexOf("alert") != -1 ? true : ($(this).attr('id').indexOf("confirm") != -1 ? true : false);
        }
        if (alert) {
            $(this).remove();
            if ($(document).find('.modal').length < 1) {
                $(document).find('body').removeClass('modal-open');
                $(document).find('.modal-backdrop').remove();
            }
        }
        $(document).find('.modal').each(function() {
            if ($(this).hasClass('no-delete-v2')) {
                doDelete = true;
            }
            if ($(this).is(':visible')) {
                i++;
            }
        });

        if (doDelete) return;
        if (!i || $('.modal').length < 1) {
            $('.modal').remove()
            $(document).find('body').removeClass('modal-open');
            if ($(document).find('.modal').length < 1) {
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
            var sidebarWidth = $(".sidebar").is(':visible') ? $(".sidebar").width() : 0
            $.fn.utilities('updateContentOnChangeNew', $(this).find('.modal-dialog').width()-sidebarWidth);

            // $.fn.utilities('tableScrollableContent');
        }
    });

    $(document).on('hide.bs.modal', '.modal', function() {
        //console.log("RESIZE")
            var sidebarWidth = $(".sidebar").is(':visible') ? $(".sidebar").width() : 0

        $.fn.utilities('updateContentOnChangeNew', $(this).find('.modal-dialog').width()-sidebarWidth + 60);
        //$.fn.utilities('updateContentOnChangeNew', $(window).width()-$(".sidebar").width()-40);
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
        var currentInput = $(document).find('input[data-id=' + currentDataId + ']');
        if (currentInput.length > 0) {
            var filesArray = currentInput[0].files;
            $.each(filesArray, function(key, value) {
                if (key != currentIndex) {
                    FILES_BUFFER[currentDataId + '_' + key] = value;
                }
            });
            currentInput.remove()
        } else {
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
                    $.fn.utilities('updateCurrentOnChange', '.qc-image-list-mobile');
                }
            });
        } else {
            row.remove();
            item.remove();
            var countFiles = currentModal.find('.modal-images-list-table table tbody tr').length;
            currentModal.find('.count-fl-list').text(countFiles);
            $.fn.utilities('updateCurrentOnChange', '.qc-image-list-mobile');
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
        var selectBox = ['multi-select-box', 'over-select', 'select-imitation', 'checkbox-wrapper-comma', 'checkbox-wrapper-comma checked', 'over-select-comma', 'mCSB_dragger', 'mCSB_dragger_bar', 'mCSB_draggerRail', 'checkbox-wrapper', 'checkbox-replace', 'checkbox-list-row', 'checkbox-text', 'checkbox-list-tick q4bikon-tick'];
        if (clickedClass != undefined && selectBox.indexOf(clickedClass.trim()) == -1) {
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
    // $(document).on('click', '.call-lit-plugin', function(e) {
    //     e.preventDefault();
    //
    //     var self = $(this);
    //
    //     //callLitPlugin(self);
    //
    //     var isCreate = self.closest('.modal').hasClass('create-modal') ? "create" : false;
    //     var inputDataId = self.data('inputid');
    //     var countInput = self.data('index')
    //     var type = self.closest('tr').hasClass('dynamically-appended') ? "dynamic" : false;
    //     var imageName = self.find('.modal-tasks-image-name').text();
    //     var planName = self.attr('title');
    //     var modalId = self.closest('.modal').attr('id');
    //     var qcId = $('#' + modalId).data('qcid') ? '/' + $('#' + modalId).data('qcid') : '';
    //     var ext = self.data('ext');
    //     var fileId = self.data('fileid') ? '/' + self.data('fileid') : '';
    //     var controller = self.data('controller')
    //     var currentClass = self.attr('class').split(' ')[0];
    //     var planId = self.data('fileid');
    //     currentClass = (currentClass != 'call-lit-plugin') ? currentClass : '';
    //     // $(document).find('.literally-canvas-modal').remove();
    //     var imageSrc = self.data('url');
    //
    //     var setModalHeight = $(window).height() * 0.70;
    //     var modalLiterallyCanvas =
    //     '<div id="literally-canvas-modal" data-backdrop="static" data-keyboard="false" class="modal no-delete fade literally-canvas-modal" role="dialog">' +
    //         '<div id="sketch-image-dialog" class="modal-dialog q4_project_modal literally-canvas-dialog">' +
    //             '<div class="modal-content" style="height: 600px">' +
    //                 '<div class="modal-header q4_modal_header">' +
    //                     '<div class="q4_modal_header-top">' +
    //                         '<button type="button" class="close q4-close-modal" data-dismiss="modal">' +
    //                             '<i class="q4bikon-close"></i>' +
    //                         '</button>' +
    //                         '<div class="clear"></div>' +
    //                     '</div>' +
    //                     '<div class="q4_modal_sub_header">' +
    //                         '<h3>' + __('Edit image') + '</h3>' +
    //                     '</div>' +
    //                 '</div>' +
    //                 '<div class="modal-body sketchpad-modal-body" style="height: 600px">' +
    //                     '<div class="" style="height: 600px" id="pixel-perfect-editor"></div>' +
    //                 '</div>' +
    //
    //                 '<div class="modal-footer text-align">' +
    //                     '<a href="#" class="btn btn-primary save-sketch export-canvas-button" data-ext="' + ext + '" data-url="/projects/' + controller + qcId + fileId + '">' + __('Save') + '</a>' +
    //                 '</div>' +
    //             '</div>' +
    //         '</div>' +
    //     '</div>';
    //
    //
    //     var backgroundImage = new Image();
    //     backgroundImage.crossOrigin = "https://qforb.net";
    //     backgroundImage.src = imageSrc.indexOf('base64') != -1 ? imageSrc : imageSrc + '?' + Q4U.timestamp();
    //     // backgroundImage.addEventListener('load', loadImage, false);
    //
    //
    //
    //     // $(document).find('body').append(modalLiterallyCanvas);
    //
    //     function loadImage() {
    //         setTimeout(function() {
    //             var currentImageWidth = backgroundImage.width;
    //             var currentImageHeight = backgroundImage.height;
    //             var imageSize = {
    //                 width: currentImageWidth,
    //                 height: currentImageHeight
    //             };
    //             var currentModalWidth = $(document).find('.literally-canvas-modal .modal-dialog').width() - 63;
    //             var hRatio = currentModalWidth / currentImageWidth;
    //             var vRatio = setModalHeight / currentImageHeight;
    //             var ratio = Math.min(hRatio, vRatio);
    //             $('#literally-canvas-modal').find('.modal-loader').hide();
    //             $('#literally-canvas-modal').find('.modal-body').css('height', 'auto').removeClass("hidden");
    //             var lc = LC.init(document.getElementsByClassName('wrap-literally-canvas')[0], {
    //                 imageURLPrefix: '/media/img/literallycanvasimg',
    //                 imageSize: imageSize,
    //                 backgroundShapes: [
    //                     LC.createShape('Image', {
    //                         x: 0,
    //                         y: 0,
    //                         image: backgroundImage,
    //                         scale: 1.0,
    //                         zoomStep: 0.1,
    //                     })
    //                 ],
    //                 defaultStrokeWidth: 10,
    //                 secondaryColor: 'transparent',
    //                 primaryColor: '#ff0000',
    //                 tools: [
    //                     LC.tools.Pan,
    //                     LC.tools.Pencil,
    //                     LC.tools.Eraser,
    //                     LC.tools.Line,
    //                     LC.tools.Rectangle,
    //                     LC.tools.Text,
    //                     LC.tools.Polygon,
    //                     LC.tools.Ellipse
    //                 ]
    //             });
    //             lc.setZoom(ratio)
    //             lc.setPan(0, 0);
    //             $('#literally-canvas-modal .wrap-literally-canvas .literally').css('height', setModalHeight);
    //             window.dispatchEvent(new Event('resize'));
    //             var canvasWidth = $('.literally-canvas-modal .lc-drawing canvas').attr('width');
    //             var canvasHeight = $('.literally-canvas-modal .lc-drawing canvas').attr('height');
    //
    //             $(document).find('.export-canvas-button').on('click', function(e) {
    //                 e.preventDefault();
    //                 $('#' + modalId).find('.hide-upload').find("input[data-remove=" + inputDataId + "_" + countInput + "]").remove();
    //                 var ext = $(this).data('ext') && $(this).data('ext') != 'application/pdf' ? $(this).data('ext') : 'image/jpeg';
    //                 var url = $(this).data('url');
    //                 var scale = lc.getRenderScale();
    //                 var canCoor = lc.clientCoordsToDrawingCoords(0, 0);
    //                 var canX = canCoor.x;
    //                 var canY = canCoor.y;
    //                 var imageBounds = {
    //                     x: canX,
    //                     y: canY,
    //                     width: canvasWidth / scale,
    //                     height: canvasHeight / scale
    //                 };
    //                 var imageBase64 = lc.getImage({
    //                     rect: imageBounds
    //                 }).toDataURL(ext);
    //                 var format = 'pixp';
    //                 var imageBase64 = editor.getCanvasDataAs(format);
    //                 console.log(imageBase64)
    //                 $('#literally-canvas-modal').find('.modal-body').addClass("hidden")
    //                 $('#literally-canvas-modal').find('.modal-loader').show()
    //                 var currentInput = $(document).find('input[data-id=' + inputDataId + ']');
    //                 if (isCreate) {
    //                     var currentDataId = inputDataId;
    //                     var currentIndex = countInput;
    //                     if (currentInput.length > 0) {
    //                         var filesArray = currentInput[0].files;
    //                         $.each(filesArray, function(key, value) {
    //                             if (key != currentIndex) {
    //                                 FILES_BUFFER[currentDataId + '_' + key] = value;
    //                             }
    //                         });
    //                         currentInput.remove()
    //                     } else {
    //                         delete FILES_BUFFER[currentDataId + '_' + currentIndex]
    //                     }
    //                     var index = Q4U.timestamp();
    //                     if (self.data('url').indexOf('base64') != -1 ) {
    //
    //                         self.data("url", imageBase64);
    //                     }
    //                     if (controller == 'add_quality_control_image_from_raw_plan') {
    //                         $('.qc-image-list-mobile').trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
    //                         $('.qc-image-list-mobile').find('.owl-stage-outer').children().unwrap();
    //                         $('.qc-image-list-mobile').find('.owl-stage').remove();
    //                         var imagePrepend = getPrependContent(planName, imageBase64);
    //
    //                         $('#' + modalId).find('.modal-images-list-table table tbody').prepend(imagePrepend);
    //
    //
    //                         var imagePrependMobile = getPrependContentMobile(planName, imageBase64);
    //                         // '<div class="item qc-image-list-mobile-item">' +
    //                         //     '<a data-url="' + imageBase64 + '" title="' + planName + '" data-controller="add_quality_control_image_from_raw_data" class="call-lit-plugin">' +
    //                         //         '<span class="modal-tasks-image-number"></span>' +
    //                         //         '<span class="modal-tasks-image-name"> ' + planName +
    //                         //         '</span>' +
    //                         //         '<span class="modal-img-upload-date"></span>' +
    //                         //     '</a>' +
    //                         //      '<div class="qc-image-list-mobile-item-options">' +
    //                         //          '<span class="circle-sm red delete-image-row disabled-gray-button">' +
    //                         //              '<i class="q4bikon-delete"></i>' +
    //                         //          '</span>' +
    //                         //      '</div>' +
    //                         // '</div>'
    //                            //console.log("modal",getPrependContentMobile(planName, imageBase64))
    //                         $('#' + modalId).find('.qc-image-list-mobile').prepend(getPrependContentMobile(planName, imageBase64));
    //                         $('#' + modalId).find('.hide-upload').append('<input type="hidden" value="' + imageBase64 + '" class="plan-raw-val" name="images_' + index + '_source">' + '<input type="hidden" value="' + planId + '" class="plan-raw-val" name="images_' + index + '_id">');
    //                         $('#' + modalId).find('.modal-images-list-table table').find('tr').each(function(i, el) {
    //                             var self = $(el);
    //                             self.find('.modal-tasks-image-number').text(i + 1 + '.');
    //                         })
    //
    //                         $('#' + modalId).find('.qc-image-list-mobile .item').each(function(i, el) {
    //                             var self = $(el);
    //                             self.find('.modal-tasks-image-number').text(i + 1 + '.');
    //                         })
    //
    //                         $.fn.utilities('setCarouselDirection', ".qc-image-list-mobile", 10);
    //                         $.fn.utilities('owlPagination', '.q4-owl-carousel');
    //                     } else {
    //                         var hiddenInput =
    //                             '<input type="hidden" value="' + imageBase64 + '" data-remove="' + inputDataId + '_' + countInput + '" class="load-images-input" name="images_' + index + '_source">' + '<input type="hidden" value="' + imageName + '" data-remove="' + inputDataId + '_' + countInput + '" class="load-images-input" name="images_' + index + '_name">';
    //                         $('#' + modalId).find('.hide-upload').append(hiddenInput);
    //                     }
    //                     $('#literally-canvas-modal').modal('hide');
    //                     $('#literally-canvas-modal').remove();
    //                     $(document).find('.modal-backdrop').remove();
    //                     $(document).find('body').removeClass('modal-open');
    //                 } else {
    //                     $('#literally-canvas-modal').modal('hide');
    //                     $('#literally-canvas-modal').remove();
    //                     $(document).find('.modal-backdrop').remove();
    //                     $(document).find('body').removeClass('modal-open');
    //                     $.ajax({
    //                         url: url,
    //                         method: 'POST',
    //                         data: JSON.stringify({
    //                             csrf: Q4U.getCsrfToken(),
    //                             "x-form-secure-tkn": '',
    //                             source: imageBase64,
    //                             name: imageName,
    //                         }),
    //                         cache: false,
    //                         contentType: false,
    //                         processData: false,
    //                         success: function(response) {
    //                             var content = JSON.parse(response)
    //                             if (content != undefined && content.errors == undefined) {
    //                                 delete FILES_BUFFER[inputDataId + '_' + countInput]
    //                                 currentInput.remove()
    //                                 if (controller.indexOf('plan') != -1) {
    //                                     var modalContent = content.images;
    //                                     $(document).find('.modal .modal-images-list-table').replaceWith(modalContent);
    //                                     $(document).find('.modal .qc-image-list-mobile').replaceWith(modalContent)
    //                                     $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
    //                                     $.fn.utilities('setCarouselDirection', ".q4-owl-carousel", 10);
    //                                     $.fn.utilities('owlPagination', '.q4-owl-carousel');
    //                                 } else if (controller == 'add_quality_control_image_from_raw_data') {
    //                                     if (currentClass) {
    //                                         $(document).find('a.' + currentClass).data('url', content.filePath);
    //                                         $(document).find('a.' + currentClass).data('controller', 'update_quality_control_image');
    //                                         $(document).find('a.' + currentClass).data('fileid', content.id);
    //                                     }
    //                                 }
    //                             }
    //                         },
    //                     });
    //                 }
    //             });
    //         }, 1000)
    //     }
    // });


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
                    $.fn.utilities('updateCurrentOnChange', '.qc-image-list-mobile');
                }
            });
        } else {
            row.remove();
            item.remove();
            var countFiles = currentModal.find('.modal-images-list-table table tbody tr').length;
            currentModal.find('.count-fl-list').text(countFiles);
            $.fn.utilities('updateCurrentOnChange', '.qc-image-list-mobile');
        }
    });
    $(window).load(function() {
        var count = $(document).find('.plans-list-layout').find('.pagination a').length;
        $(document).find('.plans-list-layout').find('.pagination a').each(function(i, el) {
            var self = $(el);
            var href = self.attr('href');
            var page = i <= 1 ? '/plans_list/' : '/plans_list/page/' + i;
            if (i == count - 1) {
                page = '/plans_list/page/' + 2;
            }
            if (i <= 1) {
                self.addClass('disabled-input');
            } else {
                href = href.replace('update/', '');
                self.attr('href', href + page);
            }
        });
        $.fn.utilities('updateContentOnChange');
        $(document).find(".progress-bg").ready(function() {
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
        if (self.hasClass('open') && self.find('h2').text() == __("Plans")) {
            self.closest('.tab_panel').find('.plans-list-layout .filter-plans').trigger('click')
        }
    });
    if ($("#plans-list-layout").length > 0) {
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
            var sidebarWidth = $(".sidebar").is(':visible') ? $(".sidebar").width() : 0

            var tasksItemsWidth = tasksItemCount * (350 + 40) + 20;
            var $resizedSlider = $('#quality-control-modal').find('.tasks-full-description-box');
            $('.tasks-full-description-box').width(modalWidth - 40);
            $('.tasks-full-description').width(tasksItemsWidth);
            $.fn.utilities('updateContentOnChangeNew', $(this).find('.modal-dialog').width()-sidebarWidth + 60);
        }
        normalizeTasksList();
        // if ($('.qc-create-window').is(':visible')) {
        //     var modalWidth = $('.qc-create-window').find('.modal-dialog').width();
        //     var tasksItemCount = $('.qc-create-window').find('.tasks-full-description li:visible').length;
        //     var sidebarWidth = $(".sidebar").is(':visible') ? $(".sidebar").width() : 0

        //     var tasksItemsWidth = tasksItemCount * (350 + 40) + 20;
        //     var $resizedSlider = $('.qc-create-window').find('.tasks-full-description-box');
        //     $('.tasks-full-description-box').width(modalWidth - 40);
        //     $('.tasks-full-description').width(tasksItemsWidth);
        //     $.fn.utilities('updateContentOnChangeNew', $('.qc-create-window').find('.modal-dialog').width()-sidebarWidth + 60);
        // }
        var jCarusel = $('.wrap-property-structure-list');
        if (jCarusel.length > 0) {
            $.fn.utilities('setScrollBarWidth', $('.wrap-property-structure-list li'), windowWidth + 30);
        }
        var $modalScrollableTable = $('#modal-report-crafts').find('.scrollable-table');
        if (windowWidth > 768) {
            var modalWidthOnLoad = windowWidth - 40;
            $modalScrollableTable.find('.scrollable-table').width(modalWidthOnLoad);
        }
        if ($(document).find('.modal.in').length > 0) {
            var width = $(document).find('.modal.in .modal-dialog').last().width();
            $.fn.utilities('setModalCarouselWidth', '.q4-carousel-table-wrap', width);
            $.fn.utilities('setModalCarouselWidth', '.q4-wrap-mobile', width);
        } else {
            $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', windowWidth);
            $.fn.utilities('setCarouselWidth', '.q4-wrap-mobile', windowWidth);
        }
        $.fn.utilities('updateContentPlans');
        var sidebarWidth = $(".sidebar").is(':visible') ? $(".sidebar").width() : 0
        $.fn.utilities('updateContentOnChangeNew', $(window).width()-sidebarWidth)
    });
    $(document).on('click', '.trigger-image-upload', function(e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).closest('.q4-file-upload').find('.upload-user-logo').trigger('click');
    });
    $(document).on('change', '.q4-file-upload .upload-user-logo', function(e) {
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
        $(document).find('select[name=tasks] option').each(function() {
            var crafts = ($(this).data('crafts')).toString().split(',');
            var usedCrafts = ($(this).data('usedcrafts')).toString().split(',')
            var el = $('.qc-tasks-list a[data-id=' + $(this).val() + ']');
            var elMobile = $('.qc-tasks-list-mobile a[data-id=' + $(this).val() + ']');
            if ((crafts.indexOf(craftVal) == -1)) {
                $(this).removeAttr('selected');
                this.selected = false;
                if (!el.parents('li').hasClass('used-task')) {
                    el.parents('li').removeClass('selected');
                }
                if (!elMobile.closest('div.item').hasClass('used-task')) {
                    // el.parents('li').removeClass('selected');
                    elMobile.closest('div.item').removeClass('selected')
                }
                el.parents('li').addClass('hidden')
                elMobile.closest('div.item').addClass('hidden')
            } else {
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
        $(document).find('#choose-plan-modal table.responsive_table tbody tr').each(function(i, el) {
            var selfTr = $(el);
            var craftsArray = JSON.parse('"' + selfTr.data('crafts') + '"');
            if (craftsArray.length && craftsArray.indexOf(craftVal) == -1) {
                selfTr.addClass('hidden');
            } else {
                selfTr.removeClass('hidden');
                itemCount++;
            }
        })
        itemCount = 0;
        $(document).find('#choose-plan-modal-mobile .q4-carousel-table .item').each(function(i, el) {
            var selfTr = $(el);
            var craftsArray = JSON.parse('"' + selfTr.data('crafts') + '"');
            if (craftsArray.length && craftsArray.indexOf(craftVal) == -1) {
                selfTr.addClass('hidden');
            } else {
                selfTr.removeClass('hidden');
                itemCount++;
            }
        })
        var self = $('#quality-control-modal');
        normalizeTasksList();

        if (craftVal == selectedCrafts) {
            var selectedCrafts = self.find('.qc-tasks-list .hidden-select').data('selected-tasks').split(',');
            self.find('.qc-tasks-list .hidden-select').val(selectedCrafts);
            selfMobile.find('.qc-tasks-list-mobile .hidden-select');
        }
        $.fn.utilities('updateCurrentOnChange', '.qc-tasks-list-mobile');
        $(window).trigger('resize');
    });

    function normalizeTasksList(){
        var modalWidth = $('#quality-control-modal').find('.modal-dialog').width();
        modalWidth = modalWidth ? modalWidth : $('.qc-create-window').width();
        $('.qc-tasks-list-mobile').trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
        $('.qc-tasks-list-mobile').find('.owl-stage-outer').children().unwrap();
        $('.qc-tasks-list-mobile').find('.owl-stage-outer').remove()
        var tasksItemCount = $('.tasks-full-description li:visible').length;
        var sidebarWidth = $(".sidebar").is(':visible') ? $(".sidebar").width() : 0;

        var tasksItemsWidth = (tasksItemCount + 1) * (360 + 30)-20;
        // Add scroll to tasks
        $('.tasks-full-description-box').width(modalWidth - 60);
        if($('.qc-create-window').width()){

            $(".tasks-full-description-box").width($(window).width()-sidebarWidth - 100);
            // console.log("$(window).width()", $(window).width()-sidebarWidth - 100);

        }
        $('.tasks-full-description').width(tasksItemsWidth);
    }
    $(document).on('click', '.qc-tasks-list li', function(e) {
        e.preventDefault();
        var el = $(document).find('.modal').find('select[name=tasks] option[value=' + $(this).children('a').data('id') + ']');
        if (el.is(':selected')) {
            if ($(this).hasClass('used-task')) {
                $(this).addClass('reusable');
            }
            $(this).removeClass('selected');
            el.prop('selected', false);
        } else {
            if ($(this).hasClass('used-task')) {
                $(this).removeClass('reusable');
            }
            $(this).addClass('selected');
            el.prop('selected', true);
        }
    });
    $(document).on('click', '.qc-tasks-list-mobile .item', function(e) {
        e.preventDefault();
        var el = $(document).find('.modal').find('select[name=tasks] option[value=' + $(this).children('a').data('id') + ']');
        if (el.is(':selected')) {
            if ($(this).hasClass('used-task')) {
                $(this).addClass('reusable');
            }
            $(this).removeClass('selected');
            el.prop('selected', false);
        } else {
            if ($(this).hasClass('used-task')) {
                $(this).removeClass('reusable');
            }
            $(this).addClass('selected');
            el.prop('selected', true);
        }
    });

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
        if (typeof $(this).data('custom-label') !== 'undefined') return;
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
            //меняю идиотический код
            var fuckIt = self.closest('.multi-select-box.pr-stage');
            if(fuckIt.length-1){
                self.closest('.advanced-reports').find('.floors-list .over-select').removeClass('disabled-input');
                var submit = $(document).find('.q4_form_submit');
                submit.removeClass('disabled-input')
                submit.removeClass('disabled-gray-button')
                if (result.length <= 0) {
                    result = "<span class='select-def-text'>" + __('Please select') + "</span>";
                    self.closest('.advanced-reports').find('.floors-list .over-select').addClass('disabled-input');
                    $(document).find('.generate-reports .q4_form_submit').addClass('disabled-input')
                }
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
    $(document).on('click', '.load-file-form', function(e) {
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
        if (html) {
            $(document).find('.modal').find('.property-quality-control-name').html(html);
            $(document).find('.qc-choose-plan').hide();
            $(document).find('.qc-plan-details').show()
        }
        $(document).find('#choose-plan-modal-mobile').modal('hide');
    });
    $(document).on('click', '.q4-delete-qc', function(e) {
        e.preventDefault();
        var url = $(this).data('url')
        Q4U.confirm(__('Are you sure, you want') + ' ' + __('delete') + ' ' + __('QC') + '?', {
            confirmCallback: function(el, params) {
                Q4U.ajaxGetRequest(url, {
                    successCallback: function(data) {
                        $(document).find('.modal').modal('hide');
                        $(document).find('.quality-control-tab .filter-settings-button.active').trigger('click')
                    }
                });
            },
            type: "danger",
            confirmText: __('Delete'),
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
                    $(document).find('#choose-sender-modal').find('form').append('<input type="hidden" name="project_id" value="' + id + '">')
                    $("#users-mails").select2({
                        width: "100%",
                        tags: true,
                        minimumInputLength: 1,
                        // allowClear: true,
                        placeholder: __("Add email address"),
                        language: {
                            inputTooShort: function() {
                                return __("Start to write email");
                            }
                        },
                        dir,
                        dropdownParent: $('.choose-icons-search'),
                        escapeMarkup: function(m) {
                            return m;
                        }
                    });
                    $(document).find('#choose-sender-modal').modal('show');
                    // $(document).find('.qc-list-users-scroll').mCustomScrollbar();
                }
            }
        });
    });
    $(document).on('select2:select', '#users-mails', function(e) {
        var userName = e.params.data.id;
        if (userName != undefined && userName != __('Add email address') && userName != '') {
            $('.send-form').find('.send-sender-block').append('<span class="send-email-block">' + '<input  type=hidden name=emails_' + Q4U.timestamp() + ' value="' + userName + '">' + '<span class="send-email-block-txt">' + userName + '</span><i class="q4bikon-close close-email-block"></i>' + '</span>');
        }
        $('#users-mails').val('').trigger('change');
    });
    $(document).on('click', '.close-email-block', function() {
        $(this).closest('.send-email-block').remove();
    });
    $(document).on('keypress', '.qc-id-to-show', function(e) {
        var val = $(this).val();
        var keycode = e.keyCode || e.which
        var submit = $(this).closest('div').find('.qc-id-submit');
        if (keycode == 13) {
            if (val.length && !isNaN(val)) {
                if (!AJAX_LOADS) submit.trigger('click')
                AJAX_LOADS = true;
                $(this).blur()
            }
            if (e.preventDefault) {
                e.preventDefault();
            } else {
                return false;
            }
        }
    });
    $(document).on('keyup', '.qc-id-to-show', function(e) {
        var val = $(this).val();
        var keycode = e.keyCode || e.which
        var submit = $(this).closest('div').find('.qc-id-submit');
        if (keycode != 13) {
            if (val.length && !isNaN(val)) {
                submit.removeClass('disabled-input')
            } else {
                submit.addClass('disabled-input')
            }
        }
    });
    $(document).on('click', '.qc-id-submit', function(e) {
        e.preventDefault();
        var modalId = $(this).data('modalid');
        var qcId = $(this).closest('div').find('.qc-id-to-show').val();
        var url = $(this).data('url') + '/' + qcId;
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
            errorCallback: function(data) {
                console.log(data)
            },
            ajaxErrorCallback: function(event, jqxhr, settings, thrownError) {
                Q4U.alert(__('Not found'), {
                    type: "danger",
                    confirmText: __("OK")
                });
            },
        });
    });
    $(document).on('click', '.qc-to-print-btn', function(e) {
        e.preventDefault();
        if($('body > #for-print').length < 1){
            $('body').append('<div id="for-print"></div>');
        }else{
            $('#for-print').html('');
        }
        var printable = $(document).find('.print-quality-control');
        $('#for-print').html(printable);
        $('.modal').modal('hide');
        setTimeout(function() {
            window.print();
        }, 400);
    });
    $(document).on('change', '.qc-status', function() {
        var value = $(this).val();
        if (value == 'repaired') {
            if ($(document).find('.modal .q4-status-select').val() == "for_repair") {
                $(document).find(".q4-status-select option[value='approved']").remove();
                $(document).find('.q4-status-select').append('<option class="q4-status-approved" value="approved">' + __('approved') + '</option>');
                $(document).find('.modal .q4-status-select').val('approved').trigger('change');
            }
        } else {
            var status = $(document).find('.modal .q4-status-select').data('status');
            $(document).find('.modal .q4-status-select').val(status).trigger('change');
        }
        $(document).find(".q4-status-select option[value='approved']").remove();
        if (value == 'invalid') {
            $(document).find('.property-quality-control-conditions .q4-form-input').removeClass('disabled-input');
            //$(document).find('.q4-status-select').addClass('disabled-input');
        } else {
            $(document).find('.property-quality-control-conditions .q4-form-input').addClass('disabled-input');
            $(document).find('.q4-status-select').append('<option class="q4-status-approved" value="approved">' + __('approved') + '</option>');
            if ($(this).data('selected') == 'invalid' && value != 'existing') {
                $(document).find('.modal .q4-status-select').val('approved').trigger('change');
            }
        }
    });
    $(document).on('change', '.q4-status-select', function() {
        var self = $(this);
        var value = self.val();
        self.removeClass('q4-status-waiting').removeClass('q4-status-for_repair').removeClass('q4-status-approved');
        self.addClass('q4-status-' + value);
    });
    /**** LOAD SINGLE IMAGE ***/
    $(document).on('click', '.modal-load-single-image', function(e) {
        e.preventDefault();
        var self = $(this);
        self.closest('.modal-images-list-box').find('.load-single-image-input').trigger('click');
    });
    $(document).on('change', '.load-single-image-input', function() {
        var input = $(this);
        $(document).find('.modal').find('a.q4_form_submit').removeClass('disabled-gray-button');
        if (input[0].files != undefined && input[0].files[0] != undefined) {
            var fileName = input[0].files[0].name;
            input.closest('.modal').find('.modal-file-name').replaceWith('<span class="modal-file-name">' + fileName + '</span>');
        }
    });
    $(document).on('profileUpdated', function(e, data) {
        $(document).find('.send-email-ad').addClass('not-active');
        $(document).find("#user-prf-modal .modal-footer").prepend("<span class='user-prf-message'>" + "<span class='user-prf-m-tick'><i class='q4bikon-tick'></i></span>" + "<span class='user-prf-m-txt'>" + __('Profile updated successfully') + "</span>" + "</span>");
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
            innerWidth += self.width() + 20;
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

function ClickCheckBox(self) {
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
        self.closest('.advanced-reports').find('.floors-list .over-select').removeClass('disabled-input');
        var submit = $(document).find('.q4_form_submit');
        submit.removeClass('disabled-input')
        submit.removeClass('disabled-gray-button')
        if (result.length <= 0) {
            result = "<span class='select-def-text'>" + __('Please select') + "</span>";
            self.closest('.advanced-reports').find('.floors-list .over-select').addClass('disabled-input');
            $(document).find('.generate-reports .q4_form_submit').addClass('disabled-input')
        }
        if (self.parents().hasClass('bottom-hidden-select')) {
            $('#' + tableID).find('[data-row-id="' + tableRowId + '"]').find('.select-imitation-title').html(result);
        } else {
            self.closest('.checkbox-list').siblings('.select-imitation').find('.select-imitation-title').html(result);
            self.closest('.checkbox-list-no-scroll').siblings('.select-imitation').find('.select-imitation-title').html(result);
        }
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
var dir = document.getElementsByTagName('html')[0].classList.toString();
var language = document.getElementsByTagName('html')[0].lang;
window.EDITOR_CONFIG = {
    // width: currentImageWidth,
    // height: currentImageHeight,
    selectionOptions:{
        // borderColor: 'red',
        // padding: 0,
        // cornerStyle: 'circle',
        // cornerBackgroundColor: 'rgba(0,0,0,.5)',
        // cornerBorderColor: 'green',
        // cornerSize: 10,
        // shiftRotateSnapAngle: 45
    },
    activeTool: 'select',
    tools: {
        select: {
            properties: [
                'transform-canvas',
            ],
            selection:[]
        },
        brush: {
            default: 'CPencilBrush',
            width: 6,
            color: '#ff0000',
            shadow: {
                blur: 0,
                color: '#000',
                offsetX: 0,
                offsetY: 0,
            },
            properties: [
                'stroke-width',
                'color',
                // 'brush-style',
                // 'shadow-blur',
                // 'shadow-color',
                // 'shadow-offsets',
            ],
            selection: [
                'stroke-width',
                'color',
                // 'shadow-blur',
                // 'shadow-color',
                // 'shadow-offsets',
                // 'object-alignment',
                // 'object-options',
            ]
        },
        shape: {
            properties: [
                'shapes',
            ],
            selection: [
                'stroke-width',
                'color',
                // 'fill',
                // 'shadow-blur',
                // 'shadow-color',
                // 'shadow-offsets',
                // 'object-alignment',
                // 'object-options',
            ]
        },
        line: {
            width: 2,
            color: '#ff0000',
            shadow: {
                blur: 0,
                color: '#000',
                offsetX: 0,
                offsetY: 0,
            },
            properties: [
                'stroke-width',
                'color',
                // 'shadow-blur',
                // 'shadow-color',
                // 'shadow-offsets',
            ],
            selection: [
                'stroke-width',
                'color',
                // 'shadow-blur',
                // 'shadow-color',
                // 'shadow-offsets',
                // 'object-alignment',
                // 'object-options',
            ]
        },
        path:{
            width: 2,
            color: '#ff0000',
            shadow: {
                blur: 0,
                color: '#000',
                offsetX: 0,
                offsetY: 0,
            },
            properties: [
                'stroke-width',
                'color',
                'fill',
                'shadow-blur',
                'shadow-color',
                'shadow-offsets',
            ],
            selection: [
                'stroke-width',
                'color',
                'fill',
                'shadow-blur',
                'shadow-color',
                'shadow-offsets',
                'object-alignment',
                'object-options',
            ]
        },
        text: {
            direction: 'rtl',
            fontFamily: 'Arial',
            fontSize: 18,
            lineHeight: 1.16,
            charSpacing: 0,
            textAlign: 'left',
            stroke: 0,
            fill: '#ff0000',
            color: '#ff0000',
            shadow: {
                blur: 0,
                color: '#000',
                offsetX: 0,
                offsetY: 0,
            },
            fontPickerOptions: {
                selectText: 'Click To Select Font',
                searchBar: true,
                googleApiKey: 'AIzaSyCfG7HltZN2dmFyP9JlVQvea7XwRnqb0ww',
                loadOnly: [],
                defaultFonts: [
                    'Arial',
                    'Arial Black',
                    'Verdana',
                    'Tahoma',
                    'Trebuchet MS',
                    'Impact',
                    'Times New Roman',
                    'Georgia',
                    'Courier ',
                    'Lucida Console',
                    'Comic Sans MS',
                ],
                maxFonts: 150,
                includeDefaultFonts: true
            },
            properties: [
                // 'font-family',
                'font-size',
                // 'char-spacing',
                // 'stroke-width',
                // 'color',
                'fill',
                // 'shadow-blur',
                // 'shadow-color',
                // 'shadow-offsets',
            ],
            selection: [
                // 'font-family',
                // 'font-style',
                'font-size',
                // 'char-spacing',
                // 'stroke-width',
                // 'color',
                'fill',
                // 'shadow-blur',
                // 'shadow-color',
                // 'shadow-offsets',
                // 'object-alignment',
                // 'object-options',
            ]
        },
        crop: {
            properties: [

            ],
            selection: [
                'aspect-ratio'
            ],
            aspectRatios: [
                '1:1',
                '5:4',
                '4:3',
                '3:2',
                '5:3',
                '16:9',
                '3:1',
                '16:10',
            ]
        },
    },
    i18n:{
        default: language,
        languages: {
            en: {
                'Selection Settings': 'Selection Settings',
                'W': 'W',
                'H': 'H',
                'X': 'X',
                'Y': 'Y',
                'object alignment': 'object alignment',
                'object options': 'object options',
                'delete': 'delete',
                'duplicate': 'duplicate',
                'stroke width': 'Stroke width',
                'brush': 'Brush',
                'brush size': 'Brush size',
                'color': 'Color',
                'brush style': 'brush style',
                'shadow blur': 'shadow blur',
                'shadow color': 'shadow color',
                'offset x': 'offset x',
                'offset y': 'offset y',
                'Simple Brush': 'Simple Brush',
                'Circle Brush': 'Circle Brush',
                'Spray Brush': 'Spray Brush',
                'Thick Brush': 'Thick Brush',
                'Pen Brush': 'Pen Brush',
                'Multiline Brush': 'Multiline Brush',
                'Veil Brush': 'Veil Brush',
                'VLines Brush': 'VLines Brush',
                'HLines Brush': 'HLines Brush',
                'Square Brush': 'Square Brush',
                'Diamond Brush': 'Diamond Brush',
                'Shape': 'Shape',
                'stroke color': 'Stroke color',
                'fill': 'fill',
                'font': 'font',
                'font size': 'Font size',
                'character spacing': 'character spacing',
                'shadow offsets': 'shadow offsets',
                'canvas options': 'Canvas options',
                'select': 'Select',
                'pan': 'pan',
                'text': 'Text',
                'shape':'Shape',
                'line':'Line',
                'path':'path',
                'crop':'Crop',
                'aspect ratio': 'Aspect ratio',
                'selection':'Selection',
                'transform canvas':'Transform canvas',
                'Zoom': 'Zoom',
                'Open': 'Open',
                'Save as': 'Save as',
                'LTR': 'EN',
                'RTL': 'HE',
            },
            ru: {
                'Selection Settings': 'Параметры выборки',
                'W': 'Ш',
                'H': 'В',
                'X': 'X',
                'Y': 'Y',
                'object alignment': 'object alignment',
                'object options': 'опции обЪекта',
                'delete': 'удалить',
                'duplicate': 'дубликат',
                'stroke width': 'Толщина линии',
                'brush': 'Кисть',
                'brush size': 'Размер кисти',
                'color': 'Цвет',
                'brush style': 'стиль кисти',
                'shadow blur': 'размытие тени',
                'shadow color': 'цвет тени',
                'offset x': 'отступ по оси x',
                'offset y': 'отступ по оси y',
                'Simple Brush': 'Простая Кисть',
                'Circle Brush': 'Круглая Кисть',
                'Spray Brush': 'Спрей',
                'Thick Brush': 'Толстая Кисть',
                'Pen Brush': 'Карандаш',
                'Multiline Brush': 'Множественная Кисть',
                'Veil Brush': 'Вуаль',
                'VLines Brush': 'Вертикальный Линии',
                'HLines Brush': 'Горизониальные Линии',
                'Square Brush': 'Квадратная Кисть',
                'Diamond Brush': 'Алмазная Кисть',
                'Shape': 'Форма',
                'stroke color': 'Цвет линии',
                'fill': 'заливка',
                'font': 'шрифт',
                'font size': 'Размер шрифта',
                'character spacing': 'межбуквенное расст.',
                'shadow offsets': 'отступы тени',
                'canvas options': 'Опции холста',
                'select': 'Выбрать',
                'pan': 'двигать',
                'text': 'Текст',
                'shape':'фигура',
                'line':'Линия',
                'path':'путь',
                'crop':'Обрезать',
                'aspect ratio': 'Соотношение сторон',
                'selection':'Выбор',
                'transform canvas':'Изменить холст',
                'Zoom': 'Увеличить',
                'Open': 'Открыть',
                'Save as': 'Сохранить как',
                'LTR': 'EN',
                'RTL': 'HE',
            },
            he: {
                'Selection Settings': 'Selection Settings',
                'W': 'W',
                'H': 'H',
                'X': 'X',
                'Y': 'Y',
                'object alignment': 'object alignment',
                'object options': 'object options',
                'delete': 'delete',
                'duplicate': 'duplicate',
                'stroke width': 'רוחב קו',
                'brush': 'מברשת',
                'brush size': 'גודל המברשת',
                'color': 'צבע',
                'brush style': 'brush style',
                'shadow blur': 'shadow blur',
                'shadow color': 'shadow color',
                'offset x': 'offset x',
                'offset y': 'offset y',
                'Simple Brush': 'Simple Brush',
                'Circle Brush': 'Circle Brush',
                'Spray Brush': 'Spray Brush',
                'Thick Brush': 'Thick Brush',
                'Pen Brush': 'Pen Brush',
                'Multiline Brush': 'Multiline Brush',
                'Veil Brush': 'Veil Brush',
                'VLines Brush': 'VLines Brush',
                'HLines Brush': 'HLines Brush',
                'Square Brush': 'Square Brush',
                'Diamond Brush': 'Diamond Brush',
                'Shape': 'Shape',
                'stroke color': 'צבע קו',
                'fill': 'fill',
                'font': 'font',
                'font size': 'גודל גופן',
                'character spacing': 'character spacing',
                'shadow offsets': 'shadow offsets',
                'canvas options': 'אפשרויות',
                'select': 'בחר',
                'pan': 'להזיז',
                'text': 'טקסט',
                'shape':'צורה',
                'line':'קו',
                'path':'path',
                'crop':'לגזור',
                'aspect ratio': 'יחס ממדים',
                'selection':'בחירה',
                'transform canvas':'אפשרויות משטח',
                'Zoom': 'להגדיל',
                'Open': 'Open',
                'Save as': 'Save as',
                'LTR': 'EN',
                'RTL': 'HE',
            }
        }
    },
    colorPresets: [
        '#000000',
        // '#8A2BE2',
        '#FFD700',
        // '#ADFF2F',
        // '#CD5C5C',
        // '#FF1493',
        '#32CD32',
        // '#FFE4C4',
        // '#C0C0C0',
        // '#7FFFD4',
        // '#FF8C00',
        // '#ADD8E6',
        // '#778899',
        // '#FAF0E6',
        // '#BA55D3',
        '#000080',
        '#ff0000',
    ],
    shapes: [
        `<svg viewBox="-10 -10 120 120" name="shape1"><polygon points="0 0, 0 100, 100 100, 100 0" stroke-width="8" stroke="#000" fill="none"></polygon></svg>`,
        // `<svg viewBox="-8 -8 120 120" name="shape2"><polygon fill="none" stroke-width="8" stroke="black" points="50 0, 85 50, 50 100, 15 50"></polygon></svg>`,
        // `<svg viewBox="-10 -10 120 120" name="shape3"><polygon points="25 0, 0 100, 75 100, 100 0" stroke-width="8" stroke="#000" fill="none"></polygon></svg>`,
        // `<svg viewBox="-8 -8 120 120" name="shape4"><polygon points="0,100 30,10 70,10 100,100" stroke-width="8" stroke="#000" fill="none"></polygon></svg>`,
        // `<svg viewBox="-10 -10 120 120" name="shape5"><path d="M 80,80 V 20 H 20 v 60 z m 20,20 V 0 H 0 v 100 z" stroke-width="8" stroke="#000" fill-rule="evenodd" fill="none"></path></svg>`,
        // `<svg viewBox="0 0 100 100" name="shape6"><polygon points="26,86 11.2,40.4 50,12.2 88.8,40.4 74,86 " stroke="#000" stroke-width="8" fill="none"></polygon></svg>`,
        // `<svg viewBox="0 0 100 100" name="shape7"><polygon points="30.1,84.5 10.2,50 30.1,15.5 69.9,15.5 89.8,50 69.9,84.5" stroke-width="8" stroke="#000" fill="none"></polygon></svg>`,
        // `<svg viewBox="0 0 100 100" name="shape8"><polygon points="34.2,87.4 12.3,65.5 12.3,34.5 34.2,12.6 65.2,12.6 87.1,34.5 87.1,65.5 65.2,87.4" stroke-width="8" stroke="#000" fill="none"></polygon></svg>`,
        // `<svg viewBox="0 0 100 100" name="shape9"><polygon points="11.2,70 11.2,40 50,12.2 88.8,40 88.8,70" stroke="#000" stroke-width="8" fill="none"></polygon></svg>`,
        // `<svg viewBox="0 0 100 100" name="shape10"><polygon points="10.2,70 10.2,35 30.1,15 69.9,15 89.8,35 89.8,70" stroke-width="8" stroke="#000" fill="none"></polygon></svg>`,
        // `<svg viewBox="-10 -10 120 120" name="shape11"><polygon points="50 15, 100 100, 0 100" stroke-width="8" stroke="#000" fill="none"></polygon></svg>`,
        // `<svg viewBox="-10 -10 120 120" name="shape12"><polygon points="0 0, 100 100, 0 100" stroke-width="8" stroke="#000" fill="none"></polygon></svg>`,
        // `<svg viewBox="-10 -10 120 120" name="shape13"><path d="M 26,85 50,45 74,85 Z m -26,15 50,-85 50,85 z" stroke-width="8" stroke="#000" fill="none"></path></svg>`,
        // `<svg viewBox="8 50 100 100" name="shape14"><path d="M 62.68234,131.5107 H 26.75771 V 96.075507 Z M 11.572401,146.76255 V 59.66782 l 87.983665,87.09473 z" stroke-width="8" stroke="#000" fill="none" fill-rule="evenodd"></path></svg>`,
        `<svg viewBox="0 0 180 180" name="shape15"><path d=" M 100, 100 m -75, 0 a 75,75 0 1,0 150,0 a 75,75 0 1,0 -150,0" stroke-width="8" stroke="#000" fill="none"/></svg>`,
        //`<svg name="shape16" x="0px" y="0px" viewBox="0 0 96 120" xml:space="preserve"><path stroke="#000" stroke-width="8" fill="none" d="M9.113,65.022C11.683,45.575,28.302,30.978,48,30.978c19.696,0,36.316,14.598,38.887,34.045H9.113z"></path></svg>`,
        // `<svg viewBox="-15 -15 152 136" name="shape17"><path stroke="#000000" stroke-width="8" d="m0 0l57.952755 0l0 0c32.006428 -1.4055393E-14 57.952755 23.203636 57.952755 51.82677c0 28.623135 -25.946327 51.82677 -57.952755 51.82677l-57.952755 0z" fill="none"></path></svg>`,
        // `<svg viewBox="-5 -50 140 140" name="shape18"><path stroke="#000000" stroke-width="8" d="m20.013628 0l84.37401 0l0 0c11.053215 -1.04756605E-14 20.013626 9.282301 20.013626 20.7326c0 11.450296 -8.960411 20.7326 -20.013626 20.7326l-84.37401 0l0 0c-11.053222 0 -20.013628 -9.282303 -20.013628 -20.7326c-5.2380687E-15 -11.450298 8.960406 -20.7326 20.013628 -20.7326z" fill="none"></path></svg>`,
        // `<svg viewBox="-8 -8 136 136" name="shape19"><path stroke="#000000" stroke-width="8" d="m0 51.82677l0 0c0 -28.623135 23.203636 -51.82677 51.82677 -51.82677l0 0c13.745312 0 26.927654 5.4603047 36.64706 15.17971c9.719406 9.719404 15.17971 22.901749 15.17971 36.64706l0 0c0 28.623135 -23.203636 51.82677 -51.82677 51.82677l0 0c-28.623135 0 -51.82677 -23.203636 -51.82677 -51.82677zm25.913385 0l0 0c0 14.311565 11.60182 25.913387 25.913385 25.913387c14.311565 0 25.913387 -11.601822 25.913387 -25.913387c0 -14.311565 -11.601822 -25.913385 -25.913387 -25.913385l0 0c-14.311565 0 -25.913385 11.60182 -25.913385 25.913385z" fill="none"></path></svg>`,
        // `<svg viewBox="-7 -35 133 105" name="shape20"><path stroke="#000000" stroke-width="8" d="m0 57.952755l0 0c0 -32.006424 25.946333 -57.952755 57.952755 -57.952755c32.006428 0 57.952755 25.946333 57.952755 57.952755l-28.97638 0c0 -16.003212 -12.97316 -28.976377 -28.976376 -28.976377c-16.003212 0 -28.976377 12.9731655 -28.976377 28.976377z" fill="none"></path></svg>`,
        // `<svg viewBox="-10 -10 150 150" name="shape21" fill="none" stroke="none" stroke-linecap="square" stroke-miterlimit="10"><path stroke="#000000" stroke-width="8" stroke-linejoin="round" stroke-linecap="butt" d="m0 51.82677l42.665005 -9.161766l9.161766 -42.665005l9.161766 42.665005l42.665005 9.161766l-42.665005 9.161766l-9.161766 42.665005l-9.161766 -42.665005z" fill-rule="evenodd" fill="none"></path></svg>`,
        // `<svg viewBox="-15 -15 137 130" name="shape22"><path stroke="#000000" stroke-width="8" d="m1.09633125E-4 37.631077l39.59224 2.632141E-4l12.234421 -37.63134l12.234425 37.63134l39.59224 -2.632141E-4l-32.030952 23.257183l12.234924 37.631172l-32.030636 -23.257607l-32.03064 23.257607l12.234926 -37.631172z" fill="none"></path></svg>`,
        // `<svg viewBox="-10 -10 150 150" name="shape23" fill="none" stroke="none" stroke-linecap="square" stroke-miterlimit="10"><path stroke="#000000" stroke-width="8" stroke-linejoin="round" stroke-linecap="butt" d="m0 59.82677l27.527777 -8.654488l-19.512508 -21.258898l28.167 6.268881l-6.268881 -28.167l21.258898 19.512508l8.654488 -27.527777l8.654491 27.527777l21.258896 -19.512508l-6.2688828 28.167l28.167 -6.268881l-19.512512 21.258898l27.527779 8.654488l-27.527779 8.654491l19.512512 21.258896l-28.167 -6.2688828l6.2688828 28.167l-21.258896 -19.512512l-8.654491 27.527779l-8.654488 -27.527779l-21.258898 19.512512l6.268881 -28.167l-28.167 6.2688828l19.512508 -21.258896z" fill-rule="evenodd"></path></svg>`,
        // `<svg viewBox="-10 -10 150 150" name="shape24" fill="none" stroke="none" stroke-linecap="square" stroke-miterlimit="10"><path stroke="#000000" stroke-width="8" stroke-linejoin="round" stroke-linecap="butt" d="m0 59.82677l33.496998 -3.4664993l-31.45845 -12.017807l33.252647 5.321434l-27.275928 -19.750513l30.742428 13.746485l-21.234838 -26.137014l26.137014 21.234838l-13.746485 -30.742428l19.750513 27.275928l-5.321434 -33.252647l12.017807 31.45845l3.4664993 -33.496998l3.4664993 33.496998l12.017811 -31.45845l-5.321434 33.252647l19.750511 -27.275928l-13.746483 30.742428l26.137009 -21.234838l-21.234833 26.137014l30.742424 -13.746485l-27.275925 19.750513l33.252647 -5.321434l-31.45845 12.017807l33.496994 3.4664993l-33.496994 3.4664993l31.45845 12.017811l-33.252647 -5.321434l27.275925 19.750511l-30.742424 -13.746483l21.234833 26.137009l-26.137009 -21.234833l13.746483 30.742424l-19.750511 -27.275925l5.321434 33.252647l-12.017811 -31.45845l-3.4664993 33.496994l-3.4664993 -33.496994l-12.017807 31.45845l5.321434 -33.252647l-19.750513 27.275925l13.746485 -30.742424l-26.137014 21.234833l21.234838 -26.137009l-30.742428 13.746483l27.275928 -19.750511l-33.252647 5.321434l31.45845 -12.017811z" fill-rule="evenodd"></path></svg>`,
        // `<svg viewBox="-10 -10 150 150" name="shape25" fill="none" stroke="none" stroke-linecap="square" stroke-miterlimit="10"><path stroke="#000000" stroke-width="8" stroke-linejoin="round" stroke-linecap="butt" d="m0 59.82677l9.952638 -6.5662766l-7.91409 -8.91803l11.312678 -3.7663116l-5.3359585 -10.662767l11.902236 -0.7101288l-2.3946476 -11.680401l11.680401 2.3946476l0.7101288 -11.902236l10.662767 5.3359585l3.7663116 -11.312678l8.91803 7.91409l6.5662766 -9.952638l6.5662804 9.952638l8.91803 -7.91409l3.7663116 11.312678l10.6627655 -5.3359585l0.7101288 11.902236l11.680397 -2.3946476l-2.3946457 11.680401l11.902237 0.7101288l-5.3359604 10.662767l11.312683 3.7663116l-7.914093 8.91803l9.952637 6.5662766l-9.952637 6.5662804l7.914093 8.91803l-11.312683 3.7663116l5.3359604 10.6627655l-11.902237 0.7101288l2.3946457 11.680397l-11.680397 -2.3946457l-0.7101288 11.902237l-10.6627655 -5.3359604l-3.7663116 11.312683l-8.91803 -7.914093l-6.5662804 9.952637l-6.5662766 -9.952637l-8.91803 7.914093l-3.7663116 -11.312683l-10.662767 5.3359604l-0.7101288 -11.902237l-11.680401 2.3946457l2.3946476 -11.680397l-11.902236 -0.7101288l5.3359585 -10.6627655l-11.312678 -3.7663116l7.91409 -8.91803z" fill-rule="evenodd"></path></svg>`,
        // `<svg viewBox="-10 -40 140 140" name="shape26" fill="none" stroke="none" stroke-linecap="square" stroke-miterlimit="10"><path stroke="#000000" stroke-width="8" stroke-linejoin="round" stroke-linecap="butt" d="m0 14.960629l89.732285 0l0 -14.960629l29.921257 29.921259l-29.921257 29.921259l0 -14.9606285l-89.732285 0z" fill-rule="evenodd"></path></svg>`,
        // `<svg viewBox="-10 -60 180 180" name="shape27" fill="none" stroke="none" stroke-linecap="square" stroke-miterlimit="10"><path stroke="#000000" stroke-width="8" stroke-linecap="butt" d="m0 32.238846l27.590551 -27.590551l0 13.795275l82.80315 0l0 -13.795275l27.590553 27.590551l-27.590553 27.59055l0 -13.795273l-82.80315 0l0 13.795273z" fill-rule="evenodd"></path></svg>`,
        // `<svg viewBox="-10 -10 150 150" name="shape28" fill="none" stroke="none" stroke-linecap="square" stroke-miterlimit="10"><path stroke="#000000" stroke-width="8" stroke-linecap="butt" d="m0.005249344 89.74016l29.913385 -29.913387l0 14.956692l44.87008 0l0 -44.87008l-14.956692 0l29.913387 -29.913385l29.913383 29.913385l-14.956688 0l0 74.78347l-74.78347 0l0 14.956688z" fill-rule="evenodd"></path></svg>`,
        // `<svg viewBox="-10 -20 200 200" name="shape29" fill="none" stroke="none" stroke-linecap="square" stroke-miterlimit="10"><path stroke="#000000" stroke-width="8" stroke-linecap="butt" d="m0.005249344 89.74016l29.913385 -29.913387l0 14.956692l40.35827 0l0 -44.87008l-14.956692 0l29.913387 -29.913385l29.913383 29.913385l-14.956688 0l0 44.87008l40.35826 0l0 -14.956692l29.913391 29.913387l-29.913391 29.913383l0 -14.956688l-110.62992 0l0 14.956688z" fill-rule="evenodd"></path></svg>`,
        // `<svg viewBox="-10 -10 150 150" name="shape30" fill="none" stroke="none" stroke-linecap="square" stroke-miterlimit="10"><path stroke="#000000" stroke-width="8" stroke-linecap="butt" d="m0.005249344 59.82677l26.922047 -19.30849l0 9.424511l23.020744 0l0 -23.020744l-9.424511 0l19.30849 -26.922047l19.30849 26.922047l-9.424507 0l0 23.020744l23.020744 0l0 -9.424511l26.922043 19.30849l-26.922043 19.30849l0 -9.424507l-23.020744 0l0 23.020744l9.424507 0l-19.30849 26.922043l-19.30849 -26.922043l9.424511 0l0 -23.020744l-23.020744 0l0 9.424507z" fill-rule="evenodd"></path></svg>`,
        //`<svg viewBox="-10 -10 158 136" name="shape31" fill="none" stroke="none" stroke-linecap="square" stroke-miterlimit="10"><path stroke="#000000" stroke-width="8" stroke-linecap="butt" d="m0 77.22078l81.043304 0l0 -51.480316l-12.870079 0l25.740158 -25.740158l25.740158 25.740158l-12.870079 0l0 77.220474l-106.78346 0z" fill-rule="evenodd"></path></svg>`,
        //`<svg viewBox="-10 -10 136 136" name="shape32" fill="none" stroke="none" stroke-linecap="square" stroke-miterlimit="10"><path stroke="#000000" stroke-width="8" stroke-linecap="butt" d="m0 102.96063l0 -57.915356l0 0c0 -24.87782 20.167458 -45.045277 45.045277 -45.045277l0 0l0 0c11.946751 0 23.404194 4.7458277 31.851818 13.193456c8.447632 8.447627 13.193459 19.905071 13.193459 31.851822l0 6.4350395l12.870079 0l-25.740158 25.740158l-25.740158 -25.740158l12.870079 0l0 -6.4350395c0 -10.661922 -8.643196 -19.305119 -19.305119 -19.305119l0 0l0 0c-10.661922 0 -19.305119 8.643196 -19.305119 19.305119l0 57.915356z" fill-rule="evenodd"></path></svg>`,
        //`<svg viewBox="-10 -10 180 180" name="shape33" fill="none" stroke="none" stroke-linecap="square" stroke-miterlimit="10"><path stroke="#000000" stroke-width="8" stroke-linecap="butt" d="m0 0l25.742783 0l0 0l38.614174 0l90.09974 0l0 52.74803l0 0l0 22.6063l0 15.070862l-90.09974 0l-61.5304 52.813744l22.916225 -52.813744l-25.742783 0l0 -15.070862l0 -22.6063l0 0z" fill-rule="evenodd"></path></svg>`,
        //`<svg viewBox="-10 -10 180 180" name="shape34" fill="none" stroke="none" stroke-linecap="square" stroke-miterlimit="10"><path stroke="#000000" stroke-width="8" stroke-linejoin="round" stroke-linecap="butt" d="m1.0425826 140.35696l25.78009 -49.87359l0 0c-30.142242 -17.309525 -35.62507 -47.05113 -12.666686 -68.71045c22.958385 -21.65932 66.84442 -28.147947 101.387596 -14.990329c34.543175 13.1576185 48.438576 41.655407 32.10183 65.83693c-16.336761 24.181526 -57.559166 36.132935 -95.233955 27.61071z" fill-rule="evenodd"></path></svg>`,
        //`<svg viewBox="0 -5 100 100" x="0px" y="0px" name="shape34"><path fill="none" stroke="#000" stroke-width="8" d="M55.2785222,56.3408313 C51.3476874,61.3645942 45.2375557,64.5921788 38.3756345,64.5921788 C31.4568191,64.5921788 25.3023114,61.3108505 21.3754218,56.215501 C10.6371566,55.0276798 2.28426396,45.8997866 2.28426396,34.8156425 C2.28426396,27.0769445 6.35589452,20.2918241 12.4682429,16.4967409 C14.7287467,7.0339786 23.2203008,0 33.3502538,0 C38.667844,0 43.5339584,1.93827732 47.284264,5.14868458 C51.0345695,1.93827732 55.9006839,0 61.2182741,0 C73.0769771,0 82.6903553,9.6396345 82.6903553,21.5307263 C82.6903553,22.0787821 82.6699341,22.6220553 82.629813,23.1598225 C87.1459866,27.1069477 90,32.9175923 90,39.396648 C90,51.2877398 80.3866218,60.9273743 68.5279188,60.9273743 C63.5283115,60.9273743 58.9277995,59.2139774 55.2785222,56.3408313 L55.2785222,56.3408313 Z M4.79695431,82 C7.44623903,82 9.59390863,80.6668591 9.59390863,79.0223464 C9.59390863,77.3778337 7.44623903,76.0446927 4.79695431,76.0446927 C2.1476696,76.0446927 0,77.3778337 0,79.0223464 C0,80.6668591 2.1476696,82 4.79695431,82 Z M13.7055838,71.9217877 C18.4995275,71.9217877 22.3857868,69.4606044 22.3857868,66.424581 C22.3857868,63.3885576 18.4995275,60.9273743 13.7055838,60.9273743 C8.91163999,60.9273743 5.02538071,63.3885576 5.02538071,66.424581 C5.02538071,69.4606044 8.91163999,71.9217877 13.7055838,71.9217877 Z"></path></svg>`
//         `<svg  stroke-width="8" width="30" height="30" stroke="#000" name="shape35" viewBox="0 0 81 79" fill="none" xmlns="http://www.w3.org/2000/svg">
// <path  d="M69.1892 36.5325C55.8521 33.491 56.7937 33.5591 56.4043 35.6059C55.7338 37.0622 59.639 37.0814 2.78876 37.3148C2.78876 37.3148 1.67069 37.8603 1.56417 38.973C1.42939 40.381 2.45147 40.8341 2.45147 40.8341C56.8043 40.7166 56.6702 41.8025 56.6693 42.1243C56.6664 43.1249 56.7382 43.5875 57.0161 43.5864C59.0675 43.5728 80.0039 39.8227 80.0262 39.4647C80.0423 39.2155 75.1648 37.8952 69.1892 36.5325Z" fill="none"/>
// </svg>`,
        '<svg stroke-width="8" stroke="#000" name="shape36" viewBox="0 0 89 9" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M66.0004 0L66.0165 3.00003H2.00001C2.00001 3.00003 0 3 0 4.5C0 6 2.00001 6 2.00001 6H66.0165V9C66.0165 9 88.9782 4.858 89.0005 4.5C89.0166 4.2508 71.976 1.3627 66.0004 0Z" fill="none"/></svg>'
    ],
    shortcuts: {
        moveLeft: 37,
        moveRight: 39,
        moveTop: 38,
        moveBottom: 40,
        rotate45: 37,
        rotate45m: 39,
        rotate180: 38,
        rotate180m: 40,
        undo: 90,
        redo: 89,
        selectAll: 65,
        duplicate: 68,
        openFile: 79,
        delete: 46,
        selectTool: 86,
        textTool: 84,
        brushTool: 66,
        shapeTool: 83,
        lineTool: 76,
        pathTool: 80,
        zoomIn: 187,
        zoomIn1: 107,
        zoomOut: 189,
        zoomOut1: 109,
        zoomReset: 96,
        zoomReset1: 48,
        zoomReset2: 192,
        pan:32
    },
    toolbar: {
        filename: {
            enabled: false,
            value: 'Pixel Perfect File'
        },
        download:{
            enabled: false,
            formats: {
                jpeg: true,
                png: true,
                svg: true,
                pixp: true
            }
        },
        loadProgress: {
            enabled: false
        },
        zoom: {
            enabled: true
        }
    },
    theme: "light"
};
$(document).ready(function(){
    $('.report_tasks').each(function(){
        $(this).find('.plus-minus').click(function(e){
            e.preventDefault ? e.preventDefault() : e.stopPropagation()
            if($(this).hasClass('q4bikon-plus')){
                $(this).parents('.report_tasks').find('.report_tasks_wraper').show()
                $(this).removeClass('q4bikon-plus').addClass('q4bikon-minus')
            }else{
                $(this).parents('.report_tasks').find('.report_tasks_wraper').hide()
                $(this).removeClass('q4bikon-minus').addClass('q4bikon-plus')
            }

        })
    })
    $('.qc_certificate').each(function(){
        $(this).find('.plus-minus').click(function(e){
            e.preventDefault ? e.preventDefault() : e.stopPropagation()
            if($(this).hasClass('q4bikon-plus')){
                $(this).parents('.qc_certificate').find('.qc_certificate_wraper').show()
                $(this).removeClass('q4bikon-plus').addClass('q4bikon-minus')
            }else{
                $(this).parents('.qc_certificate').find('.qc_certificate_wraper').hide()
                $(this).removeClass('q4bikon-minus').addClass('q4bikon-plus')
            }

        })
    })
})
