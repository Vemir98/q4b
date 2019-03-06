<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 23.03.2017
 * Time: 2:44
 */
?>


<div id="plans-list-layout" class="plans-list-layout" data-trackingurl="<?=URL::site('plans/plans_printed/'.$_PROJECT->id)?>">
    <form action="<?=URL::site('/plans/update_plan_list/'.$_PROJECT->id)?>" data-ajax=true method="post" class="q4_form" autocomplete="off">
        <input type="hidden" value="" name="x-form-secure-tkn"/>
        <input type="hidden" value="<?=$secure_tkn?>" name="secure_tkn"/>
        <input type="hidden" class="current-profession-id" value="" />
        <input type="hidden" class="selected-plans" value="" />
        <div class="panel_body container-fluid plans-layout">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel-options form_row">
                        <div class="plans-border-bottom">
                            <a href="#" class="call-professions-list-modal" data-url="<?=URL::site('plans/plans_professions_list/'.$_PROJECT->id)?>"><?=__('Professions list')?> </a>
                            <a data-url="<?=URL::site('plans/tracking_list/'.$_PROJECT->id)?>" class="plans-date-tracking q4-link-b-blue"><?=__('Date tracking')?></a>
                            <a class="orange_plus_small add-plan" data-url="<?=URL::site('plans/create_plan/'.$_PROJECT->id)?>"><i class="plus q4bikon-plus"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                    <div class="col-md-3 rtl-float-right">
                        <label class="table_label"><?=__('Property')?></label>
                        <div class="form_row">
                            <div class="select-wrapper">
                                <i class="q4bikon-arrow_bottom"></i>
                                <select data-name="object" class="q4-select q4-form-input select-icon-pd">
                                    <option value="0" selected="selected"><?=__('All')?></option>
                                    <?foreach ($objects as $object): ?>
                                        <option value="<?=$object->id?>"><?=$object->name?></option>

                                    <?endforeach ?>
                                </select>
                            </div>
                            <i class="input_icon q4bikon-project"></i>
                        </div>
                    </div>
                    <div class="col-md-3 rtl-float-right">
                        <label class="table_label"><?=__('Profession')?></label>
                        <div class="form_row">
                            <div class="select-wrapper">
                                <i class="q4bikon-arrow_bottom"></i>
                                <select data-name="profession" class="q4-select q4-form-input select-icon-pd">
                                    <option value="0" selected="selected"><?=__('All')?></option>
                                    <?foreach ($professions as $profession): ?>
                                        <option value="<?=$profession->id?>"><?=$profession->name?></option>

                                    <?endforeach ?>
                                </select>
                            </div>
                            <i class="input_icon q4bikon-position"></i>
                        </div>
                    </div>
                    <div class="col-md-3 rtl-float-right multi-select-col">
                         <label class="table_label">
                            <?=__('Floor')?>
                            <span class="check-all-links" data-seltxt="<?=__('select all')?>" data-unseltxt="<?=__('unselect all')?>"><?=__('select all')?></span>
                        </label>
                        <div class="multi-select-box comma">
                            <div class="select-imitation q4-form-input floor-numbers">
                                <span class="select-imitation-title"></span>

                                <div class="over-select"></div><i class="arrow-down q4bikon-arrow_bottom"></i>
                            </div>
                            <div class="checkbox-list">
                                <?for($i = $floorsFilter['min']; $i <= $floorsFilter['max']; $i++):?>
                                    <div class="checkbox-list-row">
                                    <span class="checkbox-text">
                                        <label class="checkbox-wrapper-multiple inline" data-val="<?=$i?>">
                                            <span class="checkbox-replace"></span>
                                            <i class="checkbox-list-tick q4bikon-tick"></i>
                                        </label>
                                        <span class="checkbox-text-content bidi-override">
                                            <?=$i?>
                                        </span>
                                    </span>
                                    </div>
                                <?endfor?>
                            </div>
                            <select class="hidden-select floors-filter" multiple>
                                <?for($i = $floorsFilter['min']; $i <= $floorsFilter['max']; $i++):?>
                                    <option value="<?=$i?>"><?=$i?></option>
                                <?endfor?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 form-group rtl-float-right">
                        <label class="table_label visibility-hidden"><?=__('Show')?></label>
                        <input data-url="<?=URL::site('/plans/'.$_PROJECT->id.'/plans_list/')?>" class="inline-block-btn-small light_blue_btn filter-plans" type="submit" value="<?=__('Show')?>">

                    </div>

            </div>

            <div class="row">
                <div class="col-md-3 rtl-float-right">
                    <div class="form-group">
                        <div class="search-input-wrapper block">
                            <input  type="search" class="search-input search-plan-input" value="">
                            <a data-url="<?=URL::site('/plans/search_in_plan_list/'.$_PROJECT->id.'/search/')?>" class="search-button search-plans search-button-text"><?=__('Search')?></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">

                    <div class="q4-carousel-table-wrap">
                        <div class="q4-carousel-table" data-structurecount="<?=count($items)?>">
                            <?foreach ($items as $item):?>

                                <div class="item">
                                    <div class="q4-carousel-blue-head reports-prop-title">
                                        <span class="blue-head-title"><?=' #'.$item->id?></span>
                                        <div class="blue-head-option project-props-qc">
                                            <a class="show-structure-mobile plan-details" data-qc="quality-control" data-url="<?=URL::site('plans/update_plan/'.$item->project_id.'/'.$item->id)?>">
                                                <i class="plus q4bikon-preview"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0" >
                                        <div class="q4-mobile-table-key">
                                            <?=__('Name')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <?=$item->file() ? $item->file()->getName() : $item->name;?>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0">
                                        <div class="q4-mobile-table-key">
                                            <?=__('Type/No')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <?if($item->place->loaded())
                                                echo $item->place->type == 'public' ? "PB-".$item->place->number: "N-" .$item->place->number?>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0">
                                        <div class="q4-mobile-table-key">
                                            <?=__('Profession')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <?=$item->profession->name?>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0">
                                        <div class="q4-mobile-table-key">
                                            <?=__('Structure')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <?=$item->object->name?>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0">
                                        <div class="q4-mobile-table-key">
                                            <?=__('Floor')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <?=$item->getFloorsAsString()?>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0">
                                        <div class="q4-mobile-table-key">
                                            <?=__('Edition')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <?=$item->edition ?: null?>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0">
                                        <div class="q4-mobile-table-key">
                                            <?=__('Upload Date')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <?=date('d/m/Y',$item->created_at)?>
                                        </div>
                                    </div>
                                </div>
                            <?endforeach;?>
                        </div>
                        <?if(isset($pagination)):?>
                                <?=$pagination?>
                            <?endif?>
                    </div>
                </div>
            </div>

        </div>

    </form>
</div><!--.plans-list-layout-->


