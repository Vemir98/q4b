<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 19.07.2018
 * Time: 3:09
 */


?>


<div  data-backdrop="static" data-keyboard="false" class="report-modal modal fade" role="dialog" data-qcid="<?=$item->id?>">
    <div class="modal-dialog q4_project_modal modal-dialog-1070">

            <input type="hidden" value="" name="x-form-secure-tkn"/>
            <div class="modal-content">
                <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                        <div class="clear"></div>
                    </div>
                    <div class="q4_modal_sub_header">
                        <h3><?=__('Tasks report for floor')?></h3>
                    </div>
                </div>
                <div class="modal-body">
                	<div class="row">
                		<div class="col-lg-12">
							<div class="text-left">
								<span><?=__('public')?></span>
							</div>
			                <table class="responsive_table table table-bordered table-hover">

								<thead>
									<tr>
										<th></th>
										<th><?=__('Craft')?></th>
                                        <th class="td-125"><?=__('Quantity')?></th>
										<th class="td-100"><?=__('Checked')?></th>
									</tr>
								</thead>
								<tbody>
									<? $i = 0;
									foreach ($data['public'] as $one):?>
                                        <?if(!(int)$one['used']) continue?>
									<tr>
										<td><?=++$i?></td>
										<td><?=$one['name']?></td>
                                        <?if((int)$one['used']):?>
                                            <td><a class="get-report-details" href="<?=URL::site('reports/tasks/details/floor/'.$floor->id.'/'.$one['id']."/public")?>"><?=(int)$one['used']?>/<?=(int)$one['total']?></a></td>
                                        <?else:?>
                                            <td><?=(int)$one['used']?>/<?=(int)$one['total']?></td>
                                        <?endif?>
										<td><?=$one['percent']?>%</td>
									</tr>
									<?endforeach; ?>
								</tbody>
							</table>
							<br>
							<hr>
							<div class="text-left">
								<span><?=__('private')?></span>
							</div>
							<table class="responsive_table table table-bordered table-hover">

								<thead>

									<tr>
										<th></th>
										<th><?=__('Craft')?></th>
                                        <th class="td-125"><?=__('Quantity')?></th>
										<th class="td-100"><?=__('Checked')?></th>
									</tr>
								</thead>
								<tbody>
									<? $i = 0;
									foreach ($data['private'] as $one):?>
                                        <?if(!(int)$one['used']) continue?>
									<tr>
										<td><?=++$i?></td>
										<td><?=$one['name']?></td>
                                        <?if((int)$one['used']):?>
                                            <td><a class="get-report-details" href="<?=URL::site('reports/tasks/details/floor/'.$floor->id.'/'.$one['id']."/private")?>"><?=(int)$one['used']?>/<?=(int)$one['total']?></a></td>
                                        <?else:?>
                                            <td><?=(int)$one['used']?>/<?=(int)$one['total']?></td>
                                        <?endif?>
										<td><?=$one['percent']?>%</td>
									</tr>
									<?endforeach; ?>
								</tbody>
							</table>

                		</div>
                	</div>


					</div><!---.modal-body-->


                <div class="modal-footer">

                	<a class="inline_block_btn orange_button" data-dismiss="modal"><?=__('Close')?></a>
                </div><!---.modal-footer--->


            </div><!--.modal-content-->
    </div><!--.modal-dialog-->
</div><!--.modal-->
