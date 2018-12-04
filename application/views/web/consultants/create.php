<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 25.09.2017
 * Time: 14:34
 */

?>

<div id="consultants-user-detail" class="modal fade in" role="dialog">
<?//echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($user); echo "</pre>";?>

    <div class="modal-dialog q4_project_modal consultants-user-detail-dialog">
        <form action="<?=URL::site('/consultants/create/')?>" data-submit="false" method="post" class="q4_form"  data-ajax="true" enctype="multipart/form-data">

            <div class="modal-content">
                <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                        <div class="clear"></div>
                    </div>

                    <div class="q4_modal_sub_header">
                        <h3><?=__('Add new user')?></h3>
                    </div>

                </div>
                <div class="modal-body bb-modal">
                	<input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">
                    <input type="hidden" value="" name="x-form-secure-tkn"/>
                    <div class="row">
                     <div class="col-lg-3 col-sm-12 rtl-float-right">
                         <!--<div class="set-image-block centered" data-id="projects">
                                <div class="upload-logo">
                                    <div class="hide-upload">
                                        <input type="file" class="upload-user-logo" accept=".jpg,.jpe,.jpeg,.png,.gif,.tif,.tiff" name="logo"/>
                                    </div>
                                    <div class="camera-bg">
                                        <img src="/media/img/camera.png" class="camera" alt="camera">
                                    </div>
                                    <img class="hidden preview-user-image" alt="preview user image">

                                </div>

                                <a href="#" class="form-control light_blue_btn set-image-link"><?=__('Set your photo')?></a>
                            </div>-->
                        </div>
                     <!--<div class="border_left col-lg-9 rtl-float-right">-->
                        <div class="border_left col-sm-12 col-lg-9 rtl-float-right">
                            <div class="row">
                                <div class="col-sm-12 col-md-6 rtl-float-right">
                                    <label class="table_label"><?=__('Email')?></label>
                                    <div class="form-group form_row">
                                        <i class="input_icon q4bikon-email"></i>
                                        <input name="email" type="text" class="q4-form-input q4_required symbol form_input disabled-input" value="<?=$user->email?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 rtl-float-right">
                                    <label class="table_label"><?=__('User Group')?></label>


                                    <div class="form-group form_row">

                                        <div class="select-wrapper select_large">
                                            <i class="q4bikon-arrow_bottom"></i>
                                            <select name="role_id" class="q4-select q4-form-input select-icon-pd select-q4bikon-pd q4_required">
                                                <?foreach ($roles as $role):?>
                                                    <option value="<?=$role->id?>"><?=__($role->name)?></option>
                                                <?endforeach; ?>
                                            </select>
                                        </div>
                                        <i class="input_icon q4bikon-user_group"></i>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-6 rtl-float-right">
                                    <label class="table_label"><?=__('Name')?></label>
                                    <div class="form-group form_row">
                                        <i class="input_icon q4bikon-username"></i>
                                        <input type="text" name="name" class="q4-form-input q4_required symbol form_input" value=""/>
                                    </div>
                                </div>
                                <div class="col-md-6 rtl-float-right">
                                    <label class="table_label"><?=__('Phone')?></label>
                                    <div class="form-group form_row">
                                        <i class="input_icon q4bikon-phone"></i>
                                        <input type="text" name="phone" class="q4-form-input symbol form_input" value=""/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 form_row">
                                    <div class="consultants-ud-scroll-wrap">
                                        <label class="table_label"><?=__('Member in')?></label>
                                        <span class="select-lists" data-type="select-all"><?=__('select all')?></span>
                                        <div class="consultants-ud-scroll-box">
                                            <ul id="tree" class="checktree-root q4-unordered-list">
                                            	<?foreach ($companies as $comp):?>
                                            		<?if(count($projects[$comp->id])):?>
	                                            		<li class="root-item">
		                                                    <div class="root-item-checkbox-wrapper">
		                                                        <label class="checkbox-wrapper">
		                                                            <input type="checkbox" value="<?=$comp->id?>" class="root-item-checkbox">
		                                                            <span class="checkbox-replace"></span>
		                                                            <i class="checkbox-tick q4bikon-tick"></i>
		                                                        </label>
		                                                    </div>
		                                                    <div class="q4-unordered-list-text">
		                                                        <div class="tree-node">
		                                                            <span class="tree-node-arrow"><i class="q4bikon-arrow_right"></i></span>
                                                                    <span class="tree-node-text-wrap">
		                                                                <span class="tree-node-text"><?=$comp->name?> </span>
                                                                        <span class="tree-node-status bidi-override">
                                                                            (<span class="tree-node-checked">0</span>/<span class="tree-node-all"><?=count($projects[$comp->id])?></span>)
                                                                        </span>
                                                                    </span>
		                                                        </div>
		                                                    </div>
		                                                    <ul class="q4-nested-list">
		                                                    	<?foreach ($projects as $compId => $proj):?>
		                                                    		<?if($comp->id != $compId) continue;?>
		                                                    		<?foreach ($proj as $p):?>
                                                                        <li>
				                                                            <div class="node-item-checkbox-wrapper">
				                                                                <label class="checkbox-wrapper">
				                                                                    <input type="checkbox" name="project_<?=$p->id?>_id" class="node-item-checkbox">
				                                                                    <span class="checkbox-replace"></span>
				                                                                    <i class="checkbox-tick q4bikon-tick"></i>
				                                                                </label>
				                                                            </div>
				                                                            <div class="q4-unordered-list-text">
				                                                                <span><?=$p->name?></span>
				                                                            </div>
				                                                        </li>
																	<?endforeach?>
																<?endforeach?>
		                                                    </ul>
		                                                </li>
		                                                <?endif;?>
											    <?endforeach;?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-modal-footer text-align">
                    <div class="row">
                        <div class="col-sm-12">
                            <a class="q4-btn-lg orange q4_form_submit create-consultant"><?=__('Save')?></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

