<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.01.2017
 * Time: 22:40
 */
?>
<!--<form class="q4_form crafts-form" autocomplete="off" action="--><?//=$action?><!--" data-ajax="true">-->
<!--    <input type="hidden" value="" name="x-form-secure-tkn"/>-->
    <div class="panel_body container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="q4-carousel-table-wrap">
                    <div class="q4-carousel-table" data-structurecount="<?=count($items)?>">
                        <?if(!empty($items)):?>
                            <?foreach($items as $item):?>
                                <?=View::make($_VIEWPATH.'form-item',['item' => $item])?>
                            <?endforeach?>
                        <?endif?>
                    </div>
                </div><!--.q4-carousel-table-wrap-->

            </div>
        </div>

    </div><!--.panel_body-->
<!--    <div class="panel_footer text-right">-->
<!--        <div class="row">-->
<!--            <div class="col-lg-4 col-lg-offset-8 col-sm-12 col-sm-offset-0">-->
<!--                <a href="#" class="inline_block_btn orange_button q4_form_submit">--><?//=__('Update')?><!--</a>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--    <input type="hidden" name="secure_tkn" value="--><?//=$secure_tkn?><!--">-->
<!--</form>-->
