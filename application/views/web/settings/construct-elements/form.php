<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 09.01.2017
 * Time: 11:36
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




    <!-- property types Modal -->

<form class="construct-element-form" action="<?=$action?>" action="<?=$action?>" data-ajax="true"  method="post">
    <input type="hidden" value="" name="x-form-secure-tkn"/>
    <input type="hidden" value="<?=$secure_tkn?>" name="secure_tkn"/>
    <div class="modal-body q4-modal-body-small">
        <div class="row">
            <div class="col-md-12">

                <div class="add-new-row-double">

                    <div class="add-new-right">
                        <span><?=__('Add new type')?></span>
                        <a class="circle-lg orange add-construct-element"><i class="plus q4bikon-plus"></i></a>
                    </div>
                </div>

            </div>
        </div>
        <select  class="hidden selecticons">
            <?foreach ($icons as $icon):?>
                <option  data-icon="<?=$icon?>" value="<?=$icon?>"></option>
            <?endforeach;?>
        </select>

        <div class="row">
            <div class="col-md-12">
                <div class="space-type-scroll">
                    <table class="responsive_table table" data-toggle="table">
                        <thead>
                            <tr>
                                <th data-field="Name" ><?=__('Name')?></th>
                                <th data-field="Icon"  class="td-100"><?=__('Icon')?></th>
                                <th data-field="Space Count"  class="td-100 align-center-left">
                                    <?=__('Space Count')?>
                                </th>
                                <th data-field="Delete" class="td-50"><?=__('Delete')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?foreach ($items as $item):?>
                                <?if($item->id>2):?>
                                    <tr>
                                        <td data-th="Name">
                                            <input type="text" name="element_<?=$item->id?>_name" class="q4-form-input q4_required" value="<?=$item->name?>">
                                        </td>
                                        <td data-th="Icon">
                                            <div class="choose-icons">
                                                <i class="q4bikon-arrow_bottom"></i>
                                                <select name="element_<?=$item->id?>_icon" class="selectpicker">
                                                    <?foreach ($icons as $icon):?>
                                                        <option  <?=$item->icon == $icon ? "selected='selected'": ''?> value="<?=$icon?>" data-icon="<?=$icon?>"></option>
                                                    <?endforeach;?>
                                                </select>
                                            </div>
                                        </td>
                                        <td data-th="Space Count">
                                            <div class="wrap-number inline-pickers">
                                                <input type="text" class="numeric-input" name="element_<?=$item->id?>_space_count" value="1"/>
                                                <span class="arrows">
                                                    <i class="arrow no-arrow_top"></i>
                                                    <i class="arrow no-arrow_bottom"></i>
                                                </span>
                                            </div>
                                        </td>
                                        <td data-th="Delete">
                                            <div class="wrap_delete_row">
                                                <span class="delete_row disabled-gray-button"><i class="q4bikon-delete"></i></span>
                                            </div>
                                        </td>
                                    </tr>
                                <?endif?>
                            <?endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer q4-modal-footer-small">
        <a href="#" class="inline_block_btn orange_button submit q4_form_submit">
            <?=__('Update')?>
        </a>
    </div>
</form>

