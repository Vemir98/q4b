<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 20.02.2019
 * Time: 13:22
 */
?>
<?foreach($item->company->professions->where('status','=',Enum_Status::Enabled)->find_all() as $prof):?>
    <?
    $crafts = $prof->crafts->where('status','=',Enum_Status::Enabled)->find_all();
    $c = [];
    foreach ($crafts as $cr)
        $c []= $cr->id;
    if(empty($c)) continue;
    ?>
    <option value="<?=$prof->id?>" data-crafts="<?=implode(',',$c)?>"><?=$prof->name?></option>
<?endforeach?>