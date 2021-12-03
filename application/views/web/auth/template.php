<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 17.01.2017
 * Time: 13:13
 */
?>
<!DOCTYPE html>
<html lang="<?=Language::getCurrent()->iso2?>" class="<?=Language::getCurrent()->direction?>">
    <head lang="en">
        <meta charset="UTF-8">
        <title>Q4B | <?=__('Login')?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="tkn" content="<?=Security::token(true)?>">
        <meta name="base-uri" content="<?=URL::base()?>">
        <meta name="current-uri" content="<?=Request::current()->uri()?>">
        <link rel="stylesheet" href="/media/css/ls.min.css">
    </head>
    <body class="login_page">
        <div class="select-language-box">
            <div class="select-language" id="select-language">
                <div class="default-option">
                    <div class="option">

                        <label for="flag-en">
                            <img src="<?=Language::getCurrent()->image?>" alt="<?=Language::getCurrent()->iso2?>" /><?=__(Language::getCurrent()->name)?>
                        </label>
                    </div>
                </div>
                <div class="options">
                    <?foreach (Language::getAll() as $lang):?>
                        <?if(Language::getCurrent()->iso2 == $lang->iso2) continue?>
                        <div class="option">
                            <input type="radio" name=lang" value="red" id="flag-en" checked/>
                            <a href="<?=Route::url(Request::$current->route()->name(Request::$current->route()), array_merge(Request::$current->param(),['lang' => $lang->slug, 'controller' => Request::$current->controller(), 'action' => Request::$current->action()])).URL::query()?>">
                                <img src="<?=$lang->image?>" alt="<?=$lang->iso2?>" />  <?=__($lang->name)?>
                            </a>
                        </div>
                    <?endforeach?>
                </div>
                <i class="q4bikon-arrow_bottom"></i>
            </div><!--.select-language-->
        </div><!--.select-language-box-->

        <div class="login_form">
        <?=Security::mousetrapLink()?>
        <?=render($content)?>
        </div>
        <div class="loader_backdrop"><div class="loader"></div></div>
        <script src="/media/js/jquery-2.2.4.min.js"></script>
        <script src="/media/js/bootstrap.min.js"></script>
        <script src="/media/js/core.js"></script>
        <script>Q4U.i18n.init(<?=JSON::encode(I18n::load(Language::getCurrent()->iso2))?>)</script>

        <script src="/media/js/validation.js"></script>
    </body>
</html>
