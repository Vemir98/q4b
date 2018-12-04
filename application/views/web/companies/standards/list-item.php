<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 17.10.2016
 * Time: 11:26
 */
?>
<tr>
    <td><?=__('Del')?></td>
    <td><input type="text" required name="standard_<?=$item->id?>_name" value="<?=$item->name?>"></td>
    <td><input type="text" required name="standard_<?=$item->id?>_organisation" value="<?=$item->organisation?>"></td>
    <td><input type="text" required name="standard_<?=$item->id?>_number" value="<?=$item->number?>"></td>
    <td><input type="text" required name="standard_<?=$item->id?>_submission_place" value="<?=$item->submission_place?>"></td>
    <td>
        <?if(count($users)):?>
            <select required name="standard_<?=$item->id?>_responsible_person">
                <?foreach ($users as $user):?>
                    <?if($user->status != Enum_UserStatus::Active) continue;?>
                    <option value="<?=$user->id?>"<?=($item->responsible_person == $user->id) ? 'selected="selected"' : null?>><?=$user->email?></option>
                <?endforeach?>
            </select>
        <?endif?>
    </td>
    <td><a href="#" class="standard-file  list-uploads" data-url="<?=URL::site('companies/standard_files/'.$_COMPANY->id.'/'.$item->id)?>"><?=__('view')?></a> <input type="file" accept=".doc,.docx,.xls,.xlsx,.pdf,.ppg,.plt,.jpg,.jpe,.jpeg,.png,.gif,.tif,.tiff" multiple="multiple" name="files[<?=$item->id?>][]"></td>
</tr>