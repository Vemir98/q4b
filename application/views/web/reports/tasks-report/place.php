<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 19.07.2018
 * Time: 3:09
 */


?>
<div class="row" style="box-shadow: 1px 1px 12px 1px #CDD0D7;margin: 0">
    <div class="col-lg-12" style="padding: 0">

        <table class="responsive_table table table-bordered table-hover" style="margin: 0;">

            <thead>
            <tr>
                <th class="td-25"></th>
                <th><?=__('Craft')?></th>
                <th class="td-125"><?=__('Task Qnty')?></th>
                <th class="td-125"><?=__('Checked')?></th>
            </tr>
            </thead>
            <tbody>
            <? $i = 0;
            foreach ($data as $one):?>
                <?if(!(int)$one['used']) continue?>
                <tr>

                    <td><?=++$i?></td>
                    <td><?=$one['name']?></td>
                    <?if((int)$one['used']):?>
                    <td><a class="get-report-details" href="<?=URL::site('reports/tasks/details/place/'.$place->id.'/'.$one['id'])?>"><?=(int)$one['used']?>/<?=(int)$one['total']?></a></td>
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