<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 11.03.2017
 * Time: 16:33
 */
?>

<!--project list-->
<section class="content_new_projects">
    <ul>
        <li class="tab_panel">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Project General Information')?></h2>
            </div>

            <div class="panel_content">
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
        <?if(!Auth::instance()->get_user()->is('project_adviser')):?>
            <?if(!Auth::instance()->get_user()->is('project_supervisor')):?>
                <li class="tab_panel">
                    <div class="panel_header">
                        <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Users')?></h2>
                    </div>
                    <div class="panel_content">
                        <?=View::make($_VIEWPATH.'users/form',
                            ['action' => URL::site('projects/remove_users/'.$_PROJECT->id),
                                'items' => $users,
                                'secure_tkn' => AesCtr::encrypt($_PROJECT->id.Text::random('alpha'),$_PROJECT->id,192)
                            ])?>
                    </div>
                </li>
                <?if(Usr::can(Usr::TASKS_PERM)):?>
                <li class="tab_panel">
                    <div class="panel_header">
                        <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Tasks')?></h2>
                    </div>
                    <div class="panel_content">
                        <?=View::make($_VIEWPATH.'tasks/form',
                            ['action' => URL::site('projects/update_tasks/'.$_PROJECT->company_id.'/'.$_PROJECT->id),
                                'items' => $tasks,
                                'secure_tkn' => AesCtr::encrypt($_PROJECT->id.Text::random('alpha'),$_PROJECT->id,192)
                            ])?>
                    </div>
                </li>
                <?endif;?>
            <?endif;?>
            <li class="tab_panel">
                <div class="panel_header open">
                    <span class="sign"><i class="panel_header_icon q4bikon-minus"></i></span>
                    <h2><?=__('Property Group')?></h2>
                </div>
                <div class="panel_content property-tab-content open">
                    <?=View::make($_VIEWPATH.'property/form',
                        ['action' => URL::site('projects/update_properties/'.$_PROJECT->id),
                            'items' => $_PROJECT->objects->order_by('id','DESC')->find_all(),
                            'itemTypes' => ORM::factory('PrObjectType')->find_all(),
                            'secure_tkn' => AesCtr::encrypt($_PROJECT->id.Text::random('alpha'),$_PROJECT->id,192)
                        ])?>
                </div>
            </li>
        <?endif;?>
        <?if(!Auth::instance()->get_user()->is('project_adviser')):?>
            <li class="tab_panel">
                <div class="panel_header">
                    <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Old')?> <?=__('Certifications')?></h2>
                </div>
                <div class="panel_content">
                    <?=View::make($_VIEWPATH.'certifications/form',
                        [   'action' => URL::site('projects/update_certifications/'.$_PROJECT->id),
                            'crafts' => $_PROJECT->company->crafts->where('status', '=', Enum_Status::Enabled)->find_all(),
                            'certs' => $_PROJECT->certifications->where('craft_id','IS',DB::expr('NULL'))->find_all(),
                            'secure_tkn' => AesCtr::encrypt($_PROJECT->id.Text::random('alpha'),$_PROJECT->id,192)
                        ])?>
                </div>
            </li>
        <?endif;?>
        <?if(!Auth::instance()->get_user()->is('project_supervisor') && !Auth::instance()->get_user()->is('project_adviser')):?>
            <li class="tab_panel">
                <div class="panel_header">
                    <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Links to other systems')?></h2>
                </div>
                <div class="panel_content">
                    <?=View::make($_VIEWPATH.'links/form',
                        ['action' => URL::site('projects/update_links/'.$_PROJECT->id),
                            'items' => $_PROJECT->links->order_by('id','DESC')->find_all(),
                            'secure_tkn' => AesCtr::encrypt($_PROJECT->id.Text::random('alpha'),$_PROJECT->id,192)
                        ])?>
                </div>
            </li>
        <?endif;?>
    </ul>
