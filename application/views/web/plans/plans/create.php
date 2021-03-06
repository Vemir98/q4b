<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 23.03.2017
 * Time: 4:25
 */
$floorNumbersWithNames = $object->floorNumbersWithNames();
?>

<div id="add-plans-modal" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog">
    <div class="modal-dialog q4_project_modal modal-dialog-1070">
        <form class="q4_form" action="<?=$action?>" data-submit="false" data-ajax="true">
            <div class="modal-content">
                <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                        <div class="clear"></div>
                    </div>
                    <div class="q4_modal_sub_header">
                        <h3><?=__('Add plans')?></h3>
                    </div>
                </div>
                <div class="modal-body bb-modal">
                    <input type="hidden" value="" name="x-form-secure-tkn"/>
                    <div class="plans-modal-dialog-top">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="table_label"><?=__('Profession')?></label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select name="profession_id" id="profession_id" class="q4-select q4_select q4-form-input profession-general-select" required>
                                        <option value=""><?=__('Please select')?></option>
                                        <?foreach ($professions as $profession): ?>
                                            <option value="<?=$profession->id?>" <?=($profession->id == $professionId) ? 'selected' : ''?>><?=$profession->name?></option>
                                        <?endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="table_label"><?=__('Project')?></label>
                                <input type="text" class="q4-form-input disabled-input" value="<?=$_PROJECT->name?>">
                                <input name="project_id" type="hidden" class="q4-form-input disabled-input" value="<?=$_PROJECT->id?>" required>
                            </div>
                        </div>
                        <div class="row">
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
                                    <table class="rwd-table responsive_table table add-new-plan-table" data-toggle="table">
                                        <thead>
                                        <tr>
                                            <th data-field="<?=__('Sheet Number')?>" class="td-100"><?=__('Sheet Number')?></th><!-- 3 -->
                                            <th data-field="<?=__('Name')?>" class="td-200"><?=__('Name')?></th><!-- 4 -->
                                            <th data-field="<?=__('Property')?>" class="td-100"><?=__('Property')?></th><!-- 3 -->
                                            <th data-field="<?=__('Floor')?>" class="td-100"><?=__('Floor')?></th>
                                            <th data-field="<?=__('Action')?>" class="td-50"><?=__('Action')?></th><!-- 12 -->
                                        </tr>
                                        </thead>
                                        <tbody class="modal-table-body create-plan-modal">
                                        <tr id="general-plan-row" class="general-plan-row">
                                            <td class="rwd-td1" data-th="">
                                                <input type="text" class="table_input sheet-number">
                                            </td>
                                            <td class="rwd-td2" data-th="">
                                                <input type="text" class="table_input plan-name">
                                            </td>
                                            <td class="rwd-td3" data-th="">
                                                <div class="select-wrapper structure-select">
                                                    <i class="q4bikon-arrow_bottom"></i>
                                                    <select class="plans-select-prof q4-select q4-form-input object-general-select">
                                                        <option value=""><?=__('Please select')?></option>

                                                        <?foreach ($objects as $obj): ?>
                                                            <option data-minfloor="<?=$obj->smaller_floor?>" data-maxfloor="<?=$obj->bigger_floor?>" data-floornames='<?=json_encode($obj->floorNumbersWithNames())?>' value="<?=$obj->id?>" <?=($obj->id == $object->id) ? 'selected' : ''?>><?=$obj->name?></option>
                                                        <?endforeach ?>

                                                    </select>
                                                </div>
                                            </td>
                                            <td class="rwd-td4 floors-select">
                                                <div class="multi-select-col floors-multi-select-col">
                                                    <label class="table_label">
                                                        <span class="check-all-links" data-seltxt="<?=__('select all')?>" data-unseltxt="<?=__('unselect all')?>"><?=__('select all')?></span>
                                                    </label>

                                                    <div class="multi-select-box-container">
                                                        <div class="multi-select-box comma floors-list">
                                                            <div class="select-imitation table_input floor-numbers<?=$disabled?>">
                                                                <span class="select-imitation-title"></span>
                                                                <div class="over-select"></div><i class="arrow-down q4bikon-arrow_bottom"></i>
                                                            </div>
                                                            <div class="checkbox-list">
                                                                <?for($i = $object->smaller_floor; $i <= $object->bigger_floor; $i++):?>
                                                                    <div class="checkbox-list-row" data-custom-label="true">
                                                                    <span class="checkbox-text">
                                                                        <label class="checkbox-wrapper-multiple inline" data-val="<?=$i?>">
                                                                            <span class="checkbox-replace"></span>
                                                                            <i class="checkbox-list-tick q4bikon-tick"></i>
                                                                        </label>
                                                                        <?if($floorNumbersWithNames[$i]):?>
                                                                            <span class="checkbox-text-content" data-val="<?=$i?>">
                                                                                <?=$floorNumbersWithNames[$i]?>
                                                                            </span>
                                                                        <?else:?>
                                                                            <span class="checkbox-text-content bidi-override" data-val="<?=$i?>">
                                                                                <?=$i?>
                                                                            </span>
                                                                        <?endif;?>

                                                                    </span>
                                                                    </div>
                                                                <?endfor?>
                                                            </div>
                                                            <select class="hidden-select" name="floors" multiple>
                                                                <?for($i = $object->smaller_floor; $i <= $object->bigger_floor; $i++):?>
                                                                    <option value="<?=$i?>">
                                                                        <?=$floorNumbersWithNames[$i] ? $floorNumbersWithNames[$i] : $i?>
                                                                    </option>
                                                                <?endfor?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                            <td class="rwd-td12" data-th="Action">
                                                <div class="text-right-left action-buttons">
                                                    <a class="circle-sm orange add-plan">
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

        </form>

    </div>
</div>












