<?defined('SYSPATH') OR die('No direct script access.');?>
<?$range = Arr::extract($_GET,["from","to"]);?>
<div id="qc-list-printable" class="print-reports-list">
        <div class="text-right">
            <div class="printable-logo">
                <img class="q4b-logo" src="/media/img/logo.png" alt="logo">
            </div>
        </div>
        <div class="report-project-desc">
            <div class="print-main-small-image-wrapper">
                <img src="/<?=$_COMPANY->logo?>" alt="project images">
            </div>
            <div class="report-project-desc-list">
                <div class="f0">

                    <div class="print-col-50">
                        <ul>
                            <li>
                                <span>
                                    <i class="icon q4bikon-companies"></i>
                                    <?=__('Company name')?>:
                                </span>
                                <span><?=$_COMPANY->name?></span>
                            </li>
                            <li>
                                <span>
                                    <i class="icon q4bikon-project"></i>
                                    <?=__('Project name')?>:
                                </span>
                                <span><?=$_PROJECT->name?></span>
                            </li>
                            <li>
                                <span>
                                    <i class="q4bikon-username"></i>
                                    <?=__('Owner')?>:
                                </span>
                                <span><?=$_PROJECT->owner?></span>
                            </li>
                            <li>

                                <span>
                                    <i class="q4bikon-date"></i>
                                    <?=__('Start Date')?>:
                                </span>
                                <span><?=date('d/m/Y',$_PROJECT->start_date)?></span>
                            </li>
                            <li>
                                <span>
                                    <i class="q4bikon-date"></i>
                                    <?=__('End Date')?>:
                                </span>
                                <span><?=date('d/m/Y',$_PROJECT->end_date)?></span>
                            </li>
                        </ul>
                    </div>

                    <div class="print-col-50">
                        <ul>
                            <li>
                                <span>
                                    <i class="q4bikon-company_id"></i>
                                    <?=__('Project ID')?>:
                                </span>
                                <span><?=$_PROJECT->id?></span>
                            </li>
                            <li>
                                <span>
                                    <i class="q4bikon-company_status"></i>
                                    <?=__('Project Status')?>:
                                </span>
                                <span><?=__($_PROJECT->status)?></span>
                            </li>
                            <li>
                                <span>
                                    <i class="q4bikon-uncheked"></i>
                                    <?=__('Quantity of properties')?>:
                                </span>
                                <span><?=$_PROJECT->objects->count_all()?></span>
                            </li>
                            <li>

                                <span>
                                    <i class="q4bikon-address"></i>
                                    <?=__('Address')?>:
                                </span>
                                <span><?=$_PROJECT->address?></span>
                            </li>
                            <li>
                                <span class="range-key">
                                    <i class="q4bikon-date"></i>
                                    <?=__('Report Range')?>:
                                </span>
                                <span class="range-val">
                                    <span><?=$range['from']?>-<?=$range['to']?></span>
                                </span>
                            </li>
                        </ul>
                    </div>

                    <hr>
                </div>
            </div>

            <div class="report-project-desc-text">
                <p>
                    <span class="report-project-desc-intro"><?=__('Project Description')?>:</span> <?=$_PROJECT->description?>
                </p>
            </div>
        </div>
        <?foreach ($qcs as $q):?>
            <div class="page-break">
                <div class="text-right">
                    <div class="printable-logo">
                        <img class="q4b-logo" src="/media/img/logo.png" alt="logo">
                    </div>
                </div>
                <h4><?=__('Quality control')?> #<?=$q->id?></h4>
                <div class="report-result">
                    <div class="reports-property">
                        <span class="reports-property-d"><?=__('Project')?> : <strong><?=$_PROJECT->name?> </strong></span>
                        <span class="reports-property-d"><?=__('Property')?> : <strong><?=__($q->object->type->name)?> -<?=$q->object->name?> </strong></span>
                        <span class="reports-property-d"><?=__('Floor')?> : <strong><span class="bidi-override"><?=$q->floor->number?></span> </strong></span>
                        <span class="reports-property-d"><?=__('Element')?> : <strong><?=$q->place->name?> </strong></span>
                        <span class="reports-property-d"><?=__('Element number')?> : <strong>
                            <?
                                $placeNumber = !empty($q->place->custom_number) ? '('.$q->place->custom_number.')&lrm;' : '';
                                if($q->place->loaded()) echo "<i class='q4bikon-".$q->place->type."'></i> ".$q->place->number.' '.$placeNumber;
                            ?>
                        </strong></span>
                        <span class="reports-property-d"><?=__('Space')?> : <strong><?=$q->space->desc ? $q->space->desc : 'Space 1'?></strong></span>
                        <span class="reports-property-d"><?=__('Crafts')?> : <strong><?=$q->craft->name?></strong></span>
                    </div>
                    <div class="reports-prop-print-title">
                        <h4>
                            <span><?=__('Status')?>: <strong><?=__($q->status)?></span></strong>&nbsp;|
                            <span><?=__('Stage')?>: <strong><?=__($q->project_stage)?></span></strong>&nbsp;|
                            <span><?=__('Due Date')?>: <strong><?=$q->due_date ? date('d/m/Y',$q->due_date): ''?></strong></span>
                        </h4>
                    </div>

                </div>

                <!-- <?$comments = $q->comments->find_all()?>
                <?if(count($comments) || $q->description):?>
                    <div class="form-group">
                        <h4 class="table-modal-label-h4"><?=__('Comments')?></h4>
                            <? if($q->description)
                                echo "<p>".trim($q->description).'</p>';?>
                            <?foreach ($comments as $comment) {
                                $class = $q->createUser->id == $comment->owner->id ? 'bold':'normal';
                                echo "<div style='margin-bottom:7px;font-weight:$class'> ".date("d/m/Y H:i",$comment->created_at)
                                       .$comment->owner->name.': '.$comment->message.
                                    '</div>';
                            }
                            ?>
                        </div>
                    </div>
                <?endif?> -->

                <?if($q->plan->files->where('status','=',Enum_FileStatus::Active)->find()->loaded()):?>

                    <div class="report-plan-properties">
                        <span><?=__('Plan name')?> : <?=$q->plan->file()->getName()?></span>|
                        <span> <?=__('Status')?>: <?=__($q->plan->status)?></span>|
                        <span> <?=__('Date')?>: <?=date('d/m/Y',$q->plan->created_at)?> </span>
                    </div>
                <?endif;?>


                <!--status-->
                <div class="print-col-50">
                    <div class="report-desc-approve-authors">

                        <div>
                            <span class="approve-author"><?=__('Responsible profession')?>: <?=$q->profession->name?></span>

