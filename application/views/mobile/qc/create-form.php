<?defined('SYSPATH') OR die('No direct script access.');?>

<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.05.2017
 * Time: 11:33
 */

?>
<div class="modal create-modal qc-create-window quality-control-dialog-mobile q4-mobile-layout" id="qc-create" style="position: inherit!important; display: block!important; margin: auto; overflow: hidden !important;">
    <div id="qc-content" class="qc-content">
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
                                            <label class="table_label add_50percent"><?=__('Project name')?></label>
                                            <div class="select-wrapper">
                                                <input type="text" class="table_input add_50percent qc-project" value=""/>
                                                <input type="hidden" name="project_id" data-url="<?=URL::site('quality_control/get_objects')?>" value=""/>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label add_50percent"><?=__('Element')?></label>
                                            <input type="text" class="table_input add_50percent qc-place disabled-input" data-url="<?=URL::site('quality_control/get_places_for_floor')?>" data-place-data-url="<?=URL::site('quality_control/get_place_data')?>" value=""/>
                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label add_50percent"><?=__('Property')?></label>
                                            <select class="q4-select q4-form-input add_50percent qc-object disabled-input">
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label add_50percent"><?=__('Floor')?></label>
                                            <input type="text" class="table_input add_50percent disabled-input bidi-override qc-floor" value=""/>
                                        </div>



                                    </div>

                                    <div class="row">

                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label add_50percent"><?=__('Craft')?></label>
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <select class="q4-select q4-form-input add_50percent qc-craft q4_select disabled-input" data-url="<?=URL::site('quality_control/get_plans')?>" name="craft_id">
                                                    <option value=''><?=__('Please select')?></option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">


                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label add_50percent"><?=__('Space/Place')?> <span class="q4-required">*</span>
                                            </label>
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <select class="q4-select q4-form-input add_50percent qc-spaces disabled-input" name="space_id">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label add_50percent"><?=__('Stage')?></label>
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <select class="q4-select q4-form-input add_50percent" name="project_stage">
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
                                            <label class="table_label add_50percent"><?=__('Responsible profession')?> <span class="q4-required">*</span></label>
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <select class="q4-select q4-form-input add_50percent qc-profession disabled-input" name="profession_id">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 rtl-float-right">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <div class="row">

                                                <div class="col-25 rtl-float-right">
                                                    <label class="table_label add_50percent"><?=__('Element number')?></label>
                                                    <input type="text" class="table_input add_50percent disabled-input qc-place-name" value=""/>

                                                </div>
                                                <div class="col-25 rtl-float-right">
                                                    <label class="table_label add_50percent"><?=__('Element id')?></label>
                                                    <input type="text" class="table_input add_50percent disabled-input bidi-override qc-place-number" value=""/>

                                                </div>
                                                <div class="col-30 rtl-float-right">
                                                    <label class="table_label add_50percent"><?=__('Due Date')?></label>
                                                    <div class="input-group date" id="qc-due-date" data-provide="datepicker">
                                                        <div class="input-group-addon small-input-group">
                                                            <span class="glyphicon glyphicon-calendar"></span>
                                                        </div>
                                                        <input type="text" name="due_date" class="q4-form-input add_50percent table_input add_50percent" data-date-format="DD/MM/YYYY" value="<?=date('d/m/Y', time())?>"/>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="table_label add_50percent"><?=__('Plan')?></label>
                                            <div class="qc-choose-plan">
                                                <a href="#" data-toggle="modal" data-target="#choose-plan-modal-mobile" class="q4-btn-lg add_50percent light-blue-bg choose-plan"><?=__('Choose plan')?></a>
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
                        <h3 class="table_label add_50percent"><?=__('Tasks List')?></h3>
                        <div class="tasks-full-description-box-mobile qc-tasks">

                        </div>
                    </div>

                    <div class="plans-modal-dialog-top form_row">

                        <!-- <div class="row">
                            <div class="form-group col-md-12">
                                <div class="modal-images-list-box">
                                    <a href="#" id="modal-tasks-load-images" class="inline_block_btn add_50percent blue-light-button  modal-load-images"><?=__('Load images')?></a>
                                    <div class="hide-upload">

                                        <input type="file" class="load-images-input" accept="image/*" data-id="<?=uniqid()?>" multiple name="images[]">
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div> -->

                        <div class="row">
                            <div class="form-group col-md-6 rtl-float-right">
                                <label class="table_label add_50percent"><?=__('Status')?> <span class="q4-required">*</span></label>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <select class="q4-select q4-form-input add_50percent qc-status" name="status">
                                        <?foreach (Enum_QualityControlStatus::toArray() as $val):?>
                                            <option value="<?=$val?>"><?=__($val)?></option>
                                        <?endforeach?>

                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6 rtl-float-right">
                                <label class="table_label add_50percent"><?=__('Conditions')?></label>
                                <div class="property-quality-control-conditions">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="table_label add_50percent"><?=__('Severity Level')?></label>
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <select class="q4-select q4-form-input add_50percent disabled-input" name="severity_level">
                                                    <option selected="selected"><?=__('Please select')?></option>
                                                    <?foreach (Enum_QualityControlConditionLevel::toArray() as $val):?>
                                                        <option value="<?=$val?>"><?=ucfirst(__($val))?></option>
                                                    <?endforeach;?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="table_label add_50percent"><?=__('Conditions List')?></label>
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <select class="q4-select q4-form-input add_50percent disabled-input" name="condition_list">
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

                            <div class="form-group col-md-12">
                                <label class="table_label add_50percent"><?=__('Images List')?></label>

                                <div data-structurecount="0" class="qc-image-list-mobile q4-owl-carousel">
                                </div>
                                <div class="modal-images-list-box">
                                    <a href="#" class="q4-btn-lg light-blue-bg modal-load-images quality-control-load-images add_50percent"><?=__('Load images')?></a>
                                    <div class="hide-upload">
                                        <input type="file" class="load-images-input" data-id="<?=uniqid()?>" accept="image/*" multiple name="images[]">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="plans-modal-dialog-bottom">
                        <div class="row">
                            <div class="form-group col-md-6 rtl-float-right">
                                <label class="table_label add_50percent"><?=__('Description')?></label>
                                <textarea class="modal-details-textarea q4_required" name="description"></textarea>
                            </div>

                            <div class="form-group col-md-6 rtl-float-right">
                                <div class="quality-control-actions-list">
                                    <div class="modal-details-actions">
                                        <div class="modal-details-actions-list">
                                            <div>
                                                <span class="modal-details-action add_50percent blue"><?=__('Viewed by')?> :</span>
                                                <span class="modal-details-action-status add_50percent orange"><?=__('Waiting to approve')?></span>
                                            </div>
                                            <div>
                                                <span class="modal-details-action add_50percent blue"><?=__('Created by')?> : </span>
                                                <span class="modal-details-action-status add_50percent gray"><?=Auth::instance()->get_user()->name?></span>
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
                            <a href="#" class="inline_block_btn add_50percent orange_button q4-form-submit q4_form_submit"><?=__('Create')?></a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="mobile_item--link-group">
    <div class="mobile_item--link-wrapper">
        <a href="<?= URL::site('dashboard') ?>" class="mobile_item--link">
            <i class="mobile_item--link-icon q4b-mobile-manager_console"></i>
            <span class="mobile_item--link-title"><?= __('Dashboard') ?></span>
        </a>
        <a href="<?= URL::site('plans') ?>" class="mobile_item--link">
            <i class="mobile_item--link-icon q4b-mobile-plan"></i>
            <span class="mobile_item--link-title"><?= __('Plans') ?></span>
        </a>
        <a href="<?= URL::site('reports/list') ?>" class="mobile_item--link">
            <i class="mobile_item--link-icon q4b-mobile-Reports"></i>
            <span class="mobile_item--link-title"><?= __('Reports') ?></span>
        </a>
        <a href="<?= URL::site('consultants') ?>" class="mobile_item--link">
            <i class="mobile_item--link-icon q4b-mobile-consultation"></i>
            <span class="mobile_item--link-title"><?= __('Menu_Consultants And Auditors') ?></span>
        </a>
    </div>
    <a id="show_console" class="mobile_item-create-qc">
        <i class="q4b-mobile-create_qc"></i>
        <span><?= __('Create Quality Control') ?></span>
    </a>
</div>


<div id="choose-plan-modal-mobile" class="choose-plan-modal modal no-delete no-delete-v2" role="dialog">
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
                        <table class="rwd-table responsive_table table" data-toggle="table">
                            <tbody class="qc-v-scroll qc-plans-list">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer text-align">
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="#" class="inline_block_btn blue-light-button confirm-plan1 confirm-plan-mobile"><?=__('Confirm')?></a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .qc-tasks{
        width: 100%!important;
    }
</style>
<script>
    (function($) {
        <?$u = []?>
        <?foreach ($projects as $key => $project): ?>
        <?$name = str_replace("'"," ",$project->name)?>
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

        /**
         *  Show Create QC
         */
        $(document).find('.mobile_item--link-group').closest('.content').addClass('mobile-bg');
        $(document).on('click', '#show_console', function() {
            $(this).closest('.mobile_item--link-group').hide();
            $(this).closest('.mobile_item--link-group').closest('.content').removeClass('mobile-bg');
            $(document).find('#qc-content').show();
        });
    })(jQuery);
</script>