<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 12.03.2017
 * Time: 7:37
 */
?>
<div class="panel_body container-fluid property-struct">
<div class="row">
    <div class="col-md-12">
        <div class="property-structure-layout-top">
            <div class="back-property-table-layout">
                <i class="q4bikon-arrow_left"></i>
                <a class="go-to-proj-props" href="#" data-url="<?=URL::site('projects/project_properties/'.$item->project_id)?>"><?=__('Back to list of properties')?></a>
            </div>
            <a class="object-type-table-btn go-to-floor">
                <span></span>
                <span></span>
                <span></span>
            </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="q4-floor-numbers-mobile-box">
            <span class="q4-floor-numbers-mobile"><?=__('Total').' '. count($itemFloors).' '.__('floor(s)')?>&#x200E;</span>
        </div>

        <div class="object-type-table-box">
            <div class="form-group">
                <table class="object-type-table">
                    <thead>
                        <tr>

                            <th><?=__('Element number')?></th>
                            <th><?=__('Floor')?></th>

                        </tr>
                    </thead>
                    <tbody>
                    <tr>

                        <td data-th="<?=__('Element number')?>">
                            <div class="div-cell">
                                <input type="text" class="q4-form-input go-to-place-number " value="">
                            </div>
                        </td>
                        <td data-th="<?=__('Go to')?>">
                            <div class="div-cell">
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select class="q4-select q4-form-input select-go-to-floor">
                                        <option value=""><?=__('Please select')?></option>
                                        <?$floors = [];?>
                                        <?for ($i = $item->smaller_floor;$i<=$item->bigger_floor;$i++ ){
                                            $floors[]=$i;
                                        }
                                        ?>
                                        <?foreach ($floors as $number=>$floor):?>
                                            <option class="bidi-override" value="<?=count($floors)-$number-1?>">
                                               <?=$floor?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </div>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <a class="btn light_blue_btn disabled-input go-to-place-button-structure" ><?=__('Go to')?></a>
            </div>
        </div>

        <div class="property-structure-list-box mobile-property">
            <a class="property-floors-arrow prev">
                <i class="q4bikon-arrow_top"></i>
            </a>
            <a class="property-floors-arrow next">
                <i class="q4bikon-arrow_bottom"></i>
            </a>
            <div class="wrap-property-structure-list" data-floor="<?=count($itemFloors)?>">
                <ul>
                <?foreach ($itemFloors as $number=>$floor):?>
                    <li >
                        <div class="property-structure-list-group">

                            <div class="property-structure-floors-mobile">
                                <span data-floornumber="<?=$number?>" class="structure-floor-number rotate"><?if($floor->number == 0):?><?=__('Ground Floor')?><?else:?><?=__('Floor')?> <?=$floor->number?><?endif?></span>
                            </div>
                            <div class="property-structure-apartments-mobile">
                            <?$places = $floor->places->order_by('ordering',($floor->number < 0) ? 'DESC' :'ASC')->find_all()?>

                                <div data-structurecount="<?=count($places)?>" id="structure-<?=$floor->id?>" class="property-structure-list-mobile q4-owl-carousel">
                                    <?foreach ($places as $idPl=>$place):?>
                                    <div class="item" data-number="<?=$idPl?>" data-placenumber="<?= !empty($place->custom_number) ? strtolower(mb_substr($place->custom_number,0,8)) : ($place->type == 'public'? 'PB' : 'N').$place->number?>">
                                        <div class="apartment-box-mobile">
                                            <div class="apartment-box-top <?if($place->type == Enum_ProjectPlaceType::PrivateS):?>blue<?else:?>gray<?endif?>">
                                                <span class="apartment-box-top-icon"><i class="q4bikon-<?=$place->type?>"></i></span>
                                                <h5 class="apartment-box-title"><?=__($place->name)?></h5>
                                            </div>
                                            <div class="apartment-box-bottom">
                                                <span class="bottom-box">
                                                    <span class="apartment-circle blue location" data-toggle="modal" data-target="#property-rooms-clicked-modal">
                                                        <i class="<?=!empty($place->icon) ? $place->icon : 'q4bikon-appartment' ?>"></i> <span class="location-number"><?$isp = $place->spaces->count_all(); if($isp > 1) echo $isp;?></span>
                                                    </span>
                                                </span>
                                                <span class="bottom-box number-box">

                                                        <?$title = !empty($place->custom_number) ? "title='".$place->custom_number."'" : '';?>


                                                    <span class="q4-form-input" <?=$title?>><?= !empty($place->custom_number) ? mb_substr($place->custom_number,0,8) : ($place->type == 'public'? 'PB' : 'N').$place->number?></span>
                                                    <span data-url="<?=URL::site('projects/property_item_quality_control_list/'.$place->id)?>" class="apartment-number <?=$place->quality_control->count_all() ? 'quality-control-list' : ''?>"><?if($place->type == 'public'):?>PB<?else:?>N<?endif?><?=$place->number?></span>
                                                </span>
                                                <span class="bottom-box">
                                                    <span class="apartment-circle count <?=($place->quality_control->count_all() ? 'orange' : 'gray')?> create-quality-control" data-url="<?=URL::site('projects/quality_control/'.$place->id)?>">
                                                        <i class="q4bikon-checked"></i>
                                                    </span>
                                                </span>
                                                <div class="clear"></div>
                                            </div>

                                        </div>
                                    </div>
                                    <?endforeach?>

                                </div><!--.property-structure-list-->

                            </div>
                        </div><!--.property-structure-list-group-->
                    </li>
                <?endforeach;?>
                </ul>
            </div>
        </div>
    </div>
</div>
</div>