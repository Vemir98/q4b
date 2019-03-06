<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 25.09.2017
 * Time: 12:38
 */
// var_dump($users->count());
// var_dump($users->count());
//
// echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($projects); echo "</pre>"; exit;
?>


<div class="consultants-layout">

	<div class="single-panel-content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-3 rtl-float-right">
					<div class="form-group">

						<div class="content-projects-filter q4-page-filter">

		                    <input type="text" class="q4-form-input consultant-autocomplete" placeholder='<?=__('Search By Name')?>' style="float: left;" value="">
		                </div>
					</div>
				</div>
				<div class="col-lg-3 rtl-float-right">
					<label class="table_label"><?=__('Filter by project')?></label>
					<div class="form-group">
	                    <div class="select-wrapper">

	                        <i class="q4bikon-arrow_bottom"></i>
	                        <select class="q4-select q4-form-input" onchange="document.location=this.options[this.selectedIndex].value">
	                            <option value="<?=URL::site('consultants')?>"><?=__('Please select')?></option>
	                            <?foreach ($projects as $item): ?>
	                            <option value="<?=URL::site('consultants/project/' . $item->id)?>"
	                                    <?if ($selectedProject == $item->id): ?>selected="selected"<?endif;?>
	                            ><?=$item->name?></option>
	                            <?endforeach;?>
	                        </select>
	                    </div>
                	</div>
                </div>
			</div>



			<div class="row">

				<div class="col-md-12">
					<div class="add-new-right form-group">
						<span><?=__('Add new user')?></span>
						<a class="orange_plus_small add-user" data-url="<?=URL::site('/consultants/check_email')?>"><i class="plus q4bikon-plus"></i></a>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="q4-carousel-table-wrap">
						<div class="q4-carousel-table" data-structurecount="<?=count($users)?>">
							<?foreach ($users as $key => $user): ?>
								<div class="item">
									<div class="q4-carousel-blue-head">
										<span class="blue-head-title"><?=__('Name') . ' : ' . $item->name?><?=__('Details') . ' #' . $user->id?></span>
										<div class="blue-head-option">
											<a class="show-structure-mobile show-user-details" id="user-<?=$user->id?>" href="#" data-url="<?=URL::site('/consultants/update/' . $user->id)?>">
												<i class="plus q4bikon-preview"></i>
											</a>
										</div>
									</div>
									<div class="q4-carousel-row f0">
										<div class="q4-mobile-table-key">
											<?=__('Name')?>
										</div>
										<div class="q4-mobile-table-value">
											<?=$user->name?>
										</div>
									</div>
									<div class="q4-carousel-row f0" >
										<div class="q4-mobile-table-key">
											<?=__('Email')?>
										</div>
										<div class="q4-mobile-table-value">
											<?=$user->email?>
										</div>
									</div>
									<div class="q4-carousel-row f0">
										<div class="q4-mobile-table-key">
											<?=__('Profession')?>
										</div>
										<div class="q4-mobile-table-value">
											<span><?=!empty($user->getProfession('name')) ? __($user->getProfession('name')) : ""?></span>
										</div>
									</div>
									<div class="q4-carousel-row f0">
										<div class="q4-mobile-table-key">
											<?=__('User Group')?>
										</div>
										<div class="q4-mobile-table-value">
											<?=__($user->getRelevantRole('name'))?>
										</div>
									</div>
								</div>
							<?endforeach;?>
						</div>
					</div><!--.q4-carousel-table-wrap-->
				</div>
			</div>
		</div>
	</div>
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
