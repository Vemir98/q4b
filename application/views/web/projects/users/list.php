<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 23.03.2017
 * Time: 17:37
 */
?>
<form action="/" class="q4_form" autocomplete="off">
    <div class="panel_body container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class=" panel-options">
                    <div class="wrap_delete_users hide">
                        <span class="inline-options">
                            <span class="delete_user" data-toggle="modal" data-target="#confirmation-modal-delete">
                                <i class="q4bikon-delete"></i>
                            </span>
                            <span class="inline-options-text"><?=__('Delete users')?></span>
                        </span>
                    </div>

                    <span ><?=__('Add new user')?></span>
                    <a class="orange_plus_small" data-toggle="modal" data-target="#users-list-modal"><i class="plus q4bikon-plus"></i></a>
                </div>

                <table class="rwd-table responsive_table table" data-toggle="table">
                    <thead>
                    <tr>
                        <th class="w-25"></th>
                        <th data-field="Username" data-sortable="true"><?=__('Username')?></th>
                        <th data-field="Professions" data-sortable="true"><?=__('Professions')?></th>
                        <th data-field="User Group" data-sortable="true"><?=__('User Group')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="rwd-td0 select-user-action" data-th="<?=__('select')?>">
                            <label class="checkbox-wrapper inline">
                                <input type="checkbox"  >
                                <span class="checkbox-replace"></span><i class="checkbox-tick q4bikon-tick"></i>
                            </label>
                        </td>
                        <td class="rwd-td1" data-th="<?=__('Username')?>">
                            <input type="text" class="table_input disabled-input q4_required" value="John Carter">
                        </td>
                        <td class="rwd-td2" data-th="<?=__('Professions')?>">
                            <input type="text" class="table_input disabled-input q4_required" value="Architect">
                        </td>
                        <td class="rwd-td3" data-th="<?=__('User Group')?>">
                            <input type="text" class="table_input disabled-input q4_required" value="Corporate Admin">
                        </td>
                    </tr>
                    <tr>
                        <td class="rwd-td0 select-user-action" data-th="<?=__('Name')?>">
                            <label  class="checkbox-wrapper inline">
                                <input type="checkbox"  >
                                 <span class="checkbox-replace"></span><i class="checkbox-tick q4bikon-tick"></i>
                            </label>
                        </td>
                        <td class="rwd-td1" data-th="<?=__('Name')?>">
                            <input type="text" class="table_input disabled-input q4_required" value="Karen Smith">
                        </td>
                        <td class="rwd-td2" data-th="<?=__('Professions')?>">
                            <input type="text" class="table_input disabled-input q4_required" value="Painter">
                        </td>
                        <td class="rwd-td3" data-th="<?=__('User Group')?>">
                            <input type="text" class="table_input disabled-input q4_required" value="Proejct Superviser">
                        </td>
                    </tr>
                    <tr>
                        <td class="rwd-td0 select-user-action" data-th="<?=__('Name')?>">
                            <label  class="checkbox-wrapper">
                                <input type="checkbox"  >
                                 <span class="checkbox-replace"></span><i class="checkbox-tick q4bikon-tick"></i>
                            </label>
                        </td>
                        <td class="rwd-td1" data-th="<?=__('Name')?>">
                            <input type="text" class="table_input disabled-input q4_required" value="Jeniffer Medison">
                        </td>
                        <td class="rwd-td2" data-th="<?=__('Professions')?>">
                            <input type="text" class="table_input disabled-input q4_required" value="Mechanic">
                        </td>
                        <td class="rwd-td3" data-th="<?=__('User Group')?>">
                            <input type="text" class="table_input disabled-input q4_required" value="Proejct Admin">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="panel_footer text-align">
        <div class="row">
            <div class="col-lg-12 col-sm-12">
                <a href="#" class="inline_block_btn orange_button q4_form_submit"><?=__('Update')?></a>
            </div>
        </div>
    </div>
</form>
