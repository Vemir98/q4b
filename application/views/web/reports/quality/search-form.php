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
    <div class="generate-reports"
        <?if($hidden):?>
        style="display: none;"
        <?endif?>
    >
        <?=$savedReports?>
        <form class="q4_form">
            <div class="generate-reports-body">
                <div class="row">
                    <div class="col-md-6 col-sm-12 rtl-float-right">
                        <div class="row">
                            <div class="report-form-group col-md-6 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('Company')?></label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select name="company" data-url="<?=URL::site('reports/quality/get_projects')?>" class="q4-select q4-form-input rpt-company">
                                        <option value="select-company"><?=__("Select Company")?></option>
                                        <?foreach ($items as $i):?>
                                            <option value="<?=$i['id']?>"><?=$i['name']?></option>
                                        <?endforeach?>
                                    </select>
                                </div>
                            </div>
                            <div class="report-form-group col-md-6 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('Project name')?>
                                    <span class="ms-custom-select-all"></span>
                                </label>
                                <select class="q4-select q4-form-input rpt-project" disabled data-url="<?=URL::site('reports/quality/get_objects')?>" multiple data-select-text="<?=__('Select from list')?>" data-select-all-text="<?=__('select all')?>" data-unselect-all-text="<?=__('unselect all')?>" name="projects[]">
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="report-form-group col-md-6 col-sm-12 rtl-float-right multi-select-col">
                                <label class="table_label">
                                    <span class="ms-custom-select-all"></span><?=__('Crafts')?>
                                </label>
                                <select class="hidden-select" disabled name="crafts[]" multiple data-select-text="<?=__('Select from list')?>" data-select-all-text="<?=__('select all')?>" data-unselect-all-text="<?=__('unselect all')?>">
                                </select>
                            </div>
                            <div class="report-form-group col-md-6 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('Structures')?>
                                    <span class="ms-custom-select-all"></span>
                                </label>
                                <select name="objects[]" disabled multiple data-select-text="<?=__('Select from list')?>" data-select-all-text="<?=__('select all')?>" data-unselect-all-text="<?=__('unselect all')?>"></select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 rtl-float-right">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="table_label"><?=__('Report Range')?></label>
                                <div class="report-range-box">
                                    <div class="report-range-unit">
                                        <div class="report-range-unit-date">
                                            <label class="table_label"><?=__('from')?></label>
                                            <div class="input-group date report-start-date" id="report-start-date" data-provide="datepicker">
                                                <div class="input-group-addon small-input-group">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </div>
                                                <input type="text" class="form-control" data-min-date data-date-format="DD/MM/YYYY" name="from" value="<?=date('d/m/Y',time() - (60 * 60 * 24 * 6 * 31))?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="report-range-unit">
                                        <div class="report-range-unit-date">
                                            <label class="table_label"><?=__('to')?></label>
                                            <div class="input-group date report-end-date" id="report-end-date" data-provide="datepicker">
                                                <div class="input-group-addon small-input-group">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </div>
                                                <input type="text" class="form-control" data-date-format="DD/MM/YYYY" name="to" value="<?=date('d/m/Y',time() + (60 * 60 * 24))?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label class="pretty-checkbox spec-details" for="spec_details"><input type="checkbox" id="spec_details" value="spec_details"><?=__('Specialty details')?></label>
                    </div>
                </div>

            </div>

            <div class="generate-reports-footer">
                <div class="row">
                    <div class="col-md-12">
                        <a href="#" class="clear-all-reports clear-report-form"><?=__('Clear all')?></a>
                        <input type="button" id="generate-report" data-url="<?=URL::site('reports/quality')?>" class="inline_block_btn blue-light-button disabled-input" value="<?=__('Generate')?>" >
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="generated-results"></div>
<script>
    var SAVED_REPORTS = null;
    $(document).ready(function() {

        //company select
        var projectsSelect = $('select[name="projects[]"]');
        var craftsSelect = $('select[name="crafts[]"]');
        var objectsSelect = $('select[name="objects[]"]');
        var companySelect = $('select[name="company"]');

        function updateReportsList(){
            var output = '';
            if(SAVED_REPORTS != null && SAVED_REPORTS.length > 0){
                if(projectsSelect.val() == null){
                    for(var i =0; i < SAVED_REPORTS.length; i++){
                        output += '<tr>' +
                            '<td><a href="' + SAVED_REPORTS[i].url + '">' + SAVED_REPORTS[i].name + '</a></td>' +
                             '<td><a href="' + SAVED_REPORTS[i].deleteURL + '" class="remove-report"><i class="q4bikon-delete"></i></a></td>' +
                             '</tr>';
                    }
                }else{
                    var vals = projectsSelect.val();
                    for(var i =0; i < SAVED_REPORTS.length; i++){
                        for (var j=0; j < SAVED_REPORTS[i].projects.length; j++){
                            if(vals.includes("" + SAVED_REPORTS[i].projects[j])){

                                output += '<tr>' +
                                    '<td><a href="' + SAVED_REPORTS[i].url + '">' + SAVED_REPORTS[i].name + '</a></td>' +
                                    '<td><a href="' + SAVED_REPORTS[i].deleteURL + '" class="remove-report"><i class="q4bikon-delete"></i></a></td>' +
                                    '</tr>';
                                break;
                            }
                        }
                    }
                }
            }

            if(output.length < 1){
                output = "<tr><td><span class=\"no-report\"><?=__('No saved reports')?></span></td></tr>";
            }
            $(document).find('.saved-reports table').html(output);
        }

        companySelect.on('change',function(){
            var url = $(this).data('url') + '/' + this.value;
            projectsSelect.html('').attr('disabled','disabled').multiselect('reload');
            objectsSelect.html('').attr('disabled','disabled').multiselect('reload');
            craftsSelect.html('').attr('disabled','disabled').multiselect('reload');
            $.get( url, function( data ) {
                var data = JSON.parse(data);
                //$(document).find('.saved-reports-container').replaceWith(data.savedReports);
                SAVED_REPORTS = data.savedReports;
                updateReportsList();
                $.each(data.projects, function(key, project) {
                    var $option = $("<option/>", {
                        value: project.id,
                        text: project.name
                    });
                    projectsSelect.append($option);
                });
                $.each(data.crafts, function(key, craft) {
                    var $option = $("<option/>", {
                        value: craft.id,
                        text: craft.name
                    });
                    craftsSelect.append($option);
                });
                projectsSelect.removeAttr('disabled').multiselect('reload');
                craftsSelect.removeAttr('disabled').multiselect('reload');
            });
        });

        projectsSelect.on('change',function(){

            var values = $(this).val();
            updateReportsList();
            if(values == null || values.length != 1){
                objectsSelect.html('').attr('disabled','disabled').multiselect('reload');
            }else{
                var url = $(this).data('url');
                $.ajax({
                    url: url,
                    data: JSON.stringify({'projects' : values, 'csrf' : Q4U.getCsrfToken(), 'x-form-secure-tkn': ""}),
                    method: 'POST',
                    type: 'HTML',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var data = JSON.parse(data);

                        $.each(data, function(key, object) {
                            var $option = $("<option/>", {
                                value: object.id,
                                text: object.name
                            });
                            objectsSelect.append($option);
                        });
                        objectsSelect.removeAttr('disabled').multiselect('reload');
                    }
                });
            }

            if(formIsValid()){
                $('#generate-report').removeClass('disabled-input');
            }else{
                $('#generate-report').addClass('disabled-input');
            }
        });

        craftsSelect.on('change',function(){
            if(formIsValid()){
                $('#generate-report').removeClass('disabled-input');
            }else{
                $('#generate-report').addClass('disabled-input');
            }
        });

        objectsSelect.on('change',function(){
            if(formIsValid()){
                $('#generate-report').removeClass('disabled-input');
            }else{
                $('#generate-report').addClass('disabled-input');
            }
        });

        function formIsValid(){
            var cmpVal = companySelect.val();
            var projVal = projectsSelect.val();
            var craftVal = craftsSelect.val();
            var objVal = objectsSelect.val();
            if(cmpVal == null){
                return false;
            }

            if(projVal == null){
                return false;
            }else{
                if(projVal.length == 1){
                    if(objVal == null) return false;
                }
            }

            if(craftVal == null){
                return false
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
            if($('#spec_details')[0].checked){
                $('#spec_details').parent().click();
            }
        });

        $('#generate-report').on('click',function(){


            if( ! formIsValid() || $(this).hasClass('disabled-input')){
                return;
            }
            var url = $(this).data('url');
            var data = {
                'company': companySelect[0].value,
                'projects': projectsSelect.val(),
                'crafts': craftsSelect.val(),
                'objects': objectsSelect.val(),
                'from': $('input[name="from"]').val(),
                'to': $('input[name="to"]').val(),
                'speciality_details': $('#spec_details')[0].checked,
                'csrf' : Q4U.getCsrfToken(),
                'x-form-secure-tkn': ""
            };

            $.ajax({
                url: url + '/show',
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
            zingchart.exec('piechart', 'destroy');
            zingchart.exec('piechart2', 'destroy');
            zingchart.exec('barchart', 'destroy');
            $('.generate-reports').slideDown("fast",function () {
                $('#generated-results').html('');
            });

        });

        $(document).on('click','.remove-report',function(e){
            e.preventDefault();
            var $this = $(this);
            Q4U.confirm('<?=__('Delete Saved Report?')?>',{
                confirmCallback: function(el,p) {
                    $.get($this.attr('href'), function( data ) {
                        $this.parents('tr').remove();
                    });
                },
                confirmText: '<?=__('Delete')?>',
                type:'danger'
            });
        });
    });
</script>
