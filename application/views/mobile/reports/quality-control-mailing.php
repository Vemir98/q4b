<?defined('SYSPATH') OR die('No direct script access.');?>
<!-- choose-sender-modal-->
<div id="choose-sender-modal" class="modal fade no-delete" data-backdrop="static" data-keyboard="false" role="dialog">
    <div class="modal-dialog q4_project_modal choose-sender-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form class="q4_form" data-ajax="true" method="post" >
            <input type="hidden" value="" name="x-form-secure-tkn"/>
            <input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">

                <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <button class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                        <div class="clear"></div>
                    </div>
                    <div class="q4_modal_sub_header">
                        <h3><?=__('Send reports by email')?></h3>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="table-modal-label-h3"><?=__('Choose whom to send')?>
                            </h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="send-form f0">
                                <div class="send-form-left">
                                    <div class="send-form-top add-sender-block f0">
                                        <div class="choose-sender">

                                            <div class="choose-icons-search" >
                                                <i class="q4bikon-arrow_bottom"></i>

                                                    <select id="users-mails" class="q4-select q4-form-input" >
                                                        <option value=""> <?=__('Please type email')?></option>
                                                       <?foreach ($autocompleteMailList as $user):?>
                                                            <option value="<?=$user?>"> <?=$user?> </option>
                                                        <?endforeach;?>
                                                    </select>

                                            </div>

                                        </div>

                                    </div>
                                    <div class="plans-to-send-users send-sender-block">

                                    </div>

                                </div>
                                <div class="send-form-right">
                                    <div class="send-form-top ex-users-list">
                                        <a href="#" class="choose-existing-users" data-toggle="modal" data-target="#qc-list-users-modal">
                                            <?=__('Choose from existing users')?>

                                        </a>
                                    </div>
                                    <div class="plans-to-send-users show-existing-users">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="table_label"><?=__('Message')?></label>
                            <div class="form-group">
                                <textarea name="message" class="modal-details-textarea"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-modal-footer text-align">
                    <div class="row">
                        <div class="col-sm-12">
                            <input data-dismiss="modal" type="submit" class="inline_block_btn blue-light-button send-email q4_form_submit" value="<?=__('Send')?>" >
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end of choose-sender-modal-->




<!-- qc-list-users-modal -->
<div id="qc-list-users-modal" data-backdrop="static" data-keyboard="false" class="modal fade no-delete" role="dialog">
    <div class="modal-dialog q4_project_modal plans-list-of-users-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header q4_modal_header">
                <div class="q4_modal_header-top">
                    <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                    <div class="clear"></div>
                </div>
                <div class="q4_modal_sub_header">
                    <h3><?=__('List of users')?></h3>
                </div>
            </div>
            <form class="q4_form" action="">
                <div class="modal-body users-modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?if(false):?>
                                <div class="plans-list-users-search">
                                    <div class="plans-list-users-search-wrap">
                                        <input type="text" class="search-input" value="">
                                        <button type="submit" class="search-button"></button>
                                    </div>
                                </div>
                            <?endif ?>

                            <div class="q4-carousel-table-wrap">

                                <div class="q4-carousel-table" data-structurecount="<?=count($items)?>">
                                    <?foreach ($items as $user):?>
                                        <div class="item">
                                            <div class="q4-carousel-blue-head reports-prop-title">
                                                <span class="blue-head-title">#<?=$user->id?></span>
                                                <div class="blue-head-option">
                                                    <label class="q4-circle-checkbox">
                                                        <input type="checkbox">
                                                        <i class="circle-checkbox-tick q4bikon-tick"></i>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="q4-carousel-row f0">
                                                <div class="q4-mobile-table-key">
                                                    <?=__('Name')?>
                                                </div>
                                                <div class="q4-mobile-table-value">
                                                    <?=$user->name?>
                                                </div>
                                            </div>
                                            <div class="q4-carousel-row f0" >
                                                <div class="q4-mobile-table-key">
                                                    <?=__('Profession')?>
                                                </div>
                                                <div class="q4-mobile-table-value">
                                                    <?=$user->getProfession('name')?>
                                                </div>
                                            </div>
                                            <div class="q4-carousel-row f0" >
                                                <div class="q4-mobile-table-key">
                                                    <?=__('User Group')?>
                                                </div>
                                                <div class="q4-mobile-table-value">
                                                    <?=__($user->getRelevantRole('name'))?>
                                                </div>
                                            </div>
                                            <div class="q4-carousel-row f0">
                                                <div class="q4-mobile-table-key">
                                                    <?=__('Email')?>
                                                </div>
                                                <div class="q4-mobile-table-value">
                                                    <span class="s-user-email"><?=$user->email?></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?endforeach;?>
                                </div>

                            </div><!--.q4-carousel-table-wrap-->


                        </div>
                    </div>
                </div>

                <div class="panel-modal-footer text-align">
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="#" class="inline_block_btn blue-light-button confirm-selected-users mobile" data-dismiss="modal"><?=__('Confirm')?></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
<!-- end of qc-list-users-modal -->
