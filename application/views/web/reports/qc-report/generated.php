<?defined('SYSPATH') or die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.05.2017
 * Time: 6:51
 */

$range = Arr::extract($_GET,["from","to"]);
?>
<?=$searchForm?>
<div id="generated-content" class="qc-report-redesign">
    <div class="qc_top_section">
        <div class=" qc_serche_section">
            <div class=" qc_top_right">
                <div class="qc_back_btn rotate-180">
                    <a href="#"><i class="q4bikon-arrow_back2 icon fs-22 generate-reports-bookmark-arrow"></i></a>
                </div>
                <div class="qc_report_title"><?=__('QC Report')?> </div>
                <div class="qc_report_date">(<?=$range['from']?>-<?=$range['to']?>)</div>
            </div>
            <div class="qc_top_left">
                <form class="qc-search-form" action="">
                    <i class="q4bikon-search1 icon"></i>
                    <input type="text" class="qc-id-to-show q4-form-input" placeholder="<?=__('Search'). ' ' . __('QC')?>..." value="">
                    <a data-url="<?=URL::site('reports/quality_control/')?>" class="qc_serarch_btn qc-id-submit disabled-input rotate-180">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="14" viewBox="0 0 20 14"
                             fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M20 7.00567C20 6.8388 19.9312 6.67942 19.8169 6.55942L13.63 0.310044C13.3856 0.063169 12.99 0.063794 12.7462 0.310044C12.5019 0.556294 12.5019 0.956294 12.7462 1.20254L17.8669 6.37442H0.625C0.28 6.37442 0 6.65692 0 7.00567C0 7.35442 0.28 7.63692 0.625 7.63692H17.8663L12.7462 12.8088C12.5019 13.055 12.5025 13.455 12.7462 13.7013C12.9906 13.9475 13.3863 13.9475 13.63 13.7013L19.8169 7.45192C19.9338 7.33379 19.9981 7.1713 20 7.00567Z"
                                  fill="#9FA2B4" />
                        </svg>
                    </a>
                </form>
            </div>
        </div>
        <?if (!count($qcs)): ?>
            <div class="report-status-list no-result">
                <img src="/media/img/no-report-result.png" alt="No result">

                <h2><?=__('No reports found')?></h2>
            </div>
        <?else: ?>
        <div class="qc_tabs_sec">
            <div class="qc_tabs_sec_tabs">
                <div class="qc_tab qc_tabs Statistics active" data-tab="tab_statistics"><?=__('Statistics')?></div>
                <div class="qc_tab qc_tabs Quality-controls" data-tab="tab_qc_controls"><?=__('Quality controls')?></div>
                <div class="qc_tab qc_tabs Info" data-tab="tab_info"><?=__('Info')?></div>
            </div>

            <?if(empty($del_rep_id)):?>
            <div class="qc_tabs_sec_btns">
                <div class="qc_tabs_sec_btns">
                    <div class="qc_tabs_sec_btn-export q4-page-export-new qc_tabs_btn">
                        <a href="<?='?'.Request::current()->getQueryString().'&export=1'?>">
                            <!--                        <i class="q4bikon-share icon-orange mr_10"></i>-->
                            <span class="q4-page-export-text"><?=__('Export')?></span>
                        </a>
                    </div>

                    <div class="send-reports qc_tabs_btn qc_tabs_sec_btn-send" data-url="<?=$sendReportsEmailUrl?>" data-id=<?=$_PROJECT->id?>>
<!--                        <i class="q4bikon-email2 icon-orange mr_10"></i>-->
                        <?=__('Send by email')?>
                    </div>
                    <div class="reports-to1-print-btn qc_tabs_btn qc_tabs_sec_btn-print">
<!--                        <i class="q4bikon-printer icon-orange mr_10"></i>-->
                        <?=__('Print')?>
                    </div>
                </div>
            </div>
            <?endif?>
        </div>
        <?endif;?>
    </div>
    <?if (count($qcs)): ?>
        <?=View::make($_VIEWPATH.'tab-statistics',
            [
                'crafts' => $crafts,
                'craftsParams' => $craftsParams,
                'filteredCraftsParams' => $filteredCraftsParams,
                'craftsList' => $craftsList,
                'filteredCraftsList' => $filteredCraftsList,
                'craftName' => $qcs[0]->craft->name,
                'qcsCount' => count($qcs),
                'del_rep_id' => $del_rep_id
            ])
        ?>
        <?=View::make($_VIEWPATH.'tab-qc-controls',
            [
                'qcs' => $qcs,
                'tasks' => $tasks,
                'pagination' =>  $pagination
            ])
        ?>
        <?=View::make($_VIEWPATH.'tab-info',
            [
                'rangeFrom' => $range['from'],
                'rangeTo' => $range['to']
            ])
        ?>
    <?endif;?>
</div>
