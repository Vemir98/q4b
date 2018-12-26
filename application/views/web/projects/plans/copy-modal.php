<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 02.05.2017
 * Time: 3:11
 */
?>

<div id="copy-plans-modal" class="modal copy-plans-modal" data-backdrop="static" data-keyboard="false" role="dialog">
    <div class="modal-dialog modal-dialog-1070">
        <form class="q4_form" action="<?=$action?>" data-ajax="true" method="post">
            <input type="hidden" value="" name="x-form-secure-tkn"/>
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                        <div class="clear"></div>
                    </div>
                    <div class="q4_modal_sub_header">
                        <h3><?=__('Copy plan')?></h3>
                    </div>
                </div>
                <div class="modal-body bb-modal"><br>
                    <div class="row">
                        <div class="col-md-6 rtl-float-right">
                            <div class="form-group">
                                <div class="space-type-scroll">
                                    <div class="professions-list-queue">
                                        <div class="professions-list-queue-title"><h3><?=__('Profession')?></h3></div>
                                        <ul class="professions-list-queue-lines">

                                            <li>
                                                <div class="professions-list-checkbox">
                                                    <label  class="checkbox-wrapper">
                                                        <input type="checkbox" disabled="disabled">
                                                        <span class="checkbox-replace"></span><i class="checkbox-tick q4bikon-tick"></i>
                                                    </label>
                                                </div>
                                                <div class="professions-list-input">
                                                    <input type="text" class="table_input_full disabled-input" value="Name"/>
                                                </div>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-5 col-md-offset-1 rtl-float-right">

                            <div class="form-group">
                                <label class="table_label">Project</label>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <select name="object_id" class="q4-select q4-form-input">
                                        <option value="1">Project 1</option>
                                        <option value="2">Project 2</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="table_label"><?=__('Choose property')?></label>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <select name="object_id" class="q4-select q4-form-input">
                                        <?foreach ($objects as $obj):?>
                                            <option value="<?=$obj->id?>"><?=$obj->type->name .' - '.$obj->name?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="modal-footer text-align">
                    <div class="row">
                        <div class="col-sm-12">
                            <span class="q4-btn-lg white close q4-close-modal" data-dismiss="modal"><?=__('Cancel')?></span>
                            <a href="#" class="inline_block_btn blue-light-button q4_form_submit"><?=__('Copy')?></a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
