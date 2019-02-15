<?defined('SYSPATH') OR die('No direct script access.');?>

<?$disabled = $item->hasQualityControl() ? ' disabled-input': ''?>
<div id="update-plan-modal" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog">
    <div class="modal-dialog q4_project_modal modal-dialog-1170">
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
                        <h3><?=__('Plan Details')?> | <?=__("File name")?>: <a href="<?=$item->file()->originalFilePath()?>" target="_blank"><?=$item->file()->original_name?></a> </h3>
                        <div class="q4_modal_sub_header-right">
                            <span><?=__('Upload date')?></span>
                            <input type="text" class="table_input disabled-input" value="<?=date('d/m/Y',$item->created_at)?>">
                        </div>
                    </div>

                </div>
                <div class="modal-body bb-modal">
                    <div class="plans-modal-dialog-top">
                        <div class="row">
                            <div class="form-group col-28 rtl-float-right">
                                <label class="table_label"><?=__('Plan name')?></label>
                                <input type="text" class="table_input<?=$disabled?>" name="name" value="<?=$item->file()->loaded() ? $item->file()->getName() : $item->name;?>">
                            </div>
                            <div class="form-group col-28 rtl-float-right">
                                <label class="table_label"><?=__('Property')?></label>
                                 <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <select class="q4-select q4-form-input qc-prop<?=$disabled?>" name="object_id">
                                        <?foreach($_PROJECT->objects->find_all() as $obj):?>
                                            <? if($item->object_id == $obj->id) $defaultObject = $obj?>
                                            <option <?=$item->object_id == $obj->id ? 'selected="selected"' : ''?> value="<?=$obj->id?>" data-min="<?=$obj->smaller_floor?>" data-max="<?=$obj->bigger_floor?>"><?=$obj->name?></option>
                                        <?endforeach?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-28 rtl-float-right">
                                <label class="table_label"><?=__('Profession')?></label>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <select class="q4-select q4-form-input plan-profession<?=$disabled?>" name="profession_id" id="plans-profession-id">
                                        <?foreach ($professions as $profession):?>
                                            <?
                                            $crafts = $profession->crafts->where('status','=',Enum_Status::Enabled)->find_all();
                                            $c = [];
                                            foreach ($crafts as $cr)
                                                $c [$cr->id]= $cr->id;

                                            if(empty($c)) continue;
                                            ?>
                                            <option value="<?=$profession->id?>" <?=$item->profession_id == $profession->id ? 'selected="selected"' : ''?> data-crafts="<?=implode(',',$c)?>"><?=__($profession->name)?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </div>2
                            <div class="form-group col-16 rtl-float-right">
                                <label class="table_label">Sheet Number</label>
                                <div class="input-group form-group">
                                    <input type="text" class="table_input" name="sheet_number" value="<?=$item->sheet_number?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-28 rtl-float-right multi-select-col">

                                <label class="table_label"><?=__('Floors')?>
                                     <?if(!$disabled):?>
                                        <span class="check-all-links" data-seltxt="<?=__('select all')?>" data-unseltxt="<?=__('unselect all')?>"><?=__('select all')?></span>
                                     <?endif?>
                                </label>
                                <div class="multi-select-box comma">
                                    <div class="select-imitation table_input floor-numbers<?=$disabled?>">
                                        <span class="select-imitation-title"><?=$item->getFloorsAsString()?></span>
                                        <div class="over-select"></div><i class="arrow-down q4bikon-arrow_bottom"></i>
                                    </div><?$floorNumbers = $item->floorNumbers();?>
                                    <div class="checkbox-list">
                                        <?for($i = $defaultObject->smaller_floor; $i <= $defaultObject->bigger_floor; $i++):?>
                                            <div class="checkbox-list-row">
                                            <span class="checkbox-text">
                                                <label class="checkbox-wrapper-multiple inline <?=in_array($i,$floorNumbers) ? 'checked' : ''?>" data-val="<?=$i?>">
                                                    <span class="checkbox-replace"></span>
                                                    <i class="checkbox-list-tick q4bikon-tick"></i>
                                                </label>
                                                <span class="checkbox-text-content bidi-override">
                                                    <?=$i?>
                                                </span>

                                            </span>
                                            </div>
                                        <?endfor?>
                                    </div>
                                    <select class="hidden-select" name="floors" multiple>
                                        <?for($i = $defaultObject->smaller_floor; $i <= $defaultObject->bigger_floor; $i++):?>
                                            <option <?=in_array($i,$floorNumbers) ? 'selected="selected"' : ''?> value="<?=$i?>"><?=$i?></option>
                                        <?endfor?>
                                    </select>
                                </div>

                            </div>
                            <div class="form-group col-14 rtl-float-right">
                                <label class="table_label table_label-small"><?=__('Element number')?></label>
                                <input value="<?=!empty($item->place->custom_number) ? $item->place->custom_number : $item->place->number?>" type="text" class="disabled-input q4-form-input plan-place-cnumber">
                            </div>
                            <?$disabledPlace = $item->place->loaded() && !$disabled  ? '':' disabled-input'?>
                            <div class="form-group col-14 rtl-float-right">
                                <label class="table_label"><?=__('Element id')?></label>
                                <input name="place_number" value="<?=$item->place->number?>" data-url="<?=URL::site('projects/get_custom_number/')?>" type="text" class="q4-form-input plan-place-number<?=$disabled?>">

                            </div>
                            <div class="form-group col-14 rtl-float-right">
                                <label class="table_label"><?=__('Element type')?></label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>

                                    <select name="place_type" value="<?=$item->place->type?>" class="q4-select q4-form-input plan-place-type<?=$disabledPlace?>">
                                        <option value=""><?=__('Select type')?></option>
                                        <?foreach (Enum_ProjectPlaceType::toArray() as $type):?>
                                        <?$selected = $item->place->type == $type ? 'selected="selected"' : ''?>
                                            <option value="<?=$type?>" <?=$selected?> ><?=__($type)?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </div>
                            <?
                                $selectedCrafts = $item->crafts->find_all();
                                $selCraftArray = [];
                                foreach ($selectedCrafts as $craft) {
                                    $selCraftArray[$craft->id] = $craft->name;
                                }
                            ?>
                            <div class="form-group col-28 rtl-float-right multi-select-col">

                                <label class="table_label">
                                    <?if(!$disabled):?>
                                        <span class="check-all-links" data-seltxt="<?=__('select all')?>" data-unseltxt="<?=__('unselect all')?>"><?=__('select all')?></span>
                                    <?endif?>
                                    <?=__('Craft')?>
                                </label>
                                <div class="multi-select-box">
                                    <div class="select-imitation<?=$disabled?>">
                                        <span class="select-imitation-title">
                                            <?foreach ($selCraftArray as $craft) {
                                               echo $craft.',';
                                            } ?></span>
                                        <div class="over-select"></div>
                                        <i class="arrow-down q4bikon-arrow_bottom"></i>
                                    </div>
                                    <div class="checkbox-list">
                                        <?$craftsArray=[];

                                        foreach ($professions as $profession):?>
                                            <?
                                            $crafts = $profession->crafts->where('status','=',Enum_Status::Enabled)->order_by('name','ASC')->find_all();
                                            $c = [];
                                                foreach ($crafts as $cr):?>
                                                    <div class="checkbox-list-row<?=$profession->id == $item->profession_id  ? '' : ' hidden'?>" data-profession=<?=$profession->id?>>
                                                        <span class="checkbox-text">
                                                            <label data-val="<?=$cr->id?>" class="checkbox-wrapper-multiple inline <?=in_array($cr->id,array_keys($selCraftArray)) ? 'checked' : ''?>">
                                                                <span class="checkbox-replace "></span>
                                                                <i class="checkbox-list-tick q4bikon-tick"></i>
                                                            </label>
                                                            <?=$cr->name?>
                                                        </span>
                                                    </div>
                                            <?endforeach; ?>
                                        <?endforeach ?>

                                    </div>

                                    <select name="crafts" class="hidden-select" id="plans-hidden-crafts" multiple>
                                        <?$craftsArray = [];

                                        foreach ($professions as $profession):?>
                                            <?
                                            $crafts = $profession->crafts->where('status','=',Enum_Status::Enabled)->find_all();
                                            $c = [];
                                            foreach ($crafts as $cr):?>
                                            <?if(!isset($craftsArray[$cr->id])):?>
                                                <?$craftsArray[$cr->id]=$cr->id?>
                                                <option value="<?=$cr->id?>" <?=in_array($cr->id,array_keys($selCraftArray)) ? ' selected="selected"':''?> data-profession="<?=$profession->id?>"><?=__($cr->name)?></option>

                                            <?endif?>
                                            <?endforeach; ?>

                                        <?endforeach;?>

                                    </select>
                                </div><!--.multi-select-box-->
                            </div>

                            <div class="form-group col-16 rtl-float-right">
                                <label class="table_label"><?=__('Plan date')?></label>
                                <div class="input-group form-group date" data-provide="datepicker">
                                    <div class="input-group-addon small-input-group">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </div>
                                    <input type="text" class="table_input<?=$disabled?>" name="date" value="<?=date('d/m/Y',$item->date)?>" data-date-format="DD/MM/YYYY">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-14 rtl-float-right">
                                <label class="table_label"><?=__('Scale')?></label>
                                <input type="text" class="table_input<?=$disabled?>" name="scale" value="<?=$item->scale?>">
                            </div>
                            <div class="form-group col-14 rtl-float-right relative">
                                <label class="table_label"><?=__('Edition')?></label>
                                <input type="text" class="table_input<?=$disabled?> q4_required " name="edition" value="<?=$item->edition?>">
                            </div>
                            <div class="form-group col-28 rtl-float-right">
                                <label class="table_label"><?=__('Status')?></label>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i><select class="q4-select q4-form-input<?=$disabled?>" name="status">
                                        <?foreach (Enum_ProjectPlanStatus::toArray() as $val):?>
                                            <option value="<?=$val?>" <?=($item->status == $val) ? 'selected="selected"' : '' ?>><?=ucfirst(__($val))?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="plans-modal-dialog-bottom">
 <?//if(Auth::instance()->get_user()->is('general_admin') OR Auth::instance()->get_user()->is('super_admin') OR Auth::instance()->get_user()->is('general_infomanager')):?>
                        <?if(true):?>
                        <div class="row">
                             <div class="col-md-12">
                                 <div class="form-group">
                                     <label class="table_label"><?=__('Tracking details')?></label>
                                 </div>
                             </div>
                         </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <table class="responsive_table table scrollable-tbody">
                                    <thead>
                                        <tr>
                                            <th data-field="Tracking ID" class="td-100"><?=__('Tracking ID')?></th>
                                            <th data-field="Print Date" class="td-100"><?=__('Print date')?></th>
                                            <th data-field="Departure date" class="td-100"><?=__('Departure date')?></th>
                                            <th data-field="Recipient person" class="td-175"><?=__('Recipient person')?></th>
                                            <th data-field="Recieved date" class="td-100"><?=__('Received date')?></th>
                                        </tr>
                                    </thead>
                                    <tbody class="qc-v-scroll-small">
                                        <?foreach ($trackingItems as $track):?>
                                            <tr>
                                                <td data-th="Tracking ID" class="td-100">
                                                    <div class="div-cell">
                                                        <span class="q4-form-input">
                                                            <a class="tracking-details" data-url="<?=URL::site('projects/update_tracking/'.$track->id)?>">#<?=$track->id?>
                                                            </a>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td data-th="Print date" class="td-100">
                                                    <div class="div-cell">
                                                        <input type="text" class="q4-form-input disabled-input" value="<?=date('d/m/Y',$track->created_at)?>">
                                                    </div>
                                                </td>
                                                <td data-th="Departure date" class="td-100">
                                                    <div class="div-cell">
                                                        <input type="text" class="q4-form-input disabled-input" value="<?=$track->departure_date ? date('d/m/Y',$track->departure_date) :'-'?>">
                                                    </div>
                                                </td>
                                                <td data-th="Recipient person" class="td-175">
                                                    <div class="div-cell">
                                                        <input type="text" class="q4-form-input disabled-input" value="<?=$track->recipient?>">
                                                    </div>
                                                </td>
                                                <td data-th="Recieved date" class="td-100">
                                                    <div class="div-cell">
                                                        <input type="text" class="q4-form-input disabled-input" value="<?=$track->received_date ? date('d/m/Y',$track->received_date) :'-'?>">
                                                    </div>
                                                </td>
                                            </tr>
                                        <?endforeach; ?>

                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <?endif?>

                        <div class="row">
                            <div class="form_row">
                                <div class="form-group col-md-12 rtl-float-right">
                                    <div class="mt-15 mb-15">
                                        <label class="table_label"><?=__('Description')?></label>
                                    </div>
                                    <div style="overflow-y: auto">
                                        <textarea class="modal-plans-details-textarea" name="description"><?=$item->description?></textarea>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer text-align">
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="#" class="inline_block_btn orange_button q4_form_submit update-plan-confirm"><?=__('Update')?></a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>


