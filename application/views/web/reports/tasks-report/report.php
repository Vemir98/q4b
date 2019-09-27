<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 27.03.2019
 * Time: 12:55
 */
?>

<div id="generated-content" class="scnd-report">
    <div class="generate-reports-bookmark">
        <div class="generate-reports-bookmark-title">
            <h2><?=$title?></h2>
        </div>
        <div class="generate-reports-bookmark-arrow">
            <i class="q4bikon-arrow_bottom"></i>
        </div>
    </div>
    <div class="report-send-out" style="display: none;">
        <div class="row">
            <div class="col-md-12">
                <a href="#" class="q4-page-export">
                    <i class="q4bikon-export icon-export"></i>
                    <span class="q4-page-export-text"><?=__('Export')?></span>
                </a>
                <span class="send-out-button send-reports"><i class="q4bikon-email"></i> <?=__('Send by email')?></span>
                <span class="send-out-button reports-to1-print-btn"><i class="q4bikon-print"></i> <?=__('Print')?></span>

            </div>
        </div>
    </div>
    <div class="report-project-desc f0" style="margin-top: 25px;">
        <div class="report-project-desc-image">
            <img src="/<?=$companyLogo?>" alt="project images">
        </div>
        <div class="report-project-desc-list">
            <ul style="width: 100%;">
                <li>
                            <span class="light-blue">
                                <i class="icon q4bikon-companies"></i>
                                <?=__('Company name')?>:
                            </span>
                    <span class="dark-blue">
                                <?=$companyName?>
                            </span>
                </li>
                <li>
                            <span class="light-blue">
                                <i class="icon q4bikon-project"></i>
                                <?=__('Project name')?>:
                            </span>
                    <span class="dark-blue">
                                <?=$projectName?>
                            </span>
                </li>
                <li>
                            <span class="light-blue">
                                <i class="q4bikon-head_office"></i>
                                <?=__('Structures')?>:
                            </span>
                    <span class="dark-blue">
                                <?=$objectsNames?>
                            </span>
                </li>
                <li>
                            <span class="light-blue">
                                <i class="q4bikon-floor"></i>
                                <?=__('Floors')?>:
                            </span>
                    <span class="dark-blue" title="<?=$floorsNames?>" style="max-width: 80%; overflow: hidden; height: 16px;">
                                <?=$floorsNames?>
                            </span>
                </li>
                <li>
                    <span class="light-blue">
                                <i class="q4bikon-appartment"></i>
                        <?=__('Place')?>:
                            </span>
                    <span class="dark-blue">
                                <?=$placeName?>
                            </span>
                </li>
                <li></li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>

    <div class="double-result">
        <div class="report-status-results" style="width: 100%">
            <div class="f0">
                <?foreach ($content as $c):?>
                <?=$c?>
                <?endforeach?>
            </div>
        </div>
    </div>

</div>
<script>
    $(document).ready(function(){
        $('.generate-reports-bookmark-arrow').on('click',function(){
            if($(this).data('url') != undefined && $(this).data('url').length > 1){
                $('.loader_backdrop').show();
                document.location.href = $(this).data('url');
            }
        });
    });
</script>

