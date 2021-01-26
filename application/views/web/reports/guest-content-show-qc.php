<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.05.2017
 * Time: 11:33
 */
$printableItem = $item;
?>
<?$disabled = $item->approval_status=="approved" ? ' disabled-input' : '';
$disabledStatus = $item->approval_status=="approved" ? ' disabled-input' : '';
$isSubcontractor = false;
$isSuperAdmin = $_USER->is('super_admin');
if($_USER->is('project_supervisor') || $isSubcontractor){//запрет на изменение статуса для project_supervisor
    $disabledStatus = ' disabled-input';
}
?>
<div class="report-result">
    <div class="text-center">
        <input type="hidden" id="show-qc" data-modal-id="quality-control-modal" data-url="<?=URL::site('reports/quality_control/')?>"/>
        <img class="q4b-logo" src="/media/img/q4b_quality.png" alt="logo">
        <img class="q4b-logo" src="/media/img/q4b_logo.png" alt="logo">
    </div>
</div>
<div id="quality-control-modal" data-backdrop="static" data-keyboard="false" class="quality-control-modal modal fade" role="dialog" data-qcid="<?=$item->id?>">
    <div class="modal-dialog q4_project_modal modal-dialog-1070">
        <form id="qc-form-id" class="q4_form" action="<?=$formAction?>" data-ajax="true" data-submit="false" method="post">
            <input type="hidden" value="" name="x-form-secure-tkn"/>
            <div class="modal-content">
                <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                        <div class="clear"></div>
                    </div>
                    <div class="q4_modal_sub_header">
                        <h3><?=__('Quality control')?> #<?=$item->id?></h3>
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
                                            <input type="text" class="q4-select q4-form-input disabled-input" value="<?=$project->name?>"/>
                                        </div>
                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label"><?=__('Property')?></label>
                                            <input type="text" class="q4-select q4-form-input disabled-input" value="<?=$item->object->type->name.'-'.$item->object->name?>"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label"><?=__('Craft')?></label>
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <?$privileged = $item->userHasExtraPrivileges($_USER)?>
                                                <select name="craft_id" data-selected-crafts="<?=$item->craft_id?>" class="q4-select q4-form-input  disabled-input qc-craft q4_select <?=$privileged && !$disabled ? '' : 'disabled-input' ?>">
                                                    <?foreach ($crafts as $craft):
                                                        $selected = $item->craft_id == $craft->id ? "selected='selected'" : '';
                                                        $profs = $craft->professions->where('status','=',Enum_Status::Enabled)->find_all();
                                                        $p = [];
                                                        foreach ($profs as $pr)
                                                            $p []= $pr->id;
                                                        if(empty($p)) continue;
                                                        ?>
                                                        <option <?=$selected?> value="<?=$craft->id?>" data-professions="<?=implode(',',$p)?>"><?=__($craft->name)?></option>
                                                    <?endforeach;?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label"><?=__('Element')?></label>

                                            <input type="text" class="table_input disabled-input" value="<?=__($item->place->name)?>"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label"><?=__('Space/Place')?> <span class="q4-required">*</span>
                                            </label>
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <select class="q4-select q4-form-input disabled-input">
                                                    <?foreach($itemPlaceSpaces as $placeSpace):
                                                        $selected = $item->space_id == $placeSpace->id ? "selected='selected'" : '';
                                                        ?>
                                                        <option <?=$selected?>> <?='Space 1'.':'.$placeSpace->desc?></option>
                                                    <?endforeach;?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label"><?=__('Stage')?></label>
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <select class="q4-select q4-form-input disabled-input" name="project_stage">

                                                    <?foreach($projectStages as $stage):
                                                        $selected = $item->project_stage == $stage ? "selected='selected'" : '';
                                                        ?>
                                                        <option value="<?=$stage?>" <?=$selected?> ><?=__($stage)?></option>
                                                    <?endforeach;?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label"><?=__('Status')?> <span class="q4-required">*</span></label>
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <select class="qc-status q4-select q4-form-input disabled-input" data-selected="<?=$item->status?>" name="status">
                                                    <?foreach ($itemStatuses as $status) :
                                                        $selected = $item->status == $status ? "selected='selected'" : '';
                                                        ?>
                                                        <option  value="<?=$status?>" <?=$selected?>><?=__($status)?></option>
                                                    <?endforeach;?>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 rtl-float-right">
                                            <label class="table_label"><?=__('Responsible profession')?> <span class="q4-required">*</span></label>
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <select class="q4-select q4-form-input qc-profession disabled-input" name="profession_id">
                                                    <?foreach ($professions as $profession) :
                                                        $selected = $item->profession_id == $profession->id ? "selected='selected'" : '';
                                                        $crafts = $profession->crafts->where('status','=',Enum_Status::Enabled)->find_all();
                                                        $c = [];
                                                        foreach ($crafts as $cr)
                                                            $c []= $cr->id;
                                                        if(empty($c)) continue;
                                                        ?>
                                                        <option value="<?=$profession->id?>" data-crafts="<?=implode(',',$c)?>" <?=$selected?> ><?=__($profession->name)?></option>
                                                    <?endforeach;?>
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
                                                    <input type="text" class="table_input bidi-override disabled-input" value="<?=$item->place->floor->number?>"/>
                                                </div>
                                                <div class="col-25 rtl-float-right">
                                                    <label class="table_label table_label-small"><?=__('Element number')?></label>
                                                    <input type="text" class="table_input bidi-override disabled-input" value="<?=isset($itemPlace->custom_number) && $itemPlace->custom_number ? $itemPlace->custom_number : $itemPlace->number?>"/>
                                                </div>
                                                <div class="col-25 rtl-float-right">
                                                    <label class="table_label"><?=__('Element id')?></label>
                                                    <input type="text" class="table_input bidi-override disabled-input" value="<?=$itemPlace->number?>"/>
                                                </div>
                                                <div class="col-30 rtl-float-right">
                                                    <label class="table_label"><?=__('Due Date')?></label>
                                                    <div class="input-group form-group date" id="qc-due-date" data-provide="datepicker">
                                                        <div class="input-group-addon small-input-group">
                                                            <span class="glyphicon glyphicon-calendar"></span>
                                                        </div>
                                                        <input type="text" name="due_date" class="q4-form-input table_input disabled-input" data-date-format="DD/MM/YYYY" value="<?=date('d/m/Y', $item->due_date)?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="table_label"><?=__('Plan')?></label>
                                            <div class="qc-choose-plan invisible">
                                                <a href="#" data-toggle="modal" data-target="#choose-plan-modal" class="q4-btn-lg light-blue-bg"><?=__('Choose plan')?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="qc-plan-details visible ">
                                                <?if(!$disabled):?>
                                                    <div class="qc-change-plan">
                                                        <a href="#" data-toggle="modal" data-target="#choose-plan-modal"><?=__('Choose plan')?></a>
                                                    </div>
                                                <?endif?>
                                                <div class="property-quality-control-name">
                                                    <?if($plan->status):?>
                                                        <h4 class="table-modal-label-h4"><?=__('Plan name')?>: <?=$plan->file() ? $plan->file()->getName() : $plan->name?></h4>
                                                        <input type="hidden" class="modal-plan-id" name="plan_id" value="<?=$plan->id?>">
                                                        <div class="col-20">
                                                            <label class="table_label"><?=__('Edition')?></label>
                                                            <input type="text" name="qc_plan_edition" class="table_input disabled-input" value="<?=__($plan->edition)?>"/>
                                                        </div>
                                                        <div class="col-30">
                                                            <label class="table_label"><?=__('Date')?></label>
                                                            <input type="text" name="qc_plan_date" class="table_input disabled-input" value="<?=$plan->date ? date('d/m/Y', $plan->date) : ''?>"/>
                                                        </div>
                                                        <div class="col-50">
                                                            <label class="table_label"><?=__('Status')?></label>
                                                            <input type="text" name="qc_plan_status"  class="table_input disabled-input" value="<?=__($plan->status)?>"/>
                                                        </div>
                                                        <div class="clear"></div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="choose-view-format">
                                                                    <span class="choose-view-format-title"><?=__('Choose view format')?>: </span>
                                                                    <ul class="choose-view-format-list">
                                                                        <?$i = 0?>
                                                                        <?$file = $plan->files->where('status','=',Enum_FileStatus::Active)->find()?>
                                                                        <li>
                                                                            <a data-url="<?=URL::site('/projects/update_quality_control_plan_image/' . $item->id.'/'.$file->id)?>" data-fileid="<?=$file->id?>" data-ext="<?=$file->mime?>" data-controller="update_quality_control_plan_image"  class="call-lit-plugin" title="<?=$file->original_name?>"><img src="/media/img/choose-format/format-<?=strtolower($file->ext)?>.png" alt="<?=$file->ext?>"/></a>
                                                                        </li>
                                                                    </ul>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?endif?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="plans-modal-dialog-top relative error-handler">
                        <h4 class="table-modal-label-h4"><?=__('Tasks List')?></h4>
                        <div class="tasks-full-description-box">
                            <ul class="tasks-full-description qc-tasks-list">
                                <?$arrayTasks = [];
                                foreach ($itemTasks as $task) {
                                    $arrayTasks[] = $task->id;

                                }?>
                                <?$usedTasksAray = [];
                                foreach ($usedTasks as $task) {
                                    $usedTasksAray[] = $task->id;

                                }

                                ?>
                                <?foreach($tasks as $task):?>
                                    <?
                                    $crafts = $task->crafts->where('cmpcraft.status','=',Enum_Status::Enabled)->find_all();
                                    $c = [];
                                    foreach ($crafts as $cr){
                                        $c []= $cr->id;
                                    }
                                    ?>
                                    <li class="1-class<?=in_array($task->id, $arrayTasks) ? ' selected' :  (in_array($item->craft_id,$c) ? '' : ' hidden' )?> disabled-input" >
                                        <a href="#" data-id="<?=$task->id?>" >
                                            <span class="selected-tick"><i class="q4bikon-tick"></i></span>
                                            <h4><?=__('Task')?> <?=$task->id?></h4>
                                            <div class="task-item-txt">
                                                <?$desc = explode("\n",$task->name);
                                                foreach ($desc as $line) {?>
                                                    <p><?=html_entity_decode($line)?></p>
                                                <?}?>
                                            </div>
                                        </a>
                                    </li>
                                <?endforeach;?>
                            </ul>


                            <select class="hidden-select q4_select" name="tasks" data-selected-tasks="<?=implode(',', $arrayTasks)?>" multiple>
                                <?foreach($tasks as $task):?>
                                    <?php
                                    $crafts = $task->crafts->where('cmpcraft.status','=',Enum_Status::Enabled)->find_all();
                                    $c = [];
                                    foreach ($crafts as $cr){
                                        $c []= $cr->id;
                                    }
                                    if(empty($c)) continue;
                                    $taskId = $task->id;
                                    $usedCraftsArray = isset($usedTasks->$taskId)? $usedTasks->$taskId->crafts: [];
                                    ?>
                                    <option data-usedcrafts="<?=implode(',',$usedCraftsArray)?>" <?=in_array($task->id, $arrayTasks) ? 'selected="selected"' : '' ?> value="<?=$task->id?>" data-crafts="<?=implode(',',$c)?>"><?=$task->name?></option>
                                <?endforeach?>
                            </select>
                        </div>

                    </div>
                    <div class="plans-modal-dialog-top form_row">

                        <div class="row">
                            <?$disabledInput =
                                $item->status == "invalid" && $item->approval_status !== 'approved' ?
                                    '' : ' disabled-input'?>
                            <div class="form-group col-md-6 rtl-float-right">
                                <div class="mt-15 mb-15">
                                    <label class="table_label"><?=__('Conditions')?></label>
                                </div>
                                <div class="property-quality-control-conditions">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="table_label"><?=__('Severity Level')?></label>
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <select name="severity_level" class="q4-select q4-form-input disabled-input">
                                                    <?$selectedf = $item->severity_level && !$disabledInput  ? '' : ' selected="selected"' ?>
                                                    <option value=""<?=$selectedf?>><?=__('Please select')?></option>
                                                    <?foreach ($itemConditionLevels as $level): ?>
                                                        <?$selected = $item->severity_level == $level ?  ' selected="selected"':'' ?>
                                                        <option<?=$selected?> value="<?=$level?>"><?=__($level)?></option>
                                                    <?endforeach;?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="table_label "><?=__('Conditions List')?></label>
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <select name="condition_list" class="q4-select q4-form-input disabled-input">
                                                    <?$selectedf = $item->condition_list && !$disabledInput  ? '' : ' selected="selected"' ?>
                                                    <option value="" <?=$selectedf?>><?=__('Please select')?></option>
                                                    <?foreach ($itemConditionList as $condition):?>
                                                        <?$selected = $item->condition_list == $condition ?  ' selected="selected"':'' ?>
                                                        <option <?=$selected?> value="<?=$condition?>"><?=__($condition)?></option>
                                                    <?endforeach;?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-6 rtl-float-right">

                                <?if(!$disabled):?>

                                    <div class="wrap-image-lists">
                                        <div class="modal-images-list-box absoluted">
                                            <a href="#" class="inline_block_btn blue-light-button modal-load-images disabled-gray-button"><?=__('Load images')?></a>
                                            <div class="hide-upload">
                                                <input type="file" class="load-images-input" data-id="<?=uniqid()?>" multiple id="tasks-load-new-images" name="images[]" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="mt-15 mb-15">
                                            <label class="table_label"><?=__('Images List').'( <span class="count-fl-list">'.count($itemImages).'</span>)';?></label>
                                        </div>
                                    </div>
                                <?else:?>
                                    <div class="wrap-image-lists">
                                        <div class="modal-images-list-box absoluted">
                                            <a href="#" class="inline_block_btn blue-light-button modal-load-images disabled-input"><?=__('Load images')?></a>
                                            <div class="hide-upload">
                                                <input type="file" class="load-images-input" data-id="<?=uniqid()?>" multiple id="tasks-load-new-images" name="images[]" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="mt-15 mb-15">
                                            <label class="table_label"><?=__('Images List').'( <span class="count-fl-list">'.count($itemImages).'</span>)';?></label>
                                        </div>
                                    </div>
                                <?endif;?>

                                <!--                            <label class="table_label">--><?//=__('Images List')?><!--</label>-->
                                <div id="tasks-quality-control-images-list" class="modal-images-list-table form-group">
                                    <table>
                                        <tbody data-qcid="<?=$item->id?>">
                                        <?foreach ($itemImages as $number => $image):?>
                                            <tr>
                                                <?$imageW = Image::factory(DOCROOT.$image->originalFilePath());?>
                                                <td data-th="Image">
                                                    <span class="modal-tasks-image-action">
                                                        <a data-url="<?=URL::withLang($image->originalFilePath(),Language::getDefault()->iso2,'https')?>" data-controller="update_quality_control_image" data-ext="<?=$image->mime?>" data-fileid="<?=$image->id?>" title="<?=$image->original_name?>" class="call-lit-plugin" >
                                                        <span class="modal-tasks-image-number"><?=$number+1?>.</span>
                                                        <span class="modal-tasks-image-name"><?=$image->original_name?></span>
                                                         <span class="modal-img-upload-date">(<?=__('uploaded')?>: <?=date('d.m.Y',$image->created_at)?>)</span></a>
                                                    </span>
                                                </td>
                                                <td data-th="Download" class="modal-tasks-image-option">
                                                    <span class="modal-tasks-image-action">
                                                        <a href="<?=URL::withLang($image->originalFilePath(),Language::getDefault()->iso2,'https')?>" class="download_file disabled-gray-button" download="<?=$image->name?>" data-url="">
                                                            <i class="q4bikon-download"></i>
                                                        </a>
                                                    </span>
                                                </td>
                                                <?if(!$disabled):?>
                                                    <td data-th="Delete" class="modal-tasks-image-option">
                                                        <span class="modal-tasks-image-action">
                                                            <span class="delete_row disabled-gray-button" data-url="<?=URL::site('/projects/delete_quality_control_file/'.$project->id).'/'.$item->id.'/'.$image->token?>"><i class="q4bikon-delete"></i></span>
                                                        </span>
                                                    </td>
                                                <?endif?>
                                            </tr>
                                        <?endforeach?>
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

                            <div class="form-group">
                                <div style="max-height:120px" class="modal-images-list-table">
                                    <?
                            echo "<div style='margin-bottom:7px' class='light-blue'>".'
                                                    <div style="padding-left: 20px">'.trim($item->description).'</div>
                                                </div>';?>
                                    <?$comments = $item->comments->find_all()?>
                                    <?if(count($comments)):?>
                                        <? foreach ($comments as $comment) {
                                $class = $createUsr->id == $comment->owner->id ? 'light-blue':'dark-blue';
                                echo "<div style='margin-bottom:7px' class='$class'>".date("d/m/Y H:i",$comment->created_at).'
                                                    <div style="padding-left: 20px">'.$comment->owner->name.': '.$comment->message.'</div>
                                                </div>';
                            }
                                ?>
                                    <?endif?>
                                </div>
                            </div>
                            <textarea maxlength="250" style="height:80px"  placeholder="<?=__('Leave your comment')?>" name="message" class='modal-plans-details-textarea'></textarea>
                        </div> -->

                            <div class="form-group col-md-6 rtl-float-right">

                                <label class="table_label"><?=__('Description')?></label>

                                <div class="form-group">
                                    <textarea name="description" class='modal-plans-details-textarea q4_required disabled-input '><?=trim(html_entity_decode($item->description))?></textarea>
                                </div>
                            </div>
                            <div class="form-group col-md-6 rtl-float-right">
                                <div class="quality-control-actions-list">
                                    <div class="modal-details-actions">
                                        <div class="modal-details-actions-list">
                                            <?if($item->approved_by):?>
                                                <div>
                                                    <span class="modal-details-action blue"><?=__('Viewed by')?> : </span>
                                                    <span class="modal-details-action-status blue"><?=$approveUsr->name?>&#x200E; <span class="d-color">(<?=date('d.m.Y H:ia',$item->approved_at)?>)&#x200E;</span></span>
                                                </div>
                                            <?endif;?>
                                            <div>
                                                <span class="modal-details-action blue"><?=__('Status')?> : </span>
                                                <span class="modal-details-action-status s-box">
                                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>

                                                    <?if($isSuperAdmin):?>
                                                        <select class="q4-select q4-status-select q4-form-input q4-status-<?=$item->approval_status?> disabled-input" data-status="<?=$item->approval_status?>" name="approval_status">
                                                        <?foreach ($approveStatusList as $status):
                                                            $selected = $item->approval_status == $status ? "selected='selected'" : '';
                                                            ?>
                                                            <?if($status == 'waiting' && $item->approval_status!='waiting') continue;
                                                            if($item->status == "invalid" && $status =="approved")continue;
                                                            ?>

                                                            <option class="q4-status-<?=$status?>" <?=$selected?> value="<?=$status?>"><?=__($status)?></option>


                                                        <?endforeach;?>
                                                        </select>
                                                    <?else:?>
                                                        <select class="q4-select q4-status-select q4-form-input disabled-input q4-status-<?=$item->approval_status?>" data-status="<?=$item->approval_status?>" name="approval_status">
                                                            <?$current = 0?>
                                                            <?foreach ($approveStatusList as $status):
                                                                $selected = $item->approval_status == $status ? "selected='selected'" : '';
                                                                if($selected){//чтобы видно были только статусы идущие после текущего
                                                                    $current = 1;
                                                                }
                                                                if($current>0):?>
                                                                    <?if($item->status == "invalid" && $status =="approved") continue?>
                                                                    <option class="q4-status-<?=$status?>" <?=$selected?> value="<?=$status?>"><?=__($status)?></option>
                                                                <?endif;?>
                                                            <?endforeach;?>
                                                        </select>
                                                    <?endif;?>
                                                </div>
                                            </span>
                                            </div>
                                            <div>
                                                <span class="modal-details-action blue"><?=__('Created by')?> : </span>
                                                <span class="modal-details-action-status blue"><?=$createUsr->name?>&#x200E; <span class="d-color">(<?=date('d.m.Y H:ia',$item->created_at)?>)&#x200E;</span></span>
                                            </div>
                                            <?if($item->updated_by):?>
                                                <div>
                                                    <span class="modal-details-action blue"><?=__('Updated by')?> : </span>
                                                    <span class="modal-details-action-status blue"><?=$updateUsr->name?> &#x200E;<span class="d-color">(<?=date('d.m.Y H:ia',$item->updated_at)?>)&#x200E;</span></span>
                                                </div>
                                            <?endif;?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-modal-footer text-align">
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="#" data-id="<?=$item->id?>" class="q4-btn-lg light-blue-bg qc-to-print-btn panel-footer-first mb-15"><?=__('Proceed to print')?></a>
                            <a href="#" class="q4-btn-lg light-blue-bg send-reports panel-footer-second ml-15 mb-15" data-url=<?=URL::site('reports/quality_control_mailing/'.$item->id)?>><?=__('Proceed to send')?></a>
                            <?if($isSuperAdmin):?>
                                <a  class="q4-btn-lg btn-confirm red q4-delete-qc ml-15 mb-15 disabled-gray-button" data-url="<?=URL::site('projects/quality_control_delete/'.$item->id)?>" ><?= __('Delete')?></a>
                            <?endif?>
                            <a  href="#" class="q4-btn-lg ml-15 mb-15 orange q4_form_submit disabled-gray-button <?=$disabled && !$isSuperAdmin ? '' : ' q4-form-submit'?>" <?=$disabled && !$isSuperAdmin ? 'data-dismiss="modal"' : ""?> data-url="<?=$formAction?>"><?=$disabled && !$isSuperAdmin ? __('Close') : __('Update')?></a>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
    <div class="print-quality-control print-quality-control-<?=$item->id?>">
        <!-- ****** PRINTABLE PART ********-->
        <div class="page-break">
            <div class="text-right">
                <div class="printable-logo">
                    <img src="/media/img/logo.png" alt="logo">
                </div>
            </div>

            <h3><?=__('Quality control')?>-<?=$printableItem->id?></h3>

            <div class="print-col-50">
                <ul class="print-quality-control-list">
                    <li>
                            <span>
                                <i class="icon q4bikon-project"></i>
                                <?=__('Project name')?>:
                            </span>
                        <span><strong><?=$project->name?></strong></span>
                    </li>
                    <li>
                            <span>
                                <i class="q4bikon-property"></i>
                                <?=__('Property')?>:
                            </span>
                        <span><strong><?=$printableItem->object->type->name?> - <?=$printableItem->object->name?> </strong></span>
                    </li>
                    <li>
                            <span>
                                <i class="q4bikon-appartment"></i>
                                <?=__('Element')?>:
                            </span>
                        <span><strong><?=__($printableItem->place->name)?></strong></span>
                    </li>
                    <li>
                            <span>
                                <i class="q4bikon-stairway"></i>
                                <?=__('Stage')?>:
                            </span>
                        <span><strong><?=__($printableItem->project_stage)?></strong></span>
                    </li>
                    <li>
                            <span>
                                <i class="q4bikon-company_status"></i>
                                <?=__('Status')?>:
                            </span>
                        <span><strong><?=__($printableItem->status)?></strong></span>
                    </li>
                    <li>
                            <span>
                                <i class="q4bikon-date"></i>
                                <?=__('Due Date')?>:
                            </span>
                        <span><strong><?=date('d/m/Y', $printableItem->due_date)?></strong></span>
                    </li>
                </ul>
            </div>

            <div class="print-col-50">
                <ul class="print-quality-control-list">
                    <li>
                            <span>
                                <i class="q4bikon-owner"></i>
                                <?=__('Responsible person(s)')?>:
                            </span>
                        <span><strong><?=__($printableItem->profession->name)?></strong></span>
                    </li>
                    <li>
                            <span>
                                <i class="q4bikon-position"></i>
                                <?=__('Craft')?>:
                            </span>
                        <span><strong><?=__($printableItem->craft->name)?></strong></span>
                    </li>
                    <li>
                            <span>
                                <i class="q4bikon-appartment"></i>
                                <?=__('Space/Place')?>:
                            </span>
                        <span><strong><?=__($printableItem->place->name)?></strong></span>
                    </li>
                    <li>
                            <span>
                                <i class="q4bikon-address"></i>
                                <?=__('Floor')?>:
                            </span>
                        <span><strong><?=$printableItem->place->floor->number?></strong></span>
                    </li>
                    <li>
                            <span>
                                <i class="q4bikon-quantity"></i>
                                <?=__('Element number')?>:
                            </span>
                        <span><strong><?=$itemPlace->number?></strong></span>
                    </li>
                </ul>
            </div>

            <!-- <?$comments = $printableItem->comments->find_all()?>
                <?if(count($comments)):?>
                    <div class="form-group">
                        <label class="table_label"><?=__('Comments')?></label>
                        <?if($printableItem->description):?>
                            <div class="textarea-div small">
                            <?$desc = explode("\n",$printableItem->description);
                foreach ($desc as $line) {?>
                                <p><?=$line?></p>
                            <?}?>
                            </div>
                        <?endif?>
                        <div class="textarea-div small">
                            <?foreach ($comments as $comment) {
                $class = $createUsr->id == $comment->owner->id ? 'bold':'normal';
                echo "<div style='margin-bottom:7px;font-weight:$class'> ".date("d/m/Y H:i",$comment->created_at)
                    .$comment->owner->name.': '.$comment->message.
                    '</div>';
            }
                ?>
                        </div>
                    </div>
                <?endif?> -->
            <?if(strlen($printableItem->description)>1):?>
                <div class="form-group">
                    <label class="table_label"><?=__('Description')?></label>

                    <?$desc = explode("\n",$printableItem->description);
                    foreach ($desc as $line) {?>
                        <p><?=$line?></p>
                    <?}?>

                </div>
            <?endif?>
            <div class="q4-copyright">
                <span>
                    <!-- <?=__('Powered by')?> <img src="/media/img/company-logo-c.png" alt="company logo" class="q4-copyright-img"> -->
                    <?=__('Copyright © 2017 Q4B').__('All right reserved')?>
                </span>
            </div>
        </div>


        <?if($printableItem->plan->files->where('status','=',Enum_FileStatus::Active)->find()->loaded()):?>
            <div class="report-plan-properties">
                <span><?=__('Plan name')?> : <?=__($printableItem->plan->name)?></span>|
                <span> <?=__('Status')?>: <?=__($printableItem->plan->status)?></span>|
                <span> <?=__('Date')?>: <?=date('d/m/Y',$printableItem->plan->created_at)?> </span>
            </div>
        <?endif?>


        <? foreach ($printableItem->images->where('status','=',Enum_FileStatus::Active)->find_all() as $number => $img):?>
            <?if(($number+2)%2==0):?>
                <div class='f0'>
            <?endif;?>
            <div class="print-col-50">
                <div class="report-plan-printableItem-image">
                    <h4 class="report-plan-title">
                        <?=$img->original_name?> <span class="report-plan-uploaded">(<?=__('uploaded')?>: <?=date('d.m.y, H:iA',$img->created_at)?> )&#x200E;</span></h4>

                    <div class="print-main-small-image-wrapper">
                        <img src="<?=$img->originalFilePath()?>" alt="<?=$img->original_name?>">
                    </div>

                </div>
            </div>
            <?if(($number+2)%2==1):?>
                </div>
            <?endif;?>
        <?endforeach?>


        <!--  ****** end of PRINTABLE PART ******** -->
    </div>
