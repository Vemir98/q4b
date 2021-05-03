<?defined('SYSPATH') or die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.05.2017
 * Time: 6:51
 */
use Helpers\ReportsHelper;
if($_APPROVAL_STATUS) return;
?>
<?

?>

<div class="report-status-results">
    <div class="report-status-results_tabels">
        <div class="report-status-result-unit QC-statistics">
            <div class="QC-statistics_title"><?=__('QC statistics')?></div>
            <table class="table">
                <thead>
                <tr>
                    <th><?=__('Status list')?></th>
                    <th>
                        <div class="double-cpt">
                            <div class="double-sub-cpt1 fs-14"><?=__('Quantity')?></div>
                            <div class="double-sub-cpt2">
                                <span class="omission-points"><?=__('Total')?></span>
                                <span class="omission-points"><?=__('Filtered')?></span>
                            </div>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td data-th="<?=__('Status List')?>"><span class="report-status-link" data-toggle="modal" data-target="#modal-report-status-crafts"><?=__(Enum_QualityControlStatus::Existing)?></span></td>
                    <td data-th="<?=__('Quantity')?>" class="enlarged">
                        <div class="double-cell-cpt">
                            <div class="double-cell-cpt1">
                                <span class="report-status-quantity"><?=$craftsParams['statuses'][Enum_QualityControlStatus::Existing]?></span>
                            </div>
                            <div class="double-cell-cpt2">
                                <span class="report-status-quantity"><?=$filteredCraftsParams['statuses'][Enum_QualityControlStatus::Existing] ? : 0?></span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td data-th="<?=__('Status List')?>"><span class="report-status-link" data-toggle="modal" data-target="#modal-report-status-crafts"><?=__(Enum_QualityControlStatus::Normal)?></span></td>
                    <td data-th="<?=__('Quantity')?>" class="enlarged">
                        <div class="double-cell-cpt">
                            <div class="double-cell-cpt1">
                                <span class="report-status-quantity"><?=$craftsParams['statuses'][Enum_QualityControlStatus::Normal]?></span>
                            </div>
                            <div class="double-cell-cpt2">
                                <span class="report-status-quantity"><?=$filteredCraftsParams['statuses'][Enum_QualityControlStatus::Normal] ? : 0?></span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td data-th="<?=__('Status List')?>"><span class="report-status-link" data-toggle="modal" data-target="#modal-report-status-crafts"><?=__(Enum_QualityControlStatus::Repaired)?></span></td>
                    <td data-th="<?=__('Quantity')?>" class="enlarged">
                        <div class="double-cell-cpt">
                            <div class="double-cell-cpt1">
                                <span class="report-status-quantity"><?=$craftsParams['statuses'][Enum_QualityControlStatus::Repaired]?></span>
                            </div>
                            <div class="double-cell-cpt2">
                                <span class="report-status-quantity"><?=$filteredCraftsParams['statuses'][Enum_QualityControlStatus::Repaired] ? : 0?></span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td data-th="<?=__('Status List')?>"><span class="report-status-link" data-toggle="modal" data-target="#modal-report-status-crafts"><?=__(Enum_QualityControlStatus::Invalid)?></span></td>
                    <td data-th="<?=__('Quantity')?>" class="enlarged">
                        <div class="double-cell-cpt">
                            <div class="double-cell-cpt1">
                                <span class="report-status-quantity"><?=$craftsParams['statuses'][Enum_QualityControlStatus::Invalid]?></span>
                            </div>
                            <div class="double-cell-cpt2">
                                <span class="report-status-quantity"><?=$filteredCraftsParams['statuses'][Enum_QualityControlStatus::Invalid] ? : 0?></span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="dark-row">
                    <td data-th="<?=__('Status List')?>"><span class="report-status-link total" data-toggle="modal" data-target="#modal-report-crafts"><?=__("Total_sum")?></span></td>
                    <td data-th="<?=__('Quantity')?>" class="enlarged">
                        <div class="double-cell-cpt">
                            <div class="double-cell-cpt1">
                                    <span class="report-status-quantity">
                                        <?= ReportsHelper::getTotalExcept($craftsParams['statuses'], $craftsParams['statuses'][Enum_QualityControlStatus::Existing.' && '.Enum_QualityControlApproveStatus::ForRepair]) ?>
                                    </span>
                            </div>
                            <div class="double-cell-cpt2">
                                    <span class="report-status-quantity">
                                        <?= ReportsHelper::getTotalExcept($filteredCraftsParams['statuses'], $filteredCraftsParams['statuses'][Enum_QualityControlStatus::Existing.' && '.Enum_QualityControlApproveStatus::ForRepair]) ?>
                                    </span>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="report-status-result-multiple Specialties-list">
            <div class="Specialties-list_title"><?=__('Crafts List')?></div>
            <?if (count($crafts) > 1): ?>
                <table class="table scrollable-tbody-content">
                    <thead>
                    <tr>
                        <th><?=__('Crafts List')?></th>
                        <th>
                            <div class="double-cpt">
                                <div class="double-sub-cpt1 fs-14"><?=__('Quantity')?></div>
                                <div class="double-sub-cpt2">
                                    <span class="omission-points"><?=__('Total')?></span>
                                    <span class="omission-points"><?=__('Filtered')?></span>
                                </div>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody class="report-status-result-scroll qc-vertical-scrll">
                    <?foreach ($craftsList as $id=>$craft): ?>
                        <tr>
                            <td data-th="<?=__('Crafts List')?>">
                            <span class="report-status-link" data-toggle="modal"
                                  data-target="#modal-report-status-crafts"><?=isset($craft['name']) ? __($craft['name']) : ''?>
                            </span>
                            </td>
                            <td data-th="<?=__('Quantity')?>" class="enlarged">
                                <div class="double-cell-cpt">
                                    <div class="double-cell-cpt1">
                                        <span class="report-status-quantity"><?=isset($craft['count']) ? $craft['count'] : 0?></span>
                                    </div>
                                    <div class="double-cell-cpt2">
                                        <span class="report-status-quantity"><?=isset($filteredCraftsList[$id]['count']) ? $filteredCraftsList[$id]['count'] : 0?></span>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    <?endforeach;?>
                    </tbody>
                </table>
            <?endif?>
        </div>
    </div>
    <div class="f0">
        <?if (count($crafts)): ?>
            <div class="report-status-result-unit">
                <div class="report-status-pie">
                    <div class="report-status-pie-top">
                        <h3><?=__('Status statistics')?></h3>
                    </div>
                    <div class="report-status-pie-bottom">
                        <div style="margin: 0 auto;">
                            <div class="report-status-pie-chart">
                                <div id="piechart" class="piechart"></div>
                            </div>
                            <div class="report-status-pie-statistics">
                                <ul id="report-chart-list">
                                    <ul id="report-chart-list">
                                        <li data-percent="<?=$craftsParams['percents'][Enum_QualityControlStatus::Existing]?>" data-name="Existing"><span class="report-chart-color green"></span><span class="report-chart-text"> <?=__('existing')?> (<?=$craftsParams['percents'][Enum_QualityControlStatus::Existing]?>%) &#x200E;</span></li>
                                        <li data-percent="<?=$craftsParams['percents'][Enum_QualityControlStatus::Existing.' && '.Enum_QualityControlApproveStatus::ForRepair]?>" data-name="Existing & Repairing"><span class="report-chart-color purple"></span><span class="report-chart-text"> <?=__('existing && for_repair')?> (<?=$craftsParams['percents'][Enum_QualityControlStatus::Existing.' && '.Enum_QualityControlApproveStatus::ForRepair]?>%) &#x200E;</span></li>
                                        <li data-percent="<?=$craftsParams['percents'][Enum_QualityControlStatus::Normal]?>" data-name="Normal"><span class="report-chart-color blue"></span><span class="report-chart-text"> <?=__('normal')?> (<?=$craftsParams['percents'][Enum_QualityControlStatus::Normal]?>%) &#x200E;</span></li>
                                        <li data-percent="<?=$craftsParams['percents'][Enum_QualityControlStatus::Repaired]?>" data-name="Repaired"><span class="report-chart-color orange"></span><span class="report-chart-text"> <?=__('repaired')?> (<?=$craftsParams['percents'][Enum_QualityControlStatus::Repaired]?>%) &#x200E;</span></li>
                                        <li data-percent="<?=$craftsParams['percents'][Enum_QualityControlStatus::Invalid]?>" data-name="Invalid"><span class="report-chart-color red"></span><span class="report-chart-text"> <?=__('invalid')?> (<?=$craftsParams['percents'][Enum_QualityControlStatus::Invalid]?>%) &#x200E;</span></li>
                                    </ul>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="report_status_valies">
                        <?
                        $sum = 100;
                        $inv = $sum - $craftsParams['percents'][Enum_QualityControlStatus::Repaired]-$craftsParams['percents'][Enum_QualityControlStatus::Invalid];
                        $other = $sum - $craftsParams['percents'][Enum_QualityControlStatus::Invalid];
                        $fixed = round($craftsParams['statuses'][Enum_QualityControlStatus::Repaired] * 100 / ($craftsParams['statuses'][Enum_QualityControlStatus::Invalid] + $craftsParams['statuses'][Enum_QualityControlStatus::Repaired]));
                        ?>
                        <div class="report_status_valies_item">
                            <span class="report_status_v_headline"><?=__('existing')?> + <?=__('normal')?> : </span>
                            <span class="report_status_v_value"><?=$inv?>%</span>
                        </div>
                        <div class="report_status_valies_item">
                            <span class="report_status_v_headline"><?=__('existing')?> + <?=__('normal')?> + <?=__('repaired')?> : </span>
                            <span class="report_status_v_value"><?=$other?>%</span>
                        </div>
                        <div class="report_status_valies_item">
                            <span class="report_status_v_headline"><?=__('Fixed')?> : </span>
                            <span class="report_status_v_value"><?=$fixed?>%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="report-status-result-unit ">
                <div class="report-status-pie">
                    <div class="report-status-pie-top">
                        <h3><?=__('Status statistics (filtered)')?></h3>
                    </div>
                    <div class="report-status-pie-bottom">
                        <div style="margin: 0 auto;">
                            <div class="report-status-pie-chart">
                                <div id="piechart2" class="piechart"></div>
                            </div>

                            <div class="report-status-pie-statistics">
                                <ul id="report-chart-list2">
                                    <li data-percent="<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Existing]?>" data-name="Existing"><span class="report-chart-color green"></span><span class="report-chart-text"> <?=__('existing')?> (<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Existing]?>%) &#x200E;</span></li>
                                    <li data-percent="<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Existing.' && '.Enum_QualityControlApproveStatus::ForRepair]?>" data-name="Existing & Repairing"><span class="report-chart-color purple"></span><span class="report-chart-text"> <?=__('existing && for_repair')?> (<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Existing.' && '.Enum_QualityControlApproveStatus::ForRepair]?>%) &#x200E;</span></li>
                                    <li data-percent="<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Normal]?>" data-name="Normal"><span class="report-chart-color blue"></span><span class="report-chart-text"> <?=__('normal')?> (<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Normal]?>%) &#x200E;</span></li>
                                    <li data-percent="<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Repaired]?>" data-name="Repaired"><span class="report-chart-color orange"></span><span class="report-chart-text"> <?=__('repaired')?> (<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Repaired]?>%) &#x200E;</span></li>
                                    <li data-percent="<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Invalid]?>" data-name="Invalid"><span class="report-chart-color red"></span><span class="report-chart-text"> <?=__('invalid')?> (<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Invalid]?>%) &#x200E;</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="report_status_valies">
                        <?
                        $sum = 100;
                        $inv = $sum - $filteredCraftsParams['percents'][Enum_QualityControlStatus::Repaired]-$filteredCraftsParams['percents'][Enum_QualityControlStatus::Invalid];
                        $other = $sum - $filteredCraftsParams['percents'][Enum_QualityControlStatus::Invalid];
                        $fixed = ($filteredCraftsParams['statuses'][Enum_QualityControlStatus::Invalid] + $filteredCraftsParams['statuses'][Enum_QualityControlStatus::Repaired]) ? round($filteredCraftsParams['statuses'][Enum_QualityControlStatus::Repaired] * 100 / ($filteredCraftsParams['statuses'][Enum_QualityControlStatus::Invalid] + $filteredCraftsParams['statuses'][Enum_QualityControlStatus::Repaired])) : 0;
                        ?>
                        <div class="report_status_valies_item">
                            <span class="report_status_v_headline"><?=__('existing')?> + <?=__('normal')?> : </span>
                            <span class="report_status_v_value"><?=$inv?>%</span>
                        </div>

                        <div class="report_status_valies_item">
                            <span class="report_status_v_headline"><?=__('existing')?> + <?=__('normal')?> + <?=__('repaired')?> : </span>
                            <span class="report_status_v_value"><?=$other?>%</span>
                        </div>
                        <div class="report_status_valies_item">
                            <span class="report_status_v_headline"><?=__('Fixed')?> : </span>
                            <span class="report_status_v_value"><?=$fixed?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        <?else: ?>
        <?endif?>
        <!--.f0-->
    </div>
</div>

