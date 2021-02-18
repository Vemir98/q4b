<?if (!count($qcs)): ?>
    <div class="report-status-list no-result">
        <img src="/media/img/no-report-result.png" alt="No result">

        <h2><?=__('No reports found')?></h2>
    </div>
    <?else: ?>
    <div class="report-result">
        <div class="text-center">
            <img class="q4b-logo" src="/media/img/iso-group-365.png" alt="logo">
            <img class="q4b-logo" src="/media/img/logo_variation2-3.png" alt="logo">
        </div>
    </div>
    <?if ($pagination->current_page == 1): ?>
    <div class="report-project-desc f0">
        <div class="report-project-desc-image">
            <?if(!$_PROJECT->image_id):?>
                <img src="/media/img/camera.png" alt="project images">
            <?else:?>
                <img src="<?=$_PROJECT->main_image->originalFilePath()?>" alt="project images">
            <?endif?>
        </div>
        <div class="report-project-desc-list">
            <ul>
                <li>
                    <span class="light-blue">
                        <i class="icon q4bikon-companies"></i>
                        <?=__('Company name')?>:
                    </span>
                    <span class="dark-blue">
                        <?=$_COMPANY->name?>
                    </span>
                </li>
                <li>
                    <span class="light-blue">
                        <i class="icon q4bikon-project"></i>
                        <?=__('Project name')?>:
                    </span>
                    <span class="dark-blue">
                        <?=$_PROJECT->name?>
                    </span>
                </li>
                <li>
                    <span class="light-blue">
                        <i class="q4bikon-username"></i>
                        <?=__('Owner')?>:
                    </span>
                    <span class="dark-blue">
                        <?=$_PROJECT->owner?>&#x200E;
                    </span>
                </li>
                <li>
                    <span class="light-blue">
                        <i class="q4bikon-date"></i>
                        <?=__('Start Date')?>:
                    </span>
                    <span class="dark-blue">
                        <?=date('d/m/Y', $_PROJECT->start_date)?>
                    </span>
                </li>
                <li>
                    <span class="light-blue">
                        <i class="q4bikon-date"></i>
                        <?=__('End Date')?>:
                    </span>
                    <span class="dark-blue">
                        <?=date('d/m/Y', $_PROJECT->end_date)?>
                    </span>
                </li>
            </ul>
            <ul>
                <li>
                    <span class="light-blue">
                        <i class="q4bikon-company_id"></i>
                        <?=__('Project ID')?>:
                    </span>
                    <span class="dark-blue">
                        <?=$_PROJECT->id?>
                    </span>
                </li>
                <li>
                    <span class="light-blue">
                        <i class="q4bikon-company_status"></i>
                        <?=__('Project Status')?>:
                    </span>
                    <span class="dark-blue">
                        <?=__($_PROJECT->status)?>
                    </span>
                </li>
                <li>
                    <span class="light-blue">
                        <i class="q4bikon-address"></i>
                        <?=__('Address')?>:
                    </span>
                    <span class="dark-blue">
                        <?=$_PROJECT->address?>
                    </span>
                </li>
                <li>
                    <span class="light-blue">
                        <i class="q4bikon-uncheked"></i>
                        <?=__('Quantity of properties')?>:
                    </span>
                    <span class="dark-blue">
                        <?=$_PROJECT->objects->count_all()?>
                    </span>
                </li>
                <li>
                    <span class="light-blue range-key">
                        <i class="q4bikon-date"></i>
                        <?=__('Report Range')?>:
                    </span>
                    <span class="dark-blue range-val">
                        <span><?=$data['from']?>-<?=$data['to']?></span>
                    </span>
                </li>
            </ul>
        </div>
        <div class="clear"></div>


        <div class="report-project-desc-text">
            <p>
                <span class="report-project-desc-intro"><?=__('Project Description')?>:</span> <?=$_PROJECT->description?>
            </p>
        </div>

    </div>
    <?if($_USER->isGuest()):?>
        <div class="text-center">
            <?$lang = Language::getCurrent()->iso2 == 'en' ? '':Language::getCurrent()->iso2 ?>
            <a href="/<?=$lang?>" target="_blank" class="inline_block_btn blue-light-button">
            <span class="q4-page-export-text"><?=__('Login')?></span>
            </a>
        </div>
    <?endif?>

        <?=View::make($_VIEWPATH.'statistics',
            [
                'crafts' => $crafts,
                'craftsParams' => $craftsParams,
                'filteredCraftsParams' => $filteredCraftsParams,
                'craftsList' => $craftsList,
                'filteredCraftsList' => $filteredCraftsList,
                'craftName' => $qcs[0]->craft->name,
            ])?>

        <?endif;?>
        <?foreach ($qcs as $q): ?>

            <?=View::make($_VIEWPATH.'list-item',
            [
                'q' => $q,
            ])?>


        <?endforeach?>
    <?=$pagination?>

<?endif?>