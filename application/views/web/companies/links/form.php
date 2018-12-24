<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 15.01.2017
 * Time: 9:59
 */
?>
<form action="<?=$action?>" class="q4_form links-form" autocomplete="off" data-ajax="true">
    <input type="hidden" value="" name="x-form-secure-tkn"/>
    <div class="panel_body container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class=" panel-options">
                    <span class="inline-options">
                        <a class="orange_plus_small add-link"><i class="plus q4bikon-plus"></i></a>
                        <span class="panel-options-text"><?=__('Add new link')?></span>
                    </span>

                </div>

                <table class="new_company_table responsive_table table" data-toggle="table">
                    <thead>
                    <tr>
                        <th data-field="Name" data-sortable="true"><?=__('Name')?></th>
                        <th data-field="URL" data-sortable="true"><?=__('URL')?></th>
                        <th data-field="Delete" data-sortable="true"><?=__('Delete')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?if(!empty($items)):?>
                        <?foreach($items as $item):?>
                            <?=View::make($_VIEWPATH.'form-item',
                                [
                                    'item' => $item,
                                ])?>
                        <?endforeach?>
                    <?endif?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <div class="panel_footer text-align">
        <div class="row">
            <div class=" col-md-12">
                <a href="#" class="inline_block_btn orange_button q4_form_submit"><?=__('Update')?></a>
            </div>
        </div>
    </div>
    <input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">
</form>
