<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.01.2017
 * Time: 22:40
 */
?>


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

