<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 01.02.2020
 * Time: 14:16
 */
$stats = $report->getStats();
$reportEntity = count($report->getObjects()) ? 'objects' : 'projects';
$entityType = count($report->getObjects()) ? 'objects' : 'projects';

?>
<style>
    @media print
    {
        .no-print, .no-print *
        {
            display: none !important;
        }
        .barchart,.pies{
            margin: 0!important;
        }
        .barchart img{
            margin: 0!important;
            border: none!important;
        }
        .barchart{
            width: 72em;
            page-break-after: always;
        }
    }
    *{
        color: #003a63;
        font-weight: normal;
        font-style: normal;
        font-family: "proxima_nova_rgregular", Arial, Helvetica, sans-serif!important;
        -webkit-print-color-adjust: exact!important;
    }
    .logo img{
        width: 170px;
    }
    .logo{
        width: 200px;
    }
    .header{
        background-color: white;
        margin: 1em auto auto auto;
        width: 98%;
        border: 1px solid #d4e1ea!important;
        height: 10em;
    }
    .header td{
        vertical-align: middle;
        padding: 1em;
    }
    .pies .pie .chart{
        width: 35.2em;
        background-color: white;
    }
    .pies .pie .chart .image{
        width: 35em;
    }
    .pies .pie .chart .image .img-wrapp{
        overflow: hidden;
        width: 35em;
        height: 205px;
        position: relative;
        padding: 1px;
    }
    .pies .pie .chart .stats{
        text-align: left;
        padding: 1em 5em;
        font-size: 14px;
        line-height: 20px;
    }
    .pies .pie .chart .stats li{
        color: #1ebae5!important;
    }
    .piechart{
        width: 35em;
        position: absolute;
    }
    .pies{
        width: 72em;
        <?if($isPhantom):?>
        margin: 5em auto auto auto!important;
        zoom: 1.2; /* IE */
        -moz-transform: scale(1.2); /* Firefox */
        -o-transform: scale(1.2); /* Opera */
        -webkit-transform: scale(1.2); /* Safari And Chrome */
        transform: scale(1.2); /* Standard Property */
        display: block;
        <?else:?>
        margin: auto!important;
        <?endif?>
    }
    .pie{
        background-color: white;
        border: 1px solid #d4e1ea!important;
        width: 35em;
    }
    .pie .header-text{
        text-align: left;
        color: #003a63!important;
        font-size: 17px;

        height: 60px;
        line-height: 60px;
        padding: 0 10px;
        background: #f2faff!important;
        border-bottom: 1px solid #d4e1ea!important
    }
    .barchart{
        width: 72.2em;
    <?if($isPhantom):?>
        margin: 10em auto auto auto!important;
        zoom: 1.2; /* IE */
        -moz-transform: scale(1.2); /* Firefox */
        -o-transform: scale(1.2); /* Opera */
        -webkit-transform: scale(1.2); /* Safari And Chrome */
        transform: scale(1.2); /* Standard Property */
        border: 2px solid #d4e1ea!important;
        display: block;
    <?else:?>
        margin: 0.2em auto auto auto!important;
    <?endif?>

        position: relative;
        overflow: hidden;
        height: 450px;
    }
    .barchart img{
        position: absolute;
        margin: 1px;
        width: 72em;
    <?if(!$isPhantom):?>
        border: 1px solid #d4e1ea!important;
    <?endif?>
    }
    .ent-name{
        text-align: center;
        margin-top: 2em;
    }
    .ent-name span{
        display: inline-block;
        width: 20px;
        height: 20px;
        position: relative;
        top: 3px;
        <?if(Language::getCurrent()->direction == 'ltr'):?>
        left: -5px;
        <?else:?>
        right: -5px;
        <?endif?>
        border-radius: 20%;
    }
    .crafts-tbl{
        width: 42em;
        margin: 1em auto auto auto;
        border: 1px solid #ddd;
    }
    .crafts-tbl > tbody > tr > td{
        border-top: 2px solid #ddd!important;
        padding: 2px;
    }
    .crafts-tbl .ctbl-head-tbl{
        width: 100%;
    }

    .crafts-tbl th, .ctbl-head-tbl{
        text-align: center;
        color: #005c87;
        font-size: 15px;
        font-weight: normal;
        font-style: normal;
        font-family: "proxima_nova_rgregular", Arial, Helvetica, sans-serif;
        padding: 0!important;
    }
    .crafts-tbl .ctbl-head-th1{
        width: 75%;
    }
    .crafts-tbl .ctbl-head-th2{
        width: 25%;
    }
    .ctbl-head-tbl td {
        border-left: 1px solid #ddd;
    }
    .ctbl-h-td{
        border-top: 1px solid #ddd;
    }
    .linechart{
        width: 42em;
        margin: 1em auto;
        display: block;
        border: 1px solid #ddd;
    }
    .craft-stats{
        width: 42em;
        margin: 1em auto 0 auto;
        page-break-inside: avoid;
    }
    .craft-stats h3{
        margin: 0.5em auto;
    }

    .craft-stats table{
        border: 1px solid #ddd;
    }

    .craft-stats table td {
        text-align: center;
    }

    .craft-stats table tr td:first-child,
    .craft-stats table tr:first-child td{
        background-color: #9bc5e0 !important;
        color: #005c87 !important;
        -webkit-print-color-adjust: exact;
        text-align: left;
    }
    .craft-stats table tr:last-child td{
        background-color: #29cbf7 !important;
        -webkit-print-color-adjust: exact;
    }
    table.craft-stats > tbody > tr:nth-child(even) td:not(:first-child){
        background-color: #f2faff!important;
        -webkit-print-color-adjust: exact;
    }
    table.crafts-tbl > tbody > tr:nth-child(even) td{
        background-color: #f2faff!important;
        -webkit-print-color-adjust: exact;
    }
    .general-opinion{
        width: 42em;

    <?if($isPhantom):?>
        margin: 1em auto 10em auto;
    <?else:?>
        margin: 1em auto 0 auto;
    <?endif?>
        page-break-inside: avoid;
        page-break-after: always;
    }
    .general-opinion > h5 > span.circle {
        width: 20px;
        height: 20px;
        display: inline-block;
        background-color: #005c8a!important;
        border-radius: 50%;
        text-align: center;
        color: white;
        line-height: 17px;
        font-weight: bold;
        font-size: 18px;
        margin-right: 5px;
        margin-left: 5px;
        -webkit-print-color-adjust: exact;
    }
    .general-opinion > h5 {
        font-size: 14px;
        text-align: left;
        padding-left: 5px;
        padding-right: 5px;
        font-weight: bold;
        line-height: 30px;
        font-family: "proxima_nova_rgregular", Arial, Helvetica, sans-serif;
    }
    .general-opinion textarea{
        width: inherit;
        height: 25em;
        overflow: hidden;
    }
    .pies td:last-child .pie{
        margin-left: 1.5em
    }
    .pies td:last-child .pie{
        margin-left: 1.5em
    }
    .pies.he td:last-child .pie{
        margin-left: -1.5em!important;
    }
    @media print{
        @page { size: landscape}
    }
