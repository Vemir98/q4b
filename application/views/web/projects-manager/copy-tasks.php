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
    <form action="<?= URL::site('/projects_manager/copy_tasks') ?>" data-ajax="true" method="post"
          autocomplete="off">
        <input type="hidden" value="" name="x-form-secure-tkn"/>
        <div class="row">
            <div class="col-md-3 rtl-float-right">
                <label class="table_label"><?= __('From Project') ?></label>
                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                    <select name="fromProjectId" class="q4-select q4-form-input filtered-projects">

                        <? foreach ($projects as $project): ?>
                            <? $selectedCompany = $project->id == 60 ? "selected" : '' ?>
                            <option <?= $selectedCompany ?>
                                    value="<?= $project->id ?>"><?= $project->name . ' - ' . $project->id ?> </option>
                        <? endforeach ?>

                    </select>
                </div>
            </div>
            <div class="col-md-3 rtl-float-right">
                <label class="table_label"><?= __('Copy To Project') ?></label>
                <div class="select-wrapper"><i class="q4bikon-arrow_bottom"></i>
                    <select name="toProjectId" class="q4-select q4-form-input filtered-projects">

                        <? foreach ($projects as $project): ?>
                            <option value="<?= $project->id ?>"><?= $project->name . ' - ' . $project->id ?> </option>
                        <? endforeach ?>

                    </select>
                </div>
            </div>
            <div class="col-md-3 rtl-float-right">
                <label class="table_label visibility-hidden"><?= __('Copy') ?></label>
                <input id="filter-dashboard-submit" class="inline-block-btn-small dark_blue_button" type="submit"
                       value="<?= __('Copy') ?>">
            </div>
        </div
    </form>
</section>
