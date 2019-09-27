<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 27.03.2019
 * Time: 12:55
 */
$stats = $report->getStats();
$reportEntity = count($report->getObjects()) ? 'objects' : 'projects';
$entityType = count($report->getObjects()) ? 'objects' : 'projects';
?>
<div id="generated-content" class="scnd-report" data-save="<?=URL::site('reports/quality/save')?>">
    <div class="generate-reports-bookmark">
        <div class="generate-reports-bookmark-title">
            <h2><?=__('Generated Reports')?></h2>
        </div>
        <div class="generate-reports-bookmark-arrow" <?if($report->isSaved()):?>data-url="<?=URL::site('reports/quality')?>"<?endif;?>>
            <i class="q4bikon-arrow_bottom"></i>
        </div>
    </div>
        <div class="report-send-out">
            <div class="row">
                <div class="col-md-12">
                    <a href="#" class="q4-page-export" data-url="<?=URL::site('reports/quality/save')?>">
                        <i class="q4bikon-export icon-export"></i>
                        <span class="q4-page-export-text"><?=__('Export')?></span>
                    </a>
                    <?if(!empty($reportId)):?>
                    <span class="send-out-button send-reports" data-url="<?=URL::site('reports/quality/send_reports/'.$reportId)?>"><i class="q4bikon-email"></i> <?=__('Send by email')?></span>
                    <?else:?>
                        <?php
                        $prId = [];
                        foreach ($report->getProjects() as $project){
                            $prId[] = $project['id'];
                        }
                        $prId = "p".implode('-',$prId)
                        ?>
                        <span class="send-out-button send-reports" data-url="<?=URL::site('reports/quality/send_reports/'.$prId)?>"><i class="q4bikon-email"></i> <?=__('Send by email')?></span>
                    <?endif?>
                    <span class="send-out-button print-report"><i class="q4bikon-print"></i> <?=__('Print')?></span>

                </div>
            </div>
        </div>
            <div class="report-project-desc f0" style="margin-top: 25px;">
                <div class="report-project-desc-image">
                    <img src="/<?=$report->getCompany()['logo']?>" alt="project images">
                </div>
                <div class="report-project-desc-list">
                    <ul<?if(!count($report->getObjects())):?> style="width: 100%"<?endif?>>
                        <li>
                            <span class="light-blue">
                                <i class="icon q4bikon-companies"></i>
                                <?=__('Company name')?>:
                            </span>
                            <span class="dark-blue">
                                <?=$report->getCompany()['name']?>
                            </span>
                        </li>
                        <li>
                            <span class="light-blue">
                                <i class="icon q4bikon-project"></i>
                                <?=__('Project name')?>:
                            </span>
                            <span class="dark-blue">
                                <?if(!count($report->getObjects())):?>
                                    <?foreach ($report->getProjects() as $project):?>
                                    <?$tmpProj[] = $project['name']?>
                                    <?endforeach;?>
                                    <?=implode(', ',$tmpProj)?>
                                <?else:?>
                                    <?$tmpProj = $report->getProjects()?>
                                    <?=array_shift($tmpProj)['name']?>
                                <?endif;?>
                                <?unset($tmpProj)?>
                            </span>
                        </li>
                        <li>
                            <span class="light-blue">
                                <i class="q4bikon-date"></i>
                                <?=__('Report Range')?>:
                            </span>
                            <span class="dark-blue">
                                <?=$report->getDateFrom(true)?> - <?=$report->getDateTo(true)?>
                            </span>
                        </li>
                    </ul>
                    <?if(count($report->getObjects())):?>
                    <ul>
                        <li></li>
                        <li>
                            <span class="light-blue">
                                <i class="q4bikon-date"></i>
                                <?=__('Structures')?>:
                            </span>
                            <span class="dark-blue">
                                <?foreach ($report->getObjects() as $object):?>
                                    <?$tmpObj[] = $object['name']?>
                                <?endforeach;?>
                                <?=implode(', ',$tmpObj)?>
                                <? unset($tmpObj)?>
                            </span>
                        </li>
                        <li></li>
                    </ul>
                    <?endif;?>
                </div>
                <div class="clear"></div>
            </div>

    <div class="report-status-list double-result">
        <div class="report-status-results">
            <div class="f0">
                <div class="report-status-result-unit">
                    <div class="report-status-pie">
                        <div class="report-status-pie-top">
                            <h3><?=__('Status statistics')?></h3>
                        </div>

                        <div class="report-status-pie-bottom">
                            <div id="piechart" class="piechart"></div>
                        </div>
                        <div style="padding-bottom: 20px;color: #1ebae5;background-color: white;">
                            <ul style="text-align: left;padding: 1em 5em;font-size: 14px;line-height: 20px;">
                                <li><?=__(Enum_QualityControlStatus::Existing).' + '.__(Enum_QualityControlStatus::Normal)?>: <?=$report->getStats()['total']['percents']['a']?>%</li>
                                <li><?=__(Enum_QualityControlStatus::Existing).' + '.__(Enum_QualityControlStatus::Normal).' + '.__(Enum_QualityControlStatus::Repaired)?>: <?=$report->getStats()['total']['percents']['b']?>%</li>
