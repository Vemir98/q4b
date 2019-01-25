<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.06.2017
 * Time: 14:11
 */?>

    <div id="tasks-quality-control-images-list" class="modal-images-list-table form-group" style="overflow-y: auto">
        <table>
            <tbody>
                <?foreach ($items as $number => $image):?>
                    <tr>

                    	<?$imageW = Image::factory(DOCROOT.$image->originalFilePath());?>
                        <td data-th="Image">
                            <span class="modal-tasks-image-action">
                                <a data-url="<?=URL::withLang($image->originalFilePath(),Language::getDefault()->iso2,'https')?>" data-controller="update_quality_control_image" data-ext="<?=$image->mime?>" data-fileid="<?=$image->id?>" title="<?=$image->original_name?>" class="call-lit-plugin" >
                                <span class="modal-tasks-image-number"><?=$number+1?>.</span>
                                <span class="modal-tasks-image-name"><?=$image->original_name?></span>
                                 <span class="modal-img-upload-date">(<?=__('uploaded')?>: <?=date('d.m.Y',$image->created_at)?>)</span></a>
                            </span>
                        </td>
                        <td data-th="Download" class="modal-tasks-image-option">
                            <span class="modal-tasks-image-action">
                                <a href="<?=URL::withLang($image->originalFilePath(),Language::getDefault()->iso2,'https')?>" class="download_file" download="<?=$image->name?>" data-url="">
                                    <i class="q4bikon-download"></i>
                                </a>
                            </span>
                        </td>
                         <?if(!$disabled):?>
                            <td data-th="Delete" class="modal-tasks-image-option">
                                <span class="modal-tasks-image-action">
                                    <span class="delete_row" data-url="<?=URL::site('/projects/delete_quality_control_file/'.$project->id).'/'.$item->id.'/'.$image->token?>"><i class="q4bikon-delete"></i></span>
                                </span>
                            </td>
                        <?endif;?>
                    </tr>
                <?endforeach?>
            </tbody>
        </table>
    </div>
