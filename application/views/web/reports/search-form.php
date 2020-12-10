<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 04.04.2017
 * Time: 11:48
 */
$selectedArray = Arr::extract($_GET,["company","project","crafts","statuses","from","to",'approval_status','condition_level','condition_list']);
$selectedAdvancedArray = Arr::extract($_GET,["object_id","floors","place_type","place_number","profession_id","project_stage","space"]);
// $selectedCrafts = $_GET["crafts"];
// $selectedStatuses = $_GET["statuses"];
$statuses = [
    'existing',
    'normal',
    'invalid',
    'repaired',
];

 // echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$_GET,$selectedArray]); echo "</pre>"; exit;
?>
<!--reports list-->
    <div class="generate-reports"
        <?if($hidden):?>
        style="display: none;"
        <?endif?>
    >
        <form class="q4_form" action="<?=URL::site('reports/generate')?>">
        <input type="hidden" value="" name="x-form-secure-tkn"/>
            <div class="generate-reports-body">
                <div class="row">
                    <div class="col-md-6 col-sm-12 rtl-float-right">
                        <div class="row">
                            <div class="report-form-group col-md-6 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('Company')?></label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select name="company" class="q4-select q4-form-input rpt-company">
                                        <option value="select-company"><?=__("Select Company")?></option>
                                        <?foreach ($items as $i):?>
                                            <option value="<?=$i['id']?>" <?=$selectedArray["company"]==$i['id'] ? "selected" : ""?>><?=$i['name']?></option>
                                        <?endforeach?>
                                    </select>
                                </div>
                            </div>
                            <div class="report-form-group col-md-6 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('Project name')?></label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select class="q4-select q4-form-input rpt-project" name="project">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="report-form-group col-md-6 col-sm-12 rtl-float-right multi-select-col">
                                <label class="table_label">
                                    <span class="check-all-crafts check-all-links" data-seltxt="<?=__('select all')?>" data-unseltxt="<?=__('unselect all')?>">
                                        <?=__('unselect all')?>

                                    </span><?=__('Crafts')?>
                                </label>
                                <div class="multi-select-box rpt-craft">
                                    <div class="select-imitation">
                                        <span class="select-imitation-title"></span>
                                        <div class="over-select" id="reports-filter-crafts"></div>
                                        <i class="arrow-down q4bikon-arrow_bottom"></i>
                                    </div>
                                    <div class="checkbox-list">
                                    </div><!--.checkbox-list-->
                                    <select class="hidden-select" name="crafts[]" multiple>
                                    </select>
                                </div>
                            </div>
                            <div class="report-form-group col-md-6 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('Status')?></label>
                                <div class="multi-select-box">
                                    <div class="select-imitation">
                                        <span class="select-imitation-title">
                                            <?foreach ($statuses as  $status):?>
                                                <?if($selectedArray["statuses"]):?>
                                                    <?if(in_array($status, $selectedArray["statuses"])):?>
                                                        <?=__($status)?>,
                                                    <?endif; ?>
                                                    <?else:?>
                                                        <?=__($status)?>,
                                                <?endif;?>
                                            <?endforeach;?>

                                        </span>
                                        <div class="over-select"></div>
                                        <i class="arrow-down q4bikon-arrow_bottom"></i>
                                    </div>
                                    <div class="checkbox-list statuses-chbx">
                                        <?foreach ($statuses as  $status):?>
                                        <?if($selectedArray["statuses"]){
                                            $checked = in_array($status, $selectedArray["statuses"]) ? "checked" : '';
                                        }else{
                                           $checked =  "checked";
                                        }
                                        ?>
                                                <div class="checkbox-list-row">
                                                    <span class="checkbox-text">
                                                        <label class="checkbox-wrapper-multiple <?=$checked?>" data-val="<?=$status?>">
                                                            <span class="checkbox-replace"></span>
                                                            <i class="checkbox-list-tick q4bikon-tick"></i>
                                                        </label>
                                                        <?=__($status)?>
                                                    </span>
                                                </div>
                                        <?endforeach;?>

                                    </div><!--.checkbox-list-->
                                    <select class="hidden-select" name="statuses[]" multiple>
                                        <?foreach ($statuses as  $status):?>
                                        <?if($selectedArray["statuses"]){
                                            $selected = in_array($status, $selectedArray["statuses"]) ? "selected" : '';
                                        }else{
                                           $selected =  "selected";
                                        }
                                        ?>
                                                <option value="<?=$status?>" <?=$selected?>><?=__($status)?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="report-form-group col-md-6 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('approval_status')?></label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select name="approval_status" class="q4-select q4-form-input">
                                        <option value="all"><?=__("All")?></option>
                                        <?foreach (Enum_QualityControlApproveStatus::toArray() as $as):?>
                                            <option value="<?=$as?>" <?=$selectedArray["approval_status"]==$as ? "selected" : ""?>><?=__($as)?></option>
                                        <?endforeach?>
                                    </select>
                                </div>
                            </div>
                            <div class="report-form-group col-md-6 col-sm-12 rtl-float-right">
                                <label class="pretty-checkbox" for="sort_by_crafts" style="margin: 27px 10px 0 0;"><input type="checkbox" name="sort_by_crafts" id="sort_by_crafts" value="1"><?=__('sort_by_crafts')?></label>
                            </div>
                        </div>
                        <div class="row cond-level">
                            <div class="report-form-group col-md-6 col-sm-12 rtl-float-right">
                                <label class="table_label"><?=__('Severity Level')?></label>
                                <div class="select-wrapper">
                                    <i class="q4bikon-arrow_bottom"></i>
                                    <select name="condition_level" class="q4-select q4-form-input">
                                        <option value="all"><?=__("All")?></option>
                                        <?foreach (Enum_QualityControlConditionLevel::toArray() as $as):?>
                                            <option value="<?=$as?>" <?=$selectedArray["condition_level"]==$as ? "selected" : ""?>><?=__($as)?></option>
                                        <?endforeach?>
                                    </select>
                                </div>
                            </div>
                            <div class="report-form-group col-md-6 col-sm-12 rtl-float-right">
                                <label class="table_label "><?=__('Conditions List')?></label>
                                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                                    <select class="q4-select q4-form-input" name="condition_list">
                                        <option value="all"><?=__("All")?></option>
                                        <?foreach (Enum_QualityControlConditionList::toArray() as $as):?>
                                            <option value="<?=$as?>" <?=$selectedArray["condition_list"]==$as ? "selected" : ""?>><?=ucfirst(__($as))?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 rtl-float-right">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="table_label"><?=__('Report Range')?></label>
                                <div class="report-range-box">
                                    <div class="report-range-unit">
                                        <div class="report-range-unit-date">
                                            <label class="table_label"><?=__('from')?></label>
                                            <div class="input-group date report-start-date" id="report-start-date" data-provide="datepicker">
                                                <div class="input-group-addon small-input-group">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </div>
                                                <input type="text" class="form-control" data-min-date data-date-format="DD/MM/YYYY" name="from" value="<?=$selectedArray["from"] ? $selectedArray["from"] : date('d/m/Y',time() - (60 * 60 * 24 * 6))?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="report-range-unit">
                                        <div class="report-range-unit-date">
                                            <label class="table_label"><?=__('to')?></label>
                                            <div class="input-group date report-end-date" id="report-end-date" data-provide="datepicker">
                                                <div class="input-group-addon small-input-group">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </div>
                                                <input type="text" class="form-control" data-date-format="DD/MM/YYYY" name="to" value="<?=$selectedArray["to"] ? $selectedArray["to"] : date('d/m/Y',time() + (60 * 60 * 24 * 30))?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <a href="#" class="show-advanced-reports disabled hidden" data-url="<?=URL::site('reports/advanced_filter')?>">
                            <span><?=__('advanced')?></span>
                            <i class="q4bikon-arrow_right"></i>
                        </a>
                    </div>
                </div>
                <div id="advanced-reports" class="advanced-reports">

                </div>

            </div>

            <div class="generate-reports-footer">
                <div class="row">
                    <div class="col-md-12">
                        <a href="#" class="clear-all-reports"><?=__('Clear all')?></a>
                        <input type="submit" id="generated-results-link" class="inline_block_btn blue-light-button q4_form_submit disabled-input" value="<?=__('Generate')?>" >
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="generated-results"></div>
    <div class="hidden rpt-adv-frm"></div>
<script>
    var jsonReportItems = <?=$data?>;
    var selectedReportItems = <?=json_encode($selectedArray)?>;
    var selectedAdvancedItems = <?=json_encode($selectedAdvancedArray)?>;
</script>
