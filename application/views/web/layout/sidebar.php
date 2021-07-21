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
        'slug' => 'quality_control/create',
        'text' => 'Create Quality Control',
        'tooltip' => 'Create Quality Control',
        'icon' => 'q4bikon-tick',
        'resource' => 'Controller_QualityControl',
        'priority' => Enum_UserPriorityLevel::Project,
        'disabled' => false,
        'active' => false
    ],
    [
        'slug' => 'labtests',
        'text' => 'Lab control',
        'icon' => 'q4bikon-lab',
        'resource' => 'Controller_LabTests',
        'priority' => Enum_UserPriorityLevel::Project,
        'children' => [
            [
                'slug' => 'labtests/project/:projectId',
                'text' => 'Lab control list',
                'resource' => 'Controller_LabTests',
                'priority' => Enum_UserPriorityLevel::Project,
                'disabled' => false,
                'active' => false
            ],
            [
                'slug' => 'labtests/project/:projectId/elements_type',
                'text' => 'Elements Type',
                'resource' => 'Controller_LabTests',
                'priority' => Enum_UserPriorityLevel::Project,
                'disabled' => false,
                'active' => false
            ],
            [
                'slug' => 'labtests/project/:projectId/elements_list',
                'text' => 'Elements List',
                'resource' => 'Controller_LabTests',
                'priority' => Enum_UserPriorityLevel::Project,
                'disabled' => false,
                'active' => false
            ]
        ],
        'disabled' => false,
        'active' => false
    ],
    [
        'slug' => 'dashboard',
        'text' => 'Dashboard',
        'tooltip' => 'Dashboard',
        'icon' => 'q4bikon-uncheked',
        'resource' => 'Controller_Dashboard',
        'priority' => Enum_UserPriorityLevel::Project,
        'disabled' => false,
        'active' => false
    ],
    [
        'slug' => 'companies',
        'text' => 'Companies',
        'tooltip' => 'Companies',
        'icon' => 'q4bikon-companies',
        'resource' => 'Controller_Companies',
        'priority' => Enum_UserPriorityLevel::Corporate,
        'disabled' => false,
        'active' => false
    ],
    [
        'slug' => 'projects',
        'text' => 'Menu _Projects',
        'tooltip' => 'Tooltip _Projects',
        'icon' => 'q4bikon-project',
        'resource' => 'Controller_Projects',
        'priority' => Enum_UserPriorityLevel::Project,
        'children' => [
            [
                'slug' => 'projects/update/:id/tasks',
                'text' => 'Tasks',
                'resource' => 'Controller_Projects',
                'priority' => Enum_UserPriorityLevel::Project,
                'disabled' => false,
                'active' => false
            ],
        ],
        'disabled' => false,
        'active' => false
    ],
    [
        'slug' => 'plans',
        'text' => 'Plans',
        'tooltip' => 'Plans',
        'icon' => 'q4bikon-file',
        'resource' => 'Controller_Plans',
        'priority' => Enum_UserPriorityLevel::Project,
        'disabled' => false,
        'active' => false
    ],
    [
        'slug' => 'reports/list',
        'text' => 'Reports',
        'tooltip' => 'Reports',
        'icon' => 'q4bikon-reports',
        'resource' => 'Controller_Reports',
        'priority' => Enum_UserPriorityLevel::Project,
        'disabled' => false,
        'active' => false
    ],
    [
        'slug' => 'consultants',
        'text' => 'Menu_Consultants And Auditors',
        'tooltip' => 'Tooltip_Consultants And Auditors',
        'icon' => 'q4bikon-public',
        'resource' => 'Controller_Consultants',
        'priority' => Enum_UserPriorityLevel::Company,
        'disabled' => false,
        'active' => false
    ],
    [
        'slug' => 'settings',
        'text' => 'Settings',
        'tooltip' => 'Settings',
        'icon' => 'q4bikon-settings2',
        'resource' => 'Controller_Settings',
        'priority' => Enum_UserPriorityLevel::General,
        'disabled' => false,
        'active' => false
    ],
    [
        'slug' => '',
        'text' => 'Archive',
        'icon' => 'q4bikon-archive',
        'resource' => 'Controller_Archive',
        'priority' => Enum_UserPriorityLevel::Company,
        'disabled' => false,
        'active' => false
    ],

];
Menu::setActiveItems($items);
file_put_contents(DOCROOT . 'menu.txt',var_export($items,true));
$detector = new Mobile_Detect; // todo:: Just add Plans page for Mobile devices
$isMobile = $detector->isMobile(); // Just add Plans page for Mobile devices
$isSubcontractor = false;
$roleName = Auth::instance()->get_user()->getRelevantRole('name');
$subcontractorsArr = Kohana::$config->load('subcontractors')->as_array();
if (array_key_exists($roleName, $subcontractorsArr)) {
    $isSubcontractor = true;
}

foreach ($items as $key => $i){
    if($isMobile and ($i['slug'] == 'plans')){ // Just add Plans page for Mobile devices
        unset($items[$key]); // Just add Plans page for Mobile devices
    } // Just add Plans page for Mobile devices
    if ($isSubcontractor) {
        if($i['slug'] !== 'reports/list'){
            unset($items[$key]);
        }
    } else {
        if(!Usr::can(Usr::READ_PERM,$i['resource'],$i['priority'])){
            unset($items[$key]);
        }
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
                    <a href="<?=!empty($i->slug) ? URL::site($i->slug) : '#'?>" class="<?=(isset($i->children) ? 'has-submenu' : '')?> sidebar-items <?=$i->active ? 'active' : ''?> <?=$i->disabled ? 'hidden' : ''?>" title="<?=__($i->tooltip)?>">
                        <i class="fw-600 icon <?=$i->icon?>" style="font-weight: 600"></i>
                        <span class="sidebar-items_title"><?=__($i->text)?></span>
                    </a>
                    <?if(isset($i->children)):?>
                        <ul class="submenu">
                            <?foreach ($i->children as $child):?>
                                <li>
                                    <a href="<?=!empty($child->slug) ? URL::site($child->slug) : '#'?>" class="sidebar-items sub-items <?=$child->active ? 'active' : ''?> <?=$child->disabled ? 'hidden' : ''?>" title="<?=__($child->tooltip)?>">
                                        <span class="sidebar-items_title"><?=__($child->text)?></span>
                                    </a>
                                </li>
                            <?endforeach?>
                        </ul>
                    <?endif?>
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