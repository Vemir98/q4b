<?defined('SYSPATH') OR die('No direct script access.');?>
<div id="tracking-details-modal" data-backdrop="static" data-keyboard="false" class="modal tracking-details-modal  fade" role="dialog">
    <div class="modal-dialog q4_project_modal modal-dialog-1170 tracking-details-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header q4_modal_header">
                <div class="q4_modal_header-top">
                    <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                    <div class="clear"></div>
                </div>
                <div class="q4_modal_sub_header">
                    <h3><?=__('Tracking details')?> | <span class="tracking-det-id">#<?=$item->id?></span></h3>
                </div>
            </div>
            <form action="<?=URL::site('projects/update_tracking/'.$item->id)?>" class="q4_form"  data-ajax="true" method="post">
            	<input type="hidden" value="" name="x-form-secure-tkn"/>
                <div class="modal-body bb-modal">
                    <div class="plans-modal-dialog-top">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-20 rtl-float-right">
                                    <label class="table_label"><?=__('Profession')?></label>
                                    <input name="" type="text" class="q4-form-input disabled-input" value="<?=$item->plans->find()->profession->name?>">
                                </div>
                                <div class="col-20 rtl-float-right">
                                    <label class="table_label"><?=__('Print date')?></label>
                                    <input data-value="<?=$item->created_at?>" name="plan_date" type="text" class="q4-form-input disabled-input" value="<?=date('d/m/Y',$item->created_at)?>">
                                </div>
                                <div class="col-20 rtl-float-right">
                                    <label class="table_label"><?=__('Departure date')?></label>
                                    <div id="tracking-details-start_date" class="input-group tracking-details-start_date <?=empty($item->departure_date) ? '' : ' disabled-input'?>" data-provide="datepicker">
                                        <div class="input-group-addon small-input-group">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </div>
                                        <input type="text" name="departure_date" class="q4-form-input<?=empty($item->departure_date) ? '' : ' disabled-input'?>" data-date-format="DD/MM/YYYY" value="<?=$item->departure_date ? date('d/m/Y',$item->departure_date) : ''?>">
                                    </div>
                                </div>
                                <div class="col-20 rtl-float-right">
                                    <label class="table_label"><?=__('Received date')?></label>
                                    <div id="tracking-details-end_date" class="input-group tracking-details-end_date<?=empty($item->received_date) ? '' : ' disabled-input'?>" data-provide="datepicker">
                                        <div class="input-group-addon small-input-group">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </div>
                                        <input name="received_date" type="text" class="q4-form-input<?=empty($item->received_date) ? '' : ' disabled-input'?>"  data-date-format="DD/MM/YYYY" value="<?=$item->received_date ? date('d/m/Y',$item->received_date) : ''?>">
                                    </div>
                                </div>
                                <div class="col-20 rtl-float-right">
                                    <label class="table_label"><?=__('Recipient person')?></label>
                                    <input name="recipient" type="text" class="q4-form-input" value="<?=$item->recipient?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="plans-modal-dialog-bottom">
                        <div class="row">
                            <div class="col-md-9 rtl-float-right">
                                <table class="table tracking-details-table scrollable-tbody-content">
                                    <thead>
                                    <tr>
                                        <th><?=__('Plan name')?></th>
                                        <th><?=__('File')?></th>
                                        <th class="td-max-100"><?=__('Edition')?></th>
                                        <th><?=__('Plan date')?></th>
                                    </tr>
                                    </thead>
                                    <tbody class="tracking-details-tbody qc-vertical-scrll scrollable-tbody-layout">
                                    	<?foreach ($item->plans->find_all() as $plan):?>
	                                        <tr>
	                                            <td data-th="Plan name">
                                                    <span class="q4-form-input" value="" title="<?=$plan->file()->original_name?>"><?=$plan->file()->getName()?></span>
                                                </td>
                                                <td data-th="File">
                                                    <span class="q4-form-input" title="<?=$plan->file()->original_name?>">
                                                        <a class="q4-link-b" href="<?=$plan->file()->originalFilePath()?>" target="_blank">
                                                            <?=$plan->file()->original_name?>
                                                        </a>
                                                    </span>
                                                </td>
                                                <td data-th="Edition" class="td-max-100">
	                                                <span class="q4-form-input" value=""><?=$plan->edition?></span>
	                                            </td>
                                                <td data-th="Plan date">
                                                    <span class="q4-form-input" value=""><?=date('d/m/Y',$plan->date)?></span>
                                                </td>
	                                        </tr>
                                    	<?endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-3 rtl-float-right">
                                <div class="wrap-image-upload">

                                    <div class="upload-tracking-img">
                                        <div class="hide-upload">
                                            <input type="file" accept=".jpg,.jpe,.jpeg,.png,.gif,.tif,.tiff,.pdf" class="upload-user-logo2"  name="file" />
                                        </div>
                                        <?if(!$item->file):?>
                                            <div class="camera-bg camera-default-image">
                                                <img src="/media/img/camera.png" class="camera" alt="camera">
                                            </div>
                                            <img class="hidden preview-user-image show-uploaded-image" alt="preview image">
                                        <?else:?>
                                            <a class="print-dt-link" target="_blank" href="<?=$item->filePath()?>">
                                                    <?if(strtolower(end(explode('.',$item->file)))=='pdf'):?>
                                                    <img src="/media/img/pdf-icon.png" class="preview-user-image show-uploaded-image pdf-icon" alt="pdf">

                                                <?else:?>
                                                    <img src="<?=$item->filePath()?>" class="preview-user-image show-uploaded-image" alt="preview user image">
                                                <?endif;?>
                                            </a>

                                        <?endif?>
                                    </div>

                                    <div class="tracking-details-buttons">
                                        <span class="circle-sm orange inliner q4-upload-file set-image-link2" title="Upload File">
                                           <i class="q4bikon-upload"></i>
                                        </span>
                                       <!--  <span class="circle-sm blue inliner print-element<?=$item->file ? '' : ' disabled-gray-button'?>" title="Print element">
                                           <i class="q4bikon-print"></i>
                                        </span> -->
                                        <?$ext = explode('.',$item->file)?>
                                        <?if($ext[count($ext)-1]!=='pdf'):?>
                                                <span class="circle-sm blue inliner print-element<?=$item->file ? '' : ' disabled-gray-button'?>" title="Print element">
                                                <i class="q4bikon-print"></i>
                                        </span>
                                            <?else:?>
                                                <a href="<?=$item->filePath()?>" target="_blank" class="circle-sm inliner blue<?=$item->file ? '' : ' disabled-gray-button'?>"  title="Print Element">
                                                    <i class="q4bikon-print"></i></a>

                                            <?endif?>
                                        <span data-url="<?=URL::site('projects/delete_tracking_file/'.$item->id)?>" class="circle-sm red inliner delete-tracking-file<?=$item->file ? '' : ' disabled-gray-button'?>" title="Delete list">
                                            <i class="q4bikon-delete"></i>
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="plans-modal-dialog-bottom">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="table_label"><?=__('Description')?></label>
                                <textarea name="comments" class="modal-plans-details-textarea"><?=trim($item->comments)?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-align">
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="#" class="q4-btn-lg orange q4_form_submit update-tracking-confirm"><?=__('Update')?></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--tracking-details-modal-->
</div>


