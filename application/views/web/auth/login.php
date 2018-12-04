<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 17.01.2017
 * Time: 13:28
 */
?>

    <div class="login_form_head">
        <div class="login_logo-bg">
            <a href="#" title="logo"><img src="/media/img/logo_web2.png" alt="logo"/></a>
        </div>
        <span class="welcome"><?=__('Welcome to Q4B')?></span>
    </div>
    <div class="login_form_body no_title">
        <form action="<?=URL::site()?>" class="q4_form" autocomplete="off" method="post" data-ajax="true">
            <input type="hidden" value="" name="x-form-secure-tkn"/>
            <div class="form-group form_row">
                <input type="email" name="login" value="<?=$username?>" class="q4-form-input symbol login_input q4_required q4-login-email" placeholder="<?=__('Email')?>"/>
                <i class="input_icon q4bikon-username"></i>
            </div>
            <div class="form-group form_row">
                <input type="password" name="pass" class="q4-form-input symbol login_input q4_password q4-login-pass" placeholder="<?=__('Password')?>"/>
                <i class="input_icon q4bikon-password"></i>
            </div>
            <div class="text-left-right">
                <div class="inline-chkbx">
                    <label  class="checkbox-wrapper">
                        <input name="remember" type="checkbox" >
                         <span class="checkbox-replace"></span><i class="checkbox-tick q4bikon-tick"></i>
                    </label>
                </div>
                <div class="inline-chkbx-text"> <?=__('Remember me')?></div>
            </div>

            <span class="check_password_match"></span>
            <div class="form-group form_row">
                <input type="submit" class="q4-form-input text-center light_blue_btn q4_form_submit" value="<?=__('Login')?>">
            </div>
            <a class="login_hint" href="<?=URL::site('/forgot_password')?>"><?=__('forgot your password?')?></a>
        </form>
    </div>



