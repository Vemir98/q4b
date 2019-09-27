<?defined('SYSPATH') OR die('No direct script access.');?>

<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.05.2017
 * Time: 11:33
 */

?>
<div class="modal create-modal qc-create-window" id="qc-create" style="position: inherit!important; display: block!important;">
    <form id="qc-form" class="q4_form" data-submit="false" action="<?=URL::site('/quality_control/create')?>"  data-ajax="true" method="post">
        <input type="hidden" value="" name="x-form-secure-tkn"/>
        <input type="hidden" value="" name="place_id">
        <div>
            <div>

                <div class="plans-modal-dialog-top">

                    <div class="form_row form-group">
                        <div class="row">
                            <div class="col-md-6 rtl-float-right">
                                <div class="row">
                                    <div class="form-group col-md-6 rtl-float-right">
                                        <label class="table_label"><?=__('Project name')?></label>
                                        <div class="select-wrapper">
                                            <input type="text" class="table_input qc-project" value=""/>
                                            <input type="hidden" name="project_id" data-url="<?=URL::site('quality_control/get_objects')?>" value=""/>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 rtl-float-right">
                                        <label class="table_label"><?=__('Property')?></label>
                                        <select class="q4-select q4-form-input qc-object disabled-input">
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6 rtl-float-right">
                                        <label class="table_label"><?=__('Craft')?></label>
                                        <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                            <select class="q4-select q4-form-input qc-craft q4_select disabled-input" data-url="<?=URL::site('quality_control/get_plans')?>" name="craft_id">
                                                <option value=''><?=__('Please select')?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 rtl-float-right">
                                        <label class="table_label"><?=__('Element')?></label>
                                        <input type="text" class="table_input qc-place disabled-input" data-url="<?=URL::site('quality_control/get_places_for_floor')?>" data-place-data-url="<?=URL::site('quality_control/get_place_data')?>" value=""/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 rtl-float-right">
                                        <label class="table_label"><?=__('Space/Place')?> <span class="q4-required">*</span>
                                        </label>
                                        <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                            <select class="q4-select q4-form-input qc-spaces disabled-input" name="space_id">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 rtl-float-right">
                                        <label class="table_label"><?=__('Stage')?></label>
                                        <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                            <select class="q4-select q4-form-input" name="project_stage">
                                                <?foreach(Enum_ProjectStage::toArray() as $stage):?>
                                                    <?$selected = isset($item->project_stage) && $item->project_stage == $stage ? " selected='selected'" : '';?>
                                                    <option<?$selected?> value="<?=$stage?>"><?=__($stage)?></option>
                                                <?endforeach?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 rtl-float-right">
                                        <label class="table_label"><?=__('Status')?> <span class="q4-required">*</span></label>
                                        <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                            <select class="q4-select q4-form-input qc-status" name="status">
                                                <?foreach (Enum_QualityControlStatus::toArray() as $val):?>
                                                    <option value="<?=$val?>"><?=__($val)?></option>
                                                <?endforeach?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 rtl-float-right">
                                        <label class="table_label"><?=__('Responsible profession')?> <span class="q4-required">*</span></label>
                                        <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                            <select class="q4-select q4-form-input qc-profession disabled-input" name="profession_id">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 rtl-float-right">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <div class="row">
                                            <div class="col-20 rtl-float-right">
                                                <label class="table_label"><?=__('Floor')?></label>
                                                <input type="text" class="table_input disabled-input bidi-override qc-floor" value=""/>
                                            </div>
                                            <div class="col-25 rtl-float-right">
                                                <label class="table_label"><?=__('Element number')?></label>
                                                <input type="text" class="table_input disabled-input qc-place-name" value=""/>

                                            </div>
                                            <div class="col-25 rtl-float-right">
                                                <label class="table_label"><?=__('Element id')?></label>
                                                <input type="text" class="table_input disabled-input bidi-override qc-place-number" value=""/>

                                            </div>
                                            <div class="col-30 rtl-float-right">
                                                <label class="table_label"><?=__('Due Date')?></label>
                                                <div class="input-group date" id="qc-due-date" data-provide="datepicker">
                                                    <div class="input-group-addon small-input-group">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </div>
                                                    <input type="text" name="due_date" class="q4-form-input table_input" data-date-format="DD/MM/YYYY" value="<?=date('d/m/Y', time())?>"/>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="table_label"><?=__('Plan')?></label>
                                        <div class="qc-choose-plan">
                                            <a href="#" data-toggle="modal" data-target="#choose-plan-modal" class="inline_block_btn blue-light-button choose-plan" style="height: 30px;margin: -1px 0 0 0;line-height: 30px;
                                                    "><?=__('Choose plan')?></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="property-quality-control-name hide">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="plans-modal-dialog-top relative error-handler">
                    <h4 class="table-modal-label-h4"><?=__('Tasks List')?></h4>
                    <div class="tasks-full-description-box qc-tasks">

                    </div>
                </div>

                <div class="plans-modal-dialog-top form_row">

                    <div class="row">
                        <div class="form-group col-md-12">
                            <div class="modal-images-list-box">
                                <a href="#" id="modal-tasks-load-images" class="inline_block_btn blue-light-button  modal-load-images"><?=__('Load images')?></a>
                                <div class="hide-upload">

                                    <input type="file" class="load-images-input" accept="image/*" data-id="<?=uniqid()?>" multiple name="images[]">
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="form-group col-md-6 rtl-float-right">
                            <label class="table_label"><?=__('Conditions')?></label>
                            <div class="property-quality-control-conditions">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="table_label"><?=__('Severity Level')?></label>
                                        <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                            <select class="q4-select q4-form-input disabled-input" name="severity_level">
                                                <option selected="selected"><?=__('Please select')?></option>
                                                <?foreach (Enum_QualityControlConditionLevel::toArray() as $val):?>
                                                    <option value="<?=$val?>"><?=ucfirst(__($val))?></option>
                                                <?endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="table_label "><?=__('Conditions List')?></label>
                                        <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                            <select class="q4-select q4-form-input disabled-input" name="condition_list">
                                                <option selected="selected"><?=__('Please select')?></option>
                                                <?foreach (Enum_QualityControlConditionList::toArray() as $val):?>
                                                    <option value="<?=$val?>"><?=ucfirst(__($val))?></option>
                                                <?endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-6 rtl-float-right">
                            <label class="table_label"><?=__('Images List')?></label>
                            <div id="tasks-quality-control-images-list" class="modal-images-list-table">
                                <table >
                                    <tbody>
                                    </tbody>
                                </table>

                            </div>

                        </div>

                    </div>
                </div>

                <div class="plans-modal-dialog-bottom">
                    <div class="row">
                        <div class="form-group col-md-6 rtl-float-right">
                            <label class="table_label"><?=__('Description')?></label>
                            <textarea class="modal-details-textarea q4_required" name="description"></textarea>
                        </div>

                        <div class="form-group col-md-6 rtl-float-right">
                            <div class="quality-control-actions-list">
                                <div class="modal-details-actions">
                                    <div class="modal-details-actions-list">
                                        <div>
                                            <span class="modal-details-action blue"><?=__('Viewed by')?> :</span>
                                            <span class="modal-details-action-status orange"><?=__('Waiting to approve')?></span>
                                        </div>
                                        <div>
                                            <span class="modal-details-action blue"><?=__('Created by')?> : </span>
                                            <span class="modal-details-action-status gray"><?=Auth::instance()->get_user()->name?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-align" style="background-color: transparent">
                <div class="row">
                    <div class="col-sm-12">
                        <a href="#" class="inline_block_btn orange_button q4-form-submit q4_form_submit"><?=__('Create')?></a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>



