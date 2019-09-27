<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 12.03.2017
 * Time: 7:37
 */
?>
<div class="generate-reports-bookmark">
    <div class="generate-reports-bookmark-title">
        <h2><?=__('Generated Reports')?></h2>
    </div>
    <div class="generate-reports-bookmark-arrow" data-url="<?=URL::site('reports/place')?>">
        <i class="q4bikon-arrow_bottom"></i>
    </div><br><br>
</div>
<div class="panel_body container-fluid property-struct">
<div class="row">
    <div class="col-md-12">
        <div class="filter-plans-report closed">
            <div class="stats">
                <ul>
                    <?foreach ($stats as $s):?>
                        <li>
                            <label for="chb-<?=$s['id']?>" class="checked" data-craft="<?=$s['id']?>">
                                <?=$crafts[$s['id']]?> - <?=round($s['percent'],2)?>%
                            </label>
                        </li>
                    <?endforeach;?>
                </ul>
            </div>
            <a href="#" class="show-filter"><?=__('Filter')?></a>
        </div>
        <div class="report-project-desc f0" style="margin-top: 25px;">
            <div class="report-project-desc-image">
                <img src="/<?=$company->logo?>" alt="project images">
            </div>
            <div class="report-project-desc-list">
                <ul style="width: 100%" >
                    <li>
                            <span class="light-blue">
                                <i class="icon q4bikon-companies"></i>
                                <?=__('Company name')?>:
                            </span>
                        <span class="dark-blue">
                                <?=$company->name?>
                            </span>
                    </li>
                    <li>
                            <span class="light-blue">
                                <i class="icon q4bikon-project"></i>
                                <?=__('Project name')?>:
                            </span>
                        <span class="dark-blue">
                                <?=$project->name?>
                            </span>
                    </li>
                    <li>
                            <span class="light-blue">
                                <i class="icon q4bikon-project"></i>
                                <?=__('Object')?>:
                            </span>
                        <span class="dark-blue">
                                <?=$object->name?>
                            </span>
                    </li>
                </ul>
                    <ul style="100%!important">
                        <li></li>
                        <li>
                            <span class="light-blue">
                                <i class="q4bikon-date"></i>
                                <?=__('Crafts')?>:
                            </span>
                            <span class="dark-blue">
                                <?=implode(',',$crafts)?>
                            </span>
                        </li>
                        <li></li>
                    </ul>
                <br>
            </div>
            <div class="clear"></div>
        </div>
        <?foreach ($itemFloors as $floor):?>
        <div class="property-structure-list-group">
            <div class="property-structure-actions inactive">

            </div>

            <div class="property-structure-floors open-report-modal cursor-pointer"  title="<?=__('Click to view report for floor')?>" data-url="<?=URL::site('reports/tasks/'.$item->project_id.'/'.$item->id.'/'.$floor->id)?>">
                <span class="structure-floor-number rotate"><?if($floor->number == 0):?><?=__('Ground Floor')?><?else:?><?=__('Floor')?> <?=$floor->number?><?endif?></span>
            </div>
            <div class="property-structure-apartments">

                <div class="property-structure-list">
                    <ul id="structure-<?=$floor->id?>" class="property-structure-list-items">
                        <?foreach ($floor->places->order_by('ordering',($floor->number < 0) ? 'DESC' :'ASC')->find_all() as $place):?>
                        <li data-crafts="<?=$placeCrafts[$place->id]?>">
                            <div class="apartment-box">
                                <div class="apartment-box-top <?if($place->type == Enum_ProjectPlaceType::PrivateS):?>blue<?else:?>gray<?endif?>">
                                    <span class="apartment-box-top-icon"><i class="q4bikon-<?=$place->type?>"></i></span>
                                    <h5 class="apartment-box-title"><?=__($place->name)?></h5>
                                </div>
                                <div class="apartment-box-bottom">
                                    <span class="bottom-box">
                                        <span class="apartment-circle  location<?=($place->quality_control->count_all() ? ' blue' : ' blue')?>" <?=$place->quality_control->count_all() ? " title='".__('Click to view report for place')."'" : "" ?>" data-url="<?=URL::site('reports/tasks/'.$item->project_id.'/'.$item->id.'/'.$floor->id.'/'.$place->id)?>" data-toggle="modal" data-target="#property-rooms-clicked-modal">
                                            <?$isp = $place->spaces->count_all()?>
                                            <i class="<?=!empty($place->icon) ? $place->icon : 'q4bikon-stairway' ?>" <?if($isp > 1) echo 'style="font-size:20px"'?>></i> <span class="location-number"><?if($isp > 1) echo $isp;?></span>
                                        </span>
                                    </span>
                                    <span class="bottom-box number-box">
                                        <div class="apartment-box-input">
                                            <?$title = !empty($place->custom_number) ? "title='".$place->custom_number."'" : '';?>
                                            <div class="q4-form-input" <?=$title?>><?= !empty($place->custom_number) ? mb_substr($place->custom_number,0,5) : ($place->type == 'public'? 'PB' : 'N').$place->number?></div>
                                        </div>
                                        <span data-url-pattern="<?=URL::site('reports/place/qc_list/'.$place->id.'/')?>/" data-url="<?=URL::site('reports/place/qc_list/'.$place->id.'/'.implode(',',$craftIds))?>" class="apartment-number <?=($place->quality_control->count_all() ? 'quality-control-list' : '')?>">
                                            <?if($place->type == 'public'):?>PB<?else:?>N<?endif?><?=$place->number?>
                                        </span>
                                    </span>
                                    <span class="bottom-box">
                                        <span class="apartment-circle <?=($place->quality_control->count_all() && in_array($place->id,$placeIds) ? 'orange' : 'gray')?>  create-quality-control cursor-pointer" data-url="<?=URL::site('projects/quality_control/'.$place->id)?>">
                                            <?=($place->quality_control->count_all() && in_array($place->id,$placeIds) ? '<i class="q4bikon-checked"></i>' : '<i class="q4bikon-uncheked"></i>')?>
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
<style>
    .filter-plans-report{
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background-color: white;
        border-bottom: 3px solid #e9e9e9;
        z-index: 10;
    }
    .filter-plans-report.closed{
        height: 40px;
    }
    .filter-plans-report.closed .stats{
        display: none;
    }
    .show-filter{
        margin: auto;
        display: block;
        width: 150px;
        height: 30px;
        margin-top: 10px;
        text-decoration: none;
    }
    .closed .show-filter:after{
        content: "\00BB";
        transform: rotate(90deg);
        display: inline-block;
        margin-left: 10px;
        margin-top: 0px;
        position: absolute;
        font-size: 20px;
    }
    .show-filter:after{
        content: "\00BB";
        transform: rotate(270deg);
        display: inline-block;
        margin-left: 10px;
        margin-top: 0px;
        position: absolute;
        font-size: 20px;
    }
    .stats label.excluded{
        text-decoration: line-through;
    }
    .stats label{
        box-sizing:border-box;
        color:rgb(112, 132, 146);
        cursor:pointer;
        display:inline-block;
        font-family:proxima_nova_rgregular, Arial, Helvetica, sans-serif;
        font-size:16px;
        font-stretch:100%;
        font-style:normal;
        font-variant-caps:normal;
        font-variant-east-asian:normal;
        font-variant-ligatures:normal;
        font-variant-numeric:normal;
        font-weight:400;
        height:25px;
        line-height:16px;
        -moz-user-select: none;
        -khtml-user-select: none;
        user-select: none;
    }
    .stats {
        margin: 10px;
        padding-left: 30px;
    }
