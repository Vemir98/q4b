<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 09.01.2017
 * Time: 11:36
 */
?>
<form action="<?=$action?>" data-ajax="true" class="q4_form tasks-form" autocomplete="off">
    <input type="hidden" value="" name="x-form-secure-tkn"/>
    <div class="panel_body container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel-options">
                    <span ><?=__('Add new task')?></span>
                    <a class="orange_plus_small add-task"  id="new_task"><i class="plus q4bikon-plus"></i></a>
                </div>

                <table class="new_company_table settings-tasks-table responsive_table table" data-toggle="table">
                    <thead>
                    <tr>
                        <th data-field="<?=__('Task Name')?>" data-sortable="true"><?=__('Task Name')?></th>
                        <th data-field="<?=__('Crafts')?>" data-sortable="true"><?=__('Crafts')?></th>
                        <th data-field="<?=__('Status')?>" data-sortable="true" class="td-100"><?=__('Status')?></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?if(!empty($items)):?>
                            <?foreach($items as $item):?>
                                <?=View::make($_VIEWPATH.'form-item',
                                    [
                                        'item' => $item,
                                        'items_crafts' => $items_crafts,
                                        'crafts' => $crafts
                                    ])?>
                            <?endforeach?>
                        <?endif?>
                    </tbody>
                </table>

            </div>
        </div>

    </div><!--.panel_body-->
    <div class="panel_footer text-right">
        <div class="row">
            <div class="col-lg-4 col-lg-offset-8 col-sm-12 col-sm-offset-0">
                <a href="#" class="inline_block_btn orange_button q4_form_submit"><?=__('Update')?></a>
            </div>
        </div>
    </div>
    <select style="display: none" class="task-crafts-data" id="" multiple="multiple">

        <?foreach($crafts as $craft):?>
            <?if($craft->status != Enum_Status::Enabled) continue;?>
            <option value="<?=$craft->id?>"><?=$craft->name?></option>
        <?endforeach?>
    </select>
    <input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">
</form>
