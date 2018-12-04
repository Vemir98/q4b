<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.01.2017
 * Time: 22:50
 */
?>


<div class="item">
    <div class="q4-carousel-blue-head">
        <span class="blue-head-title"><?=__('Name').' : '.$item->name?></span>
        <div class="blue-head-option">
            <a class="show-structure-mobile" href="#" data-url="<?=URL::site('reports/quality_control/'.$i->id)?>">
                <i class="plus q4bikon-preview"></i>
            </a>
        </div>
    </div>
    <div class="q4-carousel-row f0" >
        <div class="q4-mobile-table-key">
            <?=__('Catalog Number')?>
        </div>
        <div class="q4-mobile-table-value">
            <?=$item->catalog_number?>
        </div>
    </div>
    <div class="q4-carousel-row f0">
        <div class="q4-mobile-table-key">
            <?=__('Status')?>
        </div>
        <div class="q4-mobile-table-value">
            <?=__($item->status)?>
        </div>
    </div>
</div>
