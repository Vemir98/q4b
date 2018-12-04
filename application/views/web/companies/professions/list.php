<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 13.10.2016
 * Time: 19:03
 */
?>
<?if(!empty($items)):?>
    <?foreach($items as $item):?>
        <?=View::make($_VIEWPATH.'list-item',
            [
                'item' => $item,
                'items_crafts' => $items_crafts,
                'crafts' => $crafts
            ])?>
    <?endforeach?>
<?endif?>
