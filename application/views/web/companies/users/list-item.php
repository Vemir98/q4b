<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.11.2016
 * Time: 14:36
 */
?>
<tr>
    <td><?=__('Del')?></td>
    <td><input type="text" name="user_<?=$item->id?>_name" value="<?=$item->name?>"></td>
    <td><input type="email" name="user_<?=$item->id?>_email" value="<?=$item->email?>"></td>
    <td>
        <select name="user_<?=$item->id?>_profession">
            <?foreach ($professions as $prof):?>
                <?if($prof->status != Enum_Status::Enabled AND $item->getProfession('id') != $prof->id) continue;?>
                <option value="<?=$prof->id?>" <?=($item->getProfession('id') == $prof->id) ? 'selected="selected"' : ''?>><?=__($prof->name)?></option>
            <?endforeach;?>
        </select>
    </td>
    <td>
        <select name="user_<?=$item->id?>_role">
            <?foreach($roles as $urId => $urName):?>
                <option value="<?=$urId?>" <?=($item->getRelevantRole('id') == $urId) ? 'selected="selected"' : ''?>><?=__($urName)?></option>
            <?endforeach?>
        </select>
    </td>
    <td><?=$item->status?></td>
    <td><button type="button" class="cmp-user-details" data-url="<?=URL::site('companies/user_details/'.$_COMPANY->id.'/'.$item->id)?>"><?=__('show details')?></button></td>
</tr>
