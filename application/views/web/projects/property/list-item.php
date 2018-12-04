<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 12.03.2017
 * Time: 7:38
 */
?>
<tr>
    <td data-th="<?=__('Structure')?>">
        <?if($item->state == Enum_ObjectState::Approved):?>
            <a data-url="<?=URL::site('projects/object_property_struct/'.$_PROJECT->company_id.'/'.$item->project_id.'/'.$item->id)?>" class="show-structure show-structure-layout pr-object-show-struct" ><i class="plus q4bikon-preview"></i></a>
        <?endif?>
    </td>
    <td class="rwd-td0" data-th="<?=__('Object Type')?>">
        <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
             <select class="q4-select q4-form-input" name="property_<?=$item->id?>_type_id">
                <?foreach($itemTypes as $type):?>
                    <option value="<?=$type->id?>" <?=($type->id == $item->type_id) ? 'selected="selected"' : ''?>><?=$type->name?></option>
                <?endforeach?>
            </select>
        </div>
    </td>
    <td class="rwd-td1" data-th="<?=__('Name')?>">
        <input type="text" class="table_input q4_required" name="property_<?=$item->id?>_name" value="<?=$item->name?>">
    </td>
    <?if($item->state != Enum_ObjectState::Approved):?>
        <td class="rwd-td2" data-th="<?=__('Floors (from-to)')?>&#x200E;">
            <div class="numeric-align-c f0">
                <div class="wrap-number inline-pickers">
                    <input type="text" class="numeric-input bidi-override floors-from" value="<?=$item->smaller_floor?>" name="property_<?=$item->id?>_smaller_floor"/>
                    <span class="arrows">
                        <i class="arrow no-arrow_top"></i>
                        <i class="arrow no-arrow_bottom"></i>
                    </span>
                </div>
                <span class="inline-picker-divider">-</span>
                <div class="wrap-number inline-pickers">
                    <input type="text" class="numeric-input bidi-override floors-to" value="<?=$item->bigger_floor?>" name="property_<?=$item->id?>_bigger_floor"/>
                    <span class="arrows">
                        <i class="arrow no-arrow_top"></i>
                        <i class="arrow no-arrow_bottom"></i>
                    </span>
                </div>
            </div>

        </td>
        <td class="rwd-td3 align-center-left" data-th="<?=__('Elements')?>">
            <div class="wrap-number inline-pickers">
                <input type="text" class="numeric-input places-count" name="property_<?=$item->id?>_places_count" value="1"/>
                <span class="arrows">
                    <i class="arrow no-arrow_top"></i>
                    <i class="arrow no-arrow_bottom"></i>
                </span>
            </div>
        </td>
    <?else:?>
        <td class="rwd-td2" data-th="<?=__('Floors (from-to)')?>&#x200E;">
            <div class="numeric-align-c width100 f0">
                <div class="wrap-number inline-pickers">
                    <input type="text" class="numeric-input bidi-override disabled-input" value="<?=$item->smaller_floor?>"/>

                </div>
                <span class="inline-picker-divider">-</span>
                <div class="wrap-number inline-pickers">
                    <input type="text" class="numeric-input bidi-override disabled-input" value="<?=$item->bigger_floor?>"/>

                </div>
            </div>

        </td>
        <td class="rwd-td3 align-center-left" data-th="<?=__('Elements')?>">
            <div class="wrap-number inline-pickers">
                <input type="text" class="numeric-input disabled-input" value="<?=$item->places_count?>"/>
            </div>
        </td>
    <?endif?>
    <td class="rwd-td4" data-th="<?=__('Start Date')?>">
        <div class="div-cell">
            <div id="property-start_date-<?=$item->id?>" class="input-group scrollable-date project-property-start_date" data-provide="datepicker">
                <div class="input-group-addon small-input-group">
                    <span class="glyphicon glyphicon-calendar"></span>
                </div>
                <input type="text" class="table_input" data-date-format="DD/MM/YYYY" name="property_<?=$item->id?>_start_date" value="<?=date('d/m/Y',$item->start_date)?>">
            </div>
        </div>
    </td>
    <td class="rwd-td5" data-th="<?=__('End Date')?>">
        <div class="div-cell">
            <div id="property-end_date-<?=$item->id?>" class="input-group scrollable-date project-property-end_date" data-provide="datepicker">
                <div class="input-group-addon small-input-group">
                    <span class="glyphicon glyphicon-calendar"></span>
                </div>
                <input type="text" class="table_input" data-date-format="DD/MM/YYYY" name="property_<?=$item->id?>_end_date" value="<?=date('d/m/Y',$item->end_date)?>">
            </div>
        </div>

    </td>
    <td data-th="<?=__('Action')?>">
        <div class="wrap_delete_row">
            <!--todo:: добавить стили-->
            <span class="copy-element enable-copying copy-property" data-url="<?=URL::site('projects/copy_property/'.$item->project_id.'/'.$item->id)?>" data-original-title="" title=""><i class="q4bikon-copy"></i></span>
            <span style="display: inline-block; margin-left: 5px;" class="delete_row delete-prop" data-id="<?=$item->id?>" data-url="<?=URL::site('projects/delete_property/'.$item->project_id.'/'.$item->id)?>"><i class="q4bikon-delete"></i></span>
        </div>
    </td>
</tr>
