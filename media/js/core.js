/**
 * Created by СУРЕН on 19.09.2016.
 */
"use strict";
var Q4U = {};
$.extend(Q4U, {
    options: {
        ajaxFormSelector: 'form[data-ajax="true"]',
        baseUri: $('meta[name="base-uri"]').attr('content'),
        currentUri: $('meta[name="current-uri"]').attr('content'),
        csrfTokenSelector: 'meta[name="tkn"]'
    }
});


Q4U.a = function(i) {
    return i << 1;
};
Q4U.b = function(i) {
    return i << 2
};
Q4U.c = function(i) {
    return i >> 1;
};
Q4U.d = function(i) {
    return i >> 2;
};
Q4U.e = function(i) {
    return Q4U.a(i) + Q4U.b(i);
};
Q4U.f = function(i, y) {
    return Q4U.e(i) - Q4U.c(y);
};
Q4U.g = function(i, y, x) {
    return Q4U.f(i, y) + x;
};
Q4U.h = function() {
    return Q4U.g(0x55, 0x329, 0x32 + 0x32 + 0x16 + 0x16 + 0x6);
};

Q4U.i18n = {
    translations: {},
    init: function(json) {
        Q4U.i18n.translations = json;
    },
    translate: function(str) {
        var fr = '',
            i = 0,
            j = 0,
            lenStr = 0,
            lenFrom = 0,
            fromTypeStr = '',
            toTypeStr = '',
            istr = '';
        var from = [];
        var to = [];
        var ret = '';
        var match = false;
        /*for (fr in Q4U.i18n.translations) {
            if (Q4U.i18n.translations.hasOwnProperty(fr)) {
                from.push(fr);
                to.push(Q4U.i18n.translations[fr]);
            }
        }
        lenStr = str.length;
        lenFrom = from.length;
        fromTypeStr = typeof from === 'string';
        toTypeStr = typeof to === 'string';

        for (i = 0; i < lenStr; i++) {
            match = false;
            if (fromTypeStr) {
                istr = str.charAt(i);
                for (j = 0; j < lenFrom; j++) {
                    if (istr == from.charAt(j)) {
                        match = true;
                        break;
                    }
                }
            } else {
                for (j = 0; j < lenFrom; j++) {
                    if (str.substr(i, from[j].length) == from[j]) {
                        match = true;
                        i = (i + from[j].length) - 1;
                        break;
                    }
                }
            }
            if (match) {
                ret += toTypeStr ? to.charAt(j) : to[j];
            } else {
                ret += str.charAt(i);
            }
        }

        return ret;*/
        for (fr in Q4U.i18n.translations) {
            if (str == fr) return Q4U.i18n.translations[fr];
        }
        return str;
    }
};

/**
 * Переводит строку, если не находит то оставляет так как есть
 * пример:
 * __("hello :user",{":user":"John"}) // возвращает привет John
 * __("hello world!!!") // возвращает привет мир!!!
 * @param str string - строка которую необходимо перевести
 * @param [replacement] object - объек с параметрами для замены в подстроке на значения переменных
 * @returns {string}
 * @private
 */
function __(str, replacement) {
    var trans = Q4U.i18n.translate(str);
    if ((replacement != undefined) && typeof replacement == "object") {
        for (var key in replacement) {
            var val = replacement[key];
            trans = trans.replace(new RegExp(key, "g"), val);
        }
    }
    return trans;
}


/**
 * Возвращает токен csrf
 * @returns {*|jQuery}
 */
Q4U.getCsrfToken = function() {
    return $(Q4U.options.csrfTokenSelector).attr('content');
};

/**
 * Устонавливает токен csrf
 * @param token
 */
Q4U.setCsrfToken = function(token) {
    $(Q4U.options.csrfTokenSelector).attr('content', token);
};

/**
 * Возвращает относительный урл
 * @param uri
 * @returns {*}
 */
Q4U.url = function(uri) {
    if (uri.charAt(0) != '/') {
        uri = Q4U.options.baseUri + uri;
    }
    return uri;
};

/**
 * Возвращает адрес сабмита
 * @param form
 * @returns {*}
 */
