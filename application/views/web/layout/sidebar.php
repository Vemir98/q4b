<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 28.12.2016
 * Time: 22:05
 */

$items = [
    [
        'slug' => 'quality_control',
        'text' => 'Quality control',
        'tooltip' => 'Quality control',
        'icon' => 'q4bikon-tick',
        'resource' => 'Controller_QControls',
        'priority' => Enum_UserPriorityLevel::Project
    ], [
        'slug' => 'dashboard',
        'text' => 'Dashboard',
        'tooltip' => 'Dashboard',
        'icon' => 'q4bikon-uncheked',
        'resource' => 'Controller_Dashboard',
        'priority' => Enum_UserPriorityLevel::Project
    ], [
        'slug' => 'companies',
        'text' => 'Companies',
        'tooltip' => 'Companies',
        'icon' => 'q4bikon-companies',
        'resource' => 'Controller_Companies',
        'priority' => Enum_UserPriorityLevel::Corporate
    ], [
        'slug' => 'projects',
        'text' => 'Menu _Projects',
        'tooltip' => 'Tooltip _Projects',
        'icon' => 'q4bikon-project',
        'resource' => 'Controller_Projects',
        'priority' => Enum_UserPriorityLevel::Project
    ], [
        'slug' => 'plans',
        'text' => 'Plans',
        'tooltip' => 'Plans',
        'icon' => 'q4bikon-file',
        'resource' => 'Controller_Plans',
        'priority' => Enum_UserPriorityLevel::Project
    ], [
        'slug' => 'reports',
        'text' => 'Reports',
        'tooltip' => 'Reports',
        'icon' => 'q4bikon-reports',
        'resource' => 'Controller_Reports',
        'priority' => Enum_UserPriorityLevel::Project
    ], [
       'slug' => 'consultants',
       'text' => 'Menu_Consultants And Auditors',
       'tooltip' => 'Tooltip_Consultants And Auditors',
       'icon' => 'q4bikon-public',
       'resource' => 'Controller_Consultants',
       'priority' => Enum_UserPriorityLevel::Company
    ], [
        'slug' => 'settings',
        'text' => 'Settings',
        'tooltip' => 'Settings',
        'icon' => 'q4bikon-settings2',
        'resource' => 'Controller_Settings',
        'priority' => Enum_UserPriorityLevel::General
    ], [
        'slug' => '',
        'text' => 'Archive',
        'icon' => 'q4bikon-archive',
        'resource' => 'Controller_Archive',
        'priority' => Enum_UserPriorityLevel::Company
    ],

];
foreach ($items as $key => $i){
    if(!Usr::can(Usr::READ_PERM,$i['resource'],$i['priority'])){
        unset($items[$key]);
    }
}
$items = json_decode(json_encode($items));
?>
<div class="sidebar">

    <div class="logo">
        <div class="wrap-logo">
            <a href="/" title="logo"><img src="/media/img/logo_variation1-1-t.png" alt="logo"></a>
        </div>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <?foreach ($items as $i):?>
            <li>
                <a href="<?=!empty($i->slug) ? URL::site($i->slug) : '#'?>" class="sidebar-items <?=(!empty($i->slug) AND strpos(URL::site(Request::detect_uri(), TRUE) . URL::query(),URL::site($i->slug)) !== false) ? 'active' : ''?>" title="<?=__($i->tooltip)?>">
                    <i class="icon <?=$i->icon?>"></i>
                    <span class="sidebar-items_title"><?=__($i->text)?></span>
                </a>
            </li>
            <?endforeach?>
        </ul>
    </nav>
    <?=Security::mousetrapRandLink()?>
</div>

<div class="sidebar_mobile">
    <div class="wrap_sidebar_close">
        <span class="close_mobile_sidebar">X</span>
    </div>

    <div class="search-mobile">
        <div class="sidebar-profile-options">
            <div class="sidebar-mobile-profile-img">
                <img src="/media/img/profile.png" alt="profile picture">
            </div>
            <div class="mobile-profile-name">
                <span class="f_name"><?=Auth::instance()->get_user()->name?></span>
            </div>
            <div class="mobile-profile-role"><?=__(Usr::role())?></div>
        </div>
        <ul class="profile-drop-list">
            <li><a href="#" class="get-modal get-user-profile" data-url="<?=URL::site('user/profile')?>"><?=__('My Profile')?></a></li>
            <li><a href="<?=URL::site('logout')?>" ><?=__('Logout')?></a></li>
        </ul>
        <?=Security::mousetrapRandLink()?>
        <div class="sidebar_options">
            <span class="flag-icon">
                 <span class="mobile-current-lang">
                    <img src="<?=URL::withLang(Language::getCurrent()->image,Language::getDefault()->slug)?>" class="q4_flag" alt="<?=Language::getCurrent()->name?>">
                </span>

                <ul class="mobile-lang-list">
                    <?foreach (Language::getAll() as $lang):?>
                        <?if(Language::getCurrent()->iso2 == $lang->iso2) continue?>
                            <li>
                                <a href="<?=Route::url(Request::$current->route()->name(Request::$current->route()), array_merge(Request::$current->param(),['lang' => $lang->slug, 'controller' => Request::$current->controller(), 'action' => Request::$current->action()])).URL::query()?>">
                                    <img src="<?=URL::withLang($lang->image,Language::getDefault()->slug)?>" class="q4_flag" alt="<?=$lang->name?>">
                                </a>
                            </li>
                    <?endforeach?>
                </ul>
            </span>
        </div>
    </div>
    <ul>
        <?foreach ($items as $i):?>
        <li>
            <a href="<?=!empty($i->slug) ? URL::site($i->slug) : '#'?>" class="sidebar-items <?=(!empty($i->slug) AND strpos(URL::site(Request::detect_uri(), TRUE) . URL::query(),URL::site($i->slug)) !== false) ? 'active' : ''?>" title="<?=__($i->tooltip)?>">
                <i class="icon <?=$i->icon?>"></i>
                <span class="sidebar-items_title"><?=__($i->text)?></span>
            </a>
        </li>
        <?endforeach?>
    </ul>
<!--end responsive sidebar-->
</div>