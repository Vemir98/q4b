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
?>

<div class="panel_header">
    <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Certifications')?></h2>
</div>

<div class="panel_content">
    <form action="/" class="q4_form" autocomplete="off">
        <input type="hidden" value="" name="x-form-secure-tkn"/>
        <div class="panel_body container-fluid">
            <div class="row">
                <div class="col-lg-12">
<!--                    <div class="add-new-row-double">-->
                        <!-- <div class="desktop-layout"> -->
                        <!--<div class="q4-inside-filter">
                                <?if(!empty($data['statuses'])):?>
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
                            </div>-->
                        <!-- </div> -->
                        <!-- <div class="mobile-layout"> -->
                        
                        <!-- </div> -->

<!--                    </div>-->

                    <div class="q4-inside-filter-mobile">
                        <?if(!empty($data['statuses'])):?>
                            <div class="filter-status-text"><?=__('Filter by')?> <?=__('status')?>:</div>
                            <div class="relative">
                                <a class="q4-inside-select-filter">
                                    <span class="status symbol-active"></span>
                                    <span class="filter-button-text"><?=__('Waiting')?> <span class="filter-button-numb">(<?=$data['statuses']['Waiting']['count']?>)&#x200E;</span></span>
                                </a>
                                <ul class="inside-filters-list-mobile">
                                    <?foreach ($data['statuses'] as $class => $status):?>
                                        <?$url = $status == 'All' ? '': strtolower($class);?>
                                        <li>
                                            <a href="#" data-url="<?=$status['url']?>" data-status="<?=$status['text']?>" class="inside-filter-button-mobile filter-settings-button active">
                                                <span class="status <?=$statusArray[$status['text']]?>"></span>
                                                <span class="filter-button-text"><?=__(strtolower($status['text']))?><span class="filter-button-numb">(<?=$status['count']?>)&#x200E;</span></span>
                                            </a>
                                        </li>
                                    <?endforeach;?>
                                </ul>
                            </div>
                        <?endif;?>
                    </div>
                </div>
            </div>
            <div class="row">
                <?if($data['total_items']):?>
                    <div class="col-lg-12">
                        <div class="q4-carousel-table-wrap">

                            <div class="q4-carousel-table" data-structurecount="<?=count($data['items'])?>">
                            <?foreach ($data['items'] as $i):?>
                                <div class="item">
                                     <div class="q4-carousel-blue-head reports-prop-title">
                                         <span class="blue-head-title"><?=$i->name?></span>
                                         <div class="blue-head-option">
                                             <a class="show-structure-mobile" href="#" data-url="<?=URL::site('projects/certification_files/'.$i->project_id.'/'.$i->id)?>">
                                                 <i class="plus q4bikon-preview"></i>
                                             </a>
                                         </div>
                                    </div>
                                    <div class="q4-carousel-row f0">
                                        <div class="q4-mobile-table-key">
                                           <?=__('ID')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <?=$i->id?>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0" >
                                        <div class="q4-mobile-table-key">
                                            <?=__('Craft')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <?=empty($i->craft)? '' : __($i->craft->name)?>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0">
                                        <div class="q4-mobile-table-key">
                                           <?=__('Date')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                           <?=date('d/m/Y', $i->date)?>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0">
                                        <div class="q4-mobile-table-key">
                                            <?=__('File')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <?$file = $i->files->order_by('created_at', 'DESC')->find()?>
                                            <div class="div-cell">
                                                <?if($file):?>
                                                    <a href="<?=URL::withLang($file->originalFilePath(),Language::getDefault()->iso2,'https')?>" target="_blank" class="c-file"><img src="/media/img/choose-format/format-<?=$file->ext?>.png" title="<?=$file->original_name?>" alt="<?=$file->original_name?>"></a>
                                                    <?else:?>
                                                    <span >__('')</span>
                                                <?endif;?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0">
                                        <div class="q4-mobile-table-key">
                                            <?=__('Approvement Status')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                                <select data-url="<?=URL::site('dashboard/approve_certification/'.$i->id)?>"" name="status" class="q4-select q4-form-input q4-status-<?=$i->approval_status?> <?=$i->approval_status=='approved' ? 'disabled-input' : ''?>" >
                                                    <?foreach (Enum_CertificationsApprovalStatus::toArray() as $status) :?>

                                                        <?$selected = $i->approval_status == $status ? "selected='selected'" : ""; ?>
                                                           <option class="q4-status-<?=$status?>" <?=$selected?> value="<?=$status?>"><?=__($status)?></option>

                                                    <?endforeach;?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?endforeach;?>
                        </div>

                    </div><!--.q4-carousel-table-wrap--->
                    <?=$data['pagination']?>
                </div>
                <?else:?>
                    <h5 class="no-records-found"><?=__('Not found')?></h5>
                <?endif?>
            </div>

        </div><!--.panel-body-->

    </form>
</div><!--panel_content-->
