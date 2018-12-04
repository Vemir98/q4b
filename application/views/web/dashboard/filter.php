<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 31.05.2017
 * Time: 14:43
 */

// echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($selectedProjectId); echo "</pre>"; exit;
?>


<div>
    <form action="" class="qc-form-filter" data-ajax="true" method="post" autocomplete="off">
        <input type="hidden" value="" name="x-form-secure-tkn"/>

        <div class="row">
            <div class="col-md-3 rtl-float-right">
                <label class="table_label"><?=__('Company name')?></label>
                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                    <select name="company" class="q4-select q4-form-input rpt-company">

                        <?foreach ($companies as $c):?>
                        <?$selectedCompany = $c->id== $selectedCompanyId ? "selected='selected'" : ''?>
                            <option <?=$selectedCompany?>  value="<?=$c->id?>"><?=$c->name?></option>
                        <?endforeach;?>
                    </select>
                </div>
            </div>
            <div class="col-md-3 rtl-float-right">
                <label class="table_label"><?=__('Project name')?></label>
                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                    <select name="project" class="q4-select q4-form-input filtered-projects">
                        <?foreach ($projects as $p):?>
                            <?if($p->company_id == $selectedCompanyId):?>
                                <option <?=$p->id== $selectedProjectId ? "selected='selected'" : ''?> value="<?=$p->id?>" data-companyid="<?=$p->company_id?>"><?=$p->name?></option>
                            <?endif;?>
                        <?endforeach;?>
                    </select>
                </div>
                <div class="hidden">
                     <select class="rpt-project1">
                        <?foreach ($projects as $p):?>
                            <option value="<?=$p->id?>" data-companyid="<?=$p->company_id?>"><?=$p->name?></option>
                        <?endforeach;?>
                     </select>
                </div>

            </div>
            <div class="col-md-3 rtl-float-right">
            <div class="relative">
                <label class="table_label"><?=__('Property')?></label>
                <span class="check-all-properties" data-seltxt="<?=__('select all')?>" data-unseltxt="<?=__('unselect all')?>"><?=__('unselect all')?></span>
            </div>
                <div class="multi-select-box">
                    <div class="select-imitation">
                        <?$tmpStr = ''?>
                        <?foreach($objects as $o):?>
                             <?if(!in_array($o->id,$selectedObjectIds)) continue?>
                                <?$name = $o->name ? __($o->name) : __($o->type->name)."-"?>
                                <?$tmpStr .= ($name.', ')?>
                        <?endforeach?>
                        <span class="select-imitation-title"><?=$tmpStr?></span>
                        <div class="over-select"></div>
                        <i class="arrow-down q4bikon-arrow_bottom"></i>
                    </div>
                    <div class="checkbox-list">
                        <?foreach ($objects as $o):?>
                            <div class="checkbox-list-row <?=$o->project_id == $selectedProjectId ? "":"hidden"?>" >
                                <span class="checkbox-text">
                                    <label class="checkbox-wrapper-multiple inline <?=in_array($o->id,$selectedObjectIds) ? " checked" : ''?> " data-projectid="<?=$o->project_id?>" data-val="<?=$o->id?>">
                                        <span class="checkbox-replace"></span>
                                        <i class="checkbox-list-tick q4bikon-tick"></i>
                                    </label>
                                    <?=$o->name ? __($o->name) : __($o->type->name)."-"?>
                                </span>
                            </div>
                        <?endforeach;?>
                    </div><!--.checkbox-list-->
                    <select class="hidden-select rpt-object" name="objects" multiple>
                        <?foreach ($objects as $o):?>
                            <option class="<?=$o->project_id == $selectedProjectId ?>" value="<?=$o->id?>" data-projectid="<?=$o->project_id?>" <?=in_array($o->id,$selectedObjectIds) ? 'selected="selected"' : ''?>><?=$o->name ? __($o->name) : __($o->type->name)?></option>
                        <?endforeach;?>
                    </select>
                </div>

            </div>
            <div class="col-md-3 rtl-float-right">
                <label class="table_label visibility-hidden"><?=__('Submit')?></label>
                <input id="filter-dashboard-submit" class="inline-block-btn-small dark_blue_button<?=$tmpStr ? '': ' disabled-gray-button'?>" type="submit" value="<?=__('Submit')?>">
            </div>
        </div>



    </form>
</div>