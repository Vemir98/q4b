<?defined('SYSPATH') OR die('No direct script access.');?>

<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.05.2017
 * Time: 11:33
 */

?>

<div id="quality-control-modal" class="create-modal modal fade" data-backdrop="static" data-keyboard="false" role="dialog" data-qcid="<?=$item->id?>">
    <div class="modal-dialog q4_project_modal modal-dialog-1070">
        <form id="qc-form-id" class="q4_form" data-submit="false" action="<?=URL::site('/projects/quality_control/'.$item->id)?>"  data-ajax="true" method="post">
        <input type="hidden" value="" name="x-form-secure-tkn"/>
            <div class="modal-content">
                <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                        <div class="clear"></div>
                    </div>
                    <div class="q4_modal_sub_header">
                        <h3><?=__('Add new quality control')?></h3>
                    </div>
                </div>
                <div class="modal-body bb-modal">

                    <div class="plans-modal-dialog-top">

                        <div class="form_row form-group">
                            <div class="row">
                                <div class="col-md-6 rtl-float-right">
                                    <div class="row">
                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label"><?=__('Project name')?></label>
                                             <input type="text" class="table_input disabled-input" value="<?=$item->project->name?>"/>
                                        </div>
                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label"><?=__('Property')?></label>
                                            <input type="text" class="table_input disabled-input" value="<?=$item->object->type->name.'-'.$item->object->name?>"/>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label"><?=__('Craft')?></label>
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <select class="q4-select q4-form-input qc-craft q4_select" name="craft_id">
                                                    <option value=''><?=__('Please select')?></option>
                                                   <?foreach($item->project->company->crafts->where('status','=',Enum_Status::Enabled)->order_by('name')->find_all() as $craft):?>
                                                       <?
                                                       $profs = $craft->professions->where('status','=',Enum_Status::Enabled)->find_all();
                                                       $p = [];
                                                       foreach ($profs as $pr)
                                                           $p []= $pr->id;
                                                       if(empty($p)) continue;
                                                       ?>
                                                    <option value="<?=$craft->id?>" data-professions="<?=implode(',',$p)?>"><?=$craft->name?></option>
                                                    <?endforeach?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label"><?=__('Element')?></label>
                                            <input type="text" class="table_input disabled-input" value="<?=$item->name ?: ('element '.$item->number)?>"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label"><?=__('Space/Place')?> <span class="q4-required">*</span>
                                            </label>
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                 <select class="q4-select q4-form-input" name="space_id">
                                                <?$i = 1;?>
                                                <?foreach($item->spaces->find_all() as $space):?>
                                                    <option value="<?=$space->id?>"><?=$i.' '.$space->type->name.' '. $space->desc?></option>
                                                <?endforeach?>
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
                                               <select class="q4-select q4-form-input qc-profession" name="profession_id">

                                                <?foreach($item->project->company->professions->where('status','=',Enum_Status::Enabled)->find_all() as $prof):?>
                                                    <?
                                                    $crafts = $prof->crafts->where('status','=',Enum_Status::Enabled)->find_all();
                                                    $c = [];
                                                    foreach ($crafts as $cr)
                                                        $c []= $cr->id;
                                                    if(empty($c)) continue;
                                                    ?>
                                                    <option value="<?=$prof->id?>" data-crafts="<?=implode(',',$c)?>"><?=$prof->name?></option>
                                                <?endforeach?>
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
                                                    <input type="text" class="table_input disabled-input bidi-override" value="<?=$item->floor->number?>"/>
                                                </div>
                                                <div class="col-25 rtl-float-right">
                                                    <label class="table_label table_label-small"><?=__('Element number')?></label>
                                                    <input type="text" class="table_input disabled-input" value="<?=!empty($item->custom_number) ? $item->custom_number : $item->number?>"/>

                                                </div>
                                                <div class="col-25 rtl-float-right">
                                                    <label class="table_label"><?=__('Element id')?></label>
                                                    <input type="text" class="table_input disabled-input bidi-override" value="<?=$item->number?>"/>

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
                                <?$usedTasksAray = [];
                                $usedCrafts = [];
                                    foreach ($usedTasks as $task) {
                                       $usedTasksAray[] = $task->id;


                                    }
                                       // echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($usedTasks); echo "</pre>";
                                ?>
                        <div class="tasks-full-description-box">
                            <ul class="tasks-full-description qc-tasks-list">
                                <?foreach($tasks as $task):?>
                                    <li class="1-class hidden" >
                                        <a href="#" data-id="<?=$task->id?>" >

                                            <span class="selected-tick"><i class="q4bikon-tick"></i></span>
                                            <h4><?=__('Task')?> <?=$task->id?></h4>
                                            <div class="task-item-txt">
                                                <p><?=$task->name?></p>
                                            </div>
                                        </a>
                                    </li>
                                <?endforeach;?>
                            </ul>
                            <select class="hidden-select q4_select" name="tasks" multiple>
                                <?foreach($item->project->tasks->where('status','=',Enum_Status::Enabled)->find_all() as $task):?>
                                    <?php
                                    $crafts = $task->crafts->where('cmpcraft.status','=',Enum_Status::Enabled)->find_all();
                                    $c = [];
                                    foreach ($crafts as $cr)
                                        $c [$cr->id]= $cr->id;
                                    if(empty($c)) continue;
                                    $taskId = $task->id;
                                    $usedCraftsArray = isset($usedTasks->$taskId)? $usedTasks->$taskId->crafts: [];
                                    ?>
                                    <option value="<?=$task->id?>" data-usedcrafts="<?=implode(',',$usedCraftsArray)?>" data-crafts="<?=implode(',',$c)?>" ><?=$task->name?></option>
                                <?endforeach?>
                            </select>
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
                            <!-- <div class="form-group col-md-6 rtl-float-right">
                                <label class="table_label"><?=__('Comments')?></label>
                                <textarea maxlength="250" style="height:80px" placeholder="<?=__('Leave your comment')?>" class="modal-details-textarea" name="message"></textarea>
                            </div> -->
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
               <div class="modal-footer text-align">
                <div class="row">
                    <div class="col-sm-12">
                        <a href="#" class="inline_block_btn orange_button q4-form-submit q4_form_submit"><?=__('Create')?></a>
                    </div>
                </div>
            </div>
            </div>
        </form>
    </div>
