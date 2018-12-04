<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.01.2017
 * Time: 10:47
 */
?>
<?if($tabsDisabled):?>
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="q4_error_message" data-url="<?=URL::site('settings/apply_status/')?>" style="padding:10px 5px;"><?=__('settings-update-process')?></div>
        </div>
    </div>
<?endif;?>
<!--content_new_company-->
<section class="content_new_company">
    <ul>

        <li class="tab_panel<?=$tabsDisabled ?' disabled':null?>">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Crafts')?></h2>
            </div>
            <div class="panel_content">
                <?=View::make($_VIEWPATH.'crafts/form',
                    ['action' => URL::site('settings/update_crafts/'),
                        'items' => $crafts,
                        'secure_tkn' => AesCtr::encrypt(Auth::instance()->get_user()->id.Text::random('alpha'),Auth::instance()->get_user()->id,192)
                    ])?>
            </div><!--.panel_content-->
        </li>
        <li class="tab_panel<?=$tabsDisabled ?' disabled':null?>">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Professions')?></h2>
            </div>
            <div class="panel_content">
                <?=View::make($_VIEWPATH.'professions/form',
                    ['action' => URL::site('settings/update_professions/'),
                        'items' => $professions,
                        'items_crafts' => $professionsSelectedCrafts,
                        'crafts' => $crafts,
                        'secure_tkn' => AesCtr::encrypt(Auth::instance()->get_user()->id.Text::random('alpha'),Auth::instance()->get_user()->id,192)
                    ])?>
            </div><!--.panel_content-->
        </li>

        <li class="tab_panel<?=$tabsDisabled ?' disabled':null?>">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Tasks')?></h2>
            </div>
            <div class="panel_content">
                <?=View::make($_VIEWPATH.'tasks/form',
                    ['action' => URL::site('settings/update_tasks/'),
                        'items' => $tasks,
                        'items_crafts' => $tasksSelectedCrafts,
                        'crafts' => $crafts,
                        'secure_tkn' => AesCtr::encrypt(Auth::instance()->get_user()->id.Text::random('alpha'),Auth::instance()->get_user()->id,192)
                    ])?>
            </div><!--.panel_content-->
        </li>
        <li class="tab_panel<?=$tabsDisabled ?' disabled':null?>">
            <div class="panel_header">
                <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Property objects')?></h2>
            </div>
            <div class="panel_content">
                 <div class="panel_body property-objects-body container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="property-object-buttons">
                                <span class="inline_block_btn orange_button object-type-modal" data-url="<?=URL::site('settings/object_types')?>"><?=__('Property Types')?></span>
                                <span class="inline_block_btn orange_button construct-element-modal" data-url="<?=URL::site('settings/construct_elements')?>"><?=__('Construction Elements')?></span>
                                <span class="inline_block_btn orange_button space-type-modal" data-url="<?=URL::site('settings/space_types')?>" ><?=__('Space Types')?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--.panel_content-->
        </li>
    </ul>
</section><!--.content_new_company-->
<?if(!$tabsDisabled):?>
    <div class="text-right-left">
        <div class="row">
            <div class="col-md-12">
                <a href="<?=URL::site('settings/apply_to_all')?>" class="inline_block_btn orange_button"><?=__('Apply to all')?></a>
            </div>
        </div>
    </div>
<?endif;?>
