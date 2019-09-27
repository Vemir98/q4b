$(document).ready(function() {
    // $('select[multiple]').multiselect();
    $('select[multiple]').each(function(){
        $(this).multiselect({
            placeholder: $(this).data('select-text')&& $(this).data('select-text').length ? $(this).data('select-text') : "Select options",
            selectAll: true,
            selectAllText: $(this).data('select-all-text')&& $(this).data('select-all-text').length ? $(this).data('select-all-text') : "Select all",
            unselectAllText: $(this).data('unselect-all-text')&& $(this).data('unselect-all-text').length ? $(this).data('unselect-all-text') : "Unselect all",
            onLoad        : function( element ){
                $(element).siblings('.table_label').children('span').html($(element).siblings('.ms-options-wrap').children('.ms-options').children('.ms-selectall').text());
            },
            afterSelectAll   : function( element, selected){
                $(element).siblings('.table_label').children('span').html($(element).siblings('.ms-options-wrap').children('.ms-options').children('.ms-selectall').text());
            }
        })
    });
    $('.ms-custom-select-all').on('click',function(){
        $(this).parent().siblings('.ms-options-wrap').find('.ms-selectall').click()
    });

    //global
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
    })
    $(document).on('click', '.confirm-selected-users', function() {
        console.log(234243);
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
});