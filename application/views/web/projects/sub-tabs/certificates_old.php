<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 11.03.2017
 * Time: 16:33
 */
?>

<section id="certifications" class="new-styles">
    <ul>
        <li class="tab-panel">
            <div class="panel_header open">
                <h2><?=__('old_certificates')?></h2>
            </div>
            <div class="panel_content open">
                <div class="panel_body container-fluid">
                    <certifications-old
                            items-url="<?=URL::site('/entities/certifications_old/'. $_PROJECT->company->id.'/'.$_PROJECT->id)?>"
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
                            v-bind:status-options="[{val: '<?=Enum_ApprovalStatus::Waiting?>',label: '<?=__(Enum_ApprovalStatus::Waiting)?>'},{val: '<?=Enum_ApprovalStatus::Approved?>',label: '<?=__(Enum_ApprovalStatus::Approved)?>'}]"

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