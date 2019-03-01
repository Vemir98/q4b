<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 20.02.2019
 * Time: 13:22
 */
?>
<option value=''><?=__('Please select')?></option>
<?foreach($item->company->crafts->where('status','=',Enum_Status::Enabled)->order_by('name')->find_all() as $craft):?>
    <?
    $profs = $craft->professions->where('status','=',Enum_Status::Enabled)->find_all();
    $p = [];
    foreach ($profs as $pr)
        $p []= $pr->id;
    if(empty($p)) continue;
    ?>
    <option value="<?=$craft->id?>" data-professions="<?=implode(',',$p)?>"><?=$craft->name?></option>
<?endforeach?>