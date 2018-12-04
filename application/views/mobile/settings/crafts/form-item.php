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

<!--<tr>-->
<!--    <td data-th="--><?//=__('Name')?><!--">-->
<!--        <input type="text" name="craft_--><?//=$item->id?><!--_name" class="table_input required" value="--><?//=$item->name?><!--">-->
<!--        </td>-->
<!--    <td data-th="--><?//=__('Catalog Number')?><!--">-->
<!--        <input type="text" name="craft_--><?//=$item->id?><!--_catalog_number" class="table_input " value="--><?//=$item->catalog_number?><!--">-->
<!--    </td>-->
<!--    <td class="hidden_status" data-th="--><?//=__('Status')?><!--">-->
<!--        --><?//if($item->status == Enum_Status::Enabled):?>
<!--            <div class="q4_radio">-->
<!--                <div class="toggle_container">-->
<!--                    <label class="label_unchecked">-->
<!--                        <input type="radio" name="craft_--><?//=$item->id?><!--_status" value="--><?//=Enum_Status::Disabled?><!--"><span></span>-->
<!--                    </label>-->
<!--                    <label class="label_checked">-->
<!--                        <input type="radio" name="craft_--><?//=$item->id?><!--_status" value="--><?//=Enum_Status::Enabled?><!--"  checked="checked"><span></span>-->
<!--                    </label>-->
<!--                </div>-->
<!--            </div>-->
<!--        --><?//else:?>
<!--            <div class="q4_radio">-->
<!--                <div class="toggle_container disabled">-->
<!--                    <label class="label_checked">-->
<!--                        <input type="radio" name="craft_--><?//=$item->id?><!--_status" value="--><?//=Enum_Status::Disabled?><!--" checked="checked"><span></span>-->
<!--                    </label>-->
<!--                    <label class="label_unchecked">-->
<!--                        <input type="radio" name="craft_--><?//=$item->id?><!--_status" value="--><?//=Enum_Status::Enabled?><!--"><span></span>-->
<!--                    </label>-->
<!--                </div>-->
<!--            </div>-->
<!--        --><?//endif?>
<!--    </td>-->
<!--</tr>-->
