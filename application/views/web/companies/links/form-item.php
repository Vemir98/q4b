<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 15.01.2017
 * Time: 10:05
 */
?>
<tr>
    <td data-th="<?=__('Name')?>">
        <input type="text" class="table_input " name="link_<?=$item->id?>_name" value="<?=__($item->name)?>">
    </td>
    <td data-th="<?=__('URL')?>">
        <input type="text" class="table_input q4_url" name="link_<?=$item->id?>_url" value="<?=$item->url?>">
    </td>
    <td data-th="<?=__('Delete')?>">
        <div class="wrap_delete_row">
            <span class="delete_row delete-link" data-url="<?=URL::site('companies/delete_link/'.$_COMPANY->id.'/'.$item->id)?>"><i class="q4bikon-delete"></i></span>
        </div>
    </td>
</tr>
