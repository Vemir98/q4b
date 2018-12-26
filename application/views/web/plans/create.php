<?defined('SYSPATH') OR die('No direct script access.');?><?php/** * Created by PhpStorm. * User: SUR0 * Date: 09.03.2017 * Time: 18:48 */?><!--project list--><section class="content_new_projects">    <ul>        <li class="tab_panel">            <div class="panel_header open">                <span class="sign"><i class="panel_header_icon q4bikon-minus"></i></span><h2><?=__('Project General Information')?></h2>            </div>            <div class="panel_content open">                <form action="" class="q4_form" autocomplete="off" method="post" data-ajax="true" enctype="multipart/form-data">                    <input type="hidden" value="" name="x-form-secure-tkn"/>                    <div class="panel_body projects-general-info container-fluid">                        <div class="row">                            <div class="col-lg-3 rtl-float-right">                                <div class="set-image-block centered q4-file-upload" data-id="projects">                                    <div class="upload-logo up-box">                                        <div class="hide-upload">                                            <input type="file" accept=".jpg,.jpe,.jpeg,.png,.gif,.tif,.tiff" name="images[]" multiple="multiple" class="upload-user-logo" />                                        </div>                                        <div class="camera-bg camera-default-image">                                            <img src="/media/img/camera.png" class="camera" alt="camera">                                        </div>                                        <img class="hidden preview-user-image show-uploaded-image" alt="preview user image">                                    </div>                                    <a href="#" class="form-control light_blue_btn set-image-link trigger-image-upload"><?=__('Load projects images')?></a>                                </div>                            </div>                            <div class="border_left col-lg-9 rtl-float-right">                                <div class="row">                                    <div class="col-lg-6 col-md-12 col-sm-12 rtl-float-right">                                        <label class="table_label"><?=__('Project name')?></label>                                        <div class="form-group form_row">                                            <i class="input_icon q4bikon-project"></i>                                            <input type="text" name="name" class="q4-form-input symbol q4_required" value=""/>                                        </div>                                    </div>                                    <div class="col-lg-6 col-md-12 col-sm-12 rtl-float-right">                                        <label class="table_label"><?=__('Project Status')?></label>                                        <div class="form-group form_row">                                             <div class="select-wrapper select_large">                                                <i class="q4bikon-arrow_bottom"></i>                                                <select class="q4-select q4-form-input select-icon-pd ">                                                        <option value="<?=Enum_ProjectStatus::Active?>"><?=ucfirst(__(Enum_ProjectStatus::Active))?></option>                                                </select>                                            </div>                                            <i class="input_icon q4bikon-company_status"></i>                                        </div>                                    </div>                                </div>                                <div class="row">                                    <div class="col-lg-6 col-md-12 col-sm-12 rtl-float-right">                                        <label class="table_label"><?=__('Company')?></label>                                        <div class="form-group form_row">                                             <div class="select-wrapper select_large">                                                <i class="q4bikon-arrow_bottom"></i>                                                <select class="q4-select q4-form-input select-icon-pd" name="company_id">                                                    <?foreach ($companies as $company):?>                                                        <option value="<?=$company->id?>"><?=$company->name?></option>                                                    <?endforeach?>                                                </select>                                            </div>                                            <i class="input_icon q4bikon-companies"></i>                                        </div>                                    </div>                                    <div class="col-lg-6 col-md-12 col-sm-12 rtl-float-right">                                        <label class="table_label"><?=__('Project ID')?></label>                                        <div class="form-group form_row">                                            <i class="input_icon q4bikon-company_id"></i>                                            <input name="project_id" type="text" class="q4-form-input symbol" value=""/>                                        </div>                                    </div>                                </div>                                <div class="row">                                    <div class="col-lg-6 col-md-12 col-sm-12 rtl-float-right">                                        <label class="table_label"><?=__('Address')?></label>                                        <div class="form-group form_row">                                            <i class="input_icon q4bikon-address"></i>                                            <input type="text" class="q4-form-input symbol" name="address" value=""/>                                        </div>                                    </div>                                    <div class="col-lg-3 col-md-6 col-sm-6 rtl-float-right">                                        <label class="table_label"><?=__('Start Date')?></label>                                        <div class="input-group form-group date" data-provide="datepicker">                                            <div class="input-group-addon small-input-group">                                                <span class="glyphicon glyphicon-calendar"></span>                                            </div>                                            <input type="text" name="start_date" class="q4-form-input" data-date-format="DD/MM/YYYY">                                        </div>                                    </div>                                    <div class="col-lg-3 col-md-6 col-sm-6 rtl-float-right">                                        <label class="table_label"><?=__('End Date')?></label>                                        <div class="input-group form-group date" data-provide="datepicker">                                            <div class="input-group-addon small-input-group">                                                <span class="glyphicon glyphicon-calendar"></span>                                            </div>                                            <input type="text" name="end_date" class="q4-form-input" data-date-format="DD/MM/YYYY">                                        </div>                                    </div>                                </div>                                <div class="row">                                    <div class="col-lg-6 col-md-12 col-sm-12 rtl-float-right">                                        <label class="table_label"><?=__('Owner')?></label>                                        <div class="form-group form_row">                                            <i class="input_icon q4bikon-username"></i>                                            <input type="text" name="owner" class="q4-form-input symbol" value=""/>                                        </div>                                    </div>                                </div>                                <div class="row">                                    <div class="col-md-6 rtl-float-right">                                        <label class="table_label"><?=__('Project Description')?></label>                                        <div class="form-group form_row">                                            <textarea name="description" class="form-control" rows="4"></textarea>                                        </div>                                    </div>                                    <div class="col-md-6 rtl-float-right">                                        <div class="projects-general-info-numbers">                                            <div class="numeric-alignment">                                                <label class="table-inline-label"><?=__('Building(s)')?></label>                                                <div class="wrap-number inline-pickers">                                                    <input class="numeric-input" type="text" min="0" value="1" name="building"/>                                                    <span class="arrows">                                                        <i class="arrow no-arrow_top"></i>                                                        <i class="arrow no-arrow_bottom"></i>                                                    </span>                                                </div>                                            </div>                                            <div class="numeric-alignment">                                                <label class="table-inline-label"><?=__('Parking(s)')?></label>                                                <div class="wrap-number inline-pickers">                                                    <input class="numeric-input" type="text" min="0" value="1" name="parking"/>                                                    <span class="arrows">                                                        <i class="arrow no-arrow_top"></i>                                                        <i class="arrow no-arrow_bottom"></i>                                                    </span>                                                </div>                                            </div>                                            <div class="numeric-alignment">                                                <label class="table-inline-label"><?=__('Other')?></label>                                                <div class="wrap-number inline-pickers">                                                    <input class="numeric-input" type="text" min="0" value="1" name="object"/>                                                        <span class="arrows">                                                            <i class="arrow no-arrow_top"></i>                                                            <i class="arrow no-arrow_bottom"></i>                                                        </span>                                                </div>                                            </div>                                        </div>                                    </div>                                </div>                            </div>                        </div>                    </div><!--.panel-body-->                    <div class="panel_footer text-align">                        <div class="row">                            <div class="col-lg-12 col-sm-12">                                <a href="#" class="inline_block_btn orange_button q4_form_submit"><?=__('Create')?></a>                            </div>                        </div>                    </div>                </form>            </div><!--panel_content-->        </li>        <?if(!Auth::instance()->get_user()->is('project_supervisor')):?>            <li class="tab_panel disabled">                <div class="panel_header">                    <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Users')?></h2>                </div>                <div class="panel_content">                </div>            </li>            <li class="tab_panel disabled">                <div class="panel_header">                    <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Tasks')?></h2>                </div>                <div class="panel_content">                </div>            </li>        <?endif;?>        <li class="tab_panel disabled">            <div class="panel_header">                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span>                <h2><?=__('Property Group')?></h2>            </div>            <div class="panel_content">            </div>        </li>        <li class="tab_panel disabled">            <div class="panel_header">                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Plans')?></h2>            </div>            <div class="panel_content">            </div><!--panel_content-->        </li>        <li class="tab_panel disabled">            <div class="panel_header">                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Certifications')?></h2>            </div>            <div class="panel_content">            </div>        </li>        <?if(!Auth::instance()->get_user()->is('project_supervisor')):?>            <li class="tab_panel disabled">                <div class="panel_header">                    <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Links to other systems')?></h2>                </div>                <div class="panel_content">                </div><!--.panel_content-->            </li>        <?endif;?>    </ul></section>