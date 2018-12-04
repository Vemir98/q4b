<?defined('SYSPATH') or die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.05.2017
 * Time: 6:51
 */


?>
<?

// echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($filteredCraftsParams); echo "</pre>"; exit;
?>
    <div class="report-status-list double-result">
        <div class="report-status-results">
        <h3><?=count($crafts) == 1 ? __('Craft') . ':' . $craftName : ''?></h3>

<?if(false):?>
            <div >

            <div class="report-status-result-unit">
                <table class="table">
                    <thead>
                    <tr>
                        <th><?=__('Statuses of approval')?></th>
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
                    <tbody>
                    <tr>
                        <td data-th="<?=__('Crafts')?>"><span class="report-status-link" data-toggle="modal" data-target="#modal-report-crafts"><?=__(Enum_QualityControlApproveStatus::Waiting)?></span></td>
                        <td data-th="<?=__('Quantity')?>" class="enlarged">
                            <div class="double-cell-cpt">
                                <div class="double-cell-cpt1">
                                    <span class="report-status-quantity"><?=$craftsParams['mngrApprovalStatuses'][Enum_QualityControlApproveStatus::Waiting]?></span>
                                </div>
                                <div class="double-cell-cpt2">
                                   <span class="report-status-quantity"><?=$filteredCraftsParams['mngrApprovalStatuses'][Enum_QualityControlApproveStatus::Waiting] ? :0?></span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td data-th="<?=__('Crafts')?>"><span class="report-status-link" data-toggle="modal" data-target="#modal-report-crafts"><?=__(Enum_QualityControlApproveStatus::ForRepair)?></span></td>
                        <td data-th="<?=__('Quantity')?>" class="enlarged">
                            <div class="double-cell-cpt">
                                <div class="double-cell-cpt1">
                                    <span class="report-status-quantity"><?=$craftsParams['mngrApprovalStatuses'][Enum_QualityControlApproveStatus::ForRepair]?></span>
                                </div>
                                <div class="double-cell-cpt2">
                                    <span class="report-status-quantity"><?=$filteredCraftsParams['mngrApprovalStatuses'][Enum_QualityControlApproveStatus::ForRepair] ? :0?></span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td data-th="<?=__('Crafts')?>"><span class="report-status-link" data-toggle="modal" data-target="#modal-report-crafts"><?=__(Enum_QualityControlApproveStatus::Approved)?></span></td>
                        <td data-th="<?=__('Quantity')?>" class="enlarged">
                            <div class="double-cell-cpt">
                                <div class="double-cell-cpt1">
                                    <span class="report-status-quantity"><?=$craftsParams['mngrApprovalStatuses'][Enum_QualityControlApproveStatus::Approved]?></span>
                                </div>
                                <div class="double-cell-cpt2">
                                    <span class="report-status-quantity"><?=$filteredCraftsParams['mngrApprovalStatuses'][Enum_QualityControlApproveStatus::Approved] ? :0?></span>
                                </div>
                            </div>
                        </td>

                    </tr>
                    <tr class="dark-row">
                        <td data-th="<?=__('Crafts')?>"><span class="report-status-link total" data-toggle="modal" data-target="#modal-report-crafts"><?=__("Total_sum")?></span></td>
                        <td data-th="<?=__('Quantity')?>" class="enlarged">
                            <div class="double-cell-cpt">
                                <div class="double-cell-cpt1">
                                    <span class="report-status-quantity"><?=array_sum($craftsParams['mngrApprovalStatuses'])?></span>
                                </div>
                                <div class="double-cell-cpt2">
                                    <span class="report-status-quantity"><?=array_sum($filteredCraftsParams['mngrApprovalStatuses']) ? :0?></span>
                                </div>
                            </div>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>

            </div>
