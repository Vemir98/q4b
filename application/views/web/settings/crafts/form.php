<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.01.2017
 * Time: 22:40
 */
?>
<form class="q4_form crafts-form" autocomplete="off" action="<?=$action?>" data-ajax="true">
    <input type="hidden" value="" name="x-form-secure-tkn"/>
    <div class="panel_body container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel-options">
                    <span><?=__('Add new craft')?></span>
                    <a class="orange_plus_small add-craft" id="new_craft"><i class="plus q4bikon-plus"></i></a>
                </div>
                <table class="new_company_table responsive_table table" data-toggle="table">
                    <thead>
                    <tr>
                        <th data-field="Name" data-sortable="true"><?=__('Name')?></th>
                        <th data-field="Catalog Number" data-sortable="true"><?=__('Catalog Number')?></th>
                        <th data-field="Action"  class="td-100"><?=__('Action')?></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?if(!empty($items)):?>
                            <?foreach($items as $item):?>
                                <?=View::make($_VIEWPATH.'form-item',['item' => $item])?>
                            <?endforeach?>
                        <?endif?>
                    </tbody>
                </table>

            </div>
        </div>

    </div><!--.panel_body-->
    <div class="panel_footer text-right">
        <div class="row">
            <div class="col-lg-4 col-lg-offset-8 col-sm-12 col-sm-offset-0">
                <a href="#" class="inline_block_btn orange_button q4_form_submit"><?=__('Update')?></a>
            </div>
        </div>
    </div>
    <input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">
</form>
