<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 11.01.2017
 * Time: 7:32
 */
?>
<!-- Show details Modal -->
<div class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modal_profile_window">
    <div class="modal-dialog q4_project_modal modal_details-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header q4_modal_header">
                <div class="q4_modal_header-top">
                    <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                    <div class="clear"></div>
                </div>
                <div class="q4_modal_sub_header">
                    <h3><?=__('User details')?></h3>
                </div>
            </div>
            <form  class="form-horizontal cc_form user-details-form" action="<?=$action?>" data-ajax="true">
                <input type="hidden" value="" name="x-form-secure-tkn"/>
                <div class="modal-body q4_modal_body">

                    <div class="form_row form-group">
                        <div class="col-md-6">
                            <label class="table_label"><?=__('Name')?></label>
                            <div class="form_row">
                                <input type="text" name="user_<?=$item->id?>_name" class="q4-form-input symbol required" value="<?=$item->name?>"/>
                                <i class="input_icon q4bikon-username"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="table_label"><?=__('Email')?></label>
                            <div class="form_row">
                                <input type="text" name="user_<?=$item->id?>_email" value="<?=$item->email?>" class="q4-form-input symbol required q4_email"/>
                                <i class="input_icon q4bikon-email"></i>
                            </div>
                        </div>
                    </div>
                    <div class="form_row form-group">
                        <div class="col-md-6">
                            <label class="table_label"><?=__('Position')?></label>
                            <div class="form_row">
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select name="user_<?=$item->id?>_profession" class="q4-select q4-form-input select-icon-pd">
                                        <?foreach ($professions as $prof):?>
                                            <?if($prof->status != Enum_Status::Enabled AND $item->getProfession('id') != $prof->id) continue;?>
                                            <option value="<?=$prof->id?>" <?=($item->getProfession('id') == $prof->id) ? 'selected="selected"' : ''?>><?=__($prof->name)?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                                <i class="input_icon q4bikon-position"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="table_label"><?=__('Phone')?></label>
                            <div class="form_row">
                                <input type="text" name="user_<?=$item->id?>_phone" value="<?=$item->phone?>" class="q4-form-input symbol"/>
                                <i class="input_icon q4bikon-phone"></i>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="table_label"><?=__('User Group')?></label>
                            <div class="form_row">
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select name="user_<?=$item->id?>_role" class="q4-select q4-form-input select-icon-pd">
                                        <?foreach($roles as $urId => $urName):?>
                                            <option value="<?=$urId?>" <?=($item->getRelevantRole('id') == $urId) ? 'selected="selected"' : ''?>><?=__($urName)?></option>
                                        <?endforeach?>
                                    </select>
                                </div>
                                <i class="input_icon q4bikon-user_group"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="table_label"><?=__('Status')?></label>
                            <div class="form_row">
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select name="user_<?=$item->id?>_status" class="q4-select q4-form-input select-icon-pd">
                                        <?foreach (Enum_UserStatus::toArray() as $status):?>
                                            <option value="<?=$status?>" <?=($item->status == $status) ? 'selected="selected"' : ''?>><?=__($status)?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                                <i class="input_icon q4bikon-user_status"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-modal-footer modal-footer text-align">
                    <?if($item->status == Enum_UserStatus::Pending):?>
                        <a href="#" class="q4-btn-lg light-blue-bg mr_30 invite-usr" data-url="<?=URL::site('companies/invite_user/'.$companyId.'/'.$item->id)?>"><?=__('Invite')?></a>
                    <?endif?>
                    <?if($item->status == Enum_UserStatus::Active):?>
                        <a href="#" class="q4-btn-lg dark-blue-bg reset-usr-pwd mr_30" data-url="<?=URL::site('companies/reset_user_password/'.$companyId.'/'.$item->id)?>"><?=__('Reset Password')?></a>
                    <?endif?>
                    <a href="#" class="q4-btn-lg orange submit"><?=__('Update')?></a>
                </div>
                <input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">
            </form>
        </div>
    </div>
</div><!-- end Show details Modal -->
