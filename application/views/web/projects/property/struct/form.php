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
            <div class="back-property-table-layout structures-header-container">
                <a class="go-to-proj-props" href="#" data-url="<?=URL::site('projects/project_properties/'.$item->project_id)?>"><i class="q4bikon-arrow_back2 fs-22"></i> <?=__('Back to list')?></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 structures-container">
            <div class="properties_container mb-25">
                <div class="properties_cont">
                    <span class="properties_container_list_item">
                        <span class="properties_container_list_item_name"><?=__('Name')?></span>
                        <span class="properties_container_list_item_value">
                            <?=$item->name?> <span class="ml_5 mr_5">|</span>
                        </span>
                    </span>
                    <span class="properties_container_list_item">
                        <span class="properties_container_list_item_name"><?=__('Floors')?></span>
                        <span class="properties_container_list_item_value">
                            <?=$item->getFloorsCount()?> <span class="ml_5 mr_5">|</span>
                        </span>
                    </span>
                    <span class="properties_container_list_item">
                        <span class="properties_container_list_item_name"><?=__('Places')?></span>
                        <span class="properties_container_list_item_value">
                            <?=$item->places_count?> <span class="ml_5 mr_5">|</span>
                        </span>
                    </span>
                    <span class="properties_container_list_item">
                        <span class="properties_container_list_item_name"><?=__('Tasks report')?></span>
                        <span class="properties_container_list_item_value">
                            <a title="<?=__('Report for structure')?>" class="structure-report open-report-modal cursor-pointer" data-url="<?=URL::site('reports/tasks/'.$item->project_id.'/'.$item->id)?>">
                                <i class="icon icon-orange q4bikon-reports fs-18"></i>
                            </a>
                        </span>
                    </span>
                </div>
            </div>
            <?foreach ($itemFloors as $floor):?>
                <div class="property-structure-list-group relative mt-25">
                    <div class="property-structure-floor-cont">
                        <div class="structure-floor-cont-top">
                            <div class="structure-title-cont flex justify-flex-start">
                                <div class="property-structure-floor-title">
                                    <span class="floor-name-input" name="floor-name-input" data-maxlength="30"><?if($floor->custom_name):?><?=$floor->custom_name?><?else:?><?=__('Floor')?> <?=$floor->number?><?endif?></span>
                                </div>
                                <div class="ml-15">
                                    <span class="text-grey valign-top fs-16">(<?=$floor->number?>)</span>
                                </div>
                            </div>
                            <div class="structure-floor-actions">
                                <div class="edit-floor-custom-name cursor-pointer mr_20" title="<?=__('Edit floor description')?>"
                                     data-name="<?if($floor->custom_name):?><?=$floor->custom_name?><?else:?><?=__('Floor')?> <?=$floor->number?><?endif?>"
                                     data-url="<?=URL::site('projects/floor_update_title/'.$floor->project_id.'/'.$floor->object_id.'/'.$floor->id)?>">
                                    <i class="icon-blue q4bikon-edit2"></i>
                                </div>
                                <div class="floor-report open-report-modal cursor-pointer mr_20" title="<?=__('Report for floor')?>" data-url="<?=URL::site('reports/tasks/'.$item->project_id.'/'.$item->id.'/'.$floor->id)?>">
                                    <i class="icon-blue q4bikon-reports"></i>
                                </div>
                            </div>
                        </div>
                        <div class="property-structure-actions-cont">
                            <span class="add-elem cursor-pointer place-add show-tooltip" title="<?=__('Add')?>" data-url="<?=URL::site('projects/place_create/'.$floor->object_id.'/'.$floor->id.'/'.$floor->places->order_by('ordering',($floor->number < 0) ? 'DESC' :'ASC')->find()->id)?>">
                                <i class="icon icon-orange q4bikon-plus2" style="font-size: 24px"></i>
                            </span>
                            <span class="present-modal copy-elem present-modal show-tooltip floor-copy w32 cursor-pointer" title="<?=__('Copy')?>" data-url="<?=URL::site('projects/floor_copy/'.$floor->project_id.'/'.$floor->object_id.'/'.$floor->id)?>">
                                <i class="icon icon-orange q4bikon-copy2" style="font-size: 20px"></i>
                            </span>
                            <span class="present-modal delete-elem present-modal show-tooltip floor-delete cursor-pointer" title="<?=__('Delete')?>" data-url="<?=URL::site('projects/floor_delete/'.$floor->project_id.'/'.$floor->object_id.'/'.$floor->id)?>">
                                <i class="icon icon-red q4bikon-delete2" style="font-size: 20px"></i>
                            </span>
                        </div>
                    </div>
                <div class="property-structure-apartments apartments-row">
                    <div class="property-structure-list mt-50 pb-15">
                        <ul id="structure-<?=$floor->id?>" class="property-structure-list-items">
                            <?foreach ($floor->places->order_by('ordering',($floor->number < 0) ? 'DESC' :'ASC')->find_all() as $place):?>
                            <li>
                                <div class="apartment-box <?if($place->type == Enum_ProjectPlaceType::PrivateS):?>private-place<?else:?>public-place<?endif?>">
                                    <div class="apartment-box-top">
                                        <span class="apartment-box-top-icon"><i class="q4bikon-<?=$place->type?>"></i></span>
                                        <h5 class="apartment-box-title"><?=__($place->name)?></h5>
                                        <div class="apartment-box-more more cursor-pointer" title="<?=__('More')?>"></div>
                                    </div>
                                    <div class="apartment-box-bottom">
                                        <span class="bottom-box">
                                            <span class="apartment-circle bottom-box-cont location<?=($place->quality_control->count_all() ? ' orange cursor-pointer open-report-modal' : ' blue')?>" <?=$place->quality_control->count_all() ? " title='".__('Report for place')."'" : "''" ?> data-url="<?=URL::site('reports/tasks/'.$item->project_id.'/'.$item->id.'/'.$floor->id.'/'.$place->id)?>" data-toggle="modal" data-target="#property-rooms-clicked-modal">
                                                <?$isp = $place->spaces->count_all()?>
                                                <i class="fs-20 <?=!empty($place->icon) ? $place->icon : 'q4bikon-stairway' ?>" <?if($isp > 1) echo 'style="font-size:20px"'?>></i> <span class="location-number"><?if($isp > 1) echo $isp;?></span>
                                            </span>
                                        </span>
                                        <span class="bottom-box">
                                                <div class="apartment-box-input">
                                                <?$title = !empty($place->custom_number) ? "title='".$place->custom_number."'" : '';?>
                                               <span data-url="<?=URL::site('projects/property_item_quality_control_list/'.$place->id)?>" class="apartment-number <?=($place->quality_control->count_all() ? 'quality-control-list' : '')?>"
                                                    <?=$title?>><?= !empty($place->custom_number) ? mb_substr($place->custom_number,0,5) : ($place->type == 'public'? 'PB' : 'N').$place->number?>
                                               </span>
                                            </div>
                                        </span>
                                        <span class="bottom-box">
                                            <span class="apartment-circle bottom-box-cont <?=($place->quality_control->count_all() ? 'orange' : 'gray')?>  create-quality-control cursor-pointer" data-url="<?=URL::site('projects/quality_control/'.$place->id)?>">
                                                <?=($place->quality_control->count_all() ? '<i class="q4bikon-checked fs-20"></i>' : '<i class="q4bikon-uncheked"></i>')?>
                                            </span>
                                        </span>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="apartment-box-clicked text-left" style="padding: 10px; height: 102px;">
                                        <span class="apartment-box-clicked-close"><i class="q4bikon-close"></i></span>
                                        <div class="apartment-box-click-actions width100" style="height: 100%; text-align: center; display: flex; justify-content: space-around; align-items: center;">
                                            <span class="place-copy more-dropdown-item copy-elem show-tooltip present-modal w32 cursor-pointer icon-orange fs-20"
                                                    style="display: inline-block; margin: 0px 10px;"
                                                    title="<?=__('Copy')?>"
                                                    data-url="<?=URL::site('projects/place_copy/'.$floor->object_id.'/'.$floor->id.'/'.$place->id)?>">
                                                    <i class="q4bikon-copy2"></i><span style="color: #7985A5;"></span>
                                            </span>
                                            <span class="more-dropdown-item report-elem show-tooltip present-modal cursor-pointer properties_container_list_item_value valign-middle fs-20" title="<?=__('Report for place')?>" style="margin: 0px 10px;">
                                                <a class="structure-report open-report-modal" data-url="<?=URL::site('reports/tasks/'.$item->project_id.'/'.$item->id.'/'.$floor->id.'/'.$place->id)?>" data-toggle="modal" data-target="#property-rooms-clicked-modal">
                                                    <i class="q4bikon-reports icon-orange"></i>
                                                </a>
                                            </span>
                                            <span class="place-edit more-dropdown-item edit-elem show-tooltip present-modal cursor-pointer icon-orange fs-20"
                                                  data-url="<?=URL::site('projects/place_update/'.$floor->object_id.'/'.$floor->id.'/'.$place->id)?>"
                                                  title="<?=__('Edit')?>"
                                                  style="margin: 0px 10px;"><i class="q4bikon-edit2"></i>
                                            </span>
                                            <span class="place-delete more-dropdown-item delete-elem show-tooltip present-modal icon-red floor-delete cursor-pointer fs-20"
                                                  data-url="<?=URL::site('projects/place_delete/'.$floor->object_id.'/'.$floor->id.'/'.$place->id)?>"
                                                  title="<?=__('Delete')?>"
                                                  style="display: inline-block; margin: 0px 10px;">
                                                <i class="q4bikon-delete2"></i>
                                            </span>
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