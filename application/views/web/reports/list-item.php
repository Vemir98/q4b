<?defined('SYSPATH') or die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.05.2017
 * Time: 6:51
 */
?>


<div class="report-result" id="qcid-<?=$q->id?>">
    <div class="reports-property">
        <span class="reports-prop-desc">
            <span class="reports-prop-txt"><?=__('project')?> : </span>
            <span class="reports-prop-res dark-blue"><?=__($_PROJECT->name)?>
            <span class="prop-divider"> | </span></span>
        </span>
        <span class="reports-prop-desc">
            <span class="reports-prop-txt"><?=__('Property')?> : </span>
            <span class="reports-prop-res dark-blue"><?=__($q->object->type->name) . ' - </span> <span class="reports-prop-res dark-blue">' . $q->object->name?></span>
            <span class="prop-divider"> | </span></span>
        <span class="reports-prop-desc">
            <span class="reports-prop-txt"><?=__('Floor')?> : </span>
            <span class="reports-prop-res dark-blue"><span class="bidi-override"><?=$q->place->floor->number?></span></span>
            <span class="prop-divider"> | </span></span>
        <span class="reports-prop-desc">
            <span class="reports-prop-txt"><?=__('Element')?> : </span>
            <span class="reports-prop-res dark-blue"><?=$q->place->name?></span>
            <span class="prop-divider"> | </span></span>
        <span class="reports-prop-desc">
            <span class="reports-prop-txt"><?=__('Element number')?> : </span>
            <span class="reports-prop-res dark-blue">
                <?
                    $placeNumber = !empty($q->place->custom_number) ? '('.$q->place->custom_number.')&lrm;' : '';
                    if($q->place->loaded()) echo "<i class='q4bikon-".$q->place->type."'></i> ".$q->place->number.' '.$placeNumber;
                ?>
            </span>
            <span class="prop-divider"> | </span></span>
        <span class="reports-prop-desc">
            <span class="reports-prop-txt light-blue"><?=__('Space')?> : </span>
            <span class="reports-prop-res dark-blue"><?=$q->space->desc ? $q->space->desc : 'Space 1'?></span><span class="prop-divider"> | </span>
        </span>
        <span class="reports-prop-desc"><span class="light-blue"><?=__('Crafts')?> : </span><span class="reports-prop-res dark-blue"><?=$q->craft->name?></span></span>
    </div>
    <div class="reports-prop-title">
        <h3>
         <?if(!$_USER->isGuest() AND ( $_USER->is('project_visitor') OR $_USER->is('project_adviser'))):?>
            <span  ><?=__('Quality control')?> #<?=$q->id?></span>
        <?else:?>
            <a href="#" data-qc="quality-control" data-url="<?=URL::site('reports/quality_control/' . $q->id)?>"><?=__('Quality control')?> #<?=$q->id?></a>
        <?endif;?>
        </h3>
        <h3>
            <span class="reports-prop-title-status light-blue"><?=__('Status')?>: </span>
            <span class="dark-blue"><?=__($q->status)?></span>
            <span class="reports-prop-title-divider">|</span>
            <span class="dark-blue"><?=__($q->approval_status)?></span>
            <span class="reports-prop-title-divider">|</span>
            <span class="reports-prop-title-status light-blue"><?=__('Stage')?>: </span>
            <span class="dark-blue"><?=__($q->project_stage)?></span>
            <span class="reports-prop-title-divider">|</span>
            <span class="reports-prop-title-status light-blue"> <?=__('Due Date')?>: </span>
            <span class="dark-blue"><?=($q->due_date) ? date('d/m/Y', $q->due_date) : ''?></span>

            <?if(strlen($q->severity_level)):?>
                <span class="reports-prop-title-divider">|</span>
                <span class="reports-prop-title-status light-blue"> <?=__('Severity Level')?>: </span>
                <span class="dark-blue"><?=__($q->severity_level)?></span>
            <?endif?>

            <?if(strlen($q->condition_list)):?>
                <span class="reports-prop-title-divider">|</span>
                <span class="reports-prop-title-status light-blue"> <?=__('Conditions List')?>: </span>
                <span class="dark-blue"><?=__($q->condition_list)?></span>
            <?endif?>

        </h3>
    </div>
    <!-- <?$comments = $q->comments->find_all()?>
    <?if(count($comments) || $q->description):?>
        <div class="report-desc-approve">

            <div class="row">
                <div class="col-md-12">
                    <?
                    if($q->description)
                        echo "<div style='margin-bottom:7px' class='report-desc-approve-text'>".'
                               '.trim($q->description).'
                            </div>';?>
                    <? foreach ($comments as $comment) {
                        $class = $q->createUser->id == $comment->owner->id ? 'light-blue':'dark-blue';
                        echo "<div style='margin-bottom:7px' class='$class'>".date("d/m/Y H:i",$comment->created_at).'
                                <div style="padding-left: 20px">'.$comment->owner->name.': '.$comment->message.'</div>
                            </div>';
                    }
                   ?>

                </div>
            </div>
        </div>
    <?endif?>