<!--                                <li>--><?//=__('Fixed')?><!--:--><?//=$report->getStats()['total']['percents']['fixed']?><!--%</li>-->
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="report-status-result-unit">
                    <div class="report-status-pie">
                        <div class="report-status-pie-top">
                            <h3><?=__('Status statistics (filtered)')?></h3>
                        </div>

                        <div class="report-status-pie-bottom">
                            <div id="piechart2" class="piechart"></div>
                        </div>
                        <div style="padding-bottom: 20px;color: #1ebae5;background-color: white;">
                            <ul style="text-align: left;padding: 1em 5em;font-size: 14px;line-height: 20px;">
                                <li><?=__(Enum_QualityControlStatus::Existing).' + '.__(Enum_QualityControlStatus::Normal)?>: <?=$report->getStats()['filtered']['percents']['a']?>%</li>
                                <li><?=__(Enum_QualityControlStatus::Existing).' + '.__(Enum_QualityControlStatus::Normal).' + '.__(Enum_QualityControlStatus::Repaired)?>: <?=$report->getStats()['filtered']['percents']['b']?>%</li>
<!--                                <li>--><?//=__('Fixed')?><!--:--><?//=$report->getStats()['filtered']['percents']['fixed']?><!--%</li>-->
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="barchart-report" style="margin: 25px; box-sizing: border-box;">
                    <div id="barchart" style="box-shadow: 1px 1px 12px 1px #CDD0D7; border: 1px solid #d4e1ea;"></div>
                </div>
                <div class="report-project_status">
                    <?foreach ($report->getProjectsORObjects($reportEntity) as $entity):?>
                    <div class="report-status-result-multiple">
                        <h3><span style="background-color: <?=$stats[$entityType][$entity['id']]['color']?>!important;-webkit-print-color-adjust: exact;"></span><?=$entity['name']?></h3>
                        <table class="table scrollable-tbody-content">
                            <thead>
                            <tr>
                                <th><?=__('Crafts List')?></th>
                                <th>
                                    <div class="double-cpt">
                                        <div class="double-sub-cpt1"><?=__('Quantity')?></div>
                                        <div class="double-sub-cpt2">
                                            <span class="omission-points"><?=__('Total')?></span>
                                            <span class="omission-points"><?=__('Filtered')?></span>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="report-status-result-scroll qc-vertical-scrll">
                            <?
                                $craftStatTotal = 0;
                                $craftStatFiltered = 0;
                            ?>
                            <?foreach ($report->getCompanyCrafts() as $craft):?>
                                <?
                                $craftStat['total'] = isset($stats[$reportEntity][$entity['id']]['crafts'][$craft['id']]['count']) ? $stats[$reportEntity][$entity['id']]['crafts'][$craft['id']]['count'] : 0;
                                $craftStat['filtered'] = isset($stats[$reportEntity][$entity['id']]['filteredCrafts'][$craft['id']]['count']) ? $stats[$reportEntity][$entity['id']]['crafts'][$craft['id']]['count'] : 0;
                                $craftStatTotal += $craftStat['total'];
                                $craftStatFiltered += $craftStat['filtered'];
                                ?>
                                <tr>
                                    <td data-th="<?=__('Specialty list')?>">
                                        <span class="report-status-link" data-toggle="modal" data-target="#modal-report-status-crafts"><?=$craft['name']?></span>
                                    </td>
                                    <td data-th="<?=__('Quantity')?>" class="enlarged">
                                        <div class="double-cell-cpt">
                                            <div class="double-cell-cpt1">
                                                <span class="report-status-quantity"><?=$craftStat['total']?></span>
                                            </div>
                                            <div class="double-cell-cpt2">
                                                <span class="report-status-quantity"><?=$craftStat['filtered']?></span>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            <?endforeach;?>
                            <tr class="dark-row" style="background: #9bc5e0;">
                                <td data-th="<?=__('Specialty list')?>">
                                    <span class="report-status-link" data-toggle="modal" data-target="#modal-report-status-crafts" style="color: #005c87"><?=__('Total')?></span>
                                </td>
                                <td data-th="<?=__('Quantity')?>" class="enlarged">
                                    <div class="double-cell-cpt">
                                        <div class="double-cell-cpt1">
                                            <span class="report-status-quantity"><?=$craftStatTotal?></span>
                                        </div>
                                        <div class="double-cell-cpt2">
                                            <span class="report-status-quantity"><?=$craftStatFiltered?></span>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="craft-title">
                            <h3><?=UTF8::ucfirst(__('general'))?></h3>
                        </div>
                        <table class="table report-status-result-second-table" style="margin-top: -20px">
                            <tr>
                                <td></td>
                                <?foreach (Enum_QualityControlConditionList::toArray() as $item):?>
                                <td><?=__($item)?></td>
                                <?endforeach;?>
                            </tr>
                            <?foreach (Enum_QualityControlConditionLevel::toArray() as $conditionLevel):?>
                            <tr>
                                <td><?=__($conditionLevel)?></td>
                                <?foreach (Enum_QualityControlConditionList::toArray() as $conditionList):?>
                                    <td><?=$stats[$reportEntity][$entity['id']]['defects'][$conditionLevel][$conditionList]?></td>
                                <?endforeach;?>
                            </tr>
                            <?endforeach;?>
                            <tr>
                                <td><?=__('Total')?></td>
                                <?foreach ($stats[$reportEntity][$entity['id']]['defects']['total'] as $val):?>
                                    <td><?=$val?></td>
                                <?endforeach;?>
                            </tr>
                        </table>
                        <div class="general-opinion">
                            <h5><span class="circle">i</span><?=__('General opinion')?></h5>
                            <textarea data-id="<?=$entity['id']?>"><?=$entity['generalOpinion']?></textarea>
                        </div>

                        <?if($report->hasSpecialityDetails()):?>
                            <?foreach ($report->getCompanyCrafts() as $craft):?>
                            <?if(!$report->specHasResult($stats[$reportEntity][$entity['id']]['craftDefects'][$craft['id']]['total'])): continue; endif;?>
                                <div class="craft-title">
                                    <h3><span style="margin-left: 10px;background-color: <?=$stats[$entityType][$entity['id']]['color']?>!important;-webkit-print-color-adjust: exact;""></span> <?=$craft['name']?></h3>
                                </div>
                                <table class="table report-status-result-second-table spec-tbl">
                                    <tr>
                                        <td></td>
                                        <?foreach (Enum_QualityControlConditionList::toArray() as $item):?>
                                            <td><?=__($item)?></td>
                                        <?endforeach;?>
                                    </tr>
                                    <?foreach (Enum_QualityControlConditionLevel::toArray() as $conditionLevel):?>
                                        <tr>
                                            <td><?=__($conditionLevel)?></td>
                                            <?foreach (Enum_QualityControlConditionList::toArray() as $conditionList):?>
                                                <td><?=$stats[$reportEntity][$entity['id']]['craftDefects'][$craft['id']][$conditionLevel][$conditionList]?></td>
                                            <?endforeach;?>
                                        </tr>
                                    <?endforeach;?>
                                    <tr>
                                        <td><?=__('Total')?></td>
                                        <?foreach ($stats[$reportEntity][$entity['id']]['craftDefects'][$craft['id']]['total'] as $val):?>
                                            <td><?=$val?></td>
                                        <?endforeach;?>
                                    </tr>
                                </table>
                            <?endforeach?>
                        <?endif;?>
                    </div>
                    <?endforeach;?>
                </div>
            </div>
        </div>
        <div class="hidden report-data"><?=JSON::encode($stats)?></div>
        <?if(!$report->isSaved()):?>
        <div class="row save-report-row">
            <div class="col-sm-12">
                <a href="#" class="q4-btn-lg light-blue-bg mb-15 save-report" data-url="<?=URL::site('reports/quality/save')?>"><?=__('Save report')?></a>
            </div>
        </div>
        <?endif;?>
    </div>
    <?if(!$isPhantom):?>
    <style>
        @media print {
            @page {
                size: landscape;
                margin: 20px;
            }
            .generate-reports-bookmark, .report-send-out, .report-project-desc, .loader_backdrop {
                display: none!important;
            }
            .barchart-report {
                margin-top: 200px!important;
            }
            .piechart img{
                margin-left: -5px;
            }
            .piechart {
                height: 143px!important;
            }
            .report-status-list .report-status-result-multiple table.scrollable-tbody-content tbody.report-status-result-scroll{
                overflow: hidden!important;
                max-height: 254px!important;
            }
            .report-status-result-unit {
                width: 11cm!important;
                display: block!important;
                float: left!important;
                margin: 0 25px!important;
            }
            .report-status-pie > div:last-child{
                height: 80px!important;
            }
            .report-status-pie > div:last-child ul{
                margin-top: -90px!important;
                text-align: left;
                padding: 1em 5em;
                font-size: 12px!important;
                line-height: 15px;
            }
            .barchart-report {
                margin-top: 0 !important;
                padding-top: 20px;
                float: none!important;
                clear: both!important;
            }
            .report-status-result-multiple h3{
                page-break-before: always!important;
            }
            .report-status-list .report-status-result-multiple table.scrollable-tbody-content thead{
                width: 100%!important;
            }
            .content, .report-status-list, .report-status-results{
                margin: 0!important;
                padding: 0!important;
            }
            .save-report-row{
                display: none;
            }
            .report-status-result-multiple{
                page-break-before: always;
                break-before: always;
                display: block!important;
                float: none!important;
                page-break-inside: avoid!important;
                margin: 0;
            }
            .table.report-status-result-second-table tr td, .report-status-list .report-status-result-multiple table.scrollable-tbody-content tr td{
                border-top: 1px solid black;
            }
            .general-opinion{
                page-break-after: always;
                break-after: always;
                display: block;
                float: none!important;
                page-break-inside: avoid;
            }
            table {float: none !important;  page-break-inside: avoid; page-break-before: always; display: block!important;}
            /*.report-status-list .report-status-result-multiple table.scrollable-tbody-content tr:nth-child(even) {*/
                /*background: #f2faff!important;*/
            /*}*/
            /*.table > tbody > tr:nth-child(even) {*/
                /*background: #f2faff!important;*/
            /*}*/
            /*.report-status-list table tr td:last-child{*/
                /*border-left: 1px solid #d4e1ea!important;*/
            /*}*/
            /*.content {*/
                /*padding: 0 1cm 1cm 1cm!important;*/
            /*}*/
            /*.report-status-list .report-status-results{*/
                /*width: 100%;*/
            /*}*/
        }

        .rtl .scnd-report .report-status-result-unit{
            direction: rtl!important;
        }
    </style>
    <?else:?>
        <style>
            @media print {
                @page {
                    size: landscape;
                    margin: 20px;
                    -webkit-print-color-adjust: exact;
                }
                .generate-reports-bookmark, .report-send-out, .report-project-desc, .loader_backdrop {
                    display: none!important;
                }
                .piechart{
                    margin-left: 5px!important;
                    margin-top: 5px!important;
                }
                .report-status-list .report-status-result-multiple table.scrollable-tbody-content tbody.report-status-result-scroll{
                    overflow: hidden!important;
                    max-height: 254px!important;
                }
                .report-status-list .report-status-result-multiple table.scrollable-tbody-content thead{
                    width: 100%!important;
                }
                *{
                    -webkit-print-color-adjust: exact!important;
                }
            }
            .rtl .scnd-report .report-status-result-unit{
                direction: ltr!important;
            }
        </style>
    <?endif;?>