<div id="choose-plan-modal" class="choose-plan-modal modal no-delete no-delete-v2" role="dialog">
    <div class="modal-dialog modal-dialog-1170">
        <form class="q4_form" action="<?=1?>" data-ajax="true" method="post">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <button type="button" class="close q4-close-modal"><i class="q4bikon-close"></i></button>
                        <div class="clear"></div>
                    </div>
                    <div class="q4_modal_sub_header">
                        <h3><?=__('Choose a plan')?></h3>
                    </div>
                </div>
                <div class="modal-body bb-modal">

                    <div class="plans-modal-dialog-bottom">
                        <table class="rwd-table responsive_table table scrollable-tbody">
                            <thead>
                            <tr>
                                <th class="td-50"><?=__('')?></th>
                                <th data-field="Name/Type" data-sortable="true" class="td-cell-10"><?=__('Name/Type')?></th>
                                <th data-field="Profession" data-sortable="true" class="td-cell-10"><?=__('Profession')?></th>
                                <th data-field="Floor" class="td-10" data-sortable="true"><?=__('Floor')?></th>
                                <th data-field="Element number" data-sortable="true" class="w-10"><?=__('Element number')?></th>
                                <th data-field="Edition" data-sortable="true" class="w-10"><?=__('Edition')?></th>
                                <th data-field="Date" data-sortable="true" class="td-10"><?=__('Date')?></th>
                                <th data-field="Image" data-sortable="true" class="td-10"><?=__('Image')?></th>
                            </tr>
                            </thead>
                            <tbody class="qc-v-scroll qc-plans-list">
                            </tbody>
                        </table>


                    </div>
                </div>
                <div class="modal-footer text-align">
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="#" class="inline_block_btn blue-light-button confirm-plan1"><?=__('Confirm')?></a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>


<script>

    (function($) {
        <?$u = []?>
        <?foreach ($projects as $key => $project): ?>
        <?$name = str_replace("'","\'",html_entity_decode($project->name))?>
        <?$u[] = "{value: '{$name}', data: '{$project->id}'}"?>
        <?endforeach;?>
        <? $u = implode(",\n",$u)?>
        var projects = [
            <?=$u?>
        ];

        $('.qc-project').autocomplete({
            lookup: projects,
            minChars: 0,
            onSelect: function (suggestion) {
                if(suggestion.data) {
                    $(document).find('input[name="project_id"]').val(suggestion.data).change();
                    $('.qc-project').blur();
                }
            }
        });

        $('.qc-project').on('focus',function(){
            if($(this).val().length){
                $(this).val('').blur();
                var that = $(this);
                setTimeout(function () {
                    that.focus();
                },200);
            }
        });
    })(jQuery);
</script>