-->

    <div class="report-plans">
     <?$file = $q->plan->files->where('status', '=', Enum_FileStatus::Active)->find()->loaded()?>
        <div class="report-plan-top">
            <?if ($file): ?>
                <div class="report-plan-properties">
                    <span class="report-plan-prop">
                        <span class="light-blue"><?=__('Plan name')?>: </span>
                        <span class="dark-blue"><?=$q->plan->file()->getName()?> </span> <span class="report-plan-divider">&nbsp;| </span>
                    </span>
                    <span class="report-plan-prop">
                        <span class="light-blue"><?=__('Edition')?>: </span>
                        <span class="dark-blue"><span class="bidi-override"><?=$q->plan->edition?></span> </span><span class="report-plan-divider">&nbsp;| </span>
                    </span>
                    <span class="report-plan-prop">
                        <span class="light-blue"><?=__('Status')?>: </span>
                        <span class="dark-blue"><?=__($q->plan->status)?></span><span class="report-plan-divider">&nbsp;| </span>
                        <span class="report-plan-divider">&nbsp;| <?=__($q->approval_status)?></span>
                    </span>
                    <span class="report-plan-prop">
                        <span class="light-blue"> <?=__('Date')?>: </span>
                        <span class="dark-blue"><?=date('d/m/Y', $q->plan->created_at)?></span>
                    </span>
                </div>
            <?endif?>

            <div class="report-desc-approve">
                <div class="report-conditions">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="report-desc-approve-authors">
                                <div>
                                    <span class="approve-option light-blue"><?=__('Responsible profession')?>: </span>
                                    <span class="approve-author dark-blue"> <?=$q->profession->name?> </span>
                                </div>
<!--                                <div>-->
<!--                                    <span class="approve-option light-blue">--><?//=__('Approvement Status')?><!--: </span>-->
<!--                                    <span class="approve-author dark-blue"> --><?//=__($q->approval_status)?><!-- </span>-->
<!--                                </div>-->
<!--                                <br>-->
                                <div>
                                    <span class="approve-option light-blue"><?=__('Created by')?>: </span>
                                    <span class="approve-author dark-blue"> <?=$q->createUser->name?> (<?=date('d.m.Y H:ia', $q->created_at)?>)</span>
                                </div>
<!--                                <div>-->
<!--                                    <span class="approve-option light-blue">--><?//=__('Updated by')?><!--: </span>-->
<!--                                    <span class="approve-author dark-blue"> --><?//if($q->updated_by):?><!----><?//=$q->updateUser->name?><!-- (--><?//=date('d.m.Y H:ia', $q->updated_at)?><!--)--><?//endif?><!--</span>-->
<!--                                </div>-->
                            </div>
                        </div>
                    </div>
                </div>
                <?if (strlen($q->description) > 1): ?>
                    <div class="report-desc-approve">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="reports-tasks-box-title"><?=__('Description')?></h4>
                                <div class="report-desc-approve-text">
                                    <?$desc = explode("\n",$q->getDesc(html_entity_decode($q->description), "@##"));
                                    foreach ($desc as $line) {?>
                                        <p><?=$line?></p>
                                    <?}?>
                                </div>
                            </div>
                        </div>
                    </div>
                <div>
                    <?if (strlen($q->getDialog(html_entity_decode($q->description), "@##")) > 1) :?>
                        <ul class="report-desc-approve">
                            <div class="row">
                                <div class="col-md-12">
                                    <li class="tab_panel tab_panel_qc">
                                        <div class="panel_header panel_header_qc">
                                            <span class="sign"><i class="panel_header_icon q4bikon-plus" style="color: #000"></i></span>
                                            <h4 class="reports-tasks-box-title"><?=__('Corrective action/Performed work')?></h4>
                                            <div class="separator"></div>
                                        </div>
                                        <div class="panel_content">
                                            <div class="report-desc-approve-text">
                                                <?$desc = explode("\n",$q->getDialog(html_entity_decode($q->description), "@##"));
                                                foreach ($desc as $line) {?>
                                                    <p><?=$line?></p>
                                                <?}?>
                                            </div>
                                        </div>
                                    </li>
                                </div>
                            </div>
                        </ul>
                    <?endif?>
                </div>
                <?endif?>
            </div>

        </div>
        <div class="report-plan-items">
            <div class="row">
                <?foreach ($q->images->where('status', '=', Enum_FileStatus::Active)->find_all() as $img): ?>
                <div class="col-md-6 rtl-float-right">
                    <div class="report-plan-item">
                        <h4 class="report-plan-title"><?=$img->original_name?> <span class="report-plan-uploaded">(<?=__('uploaded')?>: <?=date('d.m.y H:i', $img->created_at)?> )</span></h4>
                        <div class="report-plan-item-image">
                            <a href="<?=$img->originalFilePath() . '?' . uniqid()?>" target="_blank">
                                <img src="<?=$img->getBigThumbPath() . '?'?>" alt="<?=$img->original_name?>">
                            </a>
                        </div>
                    </div>
                </div>
                <?endforeach?>
            </div>
        </div>
    </div>

</div>