</style>

<table class="header <?if(!$isPhantom):?>no-print<?endif?>">
    <tr>
        <td class="logo"><img src="/<?=$report->getCompany()['logo']?>" alt="project images"></td>
        <td><ul<?if(!count($report->getObjects())):?> style="width: 100%"<?endif?>>
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
            <?endif;?></td>
        <td></td>
    </tr>
</table>

<table class="pies <?=Language::getCurrent()->iso2?>">
    <tr>
        <td>
            <table class="pie">
                <tr><td class="header-text"><?=__('Status statistics')?></td></tr>
                <tr>
                    <td>
                        <table class="chart">
                            <tr>
                                <td class="image">
                                    <div class="img-wrapp" id="p">
                                        <img class="piechart" src="<?=$images['piechart']?>" />
                                    </div>

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <ul class="stats">
                                        <li><?=__(Enum_QualityControlStatus::Existing).' + '.__(Enum_QualityControlStatus::Normal)?>: <?=$report->getStats()['total']['percents']['a']?>%</li>
                                        <li><?=__(Enum_QualityControlStatus::Existing).' + '.__(Enum_QualityControlStatus::Normal).' + '.__(Enum_QualityControlStatus::Repaired)?>: <?=$report->getStats()['total']['percents']['b']?>%</li>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td>
            <table class="pie">
                <tr><td class="header-text"><?=__('Status statistics (filtered)')?></td></tr>
                <tr>
                    <td>
                        <table class="chart">
                            <tr>
                                <td class="image">
                                    <div class="img-wrapp" id="p1">
                                        <img class="piechart" src="<?=$images['piechart2']?>" />
                                    </div>

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <ul class="stats">
                                        <li><?=__(Enum_QualityControlStatus::Existing).' + '.__(Enum_QualityControlStatus::Normal)?>: <?=$report->getStats()['filtered']['percents']['a']?>%</li>
                                        <li><?=__(Enum_QualityControlStatus::Existing).' + '.__(Enum_QualityControlStatus::Normal).' + '.__(Enum_QualityControlStatus::Repaired)?>: <?=$report->getStats()['filtered']['percents']['b']?>%</li>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="barchart">
