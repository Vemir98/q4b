<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 23.03.2017
 * Time: 2:44
 */

$icons = [
    "q4bikon-public",
    "q4bikon-private",
];
?>



<div class="plans-list-layout" data-trackingurl="<?=URL::site('projects/plans_printed/'.$_PROJECT->id)?>">
    <form action="<?=URL::site('/projects/update_plan_list/'.$_PROJECT->id)?>" data-ajax=true method="post" class="q4_form" autocomplete="off">
        <input type="hidden" value="" name="x-form-secure-tkn"/>
        <input type="hidden" value="<?=$secure_tkn?>" name="secure_tkn"/>
        <input type="hidden" class="current-profession-id" value="" />
        <input type="hidden" class="selected-plans" value="" />
        <div class="panel_body container-fluid plans-layout">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel-options form_row">
                        <div class="plans-border-bottom">
                            <span class="inline-options">
                                <a href="#" class="q4-link-b-blue" data-url="<?=URL::site('projects/plans_professions_list/'.$_PROJECT->id)?>">
                                    <span><?=__('Professions list')?></span>
                                </a>
                            </span>

                            <span class="inline-options">
                                <i class="q4bikon-reports"></i>
                                <a data-url="<?=URL::site('projects/tracking_list/'.$_PROJECT->id)?>" class="q4-link-b-blue">
                                    <?=__('Date tracking')?>
                                </a>
                            </span>

                            <span class="inline-options">
                                <a class="orange_plus_small add-plan" data-url="<?=URL::site('projects/create_plan/'.$_PROJECT->id)?>">
                                    <i class="plus q4bikon-plus"></i>
                                </a>
                                <span class="inline-options-text"><?=__('Add new plan')?></span>
                            </span>
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
                                    <?if(isset($objects)):?>
                                        <?foreach ($objects as $object): ?>
                                            <option value="<?=$object->id?>"><?=$object->name?></option>

                                        <?endforeach ?>
                                    <?endif?>
                                </select>
                            </div>
                            <i class="input_icon q4bikon-project"></i>
                        </div>

                    </div>
                    <div class="col-md-3 rtl-float-right">
                        <label class="table_label"><?=__('Profession')?></label>
                        <div class="form_row form-group">
                            <div class="select-wrapper">
                                <i class="q4bikon-arrow_bottom"></i>
                                <select data-name="profession" class="q4-select q4-form-input select-icon-pd">
                                    <option value="0" selected="selected"><?=__('All')?></option>
                                    <?if(isset($professions)):?>
                                        <?foreach ($professions as $profession): ?>
                                            <option value="<?=$profession->id?>"><?=$profession->name?></option>

                                        <?endforeach ?>
                                    <?endif?>
                                </select>
                            </div>
                            <i class="input_icon q4bikon-position"></i>
                        </div>



                    </div>

                    <div class="col-md-2 rtl-float-right multi-select-col">
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
                    <div class="col-md-2 form-group rtl-float-right">
                        <label class="table_label visibility-hidden"><?=__('Show')?></label>
                        <input data-url="<?=URL::site('/projects/'.$_PROJECT->id.'/plans_list/')?>" class="inline-block-btn-small light_blue_btn filter-plans" type="submit" value="<?=__('Show')?>">
                    </div>


            </div>
            <div class="row">
                <div class="col-md-3 rtl-float-right">
                    <div class="form-group">
                    <!-- <label class="table_label visibility-hidden"><?=__('unselect all')?></label> -->
                        <a class="plans-deselect-all q4-link-b-blue"><?=__('unselect all')?></a>
                    </div>
                </div>
                <div class="col-md-3 rtl-float-right">
                    <div class="search-input-wrapper block form-group">
                        <input  type="search" class="search-input search-plan-input" value="">
                        <a data-url="<?=URL::site('/projects/search_in_plan_list/'.$_PROJECT->id.'/search/')?>" class="search-button search-plans search-button-text"><?=__('Search')?></a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="scrollable-table">
                        <table class="rwd-table responsive_table table" data-toggle="table">
                            <thead>
                            <tr>
                                <th class="hidden w-25"></th>
                                <th class="w-25 check-all-column disabled-input">
                                    <label class="checkbox-wrapper" title="<?=__('select all on page')?>">
                                        <input type="checkbox">
                                        <span class="checkbox-replace"></span><i class="checkbox-tick q4bikon-tick"></i>
                                    </label>
                                </th>
                                <th class="td-50"></th>
                                <th data-field="<?=__('Name')?>" class="td-300"><?=__('Name')?></th>
                                <th data-field="<?=__('Floor')?>" class="td-25"><?=__('Floor')?></th>

                                <th data-field="<?=__('Element type')?>" class="td-100"><?=__('Element type')?></th>
                                <th data-field="<?=__('Element number')?>" class="td-100"><?=__('Element number')?></th>

                                <th data-field="<?=__('Craft')?>" class="td-100"><?=__('Craft')?></th>

                                <th data-field="<?=__('Edition')?>" class="td-25"><?=__('Edition')?></th>
                                <th data-field="<?=__('Profession')?>" class="td-200"><?=__('Profession')?></th>
                                <th data-field="<?=__('Property')?>" class="td-200"><?=__('Property')?></th>
                                <!-- <th data-field="<?=__('Upload date')?>" data-sortable="true" class="td-100"><?=__('Upload date')?></th> -->
                                <th data-field="<?=__('Action')?>" class="td-100"><?=__('Action')?></th>
                            </tr>
                            </thead>
                            <tbody>

                            <?foreach($items as $item):?>
                                <?
                                $disabled = $item->hasQualityControl() ? ' disabled-input' : '';
                                $disabledButton = $item->hasQualityControl() ? ' disabled-gray-button' : '';?>

                                <tr data-planid="<?=$item->id?>">
                                  <td class="hidden table-print-td" data-planid="<?=$item->id?>" data-property="<?=$item->object->type->name.' - '.$item->object->name?>"  data-profession="<?=$item->profession->name?>" data-professionid="<?=$item->profession->id?>" data-id="<?=$item->object->type->name.' - '.$item->object->name?>">
                                     <table>
                                            <tr data-id="<?=$item->id?>">
                                                <td><?=$item->file() ? $item->file()->getName() : $item->name;?></td>
                                                <td><?=$item->edition?></td>
                                                <td><?=__($item->status)?></td>
                                                <td><?=date('d/m/Y',$item->date)?></td>
                                                <td><?=$item->description?></td>
                                                <td></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="rwd-td0 enable-plan-action selectable-column text-center-left-right" data-th="<?=__('select')?>">
                                        <label  class="checkbox-wrapper">
                                             <input type="checkbox">
                                             <span class="checkbox-replace"></span><i class="checkbox-tick q4bikon-tick"></i>
                                        </label>
                                    </td>
                                    <td class="rwd-td1 align-center-left" data-th="<?=__('Details')?>">
                                        <div class="div-cell td-cell-80">
                                            <span class="show-structure plan-details" title="<?=__('Details')?>" data-url="<?=URL::site('projects/update_plan/'.$item->project_id.'/'.$item->id)?>">
                                                <i class="plus q4bikon-preview"></i>
                                            </span>
                                            <span class="delete_row delete-plan<?=$disabledButton?>" data-url="<?=URL::site('projects/plan_delete/'.$item->project_id.'/'.$item->id)?>" title="<?=__('Delete plan')?>">
                                                <i class="q4bikon-delete"></i>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="rwd-td2 plan-name-field" data-th="<?=__('Name')?>">
                                        <?
                                            $name = $item->file() ? $item->file()->getName()  : $item->name;
                                            $mime = $item->file() ? strtolower($item->file()->ext) : 'unknown';
                                        ?>
                                        <input type="text" name="plan_<?=$item->id?>_name" class="q4-form-input plans-inline-input q4_required<?=$disabled?>" value="<?=$name ?>">
                                        <input type="hidden" name="plan_<?=$item->id?>_id" value="<?=$item->id?>">
                                        <span class="plans-inline-icon">
                                            <a target="_blank" href="<?=$item->file()->originalFilePath()?>" class="<?=$mime=='unknown'? 'disabled-input': ''?>">
                                                <img src="/media/img/choose-format/format-<?=$mime?>.png" title="<?=$name?>" alt="<?=$name?>">
                                            </a>
                                        </span>
                                    </td>
                                    <td class="rwd-td5" data-th="<?=__('Floor')?>">
                                        <?if($item->place->loaded()):?>
                                            <span class="bidi-override">
                                                <input type="text" class="q4-form-input disabled-input"  value="<?=$item->place->floor->number?>">
                                            </span>
                                        <?else:?>
                                            <div class="multi-select-box comma">
                                                <div class="select-imitation q4-form-input floor-numbers<?=$item->place->loaded() ? ' disabled-input' : ''?><?=$disabled?>">
                                                    <span class="select-imitation-title"><?=$item->getFloorsAsString()?></span>

                                                    <div class="over-select"></div><i class="arrow-down q4bikon-arrow_bottom"></i>
                                                </div><?php $floorNumbers = $item->floorNumbers();
                                                        $floorNumbersWithNames = $item->floorNumbersWithNames();
                                                        echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$floorNumbersWithNames]); echo "</pre>"; exit;
                                                        ?>

                                                <div class="checkbox-list-no-scroll hidden">
                                                    <?for($i = $item->object->smaller_floor; $i <= $item->object->bigger_floor; $i++):?>
                                                        <div class="checkbox-list-row">
                                                        <span class="checkbox-text">
                                                            <label class="checkbox-wrapper-multiple inline <?=in_array($i,$floorNumbers) ? 'checked' : ''?>" data-val="<?=$i?>">
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
                                                <select class="hidden-select" name="plan_<?=$item->id?>_floors" multiple>
                                                    <?for($i = $item->object->smaller_floor; $i <= $item->object->bigger_floor; $i++):?>
                                                        <option <?=in_array($i,$floorNumbers) ? 'selected="selected"' : ''?> value="<?=$i?>"><?=$i?></option>
                                                    <?endfor?>
                                                </select>
                                            </div>
                                        <?endif?>
                                    </td>
                                    <td class="rwd-td3" data-th="<?=__('Element type')?>">
                                        <div class="choose-icons<?=$disabled?>">
                                            <i class="q4bikon-arrow_bottom"></i>
                                            <select class="selectpicker" name="plan_<?=$item->id?>_place_type">
                                                <option value="0" data-icon="" <?=!$item->place->loaded() ? 'selected="selected"' : ''?>><?=__(" ")?></option>

                                                    <option value="public" data-icon="q4bikon-public" <?=$item->place->loaded() && $item->place->type == "public" ? 'selected="selected"' : ''?> ></option>
                                                    <option value="private" data-icon="q4bikon-private" <?=$item->place->loaded() && $item->place->type == "private" ? 'selected="selected"' : ''?>></option>



                                            </select>
                                        </div>
                                    </td>
                                    <td class="rwd-td3" data-th="<?=__('Element number')?>">
                                        <?
                                            $placeNumber = !empty($item->place->custom_number) ? $item->place->custom_number : $item->place->number;
                                        ?>
                                         <input name="plan_<?=$item->id?>_custom_number" type="text" class="q4-form-input plan-place-custom-number<?=empty($item->place->custom_number) ? ' disabled-input':''?><?=$disabled?>" value="<?=$item->place->loaded() ? $placeNumber: ''?>">


                                    </td>
                                    <td class="table-craft-selector" data-th="<?=__('Craft')?>">
                                        <?
                                        $selectedCrafts = $item->crafts->find_all();
                                        $selCraftArray = [];
                                        foreach ($selectedCrafts as $craft) {
                                            $selCraftArray[$craft->id] = $craft->name;
                                        }
                                    ?>
                                        <div class="multi-select-box multi-select-col">
                                            <div class="select-imitation<?=$disabled?>">
                                                <span class="select-imitation-title">
                                                    <?if(isset($selCraftArray) AND $selCraftArray):?>
                                                    <?foreach ($selCraftArray as $craft) {
                                                       echo $craft.',';
                                                    } ?>
                                                    <?else:?>
                                                        <span class="select-def-text"><?=__('Please select')?></span>
                                                    <?endif?>
                                                </span>
                                                <div class="over-select"></div>
                                                <i class="arrow-down q4bikon-arrow_bottom"></i>
                                            </div>
                                            <div class="checkbox-list-no-scroll hidden">
                                                <span class="check-all-links" data-seltxt="<?=__('select all')?>" data-unseltxt="<?=__('unselect all')?>"><?=__('select all')?></span>
                                                <?
                                                    $crafts = $item->profession->crafts->where('status','=',Enum_Status::Enabled)->order_by('name','ASC')->find_all();
                                                        foreach ($crafts as $cr):?>
                                                            <div class="checkbox-list-row" data-profession=<?=$item->profession->id?>>
                                                                <span class="checkbox-text">
                                                                    <label data-val="<?=$cr->id?>" class="checkbox-wrapper-multiple inline <?=in_array($cr->id,array_keys($selCraftArray)) ? 'checked' : ''?>">
                                                                        <span class="checkbox-replace"></span>
                                                                        <i class="checkbox-list-tick q4bikon-tick"></i>
                                                                    </label>
                                                                    <?=$cr->name?>
                                                                </span>
                                                            </div>
                                                    <?endforeach; ?>


                                            </div>

                                            <select name="plan_<?=$item->id?>_crafts" class="hidden-select" multiple>

                                                    <?
                                                    $crafts = $item->profession->crafts->where('status','=',Enum_Status::Enabled)->find_all();
                                                    $c = [];
                                                    foreach ($crafts as $cr):?>
                                                        <?$craftsArray[$cr->id]=$cr->id?>
                                                        <option value="<?=$cr->id?>" <?=in_array($cr->id,array_keys($selCraftArray)) ? ' selected="selected"':''?> data-profession="<?=$item->profession->id?>"><?=__($cr->name)?></option>
                                                    <?endforeach; ?>



                                            </select>
                                        </div>

                                    </td>
                                    <td class="rwd-td6" data-th="<?=__('Edition')?>">
                                        <input name="plan_<?=$item->id?>_edition" type="text" class="q4-form-input<?=$disabled?>" value="<?=$item->edition ?: null?>">
                                    </td>
                                    <td class="rwd-td3" data-th="<?=__('Profession')?>">
                                        <div class="select-wrapper" title="<?=$item->profession->name?>">
                                            <i class="q4bikon-arrow_bottom"></i>
                                            <select class="q4-select q4-form-input disabled-input">
                                                <option value="Property name"><?=$item->profession->name?></option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="rwd-td4" data-th="<?=__('Property')?>">
                                         <div class="select-wrapper" title="<?=$item->object->type->name?> - <?=$item->object->name?>">
                                            <select class="q4-select q4-form-input disabled-input">
                                                <option value="Property name"><?=$item->object->name?></option>
                                            </select>
                                        </div>
                                    </td>
                                    <!-- <td class="rwd-td7 align-center-left" data-th="<?=__('Upload date')?>">
                                        <div class="div-cell">
                                            <input type="text" class="q4-form-input disabled-input" value="<?=date('d/m/Y',$item->created_at)?>">
                                        </div>
                                    </td> -->
                                    <td class="rwd-td8 td-cell-120" data-th="<?=__('Action')?>">
                                        <div class="wrap-align-row">
                                            <span class="plans-add-edition add-plan-edition"
                                                  data-toggle="modal" data-target="#plans-new-edition-modal"
                                                  data-url="<?=URL::site('projects/add_edition/'.$item->project_id.'/'.$item->id)?>"  title="<?=__('Add Edition')?>"><i class="plus q4bikon-plus"></i>
                                            </span>
                                            <span class="copy-element copy-plan" data-url="<?=URL::site('projects/copy_plan/'.$item->project_id.'/'.$item->id)?>" title="<?=__('Copy plan')?>">
                                                <i class="q4bikon-copy"></i>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            <?endforeach;?>
                            </tbody>
                        </table>
                    </div>
                        <div class="plans-aux">
                            <span class="total-res"><?=__('Total selected')?> (<span class="total-res-selected">0</span>)&lrm; <?=__('plan(s)')?></span>
                            <?if(isset($pagination)):?>
                                <?=$pagination?>
                            <?endif?>
                        </div>
                </div>
            </div>

        </div>

        <div class="panel_footer text-align">
            <div class="row">
                <div class="col-sm-12">
                    <a href="#" class="q4-btn-lg light-blue-bg disabled-gray-button plans-to-print-link" data-toggle="modal" data-target="#plans-to-print-modal"><?=__('Proceed to print')?></a>
                    <a href="#" class="q4-btn-lg light-blue-bg disabled-gray-button plans-to-send" data-toggle="modal" data-id=<?=$_PROJECT->id?> data-url="<?=URL::site('projects/plans_mailing/'.$_PROJECT->id)?>"><?=__('Proceed to send')?></a>
                    <a class="q4-btn-lg q4_form_submit orange update-plans"><?=__('Update')?></a>
                </div>
            </div>
        </div>
    </form>
