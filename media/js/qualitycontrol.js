;
"use strict";
(function($) {
$(document).on('change','input[name="project_id"]',function(){
    var projectId = parseInt($(this).val());
    $(document).find('.qc-object').val('').addClass('disabled-input');
    $(document).find('.qc-floor').val('').addClass('disabled-input');
    $(document).find('.qc-place').val('').removeClass('disabled-input');
    $(document).find('.qc-craft').val('').addClass('disabled-input');
    $(document).find('.qc-profession').val('').addClass('disabled-input');
    $(document).find('.qc-spaces').val('').addClass('disabled-input');
    $(document).find('.qc-place-name').val('').addClass('disabled-input');
    $(document).find('.qc-place-number').val('').addClass('disabled-input');
    $(document).find('.qc-tasks').html('');


    $(document).find('.property-quality-control-name').html('');
    $(document).find('.property-quality-control-name').addClass('hide');
    $(document).find('.property-quality-control-name .hide').addClass('hide');
    $(document).find('.qc-choose-plan').show();
    $(document).find('.qc-plans-list').html('');

    if( ! projectId) {
        $(document).find('.qc-object').addClass('disabled-input');
        return;
    }

    var url = $(this).data('url') + '/' + projectId;
    Q4U.ajaxGetRequest(url, {
        successCallback: function(data) {
            var places = JSON.parse(data.getData().places);
            $(document).find('.qc-object').html(data.getData().items).removeClass('disabled-input');

            if(places && places.length > 0){
                var pl = [];
                for(var i = 0; i < places.length; i++){
                    pl.push({value:places[i].name, data: places[i].id});
                }
                var placeInput = $(document).find('.qc-place');
                placeInput.autocomplete({
                    lookup: pl,
                    minChars: 0,
                    onSelect: function (suggestion) {
                        if(suggestion.data){
                            getPlaceData(placeInput.data('place-data-url') + '/' + suggestion.data);
                            placeInput.blur();
                            $(document).find('input[name="place_id"]').val(suggestion.data);
                        }

                    }
                });

            }else{
                $(document).find('.qc-object').addClass('disabled-input');
                $(document).find('.qc-floor').addClass('disabled-input');
                $(document).find('.qc-place').addClass('disabled-input');
            }
        }
    });
});
    $(document).on('change','.qc-object',function() {
        $(document).find('.qc-floor').removeClass('disabled-input');
        $(document).find('.qc-place').addClass('disabled-input');
    });
    $(document).on('change','.qc-floor',function(){

        $(document).find('.qc-place').val('').addClass('disabled-input');
        $(document).find('.qc-craft').val('').addClass('disabled-input');
        $(document).find('.qc-profession').val('').addClass('disabled-input');
        $(document).find('.qc-spaces').val('').addClass('disabled-input');
        $(document).find('.qc-tasks').html('');


        $(document).find('.property-quality-control-name').html('');
        $(document).find('.property-quality-control-name').addClass('hide');
        $(document).find('.property-quality-control-name .hide').addClass('hide');
        $(document).find('.qc-choose-plan').show();
        $(document).find('.qc-plans-list').html('');




        getPlacesForFloor($(document).find('.qc-object').val(), $(this).val());
        $(document).find('.qc-object').addClass('disabled-input');
    });

    //доработать
    $(document).on('focus','.qc-object',function(){
        if($(this).val().length > 0){
            $(this).val('');
        }
    });
    function getPlacesForFloor(objectId,floorNumber){
        var placeInput = $(document).find('.qc-place');
        var url = placeInput.data('url') + '/' + objectId + '/' + floorNumber;

        Q4U.ajaxGetRequest(url, {
            successCallback: function(data) {
                var items = JSON.parse(data.getData().items);
                if(items && items.length > 0){
                    var places = [];
                    for(var i = 0; i < items.length; i++){
                        places.push({value:items[i].name, data: items[i].id});
                    }


                    placeInput.removeClass('disabled-input');

                    placeInput.autocomplete({
                        lookup: places,
                        minChars: 0,
                        onSelect: function (suggestion) {
                            if(suggestion.data){
                                getPlaceData(placeInput.data('place-data-url') + '/' + suggestion.data);
                                placeInput.blur();
                                $(document).find('input[name="place_id"]').val(suggestion.data);
                            }

                        }
                    });

                }else{
                    $(document).find('.qc-place').addClass('disabled-input');
                }
            },
            ajaxErrorCallback: function(event, jqxhr, settings, thrownError) {
                $(document).find('.qc-place').addClass('disabled-input');
                Q4U.alert('Incorrect floor',{'type' : 'danger'});

            }
        });


        var placeInput = $(document).find('.qc-place');
        placeInput.val('');
        placeInput.removeClass('disabled-input');
    }

    function getPlaceData(url){
        var floorInput = $(document).find('.qc-floor');
        var placeNameInput = $(document).find('.qc-place-name');
        var placeIdInput = $(document).find('.qc-place-number');
        var craftsSelect = $(document).find('.qc-craft');
        var professionsSelect = $(document).find('.qc-profession');
        var tasksSelect = $(document).find('.qc-tasks');
        var spacesSelect = $(document).find('.qc-spaces');

        Q4U.ajaxGetRequest(url, {
            successCallback: function(data) {
                var item = JSON.parse(data.getData().item);
                floorInput.val(item['floor']);
                placeNameInput.val(item['customNumber']);
                placeIdInput.val(item['placeNumber']);
                craftsSelect.removeClass('disabled-input').html(item['crafts']);
                professionsSelect.removeClass('disabled-input').html(item['professions']);
                tasksSelect.html(item['tasks']);
                spacesSelect.removeClass('disabled-input').html(item['spaces']);

                var objectInput = $(document).find('.qc-object');
                if(objectInput.val().length <= 0){
                    objectInput.val(item['object']).addClass('disabled-input');
                    floorInput.addClass('disabled-input');
                }
            }
        });
    }

    $(document).on('change','.qc-craft',function(){
        if(parseInt($(this).val()) > 0){
            var url = $(this).data('url') + '/' + $(document).find('input[name="place_id"]').val() + '/' + $(this).val();
            var planListTableTbody = $(document).find('.qc-plans-list');
            planListTableTbody.html('');
            Q4U.ajaxGetRequest(url, {
                successCallback: function(data) {

                    var item = JSON.parse(data.getData().item);
                    planListTableTbody.html(item['planList']);
                }
            });
        }
    });

    $(document).on('click', '.confirm-plan1', function(e) {
        e.preventDefault();
        var html = $(document).find('input[name=plan]:checked').closest('td').find('.pln-data').html();
        console.log(html);
        $(document).find('.property-quality-control-name').html(html);
        $(document).find('.property-quality-control-name').removeClass('hide');
        $(document).find('.property-quality-control-name .hide').removeClass('hide');
        $(document).find('.qc-choose-plan').hide();
        $(document).find('#choose-plan-modal').modal('hide');
    });

    $( document ).ajaxSuccess(function( event, xhr, settings ) {
        var data = JSON.parse(xhr.responseText);
        if(data && data.triggerEvent == "qualityControlCreated"){
            window.location.reload();
        }
    });
})(jQuery);