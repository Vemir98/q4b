<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 17.10.2016
 * Time: 11:25
 */
?>
<?if(!empty($items)):?>
    <?foreach($items as $item):?>
        <?=View::make($_VIEWPATH.'list-item',['item' => $item, 'users' => $users])?>
    <?endforeach?>
<?endif?>
