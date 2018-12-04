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
            <!-- <div class="modal-header q4_modal_header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="q4_modal_sub_header">
                    <h3><?=__('Standard')?> <?=$item->name?> <?=__('Files List')?></h3>
                </div>
            </div> -->


              <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                        <div class="clear"></div>
                    </div>
                     <div class="q4_modal_sub_header">
                    <h3><?=__('Standard')?> <?=$item->name?> <?=__('Files List')?></h3>
                </div>
            </div>


            <div class="modal-body q4_modal_body">
                <?if(!empty($files)): $i=1;?>
                    <ul class="list_uploaded_files">
                        <?foreach ($files as $file):?>

                                <li>
                        <span class="uploaded_file_links">
                            <a href="<?=$downloadLinkUri.'/'.$item->id.'/'.$file->token?>" class="download_file">
f                                <i class="q4bikon-download"></i>
                            </a>
                            <a href="<?=$deleteLinkUri.'/'.$item->id.'/'.$file->token?>" class="remove_file delete_row delete-std-file">
                                <i class="q4bikon-delete"></i>
                            </a>
                        </span>
                                    <div class="uploaded_file">
                                        <span class="file_number"><?=__('File')?> <?=$i++?>:</span>
                                        <span class="file_name"><?=$file->original_name?></span>
                                    </div>
                                </li>
                        <?endforeach;?>
                    </ul>
                <?endif?>

            </div>
            <div class="modal-footer q4_modal_footer">
                <form class="concrete-std-form" action="<?=$action?>" data-ajax="true" enctype="multipart/form-data">
                    <input type="hidden" value="" name="x-form-secure-tkn"/>
                    <div class="hide_upload">
                        <input type="file" name="files[]" multiple>
                    </div>
                    <input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">
                </form>
                <a href="#" class="inline_block_btn orange_button file_container_modal concrete-std-files-upload">
                    <?=__('Upload new file(s)')?>
                </a>
            </div>
        </div>
    </div>
</div>
