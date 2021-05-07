/**
 * Created by SUR0 on 21.05.2017.
 */
"use strict";
var _URL = '';
var CHANGED = false;

$(document).ready(function() {
    setTimeout(function() {
        $(document).scrollTop(0);
    }, 0);
    if (window.location.href.indexOf("generate") > -1) {
        $(document).find('.content').addClass('qc_report_content');
        if (navigator.appVersion.indexOf("Linux")!=-1) $(document).find(".qc-report-redesign").addClass('os_linux');
        var tabToSelect = location.hash ? window.location.hash.substring(1) : window.localStorage.getItem('qc_report_selected_tab');
        tabToSelect = !tabToSelect ? "tab_statistics" : tabToSelect;
        tabToSelect = tabToSelect ? tabToSelect : "tab_statistics";
        var moveTo = $(document).find('.content')
        var pagination = $(document).find('.q4-pagination');
        selectTab(tabToSelect, pagination, moveTo);
    }

    var reportsTasksListScroll = $(document).find('.reports-tasks-list-scroll').closest('.reports-tasks-box').width();
    $(document).find('.reports-tasks-list-group').each(function(i, elem) {
        var reportsTasksListScrollItem = 0;
        $(elem).find('li').each(function(key, el) {
            reportsTasksListScrollItem = reportsTasksListScrollItem + $(el).width() + 33;
        });
        $(elem).closest('.reports-tasks-list-scroll').width(reportsTasksListScroll);
        $(elem).width(reportsTasksListScrollItem);
    });
    if (window.location.href.indexOf("page") == -1) {
        setItemToLocalStorage('qc_report_selected_tab', 'tab_statistics');
    }

    $(document).on('click', '.qc_tab', function() {
        var newActiveTabName = $(this).data('tab');
        selectTab(newActiveTabName, pagination, moveTo);
    });
    $(document).on('click', '.tab_panel .panel_header_new', handlePanelClick);

    function selectTab(selectedTabName, moveEl, moveTo) {
        var selectedTab = $(document).find(`[data-tab='${selectedTabName}']`);
        var tabToSelect = $(document).find('#' + selectedTabName);
        var prevActiveTab = selectedTab.siblings('.active');
        setItemToLocalStorage('qc_report_selected_tab', selectedTabName);
        location.hash = selectedTabName;
        prevActiveTab.removeClass('active');
        $('.tab_content').hide();
        tabToSelect.show();
        console.log(tabToSelect);
        selectedTab.addClass('active');
        if (selectedTabName === 'tab_qc_controls') {
            moveDOMElement(moveEl, moveTo);
            pagination.show();
        } else {
            $(document).find('.q4-pagination').hide();
        }
        $(document).scrollTop(0);
    }

    function setItemToLocalStorage(key, value) {
        return window.localStorage.setItem(key, value);
    }

    function moveDOMElement(el, destination) {
        return destination.append(el);
    }

    function handlePanelClick() {
        var self = $(this);
        self.parent().siblings().find('.panel_content').slideUp();
        var $headerIcon = self.find('i.panel_header_icon_new');
        var $siblingsLi = self.closest('li').siblings('li');
        $siblingsLi.find('i.panel_header_icon_new').removeClass('q4bikon-arrow_top').addClass('q4bikon-arrow_bottom');
        $siblingsLi.find('.panel_header').removeClass('open');
        if (self.hasClass('open')) {
            self.removeClass('open');
            $headerIcon.removeClass('q4bikon-arrow_top').addClass('q4bikon-arrow_bottom');
        } else {
            self.addClass('open');
            $headerIcon.removeClass('q4bikon-arrow_bottom').addClass('q4bikon-arrow_top');
        }
        self.siblings('.panel_content').slideToggle(300);
    }

    function companySelected(val) {
        var projHtml = '';
        var projOptionsHtml = '';
        var craftHtml = '';
        var craftOptionsHtml = '';
        var selectedProject = selectedReportItems.project? selectedReportItems.project : [];
        var selectedCrafts = selectedReportItems.crafts ? selectedReportItems.crafts : [];
        var selectedCraftText = '';

        if(selectedProject.length){
            $(document).find('.show-advanced-reports').removeClass('hidden')
        }
        if (jsonReportItems[val]) {
            if (jsonReportItems[val].projects) {
                for (var idx in jsonReportItems[val].projects) {
                    var prj = jsonReportItems[val].projects[idx];
                    var selected = prj.id == selectedProject ? " selected" : ""
                    projOptionsHtml += '<option value="' + prj.id + '"' + selected + '>' + prj.name + '</option>';
                }
            }
            if (jsonReportItems[val].crafts) {
                if(!selectedCrafts.length){

                    var checked = "checked";

                }
                for (var idx1 in jsonReportItems[val].crafts) {
                    var crft = jsonReportItems[val].crafts[idx1];
                    if(selectedCrafts.length){

                       var checked = selectedCrafts.indexOf(crft.id) != -1 ? "checked" : "";
                    }

                    var selected = checked ? ' selected' : "";
                    selectedCraftText += checked ?  __(crft.name) + ',':'';
                    craftHtml +=
                        '<div class="checkbox-list-row">'+
                            '<span class="checkbox-text">' +
                                '<label class="checkbox-wrapper-multiple ' + checked +'" data-val="' + crft.id + '">' +
                                    '<span class="checkbox-replace"></span>' +
                                    '<i class="checkbox-list-tick q4bikon-tick"></i>'+
                                '</label>' + __(crft.name) +
                            '</span>'+
                        '</div>';
                    craftOptionsHtml += '<option value="' + crft.id + '" '+selected+'>' + __(crft.name) + '</option>';
                }
            }
        }

/***TO DO ****/
        $(document).find('.rpt-project').html(projOptionsHtml);
        if ($(document).find('.rpt-craft .checkbox-list .mCSB_container').length > 0) {
            $(document).find('.rpt-craft .checkbox-list .mCSB_container').html(craftHtml);

        } else {
            $(document).find('.rpt-craft .checkbox-list').html(craftHtml)

        }

        $(document).find('.check-all-crafts').html($(document).find('.check-all-crafts').data('unseltxt'));
        $(document).find('.rpt-project  .default-text').html($(document).find('.rpt-project  .default-text').data('text'));
        $(document).find('.rpt-craft  .default-text').html($(document).find('.rpt-craft  .default-text').data('text'));

        $(document).find('.rpt-craft select').html(craftOptionsHtml);
        $(document).find($(document).find('.rpt-craft .checkbox-wrapper-multiple')[0]).trigger('click').trigger('click');
        $(document).find($(document).find('.rpt-project option')[0]).trigger('click');

        selectedReportItems = [];


        $(document).find('.rpt-craft').find('.select-imitation').html(
            '<span class="select-imitation-title">'+
                selectedCraftText+
            '</span>'+
            '<div class="over-select" id="reports-filter-crafts"></div>'+
            '<i class="arrow-down q4bikon-arrow_bottom"></i>');


    }

    if (typeof jsonReportItems != 'undefined') {
        companySelected($(document).find('.rpt-company').val());

        $(document).on('change', '.rpt-company', function() {
            companySelected($(this).val());
            CHANGED = true;
        });



        $(document).on('change', '.rpt-company, .rpt-project', function() {
            $(document).find('#advanced-reports').hide().html('');
            CHANGED = true;

        });
        $(document).on('change', '.rpt-company', function() {
            if ($(this).val()) {
                $(document).find('.q4_form_submit').removeClass('disabled-input');
                $(document).find('.show-advanced-reports').removeClass('hidden')
            } else {

                $(document).find('.q4_form_submit').addClass('disabled-input');
                $(document).find('.show-advanced-reports').addClass('hidden')
            }

        });


        $(document).on('click', '.show-advanced-reports', function(e) {
            e.preventDefault();

            var custom_variable = "<input type='text' name='custom_variable' value='" + JSON.stringify(selectedAdvancedItems) + "'>" ;
            if(CHANGED){
                custom_variable = '';
            }
            var form = '<form class="hidden get-advanced-reports" method="post" data-ajax="true" action="' + $(this).data('url') + '">' +
                '<input type="hidden" value="" name="x-form-secure-tkn"/>' +
                '<input type="text" name="company" value="' + $('.rpt-company').val() + '">' +
                '<input type="text" name="project" value="' + $('.rpt-project').val() + '">' +
                    custom_variable +
                '</form>';
            $(document).find('.get-advanced-reports').remove();
            $('.rpt-adv-frm').append(form);
            $(document).find('.get-advanced-reports').submit();
        });

        $(document).on('showAdvancedReports', function(e, data) {
            var customInput = $(document).find('[name="custom_variable"]').val()
            if(customInput != undefined){

                var custom = JSON.parse(customInput);

            }

            $(document).find('.get-advanced-reports').remove();
            $(document).find('#advanced-reports').html('');
            $(document).find('#advanced-reports').append(data.advancedReportsHtml);
            $(document).find('#advanced-reports').show();
            if($(document).find('[name="place_number"]') &&  !$(document).find('[name="place_number"]').hasClass('disabled-input')){

                $(document).find('[name=place_number]').val(custom.place_number)
                $(document).find('[name=place_number]').trigger('change')
            }
        });

        $(document).on('click','.clear-all-reports',function(e){
            e.preventDefault();

            $(document).find('select.rpt-company [value="select-company"]').prop('selected','selected');
            $(document).find('select.rpt-company').change();
            $(document).find('.show-advanced-reports').addClass('hidden');
            $('#generated-results-link').addClass('disabled-input');

        })

        $(document).on('click', '.pr-object .checkbox-list-row', function() {
            var select = $(this).closest('.pr-object').find('select');

            setTimeout(function() {
                var selected = select.val();
                var floorsHtml = '';
                var floorsSelectOptions = '';
                var floorsArr = [];
                if (selected && selected.length) {
                    if($(document).find('[name=place_type]').val() != 'all')
                        $(document).find('[name=place_number]').removeClass('disabled-input').removeAttr('disabled');
                    for (var idx in selected) {
                        var from = select.find('option[value=' + selected[idx] + ']').data('floor-from');
                        var to = select.find('option[value=' + selected[idx] + ']').data('floor-to');
                        for (var i = from; i <= to; i++) {
                            if (floorsArr.indexOf(i) == -1) {
                                floorsArr.unshift(i);
                            }
                        }
                    }
                    floorsArr.sort(function(a, b) {
                        return a - b;
                    });
                        console.log("floorsArr", floorsArr);
                console.log(floorsArr)
                } else {
                    $(document).find('[name=place_number]').addClass('disabled-input').attr('disabled', 'disabled');
                }
                if (floorsArr.length) {

                    for (idx in floorsArr) {
                        floorsHtml +=   '<div class="checkbox-list-row">'+
                                '<span class="checkbox-text">' +
                                    '<label class="checkbox-wrapper-multiple inline" data-val="' + floorsArr[idx] + '">'+
                                        '<span class="checkbox-replace"></span>'+
                                        '<i class="checkbox-list-tick q4bikon-tick"></i>'+
                                    '</label>' + '<span class="bidi-override">' + floorsArr[idx] + '</span>' +
                                '</span>' +
                            '</div>';

                        floorsSelectOptions += '<option value="' + floorsArr[idx] + '">' + floorsArr[idx] + '</option>';
                    }
                }


                var parentSelector = $(document).find('.obj-floors');

                parentSelector.find('.select-imitation-title').html('<span class="select-def-text">' + parentSelector.find('.select-imitation-title').data('text') + '</span>');
                parentSelector.find('.checkbox-list').html(floorsHtml);

                parentSelector.find('select').html(floorsSelectOptions);


            }, 50);
        });

        $(document).on('keyup', '[name=place_number]', function() {
            var number = parseInt($(this).val());
            if (!isNaN(number)) {
                $('.pr-object,.obj-floors').addClass('disabled-input');
            } else {
                $('.pr-object,.obj-floors').removeClass('disabled-input');
            }
        });

        $(document).on('change', '[name=place_id]', function() {
            var number = parseInt($(this).val());
            if(number){
                $(document).find('[name=place_number]').addClass('disabled-input');
                var props = $(document).find('.pr-object select').val();
                if (props) {
                    props = number + '/' + props.join('-');
                } else {
                    props = number;
                }
                var el = $(document).find('[name=space]');
                el.html('');
                if (!isNaN(number)) {
                    Q4U.ajaxGetRequest($(this).data('url') + '/' + $(document).find('select[name=place_type]').val() + '/pn/' + props, {
                        successCallback: function(data) {
                            if (data.getData().options) {
                                el.html(data.getData().options);
                                el.removeClass('disabled-input').removeAttr('disabled');
                            }
                        }
                    });
                } else {
                    el.addClass('disabled-input').attr('disabled', 'disabled');
                }
            }

            else{
                $(document).find('[name=place_number]').removeClass('disabled-input');
            }
        });

        $(document).on('change', '[name=place_number]', function() {
            var number = $(this).val();
            if(number){
                $(document).find('[name=place_id]').addClass('disabled-input');
                var props = $(document).find('.pr-object select').val();
                if (props) {
                    props = number + '/' + props.join('-');
                } else {
                    props = number;
                }
                var el = $(document).find('[name=space]');
                el.html('');

                Q4U.ajaxGetRequest($(this).data('url') + '/' + $(document).find('select[name=place_type]').val() + '/pcn/' + props, {
                    successCallback: function(data) {

                        if (data.getData().options) {
                            el.html(data.getData().options);
                            el.removeClass('disabled-input').removeAttr('disabled');
                        }
                    }
                });
            }

            else{
                $(document).find('[name=place_id]').removeClass('disabled-input');
            }

        });

        $(document).on('change', 'select[name=place_type]', function() {

            if($(document).find('[name=place_type]').val() != 'all'){
                $(document).find('[name=place_number]').removeClass('disabled-input');
                $(document).find('[name=place_id]').removeClass('disabled-input');
            }else{
                $(document).find('[name=place_number]').addClass('disabled-input');
                $(document).find('[name=place_id]').addClass('disabled-input');
            }
            var number = parseInt($(document).find('[name=place_number]').val());
            if (!isNaN(number)) {
                $(document).find('[name=place_number]').trigger('change');
            }
        });

    }


    $(document).on('change', 'select[name=project_stage],.qc-craft', function() {
        $(document).find('.qc-profession option').each(function() {

            var self = $('#quality-control-modal');

            $(document).find('select[name=tasks] option').each(function() {
                var crafts = ($(this).data('crafts')).toString().split(',');
                var el = $('.qc-tasks-list a[data-id=' + $(this).val() + ']');

            });

            var modalWidth = $('#quality-control-modal').find('.modal-dialog').width();

            var tasksItemCount = $('.tasks-full-description li:visible').length;

            var tasksItemsWidth = tasksItemCount * (350 + 30)+20;
            // Add scroll to tasks
            $('.tasks-full-description-box').width(modalWidth - 40);
            $('.tasks-full-description').width(tasksItemsWidth);


        });
    });


    $(document).on('click', '.generate-reports-bookmark-arrow', function() {

        var self = $(this);
        location.hash = '';
        localStorage.removeItem('qc_report_selected_tab');
        $(document).find('.q4-pagination').hide()
        self.closest('#generated-content').toggle();
        self.closest('#generated-content').siblings('.generate-reports').slideDown();

        return false;

    });



    /***************************************
     *   Print Reports in browser не рабочий
     ***************************************/
    $(document).on('click', '.reports-to1-print-btn', function() {
        var printed = false;
        $(document).find('#qc-list-printable-new').remove();
        $('body>div.print-quality-control').remove();
        var url = window.location.href;
        $.ajax({
            url: url,
            method: 'POST',
            data: JSON.stringify({
                csrf: Q4U.getCsrfToken(),
                "x-form-secure-tkn": ''
            }),
            cache: false,
            contentType: false,
            processData: false,
            success: function(data){
                var printable = JSON.parse(data).html
                $('body').append(printable);
                $(document).find('.progress-bg').show();

                var ImgBefLoad = {};
                var imgCount =0;
                $(document).find('.print-reports-list img:not(.q4b-logo)').each(function(){
                    ImgBefLoad[$(this).attr('src')] = $(this).attr('src');
                    ++imgCount;

                });

                var count = 0;
                var imgArray = [];
                var width = 0;
                var addWidth = parseInt(100 *parseFloat(1/(imgCount)));
                $(document).find('.print-reports-list img:not(.q4b-logo)').on('load',function(){

                    count++;

                    var elem = $("#my-bar");
                    width =  count == imgCount ? 100 : width + addWidth;
                    elem.css('width', width + '%');
                    elem.closest('.progress-bar-modal').find('.progress-bar-text').html(width + '%');


                    if(count>=imgCount){

                         $(document).find('.progress-bg').hide();
                        if(!printed){
                            printed =true;
                            window.print();
                        }


                    }

                })
            },
        });



    });

    /**
     * Report in modal quality-control
     */


    $('#piechart svg text').attr('y', 80);

    function drawChart() {

        var data = google.visualization.arrayToDataTable(reportDataArray);
        var optionLeft = 25;
        var optionWidth = 400;
        var windowWidth = $(window).width();

        data.addColumn({'type': 'string', 'role': 'tooltip','p': {'html': true}});

        if(windowWidth <= 480 ){

            optionLeft = 80
            optionWidth = 180
        }
        else if(windowWidth < 640){
            optionLeft = 30
            optionWidth = 200
        }
        else if(windowWidth < 768){
            optionLeft = 80
            optionWidth = 200
        }
        else if(windowWidth < 992){
            optionLeft = 150
            optionWidth = 300
        }
        else if(windowWidth < 1200){
            optionLeft = 15
            optionWidth = 250
        }
        else if(windowWidth < 1248){
            optionLeft = 10
            optionWidth = 260
        }
        else if(windowWidth < 1300){
            optionLeft = 10
            optionWidth = 265
        }

        var options = {
            enableInteractivity: false,
            legend: { position: 'none', textStyle : {color: 'black'} },
            // colors: ['#28cf91', '#005C87', '#f99c19', '#ff0000'],
            colors: ['#28cf91', '#d515d3', '#005C87', '#f99c19', '#ff0000'],
            chartArea: {'top': 10, 'left' : 10, width: 165, height: 165},
            width: 200,
            height: 200,
            is3D: true,
            tooltip: {textStyle: {color: 'black'}},
            pieSliceText: 'none',
        };
        var chart = new google.visualization.PieChart($('#piechart')[0]);

        chart.draw(data, options);
    }

    if ($('#piechart')[0] != undefined) {

        /************* Pie Chart ***********/

        var reportDataArray = [
            ['Status', 'Quantity']
        ];
        $('#report-chart-list li').each(function() {

            var self = $(this);
            var name = self.data('name') ? self.data('name') : 'no name';
            var percent = self.data('percent') ? self.data('percent') : 0;
            reportDataArray.push([name, percent]);
        });

        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(drawChart);


        /************* end of Pie Chart ***********/
    }


    /************* SECOND PIE CHART ***********/
    $('#piechart2 svg text').attr('y', 80);

    function drawChart2() {

        var data = google.visualization.arrayToDataTable(reportDataArray2);
        var optionLeft = 25;
        var optionWidth = 400;
        var windowWidth = $(window).width();


        if(windowWidth <= 480 ){
            optionLeft = 80
            optionWidth = 180
        }
        else if(windowWidth < 640){
            optionLeft = 30
            optionWidth = 200
        }
        else if(windowWidth < 768){
            optionLeft = 80
            optionWidth = 200
        }
        else if(windowWidth < 992){
            optionLeft = 150
            optionWidth = 300
        }
        else if(windowWidth < 1200){
            optionLeft = 15
            optionWidth = 250
        }
        else if(windowWidth < 1248){
            optionLeft = 10
            optionWidth = 260
        }
        else if(windowWidth < 1300){
            optionLeft = 10
            optionWidth = 265
        }

        var options = {
            enableInteractivity: false,
            legend: { position: 'none', textStyle : {color: 'black'} },
            // colors: ['#28cf91', '#005C87', '#f99c19', '#ff0000'],
            colors: ['#28cf91', '#d515d3', '#005C87', '#f99c19', '#ff0000'],
            chartArea: {'top': 10, 'left' : 10, width: 165, height: 165},
            width: 200,
            height: 200,
            is3D: true,
            tooltip: {textStyle: {color: 'black'}},
            pieSliceText: 'none',
        };
        var chart = new google.visualization.PieChart($('#piechart2')[0]);

        chart.draw(data, options);
    }

    if ($('#piechart2')[0] != undefined) {

        /************* Pie Chart ***********/

        var reportDataArray2 = [
            ['Status', 'Quantity']
        ];
        $('#report-chart-list2 li').each(function() {

            var self = $(this);
            var name = self.data('name') ? self.data('name') : 'no name';
            var percent = self.data('percent') ? self.data('percent') : 0;
            reportDataArray2.push([name, percent]);
        });

        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(drawChart2);




        /************* end of Pie Chart ***********/
    }
    /************* end of SECOND PIE CHART ***********/


    /************* set plan height according to width ***********/
    var planWidth = $('.report-plan-item').find('.report-plan-item-image').outerWidth();
    var planHeight = (planWidth * 75) / 100;

    $('.report-plan-item').find('.report-plan-item-image').outerHeight(planHeight);
    $('.report-plan-item').find('.report-plan-item-image').css('line-height', planHeight + 'px');

    $(document).on('click', '.reports-prop-title a', function(e) {
        e.preventDefault();
        Q4U.ajaxGetRequest($(this).data('url'), {
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

                    setTimeout(function() {

                        var self = $(document).find('#quality-control-modal');
                        var modalWidth = self.find('.modal-dialog').width();

                        var tasksItemCount = $('.tasks-full-description li:visible').length;

                        var tasksItemsWidth = tasksItemCount * (350 + 50)+20;

                        // Add scroll to tasks
                        $('.tasks-full-description-box').width(modalWidth - 40);
                        $('.tasks-full-description').width(tasksItemsWidth);

                        $('.date').datetimepicker({
                            locale: $(document).find('html').attr('lang')
                        }).show();


                        // TO DO
                        if ($(document).find('html').hasClass('rtl')) {

                            $(document).find('.q4-owl-carousel').each(function (i, el) {

                                var self  =  $(this);
                                //owlPagination(self,true);

                            });
                        } else {

                            $(document).find('.q4-owl-carousel').each(function (i, el) {

                                var self  =  $(this);
                                //owlPagination(self);

                            });
                        }


                    }, 400)
                }
            }
        });
    });


    $(document).on('click', '.confirm-selected-users', function() {

        var userMail = '';
        var userMailList = [];

        if(!$(this).hasClass('mobile')){
            $(this).closest('.modal').find('.users-list-table tbody').find('tr').each(function(i, el) {
                var self = $(el);

                if (self.find('input[type=checkbox]').is(':checked')) {

                    userMail = self.find('.user-email-cell').find('input').val();
                    userMailList.push(userMail);

                }

            });
        } else {

            $(this).closest('.modal').find('.q4-circle-checkbox input[type=checkbox]').each(function (i, el) {

                var self = $(el);

                if ($(this).is(':checked')) {
                    var userMail = self.closest('.item').find('.s-user-email').text();
                    userMailList.push(userMail);

                }

            });
        }


        $('#choose-sender-modal').find('.show-existing-users').html('');

        var textToInsert = '';
        $.each(userMailList, function(count, item) {
            textToInsert += '<span class="send-email-block">' +
                    '<input type=hidden name=emails_'+count+' value="' + item + '">' +
                    '<span class="send-email-block-txt">' + item + '</span>' +
                    '<i class="q4bikon-close close-email-block"></i>' +
                '</span>';
        });

        $('#choose-sender-modal').find('.show-existing-users').html(textToInsert);


    });

    $('label.pretty-checkbox input[type=checkbox]').on('change',function(){
        if ( ! $(this).is(':checked')){
            $(this).parent().removeClass('checked');
        }else{
            $(this).parent().addClass('checked');
        }
    });

    $('label.pretty-checkbox input[type=checkbox]').each(function(){
        if ( ! $(this).is(':checked')){
            $(this).parent().removeClass('checked');
        }else{
            $(this).parent().addClass('checked');
        }
    });

    function toggleCondLevel() {
        var statuses = $('[name="statuses[]"]').val();
        if(statuses && (statuses.indexOf('invalid') > -1 || statuses.indexOf('repaired') > -1)){
            $('.cond-level').show();
        }else{
            $('.cond-level').hide();
        }
    }

    toggleCondLevel();

    $('.statuses-chbx .checkbox-list-row').on('click',function(){
        setTimeout(function(){
            toggleCondLevel();
        },250)
    });

});
