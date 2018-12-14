<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 13.01.2017
 * Time: 7:09
 */
?>
<!-- File Upload Modal -->
<div class="modal fade" id="modal_file_upload" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modal_file_upload">
    <div class="modal-dialog q4_modal" role="document">
        <div class="modal-content">
            <div class="modal-header q4_modal_header">
                <div class="q4_modal_header-top">
                    <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                    <div class="clear"></div>
                </div>
                <div class="q4_modal_sub_header">
                     <h3><?=__('Certifications')?> <?=$item->name?> <?=__('Files List')?></h3>
                </div>
            </div>
            <div class="modal-body q4_modal_body modal-images-list-table">

                <table class="list_uploaded_files">
                    <tbody>
                    <?if(!empty($files)): $i=1;?>
                        <?foreach ($files as $i=>$file):?>
                           <tr>
                                <td data-th="<?=__('Image')?>">
                                    <span class="modal-tasks-image-action">
                                        <a href="<?=URL::withLang($file->originalFilePath(),Language::getDefault()->slug,'https')?>" title="<?=$file->name?>" target="_blank" >
                                            <span class="modal-tasks-image-number"><?=$i+1?>.</span>
                                            <span class="modal-tasks-image-name"><?=$file->original_name?></span>
                                        </a>
                                    </span>
                                </td>
                                <td data-th="<?=__('Download')?>" class="modal-tasks-image-option">
                                    <span class="modal-tasks-image-action">
                                        <a href="<?=$downloadLinkUri.$item->id.'/'.$file->token?>" class="download_file" data-url="" download="<?=$file->name?>">

                                            <i class="q4bikon-download"></i>
                                        </a>
                                    </span>
                                </td>

                                <td data-th="<?=__('Delete')?>" class="modal-tasks-image-option">
                                    <span class="modal-tasks-image-action">
                                        <span class="delete_row" data-url="<?=$deleteLinkUri.$item->id.'/'.$file->token?>"><i class="q4bikon-delete"></i></span>
                                    </span>
                                </td>

                            </tr>
                        <?endforeach;?>
                    <?endif?>
                    </tbody>
                </table>

            </div>
            <div class="modal-footer q4_modal_footer">
                <form class="concrete-std-form cert-form" action="<?=$action?>" data-ajax="true" enctype="multipart/form-data">
                    <input type="hidden" value="" name="x-form-secure-tkn"/>
                    <div class="hide_upload">
                        <input type="file" class="load-images-input" multiple name="files[]">
                    </div>
                    <input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">
                    <a href="#" class="inline_block_btn orange_button file_container_modal cert-files-upload modal-load-images">
                        <?=__('Upload new file(s)')?>
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>