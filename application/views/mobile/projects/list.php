<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:14
 */

?>


<section class="content-projects">
    <div class="mobile-layout">
        <!--mobile filter-->


        <div class="q4-inside-filter-mobile">
            <?$statusProjectArrray = [
                'q4bikon-company_status' => 'All',
                'symbol-active' => 'Active',
                'symbol-suspended' => 'Suspended',
                'symbol-archive' => 'Archive'
            ];
            ?>
            <?$currentStatus = Request::current()->param('status') ? Request::current()->param('status') : 'all'?>
            <?$currentIcon = Request::current()->param('status') ? "symbol-".Request::current()->param('status') : 'q4bikon-company_status'?>
            <?$currUrl = $currentStatus == 'all' ?  '' : strtolower($currentStatus);?>
       <!--      <div class="filter-status-text"><?=__('Filter by')?> <?=__('status')?>:</div>
            <div class="relative">
                <a  class="q4-inside-select-filter">
                    <span class="status <?=$currentIcon?>"></span>
                    <span class="filter-button-text"><?=__($currentStatus)?> </span>
                </a>
                <ul class="inside-filters-list-mobile">
                    <?foreach ($statusProjectArrray as $class => $status):?>
                        <?$url = $status == 'All' ? '': strtolower($status) ?>
                        <li>
                            <a href="<?=Route::url('site.projectsWithFilters',array_diff(Arr::merge(Request::current()->param(),['status' => $url]),array('')))?>"  class="inside-filter-button-mobile  active">
                                <span class="status <?=$class?>"></span>
                                <span class="filter-button-text"><?=__(strtolower($status))?></span>
                            </a>
                        </li>
                    <?endforeach;?>
                </ul>
            </div> -->

            <?if(count($filterProjects)):?>
                <span class="filter-status-text"><?=__('Filter by name')?>:</span>
                <div class="relative">
                    <div class="select-wrapper" >

                        <i class="q4bikon-arrow_bottom"></i>
                        <select class="q4-select q4-form-input" onchange="document.location=this.options[this.selectedIndex].value">
                            <option value=""><?=__('Please select')?></option>
                            <?foreach ($filterProjects as $item): ?>
                                <option value="<?=URL::site('projects/update/'.$item->id)?>"><?=$item->name?></option>
                            <?endforeach ?>
                        </select>
                    </div>
                    </div>
            <?endif?>

        </div>


        <!-- <?$currentSorting = Request::current()->param('sorting') ? __('project '.Request::current()->param('sorting')) : __('project name')?>
        <div class="q4-inside-filter-mobile ">
            <div class="filter-status-text"><?=__('Sort by')?>:</div>
            <div class="relative">
                <a class="q4-inside-select-filter">
                    <span class="filter-button-text"><?=__($currentSorting)?> </span>
                </a>
                <ul class="inside-filters-list-mobile">
                    <li >
                        <a href="<?=Route::url('site.projectsWithFilters',array_diff(Arr::merge(Request::current()->param(),['sorting' => 'name']),array('')))?>"  class="inside-filter-button-mobile  active">
                            <span class="filter-button-text"><?=__('project name')?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?=Route::url('site.projectsWithFilters',array_diff(Arr::merge(Request::current()->param(),['sorting' => 'status']),array('')))?>"  class="inside-filter-button-mobile  active">
                            <span class="filter-button-text"><?=__('project status')?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div> -->
        <!--Mobile Version -->

        <div class="q4-wrap-mobile">
            <div data-structurecount="<?=count($items)?>" class="q4-list-items-mobile q4-owl-carousel">
                <?if(!empty($items)):?>
                    <?foreach($items as $i):?>
                        <div class="item">
                            <?$countNew = $i->quality_controls->where('created_at','>',strtotime(date('d-m-Y H:i:s'))-86400)->count_all();?>
                            <?if($countNew > 0):?>
                                <span class="q4-list-item-st"><?=__('New QC')?> (<span class="q4-list-item-st-number"><?=$countNew?></span>)</span>
                            <?endif;?>
                            <figure class="mobile-figure">
                                 <a href="<?=URL::site('projects/company_project_update/'.$i->id)?>">
                                    <img src="<?=$i->main_image->originalFilePath()?>" alt="projects logo">
                                </a>
                                <figcaption class="mobile-fig-caption">
                                    <a href="<?=URL::site('projects/company_project_update/'.$i->id)?>"><?=$i->name?></a>
                                </figcaption>
                            </figure>
                            
                            <span class="q4-list-item-mobile projects title"><?=__('Company')?>:
                                <span class="project-list-c-name"><?=$i->company->name?></span>
                            </span>
                            <span class="q4-list-item-mobile country"><?=__('Start Date')?>:
                                <span class="project-list-date"><?=date('d/m/Y',$i->start_date)?></span>
                            </span>
                            <span class="q4-list-item-mobile country"><?=__('End Date')?>:
                                <span class="project-list-date"><?=date('d/m/Y',$i->end_date)?></span>
                            </span>
                            <span class="q4-list-item-mobile"><?=__('Status')?> : <?=__(strtolower($i->status))?>
                                </span>
                            <div class="q4-list-item-mobile-desc">
                             <p><?=$i->description?></p>
                            </div>
                       </div>
                   <?endforeach;?>
               <?endif?>
            </div>
            <?=$pagination?>
         </div>
    </div>
</section>
