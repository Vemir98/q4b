<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.12.2016
 * Time: 5:44
 */

?>
    <!--company list-->
    <section class="content_companies">
        <div class="desktop-layout">
            <div class="content_companies_filter q4-page-filter">
                <ul class="filter-buttons-list">
                    <!-- <li>
                        <div class="filter-text"><?=__('Filter by')?> <?=__('status')?>:</div>
                        <a href="<?=Route::url('site.companiesWithFilters',array_diff(Arr::merge(Request::current()->param(),['status' => null]),array('')))?>" class="filter-button<?=(Request::current()->param('status') == '') ? ' current' : ''?>">
                            <i class="status symbol-all"></i>
                            <span class="filter-button-text"><?=__('All')?></span>
                        </a>
                    </li>
                    <?foreach (Enum_CompanyStatus::toArray() as $status):?>
                        <li>
                            <a href="<?=Route::url('site.companiesWithFilters',array_diff(Arr::merge(Request::current()->param(),['status' => $status]),array('')))?>" class="filter-button<?=(Request::current()->param('status') === $status) ? ' current' : ''?>">
                                <span class="status symbol-<?=$status?>"></span>
                                <span class="filter-button-text"><?=__($status)?></span>
                            </a>
                        </li>
                    <?endforeach;?> -->
                    <?if(count($filterCompanies)):?>
                        <li>
                            <span class="filter-text"><?=__('Filter by name')?>:</span>
                            <div class="select-wrapper" >

                                <i class="q4bikon-arrow_bottom"></i>
                                <select class="q4-select q4-form-input" onchange="document.location=this.options[this.selectedIndex].value">
                                    <option value=""><?=__('Please select')?></option>
                                    <?foreach ($filterCompanies as $item): ?>
                                        <option value="<?=URL::site('companies/update/'.$item->id.'?tab=info')?>"><?=$item->name?></option>
                                    <?endforeach ?>
                                </select>
                            </div>
                        </li>
                    <?endif?>
                    <!-- <li>
                        <span class="filter-text"><?=__('Sort By')?>:</span>
                        <span class="select-wrapper">
                            <i class="q4bikon-arrow_bottom"></i>
                            <select id="sort-by-company-name" class="sort-by-option q4-select q4-form-input" onchange="document.location=this.options[this.selectedIndex].value">
                                <option value="<?=Route::url('site.companiesWithFilters',array_diff(Arr::merge(Request::current()->param(),['sorting' => null]),array('')))?>"><?=__('')?></option>
                                <option value="<?=Route::url('site.companiesWithFilters',array_diff(Arr::merge(Request::current()->param(),['sorting' => 'name']),array('')))?>"<?=(Request::current()->param('sorting') === 'name') ? ' selected="selected"' : ''?>><?=__('company name')?></option>
                                <option value="<?=Route::url('site.companiesWithFilters',array_diff(Arr::merge(Request::current()->param(),['sorting' => 'type']),array('')))?>"<?=(Request::current()->param('sorting') === 'type') ? ' selected="selected"' : ''?>><?=__('company type')?></option>
                            </select>
                        </span>
                        <?if(rand(0,99) > 23) Security::mousetrapRandLink()?>
                    </li> -->
                </ul>
                <a href="<?=Route::url('site.companiesWithFilters',array_diff(Arr::merge(Request::current()->param(),['export' => 'excel']),array('')))?>" class="content_companies_export q4-page-export">
                    <i class="q4bikon-export"></i>
                    <span class="q4-page-export-text"><?=__('Export')?></span>
                </a>
                <div class="clear"></div>
            </div>

            <div class="q4-list-items-result">
                <?=__('showing')?> <span class="showing"><?=count($items)?></span> <?=__('results from')?> <span class="results_total"><?=$total_items?></span>
            </div>

            <div class="content_companies_list">
                <?if(!empty($items)):?>
                <ul class="q4-list-items">
                    <?foreach ($items as $item):?>
                    <li class="q4-list-item">
                        <figure>
                            <a href="<?=URL::site('companies/update/' . $item->id. '?tab=info')?>"><img src="/<?=$item->logo?>" alt="<?=$item->name?>" class="content_companies_list_logo"></a>
                            <figcaption><a href="<?=URL::site('companies/update/' . $item->id. '?tab=info')?>"><?=$item->name?></a></figcaption>
                        </figure>
                        <?if($item->projects->count_all()):?>
<!--                            <span class="q4-list-item-info projects"><a href="--><?//=URL::site('projects/company/'.$item->id)?><!--">--><?//=__('Projects')?><!--: --><?//=$item->projects->count_all()?><!--</a></span>-->
                            <span class="q4-list-item-info projects"><a href="<?=URL::site('companies/'.$item->id.'/projects/list')?>"><?=__('Projects')?>: <?=$item->projects->count_all()?></a></span>
                        <?else:?>
                            <span class="q4-list-item-info projects"><?=__('Projects')?>: <?=$item->projects->count_all()?></span>
                        <?endif?>
                        <span class="q4-list-item-info country"><?=__('Country')?>: <?=__($item->country->name)?></span>
                        <span class="q4-list-item-info"><?=__('Status')?>: <span class="status <?=$item->status?>"></span><?=__($item->status)?></span>
                        <span class="q4-list-item-info"> <?=__('company type')?>: <span class="company-list-item-type"><?=__($item->client->type)?></span></span>

                        <div class="q4-list-item-desc">
                            <p>
                                <?=$item->description?>
                            </p>
                        </div>
                    </li>
                    <?endforeach?>
                </ul>
                <?endif?>
                <?=$pagination?>
            </div>
        </div>
    </section>
