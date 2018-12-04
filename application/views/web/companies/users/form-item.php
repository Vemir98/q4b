<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 09.01.2017
 * Time: 14:56
 */
?>
<tr<?=($item->status == Enum_UserStatus::Pending) ? ' class="pending_row"' : ''?>>
    <td class="rwd-td0" data-th="<?=__('Name')?>">
        <input type="text" class="table_input q4_required" name="user_<?=$item->id?>_name" value="<?=$item->name?>">
    </td>
    <td class="rwd-td1" data-th="<?=__('Email')?>">
        <input type="text" class="table_input q4_email" name="user_<?=$item->id?>_email" value="<?=$item->email?>">
    </td>
    <td class="rwd-td2" data-th="<?=__('Professions')?>">
        <div class="select-wrapper">
            <i class="q4bikon-arrow_bottom"></i>
            <select name="user_<?=$item->id?>_profession"  class="q4-select q4-form-input">
                <?foreach ($professions as $prof):?>
                    <?if($prof->status != Enum_Status::Enabled AND $item->getProfession('id') != $prof->id) continue;?>
                    <option value="<?=$prof->id?>" <?=($item->getProfession('id') == $prof->id) ? 'selected="selected"' : ''?>><?=__($prof->name)?></option>
                <?endforeach;?>
            </select>
        </div>
    </td>
    <td class="rwd-td3" data-th="<?=__('User Group')?>">
        <div class="select-wrapper">
             <i class="q4bikon-arrow_bottom"></i>
            <select name="user_<?=$item->id?>_role"  class="q4-select q4-form-input">
                <?foreach($roles as $urId => $urName):?>
                    <option value="<?=$urId?>" <?=($item->getRelevantRole('id') == $urId) ? 'selected="selected"' : ''?>><?=__($urName)?></option>
                <?endforeach?>
            </select>
        </div>
    </td>
    <td class="align-center-left" data-th="<?=__('Show Details')?>">
        <a class="show_details cmp-user-details" data-url="<?=URL::site('companies/user_details/'.$_COMPANY->id.'/'.$item->id)?>"><i class="plus q4bikon-preview"></i></a>
    </td>
    <td class="rwd-td4 hidden_status" data-th="<?=__('Status')?>">
            <?if($item->status == Enum_UserStatus::Active):?>
            <div class="q4_radio">
                <div class="toggle_container">
                    <label class="label_unchecked">
                        <input type="radio" name="user_<?=$item->id?>_status" value="<?=Enum_UserStatus::Blocked?>"><span></span>
                    </label>
                    <label class="label_checked">
                        <input type="radio" name="user_<?=$item->id?>_status" value="<?=Enum_UserStatus::Active?>"  checked="checked"><span></span>
                    </label>
                </div>
            </div>
        <?else:?>
            <div class="q4_radio">
                <div class="toggle_container disabled">
                    <label class="label_checked">
                        <input type="radio" name="user_<?=$item->id?>_status" value="<?=Enum_UserStatus::Blocked?>" checked="checked"><span></span>
                    </label>
                    <label class="label_unchecked">
                        <input type="radio" name="user_<?=$item->id?>_status" value="<?=Enum_UserStatus::Active?>"><span></span>
                    </label>
                </div>
            </div>
        <?endif?>
    </td>

</tr>
