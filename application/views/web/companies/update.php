<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.01.2017
 * Time: 10:47
 */
?>

<section class="content_new_company">
    <ul>
        <li class="tab_panel">
            <div class="panel_header open">
                <span class="sign"><i class="panel_header_icon q4bikon-minus"></i></span><h2><?=__('Company General Information')?></h2>
            </div>

            <div class="panel_content open">
                <form action="" class="q4_form" autocomplete="off" data-ajax="true" enctype="multipart/form-data">
                    <input type="hidden" value="" name="x-form-secure-tkn"/>
                    <div class="panel_body container-fluid">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="set-image-block centered q4-file-upload" data-id="company">
                                    <div class="upload-logo up-box">
                                        <div class="hide-upload">
                                            <input type="file" accept=".jpg,.jpe,.jpeg,.png,.gif,.tif,.tiff" class="upload-user-logo"  name="logo" />
                                        </div>
                                        <?if(empty($company->logo)):?>
                                            <div class="camera-bg camera-default-image">
                                                <img src="/media/img/camera.png" class="camera" alt="camera">
                                            </div>
                                            <img class="hidden preview-user-image show-uploaded-image" alt="preview user image">
                                        <?else:?>
                                            <img class="preview-user-image show-uploaded-image" alt="preview image" src="/<?=$company->logo?>">
                                        <?endif?>
                                    </div>
                                    <a href="#" class="form-control light_blue_btn set-image-link trigger-image-upload"><?=__('Browse company logo')?></a>
                            <?if($company->projects->count_all()):?>
                            <div class="q4-list-item-info projects text-center"><a href="<?=URL::site('projects/company/'.$company->id)?>"><?=__('Projects')?>: <?=$company->projects->count_all()?></a></div>
                        <?endif?>
                                </div>
                            </div>


                            <div class="border_left col-lg-9">
                                <div class="row">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <label class="table_label"><?=__('Company name')?></label>
                                        <div class="form-group form_row">
                                            <i class="input_icon q4bikon-companies"></i>
                                            <input type="text" class="q4-form-input symbol q4_required" name="name" value="<?=$company->name?>" required/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <label class="table_label"><?=__('Company Status')?></label>
                                        <div class="form-group form_row">
                                             <div class="select-wrapper select_large">
                                                <i class="q4bikon-arrow_bottom"></i>

                                                <select name="status" class="q4-select q4-form-input q4_select select-icon-pd">
                                                    <?foreach(Enum_CompanyStatus::toArray() as $val):?>
                                                        <option value="<?=$val?>" <?=$val == $company->status ? 'selected="selected"' : null?>><?=ucfirst(__($val))?></option>
                                                    <?endforeach?>
                                                </select>
                                            </div>
                                            <i class="input_icon q4bikon-company_status"></i>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <label class="table_label"><?=__('Select Country')?></label>
                                        <div class="form-group form_row">
                                             <div class="select-wrapper select_large">
                                                <?=Form::select('country_id',$countries,$clientCountryId,['class' => 'q4-select q4-form-input q4_select select-icon-pd'])?>
                                            </div>
                                            <i class="input_icon q4bikon-country"></i>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <label class="table_label"><?=__('Segment Type')?></label>
                                        <div class="form-group form_row">
                                             <div class="select-wrapper select_large">
                                                <i class="q4bikon-arrow_bottom"></i>
                                                <select name="type" class="q4-select q4-form-input disable select-icon-pd">
                                                    <option value=""><?=__(ucfirst($company->client->type))?></option>
                                                </select>
                                            </div>
                                            <i class="input_icon q4bikon-sygment_type"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <label class="table_label"><?=__('Address')?></label>
                                        <div class="form-group form_row">
                                            <i class="input_icon q4bikon-address"></i>
                                            <input type="text" name="address" class="q4-form-input symbol" value="<?=$company->address?>" required/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <label class="table_label"><?=__('Company ID')?></label>
                                        <div class="form-group form_row">
                                            <i class="input_icon q4bikon-password"></i>
                                            <input type="text" name="company_id" class="q4-form-input symbol" value="<?=$company->company_id?>"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="table_label"><?=__('Company Description')?></label>
                                        <div class="form-group form_row">
                                            <textarea class="form-control" name="description" rows="4"><?=$company->description?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="panel_footer text-align">
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <a href="#" class="inline_block_btn dark_blue_button mr_30"><?=__('Archive')?></a>
                                <a href="#" class="inline_block_btn orange_button q4_form_submit"><?=__('Update')?></a>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="secure_tkn" value="<?=AesCtr::encrypt($company->id.Text::random('alpha'),$company->id,192)?>">
                </form>
            </div>

        </li>
        <li class="tab_panel ">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Crafts')?></h2>
            </div>
            <div class="panel_content">
                <?=View::make($_VIEWPATH.'crafts/form',
                    ['action' => URL::site('companies/update_crafts/'.$company->id),
                        'items' => $crafts,
                        'secure_tkn' => AesCtr::encrypt($company->id.Text::random('alpha'),$company->id,192)
                    ])?>
            </div>
        </li>
        <li class="tab_panel">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Professions')?></h2>
            </div>
            <div class="panel_content">
                <?=View::make($_VIEWPATH.'professions/form',
                    ['action' => URL::site('companies/update_professions/'.$company->id),
                        'items' => $professions,
                        'items_crafts' => $professionsSelectedCrafts,
                        'crafts' => $crafts,
                        'secure_tkn' => AesCtr::encrypt($company->id.Text::random('alpha'),$company->id,192)
                    ])?>
            </div>
        </li>
        <li class="tab_panel">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Users')?></h2>
            </div>
            <div class="panel_content">
                <?=View::make($_VIEWPATH.'users/form',
                    ['action' => URL::site('companies/update_users/'.$company->id),
                        'items' => $users,
                        'professions' => $professions,
                        'roles' => $userRoles,
                        'secure_tkn' => AesCtr::encrypt($company->id.Text::random('alpha'),$company->id,192)
                    ])?>
            </div>

        </li>
