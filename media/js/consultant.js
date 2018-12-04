"use strict";
var Q4U = window.Q4U || {};
Q4U.pages= {
    updatePage: {
        addUserSelector: '.add-user',
        checkUserSelector: '.check-email',
        showDetailsSelector:'.show-user-details',
        listUpdateEvent:'usersListUpdated',

    }
};
$( document ).ready(function() {
//gfghfg
    var currentPage = Q4U.pages.updatePage;

    $(document).on('click', '.q4-modal-dismiss', function () {

        $(this).closest('.modal').modal('toggle');

        $('#consultants-user-detail').modal('show');
    });

    $(document).on('click', currentPage.showDetailsSelector, function (e) {

        e.preventDefault();
        var url = $(this).data('url');

        $(document).find('#consultants-email-modal').remove()
        Q4U.ajaxGetRequest(url, {
            successCallback: function(data) {
                if (data.getData().modal) {
                    var modal = data.getData().modal;
                    $('body').append(modal);
                     $(document).find('#consultants-user-detail').modal('show');

                    $(document).find('.checktree-root .q4-nested-list').each(function (i, el) {

                        var self = $(el);
                        var countChecked = self.find('li .node-item-checkbox:checked').length;
                        self.closest('.root-item').find('.tree-node-checked').text(countChecked);
                        var countAll = self.closest('.root-item').find('.tree-node-all').text();

                        if(countChecked == countAll){

                            self.closest('li.root-item').find('.root-item-checkbox-wrapper .root-item-checkbox').prop('checked', true);
                        }
                    });


           //          setTimeout(function () {
			        //     $('.consultants-ud-scroll-box').mCustomScrollbar({ axis: "y"});
			        // }, 200);


                }
            }
        });
    });


    $('body').on(currentPage.listUpdateEvent, function(e, data) {
        $(document).find('.modal').modal('hide');
        $(document).find('.consultants-layout').replaceWith(data.usersList)
        $(document).find('[data-toggle="table"]').bootstrapTable();
        $.fn.utilities('setCarouselWidth', '.q4-carousel-table-wrap', window.innerWidth);
        setTimeout(function () {
            $.fn.utilities('updateContentOnChange');
        }, 200);


    });

    $(document).on('click', currentPage.addUserSelector, function (e) {

        e.preventDefault();
        var url = $(this).data('url');

        $(document).find('#consultants-email-modal').modal('hide');
        Q4U.ajaxGetRequest(url, {
            successCallback: function(data) {
                if (data.getData().modal) {

                    var modal = data.getData().modal;
                    $('body').append(modal);
                    $(document).find('#consultants-email-modal').modal('show');


                }
            }
        });
    });

    $(document).on('click', currentPage.checkUserSelector, function (e) {

        e.preventDefault();
        var url = $(this).data('url');
        $(document).find('#consultants-email-modal').modal('hide');
        var email = $(this).closest('.modal').find('input[name=email]').val();
        $.ajax({
            url: url,
            method: 'POST',
            data: JSON.stringify({
                csrf: Q4U.getCsrfToken(),
                "x-form-secure-tkn": '',
                email:email,
            }),
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                var responseData = JSON.parse(response);
                if(responseData.url != undefined){
                    Q4U.ajaxGetRequest(responseData.url, {
    		            successCallback: function(data) {
    		                if (data.getData().modal) {
    		                    var modal = data.getData().modal;
    		                    $('body').append(modal);
                                $(document).find('#consultants-email-modal').modal('hide');
    		                    $(document).find('#consultants-user-detail').modal('show');
                                $(document).find('#consultants-user-detail').find('input[name="email"]').val(email)
    		                    setTimeout(function () {
    					            $('.consultants-ud-scroll-box').mCustomScrollbar({ axis: "y"});
    					        }, 200);
                                $(document).find('.checktree-root .q4-nested-list').each(function (i, el) {

                                    var self = $(el);
                                    var countChecked = self.find('li .node-item-checkbox:checked').length;
                                    self.closest('.root-item').find('.tree-node-checked').text(countChecked);
                                    var countAll = self.closest('.root-item').find('.tree-node-all').text();

                                    if(countChecked == countAll){

                                        self.closest('li.root-item').find('.root-item-checkbox-wrapper .root-item-checkbox').prop('checked', true);
                                    }
                                });

                                $(document).on('click', '.create-consultant', function (e) {
                                    e.preventDefault()
                                    e.stopImmediatePropagation();
                                    // console.log($(this).closest('.modal').attr('id'))
                                    if($(document).find('#consultants-user-detail').find('input[name="email"]').val()==email){
                                        $(this).closest('form').submit()
                                    }
                                });
    		                }
    		            }
    		        });
                }else if(responseData.errors != undefined){

                    Q4U.alert(__(responseData.errors), {
                        type: "danger",
                        confirmText: __("OK")
                    });

                }

            },
        });

    });



    // Checkbox Tree
    $(document).on('click', '.tree-node', function () {

        var self = $(this);
        self.closest('li.root-item').find('ul').toggle();
        if(self.find('i').hasClass('q4bikon-arrow_right')){
            self.find('i').removeClass('q4bikon-arrow_right').addClass('q4bikon-arrow_bottom');
        } else {
            self.find('i').removeClass('q4bikon-arrow_bottom').addClass('q4bikon-arrow_right');
        }
    });



    $(document).on('click', '.checktree-root .root-item-checkbox-wrapper .checkbox-wrapper', function (e) {

        var self = $(this);

        var checkbox = self.find('.root-item-checkbox');
        var checkboxStatus = self.find('.root-item-checkbox').prop('checked');
        self.closest('li.root-item').find('li .node-item-checkbox').addClass('testing')
        var countChecked = self.closest('li.root-item').find('li .node-item-checkbox').length;

        self.closest('li.root-item').find('.node-item-checkbox-wrapper .node-item-checkbox').each(function(){

            $(this).prop('checked', checkboxStatus);

        });


        if(checkbox.is(':checked')){
            self.closest('li.root-item').find('.tree-node-checked').text(countChecked);

        } else {
            self.closest('li.root-item').find('.tree-node-checked').text(0);
        }

    });

    $(document).on('click', '.checktree-root .node-item-checkbox-wrapper .checkbox-wrapper', function (e) {

        var self = $(this);
        var countChecked = self.closest('ul').find('li .node-item-checkbox:checked').length;
        var countAll = self.closest('li.root-item').find('.tree-node-all').text();
        self.closest('li.root-item').find('.tree-node-checked').text(countChecked);

        if(countChecked == countAll){

            self.closest('li.root-item').find('.root-item-checkbox-wrapper .root-item-checkbox').prop('checked', true);
        } else {

            self.closest('li.root-item').find('.root-item-checkbox-wrapper .root-item-checkbox').prop('checked', false);
        }
    });

    $(document).on('click', '.select-lists', function () {

        var self = $(this);
        var consultantsWrapper = self.closest('.consultants-ud-scroll-wrap');
        var selectStatus = consultantsWrapper.find('.select-lists').data('type');

        if(selectStatus == 'select-all'){

            consultantsWrapper.find('.select-lists').data('type', 'unselect-all').text(__('unselect all'));
            consultantsWrapper.find('input[type=checkbox]').prop('checked', true);
            consultantsWrapper.find('.tree-node-status').each(function () {

                var countAll = $(this).find('.tree-node-all').text();
                $(this).find('.tree-node-checked').text(countAll);

            });

        } else if(selectStatus == 'unselect-all'){

            consultantsWrapper.find('.select-lists').data('type', 'select-all').text(__('select all'));
            consultantsWrapper.find('input[type=checkbox]').prop('checked', false);
            consultantsWrapper.find('.tree-node-checked').each(function () {

                $(this).text(0);
            });

        }

    });




});