</div>

    <div id="choose-plan-modal" class="choose-plan-modal modal no-delete" role="dialog">
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
                            <table class="rwd-table responsive_table table scrollable-tbody" data-toggle="table">
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
                                <tbody class="qc-v-scroll">

                                <?foreach($plans as $plan):?>
                                    <?
                                    $crafts = [];
                                    foreach ($plan->crafts->find_all() as $craft) {
                                        $crafts[] = $craft->id;
                                    }
                                    ?>
                                    <tr data-crafts='<?=json_encode($crafts)?>'>
                                        <td class="align-center-left td-50 enable-plan-action" data-th="<?=__('Select')?>">
                                            <div class="div-cell">

                                                <label class="q4-radio">
                                                    <input class="image-link" type="radio" value="<?=$plan->id?>" name="plan" data-img="<?=$plan->files->find()->getImageLink()?>">
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="pln-data hidden">
                                                    <div class="qc-change-plan">
                                                        <a href="#" data-toggle="modal" data-target="#choose-plan-modal"><?=__('Choose plan')?></a>
                                                    </div>
                                                    <h4 class="table-modal-label-h4"><?=__('Plan name')?>: <?=$plan->file() ? $plan->file()->getName() : $plan->name?></h4>
                                                    <input type="hidden" name="plan_id" value="<?=$plan->id?>"/>
                                                    <div class="col-20">
                                                        <label class="table_label"><?=__('Edition')?></label>
                                                        <input type="text" class="table_input disabled-input" value="<?=$plan->edition?>"/>
                                                    </div>
                                                    <div class="col-30">
                                                        <label class="table_label"><?=__('Date')?></label>
                                                        <input type="text" class="table_input disabled-input" value="<?=date('d/m/Y',$plan->date)?>"/>
                                                    </div>
                                                    <div class="col-50">
                                                        <label class="table_label"><?=__('Status')?></label>
                                                        <input type="text" class="table_input disabled-input" value="<?=__($plan->status)?>"/>
                                                    </div>
                                                    <div class="clear"></div>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="choose-view-format">
                                                                <span class="choose-view-format-title"><?=__('Choose view format')?>: </span>
                                                                <ul class="choose-view-format-list">
                                                                    <?$file = $plan->files->where('status','=',Enum_FileStatus::Active)->find()?>
                                                                   <li>

                                                                        <a data-fileid="<?=$file->id?>" data-url="<?=$file->getImageLink()?>" data-fileid="<?=$file->id?>" data-ext="<?=$file->mime?>" data-controller="add_quality_control_image_from_raw_plan"  class="call-lit-plugin" title="<?=$file->original_name?>"><img src="/media/img/choose-format/format-<?=strtolower($file->ext)?>.png" alt="<?=$file->ext?>"/></a>
                                                                    </li>

                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                        </td>
                                        <td class="rwd-td1" data-th="<?=__('Name/Type')?>">
                                            <div class="div-cell break-c">
                                                <?=$plan->file()->getName()?>
                                            </div>
                                        </td>
                                        <td class="rwd-td2" data-th="<?=__('Profession')?>">
                                            <div class="div-cell">
                                                <?=$plan->profession->name?>
                                            </div>
                                        </td>
                                        <td class="rwd-td4" data-th="<?=__('Floor')?>">
                                            <div class="div-cell">
                                                <?=$plan->getFloorsAsString() ? $plan->getFloorsAsString() : '-'?>
                                            </div>
                                        </td>
                                        <td class="rwd-td5" data-th="<?=__('Element number')?>">
                                            <div class="div-cell">
                                                <?if($plan->place_id):?>
                                                    <?=isset($plan->place->custom_number) ? $plan->place->custom_number : $plan->place->number?>
                                                <?else:?>
                                                    -
                                                <?endif?>
                                            </div>
                                        </td>
                                        <td class="rwd-td6" data-th="<?=__('Edition')?>">
                                            <div class="div-cell">
                                                <?=$plan->edition?>
                                            </div>
                                        </td>
                                        <td class="rwd-td8" data-th="<?=__('Date')?>">
                                            <div class="div-cell">
                                                <?=date('d/m/Y',$plan->date)?>
                                            </div>
                                        </td>
                                        <td class="rwd-td8" data-th="<?=__('Image')?>">
                                            <?$i = 0; $ext = null?>
                                            <?foreach ($plan->files->where('status','=',Enum_FileStatus::Active)->find() as $img):?>
                                                <?if($i > 1) break?>
                                                <!-- <?if($img->ext != $ext) $ext = $img->ext; else continue?> -->

                                                <a href="<?=$img->originalFilePath()?>" target="_blank" title="<?=$img->original_name?>"><img src="/media/img/choose-format/format-<?=strtolower($img->ext)?>.png" alt="<?=$img->ext?>"/></a>
                                            <?endforeach;?>
                                        </td>
                                    </tr>
                                <?endforeach;?>
                                </tbody>
                            </table>


                        </div>
                    </div>
                    <div class="modal-footer text-align">
                        <div class="row">
                            <div class="col-sm-12">
                                <a href="#" class="inline_block_btn blue-light-button confirm-plan"><?=__('Confirm')?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

