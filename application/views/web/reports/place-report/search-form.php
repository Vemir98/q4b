<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 04.04.2017
 * Time: 11:48
 */

?>
<!--reports list-->
    <div class="generate-reports">
        <form class="q4_form task-report-form">
            <div class="generate-reports-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12 rtl-float-right">
                        <div class="row">
                            <div class="report-form-group col-md-3 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('Company')?></label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select name="company" data-url="<?=URL::site('reports/place/get_projects')?>" class="q4-select q4-form-input rpt-company">
                                        <option value="select-company"><?=__("Select Company")?></option>
                                        <?foreach ($items as $i):?>
                                            <option value="<?=$i['id']?>"><?=$i['name']?></option>
                                        <?endforeach?>
                                    </select>
                                </div>
                            </div>
                            <div class="report-form-group col-md-3 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('Project name')?>
                                </label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select class="q4-select q4-form-input rpt-project" disabled data-url="<?=URL::site('reports/place/get_objects')?>" name="project">
                                    </select>
                                </div>

                            </div>
                            <div class="report-form-group col-md-3 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('Crafts')?>
                                    <span class="ms-custom-select-all"></span>
                                </label>
                                <select name="crafts[]" disabled multiple data-select-text="<?=__('Select from list')?>" data-select-all-text="<?=__('select all')?>" data-unselect-all-text="<?=__('unselect all')?>"></select>
                            </div>
                            <div class="report-form-group col-md-3 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('Structures')?>
                                </label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select class="q4-select q4-form-input rpt-project" disabled name="object">
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="report-form-group col-md-3 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('Status')?></label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select name="status" class="q4-select q4-form-input">
                                        <option value="all"><?=__("All")?></option>
                                        <?foreach (Enum_QualityControlStatus::toArray() as $status):?>
                                            <option value="<?=$status?>"><?=__($status)?></option>
                                        <?endforeach?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="generate-reports-footer">
                <div class="row">
                    <div class="col-md-12">
                        <a href="#" class="clear-all-reports clear-report-form"><?=__('Clear all')?></a>
                        <input type="button" id="generate-report" data-url="<?=URL::site('reports/place/show')?>" class="inline_block_btn blue-light-button disabled-input" value="<?=__('Generate')?>" >
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
        var objectsSelect = $('select[name="object"]');
        var craftsSelect= $('select[name="crafts[]"]');
        var companySelect = $('select[name="company"]');
        var placeInput = $('input[name="place"]');
        var FLOORS_DATA = null;
        var OBJECTS_DATA = null;
        companySelect.on('change',function(){
            var url = $(this).data('url') + '/' + this.value;
            projectsSelect.html('').attr('disabled','disabled');
            objectsSelect.html('').attr('disabled','disabled');
            craftsSelect.html('').attr('disabled','disabled').multiselect('reload');
            if(this.value > 0)
            $.get( url, function( data ) {
                var data = JSON.parse(data);
                FLOORS_DATA = data.floors;
                OBJECTS_DATA = data.objects;
                $.each(data.projects, function(key, project) {
                    var $option = $("<option/>", {
                        value: project.id,
                        text: project.name
                    });
                    if(!key)
                        $option.attr('selected','selected');
                    projectsSelect.append($option);
                });
                $.each(data.objects, function(key, object) {
                    var $option = $("<option/>", {
                        value: object.id,
                        text: object.name
                    });
                    objectsSelect.append($option);
                });
                $.each(data.crafts, function(key, craft) {
                    var $option = $("<option/>", {
                        value: craft.id,
                        text: craft.name
                    });
                    $option.attr('selected','selected');
                    craftsSelect.append($option);
                });
                craftsSelect.removeAttr('disabled').multiselect('reload');
                projectsSelect.removeAttr('disabled');
                objectsSelect.removeAttr('disabled');
                projectsSelect.change();
            });
        });

        projectsSelect.on('change',function(){
            var value = $(this).val();
            objectsSelect.html('');
            $.each(OBJECTS_DATA, function(key, object) {
                if(object.projectId == value) {
                    var $option = $("<option/>", {
                        value: object.id,
                        text: object.name
                    });
                    if(!key)
                    $option.attr('selected','selected');
                    objectsSelect.append($option);
                }
            });
            objectsSelect.removeAttr('disabled');
            objectsSelect.change();
            if(formIsValid()){
                $('#generate-report').removeClass('disabled-input');
            }else{
                $('#generate-report').addClass('disabled-input');
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
            var object = objectsSelect.val();
            var crafts = [];
            $.each(craftsSelect.val(), function (idx,id) {
                crafts.push(id);
            });
            var data = {
                'company': companySelect[0].value,
                'project': projectsSelect.val(),
                'object': object,
                'crafts': crafts,
                'status': $('select[name=status]').val(),
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

        $(document).on('click','a.get-report-details',function(e){
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
        });

        $(document).on('click','.stats-craft-ex',function(e){
            if($(e.target).hasClass('disable-craft')) return;
            var myClass = $(this).data('expandable');
            var el = $('.' + myClass);
            if(el.hasClass('hidden'))
                $('.' + myClass).removeClass('hidden');
            else
                $('.' + myClass).addClass('hidden')

        });
    });
</script>
