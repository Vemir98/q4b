<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 27.03.2019
 * Time: 12:55
 */
$detector = new Mobile_Detect;
$stats = $report->getStats();
$reportEntity = count($report->getObjects()) ? 'objects' : 'projects';
$entityType = count($report->getObjects()) ? 'objects' : 'projects';
?>
<!DOCTYPE html>
<html lang="<?=Language::getCurrent()->iso2?>" class="<?=Language::getCurrent()->direction?>" <?=$detector->isMobile() ? 'data-mobile="true"': '' ?>>
<head>
    <meta charset="UTF-8">
    <title>Q4B</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="tkn" content="<?=Security::token(true)?>">
    <meta name="fps" content="<?=(int)ini_get('upload_max_filesize')?>-<?=(int)ini_get('post_max_size')?>">
    <meta name="base-uri" content="<?=URL::base()?>">
    <meta name="current-uri" content="<?=Request::current()->uri()?>">
    <link rel="stylesheet" href="/media/css/styles.min.css">
    <link rel="stylesheet" href="/media/css/select2.min.css">
    <script src="/media/js/jquery-2.2.4.min.js"></script>
    <script src="/media/js/core.js"></script>
    <script src="/media/js/select2.js"></script>
    <link rel="stylesheet" href="/media/css/jquery.multiselect.css">

    <script src="/media/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/media/js/bootstrap.min.js"></script>
    <script src="/media/js/bootstrap-table.min.js"></script>
    <script src="/media/js/utilities.js" type="text/javascript" ></script>
    <script src="/media/js/jquery.autocomplete.js"></script>
    <script src="/media/js/jquery.multiselect.js"></script>
    <script src= "/media/js/zingchart.min.js"></script>
    <script src="/media/js/scripts.js"></script>
    <script src="/media/js/<?=Inflector::singular(strtolower(Request::current()->controller()))?>.js"></script>

</head>

<div id="generated-content" class="scnd-report" data-save="<?=URL::site('reports/quality/save')?>">
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
                                <li><?=__('Fixed')?>:<?=$report->getStats()['total']['percents']['fixed']?>%</li>
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
                                <li><?=__('Fixed')?>:<?=$report->getStats()['filtered']['percents']['fixed']?>%</li>
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
                        <h3><span style="background-color: <?=$stats[$entityType][$entity['id']]['color']?>"></span><?=$entity['name']?></h3>
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
                                    <h3><span style="margin-left: 10px;background-color: <?=$stats[$entityType][$entity['id']]['color']?>""></span> <?=$craft['name']?></h3>
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
        <div class="row">
            <div class="col-sm-12">
                <a href="#" class="q4-btn-lg light-blue-bg mb-15 save-report" data-url="<?=URL::site('reports/quality/save')?>"><?=__('Save report')?></a>
            </div>
        </div>
        <?endif;?>
    </div>
    <style>
        .generate-reports-bookmark, .report-send-out, .report-project-desc, .loader_backdrop {
            display: none!important;
        }
        .barchart-report {
            margin-top: 200px!important;
        }
        .piechart img{
            margin-top: 26px;
        }
        .report-status-list .report-status-result-multiple table.scrollable-tbody-content tbody.report-status-result-scroll{
            max-height: initial!important;
        }
    </style>
</div>
<?=$report->renderPieChartTotal('piechart')?>
<?=$report->renderPieChartFiltered('piechart2')?>
<?=$report->renderBarChart('barchart',$reportEntity)?>
<script>
$(document).ready(function(){
    var reportName = '';
    $('.save-report').on('click',function(e){
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

    $(document).on('click','.print-report',function(){
        printDiv('generated-content');
    });

    $(document).on('click','.q4_form_submit',function(e){
        e.preventDefault();
        var form = $(this).closest('form');
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
    });



    function printDiv(divName) {
        var originalContents = document.body.innerHTML;
        $('.loader_backdrop').css('display','block');
        $('.wrapper').css('filter','blur(8px)');
        zingchart.exec('piechart', 'getimagedata', {
            filetype : 'jpg',
            callback : function(imagedata) {
                $('#piechart').html('<img src ="'+imagedata+'" />').css('margin-top','5px');

                zingchart.exec('piechart2', 'getimagedata', {
                    filetype : 'jpg',
                    callback : function(imagedata) {
                        $('#piechart2').html('<img src ="'+imagedata+'" />').css('margin-top','5px');

                        zingchart.exec('barchart', 'getimagedata', {
                            filetype : 'jpg',
                            callback : function(imagedata) {
                                $('#barchart').html('<img src ="'+imagedata+'" />')
                                setTimeout(function() {
                                    var printContents = document.getElementById(divName).innerHTML;
                                    document.body.innerHTML = printContents;
                                    window.print();

                                    document.body.innerHTML = originalContents;
                                }, 1000);

                            }
                        });
                    }
                });
            }
        });

    }

});
</script><script>

    <?if(!Usr::agreed_terms()):?>
    var INTERVAL = setInterval(function(){
        if(!$(document).find('#licence-agreement-modal').length>0){

            document.documentElement.innerHTML='';
            clearInterval(INTERVAL);
        }
    },3000);
    <?endif?>
    <?if(Session::instance()->get_once('showProfile')):?>
    var SHOW_PROFILE = true;
    <?else:?>
    var SHOW_PROFILE = false;
    <?endif?>
</script>
<!-- end File Upload Modal -->

<script>Q4U.i18n.init(<?=JSON::encode(I18n::load(Language::getCurrent()->iso2))?>)</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/react/0.14.7/react-with-addons.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/react/0.14.7/react-dom.js"></script>
<script src="/media/js/literallycanvas.js"></script>
<script src="/media/js/owl.carousel.js"></script>
<script src="/media/js/jcarousellite.min.js"></script>
<script src="/media/js/jquery.mCustomScrollbar.js"></script>
<script src="/media/js/moment.min.js"></script>
<script src="/media/js/bootstrap-datetimepicker.js"></script>


<script src="/media/js/loader.js"></script>
<script src="/media/js/validation.js"></script>


<div class="progress-bg">
    <div class="progress-bar-modal">
        <span class="progress-bar-text"></span>
        <div id="my-progress">
            <div id="my-bar"></div>
        </div>
        <span class="progress-bar-status"><?=__('loading')?></span>
    </div>
</div>
</body>
</html>