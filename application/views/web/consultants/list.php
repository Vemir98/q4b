<?defined('SYSPATH') OR die('No direct script access.');?>
        <div class="consultants-layout">
			<div class="single-panel-content">
				<div class="single-panel-body">
					<div class="container-fluid">
						<div class="row">

                            <div class="content-projects-filter q4-page-filter">
                                <ul class="filter-buttons-list">
                                    <li><input type="text" class="q4-form-input inlined consultant-autocomplete" placeholder='<?=__('Search By Name')?>' style="float: left; width: 200px;" value=""></li>
                                    <li>

                                        <span class="filter-text"><?=__('Filter by project')?>:</span>
                                        <div class="select-wrapper">

                                            <i class="q4bikon-arrow_bottom"></i>
                                            <select class="q4-select q4-form-input" onchange="document.location=this.options[this.selectedIndex].value">
                                                <option value="<?=URL::site('consultants')?>"><?=__('Please select')?></option>
                                                <?foreach ($projects as $item):?>
                                                <option value="<?=URL::site('consultants/project/'.$item->id)?>"
                                                        <?if($selectedProject == $item->id):?>selected="selected"<?endif;?>
                                                ><?=$item->name?></option>
                                                <?endforeach;?>
                                            </select>
                                        </div>
                                    </li>
                                </ul>
                                <div class="clear"></div>
                            </div>
						<div class="col-md-12">
							<div class="add-new-right form-group">
								<span><?=__('Add new user')?></span>
								<a class="orange_plus_small add-user" data-url="<?=URL::site('/consultants/check_email')?>"><i class="plus q4bikon-plus"></i></a>
							</div>
						</div>
					</div>
						<div class="row">
							<div class="col-lg-12">

								<div class="scrollable-table">
									<table class="rwd-table responsive_table table" data-toggle="table">
										<thead>
										<tr>
											<th data-field="<?=__('Details')?>" class="td-75"><?=__('Details')?></th>
											<th data-field="<?=__('Name')?>" data-sortable="true"><?=__('Name')?></th>
											<th data-field="<?=__('Email')?>" data-sortable="true" ><?=__('Email')?></th>
											<th data-field="<?=__('Profession')?>" data-sortable="true"><?=__('Profession')?></th>
											<th data-field="<?=__('User Group')?>" data-sortable="true"><?=__('User Group')?></th>
										</tr>
										</thead>
										<tbody>
										<?foreach ($users as $key => $user): ?>
											<tr>
												<td class="rwd-td4 align-center-left" data-th="<?=__('Details')?>">
													<a class="show-structure show-user-details" id="user-<?=$user->id?>" data-url="<?=URL::site('/consultants/update/'. $user->id)?>"><i class="plus q4bikon-preview"></i></a>
												</td>
												<td class="rwd-td0" data-th="<?=__('Name')?>">
													<div class="div-cell">
														<input type="text" class="q4-form-input disabled-input" value="<?=__($user->name)?>">
													</div>
												</td>
												<td class="rwd-td1" data-th="<?=__('Email')?>">
													<div class="div-cell">
														<input type="text" class="q4-form-input q4_email disabled-input" value="<?=$user->email?>">
													</div>
												</td>
												<td class="rwd-td2" data-th="<?=__('Profession')?>">
													<div class="div-cell">
														<input  type="text" class="q4-form-input disabled-input" value="<?=!empty($user->getProfession('name'))? __($user->getProfession('name')) : ""?>">

													</div>
												</td>
												<td class="rwd-td3" data-th="<?=__('User Group')?>">
													<div class="div-cell">
														<input  type="text" class="q4-form-input disabled-input" value="<?=__($user->getRelevantRole('name'))?>">
													</div>
												</td>
											</tr>
										<?endforeach;?>
										</tbody>
									</table>

								</div><!--scrollable table-->

							</div>
						</div>
					</div>
				</div><!--.single-panel-body-->
			</div><!--.single-panel-content-->
        </div>
<script>
    <?$u = []?>
    <?foreach ($users as $key => $user): ?>
    <?$name = str_replace("'"," ",$user->name)?>
    <?$u[] = "{value: '{$name}', data: '{$user->id}'}"?>
    <?$u[] = "{value: '{$user->email}', data: '{$user->id}'}"?>
    <?endforeach;?>
    <? $u = implode(",\n",$u)?>
    var names = [
        <?=$u?>
    ];

    $('.consultant-autocomplete').autocomplete({
        lookup: names,
        onSelect: function (suggestion) {
            $('#user-' + suggestion.data).click();
            $('.consultant-autocomplete').val('');
        }
    });
</script>
<style>
    .autocomplete-suggestions { border: 1px solid #d4e1ea; border-top: none; background: #FFF; overflow: auto; }
    .autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; cursor: default;}
    .autocomplete-selected { background: #F0F0F0; }
    .autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }
    .autocomplete-group { padding: 2px 5px; }
    .autocomplete-group strong { display: block; border-bottom: 1px solid #000; }
</style>