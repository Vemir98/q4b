<?defined('SYSPATH') or die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.05.2017
 * Time: 6:51
 */
?>
<div class="qc-container">
    <div class="reports-prop-title">
        <div class="reports-prop-title_right">
            <div class="reports-prop-status">
                <div class="qc_status_cont">
                    <span class="reports-prop-title-status"><?=__('QC status')?>: </span>
                    <span class="reports-prop-title-status-value statistic-blue"><?=__($q->status)?></span>
                </div>
                <div class="qc_status_cont">
                    <span class="reports-prop-title-status"><?=__('Manager status')?>: </span>
                    <span class="reports-prop-title-status-value statistic-orange"><?=__($q->approval_status)?></span>
                </div>
            </div>
            <h3>
                <?if(!$_USER->isGuest() AND ( $_USER->is('project_visitor') OR $_USER->is('project_adviser'))):?>
                    <span  ><?=__('Quality control')?> #<?=$q->id?></span>
                <?else:?>
                    <a href="#" data-qc="quality-control" data-url="<?=URL::site('reports/quality_control/' . $q->id)?>"><?=__('Quality control')?>: <?=$q->id?></a>
                <?endif;?>
            </h3>
        </div>
        <div class="reports-prop-title_left">
            <h3>
                <div>
                    <span class="reports-prop-title-status fw-600"> <?=__('Stage')?>: </span>
                    <span class="reports-prop-title-status-value statistic-blue"><?=__($q->project_stage)?></span>
                </div>
                <div>
                    <span class="reports-prop-title-status fw-600"> <?=__('Due Date')?>: </span>
                    <span class="reports-prop-title-status-value statistic-blue"><?=($q->due_date) ? date('d/m/Y', $q->due_date) : ''?></span>
                </div>
            </h3>

            <div class="report-conditions">
                <div class="report-desc-approve-authors">
                    <div>
                        <span class="approve-option fw-600"> <?=__('Responsible profession')?>:</span>
                        <span class="approve-author"> <?=$q->profession->name?> </span>
                    </div>

                    <div>
                        <span class="approve-option fw-600"> <?=__('Created by')?>: </span>
                        <span class="approve-author"> <?=$q->createUser->name?> <span class="fs-14">(<?=date('d.m.Y H:ia', $q->created_at)?>)</span> </span>
                    </div>

                </div>
            </div>

            <?if((strlen($q->condition_list)) || (strlen($q->severity_level))):?>
                <div class="report-conditions invalid">
                    <div class="report-desc-approve-authors">
                        <?if(strlen($q->severity_level)):?>
                        <div>
                            <span class="approve-option fw-600"> <?=__('Severity Level')?>: </span>
                            <span class="approve-author"> <?=__($q->severity_level)?> </span>
                        </div>
                        <?endif?>

                        <?if(strlen($q->condition_list)):?>
                        <div>
                            <span class="approve-option fw-600"> <?=__('Conditions List')?>: </span>
                            <span class="approve-author"> <?=__($q->condition_list)?> </span>
                        </div>
                        <?endif?>
                    </div>
                </div>
            <?endif?>
        </div>
    </div>
    <div class="reports-property">
        <span class="reports-prop-desc">
            <i class="q4bikon-companies"></i>
            <span class="reports-prop-res "><?=__($_PROJECT->name)?> </span>
        </span>
        <span class="reports-prop-desc">
            <i class="q4bikon-project1"></i>
            <span class="reports-prop-res "><?=$q->object->name?> </span>
        </span>

        <span class="reports-prop-desc">
            <i class="q4bikon-baseline-stairs"></i>
            <span class="reports-prop-res"><?=$q->place->floor->custom_name ? $q->place->floor->custom_name .' ('. $q->place->floor->number .') ' : '<span class="bidi-override">'. $q->place->floor->number .'</span>' ?> </span>
        </span>
        <span class="reports-prop-desc">
            <i class="q4bikon-address"></i>
            <span class="reports-prop-res"><?=$q->place->name?> </span>
        </span>

        <span class="reports-prop-desc">
            <i class="q4bikon-N"></i>
            <span class="reports-prop-res"><?
                $placeNumber = !empty($q->place->custom_number) ? $q->place->custom_number : $q->place->number;
                if($q->place->loaded()) echo  $placeNumber. ' ' . "<i class='q4bikon-".$q->place->type."' style='margin: 0 5px;'></i> ";
                ?>
            </span>
        </span>

        <span class="reports-prop-desc">
            <i class="q4bikon-room-key"></i>
            <span class="reports-prop-res"><?=$q->space->type->name?> </span>
        </span>

        <span class="reports-prop-desc">
            <i class="q4bikon-worker"></i>
            <span class="reports-prop-res"><?=$q->craft->name?> </span>
        </span>
    </div>

