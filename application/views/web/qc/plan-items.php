<?
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 20.02.2019
 * Time: 11:59
 */
foreach($plans as $plan):?>
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
                <h4 class="table-modal-label-h4"><?=__('Plan name')?>: <?=$plan->name?></h4>
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

                                    <a data-fileid="<?=$file->id?>" data-url="<?=$file->getImageLink().'?'.uniqid()?>" data-fileid="<?=$file->id?>" data-ext="<?=$file->mime?>" data-controller="add_quality_control_image_from_raw_plan"  class="call-lit-plugin" title="<?=$file->original_name?>"><img src="/media/img/choose-format/format-<?=strtolower($file->ext)?>.png" alt="<?=$file->ext?>"/></a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </td>
        <td class="rwd-td1" data-th="<?=__('Name/Type')?>">
            <div class="div-cell break-c">
                <?=$plan->name?>
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