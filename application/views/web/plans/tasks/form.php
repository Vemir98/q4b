<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 12.03.2017
 * Time: 1:27
 */
//$projectCrafts = $_PROJECT->company->crafts->where('status','=','enabled')->order_by('name')->find_all();
$projectCrafts = $_PROJECT->company->craftsWithProfessionsFlag();
?>
<form action="<?=$action?>" class="q4_form tasks-form" autocomplete="off" method="post" data-ajax="true">
<input type="hidden" value="" name="x-form-secure-tkn"/>
    <div class="panel_body container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <div class="panel-options">
                    <span><?=__('Add new task')?></span>
                    <a class="orange_plus_small add-task"><i class="plus q4bikon-plus"></i></a>
                </div>
                <div class="tasks-nested-tb">
                    <table class="responsive_table" data-toggle="table">
                        <thead>
                        <tr>

                            <th class="td-350 nested-adj-first"><?=__('Description')?></th>
                            <th class="td-350 nested-adj-first"><?=__('Crafts')?></th>
                            <th class="td-50 hidden_status"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="hidden el-pattern">

                            <td data-th="<?=__('Task Name')?>">
                                <textarea class="q4_required nested-adj-large fixed-textarea textarea-cell" data-attr="name" data-name="task_%s_name"></textarea>
                            </td>
                            <td data-th="<?=__('Crafts')?>">
                                <div class="multi-select-box">
                                    <?if(false):?>
                                        <div class="select-imitation">
                                            <span class="select-imitation-title">--<?=__('select')?>--</span>
                                            <div class="over-select"></div>
                                            <i class="arrow-down q4bikon-arrow_bottom"></i>
                                        </div>
                                        <div class="checkbox-list-no-scroll">

                                            <?foreach ($projectCrafts as $craft):?>
                                                <?if(!$craft['belongs_to_profession']) continue;?>
                                             <div class="checkbox-list-row">
                                                <span class="checkbox-text">
                                                    <label class="checkbox-wrapper-multiple inline" data-val="<?=$craft['id']?>">
                                                        <span class="checkbox-replace"></span>
                                                        <i class="checkbox-list-tick q4bikon-tick"></i>
                                                    </label>
                                                    <?=$craft['name']?>
                                                </span>
                                             </div>
                                            <?endforeach?>
                                        </div>
                                    <?endif?>
                                    <div class="select-wrapper">
                                        <i class="q4bikon-arrow_bottom"></i>
                                        <select class="q4-select q4-form-input" data-name="task_%s_crafts">
                                            <?foreach ($projectCrafts as $craft):?>
                                                <?if(!$craft['belongs_to_profession']) continue;?>
                                            <option value="<?=$craft['id']?>"><?=$craft['name']?></option>
                                            <?endforeach;?>
                                        </select>
                                    </div>
                                </div>
                            </td>

                            <td data-th="<?=__('Action')?>">
                                <div class="q4_radio">
                                    <div class="toggle_container">
                                        <label class="label_unchecked">
                                            <input type="radio" data-name="task_%s_status" value="<?=Enum_Status::Disabled?>"><span></span>
                                        </label>
                                        <label class="label_checked">
                                            <input type="radio" data-name="task_%s_status" value="<?=Enum_Status::Enabled?>" checked="checked"><span></span>
                                        </label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?if(!empty($items)):?>
                        <?foreach($items as $item):?>
                                <?$crafts = $item->crafts->order_by('name','ASC')->find_all();$cnames = '';
                                $craftArray  = [];
                                    foreach ($crafts as $cr) {
                                        $craftArray[$cr->id] = $cr->name;
                                    }
                                ?>


                        <tr>
                            <td class="nested-adj-large textarea-cell" data-th="<?=__('Description')?>">
                                <textarea name="task_<?=$item->id?>_name"  class="table_input q4_required fixed-textarea collapsed" title="<?=$item->name?>"><?=trim($item->name)?></textarea>
                            </td>
                            <td data-th="<?=__('Crafts')?>">
                                <div class="multi-select-box">
                                    <?if(false):?>
                                        <div class="select-imitation">
                                            <span class="select-imitation-title"><?=implode(',',array_values($craftArray))?></span>
                                            <div class="over-select"></div>
                                            <i class="arrow-down q4bikon-arrow_bottom"></i>
                                        </div>
                                        <div class="checkbox-list-no-scroll">

                                            <?foreach ($projectCrafts as $craft):?>
                                                <?if(!$craft['belongs_to_profession']) continue;?>
                                                <?
                                               $checkedCraft = in_array($craft['id'],array_keys($craftArray)) ? ' checked' : '';
                                               $disabledCraft = '';
                                               $craftModel = $item->crafts->where('cmpcraft.id','=',(int)$craft['id'])->find();
                                                if($craftModel->loaded()){

                                                    $disabledCraft = $craftModel->quality_controls->count_all()<1 ? '': ' disabled-input';
                                                }
                                                ?>
                                             <div class="checkbox-list-row<?=$disabledCraft?>">
                                                <span class="checkbox-text">
                                                    <label class="checkbox-wrapper-multiple inline<?=$checkedCraft?>" data-val="<?=$craft['id']?>">
                                                        <span class="checkbox-replace"></span>
                                                        <i class="checkbox-list-tick q4bikon-tick"></i>
                                                    </label>
                                                    <?=$craft['name']?>
                                                </span>
                                             </div>
                                            <?endforeach?>
                                        </div><!--.checkbox-list-->
                                    <?endif?>
                                    <div class="select-wrapper">
                                        <i class="q4bikon-arrow_bottom"></i>
                                        <select class="q4-select q4-form-input" name="task_<?=$item->id?>_crafts">
                                            <?foreach ($projectCrafts as $craft):?>
                                                <?if(!$craft['belongs_to_profession']) continue;?>
                                            <option value="<?=$craft['id']?>" <?=in_array($craft['id'],array_keys($craftArray)) ? 'selected="selected"' : ''?>><?=$craft['name'] ?></option>
                                            <?endforeach;?>
                                        </select>
                                      </div>

                                </div>
                            </td>
                            <td class="td-75 hidden_status" data-th="<?=__('Action')?>">
                                <div class="q4_radio">
                                        <?if($item->status == Enum_Status::Enabled):?>
                                    <div class="toggle_container">
                                            <label class="label_unchecked">
                                                <input type="radio" name="task_<?=$item->id?>_status" value="<?=Enum_Status::Disabled?>"><span></span>
                                            </label>
                                            <label class="label_checked">
                                                <input type="radio" name="task_<?=$item->id?>_status" checked="checked" value="<?=Enum_Status::Enabled?>"><span></span>
                                            </label>
                                    </div>
                                        <?else:?>
                                    <div class="toggle_container disabled">
                                            <label class="label_checked">
                                                <input type="radio" name="task_<?=$item->id?>_status" checked="checked" value="<?=Enum_Status::Disabled?>"><span></span>
                                            </label>
                                            <label class="label_unchecked">
                                                <input type="radio" name="task_<?=$item->id?>_status" value="<?=Enum_Status::Enabled?>"><span></span>
                                            </label>
                                    </div>
                                        <?endif?>
                                </div>
                            </td>
                        </tr>
                        <tr class="nested-row">
                            <td class="empty-th nested-empty-cell"></td>
                            <td colspan="8" class="empty-th nested-cell">
                                <div class="collapse nested-div" id="row-level-<?=$item->id?>">
                                    <table class="table table-striped nested_table">
                                        <thead></thead>
                                        <tbody>
                                        <?foreach($crafts as $craft):?>
                                        <tr>
                                            <td data-th="<?=__('Crafts')?>">
                                                <div class="div-cell"><?=$craft->name?></div>
                                            </td>
                                            <td class="td-350" data-th="<?=__('Name')?>">
                                                <div class="div-cell">
                                            <?if($item->crafts_relation->where('craft_id','=',$craft->id)->find()->status == Enum_ProjectTaskQqStatus::Checked):?>
                                                <a href="#" data-toggle="modal" data-target="#tasks-quality-control-list-modal">Quality forms list</a>
                                            <?endif?>
                                                </div>
                                            </td>
                                            <!-- <td class="nested-adj-large" data-th="<?=__('Used in Quality form')?>">
                                                <div class="div-cell"><?=$item->crafts_relation->where('craft_id','=',$craft->id)->find()->status?></div>
                                            </td> -->
                                            <td class="td-75 hidden_status" data-th="<?=__('Action')?>">

                                            </td>
                                        </tr>
                                        <?endforeach;?>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <?endforeach?>
                        <?endif?>
                        </tbody>
                    </table>
                </div><!--tasks-nested-tb-->
            </div>
        </div>

    </div>
    <div class="panel_footer text-align">
        <div class="row">
            <div class="col-lg-12 col-sm-12">
                <a href="#" class="inline_block_btn orange_button submit"><?=__('Update')?></a>
            </div>
        </div>
    </div>
</form>
