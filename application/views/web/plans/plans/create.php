<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 23.03.2017
 * Time: 4:25
 */
?>


<div id="add-plans-modal" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog">
    <div class="modal-dialog q4_project_modal modal-dialog-1070">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header q4_modal_header">
                <div class="q4_modal_header-top">
                    <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                    <div class="clear"></div>
                </div>
                <div class="q4_modal_sub_header">
                    <h3><?=__('Add plan(s)')?></h3>
                </div>
            </div>
            <div class="modal-body bb-modal">
                <form class="q4_form" action="<?=$action?>" data-submit="false" data-ajax="true">
                    <input type="hidden" value="" name="x-form-secure-tkn"/>
                    <div class="plans-modal-dialog-top">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="table_label"><?=__('Property')?></label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select name="object_id" class="q4-select q4-form-input object-general-select" required>
                                        <option value=""><?=__('Please select')?></option>

                                        <?foreach ($objects as $object): ?>
                                            <option value="<?=$object->id?>"><?=$object->type->name.'-'.$object->name?></option>
                                        <?endforeach ?>

                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="table_label"><?=__('Profession')?></label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>

                                    <select name="profession_id" class="q4-select q4-form-input profession-general-select" required>
                                        <option value=""><?=__('Please select')?></option>

                                        <?foreach ($professions as $profession): ?>
                                            <option value="<?=$profession->id?>"><?=$profession->name?></option>
                                        <?endforeach ?>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="table_label"><?=__('Project')?></label>
                                <input type="text" class="q4-form-input disabled-input" value="<?=$_PROJECT->name?>">
                                <input name="project_id" type="hidden" class="q4-form-input disabled-input" value="<?=$_PROJECT->id?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="table_label"><?=__('Company')?></label>
                                <input type="text" class="q4-form-input disabled-input" value="<?=$_COMPANY->name?>">
                                <input name="company_id" type="hidden" class="q4-form-input disabled-input" value="<?=$_COMPANY->id?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="plans-modal-dialog-bottom">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="scrollable-table">
                                    <table class="rwd-table responsive_table table" data-toggle="table">
                                        <thead>
                                        <tr>
                                            <th data-field="<?=__('Sheet Number')?>" class="td-100"><?=__('Sheet Number')?></th><!-- 3 -->
                                            <th data-field="<?=__('Name')?>" class="td-200"><?=__('Name')?></th><!-- 4 -->
<!--                                        <th data-field="--><?//=__('Floor')?><!--" class="td-100">--><?//=__('Floor')?>
                                            <th data-field="<?=__('Action')?>" class="td-50"><?=__('Action')?></th><!-- 12 -->
                                        </tr>
                                        </thead>
                                        <tbody class="modal-table-body">
                                        <tr id="general-plan-row" class="general-plan-row">
                                            <td class="rwd-td1" data-th="">
                                                <input type="text" class="table_input sheet-number">
                                            </td>
                                            <td class="rwd-td2" data-th="">
                                                <input type="text" class="table_input plan-name">
                                            </td>
<!--                                        <td>-->
<!--                                            <div class="checkbox-list-no-scroll hidden">-->
<!---->
<!--                                                --><?//for($i = $item->object->smaller_floor; $i <= $item->object->bigger_floor; $i++):?>
<!--                                                    <div class="checkbox-list-row">-->
<!--                                                        <span class="checkbox-text">-->
<!--                                                            <label class="checkbox-wrapper-multiple inline" data-val="--><?//=$i?><!--">-->
<!--                                                                <span class="checkbox-replace"></span>-->
<!--                                                                <i class="checkbox-list-tick q4bikon-tick"></i>-->
<!--                                                            </label>-->
<!--                                                            <span class="checkbox-text-content bidi-override">-->
<!---->
<!--                                                                --><?//=$i?>
<!---->
<!--                                                            </span>-->
<!--                                                        </span>-->
<!--                                                    </div>-->
<!--                                                --><?//endfor?>
<!---->
<!--                                            </div>-->
<!--                                            <select class="hidden-select" name="plan_--><?//=$item->id?><!--_floors" multiple>-->
<!---->
<!--                                                --><?//for($i = $item->object->smaller_floor; $i <= $item->object->bigger_floor; $i++):?>
<!--                                                    <option value="--><?//=$i?><!--">--><?//=$i?><!--</option>-->
<!--                                                --><?//endfor?>
<!---->
<!--                                            </select>-->
<!--                                        </td>-->
                                            <td class="rwd-td12" data-th="Action">
                                                <div class="text-right-left action-buttons">
                                                    <a class="circle-sm orange add-plan"">
                                                        <i class="plus q4bikon-plus"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>

            <div class="modal-footer text-center">
                <div class="row">
                    <div class="col-sm-12">
                        <a href="#" class="q4-btn-lg white mr_30 cancel-upload-files" data-dismiss="modal"><?=__('Cancel')?></a>
                        <a href="#" class="q4-btn-lg orange upload-plans disabled-gray-button"><?=__('Done')?></a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>












