<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.05.2017
 * Time: 11:33
 */
?>



<div id="quality-control-modal-mobile" data-backdrop="static" data-keyboard="false" class="quality-control-modal-mobile create-modal modal fade"  role="dialog" data-qcid="<?=$item->id?>">
<!--.quality-control-modal-mobile-->
    <div class="modal-dialog q4_project_modal quality-control-dialog-mobile q4-mobile-layout">
        <form action="<?=URL::site('/projects/quality_control/'.$item->id)?>" data-submit="false" class="q4_form" data-ajax="true" method="post">
            <input type="hidden" value="" name="x-form-secure-tkn"/>
            <div class="modal-content">
                <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                        <div class="clear"></div>
                    </div>
                    <div class="q4_modal_sub_header">
                        <h3><?=__('Add new mobile quality control')?></h3>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="form-group">

                        <ul class="qc-mobile-list">
                            <li>
                               <i class="icon q4bikon-project light-blue"></i>
                                <span class="qc-mobile-list-text"> <?=$item->project->name?></span>
                            </li>
                            <li>
                                <i class="icon q4bikon-company_id light-blue"></i>
                                <span class="qc-mobile-list-text"><?=$item->object->name?></span>
                            </li>
                            <li>
                                <i class="icon q4bikon-appartment light-blue"></i>
                                <span class="qc-mobile-list-text"> <?=$item->name?></span> | </span><span class="qc-mobile-list-text"><?=__('Element number')?>:<?=!empty($item->custom_number) ? $item->custom_number : $item->number?></span>
                            </li>
                            <li>
                                <i class="icon q4bikon-stairway light-blue"></i>
                                <span class="qc-mobile-list-text"><?=__('Floor')?>: <span class="bidi-override"><?=$item->floor->number?></span></span> <span class="reports-prop-title-divider"> | </span><span class="qc-mobile-list-text"><?=__('Element id')?>:<?=$item->number?></span>
                            </li>
                        </ul>

                    </div>
                    <div class="form-group relative">
                        <ul class="qc-mobile-list">
                            <li>
                                <label class="table_label dark-blue"><?=__('Crafts')?> <span class="q4-required">*</span></label>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                     <select name="craft_id" class="q4-select q4-form-input qc-craft q4_select">
                                        <option value="" ><?=__('Please select')?></option>
                                        <?foreach ($item->project->company->crafts->where('status','=',Enum_Status::Enabled)->order_by('name')->find_all() as $craft):

                                        $profs = $craft->professions->where('status','=',Enum_Status::Enabled)->find_all();
                                               $p = [];
                                               foreach ($profs as $pr)
                                                   $p []= $pr->id;
                                               if(empty($p)) continue;
                                               ?>
                                            <option  value="<?=$craft->id?>" data-professions="<?=implode(',',$p)?>"><?=__($craft->name)?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </li>
                             <li>
                                <span class="table_label dark-blue"><?=__('Space/Place')?></span>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                     <select class="q4-select q4-form-input" name="space_id">
                                    <?$i = 1;?>
                                    <?foreach($item->spaces->find_all() as $space):?>
                                        <option value="<?=$space->id?>"><?=$i.' '.$space->type->name.' '. $space->desc?></option>
                                    <?endforeach?>
                                </select>
                                </div>
                            </li>
                            <li>
                                <label class="table_label dark-blue"><?=__('Stage')?> <span class="q4-required">*</span></label>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <select class="q4-select q4-form-input" name="project_stage">
                                        <?foreach(Enum_ProjectStage::toArray() as $stage):
                                            $selected =  isset($item->project_stage) && $item->project_stage == $stage ? " selected='selected'" : '';;
                                            ?>
                                            <option value="<?=$stage?>" <?=$selected?> ><?=__($stage)?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </li>
                            <li>
                                <label class="table_label dark-blue"><?=__('Status')?> <span class="q4-required">*</span></label>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <select class="q4-select q4-form-input qc-status" name="status">
                                        <?foreach (Enum_QualityControlStatus::toArray() as $val):?>
                                            <option value="<?=$val?>"><?=__($val)?></option>
                                        <?endforeach?>

                                    </select>
                                </div>
                            </li>
                            <li>
                                <label class="table_label dark-blue"><?=__('Responsible person')?> <span class="q4-required">*</span></label>
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
                            </li>
                            <li>
                               <label class="table_label"><?=__('Due Date')?></label>
                               <div class="input-group form-group date" id="qc-due-date" data-provide="datepicker">
                                    <div class="input-group-addon small-input-group">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </div>
                                    <input type="text" name="due_date" class="q4-form-input table_input" data-date-format="DD/MM/YYYY" value="<?=date('d/m/Y', time())?>"/>
                                </div>

                            </li>
                        </ul>
                    </div>


                    <div class="form-group qc-border-bottom">
                        <label class="table_label"><?=__('Plan')?></label>
                        <div class="qc-choose-plan">
                            <a href="#" data-toggle="modal" data-target="#choose-plan-modal-mobile" class="q4-btn-lg light-blue-bg choose-plan"><?=__('Choose Plan')?></a>
                        </div>
                        <br>

                        <div class="qc-plan-details property-quality-control-name">
                            <div class="qc-change-plan">
                                <a href="#" data-toggle="modal" data-target="#choose-plan-modal-mobile"><?=__('Choose Plan')?></a>
                            </div>
                        </div>
                    </div>

                    <div class="form-group qc-border-bottom relative error-handler">
                        <?$usedTasksAray = [];
                                    foreach ($usedTasks as $task) {
                                       $usedTasksAray[] = $task->id;

                                    }
                                ?>

                            <div data-structurecount="<?=count($tasks)?>" class="tasks-full-description-mobile qc-tasks-list-mobile q4-owl-carousel">

                                <?foreach($tasks as $task):?>
                                    <div class="item hidden">
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
                                    <option value="<?=$task->id?>" data-usedcrafts="<?=implode(',',$usedCraftsArray)?>" data-crafts="<?=implode(',',$c)?>"><?=$task->name?></option>
                                <?endforeach?>
                            </select>
                    </div>

                    <div class="form-group">
                        <label class="table_label"><?=__('Conditions')?></label>
                        <div class="conditions property-quality-control-conditions">
                            <div class="form-group">
                                <span class="conditions-list"><?=__('Severity Level')?>:</span>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                   <select class="q4-select q4-form-input disabled-input" name="severity_level">
                                       <option selected="selected"><?=__('Please select')?></option>
                                        <?foreach (Enum_QualityControlConditionLevel::toArray() as $val):?>
                                            <option value="<?=$val?>"><?=ucfirst(__($val))?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <span class="conditions-list"><?=__('Conditions List')?>:</span>
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


                    <div class="form-group qc-border-bottom">
                        <label class="table_label"><?=__('Images List')?></label>

                        <div data-structurecount="0" class="qc-image-list-mobile q4-owl-carousel">
                        </div>
                        <div class="modal-images-list-box">
                            <a href="#" class="q4-btn-lg light-blue-bg modal-load-images quality-control-load-images"><?=__('Load images')?></a>
                             <div class="hide-upload">
                                <input type="file" class="load-images-input" data-id="<?=uniqid()?>" accept="image/*" multiple name="images[]">
                            </div>
                        </div>
                    </div>


              <!--       <div class="form-group col-md-12 rtl-float-right">
                        <label class="table_label"><?=__('Comments')?></label>
                        <textarea maxlength="250" style="height:80px" placeholder="<?=__('Leave your comment')?>" class="modal-details-textarea" name="message"></textarea>
                    </div> -->
                    <div class="form-group relative">
                        <label class="table_label"><?=__('Description')?></label>
                        <textarea name="description" class="modal-details-textarea-mobile q4_required"></textarea>
                    </div>

                    <div class="form-group qc-border-bottom">
                        <ul class="approve-staff">

                            <li>
                                <span class="approve-key"><?=__('Viewed by')?>:</span>
                                <span class="modal-details-action-status orange"><?=__('Waiting to approve')?></span>
                            </li>
                            <li>
                                <span class="approve-key"><?=__('Created by')?>: </span>
                                <span class="modal-details-action-status gray"><?=Auth::instance()->get_user()->name?></span>
                            </li>
                        </ul>
                    </div>

                </div>

                <div class="panel-modal-footer text-align">
                    <div class="form-group">
                        <a href="#" class="q4-btn-lg light-blue-bg  qc-to-print-btn"><?=__('Proceed to print')?></a>
                    </div>
                    <div class="form-group">
                        <a  href="#" class="q4-btn-lg orange q4_form_submit q4-form-submit"><?=__('Create')?></a>
                    </div>
                </div>
            </div>
        </form>
    </div>