<tr>
    <td>
        <div id="barchart">
            <img src="<?=$images['barchart']?>">
        </div>
    </td>
</tr>
</table>


<?foreach ($report->getProjectsORObjects($reportEntity) as $entity):?>
<h3 class="ent-name"><span style="background-color: <?=$stats[$entityType][$entity['id']]['color']?>!important;-webkit-print-color-adjust: exact;"></span><?=$entity['name']?></h3>
    <table class="table crafts-tbl">
        <thead>
        <tr>
            <th class="ctbl-head-th1"><?=__('Crafts List')?></th>
            <th class="ctbl-head-th2">
                <table class="ctbl-head-tbl">
                    <tr>
                        <td colspan="2"><?=__('Quantity')?></td>
                    </tr>
                    <tr>
                        <td class="ctbl-h-td" style="width: 50%"><?=__('Total')?></td>
                        <td class="ctbl-h-td"><?=__('Filtered')?></td>
                    </tr>
                </table>
            </th>
        </tr>
        </thead>
        <tbody>
        <?
        $craftStatTotal = 0;
        $craftStatFiltered = 0;
        $i = 0;
        ?>
        <?foreach ($report->getCompanyCrafts() as $craft):?>
            <?
            $i++;
            $craftStat['total'] = isset($stats[$reportEntity][$entity['id']]['crafts'][$craft['id']]['count']) ? $stats[$reportEntity][$entity['id']]['crafts'][$craft['id']]['count'] : 0;
            $craftStat['filtered'] = isset($stats[$reportEntity][$entity['id']]['filteredCrafts'][$craft['id']]['count']) ? $stats[$reportEntity][$entity['id']]['crafts'][$craft['id']]['count'] : 0;
            $craftStatTotal += $craftStat['total'];
            $craftStatFiltered += $craftStat['filtered'];
            ?>
            <?if($craftStat['total'] > 0):?>
                <tr>
                    <td data-th="<?=__('Specialty list')?>" style="padding: 0 0 0 8px!important;">
                        <span><?=$craft['name']?></span>
                    </td>
                    <td data-th="<?=__('Quantity')?>" class="enlarged" style="padding: 0!important;">
                        <table class="ctbl-head-tbl">
                            <tr>
                                <td style="line-height: 40px; width: 50%"><span class="report-status-quantity"><?=$craftStat['total']?></span></td>
                                <td style="line-height: 40px"><span class="report-status-quantity"><?=$craftStat['filtered']?></span></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            <?endif?>
            <!--        --><?//if($i > 4) break?>
        <?endforeach;?>
        </tbody>
    </table>
    <?if($report->canDisplayYearStats()):?>
    <img class="linechart" src="<?=$images['linechart'.$entity['id']]?>" >
    <?endif?>
    <div class="craft-stats">
        <h3><?=UTF8::ucfirst(__('general'))?></h3>
        <table class="table">
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
    </div>
    <div class="general-opinion">
        <h5><span class="circle">i</span><?=__('General opinion')?></h5>
        <textarea data-id="<?=$entity['id']?>"><?=$entity['generalOpinion']?></textarea>
    </div>
    <div class="q4-copyright q4-copyright-quality">
        <span>
            <?=__('Copyright © 2017 Q4B').'   '.__('All right reserved')?>
        </span>
    </div>
<?endforeach?>
<?if(Language::getCurrent()->direction == 'rtl'):?>
<?=$report->renderPieChartTotal('p')?>
<?=$report->renderPieChartFiltered('p1')?>
<?endif?>
<script>
    (function(){
        if(window.opener) {
            window.opener.csrf = document.querySelector(Q4U.options.csrfTokenSelector).content;
            window.print();
            setTimeout(function () {
                window.close();
            }, 1500);
        }
    })()
</script>