</div>
<div id="choose-plan-modal" data-backdrop="static" data-keyboard="false" class="modal fade no-delete" role="dialog">
    <div class="modal-dialog choose-plan-dialog modal-dialog-1070">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header q4_modal_header">
                <div class="q4_modal_header-top">
                    <button type="button" class="close q4-close-modal q4-close-child-modal" ><i class="q4bikon-close"></i></button>
                    <div class="clear"></div>
                </div>
                <div class="q4_modal_sub_header">
                    <h3><?=__('Choose a plan')?></h3>
                </div>
            </div>
            <div class="modal-body bb-modal">
                <div class="scrollable-table">
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
                        <?$i= '';?>
                        <?foreach($plans as $plan):?>
                            <?

                            $crafts = [];
                            foreach ($plan->crafts->find_all() as $craft) {
                                $crafts[] = $craft->id;
                            }
                            ?>
                            <tr data-crafts='<?=json_encode($crafts)?>' class="<?=in_array($item->craft_id,$crafts) OR empty($crafts) ? '' : 'hidden'?>">
                                <td class="enable-plan-action align-center-left td-50" data-th="Select">
                                    <div class="div-cell">
                                        <label class="q4-radio">
                                            <input  name="plan" type="radio" value="<?=$plan->id?>" name="plan" data-img="<?=$plan->files->where('status','=',Enum_FileStatus::Active)->find()->getImageLink()?>">
                                            <span> </span>
                                        </label>
                                    </div>


                                    <div class="pln-data hide">
                                        <input type="hidden" name="plan_id" value="<?=$plan->id?>"/>
                                        <h4 class="table-modal-label-h4"><?=__('Plan name')?>: <?=$plan->name ? $plan->name : $plan->file()->getName() ?></h4>
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
                                                        <?$i = 0?>
                                                        <?$file = $plan->files->where('status','=',Enum_FileStatus::Active)->find()?>
                                                        <li>
                                                            <a data-url="<?=$file->getImageLink()?>" data-fileid="<?=$file->id?>" data-ext="<?=$file->mime?>" data-controller="update_quality_control_plan_image"  class="call-lit-plugin" title="<?=$file->original_name?>"><img src="/media/img/choose-format/format-<?=strtolower($file->ext)?>.png" alt="<?=$file->ext?>"/></a>

                                                        </li>

                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td data-th="Name/Type">
                                    <div class="div-cell break-c">
                                        <?=$plan->name?>
                                    </div>
                                </td>
                                <td data-th="Profession">
                                    <div class="div-cell">
                                        <?=$plan->profession->name?>
                                    </div>
                                </td>
                                <td data-th="Floor">
                                    <div class="div-cell">
                                        <?=$plan->getFloorsAsString() ? :'-'?>
                                    </div>
                                </td>
                                <td data-th="Element number">
                                    <div class="div-cell">
                                        <?if($plan->place_id):?>
                                            <?=isset($plan->place->custom_number) ? $plan->place->custom_number : $plan->place->number?>
                                        <?else:?>
                                            -
                                        <?endif?>
                                    </div>
                                </td>
                                <td data-th="Edition">
                                    <div class="div-cell">
                                        <?=$plan->edition?>
                                    </div>
                                </td>
                                <td data-th="Date">
                                    <div class="div-cell">
                                        <?=date('d/m/Y',$plan->date)?>
                                    </div>
                                </td>
                                <td data-th="Image">

                                    <?$i = 0; $ext = null?>
                                    <?foreach ($plan->files->where('status','=',Enum_FileStatus::Active)->find_all() as $img):?>
                                        <?if($i > 1) break?>
                                        <?if($img->ext != $ext) $ext = $img->ext; else continue?>
                                        <a href="<?=$img->originalFilePath()?>" target="_blank" title="<?=$img->original_name?>"><img src="/media/img/choose-format/format-<?=strtolower($img->ext)?>.png" alt="<?=$img->ext?>"/></a>
                                    <?endforeach;?>
                                </td>
                            </tr>
                        <?endforeach;?>
                        </tbody>
                    </table>


                </div>
            </div>
            <div class="panel-modal-footer text-right">
                <div class="row">
                    <div class="col-sm-12">
                        <a href="#" class="inline_block_btn blue-light-button confirm-plan"><?=__('Confirm')?></a>
                    </div>
                </div>
            </div>
        </div><!-- Modal content close-->

    </div>
</div>
