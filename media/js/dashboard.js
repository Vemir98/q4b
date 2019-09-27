/**
 * Created by SUR0 on 01.06.2017.
 */
"use strict";


$(document).ready(function() {
    $('body').on('dashboardUpdated', function(e, data) {
        if (data && data.html) {
            $('#dashboard-content').replaceWith(data.html);
            $(document).find('html').removeAttr('style');

            var self = $(document).find('.panel_header.open');
            var windowWidth = $(window).width();

            $.fn.utilities('setScrollBarWidth', self.closest('.tab_panel').find('.scrollable-table'), windowWidth);
            var widthT = $.fn.utilities('measureHiddenTable', self.closest('.tab_panel').find('table.table'));


            if($(document).find('html').hasClass('rtl')){

                $(document).find('body').removeAttr('style');
            }

            $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
            $.fn.utilities('updateContentOnChangeNew');

            $(document).find('[data-toggle="table"]').bootstrapTable();

        }
    });


    $(document).on('change', '.concrete-std-form input[type=file]', function() {
        $(this).closest('form').submit();
    });


    $(document).on('click','.content-dashboard .table td a', function(){

        $(this).addClass('viewed');
    });

    $(document).on('dashboardPlansUpdated', function(e, data) {
        $('#update-plan-modal').modal('hide');
        $(document).find('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        $(document).find('[data-toggle="table"]').bootstrapTable();

    })

    $(document).on('change', '.certification-tab select[name=status]', function(e) {
        var msg = __('Are you sure, you want') + ' ' + __('change status');
        var higlight = '';
        var type = 'normal';
        var confirmText = __('Confirm');
        var self = $(this);
        Q4U.confirm(msg, {
            confirmCallback: function(el, params) {
                $.ajax({
                    url: self.data('url'),
                    method: 'POST',
                    data: JSON.stringify({
                        csrf: Q4U.getCsrfToken(),
                        "x-form-secure-tkn": ''
                    }),
                    cache: false,
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        self.removeClass('q4-status-waiting');
                        self.addClass('q4-status-approved');
                        self.addClass('disabled-input');
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
    })


    $(document).on('click', '.reports-prop-title a', function(e) {
        e.preventDefault();
        var modalId = $(this).data('modalid');
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

                    $('.date').datetimepicker({
                            locale: $(document).find('html').attr('lang')
                        }).show();

                    setTimeout(function() {
                        var self = $(document).find('#' + modalId);
                        var modalWidth = self.find('.modal-dialog').width();

                        var tasksItemCount = $('.tasks-full-description li:visible').length;
                        var tasksItemsWidth = tasksItemCount * (350 + 40);

                        // Add scroll to tasks
                        self.find('.tasks-full-description-box').width(modalWidth - 40);
                        self.find('.tasks-full-description').width(tasksItemsWidth);


                    }, 300)

                }
            }
        });
    });


    /**
     * Openning and closing current tab
     */

    $(document).on('click', '.filter-settings-button', function(e) {
        e.preventDefault()
        var self = $(this);
        var status = self.data('status');
        var html = self.html();
        var url = self.data('url');
        var selectedTab = self.closest('li.tab_panel').data('tab');

        Q4U.ajaxGetRequest(self.data('url'), {
            successCallback: function(data) {
                if (data.getData()) {

                    $(document).find('li.' + selectedTab).empty();
                    $(document).find('li.' + selectedTab).append(data.getData().html);
                    $(document).find('.panel_content').removeClass('open')
                    $(document).find('li.' + selectedTab + ' .panel_header span i').removeClass('q4bikon-plus').addClass('q4bikon-minus');
                    $(document).find('li.' + selectedTab + ' .panel_header').addClass('open');
                    $(document).find('li.' + selectedTab + ' .panel_header').siblings('.panel_content').addClass('open');
                    $(document).find('li.' + selectedTab + " .filter-settings-button").removeClass('active');
                    $(document).find('li.' + selectedTab + " .filter-settings-button[data-status='" + status + "']").addClass('active');
                    $(document).find('li.' + selectedTab + ' .q4-page-export').attr('href',url + '?export_qc_list=1');

                    $(document).find('.q4-inside-filter-mobile a.q4-inside-select-filter').html(html);

                    var self = $(document).find('.panel_header.open');
                    var windowWidth = $(window).width();


                    $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
                    $.fn.utilities('setScrollBarWidth', self.closest('.tab_panel').find('.scrollable-table'), windowWidth);
                    var widthT = $.fn.utilities('measureHiddenTable', self.closest('.tab_panel').find('table.table'));

                    $(document).find('.mCSB_container').width(widthT);

                    $.fn.utilities('setCarouselDirection', ".q4-carousel-table", 10);
                    $.fn.utilities('owlPagination', '.q4-carousel-table');

                    $(document).find('[data-toggle="table"]').bootstrapTable();
                }
            }
        });
    });

    /**
     * Page ajax ---------------------------------
     */

    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var self = $(this);
        var status = self.closest('li.tab_panel').find('.inside-filters-list li a.active').data('status');

        var selectedTab = self.closest('li.tab_panel').data('tab');
        $(this).closest('li').hasClass('active');
        if($(this).attr('href')!='#'){

            Q4U.ajaxGetRequest(self.attr('href'), {
                successCallback: function(data) {
                    if (data.getData()) {

                        $(document).find('li.' + selectedTab).empty()
                        $(document).find('li.' + selectedTab).append(data.getData().html);

                        // TO DO refactor
                        $(document).find('.panel_content').removeClass('open');
                        $(document).find('li.' + selectedTab + ' .panel_header span i').removeClass('q4bikon-plus').addClass('q4bikon-minus');
                        $(document).find('li.' + selectedTab + ' .panel_header').siblings('.panel_content').addClass('open');
                        $(document).find('li.' + selectedTab + " .filter-settings-button").removeClass('active');
                        $(document).find('li.' + selectedTab + " .filter-settings-button[data-status='" + status + "']").addClass('active');


                        $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
                        var self = $(document).find('.panel_header.open');
                        var windowWidth = $(window).width();
                        $.fn.utilities('setScrollBarWidth', self.closest('.tab_panel').find('.scrollable-table'), windowWidth);
                        var widthT = $.fn.utilities('measureHiddenTable', self.closest('.tab_panel').find('table.table'));

                        $.fn.utilities('setCarouselDirection', ".q4-carousel-table", 10);

                        if($(document).find('html').hasClass('rtl')){

                            $(document).find('body').removeAttr('style');
                        }

                        $.fn.utilities('owlPagination', '.q4-carousel-table');
                        $(document).find('[data-toggle="table"]').bootstrapTable();


                    }
                }
            });
        }

    });
    //------------------------------------------------------------------------

    /**
     * Filtering the company projects and objects
     */

    $(document).on('change', '.rpt-company', function(e) {
        var companyId = $(this).val();
        var selectedProject = "";
        var index = 0;
        var selectedOpt = '';

        $(document).find('.filtered-projects').find('option').remove();
        $(document).find('.rpt-project1').find('option').each(function(i, el) {
            var self = $(el);
            if (self.data('companyid') == companyId.toString()) {
                if (index == 0) {
                    self.attr("selected", "selected");
                    selectedOpt = self.val();
                    index++;
                    selectedProject = self.val();
                    $(document).find('.filtered-projects').removeClass("disabled-input");
                    $(document).find('.select-imitation').removeClass("disabled-input");
                }

                var clone = self.clone()
                $(document).find('.filtered-projects').append(clone);


            }
        })
        selectProject(selectedProject);

        $(document).find('.filtered-projects').val(selectedOpt);

    });
    $(document).on('change', '.filtered-projects', function(e) {
        var self = $(this);
        var projectId = self.val();
        selectProject(projectId);

    });

    function selectProject(projectId) {
        $(document).find('.multi-select-box .select-imitation .select-imitation-title').text('')
        $(document).find('.rpt-object').find('option').attr("selected", false);
        $(document).find('.checkbox-list').find('.checkbox-list-row').addClass('hidden')
        $(document).find('.checkbox-list').find('label').each(function(i, el) {
            var self = $(el);
            if (self.data('projectid') == projectId.toString()) {
                if (self.closest('.checkbox-list-row').find('.checkbox-text').text() != "") {
                    self.click();
                    self.closest('.checkbox-list-row').removeClass('hidden');
                }
            }
        })

        var selectedText = $(document).find('.multi-select-box .select-imitation .select-imitation-title').text()
        if (selectedText.length > 3) {
            $(document).find('#filter-dashboard-submit').removeClass('disabled-gray-button');
        } else {
            $(document).find('#filter-dashboard-submit').addClass('disabled-gray-button');
        }
    }
    /**
     * Select ticker ---------------------------------------------------------------------
     * Desktop
     */


    //----------------------------------------------------
    // TO DO in utilities
    $(document).on('click', '.qc-choose-plan a,.qc-change-plan a, .choose-plan', function(e) {
        e.preventDefault();

        var timer = setInterval(function() {
            if (!$(document).find('.modal').length)
                clearInterval(timer)
            $(document).find('.modal').css('overflow-y', 'auto');
        }, 1000);
    });


    $(document).on('click', '.confirm-selected-users', function() {

        var userMail = '';
        var userMailList = [];
        $(this).closest('.modal').find('.users-list-table tbody').find('tr').each(function(i, el) {
            var self = $(el);

            if (self.find('input[type=checkbox]').is(':checked')) {

                userMail = self.find('.user-email-cell').find('input').val();
                userMailList.push(userMail);

            }

        });


        $('#choose-sender-modal').find('.show-existing-users').html('');

        var textToInsert = '';
        $.each(userMailList, function(count, item) {
            textToInsert += '<span class="send-email-block">' +
                '<input type=hidden name=emails_'+count+' value="' + item + '">' +
                '<span class="send-email-block-txt">' + item + '</span><i class="q4bikon-close close-email-block"></i>' +
                '</span>';
        });

        $('#choose-sender-modal').find('.show-existing-users').html(textToInsert);


    });

    /**
     * Select Unselect property
     */
    $(document).on('click', '.check-all-properties', function() {
        if ($(this).html() == $(document).find('.check-all-properties').data('unseltxt')) {
            $(document).find('.multi-select-box .checkbox-list .checkbox-wrapper-multiple.checked').each(function(i, el) {
                if (!$(this).closest('.checkbox-list-row ').hasClass('hidden'))
                    $(this).click();
            });
            $(document).find('.check-all-properties').html($(document).find('.check-all-properties').data('seltxt'));
        } else {
            $(document).find('.multi-select-box .checkbox-list .checkbox-wrapper-multiple').each(function() {
                if (!$(this).hasClass('checked') && !$(this).closest('.checkbox-list-row').hasClass('hidden')){
                    $(this).click();
                }
            });
            $(document).find('.check-all-properties').html($(document).find('.check-all-properties').data('unseltxt'));
        }

    });
    

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
});