<!--end of .quality-control-modal-mobile-->
</div>



<div id="choose-plan-modal-mobile" data-backdrop="static" data-keyboard="false" class="modal choose-plan-modal fade no-delete" role="dialog">
<!--.choose-plan-modal-mobile-->
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
                <!-- <div class="form-group">
                    <label class="table_label dark-blue"><?=__('Choose Profession')?> <span class="q4-required">*</span></label>
                    <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                        <select class="form-control">
                            <option value="">Choose profession</option>
                            <option value="Painter">Painter</option>
                            <option value="Carpenter">Carpenter</option>
                            <option value="Welder">Welder</option>
                        </select>
                    </div> b
                </div> -->
                <div class="form-group">
                    <span class="q4-list-items-result"><?=__('tap to plan name to select a plan')?> </span>
                </div>


                <div class="q4-carousel-table-wrap">
                    <div class="q4-carousel-table" data-structurecount="<?=count($plans)?>" >

                        <?foreach ($plans as $plan):?>
                            <?
                                $crafts = [];
                                foreach ($plan->crafts->find_all() as $craft) {
                                    $crafts[] = $craft->id;
                                }
                        ?>

                        <div class="item" data-crafts='<?=json_encode($crafts)?>' >

                            <div class="hidden pln-data">
                                <div class="qc-change-plan">
                                    <a href="#" data-toggle="modal" data-target="#choose-plan-modal-mobile"><?=__('Choose plan')?></a>
                                </div>
                                <ul class="qc-plan-details-list">
                                    <input type="hidden" name="plan_id" value="<?=$plan->id?>"/>
                                    <li>
                                        <span class="qc-plan-details-list-text blue-head-title"><?=__('Plan name')?>: <?=$plan->file() ? $plan->file()->getName():$plan->name?></span>
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
                                                <a data-fileid="<?=$file->id?>" data-url="<?=$file->getImageLink()?>" data-fileid="<?=$file->id?>" data-ext="<?=$file->mime?>" data-controller="add_quality_control_image_from_raw_plan"  class="call-lit-plugin" title="<?=$file->original_name?>">
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
                                <span class="blue-head-title"><?=__('Name').' : '.$plan->file()->getName()?><?=__('Details').' #'.$plan->id?></span>

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
                                    <?=__('Element Number')?>
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
<!--end of .choose-plan-modal-mobile-->
</div>

