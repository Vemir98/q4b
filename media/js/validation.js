"use strict";
$( document ).ready(function() {
    /**
     * .q4_required - if element is empty
     * .q4_email - if email is valid
     * .q4_select - if select is empty
     * .q4_number - if number is valid
     * .q4_url - if number is valid
    **/
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


    /**
     *  Regular expressions
     *
     *  email_rgx  - validate email address
     *  url_rgx  - validate email URL
     *  number_rgx  - validate email numbers
     **/
    var email_rgx = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    var url_rgx = /^(http|https)?:\/\/[a-zA-Z0-9-\.]+\.[a-z]{2,4}/;
    var number_rgx = /[^\d,.\b].+/;

    /**
     *  Validate email on focusout
     **/

    $('input.q4_email').on('focusout', function () {

        var email_fo = $(this);
        if (!email_rgx.test(email_fo.val())) {

            email_fo.addClass('error');
            if(email_fo.val() == '') {
                email_fo.addClass('error');
                setTimeout(function(){
                    remove_error_messages(email_fo);
                }, 3000);
            } else if(email_fo.val() != '') {

                email_fo.addClass('error');
                setTimeout(function(){
                    remove_error_messages(email_fo);
                }, 3000);
            }else{
                remove_error_messages(email_fo);
            }

        } else {
            email_fo.removeClass('error');
            remove_error_messages(email_fo);
        }
    });


    /**
     *  Validate email on focusout in LOGIN page
     **/
    $('input.q4-login-pass').on('focusin', function () {

        var email_fo = $(this).closest('.q4_form').find('.q4-login-email');
        if (!email_rgx.test(email_fo.val())) {

            email_fo.addClass('error');
            if(email_fo.val() == '') {

                email_fo.addClass('error');
                add_error_messages(email_fo, __("Empty field"));
                setTimeout(function(){
                    remove_error_messages(email_fo);
                }, 3000);
            } else if(email_fo.val() != '') {

                email_fo.addClass('error');
                add_error_messages(email_fo, __("Incorrect Email"));
                setTimeout(function(){
                    remove_error_messages(email_fo);
                }, 3000);
            }else{
                remove_error_messages(email_fo);
            }

        } else {
            email_fo.removeClass('error');
            remove_error_messages(email_fo);
        }
    });


    /**
     *  Validate email on focusin
     **/

    $('input.q4_email').on('focusin', function () {

        var email_fo = $(this);
        if (email_fo.val() != '' && email_rgx.test(email_fo.val())) {

            email_fo.removeClass('error');
            remove_error_messages(email_fo);
        } else if(email_rgx.test(email_fo.val())){

            email_fo.removeClass('error');
            remove_error_messages(email_fo);
        } else if(!email_rgx.test(email_fo.val())) {

            if(email_fo.val() == '') {
                email_fo.addClass('error');
                add_error_messages(email_fo, empty_input[_current_lang]);
            }else{
                email_fo.addClass('error');
                add_error_messages(email_fo, incorrect_email[_current_lang]);
            }
        }

    });

    /**
     *  Validate email on keyup
     **/

    $('input.q4_email').on('keyup', function () {

        var email_fo = $(this);
        if (email_fo.val() != '' && email_rgx.test(email_fo.val())) {

            email_fo.removeClass('error');
            remove_error_messages(email_fo);
        } else if(email_rgx.test(email_fo.val())){

            email_fo.removeClass('error');
            remove_error_messages(email_fo);
        }  else if(!email_rgx.test(email_fo.val())){

            if(email_fo.val() == '') {
                email_fo.addClass('error');
                add_error_messages(email_fo, empty_input[_current_lang]);
            }else{
                email_fo.addClass('error');
                add_error_messages(email_fo, incorrect_email[_current_lang]);
            }
        }
    });

    $('input.q4_number').on('keyup', function () {

        var input = $(this);
        input.val(input.val().replace(number_rgx, "")); // allow only numbers, dots, commas and backslash
    });

    /**
     *  Validate from on submit
     **/
    $(document).on('click','.q4_form_submit', function(event){

        event.preventDefault();

        var submit = $(this);
        var is_valid = true;
        var is_valid_email = true;

        submit.closest('.q4_form').find('.q4_required').each(function() {

            var element = $(this);

            if (element.val().length == 0) {
                element.addClass('error');
                add_error_messages(element, empty_input[_current_lang]);
                is_valid = false;
            } else {
                element.removeClass('error');
                remove_error_messages(element);
            }

        });

        submit.closest('.q4_form').find('input.q4_email').each(function() {

            var email_fo = $(this);
            if (email_fo.val() == '') {
                email_fo.addClass('error');
                add_error_messages(email_fo, empty_input[_current_lang]);
                is_valid = false;
            } else if(!email_rgx.test(email_fo.val())){
                email_fo.addClass('error');
                add_error_messages(email_fo, incorrect_email[_current_lang]);
                is_valid = false;
            }else {
                email_fo.removeClass('error');
                remove_error_messages(email_fo);
            }

        });

        submit.closest('.q4_form').find('select.q4_select').each(function() {
            var element = $(this);
            if (!element.val()) {
                element.addClass('error');
                add_error_messages(element, empty_select[_current_lang])
                is_valid = false;
            } else {
                element.removeClass('error');
                remove_error_messages(element);
            }
        });

        submit.closest('.q4_form').find('input.q4_url').each(function () {

            var form_url = $(this);

            if (form_url.val() != '' && !url_rgx.test(form_url.val())) {
                form_url.addClass('error');
                add_error_messages(form_url, incorrect_url[_current_lang]);
                is_valid = false;
            } else {
                form_url.removeClass('error');
                remove_error_messages(form_url);
            }
        });

        submit.closest('.q4_form').find('input.q4_password').each(function () {

            var set_password = $(this);

            if (set_password.val() != '' && set_password.val().length < 8) {
                set_password.addClass('error');
                set_password.closest('.q4_form').find(".check_password_match").html(short_password[_current_lang]);
                is_valid = false;
            } else {
                set_password.removeClass('error');
                set_password.closest('.q4_form').find(".check_password_match").html(" ");
            }
        });

        $('input.q4_required').on('keyup', function () {

            var input = $(this);
            if (input.val() != '' && input.val().length > 0) {
                input.removeClass('error');
                remove_error_messages(input)
            }
        });
        $('textarea.q4_required').on('keyup', function () {

            var input = $(this);
            if (input.val() != '' && input.val().length > 0) {
                input.removeClass('error');
                remove_error_messages(input)
            }
        });

        $('select.q4_select').on('change', function () {

            var select = $(this);

            if (select.val() == '') {
                select.addClass('error');
                add_error_messages(select, empty_select[_current_lang]);
                is_valid = false;
            } else {
                select.removeClass('error');
                remove_error_messages(select);
            }
        });

        if(is_valid){

            if(submit.closest('form').data('submit') == undefined){
                submit.closest('.q4_form').submit();
            }
        }

    }); // on submit


    /**
     * Password match validation
     **/
    function check_password_match() {
        var password = $("#q4_new_password").val();
        var confirm_password = $("#q4_confirm_password").val();

        if (password != confirm_password){
            $(".check_password_match").html(password_match[_current_lang]);
            add_error_messages(element, password_match[_current_lang]);
        } else if(password.length < 8 && confirm_password.length < 8){
            $(".check_password_match").html(short_password[_current_lang]);
        } else {
            $(".check_password_match").html(" ");
        }

    }

    $("#q4_confirm_password").on('keyup', check_password_match);

    /**
     * Add error messages to form fields
     **/

    function add_error_messages(element, message){

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
            element.closest('.error-handler').prepend('<div class="q4_error_message">' +
                message +
            '</div>');
        }
    }


    /**
     * Remove error messages from form fields
     **/
    function remove_error_messages(element){

        if(element.closest('.form-group').length == 1){
            element.closest('.form-group').find('.q4_error_message').remove('.q4_error_message');
        }
        if(element.closest('td').length == 1){
            element.closest('td').find('.q4_error_message').remove('.q4_error_message');
        }
        if(element.siblings('ul').length==1){
            element.closest('.error-handler').find('.q4_error_message').remove('.q4_error_message');;
        }
    }



    /**
     * Choose language in login page
     */

    $(document).on('click', '.select-language .default-option', function (e) {

        var self = $(this).closest('.select-language');
        self.find('.options').toggle();

        e.stopPropagation();
        e.preventDefault();
    });

    $(document).on('click', '.select-language .option', function (e) {

        var $this = $(this);
        var self = $this.closest('.select-language');
        var $defaultOption = $('.select-language .default-option');
        var option = $this.html();
        var optionNumber = $this.index() - 1;
        var defOption = $defaultOption.find('.option:first-child').html();

        self.find('.option').each(function () {
            $(this).find('input[type=radio]').removeAttr('checked');
        });
        $(this).find('input[type=radio]').attr( 'checked', 'checked' );


        $defaultOption.html(option);
        self.find('.option:nth-child(' + optionNumber + ')').html(defOption);

    });

    $(document).on('onPasswordReset', function(e, data) {

        $(document).find('.send-email-ad').addClass('not-active');

        $(document).find(".login_form_body > h3" ).after( "<span class='send-email-message'>" +
            "<span class='send-m-tick'><i class='q4bikon-tick'></i></span>" +
            "<span class='send-m-txt'>"+__('Email sent successfully')+"</span>" +
        "</span>" );

    });

});