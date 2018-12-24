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
<?$disabled = $item->approval_status=="approved" ? ' disabled-input' : ''?>
<?$disabledInput = $item->status == "invalid" && $item->approval_status != 'approved' ? '' : ' disabled-input';
$disabledStatus = $item->approval_status=="approved" ? ' disabled-input' : '';
$isSuperAdmin = Auth::instance()->get_user()->is('super_admin');

if(Auth::instance()->get_user()->is('project_supervisor')){//запрет на изменение статуса для project_supervisor
    $disabledStatus = ' disabled-input';
}
?>

<div id="quality-control-modal-mobile"  data-backdrop="static" data-keyboard="false" class="quality-control-modal modal fade" role="dialog" data-qcid="<?=$item->id?>">
    <div class="modal-dialog q4_project_modal quality-control-dialog-mobile q4-mobile-layout">
        <form action="<?=$formAction?>" data-ajax="true" data-submit="false" method="post">
            <input type="hidden" value="" name="x-form-secure-tkn"/>
            <div class="modal-content">
                <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                        <div class="clear"></div>
                    </div>
                    <div class="q4_modal_sub_header">
                        <h3><?=__('Quality control ')?>#<?=$item->id?></h3>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <ul class="qc-mobile-list">
                            <li>
                               <i class="icon q4bikon-project light-blue"></i>
                                <span class="qc-mobile-list-text"> <?=$project->name?></span>
                            </li>
                            <li>
                                <i class="icon q4bikon-property light-blue"></i>
                                <span class="qc-mobile-list-text"><?=$item->object->name?></span>
                            </li>
                            <li>
                                <i class="icon q4bikon-appartment light-blue"></i>
                                <span class="qc-mobile-list-text"> <?=__($item->place->name)?></span> | </span><span class="qc-mobile-list-text"><?=__('Element number')?>:<span class="bidi-override"><?=!empty($itemPlace->custom_number) ? $itemPlace->custom_number : $itemPlace->number?></span></span>
                            </li>
                            <li>
                                <i class="icon q4bikon-stairway light-blue"></i>
                                <span class="qc-mobile-list-text"><?=__('Floor')?>: <span class="bidi-override"><?=$item->place->floor->number?></span></span> <span class="reports-prop-title-divider"> | </span><span class="qc-mobile-list-text"><?=__('Element id')?>:<span class="bidi-override"><?=$itemPlace->number?></span></span>
                            </li>
                        </ul>
                    </div>
                    <div class="form-group">
                        <ul class="qc-mobile-list">
                            <li>
                                <label class="table_label dark-blue"><?=__('Crafts')?> <span class="q4-required">*</span></label>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <?$privileged = $item->userHasExtraPrivileges( Auth::instance()->get_user())?>
                                    <select name="craft_id" class="q4-select q4-form-input qc-craft q4_select <?=$privileged && !$disabled? '' : 'disabled-input' ?>">
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
                            </li>
                            <li>
                                <label class="table_label dark-blue"><?=__('Space')?> <span class="q4-required">*</span></label>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <select class="q4-select q4-form-input disabled-input">
                                        <?foreach($itemPlaceSpaces as $placeSpace):
                                        $selected = $item->space_id == $placeSpace->id ? "selected='selected'" : '';
                                        ?>
                                            <option <?=$selected?>> <?='Space 1'.':'.$placeSpace->desc?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </li>
                            <li>
                                <label class="table_label dark-blue"><?=__('Stage')?> <span class="q4-required">*</span></label>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <select class="q4-select q4-form-input <?=$disabled?>" name="project_stage">
                                        <?foreach($projectStages as $stage):
                                            $selected = $item->project_stage == $stage ? "selected='selected'" : '';
                                            ?>
                                            <option value="<?=$stage?>" <?=$selected?> ><?=__($stage)?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </li>
                            <li>
                                <label class="table_label dark-blue"><?=__('Status')?> <span class="q4-required">*</span></label>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <select class="qc-status q4-select q4-form-input <?=$disabled?>" name="status">
                                        <?foreach ($itemStatuses as $status) :
                                            $selected = $item->status == $status ? "selected='selected'" : '';
                                        ?>
                                            <option  value="<?=$status?>" <?=$selected?>><?=__($status)?></option>
                                        <?endforeach;?>

                                    </select>
                                </div>
                            </li>
                            <li>
                                <label class="table_label dark-blue"><?=__('Responsible person')?> <span class="q4-required">*</span></label>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <select class="q4-select q4-form-input qc-profession <?=$disabled?>" name="profession_id">
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
                            </li>
                            <li>
                                <label class="table_label"><?=__('Due Date')?></label>
                                <div class="input-group form-group date" id="qc-due-date" data-provide="datepicker">
                                    <div class="input-group-addon small-input-group">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </div>
                                    <input type="text" name="due_date" class="q4-form-input table_input<?=$disabled?>" data-date-format="DD/MM/YYYY" value="<?=date('d/m/Y', $item->due_date)?>"/>
                                </div>
                            </li>
                        </ul>
                    </div>


                    <div class="form-group qc-border-bottom">
                        <label class="table_label"><?=__('Plan')?></label>
                        <div class="qc-plan-details visible ">
                            <div class="property-quality-control-name">
                            <?if(!$plan->loaded() && !$disabled):?>
                            <div class="qc-choose-plan">
                                <a href="#" data-toggle="modal" data-target="#choose-plan-modal-mobile" class="q4-btn-lg light-blue-bg choose-plan-mobile"><?=__('Choose Plan')?></a>
                            </div>
                            <br>
                                <?elseif(!is_null($plan) AND $plan->loaded()):?>
                                    <?if(!$disabled):?>
                                        <div class="qc-change-plan">
                                            <a href="#" data-toggle="modal" data-target="#choose-plan-modal-mobile"><?=__('Choose plan')?></a>
                                        </div>
                                    <?endif?>
                                    <h4><?=__('Plan name')?>: <?=$plan->file() ? $plan->file()->getName() : $plan->name?></h4>
                                    <ul class="qc-plan-details-list">
                                        <li>
                                            <i class="icon q4bikon-project"></i>
                                            <span class="qc-plan-details-list-text"><?=__('Edition')?> : <?=__($plan->edition)?></span>
                                        </li>
                                        <li>
                                            <i class="icon q4bikon-date"></i>
                                            <span class="qc-plan-details-list-text"><?=__('Date')?> : <?=$plan->date ? date('d/m/Y', $plan->date) : ''?></span>
                                        </li>
                                        <li>
                                            <i class="icon q4bikon-company_status"></i>
                                            <span class="qc-plan-details-list-text"><?=__('Status')?> : <?=__($plan->status)?></span>
                                        </li>
                                    </ul>
                                    <div class="choose-view-format">
                                        <span class="choose-view-format-title"><?=__('Click to view')?>: </span>
                                        <ul class="choose-view-format-list">
                                             <?$i = 0?>
                                            <?$file = $plan->files->where('status','=',Enum_FileStatus::Active)->find() ?>
                                            <li>
                                                <a data-url="<?=URL::site('/projects/update_quality_control_plan_image/' . $item->id.'/'.$file->id)?>" data-fileid="<?=$file->id?>" data-ext="<?=$file->mime?>" data-controller="update_quality_control_plan_image"  class="call-lit-plugin" title="<?=$file->original_name?>"><img src="/media/img/choose-format/format-<?=strtolower($file->ext)?>.png" alt="<?=$file->ext?>"/></a>

                                            </li>
                                        </ul>
                                    </div>
                                <?endif;?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group qc-border-bottom relative error-handler">
                            <?$arrayTasks = [];
                                    foreach ($itemTasks as $task) {
                                        $arrayTasks[] = $task->id;

                                    }?>

                            <div data-structurecount="<?=count($arrayTasks)?>" class="tasks-full-description-mobile q4-owl-carousel qc-tasks-list-mobile">
                                <?foreach($tasks as $task):?>
                                <?
                                $crafts = $task->crafts->where('cmpcraft.status','=',Enum_Status::Enabled)->find_all();
                                $c = [];
                                foreach ($crafts as $cr){
                                    $c []= $cr->id;
                                }
                                ?>
                                    <div class="item<?=in_array($task->id, $arrayTasks) ? ' selected' :  (in_array($item->craft_id,$c) ? '' : ' hidden' )?>">
                                        <a href="#" data-id="<?=$task->id?>" >
                                            <span class="selected-tick"><i class="q4bikon-tick"></i></span>
                                            <h4><?=__('Task')?> <?=$task->id?></h4>
                                            <div class="task-item-txt">
                                                <?$desc = explode("\n",$task->name);
                                                foreach ($desc as $line) {?>
                                                    <p><?=$line?></p>
                                                <?}?>
                                            </div>
                                        </a>
                                    </div>
                                <?endforeach;?>
                            </div>
                            <select class="hidden-select q4_select" name="tasks" multiple>
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

                    <div class="form-group">
                        <label class="table_label"><?=__('Conditions')?></label>
                        <div class="conditions property-quality-control-conditions">
                            <div class="form-group">
                                <span class="conditions-list"><?=__('Severity Level')?>:</span>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <select name="severity_level" class="q4-select q4-form-input <?=$disabledInput?> ">
                                    <?$selectedF = $item->severity_level && !$disabledInput  ? '' : ' selected="selected"' ?>
                                        <option value=""<?=$selectedF?>><?=__('Please select')?></option>
                                    <?foreach ($itemConditionLevels as $level): ?>
                                        <?$selected = $item->severity_level == $level ?  ' selected="selected"':'' ?>
                                        <option<?=$selected?> value="<?=$level?>"><?=__($level)?></option>
                                    <?endforeach;?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <span class="conditions-list"><?=__('Conditions List')?>:</span>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <select name="condition_list" class="q4-select q4-form-input <?=$disabledInput?>">
                                         <?$selectedF = $item->condition_list && !$disabledInput  ? '' : ' selected="selected"' ?>
                                        <option value="" <?=$selectedF?>><?=__('Please select')?></option>
                                       <?foreach ($itemConditionList as $condition):?>
                                       <?$selected = $item->condition_list == $condition ?  ' selected="selected"':'' ?>
                                        <option <?=$selected?> value="<?=$condition?>"><?=__($condition)?></option>
                                    <?endforeach;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group qc-border-bottom">
                        <label class="table_label"><?=__('Images List')?></label>

                        <div data-structurecount="<?=count($itemImages)?>" class="qc-image-list-mobile q4-owl-carousel">
                             <?foreach ($itemImages as $number => $image):?>
                              <?$imageW = Image::factory(DOCROOT.$image->originalFilePath());?>
                                <div class="item qc-image-list-mobile-item">
                                    <a data-url="<?=$image->originalFilePath()?>" data-controller="update_quality_control_image" data-ext="<?=$image->mime?>" data-width="<?=$imageW->width?>" data-height="<?=$imageW->height?>" data-fileid="<?=$image->id?>" title="<?=$image->original_name?>" class="call-lit-plugin">
                                        <span class="modal-tasks-image-number"><?=$number+1?>&nbsp;</span>
                                        <span class="modal-tasks-image-name"><?=$image->original_name?></span>
                                        <span class="modal-img-upload-date">
                                            (<?=__('uploaded').':'.date('d.m.Y',$image->created_at)?>)&#x200E;
                                        </span>
                                    </a>
                                    <div class="qc-image-list-mobile-item-options">
                                        <span class="circle-sm red delete_row" data-url="<?=URL::site('/projects/delete_quality_control_file/'.$project->id).'/'.$item->id.'/'.$image->token?>">
                                            <i class="q4bikon-delete"></i>
                                        </span>
                                    </div>

                                </div>
                            <?endforeach?>
                        </div>
                        <?if(!$disabled):?>
                            <div class="modal-images-list-box">
                                <a href="#" class="q4-btn-lg light-blue-bg modal-load-images quality-control-load-images"><?=__('Load images')?></a>
                                 <div class="hide-upload">
                                   <input type="file" accept="image/*" class="load-images-input" data-id="<?=uniqid()?>" multiple name="images[]">
                                </div>
                            </div>
                        <?endif;?>
                    </div>


                    <!-- <div class="form-group">
                        <label class="table_label"><?=__('Comments')?></label>
                         <div class="form-group">
                                <?echo "<div style='margin-bottom:7px' class='light-blue'>".'
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
                        <textarea maxlength="255" style="height:100px" placeholder="<?=__('Leave your comment')?>" name="message" class='modal-plans-details-textarea'></textarea>
                    </div> -->

                    <div class="form-group relative">
                        <label class="table_label"><?=__('Description')?></label>
                        <textarea name="description" class="modal-details-textarea-mobile q4_required"><?=trim($item->description)?></textarea>
                    </div>

                    <div class="form-group qc-border-bottom">
                        <ul class="approve-staff">
                            <?if($item->approved_by):?>
                                <li>
                                    <span class="approve-key"><?=__('Viewed by')?>:<?=$approveUsr->name?> </span>
                                    <span class="approve-date">(<?=date('d.m.Y H:ia',$item->approved_at)?>)&#x200E;</span>
                                </li>
                            <?endif;?>
                            <li>
                                <span class="approve-key "><?=__('Status')?>:  </span>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <?if($isSuperAdmin):?>
                                        <select class="q4-select q4-status-select q4-form-input q4-status-<?=$item->approval_status?>" data-status="<?=$item->approval_status?>" name="approval_status">

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
                                        <select class="q4-select q4-status-select q4-form-input <?=$disabledStatus?> q4-status-<?=$item->approval_status?>" data-status="<?=$item->approval_status?>" name="approval_status">
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
                            </li>
                            <li>
                                <span class="approve-key"><?=__('Created by')?>: <?=$createUsr->name?></span>
                                <span class="approve-date">(<?=date('d.m.Y H:ia',$item->created_at)?>)&#x200E;</span>
                            </li>
                            <li>
                                <span class="approve-key"><?=__('Updated by')?>: <?=$updateUsr->name?> &#x200E;</span>
                                <span class="approve-date">(<?=date('d.m.Y H:ia',$item->updated_at)?>)&#x200E;</span>
                            </li>

                        </ul>
                    </div>

                </div>

                <div class="modal-footer text-align">
                    <div class="form-group">
                        <a href="#" class="q4-btn-lg light-blue-bg  qc-to-print-btn"><?=__('Proceed to print')?></a>
                    </div>
                    <div class="form-group">
                        <a href="#" class="q4-btn-lg light-blue-bg send-reports" data-url=<?=URL::site('reports/quality_control_mailing/'.$item->id)?>><?=__('Proceed to send')?></a>
                    </div>

                    <?if($isSuperAdmin):?>
                        <div class="form-group">
                            <a  class="q4-btn-lg btn-confirm red q4-delete-qc" data-url="<?=URL::site('projects/quality_control_delete/'.$item->id)?>" ><?= __('Delete')?></a>
                        </div>
                    <?endif?>
                    <div class="form-group">
                        <a  href="#" class="q4-btn-lg orange q4_form_submit<?=$disabled && !$isSuperAdmin ? '' : ' q4-form-submit'?>" <?=$disabled && !$isSuperAdmin ? 'data-dismiss="modal"' : ""?> data-url="<?=$formAction?>"><?=$disabled && !$isSuperAdmin ? __('Close') : __('Update')?></a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="print-quality-control">

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
                    <span><strong><?=$printableItem->object->type->name?></strong></span>
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
                <h4 class="table-modal-label-h4"><?=__('Comments')?></h4>
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
                <div class="textarea-div small">
                    <?$desc = explode("\n",$printableItem->description);
                    foreach ($desc as $line) {?>
                        <p><?=$line?></p>
                    <?}?>
                </div>
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
<!--end of .quality-control-modal-mobile-->



