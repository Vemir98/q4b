<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 13.01.2017
 * Time: 6:51
 */
?>
<tr>
    <td data-th="<?=__('Name')?>">
        <input type="text" class="table_input " name="standard_<?=$item->id?>_name" value="<?=$item->name?>">
    </td>
    <td data-th="<?=__('Organisation')?>">
        <input type="text" class="table_input " name="standard_<?=$item->id?>_organisation" value="<?=$item->organisation?>">
    </td>
    <td data-th="<?=__('Number')?>">
        <input type="text" class="table_input q4_number" name="standard_<?=$item->id?>_number" value="<?=$item->number?>">
    </td>
    <td data-th="<?=__('Submission Place')?>">
        <input type="text" class="table_input " name="standard_<?=$item->id?>_submission_place" value="<?=$item->submission_place?>">
    </td>
    <td data-th="<?=__('Responsible person')?>">
        <div class="select-wrapper">
            <i class="q4bikon-arrow_bottom"></i>
            <select class="q4-select q4-form-input" required name="standard_<?=$item->id?>_responsible_person">
                <?if(count($users)):?>
                <?foreach ($users as $user):?>
                    <?if($user->status != Enum_UserStatus::Active) continue;?>
                    <option value="<?=$user->id?>"<?=($item->responsible_person == $user->id) ? 'selected="selected"' : null?>><?=$user->email?></option>
                <?endforeach?>
                    <?else:?>
                    <option value="">---</option>
                <?endif?>
            </select>
        </div>
    </td>
    <td data-th="<?=__('File(s)')?>" class="min_width_140">
        <div class="wrap_files">
            <a class="list-uploads standard-file" data-url="<?=URL::site('companies/standard_files/'.$_COMPANY->id.'/'.$item->id)?>">
                <i class="q4bikon-file"></i>
            </a>
            <div class="file_container">
                <a href="#" class="standard-file load-file-form"><?=__('Load')?></a>
                <input class="hidden std_file_input input-file-form" type="file" accept=".doc,.docx,.xls,.xlsx,.pdf,.ppg,.plt,.jpg,.jpe,.jpeg,.png,.gif,.tif,.tiff" multiple="multiple" name="files[<?=$item->id?>][]">
            </div>
        </div>
    </td>
    <td class="hidden_status" data-th="<?=__('Status')?>">
        <div class="q4_radio">
            <div class="toggle_container">
                <label class="label_unchecked">
                    <input type="radio" name="toggle" value="0"><span></span>
                </label>
                <label class="label_checked">
                    <input type="radio" name="toggle" value="1"><span></span>
                </label>
            </div>
        </div>
    </td>
</tr>
