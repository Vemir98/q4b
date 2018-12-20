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
        <div class="back-property-table-layout">
            <i class="q4bikon-arrow_left"></i>
            <a class="go-to-proj-props" href="#" data-url="<?=URL::site('projects/project_properties/'.$item->project_id)?>"><?=__('Back to list of properties')?></a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="back-property-table-content">
            <table class="plain-table">
                <thead>
                <tr>
                    <th><?=__('Object Type')?></th>
                    <th><?=__('Name')?></th>
                    <th><?=__('Floors (from-to)')?></th>
                    <th><?=__('Places')?></th>
                    <th><?=__('Start Date')?></th>
                    <th><?=__('End Date')?></th>
                    <th><?=__('Tasks report')?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td data-th="<?=__('Object Type')?>"><div class="div-cell"><?=$item->type->name?></div></td>
                    <td data-th="<?=__('Name')?>"><div class="div-cell"><?=$item->name?></div></td>
                    <td data-th="<?=__('Floors (from-to)')?>"><div class="div-cell"><?=__('from')?> <?=$item->smaller_floor?> <?=__('to')?> <?=$item->bigger_floor?> (<?=$item->getFloorsCount()?>)</div></td>
                    <td data-th="<?=__('Places')?>"><div class="div-cell"><?=$item->places_count?></div></td>
                    <td data-th="<?=__('Start Date')?>"><div class="div-cell"><?=date('d/m/Y',$item->start_date)?></div></td>
                    <td data-th="<?=__('End Date')?>"><div class="div-cell"><?=date('d/m/Y',$item->end_date)?></div></td>
                    <td data-th="<?=__('Tasks report')?>"><div class="div-cell">
                        <a style="border-bottom:none" title="<?=__('Click to view report for structure')?>" class="structure-report open-report-modal cursor-pointer" data-url="<?=URL::site('reports/tasks/'.$item->project_id.'/'.$item->id)?>">
                            <i class="icon q4bikon-reports" style="font-size:30px"></i>
                        </a>
                    </div></td>
                </tr>
                </tbody>
            </table>

        </div>
        <?foreach ($itemFloors as $floor):?>
        <div class="property-structure-list-group">
            <div class="property-structure-actions inactive">
                <span class="copy-element present-modal floor-copy" data-url="<?=URL::site('projects/floor_copy/'.$floor->project_id.'/'.$floor->object_id.'/'.$floor->id)?>"><i class="q4bikon-copy"></i></span>
                <span class="edit-element"><i class="q4bikon-edit"></i></span>
                <span class="delete_row present-modal floor-delete" data-url="<?=URL::site('projects/floor_delete/'.$floor->project_id.'/'.$floor->object_id.'/'.$floor->id)?>"><i class="q4bikon-delete"></i></span>
            </div>

            <div class="property-structure-floors open-report-modal cursor-pointer"  title="<?=__('Click to view report for floor')?>" data-url="<?=URL::site('reports/tasks/'.$item->project_id.'/'.$item->id.'/'.$floor->id)?>">
                <span class="structure-floor-number rotate"><?if($floor->number == 0):?><?=__('Ground Floor')?><?else:?><?=__('Floor')?> <?=$floor->number?><?endif?></span>
            </div>
            <div class="property-structure-apartments">

                <div class="property-structure-list">
                    <ul id="structure-<?=$floor->id?>" class="property-structure-list-items">
                        <?foreach ($floor->places->order_by('ordering',($floor->number < 0) ? 'DESC' :'ASC')->find_all() as $place):?>
                        <li>
                            <div class="apartment-box">
                                <div class="apartment-box-top <?if($place->type == Enum_ProjectPlaceType::PrivateS):?>blue<?else:?>gray<?endif?>">
                                    <span class="apartment-box-top-icon"><i class="q4bikon-<?=$place->type?>"></i></span>
                                    <h5 class="apartment-box-title"><?=__($place->name)?></h5>
                                </div>
                                <div class="apartment-box-bottom">
                                    <span class="bottom-box">
                                        <span class="apartment-circle  location<?=($place->quality_control->count_all() ? ' orange cursor-pointer open-report-modal' : ' blue')?>" <?=$place->quality_control->count_all() ? " title='".__('Click to view report for place')."'" : "" ?>" data-url="<?=URL::site('reports/tasks/'.$item->project_id.'/'.$item->id.'/'.$floor->id.'/'.$place->id)?>" data-toggle="modal" data-target="#property-rooms-clicked-modal">
                                            <?$isp = $place->spaces->count_all()?>
                                            <i class="<?=!empty($place->icon) ? $place->icon : 'q4bikon-stairway' ?>" <?if($isp > 1) echo 'style="font-size:20px"'?>></i> <span class="location-number"><?if($isp > 1) echo $isp;?></span>
                                        </span>
                                    </span>
                                    <span class="bottom-box number-box">
                                        <div class="apartment-box-input">
                                            <?$title = !empty($place->custom_number) ? "title='".$place->custom_number."'" : '';?>
                                            <div class="q4-form-input" <?=$title?>><?= !empty($place->custom_number) ? mb_substr($place->custom_number,0,5) : ($place->type == 'public'? 'PB' : 'N').$place->number?></div>
                                        </div>
                                        <span data-url="<?=URL::site('projects/property_item_quality_control_list/'.$place->id)?>" class="apartment-number <?=($place->quality_control->count_all() ? 'quality-control-list' : '')?>">
                                            <?if($place->type == 'public'):?>PB<?else:?>N<?endif?><?=$place->number?>
                                        </span>
                                    </span>
                                    <span class="bottom-box">
                                        <span class="apartment-circle <?=($place->quality_control->count_all() ? 'orange' : 'gray')?>  create-quality-control cursor-pointer" data-url="<?=URL::site('projects/quality_control/'.$place->id)?>">
                                            <?=($place->quality_control->count_all() ? '<i class="q4bikon-checked"></i>' : '<i class="q4bikon-uncheked"></i>')?>
                                        </span>
                                    </span>
                                    <div class="clear"></div>
                                </div>

                                <div class="apartment-box-clicked">
                                    <span class="apartment-box-clicked-close"><i class="q4bikon-close"></i></span>
                                    <div class="apartment-box-click-actions">
                                        <span class="add-element place-add" data-url="<?=URL::site('projects/place_create/'.$floor->object_id.'/'.$floor->id.'/'.$place->id)?>"><i class="q4bikon-plus"></i></span>
                                        <span class="copy-element place-copy" data-url="<?=URL::site('projects/place_copy/'.$floor->object_id.'/'.$floor->id.'/'.$place->id)?>"><i class="q4bikon-copy"></i></span>
                                        <span class="edit-element-clicked place-edit" data-url="<?=URL::site('projects/place_update/'.$floor->object_id.'/'.$floor->id.'/'.$place->id)?>"><i class="q4bikon-edit"></i></span>
                                        <span class="delete-element place-delete" data-url="<?=URL::site('projects/place_delete/'.$floor->object_id.'/'.$floor->id.'/'.$place->id)?>"><i class="q4bikon-delete"></i></span>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?endforeach?>
                    </ul>
                </div><!--.property-structure-list-->

            </div>
        </div><!--.property-structure-list-group-->
        <?endforeach;?>

    </div>
</div>
</div>