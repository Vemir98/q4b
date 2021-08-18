<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 31.05.2017
 * Time: 15:31
 */
$statusArray = [
     "All" => "symbol-all",
     "waiting" => "symbol-active",
     "approved" => "symbol-archive",
];
$isSuperAdmin = $_USER->is('super_admin');
?>

<div class="panel_header">
    <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Certifications')?></h2>
</div>

<div class="panel_content">
    <form action="/" class="q4_form" autocomplete="off">
        <input type="hidden" value="" name="x-form-secure-tkn"/>
        <div class="panel_body container-fluid">
            <div class="row">
                <div class="col-lg-12 rtl-float-right">
                    <div class="add-new-row-double">
                        <div class="q4-inside-filter">
                            <?if(!empty($data['statuses'])):?>
                                <?
                                // echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($data['statuses']); echo "</pre>"; exit; ?>
                                <div class="filter-status-text""><?=__('Filter by')?> <?=__('status')?>:</div>
                                <ul class="inside-filters-list">
                                     <?foreach ($data['statuses'] as $key => $status):?>
                                        <li>
                                            <?$active = $status['text'] == 'waiting' ? ' active': ''?>
                                            <a href="#"  data-url="<?=$status['url']?>" data-status="<?=$status['text']?>" class="inside-filter-button filter-settings-button<?=$active?>">
                                                <span class="<?=$statusArray[$status['text']]?> status"></span>
                                                <span class="filter-button-text"><?=__(strtolower($status['text']))?>
                                                </span>
                                                <span class="filter-button-numb">(<?=$status['count']?>)&#x200E;</span>
                                            </a>
                                        </li>
                                    <?endforeach?>

                                </ul>
                            <?endif?>

                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <?if($data['total_items']):?>
                    <div class="col-lg-12">
                        <div class="scrollable-table">
                            <table class="rwd-table responsive_table table" data-toggle="table">
                                <thead>
                                <tr>
                                    <th data-field="ID"  class="td-25"><?=__('ID')?></th>
                                    <th data-field="Certification Name"  class="td-cell-180"><?=__('Certification Name')?></th>
                                    <th data-field="Craft" ><?=__('Craft')?></th>
                                    <th data-field="Date"  class="td-150"><?=__('Date')?></th>
                                    <th data-field="File" class="td-200"><?=__('File')?></th>
                                    <th data-field="Approvement Status"  class="td-200"><?=__('Approvement Status')?></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?foreach ($data['items'] as $i):?>
                                        <tr>
                                            <td class="rwd-td0" data-th="<?=__('ID')?>">
                                                <div class="div-cell"><?=$i->id?></div>
                                            </td>
                                            <td class="rwd-td1" data-th="<?=__('Certification Name')?>">
                                                <div class="div-cell reports-prop-title">
                                                    <a  data-modalid="modal_file_upload" data-url="<?=URL::site('projects/certification_files/'.$i->project_id.'/'.$i->id)?>" href="#" ><?=$i->name?></a>
                                                </div>
                                            </td>
                                            <td class="rwd-td2" data-th="<?=__('Craft')?>">
                                                <div class="div-cell"><?=empty($i->craft)? '' : __($i->craft->name)?></div>
                                            </td>
                                            <td class="rwd-td3" data-th="<?=__('Date')?>">
                                                <div class="div-cell"><?=date('d/m/Y', $i->date)?></div>
                                            </td>
                                            <td class="rwd-td4" data-th="<?=__('File')?>">
                                                <?$file = $i->files->order_by('created_at', 'DESC')->find()?>
                                                <div class="div-cell">
                                                    <?if($file):?>
                                                        <a href="<?=URL::withLang($file->originalFilePath(),Language::getDefault()->iso2,'https')?>" target="_blank" class="c-file"><img src="/media/img/choose-format/format-<?=$file->ext?>.png" title="<?=$file->original_name?>" alt="<?=$file->original_name?>"></a>
                                                        <?else:?>
                                                        <span >__('')</span>
                                                    <?endif;?>
                                                </div>
                                            </td>
                                            <td class="rwd-td11" data-th="<?=__('Approvement Status')?>">
                                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <select data-url="<?=URL::site('dashboard/approve_certification/'.$i->id)?>"" name="status" class="q4-select q4-form-input q4-status-<?=$i->approval_status?> <?=$i->approval_status=='approved' && !$isSuperAdmin ? 'disabled-input' : ''?>" >
                                                    <?foreach (Enum_ApprovalStatus::toArray() as $status) :?>

                                                        <?$selected = $i->approval_status == $status ? "selected='selected'" : ""; ?>
                                                           <option class="q4-status-<?=$status?>" <?=$selected?> value="<?=$status?>"><?=__($status)?></option>

                                                    <?endforeach;?>
                                                </select>
                                                </div>
                                            </td>
                                        </tr>
                                    <?endforeach;?>
                                </tbody>
                            </table>
                        </div><!--Scrollable-table-->

                    <?=$data['pagination']?>
                </div>
                <?else:?>
                    <h5 class="no-records-found"><?=__('Not found')?></h5>
                <?endif?>
            </div>

        </div><!--.panel-body-->

    </form>
</div><!--panel_content-->
