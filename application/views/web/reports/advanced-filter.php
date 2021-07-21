<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.05.2017
 * Time: 18:08
 */
$selectedArray = json_decode($custom_variable,true);
$selectedObjects = $selectedArray["object_id"] ? $selectedArray["object_id"] : [];
$selectedFloors = $selectedArray["floors"] ? $selectedArray["floors"] : [];
$selectedProfession = $selectedArray["profession_id"] ? $selectedArray["profession_id"] : '';
$selectedPlaceType = $selectedArray["place_type"] ? $selectedArray["place_type"] : '';
$selectedNumber = $selectedArray["place_number"] ? $selectedArray["place_number"] : '';
$selectedStages = $selectedArray["project_stage"] ? $selectedArray["project_stage"] : [];
$selectedSpace = $selectedArray["space"] ? $selectedArray["space"] : '';

?>
<div class="advanced-reports-row first">
    <div class="row">
        <div class="form-group col-md-3 rtl-float-right multi-select-col">
            <label class="table_label">
                <?=__('Property')?>
                <span class="check-all-links" data-seltxt="<?=__('select all')?>" data-unseltxt="<?=__('unselect all')?>"><?=__('select all')?></span>
            </label>
            <div class="multi-select-box pr-object">
                <div class="select-imitation">
                    <span class="select-imitation-title" data-text="<?=__('Please select')?>">
                        <span class="select-def-text">
                            <?if($selectedObjects):?>
                            <?foreach($props as $prop){
                                if(in_array($prop['id'], $selectedObjects)){

                                    echo $prop['type'].' - '. $prop['name'].',';
                                }

                            }?>
                            <?else:?>
                                <?=__('Please select')?>
                            <?endif;?>
                        </span>
                    </span>
                    <div class="over-select" id="reports-adv-property"></div>
                    <i class="arrow-down q4bikon-arrow_bottom"></i>
                </div>
                <div class="checkbox-list">
                    <?foreach($props as $prop):?>
                    <?$checked = in_array($prop['id'], $selectedObjects) ? "checked":""?>
                    <div class="checkbox-list-row">
                        <span class="checkbox-text">
                            <label class="checkbox-wrapper-multiple inline <?=$checked?>" data-val="<?=$prop['id']?>">
                                <span class="checkbox-replace"></span>
                                 <i class="checkbox-list-tick q4bikon-tick"></i>
                            </label>
                            <?=$prop['type']?> - <?=$prop['name']?>
                        </span>
                    </div>
                    <?endforeach?>
                </div>
                <select class="hidden-select" name="object_id[]" multiple>
                    <?foreach($props as $prop):?>
                    <?=$obj=ORM::factory('PrObject',$prop['id']);?>
                    <?$selected = in_array($prop['id'], $selectedObjects) ? "selected":""?>
                        <option value="<?= $prop['id'] ?>" <?=$selected?> data-floornames='<?=json_encode($obj->floorNumbersWithNames())?>' data-floor-from="<?=$prop['smaller_floor']?>" data-floor-to="<?=$prop['bigger_floor']?>"><?=$prop['type']?> - <?=$prop['name']?></option>
                    <?endforeach?>
                </select>
            </div>
        </div>
        <div class="form-group col-md-3 rtl-float-right multi-select-col">
            <label class="table_label">
                <?=__('Floor')?>
                <span class="check-all-links" data-seltxt="<?=__('select all')?>" data-unseltxt="<?=__('unselect all')?>"><?=__('select all')?></span>
            </label>
            <div class="multi-select-box comma floors-list">
                <div class="select-imitation table_input">
                    <span class="select-imitation-title" data-text="<?=__('Please select')?>">
                        <i class="q4bikon-baseline-stairs"></i>
                    </span>
                     <div class="over-select<?=$selectedFloors ? '' : ' disabled-input' ?>" id="reports-adv-floor"></div>
                     <i class="arrow-down q4bikon-arrow_bottom"></i>
                </div>
                <div class="checkbox-list">
                    <?
                        $smallFloor = 0;
                        $bigFloor = 0;

                        foreach ($props as $prop) {
                            if(in_array($prop['id'], $selectedObjects)){

                                if($prop['smaller_floor'] < $smallFloor){
                                    $smallFloor = $prop['smaller_floor'];
                                }
                                if($prop['bigger_floor'] > $bigFloor){
                                    $bigFloor = $prop['bigger_floor'];
                                }
                            }
                        }
                    ?>
                    <?if($smallFloor == $bigFloor):?>
                        <?$checked = in_array($bigFloor, $selectedFloors) ? "checked":""?>
                        <div class="checkbox-list-row"  data-custom-label="true">
                            <span class="checkbox-text">
                                <label class="checkbox-wrapper-multiple inline <?=$checked?>" data-val="<?=$bigFloor?>">
                                    <span class="checkbox-replace"></span>
                                    <i class="checkbox-list-tick q4bikon-tick"></i>
                                </label>

                                    <?=$bigFloor?>

                            </span>
                        </div>
                    <?else:?>
                        <?for($i=$smallFloor; $i <= $bigFloor; $i++):?>

                            <?$checked = in_array($i, $selectedFloors) ? "checked":""?>
                            <div class="checkbox-list-row" data-custom-label="true">
                                <span class="checkbox-text">
                                    <label class="checkbox-wrapper-multiple inline <?=$checked?>" data-val="<?=$i?>">
                                        <span class="checkbox-replace"></span>
                                        <i class="checkbox-list-tick q4bikon-tick"></i>
                                    </label>
                                     <span class="checkbox-text-content bidi-override">
                                        <?=$i?>
                                    </span>
                                </span>
                            </div>
                        <?endfor?>
                    <?endif; ?>
                </div>
                <?if($smallFloor == $bigFloor):?>
                    <?$selected = in_array($bigFloor, $selectedFloors) ? "selected":""?>
                    <select class="hidden-select" name="floors[]" multiple>
                        <option value="<?=$bigFloor?>" <?=$selected?>><?=$bigFloor?></option>
                    </select>
                <?else:?>

                    <?$selected = in_array($i, $selectedFloors) ? "selected":""?>
                    <select class="hidden-select" name="floors[]" multiple>
                    <?for($i=$smallFloor; $i <= $bigFloor; $i++):?>

                            <?$selected = in_array($i, $selectedFloors) ? "selected":""?>
                            <option value="<?=$i?>" <?=$selected?>><?=$i?></option>
                        <?endfor?>
                    </select>
                <?endif; ?>

            </div>
        </div>
        <div class="form-group col-md-2 rtl-float-right">
            <label class="table_label"><?=__('Element type')?></label>
            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                <select class="q4-select q4-form-input" name="place_type">
                   <?// echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($props); echo "</pre>"; exit;?>
                   <option value="all" <?=$selectedPlaceType=="all"?>><?=__('All')?></option>
                    <?foreach ($elementTypes as $et):?>
                        <?$selected = $et==$selectedPlaceType ? "selected":""?>

                        <option value="<?=$et?>" <?=$selected?>><?=__($et)?></option>
                    <?endforeach?>
                </select>
            </div>
        </div>
        <div class="form-group col-md-2 rtl-float-right">
            <label class="table_label"><?=__('Element number')?></label>
            <input type="text" class="table_input  <?=($selectedObjects && $selectedPlaceType!="all" )? '':'disabled-input'?> " name="place_number" data-url="<?=URL::site('reports/get_spaces/')?>">
        </div>
        <div class="form-group col-md-2 rtl-float-right">
            <label class="table_label"><?=__('Element id')?></label>
            <input type="text" class="table_input  <?=($selectedObjects && $selectedPlaceType!="all" )? '':'disabled-input'?> " name="custom_number" data-url="<?=URL::site('reports/get_spaces/')?>">
        </div>

    </div>
