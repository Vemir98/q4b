<section id="acceptance" class="new-styles">
    <ul>
        <li class="tab-panel<?=$tabsDisabled ?' disabled':null?>">
            <div class="panel_header open">
                <h2><?=__('Delivery form')?></h2>
            </div>
            <div class="panel_content open">
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