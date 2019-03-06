<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 17.01.2017
 * Time: 14:19
 */



?>
<style>
    select#profile-lang option[value="en"]   { background-image:url('/media/data/flags/en-us.jpg');   }
    select#profile-lang option[value="ru"] { background-image:url('/media/data/flags/ru-ru.jpg'); }
    select#profile-lang option[value="he"] { background-image:url('/media/data/flags/he-il.jpg'); }
</style>
<!-- Show details Modal -->
<div id="user-prf-modal" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" >
    <div class="modal-dialog q4_modal" role="document">
        <div class="modal-content">
            <div class="modal-header q4_modal_header">
                <div class="q4_modal_header-top">
                    <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                    <div class="clear"></div>
                </div>
                <div class="q4_modal_sub_header">

                    <h3><?=__('User details')?></h3>
                </div>
            </div>
            <form action="<?=URL::site('user/profile')?>" class="form-horizontal cc_form" data-ajax="true">
                <div class="modal-body">
                    <div class="plans-modal-dialog-top">
                        <input type="hidden" value="" name="x-form-secure-tkn"/>
                        <div class="form_row form-group">
                            <div class="col-md-6">
                <?//echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($selectedLang); echo "</pre>"; ?>
                                <label class="table_label"><?=__('Name')?></label>
                                <div class="form_row">
                                    <input type="text" name="name" class="q4-form-input symbol modal_input required" value="<?=$user->name?>"/>
                                    <i class="input_icon q4bikon-username"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="table_label"><?=__('Email')?></label>
                                <div class="form_row">
                                    <input type="text" name="email" class="q4-form-input symbol modal_input required q4_email" value="<?=$user->email?>"/>
                                    <i class="input_icon q4bikon-email"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form_row form-group">
                            <div class="col-md-6">
                                <label class="table_label"><?=__('Phone')?></label>
                                <div class="form_row">
                                    <input type="text" name="phone" class="q4-form-input symbol modal_input" value="<?=$user->phone?>"/>
                                    <i class="input_icon q4bikon-phone"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="table_label"><?=__('Choose language')?></label>
                                <div class="form_row">
                                    <div class="select-language" id="select-language">
                                        <div class="default-option">
                                            <div class="option">
                                                <label for="flag-<?=$user->lang?>">
                                                    <img src="<?=$selectedLang->image?>" alt="<?=$selectedLang->name?>" /><?=__($selectedLang->name)?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="options">
                                            <?foreach (Language::getAll() as $lang):?>
                                                <div class="option">
                                                    <?$checked = $user->lang == $lang->iso2 ? "checked" : ''?>
                                                    <input type="radio" name="lang" value="<?=$lang->iso2?>" id="flag-<?=$lang->iso2?>" <?=$checked?>/>
                                                    <label for="flag-en">
                                                        <img src="<?=$lang->image?>" alt="<?=$lang->iso2?>" />  <?=__($lang->name)?>
                                                    </label>
                                                </div>
                                            <?endforeach?>
                                        </div>
                                        <i class="q4bikon-arrow_bottom"></i>
                                    </div><!--.select-language-->
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label class="table_label"><?=__('change password')?></label>
                            </div>
                        </div>
                        <div class="form-group">

                            <div class="col-md-6">
                                <label class="table_label"><?=__('new password')?></label>
                                <div class="form_row">
                                    <input type="password" class="q4-form-input symbol modal_input" value="" name="password" />
                                    <i class="input_icon q4bikon-password"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="table_label"><?=__('confirm password')?></label>
                                <div class="form_row">
                                    <input type="password" class="q4-form-input symbol modal_input" value="" name="password_confirm"  />
                                    <i class="input_icon q4bikon-password"></i>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer text-align">
                    <a href="#" class="inline_block_btn orange_button cc_form_submit submit"><?=__('Update')?></a>
                </div>
            </form>
        </div>
    </div>
</div><!-- end Show details Modal -->