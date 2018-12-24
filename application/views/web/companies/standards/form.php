<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 12.01.2017
 * Time: 10:08
 */
?>
<form class="q4_form standards-form" action="<?=$action?>" data-ajax="true" autocomplete="off">
    <input type="hidden" value="" name="x-form-secure-tkn"/>
    <div class="panel_body container-fluid">
        <div class=" panel-options">
            <span class="inline-options">
                <a class="orange_plus_small add-standard" id="new_standard"><i class="plus q4bikon-plus"></i></a>
                <span class="inline-options-text"><?=__('Add new standard')?> </span>
            </span>

        </div>

        <table class="new_company_table responsive_table table" data-toggle="table">
            <thead>
            <tr>
                <th data-field="Name" data-sortable="true"><?=__('Name')?></th>
                <th data-field="Organisation" data-sortable="true"><?=__('Organisation')?></th>
                <th data-field="Number" data-sortable="true"><?=__('Number')?></th>
                <th data-field="Submission Place" data-sortable="true"><?=__('Submission Place')?></th>
                <th data-field="Responsible person" data-sortable="true"><?=__('Responsible person')?></th>
                <th data-field="File(s)" data-sortable="true"><?=__('File(s)')?></th>
                <th data-field="Status" data-sortable="true"><?=__('Status')?></th>
            </tr>
            </thead>
            <tbody>
            <?if(!empty($items)):?>
                <?foreach($items as $item):?>
                    <?=View::make($_VIEWPATH.'form-item',['item' => $item, 'users' => $users])?>
                <?endforeach?>
            <?endif?>
            </tbody>
        </table>

    </div>
    <div class="panel_footer text-align">
        <div class="row">
            <div class=" col-md-12">
                <a href="#" class="inline_block_btn orange_button q4_form_submit"><?=__('Update')?></a>
            </div>
        </div>
    </div>
    <input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">
    <?if(count($users)):?>
        <select style="display: none;" class="users-data">
            <?foreach ($users as $user):?>
                <option value="<?=$user->id?>"><?=$user->email?></option>
            <?endforeach?>
        </select>
    <?endif?>
</form>
