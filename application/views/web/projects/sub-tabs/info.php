<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 25.09.2021
 * Time: 17:55
 */
?>

<section class="content_new_projects">
    <ul>
        <li class="tab-panel">
            <div class="panel_header open">
                <h2><?=__('Project General Information')?></h2>
            </div>

            <div class="panel_content open">
                <form action="" class="q4_form" autocomplete="off" method="post" data-ajax="true" enctype="multipart/form-data">
                    <input type="hidden" value="" name="x-form-secure-tkn"/>
                    <div class="panel_body projects-general-info container-fluid">
                        <div class="row">

                            <div class="col-lg-3">
                                <div class="set-image-block centered q4-file-upload" data-id="projects">
                                    <div class="upload-logo up-box">
                                        <div class="hide-upload">
                                            <input type="file" accept=".jpg,.jpe,.jpeg,.png,.gif,.tif,.tiff" class="upload-user-logo"  name="images" />
                                        </div>
                                        <?if(!$_PROJECT->image_id):?>
                                            <div class="camera-bg camera-default-image">
                                                <img src="/media/img/camera.png" class="camera" alt="camera">
                                            </div>
                                            <img class="hidden preview-user-image show-uploaded-image" alt="preview image">
                                        <?else:?>
                                            <img  class="preview-user-image show-uploaded-image" src="<?=$_PROJECT->main_image->originalFilePath()?>" alt="preview user image">
                                        <?endif?>
                                    </div>
                                    <?if(!Auth::instance()->get_user()->is('project_admin')&&!Auth::instance()->get_user()->is('project_supervisor')&&!Auth::instance()->get_user()->is('project_adviser')&&!Auth::instance()->get_user()->is('project_visitor')):?>
                                        <a href="#" class="form-control light_blue_btn set-image-link trigger-image-upload"><?=__('Load projects images')?></a>
                                        <a href="#" data-url="<?=URL::site('projects/get_images/'.$_PROJECT->id)?>" class="form-control dark_blue_button see-project-images" data-toggle="modal" data-target="#see-project-images-modal"><?=__('See projects images')?></a>
                                    <?endif; ?>
                                </div>
                            </div>

                            <div class="border_left col-lg-9">
                                <div class="row">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <label class="table_label"><?=__('Project name')?></label>
                                        <div class="form-group form_row">
                                            <i class="input_icon q4bikon-project"></i>
                                            <input type="text" name="name" class="q4-form-input symbol q4_required" value="<?=$_PROJECT->name?>"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <label class="table_label"><?=__('Project Status')?></label>
                                        <div class="form-group form_row">

                                            <div class="select-wrapper select_large">
                                                <i class="q4bikon-arrow_bottom"></i>

                                                <select class="q4-select q4-form-input select-icon-pd q4_select" name="status">
                                                    <?foreach(Enum_ProjectStatus::toArray() as $status):?>
                                                        <?if($status == $_PROJECT->status):?>
                                                            <option value="<?=$status?>" selected="selected"><?=ucfirst(__($status))?></option>
                                                        <?else:?>
                                                            <option value="<?=$status?>"><?=ucfirst(__($status))?></option>
                                                        <?endif?>
                                                    <?endforeach?>
                                                </select>
                                            </div>
                                            <i class="input_icon q4bikon-company_status"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <label class="table_label"><?=__('Company')?></label>
                                        <div class="form-group form_row">
                                            <i class="input_icon q4bikon-companies"></i>
                                            <input type="text" class="q4-form-input symbol disabled-input" value="<?=$_PROJECT->company->name?>"/>
                                            <input type="hidden" class="disabled-input" name="company_id" value="<?=$_PROJECT->company->id?>"/>
                                        </div>

                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <label class="table_label"><?=__('Project ID')?></label>
                                        <div class="form-group form_row">
                                            <i class="input_icon q4bikon-company_id"></i>
                                            <input name="project_id" type="text" class="q4-form-input symbol" value="<?=$_PROJECT->project_id?>"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <label class="table_label"><?=__('Address')?></label>
                                        <div class="form-group form_row">
                                            <i class="input_icon q4bikon-address"></i>
                                            <input type="text" class="q4-form-input symbol" name="address" value="<?=$_PROJECT->address?>"/>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <label class="table_label"><?=__('Start Date')?></label>
                                        <div class="input-group form-group date" id="project-start-date" data-provide="datepicker">
                                            <div class="input-group-addon small-input-group">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </div>
                                            <input type="text" name="start_date" class="q4-form-input" data-date-format="DD/MM/YYYY" value="<?=date('d/m/Y',$_PROJECT->start_date)?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <label class="table_label"><?=__('End Date')?></label>
                                        <div class="input-group form-group date" id="project-end-date" data-provide="datepicker">
                                            <div class="input-group-addon small-input-group">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </div>
                                            <input type="text" name="end_date" class="q4-form-input" data-date-format="DD/MM/YYYY" value="<?=date('d/m/Y',$_PROJECT->end_date)?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <label class="table_label"><?=__('Owner')?></label>
                                        <div class="form-group form_row">
                                            <i class="input_icon q4bikon-username"></i>
                                            <input type="text" name="owner" class="q4-form-input symbol" value="<?=$_PROJECT->owner?>"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="table_label"><?=__('Project Description')?></label>
                                        <div class="form-group form_row">
                                            <textarea name="description" class="form-control" rows="4"><?=$_PROJECT->description?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="projects-general-info-numbers">

                                            <div class="numeric-alignment">
                                                <label class="table-inline-label"><?=__('Building(s)')?></label>
                                                <div class="wrap-number inline-pickers">
                                                    <input class="numeric-input disabled-input" type="text" min="0" value="<?=$objectsCount['building']?>" name="building"/>
                                                    <span class="arrows">
                                                        <i class="arrow no-arrow_top"></i>
                                                        <i class="arrow no-arrow_bottom"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="numeric-alignment">
                                                <label class="table-inline-label"><?=__('Parking(s)')?></label>
                                                <div class="wrap-number inline-pickers">
                                                    <input class="numeric-input disabled-input" type="text" min="0" value="<?=$objectsCount['parking']?>" name="parking"/>
                                                    <span class="arrows">
                                                                        <i class="arrow no-arrow_top"></i>
                                                                        <i class="arrow no-arrow_bottom"></i>
                                                                    </span>
                                                </div>
                                            </div>
                                            <div class="numeric-alignment">
                                                <label class="table-inline-label"><?=__('Other')?></label>
                                                <div class="wrap-number inline-pickers">
                                                    <input class="numeric-input disabled-input" type="text" min="0" value="<?=$objectsCount['object']?>" name="object"/>
                                                    <span class="arrows">
                                                        <i class="arrow no-arrow_top"></i>
                                                        <i class="arrow no-arrow_bottom"></i>
                                                    </span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!--.panel-body-->
                    <div class="panel_footer text-align">
                        <div class="row">

                            <div class="col-lg-12 col-sm-12">
                                <?if(!Auth::instance()->get_user()->is('project_admin')&&!Auth::instance()->get_user()->is('project_supervisor')&&!Auth::instance()->get_user()->is('project_adviser')&&!Auth::instance()->get_user()->is('project_visitor')):?>
                                    <a href="#" class="inline_block_btn dark_blue_button mr_30"><?=__('Archive')?></a>
                                    <a href="#" class="inline_block_btn orange_button q4_form_submit"><?=__('Update')?></a>
                                <?endif;?>
                            </div>
                        </div>
                    </div>
                </form>
            </div><!--panel_content-->
        </li>
    </ul>
</section>