</div>
<?=$report->renderPieChartTotal('piechart')?>
<?=$report->renderPieChartFiltered('piechart2')?>
<?=$report->renderBarChart('barchart',$reportEntity)?>
<script>
$(document).ready(function(){
    var reportName = '';
    $('.save-report').off('click').on('click',function(e){
        e.preventDefault();
        var $this = $(this);
        var url = $(this).data('url');
        Q4U.confirm('<p style="color:#1ebae5;"><?=__('Report name')?></p><br><input type="text" class="form-control" name="report-title" style="width: 50%;\n' +
            '    margin: auto;\n' +
            '    min-width: 300px;">',{
            confirmCallback: function(el,p) {
                if(reportName.length < 1){
                    Q4U.alert('Report name must not be empty',{type: "danger",confirmText: __("OK")});
                }else{
                    var data = {generalOpinions:[],json:''};
                    $(document).find('.general-opinion textarea').each(function(){
                        var myId = parseInt($(this).data('id'));
                        if(myId)
                        data.generalOpinions.push({id : myId, text : $(this).val()});
                    });
                    data.json = $('.report-data').text();
                    $.ajax({
                        url: url,
                        data: JSON.stringify({'name': reportName,'generalOpinions' : data.generalOpinions, 'json': data.json, 'csrf' : Q4U.getCsrfToken(), 'x-form-secure-tkn': ""}),
                        method: 'POST',
                        type: 'HTML',
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            data = JSON.parse(data);
                            $this.remove();
                            $(document).find('.saved-reports-container').replaceWith(data.savedReports);
                        }
                    });
                    // console.log(reportName);
                    // console.log($('#generated-results').html());
                }
            },
            confirmText: '<?=__('Save')?>'
        });
    });
    $(document).on('mouseover','.confirmation-modal-footer .btn-confirm',function () {
        reportName = $(this).parent().siblings('.confirmation-modal-body').find('input').val();
    });
    $('.generate-reports-bookmark-arrow').on('click',function(){
        if($(this).data('url') != undefined && $(this).data('url').length > 1){
            $('.loader_backdrop').show();
            document.location.href = $(this).data('url');
        }
    });

    $('.print-report').off('click').on('click',function(e){
        e.preventDefault();
        printDiv('generated-content');
    });

    // $(document).on('submit','.q4_form',function(e){
    //     e.preventDefault();
    //     var form = $(this).closest('form');
    //     console.log(JSON.stringify( form.serializeArray() ));
    //     var data = {generalOpinions:[],json:''};
    //     $(document).find('.general-opinion textarea').each(function(){
    //         var myId = parseInt($(this).data('id'));
    //         if(myId)
    //             data.generalOpinions.push({id : myId, text : $(this).val()});
    //     });
    //     data.json = $('.report-data').text();
    //     var input = $("<input>").attr("type", "hidden").attr('name','report').val(encodeURIComponent(JSON.stringify({'name': "mailing", "is_hidden": "1",'generalOpinions' : data.generalOpinions, 'json': data.json, 'csrf' : Q4U.getCsrfToken(), 'x-form-secure-tkn': ""})));
    //     form.append($(input));
    // });



    $( document ).ajaxSuccess(function( event, xhr, settings ) {
        var data = JSON.parse(xhr.responseText);
        if(data && data.triggerEvent == "sendReports"){
            var inter = setInterval(function () {
                var form = $(document).find('.modal-content .q4_form');
                if (form.length){
                    console.log(JSON.stringify( form.serializeArray() ));
                    var data = {generalOpinions:[],json:''};
                    $(document).find('.general-opinion textarea').each(function(){
                        var myId = parseInt($(this).data('id'));
                        if(myId)
                            data.generalOpinions.push({id : myId, text : $(this).val()});
                    });
                    data.json = $('.report-data').text();
                    var input = $("<input>").attr("type", "hidden").attr('name','report').val(encodeURIComponent(JSON.stringify({'name': "mailing", "is_hidden": "1",'generalOpinions' : data.generalOpinions, 'json': data.json, 'csrf' : Q4U.getCsrfToken(), 'x-form-secure-tkn': ""})));
                    form.append($(input));
                    clearInterval(inter);
                }
            },500);
        }
    });

    function printDiv(divName) {
        var originalContents = document.body.innerHTML;
        $('.loader_backdrop').css('display','block');
        $('.wrapper').css('filter','blur(8px)');
        var piechart = null;
        var piechart2 = null;
        var barchart = null;
        zingchart.exec('piechart', 'getimagedata', {
            filetype : 'jpg',
            callback : function(imagedata) {
                piechart = zingchart.exec('piechart', 'getdata');
                $('#piechart').html('<img src ="'+imagedata+'" />').css('margin-top','5px');

                zingchart.exec('piechart2', 'getimagedata', {
                    filetype : 'jpg',
                    callback : function(imagedata) {
                        piechart2 = zingchart.exec('piechart2', 'getdata');
                        $('#piechart2').html('<img src ="'+imagedata+'" />').css('margin-top','5px');

                        zingchart.exec('barchart', 'getimagedata', {
                            filetype : 'jpg',
                            callback : function(imagedata) {
                                barchart = zingchart.exec('barchart', 'getdata');
                                zingchart.exec('barchart', 'destroy');
                                $('#barchart').html('<img src ="'+imagedata+'" />');
                                // var printContents = document.getElementById(divName).innerHTML;
                                // document.body.innerHTML = printContents;
                                setTimeout(function() {

                                    // $(document).find('.report-status-list .report-status-result-multiple table.scrollable-tbody-content tbody.report-status-result-scroll').css('overflow', 'hidden');
                                    $('.loader_backdrop').css('display','none');
                                    $('.wrapper').css('filter','none');
                                    window.print();
                                    $('#barchart').html('');
                                    zingchart.render({
                                        id : 'barchart',
                                        data : barchart});
                                    // $('#piechart').html('');
                                    // zingchart.render({
                                    //     id : 'piechart',
                                    //     data : piechart});
                                    // $('#piechart2').html('');
                                    // zingchart.render({
                                    //     id : 'piechart2',
                                    //     data : piechart2});
                                    // $('#barchart').html('');
                                    $('#piechart').html('');
                                    $('#piechart2').html('');
                                    zingchart.exec('barchart', 'reload');
                                    zingchart.exec('piechart', 'reload');
                                    zingchart.exec('piechart2', 'reload');
                                    // document.body.innerHTML = originalContents;
                                }, 1000);

                            }
                        });
                    }
                });
            }
        });

    }

    $('.q4-page-export').off('click').on('click', function(e){
        e.preventDefault();
        var url = $(this).data('url');
        var data = {generalOpinions:[],json:''};
        $(document).find('.general-opinion textarea').each(function(){
            var myId = parseInt($(this).data('id'));
            if(myId)
                data.generalOpinions.push({id : myId, text : $(this).val()});
        });
        data.json = $('.report-data').text();
        $.ajax({
            url: url,
            data: JSON.stringify({'name': 'pdf','is_hidden':'1','generalOpinions' : data.generalOpinions, 'json': data.json, 'csrf' : Q4U.getCsrfToken(), 'x-form-secure-tkn': ""}),
            method: 'POST',
            type: 'HTML',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                data = JSON.parse(data);
                var win = window.open(data.url, '_blank');
                if (win) {
                    //Browser has allowed it to be opened
                    win.focus();
                } else {
                    //Browser has blocked it
                    alert('Please allow popups for this website');
                }
            }
        });
    });

    $(document).find('.no-print').removeClass('no-print');
    $(document).find('.sidebar, .layout > header, .q4-copyright').addClass('no-print');

});
</script>
