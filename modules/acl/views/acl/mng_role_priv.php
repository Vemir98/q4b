<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 25.11.2016
 * Time: 1:16
 */
?>
<a href="<?=Route::url('acl.resources-manager',['action' => 'index'])?>">back</a> <br>
<h4>Role - <?=$role->name?></h4>
<form method="post">
    <select name="privileges[]" multiple="multiple" style="height: 500pt">
        <?foreach ($resPrivs as $rp):?>
            <option <?=in_array($rp['id'],$rolePrivs) ? 'selected="selected"' : ''?> value="<?=$rp['id']?>"><?=$rp['name']?></option>
        <?endforeach;?>
    </select><br>
    <input type="submit" name="submit" value="Submit">
</form>