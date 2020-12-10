<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 19.07.2018
 * Time: 3:09
 */


?>
<div class="row" style="box-shadow: 1px 1px 12px 1px #CDD0D7;margin: 0 0 20px; 0">
    <?if(!empty($title)):?>
        <h1 style="color: #1ebae5;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;padding: 18px;border-bottom: 1px solid #ddd;"><?=$title?></h1>
    <?endif?>
    <div class="col-lg-12" style="margin-top: 10px;">
        <div class="text-left" style="font-size: 17px; padding: 0 0 5px 5px; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;color: #003a63;font-weight: bold;">
            <span><?=__('public')?></span>
        </div>
        <table class="responsive_table table table-bordered table-hover">

            <thead>
            <tr>
                <th class="td-25"></th>
                <th><?=__('Craft')?></th>
                <th class="td-125"><?=__('Task Qnty')?></th>
                <th class="td-100"><?=__('Checked')?></th>
            </tr>
            </thead>
            <tbody>
            <? $i = 0;
            foreach ($data['public'] as $one):?>
                <?if(!(int)$one['used']) continue?>
                <tr>
                    <td data-th="#"><?=++$i?></td>
                    <td data-th="<?=__('Craft')?>"><?=$one['name']?></td>
                    <?if((int)$one['used']):?>
                        <td data-th="<?=__('Task Qnty')?>"><a class="get-report-details" href="<?=URL::site('reports/tasks/details/object/'.$object->id.'/'.$one['id']."/public")?>"><?=(int)$one['used']?>/<?=(int)$one['total']?></a></td>
                    <?else:?>
                        <td data-th="<?=__('Task Qnty')?>"><?=(int)$one['used']?>/<?=(int)$one['total']?></td>
                    <?endif?>
                    <td data-th="<?=__('Checked')?>"><?=$one['percent']?>%</td>
                </tr>
            <?endforeach; ?>
            </tbody>
        </table>
        <br>
        <hr>
        <div class="text-left" style="font-size: 17px; padding: 0 0 5px 5px; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;color: #003a63;font-weight: bold;">
            <span><?=__('private')?></span>
        </div>
        <table class="responsive_table table table-bordered table-hover">

            <thead>

            <tr>
                <th class="td-25"></th>
                <th><?=__('Craft')?></th>
                <th class="td-125"><?=__('Quantity')?></th>
                <th class="td-100"><?=__('Checked')?></th>
            </tr>
            </thead>
            <tbody>
            <? $i = 0;
            foreach ($data['private'] as $one):?>
                <?if(!(int)$one['used']) continue?>
                <tr>
                    <td data-th="#"><?=++$i?></td>
                    <td data-th="<?=__('Craft')?>"><?=$one['name']?></td>
                    <?if((int)$one['used']):?>
                        <td data-th="<?=__('Quantity')?>"><a class="get-report-details" href="<?=URL::site('reports/tasks/details/object/'.$object->id.'/'.$one['id']."/private")?>"><?=(int)$one['used']?>/<?=(int)$one['total']?></a></td>
                    <?else:?>
                        <td data-th="<?=__('Quantity')?>"><?=(int)$one['used']?>/<?=(int)$one['total']?></td>
                    <?endif?>
                    <td data-th="<?=__('Checked')?>"><?=$one['percent']?>%</td>
                </tr>
            <?endforeach; ?>
            </tbody>
        </table>

    </div>
</div>