<?endif?>


                <div class="report-status-result-unit">
                    <table class="table">
                        <thead>
                        <tr>
                            <th><?=__('Status list')?></th>
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

                            <tr class="dark-row" style="background: #9bc5e0;">
                                <td data-th="<?=__('Status List')?>"><span class="report-status-link total" data-toggle="modal" data-target="#modal-report-crafts"><?=__("Total_sum")?></span></td>
                                <td data-th="<?=__('Quantity')?>" class="enlarged">
                                    <div class="double-cell-cpt">
                                        <div class="double-cell-cpt1">
                                            <span class="report-status-quantity"><?=array_sum($craftsParams['statuses'])?></span>
                                        </div>
                                        <div class="double-cell-cpt2">
                                            <span class="report-status-quantity"><?=array_sum($filteredCraftsParams['statuses']) ? :0?></span>
                                        </div>
                                    </div>
                                </td>

                            </tr>

                        </tbody>
                    </table>
                </div>
            <div class="f0">
                <?if (count($crafts)): ?>

                    <div class="report-status-result-unit">
                        <div class="report-status-pie">
                            <div class="report-status-pie-top">
                                <h3><?=__('Status statistics')?></h3>
                            </div>

                            <div class="report-status-pie-bottom">
                                <div class="report-status-pie-chart">
                                    <div id="piechart" class="piechart"></div>
                                </div>

                                <div class="report-status-pie-statistics">
                                    <ul id="report-chart-list">
                                        <li data-percent="<?=$craftsParams['percents'][Enum_QualityControlStatus::Existing]?>" data-name="Existing"><span class="report-chart-color green"></span><span class="report-chart-text"> <?=__('existing')?> (<?=$craftsParams['percents'][Enum_QualityControlStatus::Existing]?>%) &#x200E;</span></li>
                                        <li data-percent="<?=$craftsParams['percents'][Enum_QualityControlStatus::Normal]?>" data-name="Normal"><span class="report-chart-color blue"></span><span class="report-chart-text"> <?=__('normal')?> (<?=$craftsParams['percents'][Enum_QualityControlStatus::Normal]?>%) &#x200E;</span></li>
                                        <li data-percent="<?=$craftsParams['percents'][Enum_QualityControlStatus::Repaired]?>" data-name="Repaired"><span class="report-chart-color orange"></span><span class="report-chart-text"> <?=__('repaired')?> (<?=$craftsParams['percents'][Enum_QualityControlStatus::Repaired]?>%) &#x200E;</span></li>
                                        <li data-percent="<?=$craftsParams['percents'][Enum_QualityControlStatus::Invalid]?>" data-name="Invalid"><span class="report-chart-color red"></span><span class="report-chart-text"> <?=__('invalid')?> (<?=$craftsParams['percents'][Enum_QualityControlStatus::Invalid]?>%) &#x200E;</span></li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>



                    <div class="report-status-result-unit">
                        <div class="report-status-pie">
                            <div class="report-status-pie-top">
                                <h3><?=__('Status statistics (filtered)')?></h3>
                            </div>

                            <div class="report-status-pie-bottom">
                                <div class="report-status-pie-chart">
                                    <div id="piechart2" class="piechart"></div>
                                </div>

                                <div class="report-status-pie-statistics">
                                    <ul id="report-chart-list2">
                                        <li data-percent="<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Existing]?>" data-name="Existing"><span class="report-chart-color green"></span><span class="report-chart-text"> <?=__('existing')?> (<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Existing]?>%) &#x200E;</span></li>
                                        <li data-percent="<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Normal]?>" data-name="Normal"><span class="report-chart-color blue"></span><span class="report-chart-text"> <?=__('normal')?> (<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Normal]?>%) &#x200E;</span></li>
                                        <li data-percent="<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Repaired]?>" data-name="Repaired"><span class="report-chart-color orange"></span><span class="report-chart-text"> <?=__('repaired')?> (<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Repaired]?>%) &#x200E;</span></li>
                                        <li data-percent="<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Invalid]?>" data-name="Invalid"><span class="report-chart-color red"></span><span class="report-chart-text"> <?=__('invalid')?> (<?=$filteredCraftsParams['percents'][Enum_QualityControlStatus::Invalid]?>%) &#x200E;</span></li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>

                <?else: ?>
                <?endif?>
                <div class="report-status-result-multiple">
                    <?if (count($crafts) > 1): ?>
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
                                <?foreach ($craftsList as $id=>$craft): ?>
                                    <tr>
                                        <td data-th="<?=__('Crafts List')?>">
                                            <span class="report-status-link" data-toggle="modal" data-target="#modal-report-status-crafts"><?=isset($craft['name']) ? __($craft['name']) : ''?></span>
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
                    <?endif;?>
                </div>
            </div><!--.f0-->
        </div>
    </div>

