/**
 * Created by СУРЕН on 13.10.2016.
 */
"use strict";
var Q4U = window.Q4U || {};
Q4U.pages = {
    updateSettings: {
        craftMarkup: '<tr class="new-craft">' +
                            '<td data-th="Name">' +
                                '<input name="craft_%s_name" class="q4-form-input required craft-name" value="" type="text">' +
                            '</td>' +
                            '<td style="" data-th="Catalog Number">' +
                                '<input name="craft_%s_catalog_number" class="q4-form-input craft-catalog-number" value="" type="text">' +
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
                                '<td data-th="Task">' +
                                    '<input class="q4-form-input q4_required" class="profession-name" value="" name="profession_%s_name" type="text"></td>' +
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
                                    '<input class="q4-form-input profession-catalog-number"  name="profession_%s_catalog_number" value="" type="text">' +
                                '</td>' +
                                '<td class="hidden_status" style="" data-th="Status">' +
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

        taskMarkup: '<tr class="new-task">' +
                        '<td data-th="'+__('Task Name')+'">' +
                            '<input class="q4-form-input q4_required" class="task-name" value="" name="task_%s_name" type="text"></td>' +
                        '<td style="" data-th="'+__('Crafts')+'">' +
                            '<div class="multi-select-box">' +
                                '<select name="task_%s_crafts" class="q4-select q4-form-input" '+
                                '>%crafts</select>' +
                            '</div>' +
                        '</td>' +
                        '<td class="hidden_status" style="" data-th="'+__('Status')+'">' +
                            '<div class="q4_radio">' +
                                '<div class="toggle_container">' +
                                    '<label class="label_unchecked">' +
                                        '<input name="task_%s_status" value="disabled" type="radio"><span></span>' +
                                    '</label>' +
                                    '<label class="label_checked">' +
                                        '<input name="task_%s_status" value="enabled" checked="checked" type="radio"><span></span>' +
                                    '</label>' +
                                '</div>' +
                            '</div>' +
                        '</td>' +
                    '</tr>',
        addTaskSelector: '.add-task',
        taskFormClass: '.tasks-form',

        userMarkup: '<tr class="new-user pending_row">' +
                        '<td class="rwd-td0" data-th="Name">' +
                            '<input class="q4-form-input q4_required user-name" name="user_%s_name" type="text"></td>' +
                        '<td class="rwd-td1" data-th="Email">' +
                            '<input class="q4-form-input q4_email user-email" name="user_%s_email" type="text"></td>' +
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
                                '<input class="q4-form-input " name="standard_%s_name" type="text">' +
                            '</td>' +
                            '<td data-th="Organisation">' +
                                '<input class="q4-form-input " name="standard_%s_organisation" type="text">' +
                            '</td>' +
                            '<td data-th="Number">' +
                                '<input class="q4-form-input q4_number" name="standard_%s_number" type="text">' +
                            '</td>' +
                            '<td data-th="Submission Place">' +
                                '<input class="q4-form-input " name="standard_%s_submission_place" type="text">' +
                            '</td>' +
                            '<td data-th="Responsible person">' +
                                '<div class="select-wrapper">' +
                                    '<i class="q4bikon-arrow_bottom"></i> ' +
                                    '<select class="q4-select q4-form-input" required="" name="standard_%s_responsible_person">%users</select>' +
                                '</div>' +
                            '</td>' +
                            '<td data-th="File(s)" class="min_width_140">' +
                                '<div class="wrap_files">' +
                                    '<a class="list_uploads disable"><i class="q4bikon-file"></i></a>' +
                                    '<div class="file_container">' +
                                        '<a href="#" class="standard-file">Load</a>' +
                                        '<input class="hidden std_file_input" accept=".doc,.docx,.xls,.xlsx,.pdf,.ppg,.plt,.jpg,.jpe,.jpeg,.png,.gif,.tif,.tiff" multiple="multiple" name="files[%s][]" type="file">' +
                                    '</div>' +
                                '</div>' +
                            '</td>' +
                            '<td class="hidden_status" data-th="Status">new</td>' +
                        '</tr>',
        addStandardSelector: '.add-standard',
        standardFormClass: '.standards-form',

        linkMarkup: '<tr class="new-link">' +
                        '<td data-th="Name">    ' +
                            '<input type="text" class="q4-form-input link-name" name="link_%s_name">' +
                        '</td>' +
                        '<td data-th="URL"> ' +
                            '<input type="text" class="q4-form-input q4_url" name="link_%s_url" value="http://www.">' +
                        '</td>' +
                        '<td data-th="Delete">  ' +
                            '<div class="wrap_delete_row">' +
                                '<span class="delete_row delete-link disable"><i class="q4bikon-delete"></i></span>' +
                            '</div>' +
                        '</td>' +
                    '</tr>',
        addLinkSelector: '.add-link',
        linkFormClass: '.links-form',

        spaceTypeModalSelector:'.space-type-modal',
        spaceTypeMarkup: '<tr class="new-spacetype">' +
                            '<td data-th="Name">    ' +
                                '<input type="text" class="q4-form-input space-type-name q4_required" name="space_%s_name">' +
                            '</td>' +
                            '<td class="td-max-100" data-th="Delete">  ' +
                                '<div class="wrap_delete_row">' +
                                    '<span class="delete_row delete-space-type disable"><i class="q4bikon-delete"></i></span>' +
                                '</div>' +
                            '</td>' +
                        '</tr>',
        addSpaceTypeSelector: '.add-space-type',
        spaceTypeFormClass: '.space-types-form',

        objectTypeModalSelector:'.object-type-modal',
        objectTypeMarkup:   '<tr class="new-objecttype">' +
                                '<td data-th="Name">    ' +
                                    '<input type="text" class="q4-form-input object-type-name q4_required" name="object_%s_name">' +
                                '</td>' +
                                '<td class="td-max-150" data-th="Alias">  ' +
                                    '<input type="text" name="object_%s_alias" class="q4-form-input object-alias q4_required"/>'+
                                '</td>' +
                                '<td class="td-max-100" data-th="Delete">  ' +
                                    '<div class="wrap_delete_row">' +
                                        '<span class="delete_row delete-object-type disable"><i class="q4bikon-delete"></i></span>' +
                                    '</div>' +
                                '</td>' +
                            '</tr>',
        addObjectTypeSelector: '.add-object-type',
        objectTypeFormClass: '.object-types-form',

        constructElementModalSelector:'.construct-element-modal',
        constructElementMarkup:  '<tr class="new-constructelement">' +
                                '<td data-th="Name">    ' +
                                    '<input type="text" class="q4-form-input construct-element-name q4_required" name="element_%s_name">' +
                                '</td>' +
                                '<td data-th="Icons">'+
                                    '<div class="choose-icons">'+
                                        "<i class='q4bikon-arrow_bottom'></i>"+
                                        '<select name="element_%s_icon" class="new-ce selectpicker">%icons</select>'+
                                    '</div>' +
                                '</td>'+
                                 '<td data-th="Space Count" class="td-100 align-center-left">'+
                                    '<div class="wrap-number inline-pickers">'+
                                        '<input type="text" class="numeric-input" name="element_%s_space_count" value="1"/>'+
                                        '<span class="arrows">'+
                                            '<i class="arrow no-arrow_top"></i>'+
                                            '<i class="arrow no-arrow_bottom"></i>'+
                                        '</span>'+
                                    '</div>'+
                                '</td>'+
                                '<td data-th="Delete">  ' +
                                    '<div class="wrap_delete_row">' +
                                        '<span class="delete_row delete-object-type disable"><i class="q4bikon-delete"></i></span>' +
                                    '</div>' +
                                '</td>' +
                            '</tr>',
        addConstructElementSelector: '.add-construct-element',
        constructElementFormClass: '.construct-element-form',

    }
};
$(document).ready(function() {

    if($(".q4_error_message").length>0){
        var UPDATE_INTERVAL = setInterval(function(){

            Q4U.ajaxGetRequest('/settings/apply_status', {
                    successCallback: function(data) {

                        if (data.getData().status) {
                            clearInterval(UPDATE_INTERVAL);
                            location.reload();
                        }
                    },
            });
        },3000);
    }

    var currentPage = Q4U.pages.updateSettings;
    $(document).find('[data-toggle="table"]').bootstrapTable();


    //----------------------------------
    $(document).on('click', currentPage.spaceTypeModalSelector, function(e) {
        e.preventDefault();

        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {

                if (data.getData().modal) {
                    $(document).find('.modal').modal('hide');
                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('.modal').modal('show');
                    $(document).find('[data-toggle="table"]').bootstrapTable();
                }
            }
        });
    });


    $(document).on('click', currentPage.addSpaceTypeSelector, function(e) {
        e.preventDefault();
        var Id = Q4U.timestamp();

        var html = currentPage.spaceTypeMarkup.replace(/%s/g, '+' + Id); //space-type-data
        $(document).find(currentPage.spaceTypeFormClass + ' tbody .mCSB_container').prepend(html);

    });


    $(document).on('click', currentPage.objectTypeModalSelector, function(e) {
        e.preventDefault();

        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {

                if (data.getData().modal) {
                    $(document).find('.modal').modal('hide');
                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('.modal').modal('show');
                    $(document).find('[data-toggle="table"]').bootstrapTable();
                }
            }
        });
    });


    $(document).on('click', currentPage.addConstructElementSelector, function(e) {
        e.preventDefault();
        var Id = Q4U.timestamp();

        var options = $('.selecticons').html();
        var html = currentPage.constructElementMarkup.replace(/%s/g, '+' + Id); //space-type-data
        $(document).find(currentPage.constructElementFormClass + ' tbody:first').prepend(html.replace(/%icons/g, options));
        $('.new-ce.selectpicker').selectpicker({size:3,dropupAuto:false});
        $(document).find('[data-toggle="table"]').bootstrapTable();
        $(document).find('#construct-element-modal').find('table tr.no-records-found').remove()


    });

     $(document).on('click', currentPage.constructElementModalSelector, function(e) {
        e.preventDefault();
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData().modal) {
                    $(document).find('.modal').modal('hide');
                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('.modal').modal('show');
                    setTimeout(function(){

                        $('.modal .selectpicker').selectpicker({size:3,dropupAuto:false})
                    }, 100);
                    $(document).find('[data-toggle="table"]').bootstrapTable();
                }
            }
        });
    });


    $(document).on('click', currentPage.addObjectTypeSelector, function(e) {
        e.preventDefault();
        var Id = Q4U.timestamp();

        var html = currentPage.objectTypeMarkup.replace(/%s/g, '+' + Id); //space-type-data
        $(document).find(currentPage.objectTypeFormClass + ' tbody .mCSB_container').prepend(html);

    });

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


    $(document).on('click', currentPage.addTaskSelector, function() {
        var Id = Q4U.timestamp();
        var html = currentPage.taskMarkup.replace(/%s/g, '+' + Id); //profession-crafts-data
        var options = '';
        var options_helper = '';
        $('.task-crafts-data option').each(function() {
            var text = $(this).html()
            options += '<option value="' + $(this).val() + '">' + text + '</option>';
            options_helper +=
            '<div class="checkbox-list-row">'+

                '<span class="checkbox-text">' +
                    '<label class="checkbox-wrapper-multiple inline" data-val="' + $(this).val() + '">'+
                        '<span class="checkbox-replace"></span>'+
                        '<i class="checkbox-list-tick q4bikon-tick"></i>'+
                    '</label>'+ text +
                '</span>'+
            '</div>'
        });

        $(document).find(currentPage.taskFormClass + ' tbody:first').prepend(html.replace(/%crafts_helper/g, options_helper).replace(/%crafts/g, options));

    });

    $(document).on('click', currentPage.addProfessionSelector, function() {
        var Id = Q4U.timestamp();
        var html = currentPage.professionMarkup.replace(/%s/g, '+' + Id); //profession-crafts-data
        var options = '';
        var options_helper = '';
        $('.task-crafts-data option').each(function() {
            var text = $(this).html()
            options += '<option value="' + $(this).val() + '">' + text + '</option>';
            options_helper +=
            '<div class="checkbox-list-row">'+
                '<span class="checkbox-text">' +
                    '<label class="checkbox-wrapper-multiple inline" data-val="' + $(this).val() + '">'+
                        '<span class="checkbox-replace"></span>'+
                        '<i class="checkbox-list-tick q4bikon-tick"></i>'+
                    '</label>'  + text +
                '</span>'+
            '</div>'
        });
        $(document).find(currentPage.professionFormClass + ' tbody:first').prepend(html.replace(/%crafts_helper/g, options_helper).replace(/%crafts/g, options));

    });

    $(document).on('click', '.arrows > .arrow', function() {
        var num = ($(this).hasClass('no-arrow_top') == true) ? 1 : -1;
        var input = $(this).parents('.arrows').siblings('.numeric-input');
        if (input.hasClass('disabled-input')) {
            return;
        }
        if (input.length) {
            input.val(parseInt(input.val()) + num);
            input.change();
        }
    });


    /***************************************
     *   Смена символов группы для объектов
     ***************************************/
    $(document).on('click', '.wrap-letters .no-arrow_top, .wrap-letters .no-arrow_bottom', function() {
        var letterInput = $(this).closest('span').siblings('input.letters-only');
        var letter = letterInput.val().charAt(0).toUpperCase();
        if ($(this).hasClass('no-arrow_top')) {
            if (letter.charCodeAt(0) >= 65 && letter.charCodeAt(0) < 90) {
                letterInput.val(String.fromCharCode(letter.charCodeAt(0) + 1));
            } else {
                letterInput.val(String.fromCharCode(65));
            }
        } else {
            if (letter.charCodeAt(0) > 65 && letter.charCodeAt(0) <= 90) {
                letterInput.val(String.fromCharCode(letter.charCodeAt(0) - 1));
            } else {
                letterInput.val(String.fromCharCode(90));
            }
        }

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

    $('body').on('tasksUpdated', function(e, data) {
        if ($('.tasks-form').length && data.tasksForm != undefined)
            $('.tasks-form').replaceWith(data.tasksForm);
        if ($('.professions-form').length && data.professionsForm != undefined)
            $('.professions-form').replaceWith(data.professionsForm);
        $(document).find('[data-toggle="table"]').bootstrapTable();
    });


    $('body').on('professionsUpdated', function(e, data) {
        if ($('.professions-form').length && data.professionsForm != undefined)
            $('.professions-form').replaceWith(data.professionsForm);
        if ($('.users-form').length && data.usersForm != undefined)
            $('.users-form').replaceWith(data.usersForm);
        $(document).find('[data-toggle="table"]').bootstrapTable();
    });

    $('body').on('spaceTypesUpdated', function(e, data) {
        if ($(currentPage.spaceTypeFormClass).length && data.typesForm != undefined)
            $(currentPage.spaceTypeFormClass).replaceWith(data.typesForm);
        // $(document).find('.space-type-scroll').mCustomScrollbar();
        $(document).find('[data-toggle="table"]').bootstrapTable();
    });

    $('body').on('objectTypesUpdated', function(e, data) {
        if ($(currentPage.objectTypeFormClass).length && data.typesForm != undefined)
            $(currentPage.objectTypeFormClass).replaceWith(data.typesForm);
        // $(document).find('.space-type-scroll').mCustomScrollbar();
        $(document).find('[data-toggle="table"]').bootstrapTable();
    });
    $('body').on('constructElementsUpdated', function(e, data) {
        if ($(currentPage.constructElementFormClass).length && data.typesForm != undefined){
            $(currentPage.constructElementFormClass).replaceWith(data.typesForm);
            setTimeout(function(){

                $('.modal .selectpicker').selectpicker({size:3,dropupAuto:false})
            }, 100);
        }
        $(document).find('[data-toggle="table"]').bootstrapTable();
    });

});
