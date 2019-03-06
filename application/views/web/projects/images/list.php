<?defined('SYSPATH') OR die('No direct script access.');?>
<div id="see-project-images-modal" data-backdrop="static" data-keyboard="false" class="modal fade" role="dialog">
    <div class="modal-dialog q4_project_modal see-project-images-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header q4_modal_header">
                <div class="q4_modal_header-top">
                    <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                    <div class="clear"></div>
                </div>
                <div class="q4_modal_sub_header">
                    <h3><?=__('Project images')?></h3>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="display-project-images q4-vertical-scroll">

                            <ul class="display-project-images-list">
                                <?foreach($images as $image):?>
                                <li>
                                    <div class="display-project-image-scr <?=($image->id == $main_image_id)?'main-image':''?>" data-url="<?=URL::site('projects/set_image/'.$_PROJECT->id.'/'.$image->id)?>">
                                    <div class="display-project-image-wrapper">
                                        <img src="<?=$image->originalFilePath()?>" alt="<?=$image->original_name?>">
                                    </div>
                                        <span class="display-project-image-title"><?=__('Main Image')?></span>
                                    </div>
                                        <span class="display-project-image-option">
                                            <a href="<?=$image->originalFilePath()?>" target="_blank" class="download_file" download="<?=$image->original_name?>"><i class="q4bikon-download"></i></a>
                                            <?if(($image->id != $main_image_id)):?>
                                            <span class="delete_row delete-proj-image" data-url="<?=URL::site('projects/delete_image/'.$_PROJECT->id.'/'.$image->id)?>"><i class="q4bikon-delete"></i></span>
                                            <?endif?>
                                        </span>
                                </li>
                                <?endforeach;?>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-center form_row">
                <div class="row">
                    <div class="col-sm-12">
                        <a href="#" id="change-main-image" class="change-main-image"><?=__('Change main image')?></a>
                        <form action="/" enctype="multipart/form-data">

                             <div class="hide-upload">
                                    <input type="file" class="upload-project-image" name="project-load-new-image-files[]" multiple="">
                                </div>
                                <span class="inline_block_btn light_blue_btn set-project-image"><?=__('Upload new image(s)')?></span>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- end of see-project-images