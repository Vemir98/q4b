<div class="q4-carousel-table-wrap">

    <div class="q4-carousel-table" data-structurecount="<?=count($plans)?>">

        <?foreach ($plans as $plan): ?>

        <?
            $crafts = [];
            foreach ($plan->crafts->find_all() as $craft) {
                $crafts[] = $craft->id;
            }
        ?>
            <div class="item" data-crafts='<?=json_encode($crafts)?>' class="<?=in_array($item->craft_id, $crafts) ? '' : 'hidden'?>">

                <div class="hidden pln-data">
                    <div class="qc-change-plan">
                        <a href="#" data-toggle="modal" data-target="#choose-plan-modal-mobile"><?=__('Choose plan')?></a>
                    </div>
                    <ul class="qc-plan-details-list">
                        <input type="hidden" name="plan_id" value="<?=$plan->id?>"/>
                        <li>
                            <span class="qc-plan-details-list-text blue-head-title"><?=__('Plan name')?>: <?=$plan->file() ? $plan->file()->getName() : $plan->name?></span>
                        </li>
                        <li>
                            <i class="icon q4bikon-project"></i>
                            <span class="qc-plan-details-list-text"><?=__('Edition')?> : <?=__($plan->edition)?></span>
                        </li>
                        <li>
                            <i class="icon q4bikon-date"></i>
                            <span class="qc-plan-details-list-text"><?=__('Date')?> : <?=$plan->created_at ? date('d/m/Y', $plan->created_at) : ''?></span>
                        </li>
                        <li>
                            <i class="icon q4bikon-company_status"></i>
                            <span class="qc-plan-details-list-text"><?=__('Status')?> : <?=__($plan->status)?></span>
                        </li>
                    </ul>
                    <div class="choose-view-format">
                        <span class="choose-view-format-title"><?=__('Click to view')?>: </span>
                        <?$file = $plan->files->where('status', '=', Enum_FileStatus::Active)->find()?>
                        <?if ($file): ?>
                            <ul class="choose-view-format-list">
                                <li>
                                    <a data-url="<?=(strpos($file->getImageLink(), 'http') === false) ? '/' . trim($file->getImageLink(), '/') : $file->getImageLink()?>" data-fileid="<?=$file->id?>" data-ext="<?=$file->mime?>" data-controller="add_quality_control_image_from_raw_plan"  class="call-lit-plugin" title="<?=$file->original_name?>">
                                        <img src="/media/img/choose-format/format-<?=strtolower($file->ext)?>.png" alt="<?=$file->ext?>"/>
                                    </a>

                                </li>
                            </ul>
                        <?else: ?>
                            <span><?=__('No files')?></span>
                        <?endif?>
                    </div>
                </div>

                <div class="q4-carousel-blue-head">
                    <span class="blue-head-title"><?=__('Name') . ' : ' . $plan->file() ? $plan->file()->getName() : $plan->name?></span>

                    <div class="blue-head-option q4-radio-tick">
                        <input id="rd-<?=$plan->id?>" name="plan" type="radio" value="<?=$plan->id?>">
                        <label for="rd-<?=$plan->id?>"></label>
                    </div>

                </div>
                <div class="q4-carousel-row f0">
                    <div class="q4-mobile-table-key">
                        <?=__('Profession')?>
                    </div>
                    <div class="q4-mobile-table-value">
                        <?=$plan->profession->name?>
                    </div>
                </div>
                <div class="q4-carousel-row f0">
                    <div class="q4-mobile-table-key">
                        <?=__('Floor')?>
                    </div>
                    <div class="q4-mobile-table-value">
                        <?=$plan->getFloorsAsString() ?: '-'?>
                    </div>
                </div>
                <div class="q4-carousel-row f0">
                    <div class="q4-mobile-table-key">
                        <?=__('Element number')?>
                    </div>
                    <div class="q4-mobile-table-value">
                        <?if ($plan->place_id): ?>
                            <?=isset($plan->place->custom_number) ? $plan->place->custom_number : $plan->place->number?>
                        <?else: ?>
                            -
                        <?endif?>
                    </div>
                </div>
                <div class="q4-carousel-row f0">
                    <div class="q4-mobile-table-key">
                        <?=__('Edition')?>
                    </div>
                    <div class="q4-mobile-table-value">
                        <?=$plan->edition?>
                    </div>
                </div>
                <div class="q4-carousel-row f0">
                    <div class="q4-mobile-table-key">
                        <?=__('Date')?>
                    </div>
                    <div class="q4-mobile-table-value">
                        <?=date('d/m/Y', $plan->date)?>
                    </div>
                </div>
                <div class="q4-carousel-row f0">
                    <div class="q4-mobile-table-key">
                        <?=__('Image')?>
                    </div>
                    <div class="q4-mobile-table-value">
                        <?$img = $plan->files->where('status', '=', Enum_FileStatus::Active)->find()?>
                        <?if ($img): ?>
                            <a href="<?=$img->originalFilePath()?>" class="q4-mobile-table-link" target="_blank" title="<?=$img->original_name?>">
                                <img src="/media/img/choose-format/format-<?=strtolower($img->ext)?>.png" alt="<?=$img->ext?>"/>
                            </a>
                        <?endif;?>
                    </div>
                </div>
            </div>
        <?endforeach;?>
    </div>

</div>