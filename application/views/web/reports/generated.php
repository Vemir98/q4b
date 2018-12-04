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



<div id="generated-content">
    <div class="generate-reports-bookmark">
        <div class="generate-reports-bookmark-title">
            <h2><?=__('Generated Reports')?></h2>
        </div>
        <div class="generate-reports-bookmark-arrow">
            <i class="q4bikon-arrow_bottom"></i>
        </div>
    </div>
    <?if (!count($qcs)): ?>
        <div class="report-status-list no-result">
            <img src="/media/img/no-report-result.png" alt="No result">

            <h2><?=__('No reports found')?></h2>
        </div>
    <?else: ?>
        <div class="report-send-out">
            <div class="row">
                <div class="col-md-12">
                    <div class="show-qc-filter">
                        <input type="text" class="q4-form-input inlined zerofied qc-id-to-show" value="">
                        <a data-url="<?=URL::site('reports/quality_control/')?>" class="send-out-button blue qc-id-submit disabled-input">
                            <?=__('Show QC')?>
                        </a>
                    </div>
                    <a href="<?='?'.Request::current()->getQueryString().'&export=1'?>" class="q4-page-export">
                        <i class="q4bikon-export icon-export"></i>
                        <span class="q4-page-export-text"><?=__('Export')?></span>
                    </a>
                    <span class="send-out-button send-reports" data-url="<?=$sendReportsEmailUrl?>" data-id=<?=$_PROJECT->id?>><i class="q4bikon-email"></i> <?=__('Send by email')?></span>
                    <span class="send-out-button reports-to1-print-btn"><i class="q4bikon-print"></i> <?=__('Print')?></span>

                </div>
            </div>
        </div>
        <?if ($pagination->current_page == 1): ?>
            <div class="report-project-desc f0">
                <div class="report-project-desc-image">
                    <img src="/<?=$_COMPANY->logo?>" alt="project images">
                </div>
                <div class="report-project-desc-list">
                    <ul>
                        <li>
                            <span class="light-blue">
                                <i class="icon q4bikon-companies"></i>
                                <?=__('Company name')?>:
                            </span>
                            <span class="dark-blue">
                                <?=$_COMPANY->name?>
                            </span>
                        </li>
                        <li>
                            <span class="light-blue">
                                <i class="icon q4bikon-project"></i>
                                <?=__('Project name')?>:
                            </span>
                            <span class="dark-blue">
                                <?=$_PROJECT->name?>
                            </span>
                        </li>
                        <li>
                            <span class="light-blue">
                                <i class="q4bikon-username"></i>
                                <?=__('Owner')?>:
                            </span>
                            <span class="dark-blue">
                                <?=$_PROJECT->owner?>&#x200E;
                            </span>
                        </li>
                        <li>
                            <span class="light-blue">
                                <i class="q4bikon-date"></i>
                                <?=__('Start Date')?>:
                            </span>
                            <span class="dark-blue">
                                <?=date('d/m/Y', $_PROJECT->start_date)?>
                            </span>
                        </li>
                        <li>
                            <span class="light-blue">
                                <i class="q4bikon-date"></i>
                                <?=__('End Date')?>:
                            </span>
                            <span class="dark-blue">
                                <?=date('d/m/Y', $_PROJECT->end_date)?>
                            </span>
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <span class="light-blue">
                                <i class="q4bikon-company_id"></i>
                                <?=__('Project ID')?>:
                            </span>
                            <span class="dark-blue">
                                <?=$_PROJECT->id?>
                            </span>
                        </li>
                        <li>
                            <span class="light-blue">
                                <i class="q4bikon-company_status"></i>
                                <?=__('Project Status')?>:
                            </span>
                            <span class="dark-blue">
                                <?=__($_PROJECT->status)?>
                            </span>
                        </li>
                        <li>
                            <span class="light-blue">
                                <i class="q4bikon-address"></i>
                                <?=__('Address')?>:
                            </span>
                            <span class="dark-blue">
                                <?=$_PROJECT->address?>
                            </span>
                        </li>
                        <li>
                            <span class="light-blue">
                                <i class="q4bikon-uncheked"></i>
                                <?=__('Quantity of properties')?>:
                            </span>
                            <span class="dark-blue">
                                <?=$_PROJECT->objects->count_all()?>
                            </span>
                        </li>
                        <li>
                            <span class="light-blue range-key">
                                <i class="q4bikon-date"></i>
                                <?=__('Report Range')?>:
                            </span>
                            <span class="dark-blue range-val">
                                <span><?=$range['from']?>-<?=$range['to']?></span>
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="clear"></div>


                <div class="report-project-desc-text">
                    <p>
                        <span class="report-project-desc-intro"><?=__('Project Description')?>:</span> <?=$_PROJECT->description?>
                    </p>
                </div>

            </div>


            <?=View::make($_VIEWPATH.'statistics',
            [
                'crafts' => $crafts,
                'craftsParams' => $craftsParams,
                'filteredCraftsParams' => $filteredCraftsParams,
                'craftsList' => $craftsList,
                'filteredCraftsList' => $filteredCraftsList,
                'craftName' => $qcs[0]->craft->name,
            ])?>

        <?endif;?>
        <?foreach ($qcs as $q): ?>

            <?=View::make($_VIEWPATH.'list-item',
            [
                'q' => $q,
            ])?>


        <?endforeach?>
        <?=$pagination?>

    <?endif?>

</div>
