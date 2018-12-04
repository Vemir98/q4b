<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.07.2017
 * Time: 13:17
 */
?>
<?if(count($items)):?>
<ul>
    <?foreach ($items as $i):?>
        <li><?=$i->id?> <?=htmlspecialchars($i->name)?></li>
    <?endforeach;?>
</ul>
<?endif;?>