<!-- new Tasks section -->
    <?php
        $arrayTasks = $usedTasksAray = [];
        $itemTasks = $q->tasks->find_all();
        $usedtasks = $q->project->usedTasks($q->place->id);
        foreach ($itemTasks as $task) {
            $arrayTasks[] = $task->id;
        }
        foreach ($usedTasks as $task) {
            $usedTasksAray[] = $task->id;

        }
    ?>
    <div class="report_tasks">
        <h4 class="reports-tasks-box-title" style="color: rgba(0, 0, 0, 0.7);"><?=__('Tasks')?></h4>
        <div class="report_tasks_wraper">
            <?foreach($tasks as $task):?>
                <?
                    $crafts = $task->crafts->where('cmpcraft.status','=',Enum_Status::Enabled)->find_all();
                    $c = [];
                    foreach ($crafts as $cr){
                        $c []= $cr->id;
                    }
                ?>
                <div class="report_tasks_item <?=in_array($task->id, $arrayTasks) ? ' selected' :  (in_array($q->craft_id,$c) ? '' : 'hidden' )?>">
                    <div class="report_task_title"><?=__('Task')?> <?=$task->id?></div>
                    <div class="report_task_desc_wrap">
                        <div class="report_task_descripticon">
                            <?$desc = explode("\n",$task->name);
                            foreach ($desc as $line) {?>
                                <div><?=html_entity_decode($line)?></div>
                            <?}?>
                        </div>
                        <div class="report_task_status <?=in_array($task->id, $arrayTasks) ? ' done' :  (in_array($q->craft_id,$c) ? '' : ' hidden' )?>"></div>
                    </div>
                </div>
            <?endforeach?>
        </div>
    </div>
    <div class="report-plans">
        <div class="report-plan-top">
            <div class="report-desc-approve">
                <div class="report-desc-approve">

                    <h4 class="reports-tasks-box-title" style="color: rgba(0, 0, 0, 0.7);"><?=__('Description')?></h4>
                    <div class="report_descripticon mt-15">
                        <?$desc = explode("\n",$q->getDesc(html_entity_decode($q->description), "@##"));
                        foreach ($desc as $line) {?>
                            <div><?=$line?></div>
                        <?}?>
                    </div>
                </div>
            </div>

        </div>
        <div>
            <?if (strlen($q->getDialog(html_entity_decode($q->description), "@##", "\n")) > 1) :?>
                <ul class="report-desc-approve">
                    <div class="row">
                        <div class="col-md-12">
                            <li class="tab_panel tab_panel_qc">
                                <div class="panel_header_new panel_header_qc">
                                    <h4 class="reports-tasks-box-title" style="color: rgba(0, 0, 0, 0.7);"><?=__('Corrective action/Performed work')?> <span class="sign"><i class="panel_header_icon_new q4bikon-arrow_bottom fs-16" style="color: #1EBCE8"></i></span>
                                    </h4>
                                </div>
                                <div class="panel_content">
                                    <div class="report-desc-approve-text fs-14">
                                        <?$desc = explode("\n",$q->getDialog(html_entity_decode($q->description), "@##", "\n"));
                                        foreach ($desc as $line) {?>
                                            <div><?=$line?></div>
                                        <?}?>
                                    </div>
                                </div>
                            </li>
                        </div>
                    </div>
                </ul>
            <?endif?>
        </div>
        <div class="report-plan-items">
            <div class="row">
                <?foreach ($q->images->where('status', '=', Enum_FileStatus::Active)->find_all() as $img): ?>
                    <div class="col-md-6 rtl-float-right">
                        <div class="report-plan-item">
                            <h4 class="report-plan-title"><?=$img->original_name?> <span class="report-plan-uploaded">(<?=__('uploaded')?>: <?=date('d.m.y H:i', $img->created_at)?> )</span></h4>
                            <div class="report-plan-item-image-new">
                                <a href="<?=$img->originalFilePath()?>" target="_blank">
                                    <?if ($_SERVER['SERVER_NAME'] === 'qforb.net') :?>
                                        <img src="<?=$img->getBigThumbPath()?>?<?=rand(100000,99999999)?>" alt="<?=$img->original_name?>">
                                    <?else:?>
                                        <img src="<?=$img->originalFilePath() . '?' . uniqid()?>" alt="<?=$img->original_name?>">
                                    <?endif?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?endforeach?>
            </div>
        </div>
    </div>
</div>