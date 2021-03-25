<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 12.03.2017
 * Time: 7:37
 */
?>
<style>
    .properties_container {
        margin-top: 13px;
        width: 100%;
    }
    .properties_container_list_item{
        list-style-type: none;
        width: 100%;
        text-align: left;
        margin: 0;
        padding: 10px 0;
    }
    .properties_container_list_item_name {
        font-size: 18px;
        font-style: normal;
        font-weight: normal;
        line-height: 14px;
        text-align: left;
        color: #003a63;
        margin-right:6px;
    }
    .rtl .properties_container_list_item_name{
        margin-right:0;
        margin-left: 6px;
    }
    .properties_container_list_item_value {
        font-size: 22px;
        font-style: normal;
        font-weight: 600;
        line-height: 14px;
        text-align: left;
        color: #1ebae5 !important;
    }
    .properties_container_list_item_value .q4bikon-reports3 {
        padding: 5px 5px;
    }
    .properties_container_list_item_value .q4bikon-reports3:hover {
        padding: 5px 5px;
        background: #FFF2E0;
        border-radius: 8px;
    }
    .properties_cont {
        width: 100%;
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
    }
    .properties_cont .column {
        max-width: 295px;
        width: 100%;
        flex: 1;
    }
    .property-structure-list::-webkit-scrollbar {
        height: 5px;
    }
    .property-structure-list {
        scrollbar-width: thin;
    }

    .property-structure-list::-webkit-scrollbar-track {
        background: #DAE1EC;
    }

    .property-structure-list::-webkit-scrollbar-thumb {
        background: #1EBAE5;
        border-radius: 20px;
    }
</style>
<div class="panel_body container-fluid property-struct">
<div class="row">
    <div class="col-md-12">
        <div class="back-property-table-layout">
            <i class="q4bikon-arrow_back2 fs-22"></i>
            <a class="go-to-proj-props" href="#" data-url="<?=URL::site('projects/project_properties/'.$item->project_id)?>"><?=__('Back to list')?></a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="properties_container mb-25">
            <div class="properties_cont">
                <div class="column">
                    <ul>
                        <li class="properties_container_list_item">
                            <span class="properties_container_list_item_name"><?=__('Object Type')?></span>
                            <span class="properties_container_list_item_value"> <?=$item->type->name?></span>
                        </li>
                        <li class="properties_container_list_item">
                            <span class="properties_container_list_item_name"><?=__('Name')?></span>
                            <span class="properties_container_list_item_value "> <?=$item->name?></span>
                        </li>
                        <li class="properties_container_list_item">
                            <span class="properties_container_list_item_name"><?=__('Floors (from-to)')?></span>
                            <span class="properties_container_list_item_value"><?=__('from')?> <?=$item->smaller_floor?> <?=__('to')?> <?=$item->bigger_floor?> (<?=$item->getFloorsCount()?>)</span>
                        </li>
                        <li class="properties_container_list_item">
                            <span class="properties_container_list_item_name"><?=__('Places')?></span>
                            <span class="properties_container_list_item_value"><?=$item->places_count?></span>
                        </li>
                    </ul>
                </div>
                <div class="column">
                    <ul>
                        <li class="properties_container_list_item">
                            <span class="properties_container_list_item_name"><?=__('Start Date')?></span>
                            <span class="properties_container_list_item_value"><?=date('d/m/Y',$item->start_date)?></span>
                        </li>
                        <li class="properties_container_list_item">
                            <span class="properties_container_list_item_name"><?=__('End Date')?></span>
                            <span class="properties_container_list_item_value"><?=date('d/m/Y',$item->end_date)?></span>
                        </li>
                        <li class="properties_container_list_item">
                            <span class="properties_container_list_item_name"><?=__('Tasks report')?></span>
                            <span class="properties_container_list_item_value" style="vertical-align: middle;">
                                <a style="border-bottom:none" title="<?=__('Click to view report for structure')?>" class="structure-report open-report-modal cursor-pointer" data-url="<?=URL::site('reports/tasks/'.$item->project_id.'/'.$item->id)?>">
                                    <i class="icon q4bikon-reports3" style="font-size:18px;color: #f99c19;"></i>
                                </a>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?foreach ($itemFloors as $floor):?>
        <div class="property-structure-list-group" style="position: relative">
        <div class="property-structure-floor-title" style="display: flex; justify-content: space-between;min-width: 507px; padding: 9px 15px; height: 36px; background: #064266; border-radius: 5px 0px 0px 0px; position: absolute; left: 0; top: 0; color: #fff; clip-path: polygon(0 0, 100% 0, 96% 100%, 0 100%);">
            <div class="" style="flex-grow: 3;">
                <span style="font-size: 18px; color: #fff; margin-right: 10px;">Constant Technologies</span><span style="color: #BEBEBE; font-size: 16px">(floor 1)</span>
            </div>
            <div class="" style="color: #1EBAE5; display: flex; justify-content: space-around; flex-grow: 1; padding: 0 5px;">
                <div class="open-report-modal cursor-pointer" title="<?=__('Click to view report for floor')?>" data-url="<?=URL::site('reports/tasks/'.$item->project_id.'/'.$item->id.'/'.$floor->id)?>">
                    <i class="q4bikon-edit2" style="padding: 0 5px;"></i>
                </div>
                <i class="q4bikon-reports3 open-report-modal cursor-pointer" title="<?=__('Click to view report for floor')?>" data-url="<?=URL::site('reports/tasks/'.$item->project_id.'/'.$item->id.'/'.$floor->id)?>" style="padding: 0 5px;"></i>
            </div>
        </div>
            <div class="property-structure-apartments" style="padding-bottom: 10px; margin-left: 0;border: 1px solid #F2F9FF; border-right: 5px solid #1ebae5;margin-right: 0;border-radius: 5px;background: #F2F9FF;box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);height: auto;">
                <div class="property-structure-list" style="margin-top: 50px;">
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
                                        <span class="copy-element place-copy w32" data-url="<?=URL::site('projects/place_copy/'.$floor->object_id.'/'.$floor->id.'/'.$place->id)?>"><i class="q4bikon-copy"></i></span>
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