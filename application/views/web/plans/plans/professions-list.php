<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 01.04.2017
 * Time: 21:47
 */
?>


<div id="professions-list" class="modal fade" role="dialog"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog q4_project_modal plans-professions-list-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header q4_modal_header">
                <div class="q4_modal_header-top">
                    <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                    <div class="clear"></div>
                </div>
                <div class="q4_modal_sub_header">
                    <h3><?=__('Professions List')?></h3>
                </div>
            </div>
            <div class="modal-body professions-list-modal-body">
                <p class="professions-list-exist"><?=__('Selected professions already exist')?></p>

                <div class="professions-list-queue">
                    <div class="professions-list-queue-title"><h3><?=__('Profession')?></h3></div>
                    <ul class="professions-list-queue-lines">
                        <?foreach ($items as $item):?>
                        <li>
                            <div class="professions-list-checkbox">
                                <label  class="checkbox-wrapper">
                                    <input type="checkbox"   <?=in_array($item->id,$selected) ? 'checked' : ''?> disabled="disabled">
                                     <span class="checkbox-replace"></span><i class="checkbox-tick q4bikon-tick"></i>
                                </label>
                            </div>
                            <div class="professions-list-input">
                                <input type="text" class="table_input_full disabled-input" value="<?=__($item->name)?>"/>
                            </div>
                        </li>
                        <?endforeach?>
                    </ul>
                </div>

            </div>
        </div>

    </div>
</div>
