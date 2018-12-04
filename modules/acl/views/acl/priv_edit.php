<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.09.2016
 * Time: 15:43
 */
?>
<h2>Edit Privilege</h2>
<form action="" method="post">
    <fieldset>
        <label for="alias">Alias</label><br>
        <?=Form::input('alias',$privilege->alias,['id' => 'alias'])?>
    </fieldset>
    <fieldset>
        <label for="name">Name</label><br>
        <?=Form::input('name',$privilege->name,['id' => 'name'])?>
    </fieldset>
    <input type="submit" name="submit" value="Submit">
</form>
