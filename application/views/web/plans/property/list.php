<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 21.12.2016
 * Time: 12:23
 */
?>
<?if(count($items)):?>
    <?foreach($items as $item):?>
        <?=View::make($_VIEWPATH.'list-item',
            [
                'item' => $item,
                'itemTypes' => $itemTypes
            ])?>
    <?endforeach?>
<?endif?>
