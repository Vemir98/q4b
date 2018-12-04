<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.09.2016
 * Time: 15:43
 */
?>
<h2>Add Resource</h2>
<form action="" method="post">
    <fieldset>
        <label for="alias">Alias</label><br>
        <?=Form::input('alias',$data['alias'],['id' => 'alias'])?>
    </fieldset>
    <fieldset>
        <label for="name">Name</label><br>
        <?=Form::input('name',$data['name'],['id' => 'name'])?>
    </fieldset>
    <fieldset>
        <label for="privileges">Privileges</label><br>
        <?=Form::select('privileges[]',$privileges,$data['privileges'],['multiple' => 'multiple'])?>
    </fieldset>
    <input type="submit" name="submit" value="Submit">
</form>