</div>
<div class="advanced-reports-row ">
    <div class="row">

        <div class="form-group col-md-3 rtl-float-right">
            <label class="table_label"><?=__('Space')?></label>
            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                <select class="q4-select q4-form-input disabled-input" name="space" disabled>
                </select>
            </div>
        </div>
        <div class="form-group col-md-3 rtl-float-right multi-select-col">
            <label class="table_label">
                <?=__('Stage')?>
                <span class="check-all-links" data-seltxt="<?=__('select all')?>" data-unseltxt="<?=__('unselect all')?>"><?if(!empty($selectedStages)):?><?=__('select all')?><?else:?><?=__('unselect all')?><?endif?></span>
            </label>
            <div class="multi-select-box pr-stage">
                <div class="select-imitation">
                    <span class="select-imitation-title" style="width: 92%;" data-text="<?=__('Please select')?>">
                        <span class="select-def-text">
                                <?foreach($qcStages as $qcs){
                                    if(in_array($qcs, $selectedStages) OR empty($selectedStages)){

                                        echo __($qcs).',';
                                    }

                                }?>
                        </span>
                    </span>
                    <div class="over-select" id="reports-adv-stage"></div>
                    <i class="arrow-down q4bikon-arrow_bottom"></i>
                </div>
                <div class="checkbox-list">
                    <?foreach($qcStages as $qcs):?>
                        <?$checked = (in_array($qcs, $selectedStages) OR empty($selectedStages)) ? "checked":""?>
                        <div class="checkbox-list-row">
                        <span class="checkbox-text">
                            <label class="checkbox-wrapper-multiple inline <?=$checked?>" data-val="<?=$qcs?>">
                                <span class="checkbox-replace"></span>
                                 <i class="checkbox-list-tick q4bikon-tick"></i>
                            </label>
                            <?=__($qcs)?>
                        </span>
                        </div>
                    <?endforeach?>
                </div>
                <select class="hidden-select" name="project_stage[]" multiple>
                    <?foreach($qcStages as $qcs):?>
                        <?$selected = (in_array($qcs, $selectedStages) OR empty($selectedStages)) ? "selected":""?>
                        <option value="<?= $qcs ?>" <?=$selected?> ><?=__($qcs)?></option>
                    <?endforeach?>
                </select>
            </div>
        </div>
        <div class="form-group col-md-2 rtl-float-right">
            <label class="table_label"><?=__('Responsible profession')?> <span class="q4-required">*</span></label>
            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                <select class="q4-select q4-form-input" name="profession_id">
                    <option selected="selected" value="all"><?=__('All')?></option>
                    <?foreach($profs as $prof):?>
                        <option value="<?=$prof['id']?>"<?=$selectedProfession==$prof['id'] ? " selected":""?>  ><?=$prof['name']?></option>
                    <?endforeach?>
                </select>
            </div>
        </div>
    </div>
    <input type="hidden" name="advanced" value="1">
</div>
