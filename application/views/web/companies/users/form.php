<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 09.01.2017
 * Time: 14:54
 */
?>
<form class="q4_form users-form" action="<?=$action?>" data-ajax="true" autocomplete="off">
    <input type="hidden" value="" name="x-form-secure-tkn"/>
    <div class="panel_body container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel-options">
                    <span class="inline-options">
                        <a class="orange_plus_small add-user"><i class="plus q4bikon-plus"></i></a>
                        <span class="inline-options-text"><?=__('Add new user')?></span>
                    </span>
                </div>

                <table class="rwd-table new_company_table responsive_table table" data-toggle="table">
                    <thead>
                    <tr>
                        <th data-field="Name" data-sortable="true"><?=__('Name')?></th>
                        <th data-field="Email" data-sortable="true"><?=__('Email')?></th>
                        <th data-field="Professions" data-sortable="true"><?=__('Professions')?></th>
                        <th data-field="User Group" data-sortable="true"><?=__('User Group')?></th>
                        <th data-field="Status" data-sortable="true" class="td-100"><?=__('Status')?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?if(count($items)):?>
                        <?foreach($items as $item):?>
                            <?=View::make($_VIEWPATH.'form-item',
                                [
                                    'item' => $item,
                                    'professions' => $professions,
                                    'roles' => $roles
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
    <select style="display: none" class="user-roles-data">
        <?foreach($roles as $urId => $urName):?>
            <option value="<?=$urId?>"><?=__($urName)?></option>
        <?endforeach?>
    </select>
    <select style="display: none" class="user-professions-data">
        <?foreach($professions as $prof):?>
            <?if($prof->status != Enum_Status::Enabled) continue;?>
            <option value="<?=$prof->id?>"><?=__($prof->name)?></option>
        <?endforeach?>
    </select>
    <input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">
</form>
