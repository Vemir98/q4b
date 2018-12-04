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
        <form action="/" class="q4_form" autocomplete="off">
            <div class="panel_body container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="q4-inside-filter-mobile">
                            <?if(!empty($data['statuses'])):?>
                                <div class="filter-status-text"><?=__('Filter by')?> <?=__('status')?>:</div>
                                <div class="relative">
                                    <a class="q4-inside-select-filter">
                                        <span class="status symbol-active"></span>
                                        <span class="filter-button-text"><?=__('Waiting')?> <span class="filter-button-numb">(<?=$data['statuses']['Waiting']['count']?>)&#x200E;</span></span>
                                    </a>
                                    <ul class="inside-filters-list-mobile">
                                        <?foreach ($data['statuses'] as $class => $status):?>
                                            <?$url = $status == 'All' ? '': strtolower($class);
                                            ?>
                                            <li>
                                                <a href="#" data-url="<?=$status['url']?>" data-status="<?=$status['text']?>" class="inside-filter-button-mobile filter-settings-button active">
                                                    <span class="status <?=$statusArray[$status['text']]?>"></span>
                                                    <span class="filter-button-text"><?=__(strtolower($status['text']))?><span class="filter-button-numb">(<?=$status['count']?>)&#x200E;</span></span>
                                                </a>
                                            </li>
                                        <?endforeach;?>

                                    </ul>
                                    <input type="text" class="q4-form-input inlined qc-id-to-show" value="">
                                    <a data-url="<?=URL::site('reports/quality_control/')?>" class="inline-block-btn-small dark_blue_button inlined qc-id-submit disabled-input"><?=__('Show QC')?>

                                    </a>

                                    <a href="<?=$data['statuses']['Waiting']['url'].'?export_qc_list=1'?>"
                                                 class="q4-page-export inlined">
                                        <i class="q4bikon-export icon-export"></i>
                                        <span class="q4-page-export-text"><?=__('Export')?></span>
                                    </a>

                                </div>
                            <?endif;?>
                        </div>
                    </div>
                </div>
                <?if($data['total_items']):?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="q4-carousel-table-wrap">
                            <div class="q4-carousel-table" data-structurecount="<?=count($data['items'])?>">
                                <?foreach ($data['items'] as $i):?>
                                    <div class="item">
                                        <div class="q4-carousel-blue-head reports-prop-title">
                                            <span class="blue-head-title"><?=__('Quality control').' #'.$i->id?></span>
                                            <div class="blue-head-option">
                                                <a class="show-structure-mobile show-structure-layout" data-modalid="quality-control-modal" href="#" data-qc="quality-control" data-url="<?=URL::site('reports/quality_control/'.$i->id)?>">
                                                    <i class="plus q4bikon-preview"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="q4-carousel-row f0">
                                            <div class="q4-mobile-table-key">
                                               <?=__('ID')?>
                                            </div>
                                            <div class="q4-mobile-table-value">
                                                <?=$i->id?>
                                            </div>
                                        </div>
                                        <div class="q4-carousel-row f0" >
                                            <div class="q4-mobile-table-key">
                                                <?=__('Property')?>
                                            </div>
                                            <div class="q4-mobile-table-value">
                                                <?=__($i->object->type->name)?> - <?=$i->object->name?>
                                            </div>
                                        </div>
                                        <div class="q4-carousel-row f0">
                                            <div class="q4-mobile-table-key">
                                                <?=__('Floor')?>
                                            </div>
                                            <div class="q4-mobile-table-value">
                                               <span class="bidi-override"><?=$i->floor->number?>&#x200E;</span>
                                            </div>
                                        </div>
                                        <div class="q4-carousel-row f0">
                                            <div class="q4-mobile-table-key">
                                                <?=__('Element')?>
                                            </div>
                                            <div class="q4-mobile-table-value">
                                                <?=__($i->place->name)?>
                                            </div>
                                        </div>
                                        <div class="q4-carousel-row f0">
                                            <div class="q4-mobile-table-key">
                                                <?=__('Number')?>
                                            </div>
                                            <div class="q4-mobile-table-value">
                                                <span class="bidi-override"><?=$i->place->number?></span>
                                            </div>
                                        </div>
                                        <div class="q4-carousel-row f0">
                                            <div class="q4-mobile-table-key">
                                               <?=__('Crafts')?>
                                            </div>
                                            <div class="q4-mobile-table-value">
                                                <?=__($i->craft->name)?>
                                            </div>
                                        </div>
                                        <div class="q4-carousel-row f0">
                                            <div class="q4-mobile-table-key">
                                                <?=__('Status')?>
                                            </div>
                                            <div class="q4-mobile-table-value">
                                                <span class="q4-status-<?=$i->approval_status?>"><?=__($i->approval_status)?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?endforeach;?>
                            </div>
                        </div>
                        <?=$data['pagination']?>
                    </div>
                </div>
                <?else:?>
                    <h5 class="no-records-found"><?=__('Not found')?></h5>
                <?endif;?>

            </div><!--.panel-body-->
        </form>
    </div><!--panel_content-->