<!--        <li class="tab_panel">-->
<!--            <div class="panel_header">-->
<!--                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2>--><?//=__('Standards')?><!--</h2>-->
<!--            </div>-->
<!--            <div class="panel_content">-->
<!--                --><?//=View::make($_VIEWPATH.'standards/form',
//                    ['action' => URL::site('companies/update_standards/'.$company->id),
//                        'items' => $standards,
//                        'users' => $users,
//                        'secure_tkn' => AesCtr::encrypt($company->id.Text::random('alpha'),$company->id,192)
//                    ])?>
<!--            </div>-->
<!--        </li>-->
        <li class="tab_panel">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Links to other systems')?></h2>
            </div>
            <div class="panel_content">
                <?=View::make($_VIEWPATH.'links/form',
                    ['action' => URL::site('companies/update_links/'.$company->id),
                        'items' => $company->links->order_by('id','DESC')->find_all(),
                        'secure_tkn' => AesCtr::encrypt($company->id.Text::random('alpha'),$company->id,192)
                    ])?>
            </div>
        </li>
    </ul>
</section>
<section id="instructions" class="new-styles">
    <ul>
        <li class="tab_panel">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Instructions')?></h2>
            </div>
            <div class="panel_content">
                <div class="panel_body container-fluid">
                    <universal-certification
                            items-url="<?=URL::site('/entities/instructions/'. $company->id)?>"
                            delete-url="<?=URL::site('/certifications/delete_regulation')?>"
                            save-url="<?=URL::site('/certifications/save_instructions/'.$company->id)?>"
                            company-id="<?=$company->id?>"
                            companies-url="<?=URL::site('/entities/companies?fields=id,name')?>"
                            copy-url="<?=URL::site('/certifications/copy_instructions')?>"
                            select-all-txt="<?=__('select all')?>"
                            desc-txt="<?=__('Description1')?>"
                            save-txt="<?=__('Save')?>"
                            file-txt="<?=__('File')?>"
                            upload-date-txt="<?=__('Upload date')?>"
                            status-txt="<?=__('Status')?>"
                            more-txt="<?=__('More')?>"
                            delete-txt="<?=__('Delete')?>"
                            copy-txt="<?=__('Copy to')?>"
                            copy-btn-txt="<?=__('Copy')?>"
                            select-company-txt="<?=__('Select Company')?>"
                            select-project-txt="<?=__('Select project')?>"
                            v-bind:status-options="[{val: '<?=Enum_ApprovalStatus::Waiting?>',label: '<?=__(Enum_ApprovalStatus::Waiting)?>'},{val: '<?=Enum_ApprovalStatus::Approved?>',label: '<?=__(Enum_ApprovalStatus::Approved)?>'}]"

                    />
                </div>

            </div>
        </li>
    </ul>
</section>
<script>
    var regulations = new Vue({
        el: '#instructions',
    })
</script>