</section>
<?if(Usr::role() == 'super_admin'):?>
    <section id="acceptance" class="new-styles">
        <ul>
            <li class="tab_panel<?=$tabsDisabled ?' disabled':null?>">
                <div class="panel_header">
                    <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Delivery form')?></h2>
                </div>
                <div class="panel_content">
                    <div class="panel_body container-fluid">
                        <tabs>
                            <tab name="<?=__('Reserve Materials')?>" :selected="true">
                                <reserve-materials
                                        th-name1="<?=__('Actions')?>"
                                        th-name2="<?=__('Item Text')?>"
                                        th-name3="<?=__('Quantity')?>"
                                        th-name4="<?=__('Size')?>"
                                        select-all-txt="<?=__('Select All')?>"
                                        unselect-all-txt="<?=__('Deselect All')?>"
                                        add-text="<?=__('New value')?>"
                                        save-text="<?=__('Save')?>"
                                        no-items-text="<?=__('No items to show')?>"
                                        copy-text="<?=__('Copy to')?>"
                                        copy-btn-txt="<?=__('Copy')?>"
                                        select-company-txt="<?=__('Select Company')?>"
                                        choose-projects-txt="<?=__('Select project(s)')?>"
                                        items-list-txt="<?=__('Item Text')?>"
                                        more-txt="<?=__('More')?>"
                                        edit-txt="<?=__('Edit')?>"
                                        delete-txt="<?=__('Delete')?>"
                                        projects-url="<?=URL::site('/acceptance/get_projects')?>"
                                        companies-url="<?=URL::site('/acceptance/get_companies')?>"
                                        delete-url="<?=URL::site('/acceptance/delete_rm')?>"
                                        copy-url="<?=URL::site('/acceptance/copy_to_project')?>"
                                        update-url="<?=URL::site('/acceptance/update_rms_list')?>"
                                        list-url="<?=URL::site('/acceptance/get_rms_list/'.$_PROJECT->id)?>"
                                        project-id="<?=$_PROJECT->id?>"
                                />
                            </tab>
                            <tab name="<?=__('Transferable items')?>">
                                <transferable-items
                                        th-name1="<?=__('Actions')?>"
                                        th-name2="<?=__('Item Text')?>"
                                        th-name3="<?=__('Quantity')?>"
                                        select-all-txt="<?=__('Select All')?>"
                                        unselect-all-txt="<?=__('Deselect All')?>"
                                        add-text="<?=__('New value')?>"
                                        save-text="<?=__('Save')?>"
                                        no-items-text="<?=__('No items to show')?>"
                                        copy-text="<?=__('Copy to')?>"
                                        copy-btn-txt="<?=__('Copy')?>"
                                        select-company-txt="<?=__('Select Company')?>"
                                        choose-projects-txt="<?=__('Select project(s)')?>"
                                        items-list-txt="<?=__('Item Text')?>"
                                        more-txt="<?=__('More')?>"
                                        edit-txt="<?=__('Edit')?>"
                                        delete-txt="<?=__('Delete')?>"
                                        projects-url="<?=URL::site('/acceptance/get_projects')?>"
                                        companies-url="<?=URL::site('/acceptance/get_companies')?>"
                                        delete-url="<?=URL::site('/acceptance/delete_ti')?>"
                                        copy-url="<?=URL::site('/acceptance/copy_to_project')?>"
                                        update-url="<?=URL::site('/acceptance/update_ti_list')?>"
                                        list-url="<?=URL::site('/acceptance/get_ti_list/'.$_PROJECT->id)?>"
                                        project-id="<?=$_PROJECT->id?>"
                                />
                            </tab>
                            <tab name="<?=__('Texts')?>">
                                <texts
                                        th-name1="<?=__('Actions')?>"
                                        th-name2="<?=__('Item Text')?>"
                                        th-type="<?=__('Text type')?>"
                                        select-all-txt="<?=__('Select All')?>"
                                        unselect-all-txt="<?=__('Deselect All')?>"
                                        add-text="<?=__('New value')?>"
                                        save-text="<?=__('Save')?>"
                                        no-items-text="<?=__('No items to show')?>"
                                        copy-text="<?=__('Copy to')?>"
                                        copy-btn-txt="<?=__('Copy')?>"
                                        select-company-txt="<?=__('Select Company')?>"
                                        choose-projects-txt="<?=__('Select project(s)')?>"
                                        type-txt="<?=__('Text type')?>"
                                        items-list-txt="<?=__('Item Text')?>"
                                        more-txt="<?=__('More')?>"
                                        edit-txt="<?=__('Edit')?>"
                                        delete-txt="<?=__('Delete')?>"
                                        projects-url="<?=URL::site('/acceptance/get_projects')?>"
                                        companies-url="<?=URL::site('/acceptance/get_companies')?>"
                                        delete-url="<?=URL::site('/acceptance/delete_te')?>"
                                        copy-url="<?=URL::site('/acceptance/copy_to_project')?>"
                                        update-url="<?=URL::site('/acceptance/update_te_list')?>"
                                        list-url="<?=URL::site('/acceptance/get_te_list/'.$_PROJECT->id)?>"
                                        project-id="<?=$_PROJECT->id?>"
                                />
                            </tab>
                        </tabs>
                    </div>

                </div>
            </li>
        </ul>
    </section>
    <script>

        var app = new Vue({
            el: '#acceptance',
        })
    </script>
<?endif?>
<section id="certifications" class="new-styles">
    <ul>
        <li class="tab_panel">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Certifications')?></h2>
            </div>
            <div class="panel_content">
                <div class="panel_body container-fluid">
                    <universal-certification
                            items-url="<?=URL::site('/entities/certifications/'. $_PROJECT->company->id.'/'.$_PROJECT->id)?>"
                            projects-url="<?=URL::site('/entities/projects')?>"
                            delete-url="<?=URL::site('/certifications/delete_regulation')?>"
                            save-url="<?=URL::site('/certifications/save_certifications/'.$_PROJECT->id)?>"
                            company-id="<?=$_PROJECT->company->id?>"
                            project-id="<?=$_PROJECT->id?>"
                            companies-url="<?=URL::site('/entities/companies?fields=id,name')?>"
                            copy-url="<?=URL::site('/certifications/copy_certifications')?>"
                            select-all-txt="<?=__('select all')?>"
                            save-txt="<?=__('Save')?>"
                            desc-txt="<?=__('Description1')?>"
                            file-txt="<?=__('File')?>"
                            upload-date-txt="<?=__('Upload date')?>"
                            status-txt="<?=__('Status')?>"
                            more-txt="<?=__('More')?>"
                            delete-txt="<?=__('Delete')?>"
                            copy-txt="<?=__('Copy to')?>"
                            copy-btn-txt="<?=__('Copy')?>"
                            include-files-txt="<?=__('Include attached files')?>"
                            select-company-txt="<?=__('Select Company')?>"
                            select-project-txt="<?=__('Select project')?>"
                            v-bind:status-options="[{val: '<?=Enum_CertificationsApprovalStatus::Waiting?>',label: '<?=__(Enum_CertificationsApprovalStatus::Waiting)?>'},{val: '<?=Enum_CertificationsApprovalStatus::Approved?>',label: '<?=__(Enum_CertificationsApprovalStatus::Approved)?>'}]"

                    />
                </div>

            </div>
        </li>
    </ul>
</section>
<script>
    var certification = new Vue({
        el: '#certifications',
    })
</script>
