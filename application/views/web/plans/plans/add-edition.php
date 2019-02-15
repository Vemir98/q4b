<?defined('SYSPATH') OR die('No direct script access.');?>

<div id="new-plans-modal" class="modal new-plans-modal" data-backdrop="static" data-keyboard="false" role="dialog">
    <div class="modal-dialog q4_project_modal modal-dialog-1170 plans-details-dialog">
        <form class="q4_form" action="<?=$action?>" data-ajax="true" method="post">
            <input type="hidden" value="" name="x-form-secure-tkn"/>
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                        <div class="clear"></div>
                    </div>
                    <div class="q4_modal_sub_header">
                        <h3><?=__('Add Edition')?> | <?=__("File name")?>:
                        <a href="<?=$item->file()->originalFilePath()?>" class="modal-file-name" target="_blank">
                            <?=$item->file()->original_name?>
                        </a>
                        </h3>
                    </div>
                </div>
                <div class="modal-body bb-modal">
                    <div class="plans-modal-dialog-top">
                        <div class="row">
                            <div class="form-group col-28 rtl-float-right">
                                <label class="table_label"><?=__('Plan name')?></label>
                                <input type="text" class="table_input disabled-input" value="<?=$item->file() ? $item->file()->getName() : $item->name;?>">
                            </div>
                            <div class="form-group col-28 rtl-float-right">
                                <label class="table_label"><?=__('Property')?></label>
                                <input type="text" class="table_input disabled-input" value="<?=$item->object->type->name?> - <?=$item->object->name?>">
                            </div>
                            <div class="form-group col-28 rtl-float-right">
                                <label class="table_label"><?=__('Profession')?></label>
                                <input type="text" class="table_input disabled-input" value="<?=__($item->profession->name)?>">
                            </div>
                            <div class="form-group col-16 rtl-float-right">
                                <label class="table_label"><?=__('Upload date')?></label>
                                <input type="text" class="q4-form-input disabled-input" value="<?=date('d/m/Y',$item->created_at)?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-28 rtl-float-right">
                                <label class="table_label"><?=__('Floors')?></label>
                                <div class="table_input disabled-input" >
                                    <?=$item->getFloorsAsString()?>
                                </div>
                            </div>
                            <?if($item->place->loaded()){
                                $number = $item->place->number;
                                $type = $item->place->type;
                            }else{
                                $number='';
                                $type = '';
                            }?>
                            <div class="form-group col-14 rtl-float-right">
                                <label class="table_label table_label-small"><?=__('Element number')?></label>
                                <input type="text" class="q4-form-input disabled-input" value="<?=!empty($item->place->custom_number) ? $item->place->custom_number : $item->place->number?>">
                            </div>
                            <div class="form-group col-14 rtl-float-right">
                                <label class="table_label"><?=__('Element id')?></label>
                                <input type="text" class="q4-form-input disabled-input" value="<?=__($number)?>">
                            </div>
                            <div class="form-group col-14 rtl-float-right">
                                <label class="table_label"><?=__('Element type')?></label>
                                <input type="text" class="q4-form-input disabled-input" value="<?=__($type)?>">
                            </div>
                            <div class="form-group col-28 rtl-float-right">
                                <label class="table_label"><?=__('Status')?></label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select class="q4-select q4-form-input" name="status">
                                        <?foreach (Enum_ProjectPlanStatus::toArray() as $val):?>
                                            <option value="<?=$val?>" <?=($item->status == $val) ? 'selected="selected"' : '' ?>><?=ucfirst(__($val))?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-16 rtl-float-right">
                                <label class="table_label"><?=__('Plan date')?></label>
                                <div class="input-group form-group date" data-provide="datepicker">
                                    <div class="input-group-addon small-input-group">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </div>
                                    <input type="text" class="table_input" name="date" value="<?=date('d/m/Y',time())?>" data-date-format="DD/MM/YYYY">
                                </div>
                            </div>

                            <div class="form-group col-16 rtl-float-right">
                                <label class="table_label">Sheet Number</label>
                                <div class="input-group form-group">
                                    <input type="text" class="table_input plans-sheet-number disabled-input" value="<?=$item->sheet_number?>" disabled name="sheet_number">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-14 rtl-float-right">
                                <label class="table_label"><?=__('Scale')?></label>
                                <input type="text" class="table_input" name="scale" value="<?=$item->scale?>">
                            </div>
                            <div class="form-group col-14 rtl-float-right">
                                <label class="table_label"><?=__('Edition')?></label>
                                <input type="text" class="table_input" name="edition" value="<?=$item->edition+1?>">
                            </div>
                        </div>
                    </div>
                    <div class="plans-modal-dialog-bottom">
<!--                        <div class="row">-->
<!--                            <div class="form-group col-md-12">-->
<!--                                <div class="modal-images-list-box">-->
<!--                                    <a href="#" class="inline_block_btn blue-light-button modal-load-single-image">--><?//=__('Load')?><!--</a>-->
<!--                                    <div class="hide-upload">-->
<!--                                        <input type="file" class="load-single-image-input" name="file">-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
                        <div class="row">
                            <div class="form_row">
                                <div class="form-group col-md-6 rtl-float-right">
                                    <div class="mt-15 mb-15">
                                        <label class="table_label"><?=__('Description')?></label>
                                    </div>
                                    <div style="overflow-y: auto">

                                        <textarea class="modal-plans-details-textarea" name="description"><?=$item->description?></textarea>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 rtl-float-right">

                                    <div class="wrap-image-lists">
                                        <div class="modal-images-list-box absoluted">
                                            <a href="#" class="q4-btn-lg light-blue-bg modal-load-single-image"><?=__('Attach file')?></a>
                                            <div class="hide-upload">
                                                <input type="file" class="load-single-image-input" name="file">
                                            </div>
                                        </div>
                                        <div class="mt-15 mb-15">
                                            <label class="table_label"><?=__('History').'( <span class="count-fl-list">'.count($historyItems).'</span>)';?></label>
                                        </div>
                                    </div>

<!--                                    <label class="table_label">--><?//=__('History').'('.count($historyItems).')';?><!--</label>-->
                                    <div class="modal-single-image-table images-list modal-images-list-table">
                                        <table>
                                            <tbody>
                                                <?foreach ($historyItems as $fileIterator=>$item):
                                                $file = $item->files->where('status','=',Enum_FileStatus::Active)->order_by('id','DESC')->find();
                                                ?>

                                                <tr>
                                                    <td data-th="<?=__('Image')?>">
                                                        <span class="modal-tasks-image-action">
                                                            <a href="<?=$file->originalFilePath()?>" title="<?=$file->original_name?>" target="_blank">
                                                                <span class="modal-tasks-image-number"><?=++$fileIterator?>.</span>
                                                                <span class="modal-tasks-image-name"><?=$file->original_name?></span>
                                                                <span class="modal-img-upload-date">(<?=__('uploaded')?>: <?=date('d.m.Y H:i',$file->created_at)?>)</span>
                                                            </a>
                                                        </span>
                                                    </td>
                                                    <td data-th="<?=__('Download')?>" class="modal-tasks-image-option">
                                                        <span class="modal-tasks-image-action">
                                                            <a href="<?=$file->originalFilePath()?>" class="download_file" download="<?=$file->original_name?>">
                                                                <i class="q4bikon-download"></i>
                                                            </a>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>


                </div>
            </div>
                <div class="modal-footer text-align">
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="#" class="q4-btn-lg orange q4_form_submit disabled-gray-button"><?=__('Update')?></a>
                        </div>
                    </div>
                </div>
        </form>

    </div>
</div>



