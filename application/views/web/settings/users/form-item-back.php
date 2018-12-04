<tr>
    <td data-th="<?=__('Name')?>" <?if($item->status != Enum_Status::Enabled):?>class="disable"<?endif?>>
        <input type="text" class="table_input q4_required" name="profession_<?=$item->id?>_name" value="<?=$item->name?>">
    </td>
    <td data-th="<?=__('Company')?>" <?if($item->status != Enum_Status::Enabled):?>class="disable"<?endif?>>
        <?if(!empty($crafts)):?>

            <div class="select-wrapper">
                <i class="q4bikon-arrow_bottom"></i>
                <select class="q4-select q4-form-input">
                    <option value="Company name 1">Company name 1</option>
                    <option value="Company name 2">Company name 2</option>
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
            <span class="status_text enable disable"><?=__('Enabled')?></span>
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