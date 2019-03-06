<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 02.04.2017
 * Time: 15:28
 */
?>
<!-- users-list-modal -->
<div id="users-list-modal" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog">
    <form class="form-horizontal q4_form" action="<?=$action?>" data-ajax="true" method="post">
    <input type="hidden" value="" name="x-form-secure-tkn"/>
    <div class="modal-dialog q4_project_modal modal-dialog-1170">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header q4_modal_header">
                <div class="q4_modal_header-top">
                    <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                    <div class="clear"></div>
                </div>
                <div class="q4_modal_sub_header">
                    <h3><?=__('List of all users')?></h3>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                            <div class="plans-modal-dialog-top">
                                <div class="list-users-scroll">
                                    <table class="rwd-table responsive-modal-table table" data-toggle="table">
                                        <thead>
                                        <tr>
                                            <th class="td-25"></th>
                                            <th data-field="Username" data-sortable="true"><?=__('Username')?></th>
                                            <th data-field="Professions" data-sortable="true"><?=__('Professions')?></th>
                                            <th data-field="User groups" data-sortable="true"><?=__('Username groups')?></th>
                                            <th data-field="Email" data-sortable="true"><?=__('Email')?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?$currentUser = Auth::instance()->get_user()?>
                                        <?if($currentUser->is('super_admin') && !in_array($currentUser->id,$selected)):?>
                                            <tr>
                                                <td data-th="<?=__('Select')?>">
                                                    <label  class="checkbox-wrapper">
                                                        <input type="checkbox" name="users_<?=$currentUser->id?>_id" value="<?=$currentUser->id?>"  >
                                                         <span class="checkbox-replace"></span><i class="checkbox-tick q4bikon-tick"></i>
                                                    </label>
                                                </td>
                                                <td class="rwd-td0" data-th="<?=__('Username')?>">
                                                    <div class="div-cell"><?=$currentUser->name?></div>
                                                </td>
                                                <td class="rwd-td1" data-th="<?=__('Professions')?>">
                                                    <div class="div-cell"><?=__($currentUser->getProfession()->name)?></div>
                                                </td>
                                                <td class="rwd-td2" data-th="<?=__('Username groups')?>">
                                                    <div class="div-cell"><?=__($currentUser->getRelevantRole()->name)?></div>
                                                </td>
                                                <td class="rwd-td0" data-th="<?=__('Email')?>">
                                                    <div class="div-cell"><?=$currentUser->email?></div>
                                                </td>
                                            </tr>
                                        <?endif?>
                                        <?foreach($items as $item):?>
                                            <? if(in_array($item->id,$selected) ) continue;?>
                                            <tr>
                                                <td data-th="<?=__('Select')?>">
                                                    <label  class="checkbox-wrapper">
                                                        <input type="checkbox" name="users_<?=$item->id?>_id" value="<?=$item->id?>"  >
                                                         <span class="checkbox-replace"></span><i class="checkbox-tick q4bikon-tick"></i>
                                                    </label>
                                                </td>
                                                <td class="rwd-td0" data-th="<?=__('Username')?>">
                                                    <div class="div-cell"><?=$item->name?></div>
                                                </td>
                                                <td class="rwd-td1" data-th="<?=__('Professions')?>">
                                                    <div class="div-cell"><?=__($item->getProfession()->name)?></div>
                                                </td>
                                                <td class="rwd-td2" data-th="<?=__('Username groups')?>">
                                                    <div class="div-cell"><?=__($item->getRelevantRole()->name)?></div>
                                                </td>
                                                <td class="rwd-td0" data-th="<?=__('Email')?>">
                                                    <div class="div-cell"><?=$item->email?></div>
                                                </td>
                                            </tr>
                                        <?endforeach?>

                                        </tbody>
                                    </table>
                                </div>


                            </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-align">
                <div class="row">
                    <div class="col-sm-12">
                        <a href="#" class="inline_block_btn orange_button q4_form_submit"><?=__('Add to list')?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">
    </form>
</div>
<!-- end of users-list-modal -->
