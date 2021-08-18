<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.01.2017
 * Time: 10:47
 */
?>
<?if($tabsDisabled):?>
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="q4_error_message" data-url="<?=URL::site('settings/apply_status/')?>" style="padding:10px 5px;"><?=__('settings-update-process')?></div>
        </div>
    </div>
<?endif;?>
<!--content_new_company-->
<section class="content_new_company">
    <ul>

        <li class="tab_panel<?=$tabsDisabled ?' disabled':null?>">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Crafts')?></h2>
            </div>
            <div class="panel_content">
                <?=View::make($_VIEWPATH.'crafts/form',
                    ['action' => URL::site('settings/update_crafts/'),
                        'items' => $crafts,
                        'secure_tkn' => AesCtr::encrypt(Auth::instance()->get_user()->id.Text::random('alpha'),Auth::instance()->get_user()->id,192)
                    ])?>
            </div><!--.panel_content-->
        </li>
        <li class="tab_panel<?=$tabsDisabled ?' disabled':null?>">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Professions')?></h2>
            </div>
            <div class="panel_content">
                <?=View::make($_VIEWPATH.'professions/form',
                    ['action' => URL::site('settings/update_professions/'),
                        'items' => $professions,
                        'items_crafts' => $professionsSelectedCrafts,
                        'crafts' => $crafts,
                        'secure_tkn' => AesCtr::encrypt(Auth::instance()->get_user()->id.Text::random('alpha'),Auth::instance()->get_user()->id,192)
                    ])?>
            </div><!--.panel_content-->
        </li>

        <li class="tab_panel<?=$tabsDisabled ?' disabled':null?>">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Tasks')?></h2>
            </div>
            <div class="panel_content">
                <?=View::make($_VIEWPATH.'tasks/form',
                    ['action' => URL::site('settings/update_tasks/'),
                        'items' => $tasks,
                        'items_crafts' => $tasksSelectedCrafts,
                        'crafts' => $crafts,
                        'secure_tkn' => AesCtr::encrypt(Auth::instance()->get_user()->id.Text::random('alpha'),Auth::instance()->get_user()->id,192)
                    ])?>
            </div><!--.panel_content-->
        </li>
        <li class="tab_panel<?=$tabsDisabled ?' disabled':null?>">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Property objects')?></h2>
            </div>
            <div class="panel_content">
                 <div class="panel_body property-objects-body container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="property-object-buttons">
                                <span class="inline_block_btn orange_button object-type-modal" data-url="<?=URL::site('settings/object_types')?>"><?=__('Property Types')?></span>
                                <span class="inline_block_btn orange_button construct-element-modal" data-url="<?=URL::site('settings/construct_elements')?>"><?=__('Construction Elements')?></span>
                                <span class="inline_block_btn orange_button space-type-modal" data-url="<?=URL::site('settings/space_types')?>" ><?=__('Space Types')?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--.panel_content-->
        </li>
    </ul>
</section><!--.content_new_company-->
<?if(!$tabsDisabled):?>
    <div class="text-right-left"  style="margin-bottom: 20px">
        <div class="row">
            <div class="col-md-12">
                <a href="<?=URL::site('settings/apply_to_all')?>" class="inline_block_btn orange_button"><?=__('Apply to all')?></a>
            </div>
        </div>
    </div>
<?endif;?>
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
                                        select-all-txt="<?=__('Select all')?>"
                                        unselect-all-txt="<?=__('Deselect all')?>"
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
                                        list-url="<?=URL::site('/acceptance/get_rms_list')?>"
                                />
                            </tab>
                            <tab name="<?=__('Transferable items')?>">
                                <transferable-items
                                        th-name1="<?=__('Actions')?>"
                                        th-name2="<?=__('Item Text')?>"
                                        th-name3="<?=__('Quantity')?>"
                                        select-all-txt="<?=__('Select all')?>"
                                        unselect-all-txt="<?=__('Deselect all')?>"
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
                                        list-url="<?=URL::site('/acceptance/get_ti_list')?>"
                                />
                            </tab>
                            <tab name="<?=__('Texts')?>">
                                <texts
                                        th-name1="<?=__('Actions')?>"
                                        th-name2="<?=__('Item Text')?>"
                                        th-type="<?=__('Text type')?>"
                                        select-all-txt="<?=__('Select all')?>"
                                        unselect-all-txt="<?=__('Deselect all')?>"
                                        add-text="<?=__('New text')?>"
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
                                        list-url="<?=URL::site('/acceptance/get_te_list')?>"
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
        });
    </script>
<?endif?>
<section id="regulations" class="new-styles">
    <ul>
        <li class="tab_panel<?=$tabsDisabled ?' disabled':null?>">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Regulations')?></h2>
            </div>
            <div class="panel_content">
                <div class="panel_body container-fluid">
                    <universal-certification
                            items-url="<?=URL::site('/entities/regulations')?>"
                            delete-url="<?=URL::site('/certifications/delete_regulation')?>"
                            save-url="<?=URL::site('/certifications/save_regulations')?>"
                            select-all-txt="<?=__('select all')?>"
                            desc-txt="<?=__('Description1')?>"
                            file-txt="<?=__('File')?>"
                            upload-date-txt="<?=__('Upload date')?>"
                            save-txt="<?=__('Save')?>"
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
        el: '#regulations',
    })
</script>
