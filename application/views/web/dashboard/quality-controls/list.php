<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 31.05.2017
 * Time: 15:28
 */

// echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($data); echo "</pre>"; exit;
$statusArray = [
     "All" => "symbol-all",
     "waiting" => "symbol-active",
     "for_repair" => "symbol-inactive",
     "approved" => "symbol-archive",
];
?>

    <div class="panel_header open">
        <span class="sign"><i class="panel_header_icon q4bikon-minus"></i></span><h2><?=__('Quality control')?></h2>
    </div>

    <div class="panel_content open">

            <div class="panel_body container-fluid">
                <div class="row">
                    <div class="col-lg-12 rtl-float-right">
                        <div class="add-new-row-double-2">
                            <div class="q4-inside-filter">
                                <?if(!empty($data['statuses'])):?>
                                    <div class="filter-status-text""><?=__('Filter by')?> <?=__('status')?>:</div>
                                    <ul class="inside-filters-list">
                                        <?foreach ($data['statuses'] as $key => $status):?>
                                            <li>
                                                <?$active = $status['text'] == 'waiting' ? ' active': ''?>
                                                <a data-url="<?=$status['url']?>" data-status="<?=$status['text']?>" class="inside-filter-button filter-settings-button<?=$active?>">
                                                    <span class="<?=$statusArray[$status['text']]?> status"></span>
                                                    <span class="filter-button-text"><?=__(strtolower($status['text']))?>
                                                    </span>
                                                    <span class="filter-button-numb">(<?=$status['count']?>)&#x200E;</span>
                                                </a>
                                            </li>
                                        <?endforeach?>
                                            <li>
                                               <input type="text" class="q4-form-input inlined qc-id-to-show" value="">
                                                <a data-url="<?=URL::site('reports/quality_control/')?>" class="inline-block-btn-small dark_blue_button qc-id-submit disabled-input"><?=__('Show QC')?>

                                                </a>
                                            </li>

                                            <li>
                                                <a
                                                href="<?=$data['statuses']['Waiting']['url'].'?export_qc_list=1'?>"
                                                 class="q4-page-export">
                                                <i class="q4bikon-export icon-export"></i>
                                                <span class="q4-page-export-text"><?=__('Export')?></span>
                                                </a>
                                            </li>
                                    </ul>
                                <?endif?>

                            </div>

                        </div>
                    </div>

                </div>
                <?if($data['total_items']):?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="scrollable-table">
                            <table class="rwd-table responsive_table table" data-toggle="table">
                                <thead>
                                <tr>
                                    <th data-field="ID"  class="td-25"><?=__('ID')?></th>
                                    <th  data-field="Quality Control Forms"  class="td-125" ><?=__('Quality control')?></th>
                                    <th data-field="Property" class="td-300"><?=__('Property')?></th>
                                    <th data-field="Floor"  class="td-25"><?=__('Floor')?></th>
                                    <th data-field="Element" class="td-200" ><?=__('Element')?></th>
                                    <th data-field="Number"  class="td-25"><?=__('Element number')?></th>
                                    <th  data-field="Crafts"  class="td-350"><?=__('Crafts')?></th>
                                    <th  data-field="Status"  class="td-125"><?=__('Status')?></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?foreach ($data['items'] as $i):?>
                                        <tr>
                                            <td class="rwd-td0 qc-sort" data-th="<?=__('ID')?>">
                                                <div class="div-cell"><?=$i->id?></div>
                                            </td>
                                            <td class="rwd-td1 qc-sort" data-th="<?=__('Quality control')?>">
                                                <div class="div-cell reports-prop-title">
                                                    <a data-modalid="quality-control-modal" href="#" data-qc="quality-control" data-url="<?=URL::site('reports/quality_control/'.$i->id)?>"><?=__('Quality control')?>
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="rwd-td2 qc-sort" data-th="<?=__('Property')?>">
                                                <div class="div-cell"><?=__($i->object->type->name)?> - <?=$i->object->name?></div>
                                            </td>
                                            <td class="rwd-td3 qc-sort" data-th="<?=__('Floor')?>">
                                                <div class="div-cell"><span class="bidi-override"><?=$i->floor->number?>&#x200E;</span></div>
                                            </td>
                                            <td class="rwd-td4 qc-sort" data-th="<?=__('Element')?>">
                                                <div class="div-cell"><?=__($i->place->name)?></div>
                                            </td>
                                            <td class="rwd-td5 qc-sort" data-th="<?=__('Number')?>">
                                                <div class="div-cell"><span class="bidi-override"><?=$i->place->number?></span></div>
                                            </td>
                                            <td class="rwd-td6 qc-sort" data-th="<?=__('Crafts')?>">
                                                <div class="div-cell"><?=__($i->craft->name)?></div>
                                            </td>
                                            <td class="rwd-td7 qc-sort" data-th="<?=__('Status')?>">
                                                <div class="div-cell"><span class="q4-status-<?=$i->approval_status?>"><?=__($i->approval_status)?></span></div>
                                            </td>
                                        </tr>
                                    <?endforeach;?>

                                </tbody>
                            </table>

                        </div><!--scrollable table-->

                        <?=$data['pagination']?>
                    </div>
                </div>
                <?else:?>
                    <h5 class="no-records-found"><?=__('Not found')?></h5>
                <?endif;?>

            </div><!--.panel-body-->

    </div><!--panel_content-->
