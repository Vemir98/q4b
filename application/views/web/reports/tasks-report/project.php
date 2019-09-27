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
    <div class="col-lg-12" style="margin-top: 10px;">
        <div class="text-left" style="font-size: 17px; padding: 10px 0 10px 5px; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;color: #003a63;font-weight: bold;">
            <span><?=__('public')?></span>
        </div>
        <table class="responsive_table table table-bordered table-hover">

            <thead>
            <tr>
                <th class="td-25"></th>
                <th><?=__('Craft')?></th>
                <th class="td-125"><?=__('Places')?></th>
                <th class="td-125"><?=__('Task Qnty')?></th>
                <th class="td-100"><?=__('Checked')?></th>
            </tr>
            </thead>
            <tbody>
            <? $i = 0;
            foreach ($data['public'] as $one):?>
                <?if(!(int)$one['used']) continue?>
                <tr>
                    <td><?=++$i?></td>
                    <td><?=$one['name']?></td>
                    <td><a class="get-report-places" href="<?=URL::site('reports/tasks/places/project/'.$project->id.'/'.$one['id']."/public")?>"><?=$project->places->where('type','=',Enum_ProjectPlaceType::PublicS)->count_all()?></a></td>
                    <?if((int)$one['used']):?>
                        <td><a class="get-report-details" href="<?=URL::site('reports/tasks/details/project/'.$project->id.'/'.$one['id']."/public")?>"><?=(int)$one['used']?>/<?=(int)$one['total']?></a></td>
                    <?else:?>
                        <td><?=(int)$one['used']?>/<?=(int)$one['total']?></td>
                    <?endif?>
                    <td><?=$one['percent']?>%</td>
                </tr>
            <?endforeach; ?>
            </tbody>
        </table>
        <hr>
        <div class="text-left" style="font-size: 17px; padding: 0 0 10px 5px; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;color: #003a63;font-weight: bold;">
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
            <?$i = 0;
            foreach ($data['private'] as $one):?>
                <?if(!(int)$one['used']) continue?>
                <tr>
                    <td><?=++$i?></td>
                    <td><?=$one['name']?></td>
                    <td><a class="get-report-places" href="<?=URL::site('reports/tasks/places/project/'.$project->id.'/'.$one['id']."/public")?>"><?=$project->places->where('type','=',Enum_ProjectPlaceType::PrivateS)->count_all()?></a></td>
                    <?if((int)$one['used']):?>
                        <td><a class="get-report-details" href="<?=URL::site('reports/tasks/details/project/'.$project->id.'/'.$one['id']."/private")?>"><?=(int)$one['used']?>/<?=(int)$one['total']?></a></td>
                    <?else:?>
                        <td><?=(int)$one['used']?>/<?=(int)$one['total']?></td>
                    <?endif?>
                    <td><?=$one['percent']?>%</td>
                </tr>
            <?endforeach; ?>
            </tbody>
        </table>

    </div>
</div>