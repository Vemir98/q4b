<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 02.05.2017
 * Time: 3:11
 */
?>

<?foreach ($objects as $obj):?>
    <option value="<?=$obj->id?>"><?=$obj->type->name .' - '.$obj->name?></option>
<?endforeach;?>