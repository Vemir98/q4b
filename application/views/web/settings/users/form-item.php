<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 09.01.2017
 * Time: 11:39
 */
?>


<tr data-row-id="1">
    <td class="rwd-td0" data-th="Name">
        <input type="text" class="table_input q4_required  <?if($item->status != Enum_Status::Enabled):?> 'disabled-input' <?endif?>" name="profession_<?=$item->id?>_name" value="<?=$item->name?>">
    </td>
    <td class="rwd-td1" data-th="Company">
        <div class="select-wrapper">
            <i class="q4bikon-arrow_bottom"></i>
            <select class="q4-select q4-form-input">
                <option value="Company name 1">Company name 1</option>
                <option value="Company name 2">Company name 2</option>
            </select>
        </div>
    </td>
    <td class="rwd-td2" data-th="Profession">
        <div class="select-wrapper">
            <i class="q4bikon-arrow_bottom"></i>
            <select class="q4-select q4-form-input">
                <option value="Carpenter">Carpenter</option>
                <option value="Welder">Welder</option>
            </select>
        </div>
    </td>
    <td class="rwd-td3" data-th="Email">
        <input type="text" class="table_input q4_email" value="johncarter@gmail.com">
    </td>
    <td class="rwd-td4" data-th="User Group">
        <div class="multi-select-box">
            <div class="select-imitation">
                <span class="select-imitation-title">Property 5, Property 6</span>
                <div class="over-select"></div>
                <i class="arrow-down q4bikon-arrow_bottom"></i>
            </div>
            <div class="checkbox-list">
                <div class="checkbox-list-row">
                    <label class="checkbox-wrapper-multiple inline checked" data-projectid="1" data-val="1">
                        <span class="checkbox-replace"></span>
                        <i class="checkbox-list-tick q4bikon-tick"></i>
                    </label>
                    <span class="checkbox-text">Property 1</span>
                </div>
                <div class="checkbox-list-row">
                    <label class="checkbox-wrapper-multiple inline checked" data-projectid="1" data-val="2">
                        <span class="checkbox-replace"></span>
                        <i class="checkbox-list-tick q4bikon-tick"></i>
                    </label>
                    <span class="checkbox-text">Property 2</span>
                </div>
                <div class="checkbox-list-row">
                    <label class="checkbox-wrapper-multiple inline checked" data-projectid="1" data-val="3">
                        <span class="checkbox-replace"></span>
                        <i class="checkbox-list-tick q4bikon-tick"></i>
                    </label>
                    <span class="checkbox-text">Property 3</span>
                </div>
                <div class="checkbox-list-row">
                    <label class="checkbox-wrapper-multiple inline" data-projectid="2" data-val="4">
                        <span class="checkbox-replace"></span>
                        <i class="checkbox-list-tick q4bikon-tick"></i>
                    </label>
                    <span class="checkbox-text">Property 4</span>
                </div>
                <div class="checkbox-list-row">
                    <label class="checkbox-wrapper-multiple inline" data-projectid="2" data-val="5">
                        <span class="checkbox-replace"></span>
                        <i class="checkbox-list-tick q4bikon-tick"></i>
                    </label>
                    <span class="checkbox-text">Property 5</span>
                </div>
                <div class="checkbox-list-row">
                    <label class="checkbox-wrapper-multiple inline" data-projectid="2" data-val="6">
                        <span class="checkbox-replace"></span>
                        <i class="checkbox-list-tick q4bikon-tick"></i>
                    </label>
                    <span class="checkbox-text">Property 6</span>
                </div>
                <div class="checkbox-list-row">
                    <label class="checkbox-wrapper-multiple inline" data-projectid="3" data-val="7">
                        <span class="checkbox-replace"></span>
                        <i class="checkbox-list-tick q4bikon-tick"></i>
                    </label>
                    <span class="checkbox-text">Property 7</span>
                </div>
                <div class="checkbox-list-row">
                    <label class="checkbox-wrapper-multiple inline" data-projectid="3" data-val="8">
                        <span class="checkbox-replace"></span>
                        <i class="checkbox-list-tick q4bikon-tick"></i>
                    </label>
                    <span class="checkbox-text">Property 8</span>
                </div>
                <div class="checkbox-list-row">
                    <label class="checkbox-wrapper-multiple inline checked" data-projectid="1" data-val="9">
                        <span class="checkbox-replace"></span>
                        <i class="checkbox-list-tick q4bikon-tick"></i>
                    </label>
                    <span class="checkbox-text">Property 9</span>
                </div>
                <div class="checkbox-list-row">
                    <label class="checkbox-wrapper-multiple inline" data-projectid="2" data-val="10">
                        <span class="checkbox-replace"></span>
                        <i class="checkbox-list-tick q4bikon-tick"></i>
                    </label>
                    <span class="checkbox-text">Property 10</span>
                </div>

            </div><!--.checkbox-list-->
            <select class="hidden-select" name="hiddenSelect[]" multiple>
                <option value="1">Property 1</option>
                <option value="2">Property 2</option>
                <option value="3">Property 3</option>
                <option value="4">Property 4</option>
                <option value="5" selected="selected">Property 5</option>
                <option value="6" selected="selected">Property 6</option>
            </select>
        </div>
    </td>
    <td class="rwd-td5" data-th="Action">
        <div class="div-cell">
            <div class="q4_radio">
                <div class="toggle_container">
                    <label class="label_unchecked">
                        <input value="disabled" type="radio"><span></span>
                    </label>
                    <label class="label_checked">
                        <input value="enabled" checked="checked" type="radio"><span></span>
                    </label>
                </div>
            </div>
            <div class="wrap-edit-row inliner ml-15">
                <span class="edit-row" data-toggle="modal" data-target="#modal-edit-user-window">
                    <i class="q4bikon-edit"></i>
                </span>
            </div>
        </div>
    </td>
</tr>