<!--.choose-plan-modal-mobile-->
<div id="choose-plan-modal-mobile" data-backdrop="static" data-keyboard="false" class="modal fade no-delete" role="dialog">
    <div class="modal-dialog q4_project_modal choose-plan-dialog-mobile">
        <div class="modal-content">
            <div class="modal-header q4_modal_header">
                <div class="q4_modal_header-top">
                    <button type="button" class="close q4-close-modal"><i class="q4bikon-close"></i></button>
                    <div class="clear"></div>
                </div>
                <div class="q4_modal_sub_header">
                    <h3><?=__('Choose Plan')?></h3>
                </div>
            </div>
            <div class="modal-body">
            <!--     <div class="form-group">
                    <label class="table_label dark-blue"><?=__('Choose Profession')?> <span class="q4-required">*</span></label>
                    <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                        <select class="q4-select q4-form-input">
                            <option value="">Choose profession</option>
                            <option value="Painter">Painter</option>
                            <option value="Carpenter">Carpenter</option>
                            <option value="Welder">Welder</option>
                        </select>
                    </div>
                </div> -->
               <!--  <div class="form-group">
                    <span class="q4-list-items-result"><?=__('tap to plan name to select a plan')?> </span>
                </div> -->



                <div class="q4-carousel-table-wrap">

                    <div class="q4-carousel-table" data-structurecount="<?=count($plans)?>">

                        <?foreach ($plans as $plan):?>

                            <?
                                $crafts = [];
                                foreach ($plan->crafts->find_all() as $craft) {
                                    $crafts[] = $craft->id;
                                }
                            ?>
                            <div class="item" data-crafts='<?=json_encode($crafts)?>' class="<?=in_array($item->craft_id, $crafts) ? '' : 'hidden'?>">

                                <div class="hidden pln-data">
                                    <div class="qc-change-plan">
                                        <a href="#" data-toggle="modal" data-target="#choose-plan-modal-mobile"><?=__('Choose plan')?></a>
                                    </div>
                                    <ul class="qc-plan-details-list">
                                        <input type="hidden" name="plan_id" value="<?=$plan->id?>"/>
                                        <li>
                                            <span class="qc-plan-details-list-text blue-head-title"><?=__('Plan name')?>: <?=$plan->file() ? $plan->file()->getName() : $plan->name?></span>
                                        </li>
                                        <li>
                                            <i class="icon q4bikon-project"></i>
                                            <span class="qc-plan-details-list-text"><?=__('Edition')?> : <?=__($plan->edition)?></span>
                                        </li>
                                        <li>
                                            <i class="icon q4bikon-date"></i>
                                            <span class="qc-plan-details-list-text"><?=__('Date')?> : <?=$plan->created_at ? date('d/m/Y', $plan->created_at) : ''?></span>
                                        </li>
                                        <li>
                                            <i class="icon q4bikon-company_status"></i>
                                            <span class="qc-plan-details-list-text"><?=__('Status')?> : <?=__($plan->status)?></span>
                                        </li>
                                    </ul>
                                    <div class="choose-view-format">
                                        <span class="choose-view-format-title"><?=__('Click to view')?>: </span>
                                        <?$file = $plan->files->where('status','=',Enum_FileStatus::Active)->find()?>
                                        <?if($file):?>
                                            <ul class="choose-view-format-list">
                                                <li>
                                                    <a data-url="<?=$file->getImageLink()?>" data-fileid="<?=$file->id?>" data-ext="<?=$file->mime?>" data-controller="update_quality_control_plan_image"  class="call-lit-plugin" title="<?=$file->original_name?>">
                                                        <img src="/media/img/choose-format/format-<?=strtolower($file->ext)?>.png" alt="<?=$file->ext?>"/>
                                                    </a>

                                                </li>
                                            </ul>
                                        <?else:?>
                                            <span><?=__('No files')?></span>
                                        <?endif?>
                                    </div>
                                </div>

                                <div class="q4-carousel-blue-head">
                                    <span class="blue-head-title"><?=__('Name').' : '.$plan->file() ? $plan->file()->getName() : $plan->name?></span>

                                    <div class="blue-head-option q4-radio-tick">
                                        <input id="rd-<?=$plan->id?>" name="plan" type="radio" value="<?=$plan->id?>">
                                        <label for="rd-<?=$plan->id?>"></label>
                                    </div>

                                </div>
                                <div class="q4-carousel-row f0">
                                    <div class="q4-mobile-table-key">
                                        <?=__('Profession')?>
                                    </div>
                                    <div class="q4-mobile-table-value">
                                        <?=$plan->profession->name?>
                                    </div>
                                </div>
                                <div class="q4-carousel-row f0">
                                    <div class="q4-mobile-table-key">
                                        <?=__('Floor')?>
                                    </div>
                                    <div class="q4-mobile-table-value">
                                        <?=$plan->getFloorsAsString() ? :'-'?>
                                    </div>
                                </div>
                                <div class="q4-carousel-row f0">
                                    <div class="q4-mobile-table-key">
                                        <?=__('Element number')?>
                                    </div>
                                    <div class="q4-mobile-table-value">
                                        <?if($plan->place_id):?>
                                            <?=isset($plan->place->custom_number) ? $plan->place->custom_number : $plan->place->number?>
                                        <?else:?>
                                            -
                                        <?endif?>
                                    </div>
                                </div>
                                <div class="q4-carousel-row f0">
                                    <div class="q4-mobile-table-key">
                                        <?=__('Edition')?>
                                    </div>
                                    <div class="q4-mobile-table-value">
                                        <?=$plan->edition?>
                                    </div>
                                </div>
                                <div class="q4-carousel-row f0">
                                    <div class="q4-mobile-table-key">
                                        <?=__('Date')?>
                                    </div>
                                    <div class="q4-mobile-table-value">
                                        <?=date('d/m/Y',$plan->date)?>
                                    </div>
                                </div>
                                <div class="q4-carousel-row f0">
                                    <div class="q4-mobile-table-key">
                                        <?=__('Image')?>
                                    </div>
                                    <div class="q4-mobile-table-value">
                                        <?$img = $plan->files->where('status','=',Enum_FileStatus::Active)->find()?>
                                        <?if($img) :?>
                                            <a href="<?=$img->originalFilePath()?>" class="q4-mobile-table-link" target="_blank" title="<?=$img->original_name?>">
                                                <img src="/media/img/choose-format/format-<?=strtolower($img->ext)?>.png" alt="<?=$img->ext?>"/>
                                            </a>
                                        <?endif;?>
                                    </div>
                                </div>
                            </div>
                        <?endforeach;?>
                    </div>

                </div>

                <div class="form-group">
                    <a href="#" class="q4-btn-lg light-blue-bg confirm-plan-mobile"><?=__('Confirm')?></a>
                </div>

            </div>
        </div>
    </div>
<!--end of .choose-plan-modal-mobile-->
</div>


