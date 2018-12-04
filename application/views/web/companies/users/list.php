<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.11.2016
 * Time: 14:36
 */
?>
<?if(count($items)):?>
    <?foreach($items as $item):?>
        <?=View::make($_VIEWPATH.'list-item',
            [
                'item' => $item,
                'professions' => $professions,
                'roles' => $roles
            ])?>

    <?endforeach?>
<?endif?>
