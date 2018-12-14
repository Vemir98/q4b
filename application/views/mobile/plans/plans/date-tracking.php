<?defined('SYSPATH') OR die('No direct script access.');?>
<div class="plans-tracking-layout hidden">

    <form action="/" class="q4_form" autocomplete="off">
        <div class="panel_body container-fluid plans-layout">
            <div class="row">
                <div class="col-md-12">
                    <div class="back-plans-list-layout">
                        <span class="back-plans-list-layout-link">
                            <i class="q4bikon-arrow_left"></i>
                            <a class="back-plans-list-layout-link"><?=__('Back to list of plans')?></a>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="dt-filter-float">
                    <label class="table_label"><?=__('Filter by')?></label>
                    <div class="form-group form_row">
                        <div class="select-wrapper">
                            <i class="q4bikon-arrow_bottom"></i>
                            <select data-name="filter_tracking_type" class="q4-select q4-form-input select-icon-pd">
                                <option value="created_at" ><?=__('Print date')?></option>
                                <option value="received_date" selected="selected"><?=__('Received date')?></option>
                                <option value="departure_date"><?=__('Departure date')?></option>
                            </select>
                        </div>
                        <i class="input_icon glyphicon glyphicon-calendar"></i>
                    </div>
                </div>
                <div class="dt-filter-float">
                    <label class="table_label"><?=__('Filter by')?> <?=__('Profession')?></label>
                    <div class="form-group form_row">
                        <div class="select-wrapper">
                            <i class="q4bikon-arrow_bottom"></i>
                            <select data-name="filter_proffesion" class="q4-select q4-form-input select-icon-pd">
                                <option value="0" selected="selected"><?=__('All')?></option>
                                <?foreach ($_PROJECT->company->professions->where('status','=',Enum_Status::Enabled)->order_by('cmpprofession.name','ASC')->find_all() as $profession): ?>
                                    <option value="<?=$profession->id?>"><?=$profession->name?></option>

                                <?endforeach ?>
                            </select>
                        </div>
                        <i class="input_icon glyphicon glyphicon-calendar"></i>
                    </div>
                </div>
                <div class="dt-datepicker-float">
                    <div class="row">
                        <div class="col-40 rtl-float-right">
                            <label class="table_label"><?=__('from')?></label>
                            <div class="input-group date date-tracking-start_date" data-provide="datepicker">
                                <div class="input-group-addon small-input-group">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                                <input type="text" value="<?=date('d/m/Y',time() - (60 * 60 * 24 * 90))?>" class="q4-form-input filter-from" data-date-format="DD/MM/YYYY">
                            </div>
                        </div>
                        <div class="col-40 rtl-float-right">
                            <label class="table_label"><?=__('to')?></label>
                            <div class="input-group date date-tracking-end_date" data-provide="datepicker">
                                <div class="input-group-addon small-input-group">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                                <input type="text" value="<?=date('d/m/Y')?>" class="q4-form-input filter-to" data-date-format="DD/MM/YYYY">
                            </div>
                        </div>
                        <div class="col-20 rtl-float-right">
                            <label class="table_label visibility-hidden"><?=__('Show')?></label>
                            <a data-url="<?=URL::site('projects/tracking_list/'.$_PROJECT->id)?>" class="inline-block-btn-small dark_blue_button filter-tracking"><?=__('Show')?></a>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="dt-filter-float">
                    <div class="form-group">
                        <label class="table_label visibility-hidden"><?=__('Search')?></label>
                        <div class="search-input-wrapper block">
                            <input  type="search" class="search-input" value="">
                            <a data-url="<?=URL::site('projects/tracking_list/'.$_PROJECT->id)?>" class="search-button search-tracking search-button-text"><?=__('Search')?></a>
                        </div>
                    </div>
                </div>
            </div>





            <div class="row">
                <div class="col-lg-12">

                    <div class="q4-carousel-table-wrap">
                        <div class="q4-carousel-table" data-structurecount="<?=count($items)?>">
                            <?foreach ($items as $item):?>

                                <div class="item">
                                    <div class="q4-carousel-blue-head reports-prop-title">
                                        <span class="blue-head-title"><?=' #'.$item->id?></span>
                                        <div class="blue-head-option project-props-qc">
                                            <a class="show-structure-mobile plan-details" data-qc="quality-control" data-url="<?=URL::site('projects/update_tracking/'.$item->id)?>">
                                                <i class="plus q4bikon-preview"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0" >
                                        <div class="q4-mobile-table-key">
                                            <?=__('Profession')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <?=$item->plans->find()->profession->name?>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0">
                                        <div class="q4-mobile-table-key">
                                            <?=__('Received date')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <?=$item->received_date ? date('d/m/Y',$item->received_date) : '-'?>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0">
                                        <div class="q4-mobile-table-key">
                                            <?=__('Recipient person')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <?=$item->recipient?>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0">
                                        <div class="q4-mobile-table-key">
                                            <?=__('Departure date')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <?=$item->departure_date ? date('d/m/Y',$item->departure_date) : '-'?>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0">
                                        <div class="q4-mobile-table-key">
                                            <?=__('List of plans')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <span>
                                                <?foreach ($item->plans->find_all() as $plan):?>
                                                    <p><?=trim($plan->file()->getName())?></p>
                                                <?endforeach?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="q4-carousel-row f0">
                                        <div class="q4-mobile-table-key">
                                            <?=__('Print date')?>
                                        </div>
                                        <div class="q4-mobile-table-value">
                                            <?=date('d/m/Y',$item->created_at)?>
                                        </div>
                                    </div>

                                </div>
                            <?endforeach;?>
                        </div>
                        <?if(isset($pagination)):?>
                                <?=$pagination?>
                            <?endif?>
                    </div>
                </div>
            </div>
    </form>

</div>

