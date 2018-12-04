<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 09.01.2017
 * Time: 11:39
 */
?>
<tr>
    <td data-th="<?=__('Name')?>" <?if($item->status != Enum_Status::Enabled):?>class="disable"<?endif?>>
        <input type="text" class="table_input q4_required" name="profession_<?=$item->id?>_name" value="<?=$item->name?>">
    </td>
    <td data-th="<?=__('Crafts')?>" <?if($item->status != Enum_Status::Enabled):?>class="disable"<?endif?>>
        <?if(!empty($crafts)):?>
            <div class="multi-select-box">
                <div class="select-imitation table_input">
                    <span class="select-imitation-title">
                        <?$tmpStr = ''?>
                        <?foreach($crafts as $craft):?>
                            <?if(!empty($items_crafts[$item->id]) AND in_array($craft->id,$items_crafts[$item->id])):?>
                                <?$tmpStr .= ($craft->name.', ')?>
                            <?endif?>
                        <?endforeach?>
                        <?=preg_replace('~, $~','',$tmpStr)?>
                        <?unset($tmpStr)?>
                    </span>
                    <div class="over-select"></div>
                    <i class="arrow-down q4bikon-arrow_bottom"></i>
                </div>
                <div class="checkbox-list-no-scroll">
                    <?foreach($crafts as $craft):?>
                        <?if($craft->status != Enum_Status::Enabled) continue;?>
                        <div class="checkbox-list-row">
                            <span class="checkbox-text">
                                <label class="checkbox-wrapper-multiple inline <?=(!empty($items_crafts[$item->id]) AND in_array($craft->id,$items_crafts[$item->id])) ? 'checked' : ''?>"  data-val="<?=$craft->id?>">
                                    <span class="checkbox-replace"></span>
                                    <i class="checkbox-list-tick q4bikon-tick"></i>
                                </label>
                                <?=$craft->name?>
                            </span>
                        </div>
                    <?endforeach?>
                </div>
                <select name="profession_<?=$item->id?>_crafts" class="hidden-select" multiple="multiple">
                    <?foreach($crafts as $craft):?>
                        <?if($craft->status != Enum_Status::Enabled) continue;?>
                        <option value="<?=$craft->id?>" <?=(!empty($items_crafts[$item->id]) AND in_array($craft->id,$items_crafts[$item->id])) ? 'selected="selected"' : ''?>><?=$craft->name?></option>
                    <?endforeach?>
                </select>
            </div>

        <?endif?>
    </td>
    <td data-th="<?=__('Catalog Number')?>" <?if($item->status != Enum_Status::Enabled):?>class="disable"<?endif?>>
        <input type="text" class="table_input" name="profession_<?=$item->id?>_catalog_number" value="<?=$item->catalog_number?>">
    </td>
    <?if($item->status == Enum_Status::Enabled):?>
        <td class="hidden_status" data-th="<?=__('Status')?>">

            <div class="q4_radio">
                <div class="toggle_container">
                    <label class="label_unchecked">
                        <input type="radio" name="profession_<?=$item->id?>_status" value="<?=Enum_Status::Disabled?>"><span></span>
                    </label>
                    <label class="label_checked">
                        <input type="radio" name="profession_<?=$item->id?>_status" value="<?=Enum_Status::Enabled?>" checked="checked"><span></span>
                    </label>
                </div>
            </div>
        </td>
    <?else:?>
        <td class="hidden_status" data-th="<?=__('Status')?>">
            <div class="q4_radio">
                <div class="toggle_container disabled">
                    <label class="label_checked">
                        <input type="radio" name="profession_<?=$item->id?>_status" value="<?=Enum_Status::Disabled?>" checked="checked"><span></span>
                    </label>
                    <label class="label_unchecked">
                        <input type="radio" name="profession_<?=$item->id?>_status" value="<?=Enum_Status::Enabled?>"><span></span>
                    </label>
                </div>
            </div>
        </td>
    <?endif?>
</tr>
