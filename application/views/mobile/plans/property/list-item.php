<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 12.03.2017
 * Time: 7:38
 */
?>
<div class="item">
    <div class="q4-carousel-blue-head">
        <?if($item->state == Enum_ObjectState::Approved):?>
            <span class="blue-head-title"><?=$item->name?></span>
            <div class="blue-head-option">
                <a class="show-structure-mobile show-structure-layout pr-object-show-struct" href="#" data-url="<?=URL::site('projects/object_property_struct/'.$_PROJECT->company_id.'/'.$item->project_id.'/'.$item->id)?>">
                    <i class="plus q4bikon-preview"></i>
                </a>
            </div>
        <?endif?>
    </div>
    <div class="q4-carousel-row f0">
        <div class="q4-mobile-table-key">
            <?=__('Object Type')?>
        </div>
        <div class="q4-mobile-table-value">
            <?=$item->type->name?>
        </div>
    </div>


    <div class="q4-carousel-row f0">
        <div class="q4-mobile-table-key">
            <?=__('Floors')?>&#x200E;
        </div>
        <div class="q4-mobile-table-value">
            <span  class="bidi-override floors-from" >
            (<?=$item->smaller_floor.')&#x200E;</span> - <span class="bidi-override floors-to">('.$item->bigger_floor.')&#x200E;</span>'?>
        </div>
    </div>
    <div class="q4-carousel-row f0">
        <div class="q4-mobile-table-key">
            <?=__('Elements')?>
        </div>
        <div class="q4-mobile-table-value">
                <span class="bidi-override">
                    <?=$item->places_count?>
                </span>
        </div>
    </div>


    <div class="q4-carousel-row f0">
        <div class="q4-mobile-table-key">
            <?=__('Start Date')?>
        </div>
        <div class="q4-mobile-table-value">
            <!-- <div id="property-start_date-<?=$item->id?>" class="input-group date" data-provide="datepicker">
                <div class="input-group-addon small-input-group">
                    <span class="glyphicon glyphicon-calendar"></span>
                </div>
                <input type="text" class="form-control disabled-input" data-date-format="DD/MM/YYYY" name="property_<?=$item->id?>_start_date" value="<?=date('d/m/Y',$item->start_date)?>">
            </div> -->
             <div class="div-cell">
                <span><?=date('d/m/Y',$item->start_date)?></span>
            </div>
        </div>
    </div>
    <div class="q4-carousel-row f0">
        <div class="q4-mobile-table-key">
            <?=__('End Date')?>
        </div>
        <div class="q4-mobile-table-value">
           <!--  <div id="property-end_date-<?=$item->id?>" class="input-group date" data-provide="datepicker">

                <div class="input-group-addon small-input-group">
                    <span class="glyphicon glyphicon-calendar"></span>
                </div>
                <input type="text" class="form-control disabled-input" data-date-format="DD/MM/YYYY" name="property_<?=$item->id?>_end_date" value="<?=date('d/m/Y',$item->end_date)?>">
            </div> -->
            <div class="div-cell">
                <span><?=date('d/m/Y',$item->end_date)?></span>
            </div>
        </div>
    </div>
</div>
