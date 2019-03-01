<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 22.02.2019
 * Time: 14:11
 */
?>
<?foreach($items as $item):?>
    <option value="<?=$item->id?>"><?=$item->name?></option>
<?endforeach?>
