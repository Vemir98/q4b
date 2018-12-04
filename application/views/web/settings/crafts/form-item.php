<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.01.2017
 * Time: 22:50
 */
?>
<tr>
    <td data-th="<?=__('Name')?>">
        <input type="text" name="craft_<?=$item->id?>_name" class="table_input required" value="<?=$item->name?>">
        </td>
    <td data-th="<?=__('Catalog Number')?>">
        <input type="text" name="craft_<?=$item->id?>_catalog_number" class="table_input " value="<?=$item->catalog_number?>">
    </td>
    <td class="hidden_status" data-th="<?=__('Status')?>">
        <?if($item->status == Enum_Status::Enabled):?>
            <div class="q4_radio">
                <div class="toggle_container">
                    <label class="label_unchecked">
                        <input type="radio" name="craft_<?=$item->id?>_status" value="<?=Enum_Status::Disabled?>"><span></span>
                    </label>
                    <label class="label_checked">
                        <input type="radio" name="craft_<?=$item->id?>_status" value="<?=Enum_Status::Enabled?>"  checked="checked"><span></span>
                    </label>
                </div>
            </div>
        <?else:?>
            <div class="q4_radio">
                <div class="toggle_container disabled">
                    <label class="label_checked">
                        <input type="radio" name="craft_<?=$item->id?>_status" value="<?=Enum_Status::Disabled?>" checked="checked"><span></span>
                    </label>
                    <label class="label_unchecked">
                        <input type="radio" name="craft_<?=$item->id?>_status" value="<?=Enum_Status::Enabled?>"><span></span>
                    </label>
                </div>
            </div>
        <?endif?>
    </td>
</tr>
