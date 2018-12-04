<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.06.2017
 * Time: 14:11
 */?>
    <div data-structurecount="<?=count($items)?>" class="qc-image-list-mobile q4-owl-carousel">
         <?foreach ($items as $number => $image):?>
            <?$imageW = Image::factory(DOCROOT.$image->originalFilePath());?>
            <div class="item qc-image-list-mobile-item">
                <a data-url="<?=$image->originalFilePath()?>" data-controller="update_quality_control_image" data-ext="<?=$image->mime?>" data-width="<?=$imageW->width?>" data-height="<?=$imageW->height?>" data-fileid="<?=$image->id?>" title="<?=$image->original_name?>" class="call-lit-plugin">
                    <span class="modal-tasks-image-number"><?=$number+1?>&nbsp;</span>
                    <span class="modal-tasks-image-name"><?=$image->original_name?></span>
                    <span class="modal-img-upload-date">
                        (<?=__('uploaded').':'.date('d.m.Y',$image->created_at)?>)&#x200E;
                    </span>
                </a>
                <div class="qc-image-list-mobile-item-options">
                    <span class="circle-sm red delete_row" data-url="<?=URL::site('/projects/delete_quality_control_file/'.$project->id).'/'.$item->id.'/'.$image->token?>">
                        <i class="q4bikon-delete"></i>
                    </span>
                </div>
            </div>
        <?endforeach?>
    </div>

