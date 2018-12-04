<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 28.12.2016
 * Time: 22:08
 */
?>
<header>
    <div class="header-top">
        <div class="mobile-logo">
            <div class="logo-bg">
                <?=Security::mousetrapLink()?>
                <a href="<?=URL::site('/')?>">
                    <img src="/media/img/logo_tablet.png" alt="logo">
                </a>
            </div>
        </div>
        <div class="header-office">
            <span class="header-office_icon">
                <?if(Request::current()->controller() == 'Projects' AND (isset($_PROJECT) AND $_PROJECT->company->logo)):?>
                    <img src="/<?=$_PROJECT->company->logo?>" alt="<?=$_PROJECT->company->name?>">
                <?else:?>
                    <img src="/media/img/header-office-icon.jpg" alt="Office Logo">
                <?endif?>
            </span>
            <div class="header-office_info">
                <span class="header-office_text"><?=__('Head Office')?></span>
                <data class="current-date" value="<?=date('F d, Y H:i',time())?>"></data>
            </div>
        </div>
        <a class="sidebar_btn">
            <span></span>
            <span></span>
            <span></span>
        </a>
        <?if(rand(0,99) > 23) Security::mousetrapRandLink()?>
        <div class="header-nav">
            <ul class="header_profile">
                <li class="header_profile_image"><img src="/media/img/profile.png" alt="profile picture"></li>
                <li class="header_profile_options">
                    <div class="header-profile-settings">
                        <span class="header-profile-name">
                            <span class="full_name">
                                <span class="f_name"><?=Auth::instance()->get_user()->name?></span>
                            </span>
                            <i class="arrow_bottom q4bikon-dropdown"></i>
                        </span>
                        <span class="header_profile_role"><?=__(Usr::role())?></span>
                        <ul class="header-profile-drop-list">
                            <li><a href="#" class="get-modal get-user-profile" data-url="<?=URL::site('user/profile')?>"><?=__('My Profile')?></a></li>
                            <li><a href="<?=URL::site('logout')?>"><?=__('Logout')?></a></li>
                        </ul>
                    </div>
                </li>

                <li class="header_lang">
                    <div class="keep-langs">
                        <span class="header-current-lang">
                            <img src="<?=URL::withLang(Language::getCurrent()->image,Language::getDefault()->slug)?>" class="q4_flag" alt="<?=Language::getCurrent()->name?>">
                            <i class="q4bikon-arrow_bottom"></i>
                        </span>
                        <ul id="header-lang-list" class="header-lang-list">
                            <?foreach (Language::getAll() as $lang):?>
                                <?if(Language::getCurrent()->iso2 == $lang->iso2) continue?>
                                    <li>
                                        <a href="<?=Route::url(Request::$current->route()->name(Request::$current->route()), array_merge(Request::$current->param(),['lang' => $lang->slug, 'controller' => Request::$current->controller(), 'action' => Request::$current->action()])).URL::query()?>">
                                            <img src="<?=URL::withLang($lang->image,Language::getDefault()->slug)?>" class="q4_flag" alt="<?=$lang->name?>">
                                        </a>
                                    </li>
                            <?endforeach?>
                        </ul>
                    </div>
                </li>

            </ul>
        </div>

        <div class="clear"></div>
    </div>
    <div class="header_bottom">
        <?if(Request::current()->controller() == 'Companies'):?>
            <?if(Usr::can(Usr::CREATE_PERM)):?>
        <div class="header_create_company">
            <span class="header_new_company"><?=__('New company')?></span>
            <a class="orange_circle header_add_company" href="<?=URL::site('companies/create')?>"><i class="plus q4bikon-plus"></i></a>
        </div>
            <?endif?>
        <?elseif(Request::current()->controller() == 'Projects'):?>
             <?if(!Auth::instance()->get_user()->is('project_admin')&&!Auth::instance()->get_user()->is('project_supervisor')&&!Auth::instance()->get_user()->is('project_adviser')&&!Auth::instance()->get_user()->is('project_visitor')):?>
                    <div class="header_create_company">
                        <span class="header_new_company"><?=__('New project')?></span>

                        <a class="orange_circle header_add_company" href="<?=URL::site('projects/create')?>"><i class="plus q4bikon-plus"></i></a>
                    </div>
                <?endif;?>
        <?endif?>
        <div class="header_breadcrumbs">
            <?=Breadcrumbs::render()?>
        </div>
        <div class="clear"></div>
    </div>

</header><!--end of header-->
