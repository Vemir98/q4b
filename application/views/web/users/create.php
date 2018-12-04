<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 19.09.2016
 * Time: 22:30
 */
?>
<form action="" method="post" data-ajax="true">
    <label for="email"><?=__('Email')?></label><br>
    <input type="email" id="email" name="email" required><br>
    <label for="username"><?=__('Username')?></label><br>
    <input type="text" id="username" name="username" required><br>
    <label for="password"><?=__('Password')?></label><br>
    <input type="password" id="password" name="password" required><br>
    <label for="password_confirm"><?=__('Password Confirm')?></label><br>
    <input type="password" id="password_confirm" name="password_confirm" required><br>
    <?=Form::select('roles',$roles,null,['multiple' => 'multiple', 'required'])?>
    <button type="submit" name="submit"><?=__('Save')?></button>
</form>
