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
                            <a data-url="<?=URL::site('plans/tracking_list/'.$_PROJECT->id)?>" class="inline-block-btn-small dark_blue_button filter-tracking"><?=__('Show')?></a>
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
                            <a data-url="<?=URL::site('plans/tracking_list/'.$_PROJECT->id)?>" class="search-button search-tracking search-button-text"><?=__('Search')?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="scrollable-table">
                        <table class="rwd-table responsive_table table" data-toggle="table">
                            <thead>
                            <tr>
                                <th class="w-25 hidden"></th>
                                <th class="w-25"></th>
                                <th data-field="Name/Type" data-sortable="true" class="td-150"><?=__('Tracking ID')?></th>
                                <th data-field="Profession" data-sortable="true"><?=__('Profession')?></th>
                                <th data-field="Edition" data-sortable="true" class="td-150"><?=__('Received date')?></th>
                                <th data-field="Floor"><?=__('Recipient person')?></th>
                                <th data-field="Property" data-sortable="true" class="td-150"><?=__('Departure date')?></th>
                                <th data-field="Professions"><?=__('List of plans')?></th>
                                <th data-field="Craft" data-sortable="true" class="td-150"><?=__('Print date')?></th>
                                <th data-field="Date" class="td-100"><?=__('Action')?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <?foreach ($items as $item):?>
                                    <td class="rwd-td1 align-center-left" data-th="Details">
                                        <a class="show_details tracking-details" data-url="<?=URL::site('plans/update_tracking/'.$item->id)?>"><i class="plus q4bikon-preview"></i></a>
                                    </td>
                                    <td class="rwd-td2" data-th="Tracking ID">
                                        <input type="text" class="q4-form-input disabled-input" value="<?='#'.$item->id?>">
                                    </td>
                                    <td class="rwd-td2" data-th="Proffesion">
                                        <input type="text" class="q4-form-input disabled-input" value="<?=$item->plans->find()->profession->name?>">
                                    </td>
                                    <td class="rwd-td7" data-th="Recieved Date">
                                        <input value="<?=$item->received_date ? date('d/m/Y',$item->received_date) : '-'?>" name="recieved_date" type="text" class="q4-form-input disabled-input" data-date-format="DD/MM/YYYY">
                                    </td>
                                    <td class="rwd-td6" data-th="Recipient person">

                                        <input type="text" name="recipient" class="q4-form-input disabled-input" value="<?=$item->recipient?>">


                                    </td>
                                    <td class="rwd-td4" data-th="Departure date">
                                        <input type="text" value="<?=$item->departure_date ? date('d/m/Y',$item->departure_date) : '-'?>" name="departure_date" class="q4-form-input disabled-input" data-date-format="DD/MM/YYYY">
                                    </td>
                                    <td class="rwd-td3" data-th="List of plans">
                                        <div class="multi-select-box">
                                            <div class="select-imitation">
                                                <span class="select-imitation-title"><?=__('Click to vew list')?></span>
                                                <div class="over-select"></div>
                                                <i class="arrow-down q4bikon-arrow_bottom"></i>
                                            </div>
                                            <div class="checkbox-list-no-scroll hidden">

                                                <?foreach ($item->plans->find_all() as $plan):?>
                                                    <div class="checkbox-list-row disabled-input">
                                                        <span class="checkbox-text">
                                                            <label class="checkbox-wrapper-multiple inline checked">
                                                                <span class="checkbox-replace "></span>
                                                                <i class="checkbox-list-tick q4bikon-tick"></i>
                                                            </label><?=trim($plan->file()->getName())?></span>
                                                    </div>

                                                <?endforeach?>
                                            </div><!--.checkbox-list-->
                                            <select class="hidden-select" multiple>
                                                <?foreach ($item->plans->find_all() as $plan):?>
                                                    <option value="<?=$plan->file()->getName()?>"><?=$plan->file()->getName()?></option>
                                                <?endforeach?>
                                            </select>
                                        </div><!--.multi-select-box-->
                                    </td>
                                    <td class="rwd-td3" data-th="Print date">
                                        <input type="text" class="q4-form-input disabled-input" value="<?=date('d/m/Y',$item->created_at)?>">
                                    </td>
                                    <td class="rwd-td10 align-center-left" data-th="Action">
                                        <div class="div-cell-inline">
                                            <?$ext = explode('.',$item->file)?>
                                            <?
                                            // echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r( [explode('.',$item->file),$item->file]); echo "</pre>"; ?>
                                            <?if($ext[count($ext)-1]!=='pdf'):?>
                                                <span class="print-element circle-sm inliner blue<?=$item->file ? '' : ' disabled-gray-button'?>" data-imagesource="/<?=$item->file?>" title="Print Element">
                                                   <i class="q4bikon-print"></i>
                                                </span>
                                            <?else:?>
                                                <a href="/<?=$item->file?>" target="_blank" class="circle-sm inliner blue<?=$item->file ? '' : ' disabled-gray-button'?>"  title="Print Element">
                                                    <i class="q4bikon-print"></i></a>

                                            <?endif?>
                                            <span data-id=<?=$item->id?> class="delete_row circle-sm inliner red delete-tracking" data-url="<?=URL::site('plans/delete_tracking/'.$item->id)?>" title="Delete Row">
                                                <i class="q4bikon-delete"></i>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                             <?endforeach; ?>
                            </tbody>
                        </table>
                    </div><!--scrollable table-->

                    <?=$pagination?>
                </div>
            </div>
        </div>
        <!-- <div class="panel_footer text-align">
            <div class="row">
                <div class="col-md-12 text-align">
                    <a href="#" class="q4-btn-lg orange q4_form_submit"><?=__('Update')?></a>
                </div>
            </div>
        </div> -->
    </form>

</div>

