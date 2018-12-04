<?defined('SYSPATH') OR die('No direct script access.');?>

<?php
/**
 * CHECK_AGAIN
 * Created by PhpStorm.
 * User: SUR0
 * Date: 13.10.2016
 * Time: 19:03
 */
?>
<tr>
    <td>
        <input type="radio" name="profession_<?=$item->id?>_status" value="<?=Enum_Status::Enabled?>" <?=$item->status == Enum_Status::Enabled ? 'checked="checked"' : null?>>
        <input type="radio" name="profession_<?=$item->id?>_status" value="<?=Enum_Status::Disabled?>" <?=$item->status == Enum_Status::Disabled ? 'checked="checked"' : null?>>
    </td>
    <td><input type="text" value="<?=$item->name?>" name="profession_<?=$item->id?>_name"></td>
    <td>
        <?if(!empty($crafts)):?>
            <select name="profession_<?=$item->id?>_crafts" id="" multiple="multiple">
                <?foreach($crafts as $craft):?>
                    <?if($craft->status != Enum_Status::Enabled) continue;?>
                    <option value="<?=$craft->id?>" <?=(!empty($items_crafts[$item->id]) AND in_array($craft->id,$items_crafts[$item->id])) ? 'selected="selected"' : ''?>><?=__($craft->name)?></option>
                <?endforeach?>
            </select>
        <?endif?>
    </td>
    <td><input type="text" name="profession_<?=$item->id?>_catalog_number" value="<?=$item->catalog_number?>"></td>
    <td><?=__('Enabled')?></td>
</tr>
