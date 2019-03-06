<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 12.03.2017
 * Time: 1:09
 */
?>
<form action="<?=$action?>" data-ajax="true" class="q4_form project-users-form" autocomplete="off">
    <input type="hidden" value="" name="x-form-secure-tkn"/>
    <div class="panel_body container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class=" panel-options">
                    <div class="inline-options wrap_delete_users hide">
                        <span class="delete_user q4_form_submit"><i class="q4bikon-delete"></i></span>
                        <span class="inline-options-text"><?=__('Delete users')?></span>
                    </div>

                    <span class="inline-options">
                        <a class="orange_plus_small add-new-project-user" data-url="<?=URL::site('projects/assign_users/'.$_PROJECT->id)?>"><i class="plus q4bikon-plus"></i></a>
                        <span class="inline-options-text"><?=__('Add new user')?></span>
                    </span>
                </div>

                <table class="rwd-table responsive_table table" data-toggle="table">
                    <thead>
                    <tr>
                        <th class="w-25"></th>
                        <th data-field="Username" data-sortable="true"><?=__('Username')?></th>
                        <th data-field="Professions" data-sortable="true"><?=__('Professions')?></th>
                        <th data-field="User Group" data-sortable="true"><?=__('User Group')?></th><th data-field="Email" data-sortable="true"><?=__('Email')?></th>
                        <th data-field="Get Messages By Email"><?=__('Get Messages')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?$currentUser = Auth::instance()->get_user()?>
                    <?foreach($items as $item):?>
                        <?if($item->is('super_admin') && !$currentUser->is('super_admin'))
                        continue;?>

                        <tr>
                            <td class="rwd-td0 select-user-action" data-th="<?=__('select')?>">
                                <label class="checkbox-wrapper ">
                                    <input type="checkbox" name="users_<?=$item->id?>_id"  >
                                     <span class="checkbox-replace"></span><i class="checkbox-tick q4bikon-tick"></i>
                                </label>
                            </td>
                            <td class="rwd-td1" data-th="<?=__('Username')?>">
                                <input type="text" class="table_input disabled-input q4_required" value="<?=$item->name?>">
                            </td>
                            <td class="rwd-td2" data-th="<?=__('Professions')?>">
                                <input type="text" class="table_input disabled-input" value="<?=__($item->getProfession()->name)?>">
                            </td>
                            <td class="rwd-td3" data-th="<?=__('User Group')?>">
                                <input type="text" class="table_input disabled-input q4_required" value="<?=__($item->getRelevantRole()->name)?>">
                            </td>
                            <td class="rwd-td3" data-th="<?=__('Email')?>">
                                <input type="text" class="table_input disabled-input q4_required" value="<?=__($item->email)?>">
                            </td>
                            <td class="rwd-td4 hidden_status user-messages-btns" data-url="<?=URL::site('https://q4b.horizondvp.org/projects/toggle_notifications/'.$_PROJECT->id.'/'.$item->id)?>" data-th="<?=__('Get Messages')?>">

                                <?if(Model_User::needNotify($item->id,$_PROJECT->id)):?>

                                    <div class="toggle-container">
                                        <div class="toggle-radio-btn">
                                            <label>
                                                <input type="radio" class="user-radio" name="user_<?=$item->id?>_message" value="<?=Enum_Status::Disabled?>"><span ></span>
                                            </label>
                                        </div>
                                        <div class="toggle-radio-btn">
                                            <label>
                                                <input type="radio" class="user-radio" name="user_<?=$item->id?>_message" value="<?=Enum_Status::Enabled?>"><span class="input-checked"></span>
                                            </label>
                                        </div>
                                    </div>
                                <?else:?>

                                    <div class="toggle-container disabled-radio-btn">
                                        <div class="toggle-radio-btn">
                                            <label>
                                                <input type="radio" class="user-radio" name="user_<?=$item->id?>_message" value="<?=Enum_Status::Disabled?>"><span class="input-checked"></span>
                                            </label>
                                        </div>
                                        <div class="toggle-radio-btn">
                                            <label>
                                                <input type="radio" class="user-radio" name="user_<?=$item->id?>_message" value="<?=Enum_Status::Enabled?>"><span></span>
                                            </label>
                                        </div>
                                    </div>
                                <?endif?>
                            </td>
                        </tr>
                    <?endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">
</form>


