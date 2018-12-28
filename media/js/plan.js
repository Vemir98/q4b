/**
 * Created by СУРЕН on 21.12.2016.
 */

"use strict";

var Q4U = window.Q4U || {};
var CURRENT_PLAN_PAGE;
var CURRENT_PROFFESION_ID = $(document).find('.current-profession-id').val() ? $(document).find('.current-profession-id').val(): '';
var CHECKED_PLANS = $(document).find('.selected-plans').val() ? JSON.PARSE($(document).find('.selected-plans').val()): {};
Q4U.pages = window.Q4U.pages || {};
$(document).ready(function() {
    $.extend(Q4U.pages, {
        updatePage: {
            struct:     {
                addEditSpace: {
                    spaceMarkup: '<tr>'+
                                    '<td data-th="Name">'+
                                        '<input type="text" class="table_input disabled-input sp-number" name="space_%s_number" value="%number">'+
                                    '</td>'+
                                    '<td data-th="Item Type">'+
                                        '<div class="select-wrapper">'+
                                            '<i class="q4bikon-arrow_bottom"></i>'+
                                            '<select class="q4-select q4-form-input" name="space_%s_type">%space-types</select>'+
                                        '</div>'+
                                    '</td>'+
                                    '<td data-th="Item Description">'+
                                        '<input type="text" class="table_input" name="space_%s_desc">'+
                                    '</td>'+
                                    '<td data-th="Delete"><div class="wrap_delete_row">'+
                                            '<span class="delete_row delete-space">'+
                                                '<i class="q4bikon-delete"></i>'+
                                            '</span>'+
                                        '</div>'+
                                    '</td>'+
                                '</tr>',
                    addSpaceSelector: '.add-space',
                    spaceTableClass: '.spaces-tbl'
                },

            },
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
            planMarkup:'<tr class="new-link">' +

                '<td class="rwd-td3" data-th=""> ' +
                    '<input type="text" class="table_input" name="" value="3">' +
                '</td>' +
                '<td class="rwd-td4" data-th="">  ' +
                    '<input type="text" class="table_input" name="" value="4">' +
                '</td>' +
                '<td class="rwd-td6" data-th="">  ' +
                    '<input type="text" class="table_input" name="" value="6">' +
                '</td>' +
                '<td class="rwd-td12" data-th="Action"> ' +
                    '<div class="text-center">' +
                        '<span class="delete_row delete-plan<?=$disabledButton?>" data-url="">' +
                            '<i class="q4bikon-delete"></i>' +
                        '</span>' +
                    '</div>' +
                '</td>' +
            '</tr>',
            addPlanSelector: '.add-plan',
            addPlanSelectorModal: '.add-plan-modal',

            addLinkSelector: '.add-link',
            linkFormClass: '.links-form',
            addTaskSelector: '.add-task',
            taskFormClass: '.tasks-form',
            addPropSelector: '.add-prop',
            propFormClass: '.props-form',

            certFormClass: '.certifications-form',
            addCertSelector: '.add-cert',
            qCListSelector: '.quality-control-list',
            qcCurrentSelector : '.project-props-qc a',
            qcFilterSettings:'.filter-settings-button',
            filterPlans:'.filter-plans',
            filterTracking:'.filter-tracking',
            searchTracking:'.search-tracking',
            disabledGrayButton:'disabled-gray-button'

        },

    });

    var currentPage = Q4U.pages.updatePage;


    $(document).on('click', currentPage.struct.addEditSpace.addSpaceSelector, function(e) {
        e.preventDefault();
        var Id = Q4U.timestamp();

        var html = currentPage.struct.addEditSpace.spaceMarkup.replace(/%space-types/g, $(document).find('.space-types').html());
        html = html.replace(/%s/g, '+' + Id);
        var i = 1;
        $(document).find(currentPage.struct.addEditSpace.spaceTableClass + ' tbody:first').prepend($(html));

        $(currentPage.struct.addEditSpace.spaceTableClass + ' tbody tr').each(function() {
            $(this).find('.sp-number').val(i++);
        });

    });

    $(document).on('click', '.delete-space', function() {
        $(this).parents('tr').remove();
    });

    $(document).on('click', '.add-new-project-user', function(e) {
        e.preventDefault();
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData().modal) {
                    $(document).find('.modal').modal('hide');
                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('.modal').modal('show');
                    // $('.modal .list-users-scroll').mCustomScrollbar({ axis: "y" });
                    $('[data-toggle="table"]').bootstrapTable();
                }
            }
        });
    });
    $(document).on('click', currentPage.addLinkSelector, function() {
        if (!$(document).find('.new-link').length || !!$(document).find('.new-link').first().find('.link-name').val().trim()) {
            var Id = Q4U.timestamp();
            var html = currentPage.linkMarkup.replace(/%s/g, '+' + Id);
            $(document).find(currentPage.linkFormClass + ' tbody:first').prepend($(html));
        }

    });


    $(document).on('change','.modal .plan-place-number',function(){
        var self = $(this);
        var text = self.val();
        var modal = self.closest('.modal');
        var objectId =modal.find('.qc-prop').val();
        var placeType = modal.find('.plan-place-type');
        var placeTypeVal = modal.find('.plan-place-type').val()=='private' ? 'private' :'public' ;
        var cNumber = modal.find('.plan-place-cnumber');
        Q4U.ajaxGetRequest($(this).data('url')+'/' + objectId + '/'+self.val()+'/' +placeTypeVal, {
            successCallback: function(data) {
                if (data.getData()) {
                    var number = data.getData() ? data.getData().number : '';

                    cNumber.val(number)
                }
            }
        });
        if(text.length){
            modal.find('.plan-place-type').removeClass('disabled-input').addClass('q4_required');
            modal.find('.q4_form_submit').addClass(currentPage.disabledGrayButton);
        }else{
           placeType.addClass('disabled-input').removeClass('q4_required');
           modal.find('.q4_form_submit').removeClass(currentPage.disabledGrayButton);
        }
    });
    $(document).on('change','.modal .plan-place-type',function(){
        var self = $(this);
        var text = self.val()
        var modal = self.closest('.modal');
        var placeNumber = modal.find('.plan-place-number').val()

        if(text.length){
            modal.find('.q4_form_submit').removeClass(currentPage.disabledGrayButton);
        }else{
           modal.find('.q4_form_submit').addClass(currentPage.disabledGrayButton);
        }
    });

    $(document).on('click', currentPage.addPlan, function(e) {

    });
    $(document).on('click', currentPage.filterPlans, function(e) {
        e.preventDefault();
        var current = $(this)
        var objectId = current.closest('form').find('[data-name="object"]').val();
        var professionId = current.closest('form').find('[data-name="profession"]').val();
        var floors = current.closest('.plans-layout').find('.floors-filter').val();
        floors = floors ? '/floors/' + floors.join('_') + '/': '';
        var page = CURRENT_PLAN_PAGE ? '/page/'+CURRENT_PLAN_PAGE : '';
        var url = current.data('url') + '/object/' + objectId + '/professions/' + professionId + floors + page;

        Q4U.ajaxGetRequest(url, {
            successCallback: function(data) {
                if (data.getData()) {
                    var printLandscape = $(document).find('.print-landscape-mode');
                    printLandscape.find('.printable-table-other').empty();
                    CHECKED_PLANS = {};
                    if ($(document).find('.plans-list-layout').length && data.getData().plans != undefined){
                        var currentModal = data.getData().plans;

                        $(document).find('.plans-list-layout .scrollable-table').closest('div.row').replaceWith($(currentModal).find('.scrollable-table').closest('div.row'));
                        $(document).find('.plans-list-layout .q4-carousel-table-wrap').closest('div.row').replaceWith($(currentModal).find('.q4-carousel-table-wrap').closest('div.row'));

                    }

                    var self = $(document).find('.plans-list-layout').closest('.tab_panel').find('.panel_header');


                    var windowWidth = window.innerWidth;

                    $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
                    $.fn.utilities('setScrollBarWidth', self.closest('.tab_panel').find('.scrollable-table'), windowWidth);

                    var widthT = $.fn.utilities('measureHiddenTable', self.closest('.tab_panel').find('table.table'),false);


                    setTimeout(function(){

                        $.fn.utilities('updateContentOnChange');
                        $('.selectpicker').selectpicker({size:3,dropupAuto:false})
                    }, 300);
                    current.closest('.panel_content').find('.panel_footer').find('.plans-to-print-link').addClass('disabled-gray-button');
                    current.closest('.panel_content').find('.panel_footer').find('.plans-to-send').addClass('disabled-gray-button');

                    $('[data-toggle="table"]').bootstrapTable();
                    CURRENT_PLAN_PAGE = false;
                }
            }
        });
    });

    var selectProfession;

    $(document).on('change','.select-structure, .select-profession',function(e){

        var current = $(this);

        var objectId = current.closest('form').find('[data-name="object"]').val();
        var professionId = current.closest('form').find('[data-name="profession"]').val();
        selectProfession = $(this).find('option:selected').text();

        var floors = current.closest('.plans-layout').find('.floors-filter').val();
        floors = floors ? '/floors/' + floors.join('_') + '/': '';
        var page = CURRENT_PLAN_PAGE ? '/page/'+CURRENT_PLAN_PAGE : '';

        var url = current.closest('form').data('url') + '/object/' + objectId + '/professions/' + professionId + floors + page;

        Q4U.ajaxGetRequest(url, {
            successCallback: function(data) {
                if (data.getData()) {
                    var printLandscape = $(document).find('.print-landscape-mode');
                    printLandscape.find('.printable-table-other').empty();
                    CHECKED_PLANS = {};
                    if ($(document).find('.plans-list-layout').length && data.getData().plans != undefined){
                        var currentModal = data.getData().plans;

                        $(document).find('.plans-list-layout .scrollable-table').closest('div.row').replaceWith($(currentModal).find('.scrollable-table').closest('div.row'));
                        $(document).find('.plans-list-layout .q4-carousel-table-wrap').closest('div.row').replaceWith($(currentModal).find('.q4-carousel-table-wrap').closest('div.row'));

                    }

                    var self = $(document).find('.plans-list-layout').closest('.tab_panel').find('.panel_header');


                    var windowWidth = window.innerWidth;

                    $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
                    $.fn.utilities('setScrollBarWidth', self.closest('.tab_panel').find('.scrollable-table'), windowWidth);

                    var widthT = $.fn.utilities('measureHiddenTable', self.closest('.tab_panel').find('table.table'),false);


                    setTimeout(function(){

                        $.fn.utilities('updateContentOnChange');
                        $('.selectpicker').selectpicker({size:3,dropupAuto:false})
                    }, 300);
                    current.closest('.panel_content').find('.panel_footer').find('.plans-to-print-link').addClass('disabled-gray-button');
                    current.closest('.panel_content').find('.panel_footer').find('.plans-to-send').addClass('disabled-gray-button');

                    $('[data-toggle="table"]').bootstrapTable();
                    CURRENT_PLAN_PAGE = false;
                }
            }
        });

    });


    $(document).on('change','.selectpicker',function(e){
        var self = $(this);

        var tr = self.closest('tr');
        var customNumber = tr.find('.plan-place-custom-number');

        self.removeClass('q4_required');
        self.removeClass('error');
        var floor = tr.find('.floor-numbers');

        if(self.val()!='0'){
            customNumber.removeClass('disabled-input');
            floor.addClass('disabled-input')

        }else{

            customNumber.addClass('disabled-input');
            floor.removeClass('disabled-input')
        }

    })

    $(document).on('keyup change', '.plan-place-custom-number', function() {
        var self = $(this);
        var tr = self.closest('tr');
        var icon = tr.find('.choose-icons');
        self.removeClass('q4_required');
        self.removeClass('error');
    });

    $(document).on('click','.plans-layout .scrollable-table .checkbox-list-row',function (e) {

        var self = $(this);



        var currentCheckbox = self.closest('tr').find('.selectable-column').find('input[type=checkbox]')

        var currentPlanId = self.closest('tr').data('planid');
        var selection = self.closest('td').find('.multi-select-col .hidden-select').val();

        if(currentCheckbox.is(':checked') && !self.closest('.multi-select-box').hasClass('comma')){

            self.closest('.plans-layout').find('table .selectable-column input[type=checkbox]:checked').each(function(){
                var current = $(this);
                var cell = current.closest('tr').find('.multi-select-col');
                var disabledInput = cell.find('.select-imitation').hasClass('disabled-input')
                if(!disabledInput){
                    var planId =cell.closest('tr').data('planid');

                    if(planId!=currentPlanId){

                             var currentCell = self.closest('td').html();

                            cell.parent().html(currentCell);

                            var select = current.closest('tr').find('.multi-select-col .hidden-select').val(selection);
                            current.closest('tr').find('.multi-select-col .checkbox-list-no-scroll').addClass('hidden')
                            select.attr('name','plan_'+planId+'_crafts');

                    }

                }

            })

        }

        // }, 200);
    })



    $(document).on('click', '.search-plans', function(e) {
        e.preventDefault();
        var current = $(this)

        var searchText = current.closest('.plans-layout').find('.search-plan-input').val();
        var search = $.fn.utilities('base64_encode',$.fn.utilities('utf8_encode',searchText))
        var url = current.data('url') + '/' + search;

        Q4U.ajaxGetRequest(url, {
            successCallback: function(data) {
                if (data.getData()) {
                    var printLandscape = $(document).find('.print-landscape-mode');
                    printLandscape.find('.printable-table-other').empty();
                    CHECKED_PLANS = {};
                    if ($(document).find('.plans-list-layout').length && data.getData().plans != undefined){
                        var currentModal = data.getData().plans;

                        $(document).find('.plans-list-layout .scrollable-table').closest('div.row').replaceWith($(currentModal).find('.scrollable-table').closest('div.row'));
                        $(document).find('.plans-list-layout .q4-carousel-table-wrap').closest('div.row').replaceWith($(currentModal).find('.q4-carousel-table-wrap').closest('div.row'));

                    }


                    var self = $(document).find('.plans-list-layout').closest('.tab_panel').find('.panel_header');


                    var windowWidth = window.innerWidth;

                    $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
                    $.fn.utilities('setScrollBarWidth', self.closest('.tab_panel').find('.scrollable-table'), windowWidth);

                    var widthT = $.fn.utilities('measureHiddenTable', self.closest('.tab_panel').find('table.table'),false);


                    setTimeout(function(){
                        self.closest('.tab_panel').find('table.table').closest('.mCSB_container').width(widthT);

                        $.fn.utilities('updateContentOnChange');
                        $('.selectpicker').selectpicker({size:3,dropupAuto:false})
                    }, 300);
                    current.closest('.panel_content').find('.panel_footer').find('.plans-to-print-link').addClass('disabled-gray-button');
                    current.closest('.panel_content').find('.panel_footer').find('.plans-to-send').addClass('disabled-gray-button');

                    $('[data-toggle="table"]').bootstrapTable();
                }
            }
        });
    });
    $(document).on('click', '.plans-deselect-all', function(e) {
        e.preventDefault();
        var printLandscape = $(document).find('.print-landscape-mode');
        printLandscape.find('.printable-table-other').empty();
        CHECKED_PLANS = {};
        CURRENT_PROFFESION_ID = '';
        $.fn.utilities('checkCurrentProffesion',CURRENT_PROFFESION_ID,false)

        $(document).find('.plans-list-layout .total-res-selected').html(0);
        $(document).find('.plans-list-layout').find('.enable-plan-action input[type=checkbox]').prop('checked',false)
        $(document).find('.plans-list-layout').find('.check-all-column input[type=checkbox]').prop('checked',false)
        $(document).find('.current-profession-id').val('')
        $(document).find('.selected-plans').val('')
        $(document).find('.check-all-column').addClass('disabled-input');
        $(this).closest('.panel_content').find('.panel_footer').find('.plans-to-print-link').addClass('disabled-gray-button');
        $(this).closest('.panel_content').find('.panel_footer').find('.plans-to-send').addClass('disabled-gray-button');


    });
    $(document).on('click', currentPage.filterTracking, function(e) {
        e.preventDefault();
        var current = $(this)
        var filterType = current.closest('form').find('[data-name="filter_tracking_type"]').val()
        var proffession = current.closest('form').find('[data-name="filter_proffesion"]').val()
        var fromSplit = current.closest('form').find('.filter-from').val().split('/')
        var toSplit = current.closest('form').find('.filter-to').val().split('/')
        var from = fromSplit[2] + '-' + fromSplit[1] + '-' + fromSplit[0]
        var to = toSplit[2] + '-' + toSplit[1] + '-' + toSplit[0]
        var url = current.data('url') +'/filter/'+filterType +'/profession/' + proffession +'/from/' + from + '/to/' + to;


        Q4U.ajaxGetRequest(url, {
            successCallback: function(data) {

                if (data.getData()!=undefined) {

                    if ($(document).find('.plans-tracking-layout').length && data.getData().html != undefined){
                        var currentModal = data.getData().html;

                        $(document).find('.plans-tracking-layout .scrollable-table').closest('div.row').replaceWith($(currentModal).find('.scrollable-table').closest('div.row'));
                        $(document).find('.plans-tracking-layout .q4-carousel-table-wrap').closest('div.row').replaceWith($(currentModal).find('.q4-carousel-table-wrap').closest('div.row'));
                    }

                    var self = $(document).find('.plans-tracking-layout').closest('.tab_panel').find('.panel_header');
                    $(document).find('.date').datetimepicker({
                        locale: $(document).find('html').attr('lang')
                    });

                    var windowWidth = window.innerWidth;

                    $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
                    $.fn.utilities('setScrollBarWidth', self.closest('.tab_panel').find('.scrollable-table'), windowWidth);

                    var widthT = $.fn.utilities('measureHiddenTable', self.closest('.tab_panel').find('table.table'),false);


                    setTimeout(function(){
                        self.closest('.tab_panel').find('table.table').closest('.mCSB_container').width(widthT);

                        $.fn.utilities('updateContentOnChange');
                    }, 300);

                    $('[data-toggle="table"]').bootstrapTable();
                }
            }
        });
    });
    $(document).on('click', '.plans-tracking-layout .pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var current = $(this);
        Q4U.ajaxGetRequest(url, {
            successCallback: function(data) {
                 if (data.getData()!=undefined) {

                    if ($(document).find('.plans-tracking-layout').length && data.getData().html != undefined){
                        var currentModal = data.getData().html;

                        $(document).find('.plans-tracking-layout .scrollable-table').closest('div.row').replaceWith($(currentModal).find('.scrollable-table').closest('div.row'));
                        $(document).find('.plans-tracking-layout .q4-carousel-table-wrap').closest('div.row').replaceWith($(currentModal).find('.q4-carousel-table-wrap').closest('div.row'));
                    }

                    var self = $(document).find('.plans-tracking-layout').closest('.tab_panel').find('.panel_header');
                    $(document).find('.date').datetimepicker({
                        locale: $(document).find('html').attr('lang')
                    });

                    var windowWidth = window.innerWidth;

                    $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
                    $.fn.utilities('setScrollBarWidth', self.closest('.tab_panel').find('.scrollable-table'), windowWidth);

                    var widthT = $.fn.utilities('measureHiddenTable', self.closest('.tab_panel').find('table.table'),false);


                    setTimeout(function(){
                        self.closest('.tab_panel').find('table.table').closest('.mCSB_container').width(widthT);

                        $.fn.utilities('updateContentOnChange');
                    }, 300);

                    $('[data-toggle="table"]').bootstrapTable();
                }
            }
        });
    });

    $(document).on('click', currentPage.searchTracking, function(e) {
        e.preventDefault();
        var current = $(this)
        var searchText = current.closest('form').find('.search-input').val();
        var current = $(this)
        var filterType = current.closest('form').find('[data-name="filter_tracking_type"]').val();
        var proffession = current.closest('form').find('[data-name="filter_proffesion"]').val();
        var fromSplit = current.closest('form').find('.filter-from').val().split('/');
        var toSplit = current.closest('form').find('.filter-to').val().split('/');
        var from = fromSplit[2] + '-' + fromSplit[1] + '-' + fromSplit[0];
        var to = toSplit[2] + '-' + toSplit[1] + '-' + toSplit[0];
        var search = $.fn.utilities('base64_encode',$.fn.utilities('utf8_encode',searchText));
        var url = current.data('url') +'/filter/'+filterType +'/profession/' + proffession +'/from/' + from + '/to/' + to;
        url = url +'/search/'+search;

        Q4U.ajaxGetRequest(url, {
            successCallback: function(data) {

                if (data.getData()!=undefined) {

                    if ($(document).find('.plans-tracking-layout').length && data.getData().html != undefined){
                        var currentModal = data.getData().html;

                        $(document).find('.plans-tracking-layout .scrollable-table').closest('div.row').replaceWith($(currentModal).find('.scrollable-table').closest('div.row'));
                        $(document).find('.plans-tracking-layout .q4-carousel-table-wrap').closest('div.row').replaceWith($(currentModal).find('.q4-carousel-table-wrap').closest('div.row'));

                    }

                    var self = $(document).find('.plans-tracking-layout').closest('.tab_panel').find('.panel_header');
                    $(document).find('.date').datetimepicker({
                        locale: $(document).find('html').attr('lang')
                    });

                    var windowWidth = window.innerWidth;

                    $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
                    $.fn.utilities('setScrollBarWidth', self.closest('.tab_panel').find('.scrollable-table'), windowWidth);

                    var widthT = $.fn.utilities('measureHiddenTable', self.closest('.tab_panel').find('table.table'),false);

                    setTimeout(function(){
                        self.closest('.tab_panel').find('table.table').closest('.mCSB_container').width(widthT);

                        $.fn.utilities('updateContentOnChange');
                    }, 300);

                    $('[data-toggle="table"]').bootstrapTable();




                }
            }
        });
    });


    $(document).on('click', '.tracking-details', function(e) {
        e.preventDefault();
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData().modal) {
                    $(document).find('.tracking-details-modal').remove();
                    var modal = data.getData().modal;
                    var startDate = [];

                    $('body').append(modal);
                    $(document).find('.tracking-details-modal').addClass('no-delete').modal('show');
                    var dateArray = $(document).find('.modal input[name=plan_date]').val().split('/');
                    $('#tracking-details-end_date').datetimepicker({
                        locale: $(document).find('html').attr('lang')
                    })

                    if(!$('#tracking-details-start_date').hasClass('disabled-input')){
                        $('#tracking-details-start_date').datetimepicker({
                            locale: $(document).find('html').attr('lang'),
                            format : 'DD/MM/YYYY',
                            minDate : dateArray[2] + '/' + dateArray[1] + '/' + dateArray[0]
                        }).show()
                    }


                    if($('#tracking-details-start_date input').val() != '' && $('#tracking-details-start_date input').val().length > 0){

                        $('#tracking-details-end_date input').prop('disabled', false);
                    } else {

                        $('#tracking-details-end_date input').prop('disabled', true);
                    }

                    $(document).on('dp.change','#tracking-details-start_date', function(e) {

                        $('#tracking-details-end_date input').prop('disabled', false);
                        $('#tracking-details-end_date').data("DateTimePicker").minDate(e.date).date(null)
                    });

                    $('[data-toggle="table"]').bootstrapTable();
                }
            }
        });
    });



    $(document).on('click', '.plans-date-tracking',function () {
        $(document).find('.plans-tracking-layout').remove()
        var self = $(this);
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {

                if (data.getData().html) {

                    $(document).find('.plans-list-layout').addClass('hidden').fadeOut();
                    $(document).find('.plans-list-layout').parent().append(data.getData().html);
                    /* Bootstrap Datepicker */
                    $('.date').datetimepicker({
                        locale: $(document).find('html').attr('lang')
                    }).show();


                    // date time validation for data-tracking page
                    $(document).find('.date-tracking-start_date').datetimepicker().on('dp.change', function(e) {
                        var incrementDay = moment(new Date(e.date));
                        $(this).closest('.plans-tracking-layout').find('.date-tracking-end_date').data('DateTimePicker').minDate(incrementDay);
                        $(this).data("DateTimePicker").hide();
                    });

                    $(document).find('.date-tracking-end_date').datetimepicker().on('dp.change', function(e) {
                        var decrementDay = moment(new Date(e.date));
                        $(this).closest('.plans-tracking-layout').find('.date-tracking-start_date').data('DateTimePicker').maxDate(decrementDay);
                        $(this).data("DateTimePicker").hide();
                    });

                    var windowWidth = window.innerWidth;

                    $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
                    $.fn.utilities('setScrollBarWidth', self.closest('.tab_panel').find('.scrollable-table'), windowWidth);

                    var widthT = $.fn.utilities('measureHiddenTable', self.closest('.tab_panel').find('table.table'),false);

                    setTimeout(function(){
                        self.closest('.tab_panel').find('table.table').closest('.mCSB_container').width(widthT);

                        $.fn.utilities('updateContentOnChange');
                    }, 300);

                    $(document).find('.plans-tracking-layout').removeClass('hidden').fadeIn();
                    self.closest('.tab_panel').find('.panel_header h2').text(__('Plans') + ' / ' + __('Date tracking'))
                    self.closest('.tab_panel').find(currentPage.filterTracking).trigger('click')
                    $('[data-toggle="table"]').bootstrapTable();

                }
            }
        });


        return false;
    });

    $(document).on('click','.back-plans-list-layout-link', function (e) {

        var self = $(this);
            $(document).find('.loader_backdrop').show();



        self.closest('.plans-tracking-layout').addClass('hidden').fadeOut();
        self.closest('.panel_content').find('.plans-list-layout').removeClass('hidden').fadeIn();
        self.closest('.tab_panel').find('.panel_header h2').text(__('Plans'))
        setTimeout(function () {
             $.fn.utilities('updateContentOnChange');
            $(document).find('.loader_backdrop').hide();
        },500)

        return false;
    });

    $(document).on('click', '.plans-list-layout .pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var current = $(this);
        Q4U.ajaxGetRequest(url, {
            successCallback: function(data) {
                if (data.getData()) {

                    if ($(document).find('.plans-list-layout').length && data.getData().plans != undefined){
                        var currentModal = data.getData().plans;

                        $(document).find('.plans-list-layout .scrollable-table').closest('div.row').replaceWith($(currentModal).find('.scrollable-table').closest('div.row'));
                        $(document).find('.plans-list-layout  .q4-carousel-table-wrap').closest('div.row').replaceWith($(currentModal).find(' .q4-carousel-table-wrap').closest('div.row'));
                    }

                    var currentTab = $(document).find('.plans-list-layout').closest('.tab_panel')
                    var self = currentTab.find('.panel_header');
                    var windowWidth = window.innerWidth;

                    $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
                    $.fn.utilities('setScrollBarWidth', self.closest('.tab_panel').find('.scrollable-table'), windowWidth);

                    var widthT = $.fn.utilities('measureHiddenTable', self.closest('.tab_panel').find('table.table'),false);

                    setTimeout(function(){
                        self.closest('.tab_panel').find('table.table').closest('.mCSB_container').width(widthT);

                        $.fn.utilities('updateContentOnChange');
                        currentTab.find('.total-res-selected').html($.fn.utilities('getObjectLength',CHECKED_PLANS));
                        $('.selectpicker').selectpicker()
                    }, 300);
                    if($.fn.utilities('getObjectLength',CHECKED_PLANS)<1){
                        CURRENT_PROFFESION_ID = '';
                        currentTab.find('.panel_footer').find('.plans-to-print-link').addClass('disabled-gray-button');
                        currentTab.find('.panel_footer').find('.plans-to-send').addClass('disabled-gray-button');
                    }else{

                        $.fn.utilities('checkCurrentProffesion',CURRENT_PROFFESION_ID,true);
                        $(document).find('.check-all-column').removeClass('disabled-input');
                    }

                    $('[data-toggle="table"]').bootstrapTable();
                }
            }
        });
    });


    $(document).on('click', '.load-plan-files', function(e) {
        e.preventDefault();
       var currentInput = $(this).closest('.upload-plans-title').find('.hide-upload').find('.load-images-input[type=file]').last().trigger('click');
        var currentModal = $(this).closest('.modal');

        currentInput.on('change', function(e) {
            $.fn.utilities('modalLoadPlans', this, e,FILES_BUFFER);
            var date = new Date();

            currentModal.find('.load-files-date-num').text(date.getDate() + '/' + (date.getMonth() +1) +'/' +  date.getFullYear())

            var minute = date.getMinutes()<10 ? '0' + date.getMinutes() : date.getMinutes();
            currentModal.find('.load-files-date-time').text(date.getHours() + ':' + minute);
            currentModal.find('.load-files-date').removeClass('hidden');
            if(currentModal.find('select[name=object_id]').val() && currentModal.find('select[name=profession_id]').val())
                currentModal.find('.upload-plans').removeClass(currentPage.disabledGrayButton);
            currentModal.find('.empty-list').hide();

            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
        })
    });

    $(document).on('change', '.modal .plans-modal-dialog-top select[name=object_id]', function(e) {
        e.preventDefault();
        if($(this).val() && $(document).find('.modal .upload-plans-scroll ul>li').length > 0 && $(document).find('.modal select[name=profession_id]').val())
            $(this).closest('.modal').find('.upload-plans').removeClass(currentPage.disabledGrayButton)
        else
            $(this).closest('.modal').find('.upload-plans').addClass(currentPage.disabledGrayButton)
    });
    $(document).on('change', '.modal .plans-modal-dialog-top select[name=profession_id]', function(e) {
        e.preventDefault();
        if($(this).val() && $(document).find('.modal .upload-plans-scroll ul>li').length > 0 && $(document).find('.modal select[name=object_id]').val())
            $(this).closest('.modal').find('.upload-plans').removeClass(currentPage.disabledGrayButton)
        else
            $(this).closest('.modal').find('.upload-plans').addClass(currentPage.disabledGrayButton)
    });

    $(document).on('click', '.upload-plans', function(e) {
        e.preventDefault();
        LOADER = false;
        var modal = $(this).closest('.modal')
        var currentForm = modal.find('form');
        var urlPost = currentForm.attr('action');
        modal.find('.modal-progress-bg').show();
        var uploads = modal.find('.upload-plans-title .hide-upload')

        var plansCount = 0;
        for(var key in FILES_BUFFER){
            plansCount++;
        }
        var length = modal.find('.upload-plans-scroll ul').find('li').length;
        var elem = modal.find(".modal-bar");
        var addWidth = parseInt(100 *parseFloat(1/(plansCount)));
        var width = 0;
        var i = 1;
        var reject = 0;
        var deleteKey = '';
        (async () => {

            for(var key in FILES_BUFFER){
                deleteKey = key;
                var formData = new FormData();
                var data = getFormData(currentForm);
                modal.find('.upload-plans').addClass(currentPage.disabledGrayButton)
                data["csrf"] = Q4U.getCsrfToken();
                formData.append("Data", JSON.stringify(data));

                if(FILES_BUFFER[key]!=undefined){
                    formData.append('file',FILES_BUFFER[key]);
                    var res = await $.ajax({//можно использовать только асинхронные функции
                        url: urlPost,
                        type: 'POST',
                        data:formData,
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                    });

                    if(res){
                            width =  (plansCount == i) ? 100 : width + addWidth;
                            $('.upload-plans-title').find('.q4-plans-count').html('('+plansCount+')')

                            elem.css('width', width + '%');
                            elem.closest('.modal-progress-bar').find('.progress-bar-text').html(__("Load") + ' ' + i + ' ' +__("from") + ' ' +  plansCount);
                            elem.closest('.modal-progress-bar').find('.progress-bar-status').html(__("loading") +' ('+ FILES_BUFFER[key].name +')');


                        if(res.errors){
                            reject++;
                        }else{
                            modal.find('.upload-plans-wrapper').find('li.'+deleteKey).remove();
                        }
                        if(i==plansCount){
                            modal.find('.modal-progress-bg').fadeOut()
                            $('.upload-plans-title').find('.q4-plans-count').html('')
                            modal.find('.upload-plans').text(__('Done')).removeClass('upload-plans').addClass("close-upload-plans-modal").removeClass(currentPage.disabledGrayButton)
                            FILES_BUFFER = [];
                            LOADER = true;

                            if(reject > 0){
                                Q4U.alert(__('You have $s rejected files').replace(/%s/g, reject), {
                                    type: "danger",
                                    confirmText: __("OK")
                                });
                            }
                        }
                        i++;
                    }

                }

        }

        })().catch((err) => {

            reject++;
            if(i==plansCount){
                modal.find('.modal-progress-bg').fadeOut()
                $('.upload-plans-title').find('.q4-plans-count').html('')
                modal.find('.upload-plans').text(__('Done')).removeClass('upload-plans').addClass("close-upload-plans-modal").removeClass(currentPage.disabledGrayButton)
                FILES_BUFFER = [];
                LOADER = true;

                if(reject > 0){
                    Q4U.alert(__('You have $s rejected files').replace(/%s/g, reject), {
                        type: "danger",
                        confirmText: __("OK")
                    });
                }
            }
            i++;
        })

    });

    $(document).on('click', '.close-upload-plans-modal', function(e) {
        e.preventDefault();
        $(document).find('.plans-list-layout .filter-plans').trigger('click')
        $(this).closest('.modal').modal('hide')
    });

    $(document).on('click', '.cancel-upload-files', function(e) {
        e.preventDefault();
        FILES_BUFFER = [];
        LOADER = true;
    });



    $(document).on('click', currentPage.qCListSelector, function(e) {
        e.preventDefault();

        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {

                if (data.getData().modal) {
                    $(document).find('.modal').modal('hide');
                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('.modal').modal('show');
                     setTimeout(function () {

                        $.fn.utilities('setModalCarouselWidth', '.q4-carousel-table-wrap', $('.modal-dialog').width());
                        $.fn.utilities('setCarouselDirection', ".q4-carousel-table", 10);
                        $.fn.utilities('owlPagination', '.q4-carousel-table');

                    },300);

                    $(document).find('[data-toggle="table"]').bootstrapTable();
                }
            }
        });
    });


    $(document).on('click', currentPage.qcFilterSettings, function(e) {
        e.preventDefault();
        var self = $(this);
        var status = self.data('status');
        var html = self.html();
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData()) {

                    if ($(document).find('.qc-list-modal').length && data.getData().modal != undefined){
                        var currentModal = data.getData().modal;
                        $(document).find('.q4_form').replaceWith($(currentModal).find('.q4_form'));
                    }

                    $(document).find(".filter-settings-button").removeClass('active');
                    $(document).find(".filter-settings-button[data-status='" + status + "']").addClass('active');

                    $(document).find('.q4-inside-filter-mobile a.q4-inside-select-filter').html(html);

                    if($(document).find('html').hasClass('rtl')){
                        $(document).find('body').removeAttr('style');

                    }

                    $.fn.utilities('setModalCarouselWidth', '.q4-carousel-table-wrap', $('.modal-dialog').width());
                    $.fn.utilities('setCarouselDirection', ".q4-carousel-table", 10);
                    $.fn.utilities('owlPagination', '.q4-carousel-table');

                    $(document).find('[data-toggle="table"]').bootstrapTable();


                }
            }
        });
    });



    $(document).on('click', currentPage.addTaskSelector, function(e) {
        e.preventDefault();
        if ($(document).find(currentPage.taskFormClass + ' .new-el [data-attr="name"]').val() != '') {
            var id = Q4U.timestamp();
            var html = $(document).find(currentPage.taskFormClass + ' .el-pattern')[0].outerHTML;

            html = html.replace(/%s/g, '+' + id);
            html = html.replace(/data-name/g, 'name');

            $(document).find(currentPage.taskFormClass + ' tbody:first').prepend($(html).removeClass('hidden').removeClass('el-pattern').addClass('new-el'));
        }


    });


    $(document).on('click', currentPage.addCertSelector, function(e) {
        e.preventDefault();
        if ($(document).find(currentPage.certFormClass + ' .new-el [data-attr="name"]').val() != '') {
            var id = Q4U.timestamp();
            var html = $(document).find(currentPage.certFormClass + ' .el-pattern')[0].outerHTML;

            html = html.replace(/%s/g, '+' + id);
            html = html.replace(/data-name/g, 'name');

            $(document).find(currentPage.certFormClass + ' tbody:first').prepend($(html).removeClass('hidden').removeClass('el-pattern').addClass('new-el'));
        }


    });


    $(document).on('click', currentPage.addPropSelector, function(e) {
        e.preventDefault();
        if ($(document).find(currentPage.propFormClass + ' .new-el [data-attr="name"]').val() != '') {
            var id = Q4U.timestamp();
            var html = $(document).find(currentPage.propFormClass + ' .el-pattern')[0].outerHTML;

            html = html.replace(/%s/g, '+' + id);
            html = html.replace(/data-name/g, 'name');
            $(document).find(currentPage.propFormClass + ' tbody:first').prepend($(html).removeClass('hidden').removeClass('el-pattern').addClass('new-el').addClass(id));
            $(document).find(currentPage.propFormClass + ' tbody:first').find('.new-el').find('.property-name-input').addClass('q4_required')
            $(document).find('.scrollable-table .scrollable-date').each(function() {

                $(this).datetimepicker({locale:LANG}).show();

                $(this).datetimepicker({ widgetParent: 'body' })
                    .on('dp.show', function() {
                        var top = ($(this).offset().top - 270);
                        var left = $(this).offset().left;
                        if ($(this).offset().top - 400 <= 0) { //display below if not enough room above
                            top = $(this).offset().top + $(this).height() + 10;
                        }
                        $(document).find('.bootstrap-datetimepicker-widget').css({
                            'top': top + 'px',
                            'left': left + 'px',
                            'bottom': 'auto',
                            'right': 'auto'
                        });

                    });

            });
        }


    });

    $(document).on('click', '.cert-files', function(e) {
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

    $(document).on('click', '.cert-files-upload', function(e) {
        e.preventDefault();
        var el = $(this).closest('form').find('input[type="file"]');
        el[0].value = null;
        el.trigger('click');
    });



    $('body').on('renewCertificationModal', function(e, data) {
        if ($('.modal').length && data.modal != undefined)
            $('.modal .q4_modal').replaceWith($(data.modal).find('.q4_modal'));
        if ($('.certifications-form').length && data.certificationsForm != undefined) {
            $('.certifications-form').replaceWith(data.certificationsForm);

        }

        $('[data-toggle="table"]').bootstrapTable();
    });

    $(document).on('click', currentPage.qcCurrentSelector, function(e) {
        e.preventDefault();
        var modalId = $(this).data('modalid');
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData().modal) {

                    $(document).find('.quality-control-modal').remove();
                    $(document).find('#choose-plan-modal').remove();
                    $(document).find('#choose-plan-modal-mobile').remove();
                    $(document).find('.print-quality-control').remove();
                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('.quality-control-modal').addClass('no-delete').modal('show');

                    setTimeout(function() {
                        var self = $(document).find('#' + modalId);
                        var modalWidth = self.find('.modal-dialog').width();

                        var tasksItemCount = $('.tasks-full-description li:visible').length;
                        var tasksItemsWidth = tasksItemCount * (350 + 40)+20;

                        // Add scroll to tasks
                        self.find('.tasks-full-description-box').width(modalWidth - 40);
                        self.find('.tasks-full-description').width(tasksItemsWidth);

                    }, 300)

                }
            }
        });
    });


    $(document).on('click', '.add-plan', function(e) {
        e.preventDefault();

        console.log('#add-plans-modal ');
        var html = currentPage.planMarkup;

        $(document).find('#add-plans-modal').find('table tbody:first').prepend(html);
        $(this).closest('.panel_body').find('table tbody tr:first [data-col="structure"]')
            .html('<span>' + selectProfession + '</span>');

    });

    $(document).on('click', currentPage.addPlanSelectorModal, function(e) {
        e.preventDefault();
        FILES_BUFFER = [];
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData().modal) {
                    $(document).find('.modal').modal('hide');
                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('.modal').modal('show');

                    if($(document).find('html').hasClass('rtl'))
                    {
                        $(document).find('.date').datetimepicker({locale:'he'}).show();
                    }else{
                        $(document).find('.date').datetimepicker({locale:'en'}).show();
                    }
                }
            }
        });
    });

    $(document).on('click', '.copy-plan', function(e) {
        e.preventDefault();

        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData().modal) {
                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('.copy-plans-modal').modal('show');
                }
            }
        });

    });

    $(document).on('click', '.delete-plan', function(e) {
        e.preventDefault();
        var planName = $(this).closest('tr').find('td.plan-name-field input').val()
        var filterButton = $(this).closest('.tab_panel').find('.plans-list-layout .filter-plans');
        Q4U.confirm(__('Are you sure, you want') + ' ' +  __('delete') + ' ' + planName +' '+ __('plan') + '?', {
            confirmCallback: function(el, params) {
                Q4U.ajaxGetRequest($(params.custom.el).data('url'), {
                    successCallback: function(data) {
                        filterButton.trigger('click')
                        $(params.custom.el).parents('tr').remove()
                    }
                });
            },
            hilight: planName,
            type: "danger",
            confirmText:  __('Delete'),
            custom: {
                el: this
            }
        });

    });

    $(document).on('click', '.delete-tracking', function(e) {
        e.preventDefault();
        // var filterButton = $(this).closest('.tab_panel').find('.plans-tracking-layout .filter-tracking');
        var trackingId = $(this).data('id');
        var searchText = $(document).find('.plans-tracking-layout .search-input').val();
        Q4U.confirm(__('Are you sure, you want') + ' ' +  __('delete') + ' ' + trackingId +' '+ __('tracking') + '?', {
            confirmCallback: function(el, params) {
                Q4U.ajaxGetRequest($(params.custom.el).data('url'), {
                    successCallback: function(data) {
                        // filterButton.trigger('click')

                        if(searchText.length > 0){
                            $(document).find('.plans-tracking-layout .search-tracking').trigger('click');

                        }else{
                            $(document).find('.plans-tracking-layout .filter-tracking').trigger('click');
                        }

                        $(params.custom.el).parents('tr').remove()

                    }
                });
            },
            hilight: trackingId,
            type: "danger",
            confirmText:  __('Delete'),
            custom: {
                el: this
            }
        });

    });


    $(document).on('click', '.plan-details, .add-plan-edition', function(e) {

        e.preventDefault();

        var currentSheetNumber = $(this).closest('tr').find('.plans-sheet-number-cell .plans-sheet-number-val').text();

        CURRENT_PLAN_PAGE = $(document).find('.plans-list-layout .pagination .active a').text();
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData().modal) {

                    $(document).find('.modal').remove();
                    var modal = data.getData().modal;

                    $('body').append(modal);
                    $(document).find('.modal').modal('show');

                    if($(document).find('html').hasClass('rtl'))
                    {
                        $(document).find('.date').datetimepicker({locale:'he'}).show();
                    }else{
                        $(document).find('.date').datetimepicker({locale:'en'}).show();
                    }
                    $('[data-toggle="table"]').bootstrapTable();
                }

                $(document).find('#update-plan-modal').find('.plans-sheet-number').val(currentSheetNumber);
            }
        });
    });

    $(document).on('click', '.enable-plan-action > .checkbox-wrapper', function () {

        var checked = $('table .enable-plan-action input[type=checkbox]:checked').length;

        if(checked > 0){
            $(document).find('.plans-to-print-link, .plans-to-send').removeClass('disabled-link');
        } else {
            $(document).find('.plans-to-print-link, .plans-to-send').addClass('disabled-link');

        }

    });


    $(document).on('click', '.copy-property', function(e) {

        Q4U.confirm(__('Are you sure, you want') + __('copy object?'), {
            confirmCallback: function(el, params) {
                Q4U.ajaxGetRequest($(params.custom.el).data('url'));
            },
            hilight: "object",
            type: "normal",
            confirmText:  __('Confirm'),
            custom: {
                el: this
            }
        });
    });

    $(document).on('click', '.call-professions-list-modal', function(e) {
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

    $(document).on('change keyup', '.place-number', function() {
        var val = parseInt($(this).val());
        if (!isNaN(val) && val != 0) {
            $(document).find('.floor-numbers').removeClass('select-imitation');
        } else {
            $(document).find('.floor-numbers').addClass('select-imitation');
        }
    });

    $(document).on('change', '.qc-prop', function() {
        var option = '';
        var optMarckup = '';
        var select = $(this).parents('.modal').find('select[name=floors]');
        select.closest('.multi-select-box').find('.select-imitation-title').text( __('Please select'));

        var currentEl = $(this).find('option[value=' + $(this).val() + ']');
        var max = currentEl.data('max');
        for (var i = currentEl.data('min'); i <= max; i++) {
            optMarckup  +=  '<div class="checkbox-list-row">'+
                                '<span class="checkbox-text">' +
                                     '<label class="checkbox-wrapper-multiple inline" data-val="' + i + '">'+
                                        '<span class="checkbox-replace"></span>'+
                                        '<i class="checkbox-list-tick q4bikon-tick"></i>'+
                                    '</label>' + '<span class="checkbox-text-content bidi-override">' + i +'</span>'+
                                '</span>'+
                            '</div>';
            option += '<option value="' + i + '">' + i + '</option>';
        }

        select.html(option);
        select.closest('.multi-select-box').find('.checkbox-list .mCSB_container').html(optMarckup);


    });

    $(document).on('change', '.plan-profession, .plan-craft', function() {

        if ($(this).hasClass('plan-profession')) {
            var needReselect = false;
            var attrs = ($(this).find('option:selected').data('crafts')).toString().split(',');
            var selectedCraftId = $(document).find('.plan-craft').val();
            $(document).find('.plan-craft option').removeClass('hidden');
            $(document).find('.plan-craft option').each(function() {
                if ($.inArray(($(this).val()).toString(), attrs) < 0) {
                    if ($(this).val() == selectedCraftId) {
                        needReselect = true;
                    }
                    $(this).removeAttr('selected', '');
                    $(this).addClass('hidden');
                }
            });
            if (needReselect) {
                $(document).find('.plan-craft').val($($(document).find('.plan-craft option:not(.hidden)')[0]).val());
            }
        } else {
            var attrs = ($(this).find('option:selected').data('professions')).toString().split(',');
            var selectedProfessionId = $(document).find('.plan-profession').val();
            $(document).find('.plan-profession option').each(function() {
                if ($.inArray(($(this).val()).toString(), attrs) < 0) {
                    if ($(this).val() == selectedProfessionId) {
                        needReselect = true;
                    }
                    $(this).removeAttr('selected', '');
                }
            });
            if (needReselect) {
                $(document).find('.plan-profession').val(attrs[0]);
            }
        }
    });


    $(document).on('click', '.show-proj-images', function(e) {
        e.preventDefault();
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData().modal) {
                    $(document).find('.modal').modal('hide');
                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('.modal').modal('show');
                    // $('.display-project-images').mCustomScrollbar({ axis: 'y' });
                }
            }
        });
    });

    $(document).on('click', '.create-quality-control', function(e) {
        e.preventDefault();

        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData().modal) {

                    $(document).find('.create-modal').remove();
                    $(document).find('.choose-plan-modal').remove();
                    $(document).find('#choose-plan-modal').remove();
                    $(document).find('#choose-plan-modal-mobile').remove();
                    // $(document).find('.print-quality-control').remove();
                    var modal = data.getData().modal;
                    $('body').append(modal);

                    $(document).find('.create-modal').addClass('no-delete').modal('show');


                    var self = $(document).find('#quality-control-modal');
                    var modalWidth = self.find('.modal-dialog').width();
                    $('.date').datetimepicker({
                        locale: $(document).find('html').attr('lang')
                    }).show();
                    setTimeout(function() {
                        var tasksItemCount = $('.modal .tasks-full-description li:visible').length;
                        var tasksItemsWidth = tasksItemCount * (350 + 30);

                        // Add scroll to tasks
                        $('.tasks-full-description-box').width(modalWidth - 60);
                        $('.tasks-full-description').width(tasksItemsWidth);

                        $.fn.utilities('owlPagination', '.modal .q4-owl-carousel');


                    }, 400)

                }

            }
        });
    });


    $(document).on('click', '.qc-choose-plan a,.qc-change-plan a', function(e) {
        e.preventDefault();

         setTimeout(function () {

            $.fn.utilities('setModalCarouselWidth', '.q4-carousel-table-wrap', $('#choose-plan-modal-mobile .modal-dialog').width());
            $.fn.utilities('setCarouselDirection', ".q4-carousel-table", 10);
            $.fn.utilities('owlPagination', '.q4-carousel-table');

        },300);

    });

    // Check all rows in table
    $(document).on('change', '.check-all-column input[type=checkbox]', function () {

        var self =  $(this);
        var label =self.closest('label');
        var checkedColumn = self.is(':checked');

        checkedColumn ? label.attr('title',__("unselect all on page")):label.attr('title',__("select all on page"))


        self.closest('table').find('.selectable-column input[type=checkbox]').each(function(){
            var selfCheckbox = $(this);
            var disabled = selfCheckbox.closest('.selectable-column').hasClass('disabled-input');
            var checkedBox = selfCheckbox.is(':checked');
            if(!disabled){
                if(checkedColumn){
                    if(!checkedBox){
                        selfCheckbox.trigger('click')
                    }
                }else{
                    if(checkedBox){
                        selfCheckbox.trigger('click')
                    }
                }
            }
        })

    });



    $(document).on('change', '.select-user-action label.checkbox-wrapper input[type=checkbox],.enable-plan-action label.checkbox-wrapper input[type=checkbox]', function() {
        var self = $(this)
        var printLandscape = $(document).find('.print-landscape-mode');
        var table = printLandscape.find('.printable-table-first .page-break');
        var firstPage = $(document).find('.print-landscape-mode .first-page');
        var tablePrintTd = self.closest('tr').find('.table-print-td');
        var planId = tablePrintTd.data('planid');
        var tableId = tablePrintTd.data('id');
        var profession = tablePrintTd.data('profession');
        var professionId = tablePrintTd.data('professionid');
        var planLength = 0;
        if(self.is(':checked')){
            CHECKED_PLANS['plans_' + tablePrintTd.data('planid') + '_id'] = planId;

            planLength = $.fn.utilities('getObjectLength',CHECKED_PLANS);

            if(planLength == 1){
                CURRENT_PROFFESION_ID = professionId;
                $(document).find('.check-all-column').removeClass('disabled-input');
                $(document).find('.current-profession-id').val(CURRENT_PROFFESION_ID)

                $.fn.utilities('checkCurrentProffesion',CURRENT_PROFFESION_ID,true)

            }
            var property = tablePrintTd.data('property');
            var currentTable ='';
            if($(document).find('.print-landscape-mode table[data-id="'+ tableId +'"]').length>0){
                currentTable = $(document).find('.print-landscape-mode table[data-id="'+ tableId +'"]');


            }else{
                var newTable = table.clone();
                $(document).find('.print-landscape-mode .printable-table-other').append(newTable);
                newTable.find('th[data-type=property]').text(__('Property')+ ":" + property);
                newTable.find('table').attr('data-id',tableId);
                currentTable = newTable;

            }
            var line =  self.closest('tr').find('.table-print-td table tr').clone();
            if(profession && property){
                currentTable.find('tbody').append(line);
            }

            self.closest('.panel_content').find('.wrap_delete_users').removeClass('hide');
            self.closest('.panel_content').find('.panel_footer').find('.plans-to-print-link').removeClass('disabled-gray-button');
            self.closest('.panel_content').find('.panel_footer').find('.plans-to-send').removeClass('disabled-gray-button');

        }else{
            delete CHECKED_PLANS['plans_' + tablePrintTd.data('planid') + '_id'];
            var currentTr = $(document).find('.print-landscape-mode .printable-table-other [data-id='+planId+']').closest('tr');
            var currentTableRemove = currentTr.closest('table');
            currentTr.remove()

            if(currentTableRemove.find('tbody tr').length<1){
                currentTableRemove.closest('.page-break').remove();
            }
            if(self.closest('.panel_content').find('.checkbox-wrapper input[type=checkbox]').is(":checked").length>0){
                self.closest('.panel_body').find('.wrap_delete_users').removeClass('hide');
            }
        }
        var plansJson = JSON.stringify(CHECKED_PLANS)
        $(document).find('.selected-plans').val(plansJson)

        var userLength = $(this).closest('.panel_content').find('.select-user-action').find("input[type=checkbox]:checked").length;
        planLength = $.fn.utilities('getObjectLength',CHECKED_PLANS);
        $(this).closest('.panel_content').find('.total-res-selected').html($.fn.utilities('getObjectLength',CHECKED_PLANS));
        if (userLength == 0) {
            $(this).closest('.panel_content').find('.wrap_delete_users').addClass('hide');
        }
        if (planLength == 0) {
            CURRENT_PROFFESION_ID = '';
            $.fn.utilities('checkCurrentProffesion',CURRENT_PROFFESION_ID,false)
            $(document).find('.current-profession-id').val('')
            $(document).find('.selected-plans').val('')
            $(document).find('.check-all-column').addClass('disabled-input');

            $(this).closest('.panel_content').find('.panel_footer').find('.plans-to-print-link').addClass('disabled-gray-button');
            $(this).closest('.panel_content').find('.panel_footer').find('.plans-to-send').addClass('disabled-gray-button');
        }



    });



    /**Print Plans in projects page */
    $(document).on('click', '.plans-to-print-link', function() {
        $('body>.print-landscape-mode').remove();

        var printLandscape = $(document).find('.print-landscape-mode');
        var table = printLandscape.find('.printable-table-first .page-break');
        var firstPage = $(document).find('.print-landscape-mode .first-page');
        var error = '';
        var trackingId = '';
        var urlTracking = $(document).find('.plans-list-layout').data('trackingurl');

        var fPage = firstPage.clone();
        var profession =  $(document).find('.plans-list-layout [data-professionid='+CURRENT_PROFFESION_ID+']:first').data('profession')
        $(document).find('.print-landscape-mode').removeClass('page-break').find('.first-page').replaceWith(fPage.removeClass('hidden'))
        var firstTable = $('.print-landscape-mode table')[1];
        $(firstTable).closest('.page-break').find('.printable-logo').remove();
        $(firstTable).closest('.page-break').removeClass('page-break');
        $(fPage).find('.pr-proff-name').html(profession + '&nbsp;');

        var formData = new FormData();
        formData["csrf"] = Q4U.getCsrfToken();
        formData["x-form-secure-tkn"] = "";
        for(var key in CHECKED_PLANS){
            formData[key] = CHECKED_PLANS[key];
        }
        $.ajax({
            url: urlTracking,
            method: 'POST',
            data:JSON.stringify(formData),
            cache: false,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(data){

                if(data['id']!=undefined){

                    trackingId = data['id'];
                    setTimeout(function(){
                        if(trackingId>0){
                            $(printLandscape).find('.pr-tracking-val').text(trackingId);
                            var content = printLandscape.clone()
                            $('body').append(content);
                            window.print();
                            $('body>.print-landscape-mode').remove();

                            }
                    },1000);
                }else if(data.errors!=undefined){
                    error = data.errors;

                    if(error.length>0){
                        Q4U.alert(__(error), {
                            type: "danger",
                            confirmText: __("OK")
                        });
                    }
                }
            },
        });

    });
    $(document).on('click', '.plans-to-send', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var id = $(this).data('id');
        var dir = $(document).find('html').hasClass('rtl') ? "dir:'rtl'," : '';

        $(document).find('#choose-sender-modal').remove();
        $(document).find('#qc-list-users-modal').remove();
        Q4U.ajaxGetRequest(url, {
            successCallback: function(data) {
                if (data.getData().modal) {

                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('#choose-sender-modal').find('form').attr('action', url);

                    $(document).find('#choose-sender-modal').find('form').append('<input type="hidden" name="project_id" value="'+id+'">')
                    $(document).find('#choose-sender-modal').find('form .plans-tosend').remove();
                    for(var key in CHECKED_PLANS){
                       $(document).find('#choose-sender-modal').find('form').append('<input type="hidden" class="plans-tosend" name="plans_'+CHECKED_PLANS[key]+'_id" value="'+CHECKED_PLANS[key]+'">') ;
                    }
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

                }
            }
        });

    });

    $('body').on('projectUsersUpdated', function(e, data) {
        if ($('.project-users-form').length && data.projectUsersForm != undefined) {
            $('.project-users-form').replaceWith(data.projectUsersForm);
            $(document).find('.modal').modal('hide');
            $('[data-toggle="table"]').bootstrapTable();
        }

    });

    $('body').on('placeError', function(e, data) {

        if(data.placeErrorsList){

            var list =data.placeErrorsList;

            for(var i=0;i<list.length;i++){

                var tr = $(document).find('[data-planid="'+list[i]+'"]')
                tr.find('.plan-place-custom-number').addClass('q4_required');
                tr.find('.plan-place-custom-number').addClass('error');
            }
            Q4U.alert(__('Incorrect element number'), {
                type: "danger",
                confirmText: __("OK")
            });
        }

    });

    $('body').on('projectPlansUpdated', function(e, data) {

        if ($('.call-professions-list-modal').closest('.panel_body').length && data.projectPlansForm != undefined) {

            $(document).find('.modal').modal('hide');
            $('.call-professions-list-modal').closest('.panel_body').find('.filter-plans').trigger('click');

        }

    });

    $(document).on('click','.open-report-modal',function (e) {

        var url  = $(this).data('url');

        $.ajax({
            url: url,
            method: 'GET',
            cache: false,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(data){

                if(data['html']!=undefined){
                // var modal = data.getData().modal;
                 $(document).find('.modal').remove();
                var modal = data.html;
                $('body').append(modal);
                $(document).find('.modal').modal('show');


                }else if(data.errors!=undefined){
                    error = data.errors;

                }
            },
        });
    })

    /**
     * Tasks on update event handler
     */
    $('body').on('tasksUpdated', function(e, data) {
        if ($('.tasks-form').length && data.tasksForm != undefined) {
            $('.tasks-form').replaceWith(data.tasksForm);
            $('[data-toggle="table"]').bootstrapTable();
            $(document).find('.checkbox-list-no-scroll').addClass('hidden')
        }

    });

    $('body').on('certificationsUpdated', function(e, data) {
        //certificationsForm
        if ($('.certifications-form').length && data.certificationsForm != undefined) {
            $('.certifications-form').replaceWith(data.certificationsForm);
            $('[data-toggle="table"]').bootstrapTable();
        }
    });

    $('body').on('propsUpdated', function(e, data) {
        if ($('.props-form').length && data.propsForm != undefined) {
            $('.props-form').replaceWith(data.propsForm);
            $('[data-toggle="table"]').bootstrapTable();
        }

    });

    $(document).on('change keyup', '.floors-from, .floors-to', function(e) {
        e.preventDefault();
        if ($(this).val() != '-') {
            var val = parseInt($(this).val());
            var secondVal = 0;
            if ($(this).hasClass('floors-from')) {
                if (val > 0) {
                    val = 0;
                }
                secondVal = parseInt($(this).parents('td').find('.floors-to').val());
            } else {
                secondVal = parseInt($(this).parents('td').find('.floors-from').val());
                if (val < 0) {
                    if (secondVal > val || val < -1) {
                        if (secondVal >= 0) {
                            val = 0;
                        } else {
                            val = -1;
                        }
                    }
                }
            }
            if (isNaN(val)) {
                val = 0;
            }

            if (isNaN(secondVal)) {
                secondVal = 0;
            }

            $(this).val(isNaN(val) ? 0 : val);

            var placesInput = $(this).parents('tr').find('.places-count');
            var totalPlaces = isNaN(parseInt(placesInput.val())) ? 0 : parseInt(placesInput.val());
            var minPlaces = 1;
            if (val != secondVal) {
                minPlaces = Math.abs(val) + Math.abs(secondVal) + 1;
            }

            if (minPlaces > totalPlaces) {
                placesInput.val(minPlaces);
                placesInput.change();
            } else {
                placesInput.val(totalPlaces);
            }


        }

    });

    $(document).on('click', '.places-count', function() {
        $(this).select();
    });

    $(document).on('change keyup', '.places-count', function(e) {
        e.preventDefault();
        var el = this;
        setTimeout(function() {
            var x = parseInt($(el).parents('tr').find('.floors-from').val());
            var y = parseInt($(el).parents('tr').find('.floors-to').val());
            if (isNaN(x)) {
                x = 0;
            }
            if (isNaN(y)) {
                y = 0;
            }
            var totalFloors = 1;
            if (x != y) {
                totalFloors = Math.abs(x) + Math.abs(y) + 1;
            }

            var val = parseInt($(el).val());
            if (isNaN(val)) {
                val = 0;
            }

            if (val < totalFloors) {
                $(el).val(totalFloors);
            }

        }, 1000);
    });

    /**
     * Удаление объекта
     */
    $(document).on('click', '.delete-prop', function(e) {
        e.preventDefault();

        Q4U.confirm(__('Are you sure, you want') + __('delete object?'), {
            confirmCallback: function(el, params) {
                Q4U.ajaxGetRequest($(params.custom.el).data('url'), {
                    successCallback: function(data) {
                        $(params.custom.el).parents('tr').remove()
                    }
                });
            },
            hilight: 'object',
            type: 'danger',
            confirmText: __('Delete'),
            custom: {
                el: this
            }
        });
    });

    /**
     *  Манипуляция с объектами и этажами
     */
    $(document).on('click',
        ' .property-structure-list-group.enabled .floor-copy,' +
        ' .property-structure-list-group.enabled .floor-delete,' +
        ' .place-copy,' +
        ' .place-delete',
        function(e) {
            e.preventDefault();
            var msg = __('Are you sure, you want');
            var higlight = '';
            var type = 'normal';
            var confirmText = __('Confirm');
            if ($(this).hasClass('floor-copy')) {
                msg += __('copy floor?');
                higlight = __('Floor-');
            } else if ($(this).hasClass('floor-delete')) {
                msg += __('delete a floor?');
                higlight = __('Floor-');
                type = 'danger';
                confirmText = __('Delete');
            } else if ($(this).hasClass('place-add')) {
                msg += __('add new element?');
                higlight = __('element');
            } else if ($(this).hasClass('place-copy')) {
                msg += __('copy element?');
                higlight = __('Element');
            } else if ($(this).hasClass('place-delete')) {
                msg += __('delete element?');
                higlight = __('Element');
                type = 'danger';
                confirmText = __('Delete');
            }

            Q4U.confirm(msg, {
                confirmCallback: function(el, params) {
                    Q4U.ajaxGetRequest($(params.custom.el).data('url'), {
                        successCallback: function(data) {
                            if (data.getData().struct) {
                                var struct = data.getData().struct;
                                $(document).find('.property-tab-content').html(struct);
                                normalizeObjectStruct();
                            }
                            if (data.getData().modal) {
                                $(document).find('.modal').modal('hide');
                                $(document).find('.modal').remove();
                                var modal = data.getData().modal;
                                $('body').append(modal);
                                $(document).find('.modal').modal('show');
                                $('.selectpicker').selectpicker();
                            }
                        },
                    });
                },

                hilight: higlight,
                type: type,
                confirmText: confirmText,
                custom: {
                    el: this
                }
            });
        });
    function formatSelection(obj, container) {

        var originalOption = obj.element;
        return "<i class='" + $(originalOption).data('icon') + "'></i>" + obj.text;
    }
    function select2Object(selector){
        $("#constaction-object").select2({
            templateResult: function (data, term) {
                var originalOption = data.element;

                return "<i class='" + $(originalOption).data('icon') + "'></i> " + data.text;
            },
            // templateSelection: formatSelection,
            width: "100%",
            tags: true,
            dropdownParent:$('.choose-icons-search'),
            escapeMarkup: function(m) { return m; }
        });
    }
    $(document).on('click',' .place-edit, .place-add',function(){
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData().struct) {
                    var struct = data.getData().struct;
                    $(document).find('.property-tab-content').html(struct);
                    normalizeObjectStruct();
                }
                if (data.getData().modal) {
                    $(document).find('.modal').modal('hide');
                    $(document).find('.modal').remove();
                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('.modal').modal('show');
                    $('.selectpicker').selectpicker({size:3,dropupAuto:false})
                    select2Object("#constaction-object");

                }
            },
        });
    })

    $(document).on('select2:select',"#constaction-object", function (e) {
        var data = e.params.data.element;
        if($(data).data('icon') !=undefined)
            $('.choose-icons .selectpicker').selectpicker('val',$(data).data('icon'));

    });


    $('body').on('placeCreated', function(e, data) {
        $(document).find('.modal').modal('hide');
        $(document).find('.property-tab-content').html(data.struct);
        normalizeObjectStruct();
        $('[data-toggle="table"]').bootstrapTable();
    });
    $('body').on('qualityControlCreated', function(e, data) {

        if($(document).find('html').data('mobile')==undefined){
            $(document).find('.property-tab-content').html(data.struct);
            normalizeObjectStruct();
        }

        var active = $(document).find('.property-structure-list-group .owl-item.active');
        active.find('.count.apartment-circle').removeClass('gray').addClass('orange');
        active.find('.apartment-number').addClass('quality-control-list');

        FILES_BUFFER = [];
        $(document).find('.modal').modal('hide');
        $('[data-toggle="table"]').bootstrapTable();
    });

    $(document).on('click', '.floor-row .apt', function(e) {
        $('.apt-tools').hide();
        $(this).find('.apt-tools').show();
        e.preventDefault();
        return false;
    });

    $(document).on('click', '.place-quality-controls-list', function(e) {
        $('.apt-tools').hide();
        e.preventDefault();
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData().struct) {
                    var struct = data.getData().struct;
                    $(document).find('.property-tab-content').html(struct);
                }

                if (data.getData().modal) {
                    $(document).find('.modal').modal('hide');
                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('.modal').modal('show');
                }
                $('[data-toggle="table"]').bootstrapTable();
            }
        });
        return false;
    });

    $(document).on('click', '.w-center', function(e) {
        e.preventDefault();
        $('.apt-tools').hide();
    });


    $(document).on('click', '.pr-object-show-struct', function(e) {
        e.preventDefault();

        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                $(document).find('.property-tab-content').html(data.getData().struct);
                normalizeObjectStruct();


                var allFloors = $(document).find('.wrap-property-structure-list').data('floor');
                var middleFloor = parseInt(allFloors/2);

                 // Switch floors wrap-property-structure-list /
                $('.wrap-property-structure-list').jCarouselLite({
                    btnNext: ".next",
                    btnPrev: ".prev",
                    vertical: true,
                    visible: 1,
                    circular:false,
                    start: middleFloor,
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
            }
        });
    });

    $(document).on('click', '.go-to-proj-props', function(e) {
        e.preventDefault();
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                $(document).find('.property-tab-content').html(data.getData().projectObjects);
                //$(document).find('.scrollable-table').mCustomScrollbar({ axis: "x" });
                $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
                $.fn.utilities('setScrollBarWidth', $(document).find('.panel_content.open').find('.scrollable-table'), window.innerWidth);
                var widthT = $.fn.utilities('measureHiddenTable', $(document).find('.panel_content.open').find('table.table'));

                $(document).find('.panel_content.open').find('table.table').closest('.mCSB_container').width(widthT);


                $('[data-toggle="table"]').bootstrapTable();

            }
        });
    });

    $(document).on('change', '.qc-status', function() {
        if ($(this).val() == 'invalid') {
            $(document).find('.property-quality-control-conditions .select-wrapper .q4-form-input').removeClass('disabled-input');
        } else {

            $(document).find('.property-quality-control-conditions .q4-form-input').addClass('disabled-input');
        }
    });


    /**
     * Смена иконки у tasks-ов
     */
    $(document).on('click', '.open-nested', function(e) {

        e.preventDefault();

        var self = $(this);
        var windowWidth = $(window).width();
        if (self.find('i').hasClass('q4bikon-arrow_right')) {

            self.find('i').removeClass('q4bikon-arrow_right');
            self.find('i').addClass('q4bikon-arrow_bottom');
            self.closest('.table').find('tr.nested-row > td').css('border-bottom', '1px solid #ddd');


        } else if (self.find('i').hasClass('q4bikon-arrow_bottom')) {

            self.find('i').removeClass('q4bikon-arrow_bottom');
            self.find('i').addClass('q4bikon-arrow_right');

            self.closest('.table').find('tr.nested-row > td').css('border-bottom', '1px solid transparent');

        }

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


    $(document).on('click', '.property-structure-actions .edit-element', function() {


        if ($(this).hasClass('enable-sorting')) {

            $(this).removeClass('enable-sorting');
            $(this).closest('.property-structure-list-group').css('background', '#ffffff').removeClass('enabled');
            $(this).siblings('.copy-element').removeAttr('data-toggle, data-target').removeClass('enable-copying');
            $(this).siblings('.delete_row').removeAttr('data-toggle, data-target').removeClass('enable-delete');

        } else {

            var editEl = $(document).find('.property-structure-actions .edit-element')

            editEl.removeClass('enable-sorting');
            editEl.closest('.property-structure-list-group').css('background', '#ffffff').removeClass('enabled');
            editEl.siblings('.copy-element').removeAttr('data-toggle, data-target').removeClass('enable-copying');
            editEl.siblings('.delete_row').removeAttr('data-toggle, data-target').removeClass('enable-delete');

            $(this).addClass('enable-sorting');
            $(this).closest('.property-structure-list-group').css('background', '#e0f1fd').addClass('enabled');
            $(this).siblings('.copy-element').addClass('enable-copying');
            $(this).siblings('.delete_row').addClass('enable-delete');

        }
    });




    $(window).on('resize', function() {

        var windowWidth = $(this).width(); //this = window

        if ($(document).find('.property-struct').length) {

            var self = $(document).find('.panel_header.open');
            var sidebarWidth = $(document).find('.sidebar').is(":visible") ? 295 : 90;
            if (windowWidth > 480) {
                var tabWidthOnLoad = windowWidth - sidebarWidth - 115;
            }

            var $panelContent = self.siblings('.panel_content');
            $panelContent.find('.property-structure-list').width(tabWidthOnLoad);

        }


        var windowWidthResize = $(window).width();

        if (windowWidthResize > 992) {

            $(document).find('.property-structure-actions .copy-element').tooltip('enable');
            $(document).find('.property-structure-actions .edit-element').tooltip('enable');
            $(document).find('.property-structure-actions .delete_row').tooltip('enable');

            $(document).find('.property-structure-actions .copy-element').tooltip({ title: "Copy Element", placement: "top" });
            $(document).find('.property-structure-actions .edit-element').tooltip({ title: "Edit Element", placement: "top" });
            $(document).find('.property-structure-actions .delete_row').tooltip({ title: "Delete Element", placement: "top" });
        } else {
            $(document).find('.property-structure-actions .copy-element').tooltip('disable');
            $(document).find('.property-structure-actions .edit-element').tooltip('disable');
            $(document).find('.property-structure-actions .delete_row').tooltip('disable');
        }
    });
    $(document).on('click', '.go-to-floor', function () {

        $(this).closest('.property-struct').find('.object-type-table-box').toggle();
        if($(this).closest('.property-struct').find('.object-type-table').is(':visible')){



        }
        return false;
    });


     $(document).on('click', '.select-go-to-floor', function () {
        var self = $(this)
       if(self.val()!=undefined && self.val().length){

            self.closest('.property-struct').find('.go-to-place-button-structure').removeClass('disabled-input')
       }else{
            if(self.closest('.property-struct').find('.go-to-place-number').val().length<1){
            self.closest('.property-struct').find('.go-to-place-button-structure').addClass('disabled-input')
        }

       }
    });

    $(document).on('keyup', '.go-to-place-number', function () {
        var self = $(this);
        if(self.val().length){
            self.closest('.property-struct').find('.go-to-place-button-structure').removeClass('disabled-input')
        }else{
        if(self.closest('.property-struct').find('.select-go-to-floor').val().length<1){

            self.closest('.property-struct').find('.go-to-place-button-structure').addClass('disabled-input')
        }

       }
    });


     $(document).on('click', '.go-to-place-button-structure', function () {

            var allFloors = $(document).find('.wrap-property-structure-list').data('floor');
            var firstFloor = $('.select-go-to-floor').find("option:first").val();
            var lastFloor = $('.select-go-to-floor').find("option:last").val();
            var selectedFloor = $(document).find('.select-go-to-floor').val();
            var selectedPlaceNumber = $(document).find('.go-to-place-number').val().toLowerCase();
            var floorNumber = false;
            var number = 0;
            if(selectedPlaceNumber.length>0){
                    var selectedItem = $(document).find('[data-placenumber="'+selectedPlaceNumber+'"]');
                    if(selectedItem.length){

                        number =  selectedItem.data('number');
                        floorNumber =  selectedItem.closest('.property-structure-list-group').find('.structure-floor-number').data('floornumber');

                       setTimeout(function() {

                            selectedItem.find('.create-quality-control').trigger('click')

                        }, 100)
                    }
                }

            $(this).closest('.property-struct').find('.object-type-table-box').hide();

            $('div.wrap-property-structure-list').trigger('endCarousel');

            number = number ? number : 0;
            var allFloors = $(document).find('.wrap-property-structure-list').data('floor');
            var middleFloor = parseInt(allFloors/2);

            middleFloor = selectedFloor ? selectedFloor : middleFloor
            middleFloor = floorNumber !== false ? floorNumber : middleFloor;
            var placeNumber = $(document).find('.wrap-property-structure-list').find('[data-floornumber="'+middleFloor+'"]').closest('li').find('.q4-owl-carousel').data('structurecount')
            placeNumber = parseInt(placeNumber/2)
            number = floorNumber !== false ? number : placeNumber
            $('.wrap-property-structure-list').jCarouselLite({
                btnNext: ".next",
                btnPrev: ".prev",
                vertical: true,
                visible: 1,
                circular:false,
                start: middleFloor,
            });
            $('.wrap-property-structure-list').css('overflow', 'hidden');


            $.fn.utilities('setCarouselDirection', ".q4-owl-carousel", 0,number);
            $.fn.utilities('owlPagination', '.q4-owl-carousel',number+1);


            if(selectedFloor == firstFloor){

                $('.wrap-property-structure-list')
                    .closest('.property-struct')
                    .find('.property-floors-arrow.prev')
                    .removeClass('disabled');
            }
            else if(selectedFloor == lastFloor){

                $('.wrap-property-structure-list')
                    .closest('.property-struct')
                    .find('.property-floors-arrow.next')
                    .removeClass('disabled');
            }
            else{

                $('.wrap-property-structure-list')
                    .closest('.property-struct')
                    .find('.property-floors-arrow')
                    .removeClass('disabled');
            }
            if(allFloors < 2){

                $('.wrap-property-structure-list')
                    .closest('.property-struct')
                    .find('.property-floors-arrow')
                    .addClass('disabled');
            }


    });


     $(document).on('click', '.go-to-place', function (e) {
        e.preventDefault()
        var self = $(this);
        self.closest('.property-struct').find('.object-type-table-box').toggle();






    });


     $(document).on('change', '.select-structure', function () {
        var self = $(this);
        if(self.val().length){
            var allFloors = $(document).find('.wrap-property-structure-list').data('floor');

            var firstFloor = self.find('option:selected').data("minfloor");
            var lastFloor = self.find('option:selected').data('maxfloor');
            var floorCount = lastFloor - firstFloor;
            var floors="";
            var j=0;
            var middleFloor = parseInt(floorCount/2);
            for (var i = firstFloor; i <= lastFloor; i++) {
                var selected = j==middleFloor ? ' selected="selected"':''
                floors += '<option '+ selected +' class="bidi-override" value="'+ parseInt(floorCount-j) +  '">'+i+'</option>';
                j++;
            }
            self.closest('.property-struct').find('.select-go-to-floor').empty()
            self.closest('.property-struct').find('.select-go-to-floor').append(floors)

            self.closest('.property-struct').find('.go-to-place-button').removeClass('disabled-input')
        }else{
            self.closest('.property-struct').find('.select-go-to-floor').empty()
            self.closest('.property-struct').find('.go-to-place-button').addClass('disabled-input')

        }

    });



    $(document).on('click','.go-to-place-button', function(){
        var url = $(this).data('url');
        var structureId = $(document).find('.select-structure').val()
        var selectedFloor = $(document).find('.select-go-to-floor').val();
        var selectedPlaceNumber = $(document).find('.go-to-place-number').val().toLowerCase();

        Q4U.ajaxGetRequest(url +'/'+ structureId, {
            successCallback: function(data) {

                $(document).find('.property-tab-content').html(data.getData().struct);
                normalizeObjectStruct();
                var floorNumber = false;
                var number = 0;
                if(selectedPlaceNumber.length>0){
                    var selectedItem = $(document).find('[data-placenumber="'+selectedPlaceNumber+'"]');
                    if(selectedItem.length){

                        number =  selectedItem.data('number');
                        floorNumber =  selectedItem.closest('.property-structure-list-group').find('.structure-floor-number').data('floornumber');

                       setTimeout(function() {

                            selectedItem.find('.create-quality-control').trigger('click')

                        }, 100)
                    }
                }

                number = number ? number : 0;
                var allFloors = $(document).find('.wrap-property-structure-list').data('floor');
                var middleFloor = parseInt(allFloors/2);
                middleFloor = selectedFloor ? selectedFloor : middleFloor
                middleFloor = floorNumber !== false ? floorNumber : middleFloor;
                var placeNumber = $(document).find('.wrap-property-structure-list').find('[data-floornumber="'+middleFloor+'"]').closest('li').find('.q4-owl-carousel').data('structurecount')

                placeNumber = parseInt(placeNumber/2)
                number = floorNumber !== false ? number : placeNumber
                $('.wrap-property-structure-list').jCarouselLite({
                    btnNext: ".next",
                    btnPrev: ".prev",
                    vertical: true,
                    visible: 1,
                    circular:false,
                    start: middleFloor,
                });
                $('.wrap-property-structure-list').css('overflow', 'hidden');
                if(allFloors < 2){

                    $('.wrap-property-structure-list')
                        .closest('.property-struct')
                        .find('.property-floors-arrow')
                        .addClass('disabled');
                }

                $.fn.utilities('setCarouselDirection', ".q4-owl-carousel", 0,number);
                $.fn.utilities('owlPagination', '.q4-owl-carousel',number+1);

            }
        });

    })


    $(document).on('click', '.apartment-box-top', function() {
        $(document).find('.apartment-box-clicked').hide()
        $(this).siblings('.apartment-box-clicked').show();

    });

    $(document).on('click', '.apartment-box-clicked-close', function() {

        $(this).closest('.apartment-box-clicked').hide();

    });



    $(document).on('click', '#change-main-image', function(e) {

        e.preventDefault();
        if ($(this).hasClass('clicked')) {

            $(this).removeClass('clicked');
        } else {
            $(this).addClass('clicked');
        }

    });

    $(document).on('click', '.display-project-image-scr', function(e) {

        e.preventDefault();
        if ($('#change-main-image').hasClass('clicked')) {

            if (!$(this).hasClass('main-image')) {
                $(this).addClass('main-image');
                $(this).closest('li').siblings('li').find('.display-project-image-scr').removeClass('main-image');
                $(document).find('#preview_image').attr('src', $(this).find('img').attr('src'));
                Q4U.ajaxGetRequest($(this).data('url'), {
                    successCallback: function(data) {
                        $(document).find('.modal').modal('hide');
                    }
                });
            }
        }

    });

    $(document).on('click', '.delete-proj-image', function(e) {
        e.preventDefault();
        Q4U.confirm('Are you sure you want delete that image?', {
            confirmCallback: function(el, params) {
                Q4U.ajaxGetRequest($(params.custom.el).data('url'), {
                    successCallback: function(data) {
                        $(params.custom.el).parents('li').remove();
                    }
                });
            },
            hilight: 'image',
            type: 'danger',
            confirmText: 'Delete',
            custom: {
                el: this
            }
        });
    });


    $(document).on('click', '.see-project-images', function(e) {
        e.preventDefault();
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData().modal) {
                    $(document).find('.modal').modal('hide');
                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $('.modal').modal('show');
                    // $('.modal .q4-vertical-scroll').mCustomScrollbar({ axis: "x" });
                }
            }
        });

    });




    /**
     * Datepicker Part
     */
    if($(document).find('html').hasClass('rtl'))
    {
        $('#project-start-date, #project-end-date').datetimepicker({locale:'he'}).show();
    }else{
        $('#project-start-date, #project-end-date').datetimepicker({locale:'en'}).show();
    }
    $('#project-start-date, #project-end-date').datetimepicker({
        useCurrent: false,
        minDate: moment()
    });
    $('#project-start-date').datetimepicker().on('dp.change', function(e) {
        var incrementDay = moment(new Date(e.date));
        $('#project-end-date').data('DateTimePicker').minDate(incrementDay);
        $(this).data("DateTimePicker").hide();
    });

    $('#project-end-date').datetimepicker().on('dp.change', function(e) {
        var decrementDay = moment(new Date(e.date));
        $('#project-start-date').data('DateTimePicker').maxDate(decrementDay);
        $(this).data("DateTimePicker").hide();
    });


    if($(document).find('html').hasClass('rtl'))
    {
        $('.project-property-start_date, .project-property-end_date, ' +
            '.date-tracking-start_date, .date-tracking-end_date').datetimepicker({locale:'he'}).show();
    }else{
        $('.project-property-start_date, .project-property-end_date, ' +
            '.date-tracking-start_date, .date-tracking-end_date').datetimepicker({locale:'en'}).show();
    }

    //Property td datepicker
    $('.project-property-start_date, .project-property-end_date, ' +
        '.date-tracking-start_date, .date-tracking-end_date').datetimepicker({
        useCurrent: false,
        minDate: moment()
    });
    $(document).find('[id^=property-start_date]').datetimepicker().on('dp.change', function(e) {
        var incrementDay = moment(new Date(e.date));

        $(this).closest('td').siblings('td').find('.project-property-end_date').data('DateTimePicker').minDate(incrementDay);
        $(this).data("DateTimePicker").hide();
    });

    $(document).find('[id^=property-end_date]').datetimepicker().on('dp.change', function(e) {
        var decrementDay = moment(new Date(e.date));

        $(this).closest('td').siblings('td').find('.project-property-start_date').data('DateTimePicker').maxDate(decrementDay);
        $(this).data("DateTimePicker").hide();
    });


    /**
     * End Of Datepicker
     */

    $(document).on('click', '.show-plan-history', function(e) {
        e.preventDefault();
        $('#plans-details-history-modal').remove();
        Q4U.ajaxGetRequest($(this).data('url'), {
            successCallback: function(data) {
                if (data.getData().modal) {

                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $('#plans-details-history-modal').modal('show');
                    // $('.modal .q4-vertical-scroll').mCustomScrollbar({ axis: "x" });
                }
            }
        });

    });
    $(document).on('click', '.modal .update-tracking-confirm', function(e) {
        var self = $(this);
        var searchText = $(document).find('.plans-tracking-layout .search-input').val();
        self.closest('.modal').modal('hide');
        setTimeout(function () {
            if(searchText.length > 0){
                $(document).find('.plans-tracking-layout .search-tracking').trigger('click');

            }else{
                $(document).find('.plans-tracking-layout .filter-tracking').trigger('click');
            }
        },700)


    });

    $(document).on('click', '.modal .delete-tracking-file', function(e) {
        e.preventDefault();
        var self = $(this);
        var currentModal = self.closest('.modal');
        Q4U.ajaxGetRequest(self.data('url'), {
            successCallback: function(data) {
                currentModal.find('.wrap-image-upload .show-uploaded-image').attr('src',"/media/img/camera.png");
                currentModal.find('.wrap-image-upload .upload-user-logo2').val('');
                $(document).find('.modal .tracking-details-buttons .print-element').addClass(currentPage.disabledGrayButton)
                $(document).find('.modal .tracking-details-buttons .delete-tracking-file').addClass(currentPage.disabledGrayButton)

            }
        });

    });




    $(document).on('click', '.set-image-link2', function(e) {

        e.stopPropagation();
        e.preventDefault();

        $(this).closest('.wrap-image-upload').find('.upload-user-logo2').trigger('click');

    });

    $(document).on('change','.wrap-image-upload .upload-user-logo2', function(e) {

        e.stopImmediatePropagation();
        setImageLink2(this, $(this));
        $(document).find('.modal .tracking-details-buttons .print-element').removeClass(currentPage.disabledGrayButton)
        $(document).find('.modal .tracking-details-buttons .delete-tracking-file').removeClass(currentPage.disabledGrayButton)

    });

    function setImageLink2(input, element) {

        /****************************************
         * style input type file
         ****************************************/

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            $(element).closest('.hide-upload').siblings(".camera-default-image").hide();
            $(element).closest('.hide-upload').siblings(".show-uploaded-image").removeClass('hidden');
            var file_ext = input.files[0].type.split('/')[1].toLowerCase();
            var result = '';


            reader.onload = function(e) {

                if(file_ext == 'pdf'){

                    result = '/media/img/pdf-icon.png';
                    $(element).closest(".upload-tracking-img").find(".show-uploaded-image").addClass('pdf-icon');
                } else {
                    result =  e.target.result;
                    $(element).closest(".upload-tracking-img").find(".show-uploaded-image").removeClass('pdf-icon');
                }

                $(element).closest(".upload-tracking-img").find(".show-uploaded-image").attr('src', result);
                $(element).closest(".upload-tracking-img").find(".print-dt-link").removeAttr('href');

            }

            reader.readAsDataURL(input.files[0]);

        }

        if ($(element).closest('.wrap-image-upload').hasClass('error')) {
            $(element).removeClass('error');
            $(element).closest('.wrap-image-upload').removeClass('error');
        }


    }


    $(document).on('click', '.print-element', function() {
        $(document).find(".print-data-tracking").remove()
        var printImageSrc = $(document).find('.upload-tracking-img .show-uploaded-image').attr('src');
        if(printImageSrc == undefined){
            printImageSrc = $(this).data('imagesource');

        }

        var printable = "<div class='print-data-tracking'>"+
                            "<style type='text/css'> @media print { @page {size: portrait; } } </style>"+
                            "<div class='print-data-tracking-content'>"+
                                "<img src='"+printImageSrc+"'>"+
                            "</div>"+
                        "</div>";


        $('body').append(printable);

        $(document).find('.print-data-tracking-content img').on('load',function(){
            $('.tracking-details-modal').hide();
            window.print();
            $('.tracking-details-modal').show();
            $(document).find('.print-data-tracking').remove();
        })



    });

    $(document).on('change','#plans-profession-id',function(){

        var self = $(this);
        var plansCraftsSelected = [];
        var craftsValue = $(this).val();
        var craftsText;
        var selfOption;

        if(self.val() == craftsValue){

            $('#plans-hidden-crafts option').prop("selected", false);
            $('#plans-hidden-crafts').closest('.multi-select-box').find('.checkbox-list-row').each(function(i, el){

                var dataProfession = $(this).data('profession');
                var dataVal = $(this).find('label').data('val');


                if(craftsValue == dataProfession){
                    selfOption = $('#plans-hidden-crafts option[value="'+dataVal+'"]');
                    selfOption.prop("selected", true);
                    craftsText = $(this).find('.checkbox-text').text();

                    if($(this).hasClass('hidden')){
                        $(this).removeClass('hidden');
                    }
                    $(this).find('.checkbox-wrapper-multiple').addClass('checked');

                    plansCraftsSelected.push(craftsText.trim());

                } else {

                    $(this).addClass('hidden');
                    $(this).find('.checkbox-wrapper-multiple').removeClass('checked');
                }

            });

        }

        var result = plansCraftsSelected.join(', ');

        $('#plans-hidden-crafts').closest('.multi-select-box').find('.select-imitation-title').html(result);

    });


    $(document).on('click','.toggle-radio-btn label',function(){

        $(this).children('span').addClass('input-checked');
        $(this).parent('.toggle-radio-btn').siblings('.toggle-radio-btn').children('label').children('span').removeClass('input-checked');

        if ( $(this).closest('.toggle-container').hasClass('disabled-radio-btn') ) {

            $($(this).closest('.toggle-container')).removeClass( 'disabled-radio-btn' );
        } else {
            $($(this).closest('.toggle-container')).addClass( 'disabled-radio-btn' );
        }

    });


    $(document).on('change','.toggle-container  input.user-radio',function() {

        $(this).closest('.toggle-container').toggleClass('disabled-radio-btn');

        Q4U.ajaxGetRequest($(this).closest('td').data('url'), {
            successCallback: function(data) {

                console.log("data ", data);

            }
        });

    });




});
