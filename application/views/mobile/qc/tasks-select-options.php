<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 20.02.2019
 * Time: 13:22
 */
$usedTasksArray = [];
$usedCraftsArray = [];
foreach ($usedTasks as $task) {
    $usedTasksArray[] = $task->id;
}
?>
<div class="q4-wrap-mobile">

    <div data-structurecount="0" class="tasks-full-description-mobile qc-tasks-list-mobile q4-owl-carousel">

        <?foreach($items as $task):?>
            <div class="item hidden">
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
            </div>
        <?endforeach;?>
    </div>
    <select class="hidden-select q4_select" name="tasks" multiple>
        <?foreach($project->tasks->where('status','=',Enum_Status::Enabled)->find_all() as $task):?>
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