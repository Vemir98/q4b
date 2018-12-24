<?defined('SYSPATH') OR die('No direct script access.');?><?php/** * Created by PhpStorm. *   User: SUR0 *   Date: 12.03.2017 *   Time: 7:34**/?><form action="<?=$action?>" method="post" data-ajax="true" autocomplete="off" class="q4_form props-form">   <input type="hidden" name="secure_tkn" value="<?=$secure_tkn?>">   <input type="hidden" value="" name="x-form-secure-tkn"/>   <div class="panel_body container-fluid">      <div class="row">         <div class="col-lg-12">            <!--Porperty-->            <div class="property-table-layout">               <div class="panel-options form_row">                   <span class="inline-options">                      <a class="orange_plus_small add-prop">                          <i class="plus q4bikon-plus"></i>                      </a>                       <span class="inline-options-text">                           <span><?=__('Add new property')?></span>                       </span>                   </span>               </div>               <div class="scrollable-table">                  <table class="rwd-table responsive_table table" data-toggle="table">                     <thead>                        <tr>                           <th class="td-25"></th>                           <th data-field="Object Type" data-sortable="true" class="td-200">                              <?=__('Object Type')?>                           </th>                           <th data-field="Name" data-sortable="true" class="td-300">                              <?=__('Name')?>                           </th>                           <th data-field="Floors (from-to)" data-sortable="true" class="td-max-150">                              <?=__('Floors (from-to)')?>                           </th>                           <th data-field="Elements" data-sortable="true" class="td-max-100">                              <?=__('Elements')?>                           </th>                           <th data-field="Start Date" data-sortable="true" class="td-datepicker">                              <?=__('Start Date')?>                           </th>                           <th data-field="End Date" data-sortable="true" class="td-datepicker">                              <?=__('End Date')?>                           </th>                           <th class="td-100">                              <?=__('Action')?>                           </th>                        </tr>                     </thead>                     <tbody>                        <tr class="hidden el-pattern">                           <td data-th="<?=__('Structure')?>"> </td>                           <td class="rwd-td0" data-th="<?=__('Object Type')?>">                              <div class="select-wrapper">                                 <i class="q4bikon-arrow_bottom"></i>                                  <select class="q4-select q4-form-input" data-name="property_%s_type_id">                                    <?$i=0 ;?>                                    <?foreach($itemTypes as $type):?>                                        <option value="<?=$type->id?>" <?=(!$i) ? 'selected="selected"' : ''?>>                                           <?=$type->name?>                                        </option>                                        <?$i++?>                                    <?endforeach?>                                 </select>                              </div>                           </td>                           <td class="rwd-td1" data-th="<?=__('Name')?>">                              <input type="text" class="table_input property-name-input" data-name="property_%s_name" value="">                           </td>                           <td class="rwd-td2" data-th="<?=__('Floors (from-to)')?>">                              <div class="numeric-align-c f0">                                 <div class="wrap-number inline-pickers">                                    <input type="text" class="numeric-input bidi-override floors-from" value="0" data-name="property_%s_smaller_floor" />                                    <span class="arrows">                                        <i class="arrow no-arrow_top"></i>                                        <i class="arrow no-arrow_bottom"></i>                                    </span>                                 </div>                                 <span class="inline-picker-divider">-</span>                                 <div class="wrap-number inline-pickers">                                    <input type="text" class="numeric-input bidi-override floors-to" value="0" data-name="property_%s_bigger_floor" />                                    <span class="arrows">                                        <i class="arrow no-arrow_top"></i>                                        <i class="arrow no-arrow_bottom"></i>                                    </span>                                 </div>                              </div>                           </td>                           <td class="rwd-td3 align-center-left" data-th="<?=__('Elements')?>">                              <div class="wrap-number inline-pickers">                                 <input type="text" class="numeric-input places-count" data-name="property_%s_places_count" value="1" />                                 <span class="arrows">                                     <i class="arrow no-arrow_top"></i>                                     <i class="arrow no-arrow_bottom"></i>                                 </span>                              </div>                           </td>                           <td class="rwd-td4" data-th="<?=__('Start Date')?>">                              <div class="div-cell">                                 <div class="input-group scrollable-date" data-provide="datepicker">                                    <div class="input-group-addon small-input-group">                                       <span class="glyphicon glyphicon-calendar"></span>                                    </div>                                    <input type="text" class="table_input" data-date-format="DD/MM/YYYY" data-name="property_%s_start_date" value="<?=date('d/m/Y',time())?>">                                 </div>                              </div>                           </td>                           <td class="rwd-td5" data-th="<?=__('End Date')?>">                              <div class="div-cell">                                 <div class="input-group scrollable-date" data-provide="datepicker">                                    <div class="input-group-addon small-input-group">                                       <span class="glyphicon glyphicon-calendar"></span>                                    </div>                                    <input type="text" class="table_input" data-date-format="DD/MM/YYYY" data-name="property_%s_end_date" value="<?=date('d/m/Y',time())?>">                                 </div>                              </div>                           </td>                           <td data-th="Delete">                              <div class="wrap_delete_row">                                 <span class="delete_row delete-prop" data-id="%s">                                    <i class="q4bikon-delete"></i>                                 </span>                              </div>                           </td>                        </tr>                        <?=View::make($_VIEWPATH. 'list', [ 'items'=> $items, 'itemTypes' => $itemTypes, ])?>                     </tbody>                  </table>               </div>            </div>            <!--end of property-->         </div>      </div>   </div>   <div class="panel_footer text-align">      <div class="row">         <div class="col-lg-12 col-sm-12">            <a href="#" class="inline_block_btn orange_button q4_form_submit">            <?=__( 'Update')?>            </a>         </div>      </div>   </div></form>