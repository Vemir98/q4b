<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 04.04.2017
 * Time: 11:48
 */

?>
<script src="/media/js/project.js"></script>

<!--reports list-->
    <div class="generate-reports">
        <form class="q4_form task-report-form">
            <div class="generate-reports-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12 rtl-float-right">
                        <div class="row">
                            <div class="report-form-group col-md-4 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('Company')?></label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select name="company" data-url="<?=URL::site('reports/tasks/get_projects')?>" class="q4-select q4-form-input rpt-company">
                                        <option value="select-company"><?=__("Select Company")?></option>
                                        <?foreach ($items as $i):?>
                                            <option value="<?=$i['id']?>"><?=$i['name']?></option>
                                        <?endforeach?>
                                    </select>
                                </div>
                            </div>
                            <div class="report-form-group col-md-4 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('Project name')?>
                                </label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select class="q4-select q4-form-input rpt-project" disabled data-url="<?=URL::site('reports/tasks/get_objects')?>" name="project">
                                    </select>
                                </div>

                            </div>
                            <div class="report-form-group col-md-4 col-sm-12 rtl-float-right multi-select-col">
                                <label class="table_label">
                                    <span class="ms-custom-select-all"></span><?=__('Structures')?>
                                </label>
                                <select disabled name="objects[]" multiple data-select-text="<?=__('Select from list')?>" data-select-all-text="<?=__('select all')?>" data-unselect-all-text="<?=__('unselect all')?>">
                                </select>
                            </div>
                        </div>

                        <div class="row">

                            <div class="report-form-group col-md-4 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('Floors')?>
                                    <span class="ms-custom-select-all"></span>
                                </label>
                                <select name="floors[]" disabled multiple data-select-text="<?=__('Select from list')?>" data-select-all-text="<?=__('select all')?>" data-unselect-all-text="<?=__('unselect all')?>"></select>
                            </div>
                            <div class="report-form-group col-md-4 col-sm-12 rtl-float-right multi-select-col">
                                <label class="table_label">
                                    <span class="ms-custom-select-all"></span><?=__('Element')?>
                                </label>
                                <input type="text" class="table_input disabled-input" value="" name="place"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="generate-reports-footer">
                <div class="row">
                    <div class="col-md-12">
                        <a href="#" class="clear-all-reports clear-report-form"><?=__('Clear all')?></a>
                        <input type="button" id="generate-report" data-url="<?=URL::site('reports/tasks/show')?>" class="inline_block_btn blue-light-button disabled-input" value="<?=__('Generate')?>" >
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="generated-results"></div>
<style>
    .ms-options-wrap > button > span {
        display: inline-block;
        max-width: 470px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }
</style>
<script>

    $(document).ready(function() {

        //company select
        var projectsSelect = $('select[name="project"]');
        var objectsSelect = $('select[name="objects[]"]');
        var floorsSelect = $('select[name="floors[]"]');
        var companySelect = $('select[name="company"]');
        var placeInput = $('input[name="place"]');
        var FLOORS_DATA = null;
        var OBJECTS_DATA = null;
        var PLACES_DATA = null;
        var PLACE_ID = null;
        companySelect.on('change',function(){
            var url = $(this).data('url') + '/' + this.value;
            projectsSelect.html('').attr('disabled','disabled');
            objectsSelect.html('').attr('disabled','disabled').multiselect('reload');
            floorsSelect.html('').attr('disabled','disabled').multiselect('reload');
            placeInput.addClass('disabled-input').autocomplete({lookup: null});
            placeInput.val('');
            PLACE_ID = null;
            if(this.value > 0)
            $.get( url, function( data ) {
                var data = JSON.parse(data);
                FLOORS_DATA = data.floors;
                OBJECTS_DATA = data.objects;
                PLACES_DATA= data.places;
                $.each(data.projects, function(key, project) {
                    var $option = $("<option/>", {
                        value: project.id,
                        text: project.name
                    });
                    projectsSelect.append($option);
                });
                $.each(data.objects, function(key, object) {
                    var $option = $("<option/>", {
                        value: object.id,
                        text: object.name
                    });
                    objectsSelect.append($option);
                });
                $.each(data.floors, function(key, floor) {
                    var $option = $("<option/>", {
                        value: floor.id,
                        text: floor.name
                    });
                    floorsSelect.append($option);
                });
                projectsSelect.removeAttr('disabled');
                objectsSelect.multiselect('reload');
                floorsSelect.multiselect('reload');
                projectsSelect.change();
            });
        });

        projectsSelect.on('change',function(){
            var value = $(this).val();
            objectsSelect.html('');
            floorsSelect.html('');
            placeInput.val('');
            PLACE_ID = null;
            $.each(OBJECTS_DATA, function(key, object) {
                if(object.projectId == value) {
                    var $option = $("<option/>", {
                        value: object.id,
                        text: object.name
                    });
                    $option.attr('selected','selected');
                    objectsSelect.append($option);
                }
            });
            objectsSelect.removeAttr('disabled').multiselect('reload');
            objectsSelect.change();
            if(formIsValid()){
                $('#generate-report').removeClass('disabled-input');
            }else{
                $('#generate-report').addClass('disabled-input');
            }
        });

        objectsSelect.on('change',function(){
            floorsSelect.html('');
            placeInput.val('');
            PLACE_ID = null;
            var objects = [];
            $.each(objectsSelect.val(), function (idx,id) {
                objects.push(id);
            });
            var disabled = true;
            $.each(FLOORS_DATA, function(key, floor) {
                if(objects.indexOf(floor.objectId) != -1){
                    disabled = false;
                    var $option = $("<option/>", {
                        value: floor.id,
                        text: floor.name
                    });
                    $option.attr('selected','selected');
                    floorsSelect.append($option);
                }
            });
            if(disabled){
                floorsSelect.attr('disabled','disabled').multiselect('reload');
            }else{
                floorsSelect.removeAttr('disabled').multiselect('reload');
            }

            floorsSelect.change();
        });
//{value: '{$name}', data: '{$project->id}'}
        floorsSelect.on('change',function(){
            placeInput.val('');
            PLACE_ID = null;
            placeInput.removeAttr('disabled').removeClass('disabled-input');
            var floors = [];
            var objects = [];
            var places = [];
            $.each(floorsSelect.val(), function (idx,id) {
                floors.push(id);
            });

            $.each(objectsSelect.val(), function (idx,id) {
                objects.push(id);
            });

            $.each(PLACES_DATA, function(key, place) {
                if(floors.indexOf(place.floorId) != -1 || floors.length < 1){
                    places.push({'value': place.name + " (" + place.customNumber + ") " + (floors.length > 1 || floors.length == 0 ? place.floorName : "") + (objects.length > 1 || objects.length == 0 ? " - " + place.objectName : ""), 'data': place.id});
                }
            });
            placeInput.autocomplete({
                lookup: places,
                minChars: 0,
                onSelect: function (suggestion) {
                    if(suggestion.data) {
                        PLACE_ID = suggestion.data;
                        placeInput.blur();
                    }else{
                        PLACE_ID = suggestion.data;
                    }
                }
            });
        });
        placeInput.on('focus',function(){
            if($(this).val().length){
                $(this).val('').blur();
                PLACE_ID = null;
                var that = $(this);
                setTimeout(function () {
                    that.focus();
                },200);
            }
        });
        function formIsValid(){
            var cmpVal = companySelect.val();
            var projVal = projectsSelect.val();
            if(cmpVal == null){
                return false;
            }

            if(projVal == null){
                return false;
            }

            return true;
        }

        $('.clear-report-form').on('click',function (e) {
            if(e.preventDefault){
                e.preventDefault()
            }else{
                e.stopPropagation();
            }

            companySelect.prop("selectedIndex", 0).change();
            $('#generate-report').addClass('disabled-input');
        });

        $('#generate-report').on('click',function(){


            if( ! formIsValid() || $(this).hasClass('disabled-input')){
                return;
            }
            var url = $(this).data('url');
            var objects = []
            $.each(objectsSelect.val(), function (idx,id) {
                objects.push(id);
            });
            var floors = [];
            $.each(floorsSelect.val(), function (idx,id) {
                floors.push(id);
            });
            var data = {
                'company': companySelect[0].value,
                'project': projectsSelect.val(),
                'objects': objects,
                'floors': floors,
                'place': PLACE_ID,
                'csrf' : Q4U.getCsrfToken(),
                'x-form-secure-tkn': ""
            };

            $.ajax({
                url: url,
                data: JSON.stringify(data),
                method: 'POST',
                type: 'HTML',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    var data = JSON.parse(data);
                    $('#generated-results').html(data.report);
                    $('.generate-reports').slideUp();
                    $('#generated-results').slideDown("slow");
                    normalizeTables();
                }
            });

        });

        $(document).on('click','.generate-reports-bookmark-arrow',function(e){
            if(e.preventDefault){
                e.preventDefault()
            }else{
                e.stopPropagation();
            }
            $('.generate-reports').slideDown("fast",function () {
                $('#generated-results').html('');
            });

        });

        $(document).on('click','a.get-report-details, .get-report-places',function(e){
            e.preventDefault();
            var url = $(this).attr('href');
            $.get( url, function( data ) {
                var data = JSON.parse(data);
                $('.report-status-results').append(data.details).ready(function(){
                    $('.report-status-results > .f0').slideUp("slow",function(){
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    });
                });
            });
        });
        $(document).on('click','.report-details-back',function(){
            $('.report-status-results > .f0').slideDown("slow",function(){
                $(document).find('.task-details').remove();
            });
            $('#generated-content').find('table').each(function(){
               $(this).show();
            });
        });

        $(document).on('click', '.quality-control-list222', function(e) {
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
    });
</script>
