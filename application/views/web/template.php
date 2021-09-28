<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 28.12.2016
 * Time: 17:40
 */
$detector = new Mobile_Detect;
$token = !$_USER->isGuest() ? $_USER->getAuthToken() : null;
?>
<!DOCTYPE html>
<html lang="<?=Language::getCurrent()->iso2?>" class="<?=Language::getCurrent()->direction?>" <?=$detector->isMobile() ? 'data-mobile="true"': '' ?>>
<head>
    <meta charset="UTF-8">
    <title>Q4B</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="tkn" content="<?=Security::token(true)?>">
    <meta name="fps" content="<?=(int)ini_get('upload_max_filesize')?>-<?=(int)ini_get('post_max_size')?>">
    <meta name="base-uri" content="<?=URL::base()?>">
    <meta name="current-uri" content="<?=Request::current()->uri()?>">
    <meta name="u_token" content="<?=$token?>">
    <meta name="site_url" content="<?=trim(URL::site('','http'),'/')?>">
    <meta name="api_path" content="/api/json/v2">
    <link rel="stylesheet" href="/media/css/styles.min.css">
    <link rel="stylesheet" href="/media/css/select2.min.css">
    <link rel="stylesheet" href="/media/css/print.min.css">
    <script src="/media/js/jquery-2.2.4.min.js"></script>
    <script src="/media/js/core.js"></script>
    <script src="/media/js/select2.js"></script>
    <link rel="stylesheet" href="/media/css/jquery.multiselect.css">
    <link rel="stylesheet" href="/media/css/new_styles.css">

    <?=VueJs::instance()->render()?>

    <script src="/media/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/media/js/bootstrap.min.js"></script>
    <script src="/media/js/bootstrap-table.min.js"></script>
    <script src="/media/js/utilities.js" type="text/javascript" ></script>
    <script src="/media/js/jquery.autocomplete.js"></script>
    <script src="/media/js/jquery.multiselect.js"></script>
    <script src= "/media/js/zingchart.min.js"></script>
    <script src="https://unpkg.com/json-form-data@^1.7.0/dist/jsonToFormData.min.js"></script>
    <script src="/media/js/scripts.js"></script>
    <script src="/media/js/print.min.js"></script>
    <?if(file_exists(DOCROOT."media/js/".Inflector::singular(strtolower(Request::current()->controller())).".js")):?>
    <script src="/media/js/<?=Inflector::singular(strtolower(Request::current()->controller()))?>.js"></script>
    <?endif?>
    <script>
        window.eventBus = new Vue({})
    </script>
    <script type="text/javascript" src="/media/js/vue/libs/vue2-pagination/vue-pagination-2-master/dist/vue-pagination-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vee-validate@<3.0.0/dist/vee-validate.js"></script>
    <script src="/media/js/moment.min.js"></script>
    <script src="/media/js/moment-timezone.js"></script>
    <?if(!$include_editor):?>
        <script src="/media/js/pixel_perfect/js/pixp.editor.js"></script>
    <?endif;?>
    <script>
        Vue.use(VeeValidate, {
            dictionary: {
                msg: {
                    messages: {
                        "required": "<?=__('This field is required.')?>",
                        "decimal": "<?=__('This field should be numeric.')?>",
                        "numeric": "<?=__('This field should be numeric.')?>"
                    }
                }
            }
        });
    </script>
</head>
<body>
    <style >
  /*      ::-webkit-scrollbar {
    width: 10px;
}

/* Track */
::-webkit-scrollbar-track {
    box-shadow: inset 0 0 5px grey;
    border-radius: 20px;
}

/* Handle */
::-webkit-scrollbar-thumb {
    background: #1ebae5;
    border-radius: 10px;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
    background: #b30000;
}*/
    </style>
    <div style="display:none" class="loader_backdrop">
        <div class="loader"></div>
    </div>
    <?if(empty($rawContent)):?>
    <div class="wrapper no-print">
        <?=View::make($_VIEWPATH.'layout/sidebar')?>
        <div class="layout">
            <?=View::make($_VIEWPATH.'layout/header')?>
            <div class="content">
                <?=render($content)?>
                <?if(rand(0,99) > 23) Security::mousetrapRandLink()?>
            </div>


            <div class="q4-copyright classis">
                <span><?=__('Copyright © 2017 Q4B')?></span>
                <span><?=__('All right reserved')?></span>
            </div>
        </div>
    </div>
    <?else:?>
        <?=render($rawContent)?>
    <?endif?>
<?if(rand(0,99) > 69) Security::mousetrapLink()?>
<?if(!Usr::agreed_terms()):?>
    <?=View::make('auth/license')?>
<?endif;?>

<script>

    <?if(!Usr::agreed_terms()):?>
        var INTERVAL = setInterval(function(){
            if(!$(document).find('#licence-agreement-modal').length>0){

                document.documentElement.innerHTML='';
                clearInterval(INTERVAL);
            }
        },3000);
    <?endif?>
    <?if(Session::instance()->get_once('showProfile')):?>
    var SHOW_PROFILE = true;
    <?else:?>
    var SHOW_PROFILE = false;
    <?endif?>
</script>
<!-- end File Upload Modal -->

<script>Q4U.i18n.init(<?=JSON::encode(I18n::load(Language::getCurrent()->iso2))?>)</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/react/0.14.7/react-with-addons.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/react/0.14.7/react-dom.js"></script>
<script src="/media/js/literallycanvas.js"></script>
<script src="/media/js/owl.carousel.js"></script>
<script src="/media/js/jcarousellite.min.js"></script>
<script src="/media/js/jquery.mCustomScrollbar.js"></script>
<!--<script src="/media/js/moment.min.js"></script>-->
<script src="/media/js/bootstrap-datetimepicker.js"></script>


<script src="/media/js/loader.js"></script>
<script src="/media/js/validation.js"></script>
    <?if($include_editor):?>
        <?require_once('utility/pixp-editor-modal.php')?>
    <?endif;?>
    <div class="progress-bg">
        <div class="progress-bar-modal">
                <span class="progress-bar-text"></span>
            <div id="my-progress">
                <div id="my-bar"></div>
            </div>
            <span class="progress-bar-status"><?=__('loading')?></span>
        </div>
    </div>
</body>
</html>