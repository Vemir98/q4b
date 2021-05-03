<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 4/23/21
 * Time: 4:53 PM
 */
?>
    <div class="report-project-desc tab_content" id="tab_info" style="display: none">
        <div class="report_project_title"><?=__('General Information')?></div>
<div class="report-project-desc_wraper">
    <div class="report-project-desc-image">
        <div></div>
        <?if(!$_PROJECT->image_id):?>
            <img src="/media/img/camera.png" alt="project images">
        <?else:?>
            <img src="<?=$_PROJECT->main_image->originalFilePath()?>" alt="project images">
        <?endif?>
    </div>
    <div class="report-project-desc-list">
        <ul>
            <li>
                <span class="dark-blue">
                    <?=__('Company name')?>:
                </span>
                <span class="light-blue">
                   <?=$_COMPANY->name?>
                </span>
            </li>
            <li>
                <span class="dark-blue">
                    <?=__('Project name')?>:
                </span>
                <span class="light-blue">
                   <?=$_PROJECT->name?>
                </span>
            </li>
            <li>
                <span class="dark-blue">
                    <?=__('Owner')?>:
                </span>
                <span class="light-blue">
                    <?=$_PROJECT->owner?>
                </span>
            </li>
            <li>
                <span class="dark-blue">
                   <?=__('Start Date')?>:
                </span>
                <span class="light-blue">
                    <?=date('d/m/Y', $_PROJECT->start_date)?>
                </span>
            </li>
            <li>
                <span class="dark-blue">
                    <?=__('End Date')?>:
                </span>
                <span class="light-blue">
                    <?=date('d/m/Y', $_PROJECT->end_date)?>
                </span>
            </li>
        </ul>
        <ul>
            <li>
                <span class="dark-blue">
                   <?=__('Project ID')?>:
                </span>
                <span class="light-blue">
                   <?=$_PROJECT->id?>
                </span>
            </li>
            <li>
                <span class="dark-blue">
                    <?=__('Project Status')?>:
                </span>
                <span class="light-blue">
                    <?=__($_PROJECT->status)?>
                </span>
            </li>
            <li>
                <span class="dark-blue">
                    <?=__('Address')?>:
                </span>
                <span class="light-blue">
                    <?=$_PROJECT->address?>
                </span>
            </li>
            <li>
                <span class="dark-blue">
                    <?=__('Quantity of properties')?>:
                </span>
                <span class="light-blue">
                    <?=$_PROJECT->objects->count_all()?>
                </span>
            </li>
            <li>
                <span class="dark-blue ">
                    <?=__('Report Range')?>:
                </span>
                <span class="light-blue ">
                    <span><?=$rangeFrom?>-<?=$rangeTo?></span>
                </span>
            </li>
        </ul>
    </div>
</div>
</div>