<!--                            <span class="approve-author">--><?//=__('Approvement Status')?><!--: --><?//=__($q->approval_status)?><!--</span>-->
                            <!-- <span class="approve-author"> <?=__($q->approval_status)?> </span> -->
                        </div>
                    </div>
                </div>

                <!---Description-->
                <?if(strlen($q->description)>1):?>
                    <h4 class="table-modal-label-h4"><?=__('Description')?></h4>
                    <?$desc = explode("\n",$q->description);
                    foreach ($desc as $line) {?>
                        <p><?=$line?></p>
                    <?}?>


                <?endif?>

                <div class="q4-copyright">
                    <span>
                        <!-- <?=__('Powered by')?> <img src="/media/img/company-logo-c.png" alt="company logo" class="q4-copyright-img"> -->
                        <?=__('Copyright © 2017 Q4B').__('All right reserved')?>
                    </span>
                </div>
            </div>


                <?$images = $q->images->where('status','=',Enum_FileStatus::Active)->find_all()?>
            <?if(count($images)):?>

                    <div class="report-plan-items">
                        <? foreach ($images as $number => $img):?>
                           <?if(($number+2)%2==0):?>
                                <div class='f0'>
                           <?endif;?>
                           <div class="print-col-50">
                                <div class="report-plan-item-image">
                                    <h4 class="report-plan-title">
                                    <?=$img->original_name?> <span class="report-plan-uploaded">(<?=__('uploaded')?>: <?=date('d.m.y H:i',$img->created_at)?> )&#x200E;</span></h4>

                                    <div class="report-plan-item-image-wrapper">
                                        <img src="<?=$img->originalFilePath()?>" alt="<?=$img->original_name?>">
                                    </div>
                                </div>
                            </div>
                            <?if(($number+2)%2==1 || $number == count($images)):?>
                                </div>
                           <?endif;?>
                        <?endforeach?>
                    </div>

            <?endif;?>
        <?endforeach;?>
    </div>
