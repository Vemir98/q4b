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
                <div class="add-new-row-double">

                    <div class="add-new-row-double">

                        <div class="add-new-right">
                            <span ><?=__('Add new user')?></span>
                            <a class="orange_plus_small add-profession"  id="new_profession"><i class="plus q4bikon-plus"></i></a>
                        </div>
                        <div class="q4-inside-filter">
                            <div class="filter-status-text">Filter by status:</div>
                            <ul class="inside-filters-list">
                                <li>
                                    <a href="#" class="inside-filter-button-lg active">
                                        <span class="filter-button-text">All <span class="filter-button-numb">(2488)</span></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="inside-filter-button-lg">
                                        <span class="filter-button-text">Base <span class="filter-button-numb">(8)</span></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="inside-filter-button-lg">
                                        <span class="filter-button-text">Custom <span class="filter-button-numb">(80)</span></span>
                                    </a>
                                </li>
                            </ul>
                        </div><!--.q4-page-filter-->
                    </div>

                </div>

                <table class="new_company_table responsive_table table" data-toggle="table">
                    <thead>
                    <tr>
                        <th class="td-cell-180" data-field="Name" data-sortable="true"><?=__('Name')?></th>
                        <th class="td-cell-180" data-field="Company" data-sortable="true"><?=__('Company')?></th>
                        <th class="td-cell-180" data-field="Profession" data-sortable="true"><?=__('Profession')?></th>
                        <th class="td-cell-180" data-field="Email"  data-sortable="true"><?=__('Email')?></th>
                        <th class="td-250" data-field="User Group"  data-sortable="true"><?=__('User Group')?></th>
                        <th class="td-125" data-field="Actions"  data-sortable="true"></th>
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
    <select style="display: none" class="profession-crafts-data1" id="" multiple="multiple">
        <?foreach($crafts as $craft):?>
            <?if($craft->status != Enum_Status::Enabled) continue;?>
            <option value="<?=$craft->id?>"><?=$craft->name?></option>
        <?endforeach?>
    </select>
    <input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">
</form>
