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

                    <li><span class="light-blue">
                                <i class="icon q4bikon-not_found"></i>
                                <?=__('Status')?>:
                            </span>
                        <span class="dark-blue">
                                <?=$status?>
                            </span></li>
                </ul>
                <br>
            </div>
            <div class="clear"></div>
        </div>

        <div class="filter-plans-report closed">
            <div class="stats">
                <table class="table">
                    <thead>
                    <tr>
                        <th><?=__('percent')?></th>
                        <th><?=__('speciality')?></th>
                        <th><?=__('average value')?></th>
                        <th>#</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?foreach ($stats as $s):?>
                        <tr class="checked stats-craft-ex" style="cursor: pointer;"  data-craft="<?=$s['id']?>" data-expandable="craftEx-<?=$s['id']?>">
                            <td style=" font-weight: bold"><?=round($s['percent'],1)?>%</td>
                            <td><?=$crafts[$s['id']]?></td>
                            <td><?=round($craftAVG[$s['id']],1)?>%</td>
                            <td class="disable-craft">X</td>
                        </tr>
                        <tr class="expandable hidden craftEx-<?=$s['id']?>">
                            <td colspan="4">
                                <?if(count($objectsData) > 1):?>
                                <table class="table" style="margin-left: 3%; width: 97%;">
                                    <thead>
                                    <tr>
                                        <th><?=__('percent')?></th>
                                        <th><?=__('structure')?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?foreach ($objectsData as $od):?>
                                    <?if($od['object']->id == $item->id) continue;?>
                                    <tr>
                                        <td style="width: 150px;"><?=round($od['stats'][$s['id']]['percent'],2)?>%</td>
                                        <td><?=$od['object']->name?></td>
                                    </tr>
                                    <?endforeach;?>
                                    </tbody>
                                </table>
                                <?else:?>
                                    <p style="text-align: center"><?=__('No data for show')?></p>
                                <?endif?>
                            </td>
                        </tr>
                    <?endforeach;?>
                    </tbody>
                </table>
            </div>
            <a href="#" class="show-filter"><?=__('Crafts List')?></a>
        </div>
        <?foreach ($itemFloors as $floor):?>
        <div class="property-structure-list-group" style="border-left:1px solid #d4e1ea;">
            <div class="property-structure-floors open-report-modal cursor-pointer"  title="<?=__('Click to view report for floor')?>" data-url="<?=URL::site('reports/tasks/'.$item->project_id.'/'.$item->id.'/'.$floor->id)?>">
                <span class="structure-floor-number rotate"><?if($floor->number == 0):?><?=__('Ground Floor')?><?else:?><?=__('Floor')?> <?=$floor->number?><?endif?></span>
            </div>
            <div class="property-structure-apartments" style="margin-left: 0px;">

                <div class="property-structure-list">
                    <ul id="structure-<?=$floor->id?>" class="property-structure-list-items">
                        <?foreach ($floor->places->order_by('ordering',($floor->number < 0) ? 'DESC' :'ASC')->find_all() as $place):?>
                        <li data-crafts="<?=@$placeCrafts[$place->id]?>">
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
                                        <span data-url-pattern="<?=URL::site('reports/place/qc_list/qc_status/'.$qcStatus.'/'.$place->id.'/')?>/" data-url="<?=URL::site('reports/place/qc_list/qc_status/'.$qcStatus.'/'.$place->id.'/'.implode(',',$craftIds))?>" class="apartment-number <?=($place->quality_control->count_all() ? 'quality-control-list' : '')?>">
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
    .checked:hover td{
        background-color: #1ed2fe;
        color: #fff;
    }
    .filter-plans-report{
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
    .rtl .show-filter:after{
        margin-right: 4px;
        margin-top: -2px;
    }
    .show-filter{
        margin: auto;
        display: block;
        width: 250px;
        height: 30px;
        margin-top: 10px;
        text-decoration: none;
        text-align: center;
        font-family:proxima_nova_rgregular, Arial, Helvetica, sans-serif;
        font-size:16px;
        font-stretch:100%;
        font-style:normal;
        font-variant-caps:normal;
        font-variant-east-asian:normal;
        font-variant-ligatures:normal;
        font-variant-numeric:normal;
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
    .stats thead{
        text-transform: capitalize;
    }
    .stats tr.excluded{
        text-decoration: line-through;
    }
    .stats tr{
        box-sizing:border-box;
        color:rgb(112, 132, 146);
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
    .stats .disable-craft{
        cursor:pointer;
    }
    .stats {
        margin: 10px;
        padding-left: 30px;
    }
</style>
<script>
    $(document).ready(function(){
        foo();
        function foo (){
            var enabledCrafts = [];
            $('.stats tr').each(function(){
                if(!$(this).hasClass('excluded') && $(this).data('craft') != undefined){
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
                    $(this).find('.apartment-number').removeClass('quality-control-list');
                }else{
                    $(this).find('.bottom-box .create-quality-control').removeClass('gray').addClass('orange');
                    $(this).find('.apartment-number').addClass('quality-control-list');
                }

            });
            $('.quality-control-list').each(function () {
                $(this).removeData('url');
                $(this).removeAttr('data-url');
                $(this).data('url',$(this).data('url-pattern') + enabledCrafts.join(','));
                $(this).attr('data-url',$(this).data('url-pattern') + enabledCrafts.join(','));
            })
        }
        //$('.stats .disable-craft').click();

        $('.stats .disable-craft').on('click',function (){
            $(this).parents('tr').toggleClass( "excluded" );
            var enabledCrafts = [];
            $('.stats tr').each(function(){
                if(!$(this).hasClass('excluded') && $(this).data('craft') != undefined){
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
                    $(this).find('.apartment-number').removeClass('quality-control-list');
                }else{
                    $(this).find('.bottom-box .create-quality-control').removeClass('gray').addClass('orange');
                    $(this).find('.apartment-number').addClass('quality-control-list');
                }

            });
            $('.quality-control-list').each(function () {
                $(this).removeData('url');
                $(this).removeAttr('data-url');
                $(this).data('url',$(this).data('url-pattern') + enabledCrafts.join(','));
                $(this).attr('data-url',$(this).data('url-pattern') + enabledCrafts.join(','));
            })
        });

        $('.show-filter').on('click',function (e){
            e.preventDefault();
            $('.filter-plans-report').toggleClass("closed");
        });
        $('.generate-reports-bookmark-arrow').on('click',function (e){
            e.preventDefault();
            $('.loader_backdrop').show();
            window.location = $(this).data('url');
        });

        $(window).on('resize', function() {
            var windowWidth = $(this).width();
            var jCarusel = $('.wrap-property-structure-list');
            if (jCarusel.length > 0) {
                $.fn.utilities('setScrollBarWidth', $('.wrap-property-structure-list li'), windowWidth + 30);
            }
        });

        $(window).on('resize', function() {

            var windowWidth = $(this).width(); //this = window

            if ($(document).find('.property-struct').length) {


                var sidebarWidth = $(document).find('.sidebar').is(":visible") ? 295 : 90;
                if (windowWidth > 480) {
                    var tabWidthOnLoad = windowWidth - sidebarWidth - 115;
                }


                $('.property-structure-list').width(tabWidthOnLoad);

            }


            var windowWidthResize = $(window).width();

            if (windowWidthResize > 992) {

                $(document).find('.property-structure-actions .copy-element').tooltip('enable');
                $(document).find('.property-structure-actions .edit-element').tooltip('enable');
                $(document).find('.property-structure-actions .delete_row').tooltip('enable');

                $(document).find('.property-structure-actions .copy-element').tooltip({ title: "Copy Element", placement: "top" });
                $(document).find('.property-structure-actions .edit-element').tooltip({ title: "Edit Element", placement: "top" });
                $(document).find('.property-structure-actions .delete_row').tooltip({ title: "Delete Element", placement: "top" });
            } else {
                $(document).find('.property-structure-actions .copy-element').tooltip('disable');
                $(document).find('.property-structure-actions .edit-element').tooltip('disable');
                $(document).find('.property-structure-actions .delete_row').tooltip('disable');
            }
        });


        normalizeObjectStruct();


        var allFloors = $(document).find('.wrap-property-structure-list').data('floor');
        var middleFloor = parseInt(allFloors/2);

        // Switch floors wrap-property-structure-list /
        $('.wrap-property-structure-list').jCarouselLite({
            btnNext: ".next",
            btnPrev: ".prev",
            vertical: true,
            visible: 1,
            circular:false,
            start: middleFloor,
        });
        $('.wrap-property-structure-list').css('overflow', 'hidden');

        if(allFloors < 2){

            $('.wrap-property-structure-list')
                .closest('.property-struct')
                .find('.property-floors-arrow')
                .addClass('disabled');
        }

        $.fn.utilities('setCarouselDirection', ".q4-owl-carousel", 0);
        $.fn.utilities('owlPagination', '.q4-owl-carousel');
    });
    function inArray(needle, haystack) {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(haystack[i] == needle) return true;
        }
        return false;
    }
    function normalizeObjectStruct() {
        /***************************************
         *   scroll apartments Structure (Property tab)
         ***************************************/
        var scrollWidth = $(document).find('.property-structure-list').width();
        var innerWidth = 0;
        $('[data-toggle="table"]').bootstrapTable();
        var structureId = 0;
        $(document).find('[id^="structure-"]').each(function(i, el) {
            structureId = $(this).attr('id');
            $(document).find('#' + structureId + ' li').each(function(i, el) {
                var self = $(el);
                innerWidth += self.width() + 30;
                $(this).parent().width(innerWidth);
            });
            innerWidth = 0;
        });
        $(document).find('.property-structure-list').width(scrollWidth);
        var windowWidth = $(window).width();
        if (windowWidth > 992) {
            $(document).find('.property-structure-actions .copy-element').tooltip({
                title: "Copy Element",
                placement: "top"
            });
            $(document).find('.property-structure-actions .edit-element').tooltip({
                title: "Edit Element",
                placement: "top"
            });
            $(document).find('.property-structure-actions .delete_row').tooltip({
                title: "Delete Element",
                placement: "top"
            });
        }

        setTimeout(function(){
            $('.property-structure-list').css('width','auto');
        },1200);
    }
</script>
<script type="text/javascript" src="/media/js/project.js"></script>