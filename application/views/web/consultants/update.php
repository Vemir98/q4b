<?defined('SYSPATH') OR die('No direct script access.');?>

<div id="consultants-user-detail" data-backdrop="static" data-keyboard="false" class="modal fade in" role="dialog">

    <div class="modal-dialog q4_project_modal modal-dialog-1170">
        <div class="modal-content">
            <form action="<?=URL::site('/consultants/update/'.$user->id)?>" method="post" class="q4_form"  data-ajax="true" enctype="multipart/form-data">
            <div class="modal-header q4_modal_header">
                <div class="q4_modal_header-top">
                    <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                    <div class="clear"></div>
                </div>
                <div class="q4_modal_sub_header">
                    <h3><?=__('User details')?></h3>
                </div>
            </div>
            <div class="modal-body bb-modal">
                	<input type="hidden" value="" name="x-form-secure-tkn"/>
                	<input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">
                    <div class="row">
                        <div class="col-sm-12 col-md-3 rtl-float-right">
                        <!--<div class="set-image-block centered" data-id="projects">
                                <div class="upload-logo up-box">
                                    <div class="hide-upload">
                                        <input type="file" class="upload-user-logo" accept=".jpg,.jpe,.jpeg,.png,.gif,.tif,.tiff" name="logo"/>
                                    </div>
                                    <?if(empty($user->logo)):?>
                                        <div class="camera-bg">
                                            <img src="/media/img/camera.png" class="camera" alt="camera">
                                        </div>
                                        <img class="hidden preview-user-image show-uploaded-image" alt="preview user image">
                                    <?else:?>
                                        <img class="preview-user-image show-uploaded-image" alt="preview image" src="/<?=$user->logo?>">
                                    <?endif?>
                                </div>

                                <a href="#" class="form-control light_blue_btn set-image-link trigger-image-upload"><?=__('Set your photo')?></a>
                            </div> -->
                        </div>
                        <div class="border_left col-sm-12 col-md-9 rtl-float-right">
                            <div class="row">
                                <div class="col-sm-12 col-md-6 rtl-float-right">
                                    <label class="table_label"><?=__('Email')?></label>
                                    <div class="form-group form_row">
                                        <i class="input_icon q4bikon-email"></i>
                                        <input name="email" type="text" class="q4-form-input symbol disabled-input q4_required" value="<?=$user->email?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 rtl-float-right">
                                    <label class="table_label"><?=__('User Group')?></label>
                                    <div class="form-group form_row">
                                        <input type="text" class="q4-form-input disabled-input symbol q4_required" value="<?=__($userRole)?>">
                                        <i class="input_icon q4bikon-user_group"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-6 rtl-float-right">
                                    <label class="table_label"><?=__('Name')?></label>
                                    <div class="form-group form_row">
                                        <i class="input_icon q4bikon-username"></i>
                                        <input type="text" name="name" class="q4-form-input symbol q4_required" value="<?=$user->name?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 rtl-float-right">
                                    <label class="table_label"><?=__('Phone')?></label>
                                    <div class="form-group form_row">
                                        <i class="input_icon q4bikon-phone"></i>
                                        <input type="text" name="phone" class="q4-form-input symbol" value="<?=$user->phone?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label class="table_label"><?=__('New password')?></label>
                                        <div class="form_row">
                                            <input type="password" class="q4-form-input symbol modal_input" value="" name="password" />
                                            <i class="input_icon q4bikon-password"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="table_label"><?=__('confirm password')?></label>
                                        <div class="form_row">
                                            <input type="password" class="q4-form-input symbol modal_input" value="" name="password_confirm"  />
                                            <i class="input_icon q4bikon-password"></i>
                                        </div>
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
		                                                    			<?$selected = in_array($p->id, $selectedProjects) ? "checked": ''?>
			                                                    		<li>
				                                                            <div class="node-item-checkbox-wrapper">
				                                                                <label class="checkbox-wrapper">
				                                                                    <input type="checkbox" name="project_<?=$p->id?>_id" <?=$selected?> class="node-item-checkbox">
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
            <div class="modal-footer text-align">
                <div class="row">
                    <div class="col-sm-12">
                        <!-- <a class="q4-btn-lg dark-blue-bg q4-modal-dismiss mr_30"><?=__('Reset Password')?></a> -->
                        <a href="#" class="q4-btn-lg orange q4_form_submit" ><?=__('Save')?></a>

                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>

