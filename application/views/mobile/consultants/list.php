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
							<?foreach ($users as $key => $user):?>
								<div class="item">
									<div class="q4-carousel-blue-head">
										<span class="blue-head-title"><?=__('Name').' : '.$item->name?><?=__('Details').' #'.$user->id?></span>
										<div class="blue-head-option">
											<a class="show-structure-mobile show-user-details" href="#" data-url="<?=URL::site('/consultants/update/'. $user->id)?>">
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
											<span><?=!empty($user->getProfession('name'))? __($user->getProfession('name')) : ""?></span>
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

