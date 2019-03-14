<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 21.02.2019
 * Time: 9:48
 */
?>
<?$i=1;?>
<?foreach($spaces as $space):?>
    <option value="<?=$space->id?>"><?=$i.' '.$space->type->name.' '. $space->desc?></option>
<?$i++;?>
<?endforeach?>