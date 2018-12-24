<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 04.05.2017
 * Time: 15:25
 */
?>
<form action="<?=$action?>" class="q4_form certifications-form" autocomplete="off" data-ajax="true">
    <input type="hidden" value="" name="x-form-secure-tkn"/>
    <div class="panel_body container-fluid">
        <div class=" panel-options form_row">
            <span class="inline-options">
                <a class="orange_plus_small add-cert"><i class="plus q4bikon-plus"></i></a>
                <span class="inline-options-text"><?=__('Add new certification')?></span>
            </span>
        </div>
        <div class="scrollable-table">

            <table class="responsive_table table" data-toggle="table">
                <thead>
                <tr>
                    <th data-field="Crafts" data-sortable="true" class="td-200"><?=__('Crafts')?></th>
                    <th data-field="Certification Name" data-sortable="true" class="td-200"><?=__('Certification Name')?></th>
                    <th data-field="Date" data-sortable="true" class="td-150"><?=__('Date')?></th>
                    <th data-field="File(s)" data-sortable="true" class="td-150"><?=__('File(s)')?></th>
                    <th data-field="Approved by" data-sortable="true" class="td-200"><?=__('Approved by')?></th>
                </tr>
                </thead>
                <tbody>
                <tr class="el-pattern hidden">
                    <td data-th="<?=__('Crafts')?>">

                    </td>
                    <td data-th="<?=__('Certification Name')?>">
                        <input type="text" class="table_input" data-name="certification_%s_name">
                    </td>
                    <td data-th="<?=__('Date')?>" class="min_width_140 nested-adj-large">
                        <div class="div-cell">
                            <div class="input-group">
                                <div class="input-group-addon small-input-group">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                                <input type="text" class="table_input disabled-input" data-date-format="DD/MM/YYYY" data-name="certification_%s_date" value="<?=date('d/m/Y')?>">
                            </div>
                        </div>

                    </td>
                    <td data-th="<?=__('File(s)')?>" class="min_width_140">
                        <div class="wrap_files">
                            <span  class="list-uploads disabled-gray-button">
                                <i class="q4bikon-file"></i>
                            </span>
                            <div class="file_container">
                                <a href="#" class="file-input standard-file load-file-form"><?=__('Load')?></a>
                                <input class="hidden input-file-form input-file-form" type="file" accept=".doc,.docx,.xls,.xlsx,.pdf,.ppg,.plt,.jpg,.jpe,.jpeg,.png,.gif,.tif,.tiff" multiple="multiple" name="files[%s][]"/>
                            </div>
                        </div>
                    </td>
                    <td class="nested-adj-large" data-th="<?=__('Approved by')?>">
                        <div class="div-cell"><?=__('New')?></div>
                    </td>
                </tr>
                <?if(!empty($certs)):?>
                    <?foreach ($certs as $item):?>
                        <tr>
                            <td data-th="<?=__('Craft')?>">

                            </td>
                            <td data-th="<?=__('Certification Name')?>">
                                <input type="text" class="table_input" name="certification_<?=$item->id?>_name" value="<?=$item->name?>">
                            </td>
                            <td data-th="<?=__('Date')?>">
                                <div class="div-cell">
                                    <div class="input-group">
                                        <div class="input-group-addon small-input-group">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </div>
                                        <input type="text" class="table_input disabled-input" data-date-format="DD/MM/YYYY" name="certification_<?=$item->id?>_date" value="<?=date('d/m/Y',$item->date)?>">
                                    </div>
                                </div>

                            </td>
                            <td data-th="<?=__('File(s)')?>">
                                <div class="wrap_files">
                                    <a href="#" class="list-uploads cert-files" <?if($item->files->where('status','=',Enum_FileStatus::Active)->count_all()):?> style="background-color: #f99c19;"<?endif?> data-url="<?=URL::site('projects/certification_files/'.$_PROJECT->id.'/'.$item->id)?>">
                                        <i class="q4bikon-file"></i>
                                    </a>
                                    <div class="file_container">
                                        <a href="#" class="file-input standard-file load-file-form"><?=__('Load')?></a>
                                        <input type="file" class="hidden input-file-form" accept=".doc,.docx,.xls,.xlsx,.pdf,.ppg,.plt,.jpg,.jpe,.jpeg,.png,.gif,.tif,.tiff" multiple="multiple" name="files[<?=$item->id?>][]"/>
                                    </div>
                                </div>
                            </td>
                            <td class="nested-adj-large" data-th="<?=__('Approved by')?>">
                                <div class="div-cell"><?=__($item->approval_status)?></div>
                            </td>
                        </tr>
                    <?endforeach?>
                <?endif?>
                <?if(!empty($crafts)):?>
                    <?foreach ($crafts as $craft):?>
                        <?$item = $craft->certifications->where('project_id','=',$_PROJECT->id)->find()?>
                        <?if($item->loaded()):?>
                            <tr>
                                <td data-th="<?=__('Craft')?>">
                                    <label class="div-cell"><?=$craft->name?></label>
                                    <input type="hidden" name="certification_<?=$item->id?>_craft_id" value="<?=$craft->id?>">
                                </td>
                                <td data-th="<?=__('Certification Name')?>">
                                    <input type="text" class="table_input" name="certification_<?=$item->id?>_name" value="<?=$item->name?>">
                                </td>
                                <td data-th="<?=__('Date')?>">
                                    <div class="div-cell">
                                        <div class="input-group">
                                            <div class="input-group-addon small-input-group">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </div>
                                            <input type="text" class="table_input disabled-input" data-date-format="DD/MM/YYYY" name="certification_<?=$item->id?>_date" value="<?=date('d/m/Y',$item->date)?>">
                                        </div>
                                    </div>

                                </td>
                                <td data-th="<?=__('File(s)')?>">
                                    <div class="wrap_files">
                                        <a href="#" class="list-uploads cert-files" <?if($item->files->where('status','=',Enum_FileStatus::Active)->count_all()):?> style="background-color: #f99c19;"<?endif?> data-url="<?=URL::site('projects/certification_files/'.$_PROJECT->id.'/'.$item->id)?>">
                                            <i class="q4bikon-file"></i>
                                        </a>
                                        <div class="file_container">
                                            <a href="#" class="file-input standard-file load-file-form"><?=__('Load')?></a>
                                            <input type="file" class="hidden input-file-form" accept=".doc,.docx,.xls,.xlsx,.pdf,.ppg,.plt,.jpg,.jpe,.jpeg,.png,.gif,.tif,.tiff" multiple="multiple" name="files[<?=$item->id?>][]"/>
                                        </div>
                                    </div>
                                </td>
                                <td data-th="<?=__('Approved by')?>">
                                    <div class="div-cell"><?=__($item->approval_status)?></div>
                                </td>
                            </tr>
                            <?else:?>
                            <?$id = uniqid("+");?>
                                <tr>
                                    <td data-th="<?=__('Craft')?>" class="cert-craft">
                                        <label class="div-cell"><?=$craft->name?></label>
                                        <input type="hidden" name="certification_<?=$id?>_craft_id" value="<?=$craft->id?>">
                                    </td>
                                    <td data-th="<?=__('Certification Name')?>">
                                        <input type="text" class="table_input" placeholder="<?=__('Certification Name')?>" name="certification_<?=$id?>_name">
                                    </td>
                                    <td data-th="<?=__('Date')?>">
                                        <div class="div-cell">
                                            <div class="input-group">
                                                <div class="input-group-addon small-input-group">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </div>
                                                <input type="text" class="table_input disabled-input" value="<?=date('d/m/Y')?>" data-date-format="DD/MM/YYYY" name="certification_<?=$id?>_date">
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="<?=__('File(s)')?>">
                                        <div class="wrap_files">
                                            <span  class="list-uploads disabled-gray-button">
                                                <i class="q4bikon-file"></i>
                                            </span>
                                            <div class="file_container">
                                                <a href="#" class="file-input standard-file load-file-form"><?=__('Load')?></a>
                                                <input class="hidden input-file-form" type="file" accept=".doc,.docx,.xls,.xlsx,.pdf,.ppg,.plt,.jpg,.jpe,.jpeg,.png,.gif,.tif,.tiff" multiple="multiple" name="files[<?=$id?>][]"/>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="<?=__('Approved by')?>">
                                        <div class="div-cell">-</div>
                                    </td>
                                </tr>
                            <?endif?>
                    <?endforeach?>
                <?endif?>
                </tbody>
            </table>

        </div>
    </div>
    <div class="panel_footer text-align">
        <div class="row">
            <div class="col-lg-12 col-sm-12">
                <a href="#" class="inline_block_btn orange_button q4_form_submit"><?=__('Update')?></a>
            </div>
        </div>
    </div>
    <input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">
</form>
