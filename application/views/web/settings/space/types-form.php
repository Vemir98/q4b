<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 09.01.2017
 * Time: 11:36
 */
?>


<form class="space-types-form q4_form" action="<?=$action?>" data-ajax="true"  method="post">
    <input type="hidden" value="" name="x-form-secure-tkn"/>
    <input type="hidden" value="<?=$secure_tkn?>" name="secure_tkn"/>
    <div class="modal-body q4-modal-body-small">
        <div class="row">
            <div class="col-md-12">
                <div class="panel-options">
                    <span class="inline-options">
                        <a class="circle-lg orange add-space-type" ><i class="plus q4bikon-plus"></i></a>
                        <span class="inline-options-text"><?=__('Add new type')?></span>
                    </span>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
             <!--<div class="space-type-scroll">
                    <table class="responsive_table table" data-toggle="table">
                        <thead>
                            <tr>
                                <th data-field="<?//=__('Name')?>" data-sortable="true"><?//=__('Name')?></th>
                                <th data-field="<?//=__('Delete')?>" class="td-25"><?//=__('Delete')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?//foreach ($items as $item):?>
                                <tr>
                                    <td data-th="<?//=__('Name')?>">
                                        <input type="text" class="table_input q4_required" name="space_<?//=$item->id?>_name" value="<?//=$item->name?>">
                                    </td>
                                    <?//if($item->id != 1):?>
                                        <td data-th="<?//=__('Delete')?>">
                                            <div class="wrap_delete_row">
                                                <span class="delete_row disabled-gray-button" data-url="<?//=URL::site('/settings/delete_space_type/'.$item->id)?>"><i class="q4bikon-delete"></i></span>
                                            </div>
                                        </td>
                                    <?//else:?>
                                        <td>

                                        </td>
                                    <?//endif;?>
                                </tr>
                            <?//endforeach;?>
                        </tbody>
                    </table>
                </div>-->

                <table class="table-scrollable-content">
                    <thead>
                        <tr>
                            <th data-field="<?=__('Name')?>" data-sortable="true"><?=__('Name')?></th>
                            <th data-field="<?=__('Delete')?>" class="td-max-100"><?=__('Delete')?></th>
                        </tr>
                    </thead>
                    <tbody class="tbody-vertical-scroll spaces-tbody">
                        <?foreach ($items as $item):?>
                            <tr>
                                <td data-th="<?=__('Name')?>">
                                    <input type="text" class="q4-form-input q4_required" name="space_<?=$item->id?>_name" value="<?=$item->name?>">
                                </td>
                                <?if($item->id != 1):?>
                                    <td data-th="<?=__('Delete')?>" class="td-max-100">
                                        <div class="text-center">
                                            <span class="delete_row disabled-gray-button" data-url="<?=URL::site('/settings/delete_space_type/'.$item->id)?>">
                                                <i class="q4bikon-delete"></i>
                                            </span>
                                        </div>
                                    </td>
                                <?else:?>
                                    <td class="td-max-100">

                                    </td>
                                <?endif;?>
                            </tr>
                        <?endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer text-align q4-modal-footer-small">
        <a href="#" class="inline_block_btn orange_button  q4_form_submit"><?=__('Update')?></a>
    </div>
</form>
