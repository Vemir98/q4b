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
        <!--mobile layout-->
        <div class="mobile-layout">
            <!--mobile filter-->
            <div class="q4-inside-filter-mobile">
                <?$statusProjectArrray = [
                    'q4bikon-company_status' => 'All',
                    'symbol-active' => 'Active',
                    'symbol-inactive' => 'Inactive',
                    'symbol-archive' => 'Archive'
                ];
                ?>
                <?$currentStatus = Request::current()->param('status') ? Request::current()->param('status') : 'all'?>
                <?$currentIcon = Request::current()->param('status') ? "symbol-".Request::current()->param('status') : 'q4bikon-company_status'?>
                <?$currUrl = $currentStatus == 'all' ?  '' : strtolower($currentStatus);?>
                <!-- <div class="filter-status-text"><?=__('Filter by')?> <?=__('status')?>:</div>
                <div class="relative">
                    <a  class="q4-inside-select-filter">
                        <span class="status <?=$currentIcon?>"></span>
                        <span class="filter-button-text"><?=__($currentStatus)?> </span>
                    </a>
                    <ul class="inside-filters-list-mobile">
                        <?foreach ($statusProjectArrray as $class => $status):?>
                            <?$url = $status == 'All' ? '': strtolower($status) ?>
                            <li >
                                <a href="<?=Route::url('site.companiesWithFilters',array_diff(Arr::merge(Request::current()->param(),['status' => $url]),array('')))?>"  class="inside-filter-button-mobile filter-settings-button active">
                                    <span class="status <?=$class?>"></span>
                                    <span class="filter-button-text"><?=__(strtolower($status))?></span>
                                </a>
                            </li>
                        <?endforeach;?>
                    </ul>
                </div> -->
                <?if(count($filterCompanies)):?>
                    <span class="filter-status-text"><?=__('Filter by name')?>:</span>
                    <div class="relative">
                        <div class="select-wrapper" >

                            <i class="q4bikon-arrow_bottom"></i>
                            <select class="q4-select q4-form-input" onchange="document.location=this.options[this.selectedIndex].value">
                                <option value=""><?=__('Please select')?></option>
                                <?foreach ($filterCompanies as $item): ?>
                                    <option value="<?=URL::site('companies/update/'.$item->id)?>"><?=$item->name?></option>
                                <?endforeach ?>
                            </select>
                    </div>
                <?endif?>
            </div>

            </div>
           <!--  <?$currentSorting = Request::current()->param('sorting') ? __('company '.Request::current()->param('sorting')) : __('company name')?>
            <div class="q4-inside-filter-mobile"> -->


                <!-- <div class="filter-status-text"><?=__('Filter by')?> <?=__('status')?>:</div>
                <div class="relative">
                    <a class="q4-inside-select-filter">

                        <span class="filter-button-text"><?=__($currentSorting)?> </span>
                    </a>
                    <ul class="inside-filters-list-mobile">

                            <li >
                                <a href="<?=Route::url('site.companiesWithFilters',array_diff(Arr::merge(Request::current()->param(),['sorting' => 'name']),array('')))?>"  class="inside-filter-button-mobile filter-settings-button active">

                                    <span class="filter-button-text"><?=__('company name')?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?=Route::url('site.companiesWithFilters',array_diff(Arr::merge(Request::current()->param(),['sorting' => 'type']),array('')))?>"  class="inside-filter-button-mobile filter-settings-button active">

                                    <span class="filter-button-text"><?=__('company type')?></span>
                                </a>
                            </li>
                    </ul>
                </div> -->
            <!-- </div> -->

            <!--Mobile Version -->

            <div class="q4-wrap-mobile">
                <div data-structurecount="<?=count($items)?>" class="q4-list-items-mobile q4-owl-carousel">
                    <?if(!empty($items)):?>
                        <?foreach ($items as $item):?>
                            <div class="item">

                                <figure class="mobile-figure">
                                    <a href="<?=URL::site('companies/update/' . $item->id)?>">
                                        <img src="/<?=$item->logo?>" alt="<?=$item->name?>" class="content_companies_list_logo"></a>
                                    <figcaption class="mobile-fig-caption"><a href="<?=URL::site('companies/update/' . $item->id)?>"><?=$item->name?></a></figcaption>
                                </figure>
                                <span class="q4-list-item-mobile projects title"><a href="<?=URL::site('projects/company/'.$item->id)?>"><?=__('Projects')?>: <?=$item->projects->count_all()?></a></span>
                                <span class="q4-list-item-mobile country"><?=__('Country')?> : <?=__($item->country->name)?></span>
                                <span class="q4-list-item-mobile"><?=__('Status')?> : <?=__(strtolower($item->status))?>
                                </span>
                                <div class="q4-list-item-mobile-desc">
                                    <p><?=$item->description?></p>
                                </div>
                            </div>
                        <?endforeach;?>
                    <?endif?>
                </div>
                <?=$pagination?>

            </div>
        </div>

    </section>
