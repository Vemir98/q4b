<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 23.03.2017
 * Time: 4:25
 */
?>


<div id="upload-plans-modal" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog">
    <div class="modal-dialog q4_project_modal modal-dialog-740 ">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header q4_modal_header">
                <div class="q4_modal_header-top">
                    <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                    <div class="clear"></div>
                </div>
                <div class="q4_modal_sub_header">
                    <h3><?=__('Upload plan(s)')?></h3>
                </div>
            </div>
            <div class="modal-body bb-modal">
                <form class="q4_form" action="<?=$action?>" data-submit="false" data-ajax="true">
                    <input type="hidden" value="" name="x-form-secure-tkn"/>
                    <div class="plans-modal-dialog-top">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="table_label"><?=__('Property')?></label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select name="object_id" class="q4-select q4-form-input">
                                        <option value=""><?=__('Please select')?></option>
                                        <?foreach ($objects as $object): ?>
                                            <option value="<?=$object->id?>"><?=$object->type->name.'-'.$object->name?></option>

                                        <?endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="table_label"><?=__('Profession')?></label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>

                                    <select name="profession_id" class="q4-select q4-form-input">
                                        <option value=""><?=__('Please select')?></option>
                                        <?foreach ($professions as $profession): ?>
                                            <option value="<?=$profession->id?>"><?=$profession->name?></option>

                                        <?endforeach ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="table_label"><?=__('Project')?></label>
                                <input type="text" class="q4-form-input disabled-input" value="<?=$_PROJECT->name?>">
                                <input name="project_id" type="hidden" class="q4-form-input disabled-input" value="<?=$_PROJECT->id?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="table_label"><?=__('Company')?></label>
                                <input type="text" class="q4-form-input disabled-input" value="<?=$_COMPANY->name?>">
                                <input name="company_id" type="hidden" class="q4-form-input disabled-input" value="<?=$_COMPANY->id?>">
                            </div>
                        </div>
                    </div>
                </form>
                <div class="plans-modal-dialog-bottom">
                    <div class="upload-plans-box">
                        <div class="upload-plans-title">
                            <h3>
                                <span class="q4-plans-list"><?=__('File(s) list')?></span>
                                <span class="q4-plans-count"></span>
                            </h3>
                            <div class="load-files-btn-wrap">
                                <div class="load-files-date hidden">
                                    <?=__('Upload date')?>:
                                    <span class="load-files-date-num"><?=date("d/m/Y")?></span>
                                    <span class="load-files-date-time"><?=date("H:i")?></span>
                                </div>
                                <a href="#" class="q4-btn-lg light-blue-bg load-files-btn load-plan-files"><?=__('Load files')?></a>
                            </div>

                            <div class="hide-upload">
                                <input type="file" class="load-images-input" data-id="<?=uniqid()?>" multiple id="tasks-load-new-images" name="file">
                            </div>
                        </div>
                        <div class="upload-plans-wrapper">
                            <div class="upload-plans-scroll">
                                <ul>

                                </ul>
                            </div>
                            <div class="modal-progress-bg">
                                <div class="modal-progress-bar">
                                    <span class="progress-bar-text"></span>
                                    <div class="modal-progress">
                                        <div class="modal-bar"></div>
                                    </div>
                                    <span class="progress-bar-status"><?=__('loading')?></span>
                                </div>
                            </div>
                            <span class="empty-list" style="display: inline-block; height: 25px;width: 200px;text-align: center;line-height: 25px;position: absolute;top: 0;left: 0;right: 0;bottom: 0;margin: auto;color: #1ebae5;font-size: 18px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;">
                                <?=__('Empty list')?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer text-center">
                <div class="row">
                    <div class="col-sm-12">
                        <a href="#" class="q4-btn-lg dark-blue-bg mr_30 cancel-upload-files" data-dismiss="modal"><?=__('Cancel')?></a>
                        <a href="#" class="q4-btn-lg orange upload-plans disabled-gray-button"><?=__('Upload')?></a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>












