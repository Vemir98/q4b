<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 04.06.2019
 * Time: 13:45
 */
?>
<div class="row task-details" style="box-shadow: 1px 1px 12px 1px #CDD0D7;margin: 0 0 20px; 0">
    <button type="button" class="report-details-back q4-close-modal"><i class="glyphicon glyphicon-share-alt"></i></button>
    <h1 style="color: #1ebae5;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;padding: 18px;border-bottom: 1px solid #ddd;"><?=__('Place List')?></h1>
    <div class="col-lg-12" style="margin-top: 10px;">
        <table class="responsive_table table table-bordered table-hover">
            <thead>
            <tr>
                <th class="td-100"><?=__('Number')?></th>
                <th class="td-100"><?=__('Place')?></th>
                <th class="td-100"><?=__('Action')?></th>
            </tr>
            </thead>
            <tbody>
            <?foreach ($items as $item):?>
                <tr>
                    <td><?=$item->custom_number?></td>
                    <td><?=$item->name?></td>
                    <?if($item->quality_control->where('craft_id','=',$craft->id)->count_all()):?>
                    <td><span data-url="<?=URL::site('reports/tasks/property_item_quality_control_list/'.$item->id.'/'.$craft->id)?>" class="quality-control-list light-blue" style="cursor:pointer;"><?=__('Quality control list')?></span></td>
                    <?else:?>
                        <td><?=__('Quality control list')?></td>
                    <?endif?>
                </tr>
            <?endforeach; ?>
            </tbody>
        </table>
    </div>
    <style>
        .report-details-back {
            width: 50px;
            font-size: 25px;
            background-color: transparent;
            border: 0;
            -moz-transform: scaleX(-1);
            -o-transform: scaleX(-1);
            -webkit-transform: scaleX(-1);
            transform: scaleX(-1);
            filter: FlipH;
            -ms-filter: "FlipH";
            position: absolute;
            top: 20px;
            left: 11px;
            color: #005c87;
        }
        .task-details{
            position: relative;
        }
        .task-details h1{
            padding-left: 70px!important;
        }
        .report-details-back:hover i{
            text-shadow: 0 0 3px rgba(0, 92, 135, 0.2), 0 0 5px rgba(0, 92, 135, 0.61);
        }
    </style>
</div>
