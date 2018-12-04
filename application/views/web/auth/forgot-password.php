<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 17.01.2017
 * Time: 13:29
 */
?>
<div class="login_form_head">
    <div class="login_logo-bg">
        <a href="/" title="Logo"><img src="/media/img/logo_web2.png" alt="logo"/></a>
    </div>
    <span class="welcome"><?=__('Welcome to')?> Q4B</span>
</div>
<div class="login_form_body">
    <h3><?=__('Reset Password')?></h3>
    <form action="<?=URL::site('forgot_password')?>" class="q4_form" data-ajax="true">
        <input type="hidden" value="" name="x-form-secure-tkn"/>
        <div class="form-group form_row">
            <input type="text" name="email" class="form-control login_input q4_email send-email-ad-input" placeholder="<?=__('Email')?>"/>
            <i class="input_icon q4bikon-username"></i>
        </div>
        <div class="form-group form_row top_padding">
            <a href="#" class="form-control light_blue_btn q4_form_submit send-email-ad"/><?=__('Send')?></a>
        </div>
        <p class="login_hint">
            <?=__('forgot password text')?>
        </p>
    </form>
</div>
<div class="text-center">
    <a class="login_link" href="<?=URL::site()?>"><?=__('back to login')?></a>
</div>
