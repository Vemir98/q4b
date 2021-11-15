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
    <div class="desktop-layout">
        <div class="content-projects-filter q4-page-filter">
            <ul class="filter-buttons-list">
                <!--  <li>
                    <div class="filter-text"><?=__('Filter by')?> <?=__('status')?>:</div>
                    <a href="<?=Route::url('site.projectsWithFilters',array_diff(Arr::merge(Request::current()->param(),['status' => null]),array('')))?>" class="filter-button<?=(Request::current()->param('status') == '') ? ' current' : ''?>">
                        <i class="q4bikon-company_status"></i>
                        <span class="filter-button-text"><?=__('All')?></span>
                    </a>
                </li>
                <?foreach (Enum_ProjectStatus::toArray() as $status):?>
                    <li>
                        <a href="<?=Route::url('site.projectsWithFilters',array_diff(Arr::merge(Request::current()->param(),['status' => $status]),array('')))?>" class="filter-button<?=(Request::current()->param('status') === $status) ? ' current' : ''?>">
                            <span class="status symbol-<?=$status?> <?=($status == 'suspended') ? 'inactive' : ''?>"></span>
                            <span class="filter-button-text"><?=__($status)?></span>
                        </a>
                    </li>
                <?endforeach;?> -->
                <?if(count($filterProjects)):?>
                    <li>

                        <span class="filter-text"><?=__('Filter by name')?>:</span>
                        <div class="select-wrapper" >

                            <i class="q4bikon-arrow_bottom"></i>
                            <select class="q4-select q4-form-input" onchange="document.location=this.options[this.selectedIndex].value">
                                <option value=""><?=__('Please select')?></option>
                                <?foreach ($filterProjects as $item): ?>
                                    <option value="<?=URL::site('projects/update/'.$item->id)?>"><?=$item->name?></option>
                                <?endforeach ?>
                            </select>
                        </div>
                    </li>
                <?endif?>
                <!-- <li>
                    <span class="filter-text"><?=__('Sort By')?>:</span>
                    <span class="select-wrapper">
                        <i class="q4bikon-arrow_bottom"></i>
                        <select id="sort-by-project-name" class="q4-select q4-form-input sort-by-option" onchange="document.location=this.options[this.selectedIndex].value">
                            <option value="<?=Route::url('site.projectsWithFilters',array_diff(Arr::merge(Request::current()->param(),['sorting' => null]),array('')))?>"><?=__('')?></option>
                            <option value="<?=Route::url('site.projectsWithFilters',array_diff(Arr::merge(Request::current()->param(),['sorting' => 'name']),array('')))?>"<?=(Request::current()->param('sorting') === 'name') ? ' selected="selected"' : ''?>><?=__('project name')?></option>
                            <option value="<?=Route::url('site.projectsWithFilters',array_diff(Arr::merge(Request::current()->param(),['sorting' => 'status']),array('')))?>"<?=(Request::current()->param('sorting') === 'status') ? ' selected="selected"' : ''?>><?=__('project status')?></option>
                        </select>
                    </span>
                </li> -->
            </ul>
            <a href="<?=Route::url('site.projectsWithFilters',array_diff(Arr::merge(Request::current()->param(),['export' => 'excel']),array('')))?>" class="content-projects-export q4-page-export">
                <i class="q4bikon-export icon-export"></i>
                <span class="q4-page-export-text"><?=__('Export')?></span>
            </a>
            <div class="clear"></div>
        </div>


        <div class="q4-list-items-result">
            <?=__('showing')?> <span class="showing"><?=count($items)?></span> <?=__('results from')?> <span class="results_total"><?=$total_items?></span>
        </div>

        <div class="content-projects-list">

            <ul class="q4-list-items">
                <?foreach($items as $i):?>
                    <li class="q4-list-item">
                        <?$countNew = $i->quality_controls->where('created_at','>',strtotime(date('d-m-Y H:i:s'))-86400)->count_all();?>
                        <?if($countNew > 0):?>
                            <span class="q4-list-item-st"><?=__('New QC')?> (<span class="q4-list-item-st-number"><?=$countNew?></span>)</span>
                        <?endif;?>
                        <figure>
                            <?if(Request::current()->action() == 'company'):?>
                                <?if($i->image_id):?>
                                    <a href="<?=URL::site('projects/update/'.$i->id. '?tab=info')?>"><img src="<?=$i->main_image->originalFilePath()?>" alt="<?=$i->name?>"></a>
                                <?else:?>
                                    <a href="<?=URL::site('projects/update/'.$i->id. '?tab=info')?>"><img src="/media/img/camera.png" alt="<?=$i->name?>"></a>
                                <?endif?>
                                <figcaption><a href="<?=URL::site('projects/update/'.$i->id. '?tab=info')?>"><?=$i->name?></a></figcaption>
                            <?else:?>
                                <?if($i->image_id):?>
                                    <a href="<?=URL::site('projects/update/'.$i->id)?>"><img src="<?=$i->main_image->originalFilePath()?>" alt="<?=$i->name?>"></a>
                                <?else:?>
                                    <a href="<?=URL::site('projects/update/'.$i->id)?>"><img src="/media/img/camera.png" alt="<?=$i->name?>"></a>
                                <?endif?>
                                <figcaption><a href="<?=URL::site('projects/update/'.$i->id)?>"><?=$i->name?></a></figcaption>
                            <?endif;?>
                        </figure>


                        <div class="wrap-projects-list-items">
                            <span class="q4-list-item-info title"><?=__('Company')?>: <span class="project-list-c-name"><?=$i->company->name?></span></span>
                            <span class="q4-list-item-info"><?=__('Start Date')?>: <span class="project-list-date"><?=date('d/m/Y',$i->start_date)?></span></span>
                            <span class="q4-list-item-info"><?=__('End Date')?>: <span class="project-list-date"><?=date('d/m/Y',$i->end_date)?></span></span>
                            <span class="q4-list-item-info"><?=__('Status')?>: <span class="status inactive"></span> <?=__($i->status)?></span>
                        </div>
                        <div class="q4-list-item-desc">
                            <p>
                                <?=$i->description?>
                            </p>
                        </div>

                    </li>
                <?endforeach?>
            </ul>

            <?=$pagination?>

        </div>
    </div>

</section>
