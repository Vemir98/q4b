<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 20.04.2017
 * Time: 10:31
 */
$icons = [
    "q4bikon-floor",
    "q4bikon-appartment",
    "q4bikon-balcony",
    "q4bikon-cafe",
    "q4bikon-canteen",
    "q4bikon-cellar",
    "q4bikon-electricity_room",
    "q4bikon-elevator",
    "q4bikon-escalator",
    "q4bikon-hair_salon",
    "q4bikon-parking",
    "q4bikon-pharmacy",
    "q4bikon-playground",
    "q4bikon-reception",
    "q4bikon-residents-club",
    "q4bikon-security_room",
    "q4bikon-shop",
    "q4bikon-smoking_room",
    "q4bikon-sporthall",
    "q4bikon-stairway",
    "q4bikon-washing_room",
    "q4bikon-wc",
    "q4bikon-address",
    "q4bikon-delete",
    "q4bikon-company_status",
    "q4bikon-head_office",
    "q4bikon-not_found",
    "q4bikon-password",
    "q4bikon-preview",
    "q4bikon-project",
    "q4bikon-private",
    "q4bikon-send-by-email",
    "q4bikon-search",
    "q4bikon-property",
    "q4bikon-user_status",
];
?>
<div id="property-rooms-add-new-modal"  data-backdrop="static" data-keyboard="false" class="modal fade" role="dialog">
    <div class="modal-dialog q4_project_modal modal-dialog-1070">
        <form action="<?=$action?>" data-ajax="true" method="post">
        <input type="hidden" value="" name="x-form-secure-tkn"/>
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header q4_modal_header">
                <div class="q4_modal_header-top">
                    <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                    <div class="clear"></div>
                </div>
                <div class="q4_modal_sub_header">
                    <h3><?=__('New Element')?></h3>
                </div>
            </div>
            <div class="modal-body bb-modal">
                <div class="rooms-add-new-modal-top">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="table_label"><?=__('Element')?></label>

                            <div class="choose-icons-search">
                                <i class="q4bikon-arrow_bottom"></i>
                                <select id="constaction-object" class="q4-select q4-form-input" name="name" >
                                     <!-- <option value="1"><?=__("Space")?></option> -->
                                   <?foreach ($placeTypes as $type):?>
                                        <option data-icon="<?=$type->icon?>" value="<?=$type->name?>"><?=__($type->name)?></option>
                                    <?endforeach;?>
                                </select>
                            </div>
                            <!-- <input class="table_input form_input" name="name" value="" type="text"> -->

                        </div>
                        <div class="col-md-2">
                            <label class="table_label"><?=__('Floor')?></label>
                            <input type="text" class="table_input disabled-input bidi-override" value="<?=$place->floor->number?>">
                        </div>
                        <div class="col-md-2">
                            <label class="table_label"><?=__('Element number')?></label>
                            <input name="custom_number" type="text" class="table_input disabled-input" value="<?=$place->number > 0 ? ($place->number + 1) : ($place->number - 1)?>">
                        </div>
                        <div class="col-md-2">
                            <label class="table_label"><?=__('Choose an icon')?></label>

                             <div class="choose-icons">
                                <i class="q4bikon-arrow_bottom"></i>
                                <select class="selectpicker" name="icon">
                                   <?foreach($icons as $icon):?>
                                        <option value="<?=$icon?>" data-icon="<?=$icon?>"></option>
                                    <?endforeach;?>
                                </select>
                            </div>

                        </div>
                        <div class="col-md-3">
                            <label class="table_label"><?=__('Choose element type')?></label>
                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                 <select class="q4-select q4-form-input" name="type">
                                    <?foreach (Enum_ProjectPlaceType::toArray() as $val):?>
                                    <option value="<?=$val?>" <?if($val == Enum_ProjectPlaceType::PrivateS):?> selected="selected"<?endif?>><?=ucfirst(__($val))?></option>
                                    <?endforeach;?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rooms-add-new-modal-bottom">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel-options">
                                <span class="inline-options">
                                    <a class="orange_plus_small add-space"><i class="plus q4bikon-plus"></i></a>
                                    <span class="inline-options-text"><?=__('Add new item')?></span>
                                </span>
                            </div>
                            <table class="responsive-modal-table table rooms-clicked-table spaces-tbl">
                                <thead>
                                <tr>
                                    <th><?=__('№')?></th>
                                    <th><?=__('Item Type')?></th>
                                    <th><?=__('Item description')?></th>
                                    <th><?=__('Delete')?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td data-th="№">
                                        <input type="text" class="table_input disabled-input sp-number" value="1">
                                    </td>
                                    <td data-th="<?=__('Item Type')?>">
                                        <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                            <select class="q4-select q4-form-input" name="space_+<?=$defaultSpaceId?>_type" >
                                                <!-- <option value="1"><?=__("Space")?></option> -->
                                               <?foreach ($spaceTypes as $type):?>
                                                    <option value="<?=$type->id?>"><?=__($type->name)?></option>
                                                <?endforeach;?>
                                            </select>
                                        </div>
                                    </td>
                                    <td data-th="<?=__('Item Description')?>">
                                        <input type="text" class="table_input" name="space_+<?=$defaultSpaceId?>_desc">
                                    </td>
                                    <td data-th="<?=__('Delete')?>">

                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer text-right">
                <div class="row">
                    <div class="col-sm-12">
                        <a href="#" class="inline_block_btn light_blue_btn submit"><?=__('Create')?></a>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <select class="hidden space-types">
            <?foreach ($spaceTypes as $type):?>
                <option value="<?=$type->id?>"><?=__($type->name)?></option>
            <?endforeach;?>
        </select>
    </div>
    </div>
</div>
