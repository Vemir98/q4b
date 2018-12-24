<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 09.01.2017
 * Time: 11:36
 */
?>




    <!-- property types Modal -->

<form class="object-types-form q4_form" action="<?=$action?>" action="<?=$action?>" data-ajax="true"  method="post">
    <input type="hidden" value="" name="x-form-secure-tkn"/>
    <input type="hidden" value="<?=$secure_tkn?>" name="secure_tkn"/>
    <div class="modal-body q4-modal-body-small">
        <div class="row">
            <div class="col-md-12">
                <div class="panel-options">
                    <span class="inline-options">
                        <a class="circle-lg orange add-object-type"><i class="plus q4bikon-plus"></i></a>
                        <span class="inline-options-text"><?=__('Add new type')?></span>
                    </span>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

                <table class="table-scrollable-content" data-toggle="table">
                    <thead>
                        <tr>
                            <th data-field="<?=__('Name')?>" data-sortable="true"><?=__('Name')?></th>
                            <th data-field="<?=__('Alias')?>" data-sortable="true" class="td-max-150 align-center-left"><?=__('Alias')?></th>
                            <th data-field="<?=__('Delete')?>" class="td-max-100"><?=__('Delete')?></th>
                        </tr>
                    </thead>
                    <tbody class="tbody-vertical-scroll">
                        <?foreach ($items as $item):?>
                            <tr>
                                <td data-th="<?=__('Name')?>">
                                    <input type="text" class="q4-form-input q4_required" name="object_<?=$item->id?>_name" value="<?=$item->name?>">
                                </td>
                                <td data-th="<?=__('Alias')?>" class="td-max-150">
                                    <input type="text" readonly="readonly" class="q4-form-input object-alias" value="<?=$item->alias?>"/>
                                </td>
                                <td data-th="<?=__('Delete')?>" class="td-max-100">
                                    <div class="text-center">
                                        <span class="delete_row disabled-gray-button"><i class="q4bikon-delete"></i></span>
                                    </div>
                                </td>
                            </tr>
                        <?endforeach;?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer text-align q4-modal-footer-small">
        <a href="#" class="inline_block_btn orange_button q4_form_submit">
            <?=__('Update')?>
        </a>
    </div>
</form>