Q4U.getFormSubmitUri = function(form) {
    if (!(form instanceof jQuery))
        form = $(form);
    var uri = form.attr('action');
    if (!uri.length) {
        uri = Q4U.options.currentUri;
    }
    return Q4U.url(uri);
};


Q4U.getAjaxResponse = function(data) {
    try {
        // data = JSON.parse(Q4U.Crypt.Aes.Ctr.decrypt(data,document.domain,Q4U.h()));
        data = JSON.parse(data);
    } catch (err) {
        // data = Q4U.Crypt.Aes.Ctr.decrypt(data,document.domain,Q4U.h());
        data = data;
    }

    //$.trigger("Q4UResponseReceived",[data]);
    if (data && data.triggerEvent != undefined) {
        $('body').trigger(data.triggerEvent, [data]);
    }
    //console.log("response start");
    //console.log(data);
    // console.log("response end");
    this.hasErrors = function() {
        return !!data && !!data.errors;
    };

    this.showErrorsMsg = function() {
        if (this.hasErrors()) {
            var errMsg = '';
            $.each(data.errors, function(idx, val) {
                errMsg += val + '\n';
            });
            if (Q4U.alert != undefined)
                Q4U.alert(__(errMsg), { type: 'danger' });
            else
                alert(errMsg);
        }
    };

    this.needRedirect = function() {
        return !!data && !!data.redirect;
    };

    this.doRedirect = function() {
        if (this.needRedirect())
            window.location = data.redirect;
        else
            throw Error('Can\'t redirect to empty path');
    };

    this.getData = function() {
        return data;
    };

    if (this.hasErrors()) {
        this.showErrorsMsg();
    }

    return this;
};


Q4U.timestamp = function() {
    return new Date().getTime();
};










$(document).ready(function() {
    $.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
    /**
     * Событие для форм которые должны работать через Ajax
     */
    // $(Q4U.options.ajaxFormSelector).on('submit',function(e){
    //     var jsonData = $(this).serializeObject();
    //
    //    jsonData['csrf'] = Q4U.getCsrfToken();
    //     console.log("this is post data");
    //     console.log(jsonData);
    //     console.log("post data is end");
    //     $.ajax({
    //         url : Q4U.getFormSubmitUri($(this)),
    //         data: Q4U.Crypt.Aes.Ctr.encrypt(JSON.stringify(jsonData),document.domain,256),
    //         method: 'POST',
    //        // type: 'HTML',
    //         cache: false,
    //         contentType: false,
    //         processData: false,
    //         success : function(response){
    //            var fmResp = Q4U.getAjaxResponse(response);
    //             if(fmResp.hasErrors()){
    //                 fmResp.showErrorsMsg();
    //             }
    //             if(fmResp.needRedirect()){
    //                 fmResp.doRedirect();
    //             }
    //         }
    //     });
    //     e.preventDefault();
    // });

    $(document).on('submit', Q4U.options.ajaxFormSelector, function(e) {
        e.preventDefault();
        console.log($(this).serializeObject());
        var jsonData = $(this).serializeObject();
        jsonData['csrf'] = Q4U.getCsrfToken();
        console.log(jsonData);
        // console.log(Q4U.getFormSubmitUri($(this)));
        //jsonData = Q4U.Crypt.Aes.Ctr.encrypt(JSON.stringify(jsonData),document.domain,Q4U.h());
        jsonData = JSON.stringify(jsonData);

        //Если есть поле для файлов то основные данные данные отправляем в Data инача без неё
        if ($(this).find('input[type="file"]').length) {
            var formData = new FormData();
            var hasFiles = false;
            $(this).find('input[type="file"]').each(function() {
                var inputName = $(this).attr('name');
                $.each($(this)[0].files, function(i, file) {
                    console.log(file);
                    formData.append(inputName, file);
                    hasFiles = true;
                });
            });
            if (hasFiles)
                formData.append('Data', jsonData);
            else
                formData = undefined;
        }

        $.ajax({
            url: Q4U.getFormSubmitUri($(this)),
            data: formData != undefined ? formData : jsonData,
            method: 'POST',
            type: 'HTML',
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                var fmResp = Q4U.getAjaxResponse(response);

                if (fmResp.needRedirect()) {
                    fmResp.doRedirect();
                }
            }
        });

    });
});
