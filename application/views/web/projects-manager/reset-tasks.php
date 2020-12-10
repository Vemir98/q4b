<? defined('SYSPATH') OR die('No direct script access.'); ?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 09.01.2017
 * Time: 11:36
 */
?>
<section>
    <form action="<?=URL::site('/projects_manager/reset_tasks_statuses')?>" data-ajax="true" method="post" autocomplete="off">
        <input type="hidden" value="" name="x-form-secure-tkn"/>
        <div class="row">
            <div class="col-md-3 rtl-float-right">
                <label class="table_label"><?= __('Reset Project Tasks Statuses') ?></label>
                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                    <select name="projectId" class="q4-select q4-form-input filtered-projects">

                        <? foreach ($projects as $project): ?>
                            <option value="<?= $project->id ?>"><?= $project->name . ' - ' . $project->id ?> </option>
                        <? endforeach ?>

                    </select>
                </div>
            </div>
            <div class="col-md-3 rtl-float-right">
                <label class="table_label visibility-hidden"><?= __('Reset') ?></label>
                <input id="filter-dashboard-submit" class="inline-block-btn-small dark_blue_button" type="submit" value="<?= __('Reset') ?>">
            </div>
        </div
    </form>
</section>