</div><!--.plans-list-layout-->



<!--  ***** PRINTABLE PART ******* -->
    <div id="plans-printable2" class="print-landscape-mode">
        <style type="text/css">
            @media print {
                @page {
                    size: landscape;
                }
            }
        </style>
        <div class="first-page hidden">
            <div class="printable-logo">
                <img src="/media/img/logo.png" alt="Logo">
            </div>
            <h2 class="printable-title"><?=__('Plan list')?> </h2>
            <div class="printable-general">

                <div class="form-group">
                    <span class="pr-option"><?=__('Company name')?> : <?=$_PROJECT->company->name?> &nbsp;</span>
                    <span class="pr-option"><?=__('Project name')?> : <?=$_PROJECT->name?> &nbsp;</span>
                </div>
                <div class="form-group">
                    <span class="pr-date"><?=__('Date')?>: <?=date('d-m-Y')?></span>
                    <span class="pr-proff"><?=__('Profession')?>: </span><span class="pr-proff-name"></span>
                </div>

            </div>
        </div>
        <div class="printable-table-first hidden">
            <div class="page-break">
                <div class="printable-logo">
                    <img src="/media/img/logo.png" alt="Logo">
                </div>

                <table class="printable-table">
                    <thead>
                        <tr>
                            <th colspan="6" class="text-center" data-type="property"><?=__('Property')?> :</th>
                        </tr>
                        <tr>
                            <th class="pr-cell-name"><?=__('Name/Type')?></th>
                            <th><?=__('Edition')?></th>
                            <th><?=__('Status')?></th>
                            <th class="pr-cell-date"><?=__('Plan date')?></th>
                            <th class="pr-cell-name"><?=__('Description')?></th>
                            <th><?=__('Quantity')?></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <div class="wrap-floats">
                    <span class="pr-tracking-id"><?=__('Tracking ID')?>:<span class="pr-tracking-val"></span></span>
                </div>

                <div class="q4-copyright">
                    <span>
                        <!-- ?=__('Powered by')?> <img src="/media/img/company-logo-c.png" alt="company logo" class="q4-copyright-img">  -->
                        <?=__('Copyright ?? 2017 Q4B')?></span>
                    <span><?=__('All right reserved')?></span>
                </div>
            </div>
        </div>
        <div class="printable-table-other">

        </div>
        <div class="printable-bottom">
            <div class="printable-bottom-section">
                <div class="printable-bottom-section-wrapper">
                    <div class="printable-bottom-details"></div>
                    <span class="printable-bottom-hint"><?=__('Name')?></span>
                </div>
            </div>
            <div class="printable-bottom-section">
                <div class="printable-bottom-section-wrapper">
                    <div class="printable-bottom-details"></div>
                    <span class="printable-bottom-hint"><?=__('Date')?></span>
                </div>
            </div>
            <div class="printable-bottom-section">
                <div class="printable-bottom-section-wrapper">
                    <div class="printable-bottom-details"></div>
                    <span class="printable-bottom-hint"><?=__('Signature')?></span>
                </div>
            </div>
        </div>
    </div>
    <!--  ***** end of PRINTABLE PART ******* -->