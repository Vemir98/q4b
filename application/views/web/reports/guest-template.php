<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 28.12.2016
 * Time: 17:40
 */
?>
<!DOCTYPE html>
<html lang="en" class="<?=Language::getCurrent()->direction?>">
<head>
    <meta charset="UTF-8">
    <title>Q4B</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="tkn" content="<?=Security::token(true)?>">
    <meta name="base-uri" content="<?=URL::base()?>">
    <meta name="fps" content="<?=(int)ini_get('upload_max_filesize')?>-<?=(int)ini_get('post_max_size')?>">
    <meta name="current-uri" content="<?=Request::current()->uri()?>">
    <link rel="stylesheet" href="/media/css/styles.min.css">
</head>
<body>
<div class="wrapper">
    <div class="layout">
        <div class="content">
            <?=render($content)?>
        </div>
    </div>
</div>

<script src="/media/js/jquery-2.2.4.min.js"></script>
<script src="/media/js/core.js"></script>
<script>Q4U.i18n.init(<?=JSON::encode(I18n::load(Language::getCurrent()->iso2))?>)</script>
<script src="/media/js/jquery-ui.min.js"></script>
<script src="/media/js/bootstrap-select.min.js" type="text/javascript"></script>
<script src="/media/js/bootstrap.min.js"></script>
<script src="/media/js/bootstrap-table.min.js"></script>
<script src="/media/js/bootstrap-table-en-US.min.js"></script>
<script src="/media/js/utilities.js" type="text/javascript" ></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/react/0.14.7/react-with-addons.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/react/0.14.7/react-dom.js"></script>
<script src="/media/js/literallycanvas.js"></script>
<script src="/media/js/owl.carousel.js"></script>
<script src="/media/js/jquery.mCustomScrollbar.js"></script>
<script src="/media/js/moment.min.js"></script>
<script src="/media/js/bootstrap-datetimepicker.js"></script>
<script src="/media/js/loader.js"></script>
<script src="/media/js/validation.js"></script>
<?if(file_exists(DOCROOT."media/js/".Inflector::singular(strtolower(Request::current()->controller())).".js")):?>
    <script src="/media/js/<?=Inflector::singular(strtolower(Request::current()->controller()))?>.js"></script>
<?endif?>
<script src="/media/js/scripts.js"></script>

</body>
</html>