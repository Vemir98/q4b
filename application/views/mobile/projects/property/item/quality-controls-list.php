
    <?defined('SYSPATH') OR die('No direct script access.');?>
    <div class="modal fade qc-list-modal" data-backdrop="static" data-keyboard="false" id="element-qc-forms" tabindex="-1" role="dialog" aria-labelledby="element-qc-forms">

    <?
        $statusArray = [
         "All" => "symbol-all",
         "waiting" => "symbol-active",
         "for_repair" => "symbol-inactive",
         "approved" => "symbol-archive",
        ];
    ?>
        <div class="modal-dialog q4_project_modal modal-report-craft-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                        <div class="clear"></div>
                    </div>
                    <div class="q4_modal_sub_header">
                        <h3><?=__('Quality control list')?></h3>
                    </div>
                </div>
            <form action="/" class="q4_form" autocomplete="off">
                <div class="panel_body container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="add-new-right mobile">
                                <a class="orange_plus_small create-quality-control" data-url="<?=URL::site('projects/quality_control/'.$items[0]->place->id)?>"  title="Add QC">
                                    <i class="plus q4bikon-plus"></i>
                                </a>
                            </div>
                            <div class="q4-inside-filter-mobile">
                                <?if(!empty($filterData['statuses'])):?>
                                    <div class="filter-status-text"><?=__('Filter by')?> <?=__('status')?>:</div>
                                    <div class="relative">
                                        <a class="q4-inside-select-filter">
                                            <span class="status symbol-active"></span>
                                            <span class="filter-button-text"><?=__('All')?> <span class="filter-button-numb">(<?=$filterData['statuses']['all']['count']?>)&#x200E;</span></span>
                                        </a>
                                        <ul class="inside-filters-list-mobile">
                                            <?foreach ($filterData['statuses'] as $class => $status):?>
                                                <?$url = $status == 'All' ? '': strtolower($class)
                                                ?>
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
                    <?if(count($items)>0):?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="q4-carousel-table-wrap">
                                <div class="q4-carousel-table" data-structurecount="<?=count($items)?>">
                                    <?foreach ($items as $i):?>
                                        <div class="item">
                                            <div class="q4-carousel-blue-head reports-prop-title">
                                                <span class="blue-head-title"><?=__('Quality control').' #'.$i->id?></span>
                                                <div class="blue-head-option project-props-qc">
                                                    <a class="show-structure-mobile" href="#" data-qc="quality-control" data-url="<?=URL::site('reports/quality_control/'.$i->id)?>">
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
                                                    <?=__('Property')?>
                                                </div>
                                                <div class="q4-mobile-table-value">
                                                    <?=__($i->object->type->name)?> - <?=$i->object->name?>
                                                </div>
                                            </div>
                                            <div class="q4-carousel-row f0">
                                                <div class="q4-mobile-table-key">
                                                    <?=__('Floor')?>
                                                </div>
                                                <div class="q4-mobile-table-value">
                                                   <span class="bidi-override"><?=$i->floor->number?>&#x200E;</span>
                                                </div>
                                            </div>
                                            <div class="q4-carousel-row f0">
                                                <div class="q4-mobile-table-key">
                                                    <?=__('Element')?>
                                                </div>
                                                <div class="q4-mobile-table-value">
                                                    <?=__($i->place->name)?>
                                                </div>
                                            </div>
                                            <div class="q4-carousel-row f0">
                                                <div class="q4-mobile-table-key">
                                                    <?=__('Number')?>
                                                </div>
                                                <div class="q4-mobile-table-value">
                                                    <span class="bidi-override"><?=$i->place->number?></span>
                                                </div>
                                            </div>
                                            <div class="q4-carousel-row f0">
                                                <div class="q4-mobile-table-key">
                                                   <?=__('Crafts')?>
                                                </div>
                                                <div class="q4-mobile-table-value">
                                                    <?=__($i->craft->name)?>
                                                </div>
                                            </div>
                                            <div class="q4-carousel-row f0">
                                                <div class="q4-mobile-table-key">
                                                    <?=__('Status')?>
                                                </div>
                                                <div class="q4-mobile-table-value">
                                                    <span class="q4-status-<?=$i->approval_status?>"><?=__($i->approval_status)?></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?endforeach;?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?else:?>
                        <h5 class="no-records-found"><?=__('Not found')?></h5>
                    <?endif;?>

                </div><!--.panel-body-->
            </form>
        </div><!--panel_content-->
    </div>
</div>










