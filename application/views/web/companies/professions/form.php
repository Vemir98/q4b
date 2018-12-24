<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 09.01.2017
 * Time: 11:36
 */
?>
<form action="<?=$action?>" data-ajax="true" class="q4_form professions-form" autocomplete="off">
    <input type="hidden" value="" name="x-form-secure-tkn"/>
    <div class="panel_body container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel-options">
                    <a class="call-professions-list-modal crafts-list-modal q4-link-b-blue" data-url="<?=URL::site('/companies/crafts_with_professions/'.$_COMPANY->id)?>">
                        <?=__('Crafts List')?>
                    </a>
                    <span class="inline-options">
                        <span class="inline-options-text"><?=__('Add new profession')?></span>
                        <a class="orange_plus_small add-profession"  id="new_profession"><i class="plus q4bikon-plus"></i></a>
                    </span>

                </div>

                <table class="new_company_table responsive_table table" data-toggle="table">
                    <thead>
                    <tr>
                        <th data-field="Name" data-sortable="true"><?=__('Name')?></th>
                        <th data-field="Crafts" data-sortable="true"><?=__('Crafts')?></th>
                        <th class="td-350" data-field="Catalog Number" data-sortable="true"><?=__('Catalog Number')?></th>
                        <th class="td-100" data-field="Status"  data-sortable="true"><?=__('Status')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?if(!empty($items)):?>
                        <?$i=0;?>
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
    <div class="panel_footer text-align">
        <div class="row">
            <div class=" col-md-12">
                <a href="#" class="inline_block_btn orange_button q4_form_submit"><?=__('Update')?></a>
            </div>
        </div>
    </div>
    <select style="display: none" class="profession-crafts-data" id="" multiple="multiple">
        <?foreach($crafts as $craft):?>
            <?if($craft->status != Enum_Status::Enabled) continue;?>
            <option value="<?=$craft->id?>"><?=__($craft->name)?></option>
        <?endforeach?>
    </select>
    <input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">
</form>
