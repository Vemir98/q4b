<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 12.02.2017
 * Time: 16:02
 */
?>
<div class="modal" id="modal_file_upload" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" >
    <div class="modal-dialog q4_modal" role="document">
        <div class="modal-content">
            <div class="modal-header q4_modal_header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="q4_modal_sub_header">
                    <span class="pull-right"><?=__('Floor')?> <?=$floor->number?></span>
                    <h3><?=__('Quality control list')?></h3>
                </div>
            </div>
            <form action="<?=$action?>" data-ajax="true" method="post">
                <input type="hidden" value="" name="x-form-secure-tkn"/>
                <div class="modal-body q4_modal_body">
                    <table>
                        <thead>
                        <tr>
                            <th><?=__('Element')?></th>
                            <th><?=__('Number')?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?=$place->name?></td>
                            <td><?=$place->number?></td>
                        </tr>
                        </tbody>

                    </table>
                    <div style="height: 50px;">
                        <a href="#" style="color: #2e6da4!important;" class="pull-right add-space"><?=__('Add new Item')?></a>
                    </div>
                    <table class="spaces-tbl">
                        <thead>
                        <tr>
                            <th><?=__('Craft')?></th>
                            <th><?=__('Space')?></th>
                            <th><?=__('Status')?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="sp-number">1</td>
                            <td>
                                <select name="space_+<?=$defaultSpaceId?>_type" >
                                    <option value="1"><?=__('Space')?></option>
                                </select>
                            </td>
                            <td><input type="text" name="space_+<?=$defaultSpaceId?>_desc"></td>
                            <td><a href="#" class="delete-space"><?=__('Delete')?></a></td>
                        </tr>
                        </tbody>

                    </table>

                    <select class="hidden space-types">
                        <option value="1">Space</option>
                        <option value="12">Space1</option>
                    </select>

                </div>
                <div class="modal-footer text-align q4_modal_footer">

                    <a href="#" class="inline_block_btn orange_button submit"><?=__('Update')?></a>
                </div>
            </form>
        </div>
    </div>
</div>

