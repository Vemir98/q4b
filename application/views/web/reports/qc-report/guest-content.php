<div class="report-result" style="background: #fff;">
    <div class="text-center">
        <img class="q4b-logo" src="/media/img/q4b_quality.png" alt="logo">
        <img class="q4b-logo" src="/media/img/q4b_logo.png" alt="logo">
    </div>
</div>
<div id="generated-content" class="qc-report-redesign mt-25">
    <div class="qc_top_section">
        <div class=" qc_serche_section">
            <div class=" qc_top_right">
                <div class="qc_back_btn rotate-180">
                    <a href="<?=URL::site('reports')?>"><i class="q4bikon-arrow_back2 icon fs-22"></i></a>
                </div>
                <div class="qc_report_title"><?=__('QC Report')?> </div>
                <div class="qc_report_date">(<?=$data['from']?>-<?=$data['to']?>)</div>
            </div>
        </div>
        <?if (!count($qcs)): ?>
            <div class="report-status-list no-result">
                <img src="/media/img/no-report-result.png" alt="No result">

                <h2><?=__('No reports found')?></h2>
            </div>
        <?else: ?>
            <div class="qc_tabs_sec">
                <div class="qc_tabs_sec_tabs">
                    <div class="qc_tab qc_tabs Statistics active" data-tab="tab_statistics"><?=__('Statistics')?></div>
                    <div class="qc_tab qc_tabs Quality-controls" data-tab="tab_qc_controls"><?=__('Quality controls')?></div>
                    <div class="qc_tab qc_tabs Info" data-tab="tab_info"><?=__('Info')?></div>
                </div>
            </div>
        <?endif;?>
    </div>
    <?if($_USER->isGuest()):?>
        <div class="text-center">
            <?$lang = Language::getCurrent()->iso2 == 'en' ? '':Language::getCurrent()->iso2 ?>
            <a href="/<?=$lang?>" target="_blank" class="inline_block_btn blue-light-button">
                <span class="q4-page-export-text"><?=__('Login')?></span>
            </a>
        </div>
    <?endif?>
    <?if (count($qcs)): ?>
        <?=View::make($_VIEWPATH.'tab-statistics',
            [
                'crafts' => $crafts,
                'craftsParams' => $craftsParams,
                'filteredCraftsParams' => $filteredCraftsParams,
                'craftsList' => $craftsList,
                'filteredCraftsList' => $filteredCraftsList,
                'craftName' => $qcs[0]->craft->name,
                'qcsCount' => count($qcs),
                'del_rep_id' => $del_rep_id
            ])
        ?>
        <?=View::make($_VIEWPATH.'tab-qc-controls',
            [
                'qcs' => $qcs,
                'tasks' => $tasks,
                'pagination' =>  $pagination
            ])
        ?>
        <?=View::make($_VIEWPATH.'tab-info',
            [
                'rangeFrom' => $data['from'],
                'rangeTo' => $data['to']
            ])
        ?>
    <?endif;?>
</div>
