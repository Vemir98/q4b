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
    <h1 style="color: #1ebae5;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;padding: 18px;border-bottom: 1px solid #ddd;"><?=$craft->name?></h1>
    <div class="col-lg-12" style="margin-top: 10px;">
        <table class="responsive_table table table-bordered table-hover">
            <thead>
            <tr>
                <th class="td-100"><?=__('Place')?></th>
                <th class="td-100"><?=__('Task ID')?></th>
                <th><?=__('Task description')?></th>
                <th class="td-125"><?=__('Users')?></th>
            </tr>
            </thead>
            <tbody>
            <?foreach ($items as $item):?>
                <tr>
                    <td  data-th="<?=__('Place')?>"><?=$item['number']?></td>
                    <td  data-th="<?=__('Task ID')?>"><?=$item['taskId']?></td>
                    <td  data-th="<?=__('Task description')?>"><?=nl2br($item['desc'])?></td>
                    <td  data-th="<?=__('Users')?>"><?=implode(', ',$item['users'])?></td>
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
