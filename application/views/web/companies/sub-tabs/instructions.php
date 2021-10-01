<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 25.09.2021
 * Time: 14:46
 */
?>
<section id="instructions" class="new-styles">
    <ul>
        <li class="tab-panel">
            <div class="panel_header open">
                <h2><?=__('Instructions')?></h2>
            </div>
            <div class="panel_content open">
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
