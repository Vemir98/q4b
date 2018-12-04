/**
 * Created by СУРЕН on 13.10.2016.
 */
"use strict";
var Q4U = window.Q4U || {};
Q4U.pages = {
    updateCompany: { //Страница обновления компании
        craftMarkup: '<tr class="new-craft">' +
                            '<td data-th="Name">' +
                                '<input name="craft_%s_name" class="table_input required craft-name" value="" type="text">' +
                            '</td>' +
                            '<td style="" data-th="Catalog Number">' +
                                '<input name="craft_%s_catalog_number" class="table_input craft-catalog-number" value="" type="text">' +
                            '</td>' +
                            '<td class="hidden_status" style="" data-th="Status">' +
                                '<div class="q4_radio">' +
                                    '<div class="toggle_container">' +
                                        '<label class="label_unchecked">' +
                                            '<input name="craft_%s_status" value="disabled" type="radio"><span></span>' +
                                        '</label>' +
                                        '<label class="label_checked">' +
                                            '<input name="craft_%s_status" value="enabled" checked="checked" type="radio"><span></span>' +
                                        '</label>' +
                                    '</div>' +
                                '</div>' +
                            '</td>' +
                        '</tr>',
        addCraftSelector: '.add-craft',
        craftFormClass: '.crafts-form',
        professionMarkup: '<tr  class="new-profession">' +
                                '<td data-th="Name">' +
                                    '<input class="table_input q4_required" class="profession-name" value="" name="profession_%s_name" type="text"></td>' +
                                '<td style="" data-th="Crafts">' +
                                    '<div class="multi-select-box">' +
                                    '<div class="select-imitation">' +
                                        '<span class="select-imitation-title">' +
                                        '<span class="select-def-text">Please select</span>' +
                                        '</span>'+
                                        '<div class="over-select"></div>' +
                                        '<i class="arrow-down q4bikon-arrow_bottom"></i>'+
                                    '</div>' +
                                    '<div class="checkbox-list">%crafts_helper</div>' +
                                        '<select name="profession_%s_crafts" class="hidden-select" multiple="multiple">%crafts</select>' +
                                    '</div>' +
                                '</td>' +
                                '<td style="" data-th="Catalog Number">' +
                                    '<input class="table_input profession-catalog-number"  name="profession_%s_catalog_number" value="" type="text">' +
                                '</td>' +
                                '<td class="hidden_status" style="" data-th="Status">'+
                                    '<div class="q4_radio">' +
                                        '<div class="toggle_container">' +
                                            '<label class="label_unchecked">' +
                                                '<input name="profession_%s_status" value="disabled" type="radio"><span></span>' +
                                            '</label>' +
                                            '<label class="label_checked">' +
                                                '<input name="profession_%s_status" value="enabled" checked="checked" type="radio"><span></span>' +
                                            '</label>' +
                                        '</div>' +
                                    '</div>' +
                                '</td>' +
                            '</tr>',
        addProfessionSelector: '.add-profession',
        professionFormClass: '.professions-form',
        userMarkup: '<tr class="new-user pending_row">' +
                        '<td class="rwd-td0" data-th="Name">' +
                            '<input class="table_input q4_required user-name" name="user_%s_name" type="text"></td>' +
                        '<td class="rwd-td1" data-th="Email">' +
                            '<input class="table_input q4_email user-email" name="user_%s_email" type="text"></td>' +
                        '<td class="rwd-td2" data-th="Professions">' +
                            '<div class="select-wrapper">' +
                                '<i class="q4bikon-arrow_bottom"></i><' +
                                'select name="user_%s_profession" class="q4-select q4-form-input">%professions</select>' +
                            '</div>' +
                        '</td>' +
                        '<td class="rwd-td3" data-th="User Group">' +
                            '<div class="select-wrapper">' +
                                '<i class="q4bikon-arrow_bottom"></i>' +
                                '<select name="user_%s_role" class="q4-select q4-form-input">%roles</select>' +
                            '</div>' +
                        '</td>' +
                        '<td class="align-center-left" data-th="Show Details">' +
                            '<a class="show_details disable"><i class="q4bikon-preview"></i></a>' +
                        '</td>' +
                        '<td class="rwd-td4 hidden_status" data-th="Status"></td>' +
                    '</tr>',
        addUserSelector: '.add-user',
        userFormClass: '.users-form',
        standardMarkup: '<tr class="new-standard">' +
                            '<td data-th="Name">' +
                                '<input class="table_input " name="standard_%s_name" type="text">' +
                            '</td>' +
                            '<td data-th="Organisation">' +
                                '<input class="table_input " name="standard_%s_organisation" type="text">' +
                            '</td>' +
                            '<td data-th="Number">' +
                                '<input class="table_input q4_number" name="standard_%s_number" type="text">' +
                            '</td>' +
                            '<td data-th="Submission Place">' +
                                '<input class="table_input " name="standard_%s_submission_place" type="text">' +
                            '</td>' +
                            '<td data-th="Responsible person">' +
                                '<div class="select-wrapper">' +
                                    '<i class="q4bikon-arrow_bottom"></i> ' +
                                    '<select class="q4-select q4-form-input" required="" name="standard_%s_responsible_person">%users</select>' +
                                '</div>' +
                            '</td>' +
                            '<td data-th="File(s)" class="min_width_140">' +
                                '<div class="wrap_files">' +
                                    '<a class="list-uploads disable"><i class="q4bikon-file"></i></a>' +
                                    '<div class="file_container">' +
                                        '<a href="#" class="standard-file load-file-form">Load</a>' +
                                        '<input class="hidden std_file_input input-file-form" accept=".doc,.docx,.xls,.xlsx,.pdf,.ppg,.plt,.jpg,.jpe,.jpeg,.png,.gif,.tif,.tiff" multiple="multiple" name="files[%s][]" type="file">' +
                                    '</div>' +
                                '</div>' +
                            '</td>' +
                            '<td class="hidden_status" data-th="Status">new</td>' +
                        '</tr>',
        addStandardSelector: '.add-standard',
        standardFormClass: '.standards-form',
        linkMarkup: '<tr class="new-link">' +
                        '<td data-th="Name">    ' +
                            '<input type="text" class="table_input link-name" name="link_%s_name">' +
                        '</td>' +
                        '<td data-th="URL"> ' +
                            '<input type="text" class="table_input q4_url" name="link_%s_url" value="http://www.">' +
                        '</td>' +
                        '<td data-th="Delete">  ' +
                            '<div class="wrap_delete_row">' +
                                '<span class="delete_row delete-link disable"><i class="q4bikon-delete"></i></span>' +
                            '</div>' +
                        '</td>' +
                    '</tr>',
        addLinkSelector: '.add-link',
        linkFormClass: '.links-form',
    }
};
$(document).ready(function() {
    var currentPage = Q4U.pages.updateCompany;
    $(document).find('[data-toggle="table"]').bootstrapTable();

    /**
     * Add craft event handler
     */
    $(document).on('click', currentPage.addCraftSelector, function() {
        if (!$(document).find('.new-craft').length || !!$(document).find('.new-craft').first().find('.craft-name').val().trim() ||
            !!$(document).find('.new-craft').first().find('.craft-catalog-number').val().trim()); {
            var Id = Q4U.timestamp();
            $(document).find(currentPage.craftFormClass + ' tbody:first').prepend($(currentPage.craftMarkup.replace(/%s/g, '+' + Id)));
        }

    });

    /**
     * Add profession event handler
     */
    $(document).on('click', currentPage.addProfessionSelector, function() {
        var Id = Q4U.timestamp();
        var html = currentPage.professionMarkup.replace(/%s/g, '+' + Id); //profession-crafts-data
        var options = '';
        var options_helper = '';
        $('.profession-crafts-data option').each(function() {
            options += '<option value="' + $(this).val() + '">' + $(this).html() + '</option>';
           options_helper +=
            '<div class="checkbox-list-row">'+
                '<span class="checkbox-text">' +
                    '<label class="checkbox-wrapper-multiple inline" data-val="' + $(this).val() + '">'+
                        '<span class="checkbox-replace"></span>'+
                        '<i class="checkbox-list-tick q4bikon-tick"></i>'+
                    '</label>'+ $(this).html() +
                '</span>'+
            '</div>'
        });
        $(document).find(currentPage.professionFormClass + ' tbody:first').prepend(html.replace(/%crafts_helper/g, options_helper).replace(/%crafts/g, options));

    });

    /**
     * Crafts on update event handler
     */
    $('body').on('craftsUpdated', function(e, data) {
        if ($('.crafts-form').length && data.craftsForm != undefined)
            $('.crafts-form').replaceWith(data.craftsForm);
        if ($('.professions-form').length && data.professionsForm != undefined)
            $('.professions-form').replaceWith(data.professionsForm);
        if ($('.users-form').length && data.usersForm != undefined)
            $('.users-form').replaceWith(data.usersForm);
        $(document).find('[data-toggle="table"]').bootstrapTable();
    });

    /**
     * Professions on update event handler
     */
    $('body').on('professionsUpdated', function(e, data) {
        if ($('.professions-form').length && data.professionsForm != undefined)
            $('.professions-form').replaceWith(data.professionsForm);
        if ($('.users-form').length && data.usersForm != undefined)
            $('.users-form').replaceWith(data.usersForm);
        $(document).find('[data-toggle="table"]').bootstrapTable();
    });

    /**
     * Users on update event handler
     */
    $('body').on('usersUpdated', function(e, data) {
        if ($('.users-form').length && data.usersForm != undefined)
            $('.users-form').replaceWith(data.usersForm);
        if ($('.standards-form').length && data.standardsForm != undefined)
            $('.standards-form').replaceWith(data.standardsForm);
        $(document).find('[data-toggle="table"]').bootstrapTable();    });

    /**
     * Standards on update event handler
     */
    $('body').on('standardsUpdated', function(e, data) {
        if ($('.standards-form').length && data.standardsForm != undefined)
            $('.standards-form').replaceWith(data.standardsForm);
        $(document).find('[data-toggle="table"]').bootstrapTable();
    });

    $('body').on('renewStandardModal', function(e, data) {
        if ($('.modal').length && data.modal != undefined)
            $('.modal .q4_modal').replaceWith($(data.modal).find('.q4_modal'));
        $(document).find('[data-toggle="table"]').bootstrapTable();
    });

    /**
     * Links on update event handler
     */
    $('body').on('linksUpdated', function(e, data) {
        if ($('.links-form').length && data.linksForm != undefined)
            $('.links-form').replaceWith(data.linksForm);
        $(document).find('[data-toggle="table"]').bootstrapTable();
    });

    $(document).on('click', '.delete-link', function(e) {
        e.preventDefault();
        Q4U.ajaxGetRequest($(this).data('url'));
    });

    /**
     * Add user event handler
     */
    $(document).on('click', currentPage.addUserSelector, function() {
        if (!$(document).find('.new-user').length || !!$(document).find('.new-user').first().find('.user-email').val().trim()) {
            var Id = Q4U.timestamp();
            var html = currentPage.userMarkup.replace(/%s/g, '+' + Id);
            var options = '';
            $('.user-roles-data option').each(function() {
                options += '<option value="' + $(this).val() + '">' + $(this).html() + '</option>';
            });
            html = html.replace(/%roles/g, options);

            options = '';
            $('.user-professions-data option').each(function() {
                options += '<option value="' + $(this).val() + '">' + $(this).html() + '</option>';
            });
            $(document).find(currentPage.userFormClass + ' tbody:first').prepend($(html.replace(/%professions/g, options)));
        }

    });



    /**
     * Add standard event handler
     */
    $(document).on('click', currentPage.addStandardSelector, function() {
        if (!$(document).find('.new-standard').length || !!$(document).find('.new-standard').first().find('.standard-name').val().trim()) {
            var Id = Q4U.timestamp();
            var html = currentPage.standardMarkup.replace(/%s/g, '+' + Id);
            var options = '';

            options = '';
            $('.users-data option').each(function() {
                options += '<option value="' + $(this).val() + '">' + $(this).html() + '</option>';
            });
            $(document).find(currentPage.standardFormClass + ' tbody:first').prepend($(html.replace(/%users/g, options)));
        }

    });

    /**
     * Add link event handler
     */
    $(document).on('click', currentPage.addLinkSelector, function() {
        if (!$(document).find('.new-link').length || !!$(document).find('.new-link').first().find('.link-name').val().trim()) {
            var Id = Q4U.timestamp();
            var html = currentPage.linkMarkup.replace(/%s/g, '+' + Id);
            $(document).find(currentPage.linkFormClass + ' tbody:first').prepend($(html));
        }

    });

    /**
     * User details modal
     */
    $(document).on('click', '.cmp-user-details', function(e) {
        e.preventDefault();
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData().modal) {
                    $(document).find('.modal').modal('hide');
                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('.modal').modal('show');
                }
            }
        });
    });
    /**
     * User details modal
     */
    $(document).on('click', '.crafts-list-modal', function(e) {
        e.preventDefault();

        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData().craftsList) {
                    $(document).find('.modal').modal('hide');
                    var modal = data.getData().craftsList;
                    $('body').append(modal);
                    $(document).find('.modal').modal('show');
                }
            }
        });
    });

    /**
     * Invite user or reset user pwd btn in usr details modal
     */
    $(document).on('click', '.invite-usr,.reset-usr-pwd', function(e) {
        e.preventDefault();
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                $(document).find('.modal').modal('hide');
            }
        });
    });

    /**
     * Delete standards file
     */
    $(document).on('click', '.delete-std-file', function(e) {
        e.preventDefault();
        var row = $(this).parents('li');
        Q4U.ajaxGetRequest($(this).attr('href'), {
            successCallback: function(data) {
                row.remove();
            }
        });
    });


});