</style>
<script>
    $(document).ready(function(){
        $('.stats label').click();

        $('.stats label').on('click',function (){
            $(this).toggleClass( "excluded" );
            var enabledCrafts = [];
            $('.stats label').each(function(){
                if(!$(this).hasClass('excluded')){
                    enabledCrafts.push($(this).data('craft'));
                }
            });

            $('.property-structure-list-items > li').each(function(){
                var crafts = $(this).data('crafts');
                var needDisable = true;
                if(typeof crafts == 'number'){
                    needDisable = !inArray(crafts,enabledCrafts)
                }else{
                    crafts = crafts.split(',');
                    for(var i = 0; i < crafts.length; i++){
                        if(inArray(crafts[i]*1,enabledCrafts)){
                            needDisable = false;
                        }
                    }
                }

                if(needDisable){
                    $(this).find('.bottom-box .create-quality-control').removeClass('orange').addClass('gray');
                }else{
                    $(this).find('.bottom-box .create-quality-control').removeClass('gray').addClass('orange');
                }

            });
            $('.quality-control-list').each(function () {
                $(this).attr('data-url',$(this).attr('data-url-pattern') + enabledCrafts.join(','));
            })
        });

        $('.show-filter').on('click',function (e){
            e.preventDefault();
            $('.filter-plans-report').toggleClass("closed");
        });
        $('.generate-reports-bookmark-arrow').on('click',function (e){
            e.preventDefault();
            window.location = $(this).data('url');
        });
    });
    function inArray(needle, haystack) {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(haystack[i] == needle) return true;
        }
        return false;
    }
</script>
<script type="text/javascript" src="/media/js/project.js"></script>