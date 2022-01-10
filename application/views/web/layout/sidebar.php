<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 28.12.2016
 * Time: 22:05
 */

$items = [];

if(in_array(Usr::role(), ['super_admin','project_admin', 'company_admin'])) {
    $items[] = [
        'slug' => 'dashboard',
        'href' => 'dashboard',
        'text' => 'Dashboard_new',
        'tooltip' => __('Dashboard_new'),
        'icon' => 'q4bikon-uncheked',
        'resource' => 'Controller_Dashboard',
        'priority' => Enum_UserPriorityLevel::Project,
        'deepLevel' => 1,
        'showIn' => [
            'dashboard'
        ],
        'disabled' => false,
        'active' => false,
        'hasActiveChild' => false
    ];
}

$items[] = [
    'slug' => 'companies/{any}',
    'href' => 'companies',
    'text' => 'Companies',
    'tooltip' => __('Companies'),
    'icon' => 'q4bikon-companies',
    'resource' => 'Controller_Companies',
    'priority' => Enum_UserPriorityLevel::Corporate,
    'deepLevel' => 1,
    'children' => [
        [
            'slug' => 'companies/update/:id?tab=info',
            'href' => 'companies/update/:id?tab=info',
            'text' => 'Info',
            'resource' => 'Controller_Companies',
            'priority' => Enum_UserPriorityLevel::Project,
            'deepLevel' => 2,
            'showIn' => Menu::CATEGORIES['companies'],
            'disabled' => false,
            'active' => false,
            'hasActiveChild' => false
        ],
        [
            'slug' => 'companies/update/:id?tab=specialities',
            'href' => 'companies/update/:id?tab=specialities',
            'text' => 'Crafts',
            'resource' => 'Controller_Companies',
            'priority' => Enum_UserPriorityLevel::Project,
            'deepLevel' => 2,
            'showIn' => Menu::CATEGORIES['companies'],
            'disabled' => false,
            'active' => false,
            'hasActiveChild' => false
        ],
        [
            'slug' => 'companies/update/:id?tab=professions',
            'href' => 'companies/update/:id?tab=professions',
            'text' => 'Professions',
            'resource' => 'Controller_Companies',
            'priority' => Enum_UserPriorityLevel::Project,
            'deepLevel' => 2,
            'showIn' => Menu::CATEGORIES['companies'],
            'disabled' => false,
            'active' => false,
            'hasActiveChild' => false
        ],
        [
            'slug' => 'companies/update/:id?tab=instructions',
            'href' => 'companies/update/:id?tab=instructions',
            'text' => 'Instructions',
            'resource' => 'Controller_Companies',
            'priority' => Enum_UserPriorityLevel::Project,
            'deepLevel' => 2,
            'showIn' => Menu::CATEGORIES['companies'],
            'disabled' => false,
            'active' => false,
            'hasActiveChild' => false
        ],
        [
            'slug' => 'companies/update/:id?tab=users',
            'href' => 'companies/update/:id?tab=users',
            'text' => 'Users',
            'resource' => 'Controller_Companies',
            'priority' => Enum_UserPriorityLevel::Project,
            'deepLevel' => 2,
            'showIn' => Menu::CATEGORIES['companies'],
            'disabled' => false,
            'active' => false,
            'hasActiveChild' => false
        ],
    ],
    'showIn' => [
        'companies/{any}'
    ],
    'disabled' => false,
    'active' => false,
    'hasActiveChild' => false
];

$projectRoutes = [
    'slug' => 'projects/{any}',
    'href' => 'projects',
    'text' => 'Menu _Projects',
    'tooltip' => __('Tooltip _Projects'),
    'icon' => 'q4bikon-project',
    'resource' => 'Controller_Projects',
    'priority' => Enum_UserPriorityLevel::Project,
    'deepLevel' => 1,
    'children' => [
        [
            'slug' => 'projects/update/:projectId?tab=info',
            'href' => 'projects/update/:projectId?tab=info',
            'text' => 'Info',
            '22' => 22,
            'resource' => 'Controller_Projects',
            'priority' => Enum_UserPriorityLevel::Project,
            'deepLevel' => 2,
            'showIn' => array_merge(
                    Menu::CATEGORIES['projects'],
                    Menu::CATEGORIES['labtests'],
                    Menu::CATEGORIES['elements']
            ),
            'disabled' => false,
            'active' => false,
            'hasActiveChild' => false
        ],
    ],
    'showIn' => [
        'projects/{any}'
    ],
    'disabled' => false,
    'active' => false,
    'hasActiveChild' => false
];

if(!Auth::instance()->get_user()->is('project_adviser')) {
    $projectRoutes['children'][] = [
        'slug' => 'projects/update/:projectId?tab=structures',
        'href' => 'projects/update/:projectId?tab=structures',
        'text' => 'Structures',
        'resource' => 'Controller_Projects',
        'priority' => Enum_UserPriorityLevel::Project,
        'deepLevel' => 2,
        'showIn' => array_merge(
            Menu::CATEGORIES['projects'],
            Menu::CATEGORIES['labtests'],
            Menu::CATEGORIES['elements']
        ),
        'disabled' => false,
        'active' => false,
        'hasActiveChild' => false
    ];
}

$projectRoutes['children'][] = [
    'slug' => 'plans/update/:projectId',
    'href' => 'plans/update/:projectId',
    'text' => 'Plans',
    'icon' => 'q4bikon-file',
    'resource' => 'Controller_Plans',
    'priority' => Enum_UserPriorityLevel::Project,
    'deepLevel' => 2,
    'showIn' => array_merge(
        Menu::CATEGORIES['projects'],
        Menu::CATEGORIES['labtests'],
        Menu::CATEGORIES['elements']
    ),
    'disabled' => false,
    'active' => false,
    'hasActiveChild' => false
];

//if(Usr::can(Usr::TASKS_PERM)) {
    $projectRoutes['children'][] = [
        'slug' => 'projects/update/:projectId/tasks',
        'href' => 'projects/update/:projectId/tasks',
        'text' => 'Tasks',
        'resource' => 'Controller_Projects',
        'priority' => Enum_UserPriorityLevel::Project,
        'deepLevel' => 2,
        'showIn' => array_merge(
            Menu::CATEGORIES['projects'],
            Menu::CATEGORIES['labtests'],
            Menu::CATEGORIES['elements']
        ),
        'disabled' => false,
        'active' => false,
        'hasActiveChild' => false
    ];
//}


$projectRoutes['children'][] = [
    'slug' => 'labtests/project/:projectId',
    'href' => 'labtests/project/:projectId',
    'text' => 'Lab control menu',
    'icon' => 'q4bikon-lab',
    'resource' => 'Controller_LabTests',
    'priority' => Enum_UserPriorityLevel::Project,
    'deepLevel' => 2,
    'children' => [
//        [
//            'slug' => 'labtests/create/:projectId',
//            'href' => 'labtests/create/:projectId',
//            'text' => 'Create Lab Control',
//            'resource' => 'Controller_LabTests',
//            'priority' => Enum_UserPriorityLevel::Project,
//            'deepLevel' => 3,
//            'showIn' => Menu::CATEGORIES['labtests'],
//            'disabled' => false,
//            'active' => false,
//            'hasActiveChild' => false
//        ],
        [
            'slug' => 'labtests/project/:projectId',
            'href' => 'labtests/project/:projectId',
            'text' => 'Lab control list',
            'resource' => 'Controller_LabTests',
            'priority' => Enum_UserPriorityLevel::Project,
            'deepLevel' => 3,
            'showIn' => Menu::CATEGORIES['labtests'],
            'disabled' => false,
            'active' => false,
            'hasActiveChild' => false
        ],
    ],
    'showIn' => array_merge(
        Menu::CATEGORIES['projects'],
        Menu::CATEGORIES['labtests'],
        Menu::CATEGORIES['elements']
    ),
    'disabled' => false,
    'active' => false,
    'hasActiveChild' => false
];

$projectRoutes['children'][] = [
    'slug' => 'elements',
    'href' => 'labtests/project/:projectId/elements_list',
    'text' => 'elements',
    'resource' => 'Controller_LabTests',
    'priority' => Enum_UserPriorityLevel::Project,
    'deepLevel' => 2,
    'children' => [
        [
            'slug' => 'labtests/project/:projectId/elements_type',
            'href' => 'labtests/project/:projectId/elements_type',
            'text' => 'Elements Type',
            'resource' => 'Controller_LabTests',
            'priority' => Enum_UserPriorityLevel::Project,
            'deepLevel' => 3,
            'showIn' => Menu::CATEGORIES['elements'],
            'disabled' => false,
            'active' => false,
            'hasActiveChild' => false
        ],
        [
            'slug' => 'labtests/project/:projectId/elements_list',
            'href' => 'labtests/project/:projectId/elements_list',
            'text' => 'Elements List',
            'resource' => 'Controller_LabTests',
            'priority' => Enum_UserPriorityLevel::Project,
            'deepLevel' => 3,
            'showIn' => Menu::CATEGORIES['elements'],
            'disabled' => false,
            'active' => false,
            'hasActiveChild' => false
        ],
        [
            'slug' => 'reports/approve_element/:projectId',
            'href' => 'reports/approve_element/:projectId',
            'text' => 'approval_element_reports',
            'resource' => 'Controller_LabTests',
            'priority' => Enum_UserPriorityLevel::Project,
            'deepLevel' => 3,
            'showIn' => Menu::CATEGORIES['elements'],
            'disabled' => false,
            'active' => false,
            'hasActiveChild' => false
        ],
    ],
    'showIn' => array_merge(
        Menu::CATEGORIES['projects'],
        Menu::CATEGORIES['labtests'],
        Menu::CATEGORIES['elements']
    ),
    'disabled' => false,
    'active' => false,
    'hasActiveChild' => false
];

if(!Auth::instance()->get_user()->is('project_adviser')) {
    $projectRoutes['children'][] = [
        'slug' => 'projects/update/:projectId?tab=certificates',
        'href' => 'projects/update/:projectId?tab=certificates',
        'text' => 'Certificates',
        'resource' => 'Controller_Projects',
        'priority' => Enum_UserPriorityLevel::Project,
        'deepLevel' => 2,
        'showIn' => array_merge(
            Menu::CATEGORIES['projects'],
            Menu::CATEGORIES['labtests'],
            Menu::CATEGORIES['elements']
        ),
        'disabled' => false,
        'active' => false,
        'hasActiveChild' => false
    ];
}

if(Usr::role() == 'super_admin') {
    $projectRoutes['children'][] = [
        'slug' => 'projects/update/:projectId?tab=delivery-items',
        'href' => 'projects/update/:projectId?tab=delivery-items',
        'text' => 'delivery_module_settings',
        'resource' => 'Controller_Projects',
        'priority' => Enum_UserPriorityLevel::Project,
        'deepLevel' => 2,
        'showIn' => array_merge(
            Menu::CATEGORIES['projects'],
            Menu::CATEGORIES['labtests'],
            Menu::CATEGORIES['elements']
        ),
        'disabled' => false,
        'active' => false,
        'hasActiveChild' => false
    ];
}

if(!Auth::instance()->get_user()->is('project_adviser') && !Auth::instance()->get_user()->is('project_supervisor')) {
    $projectRoutes['children'][] = [
        'slug' => 'projects/update/:projectId?tab=users',
        'href' => 'projects/update/:projectId?tab=users',
        'text' => 'Users',
        'resource' => 'Controller_Projects',
        'priority' => Enum_UserPriorityLevel::Project,
        'deepLevel' => 2,
        'showIn' => array_merge(
            Menu::CATEGORIES['projects'],
            Menu::CATEGORIES['labtests'],
            Menu::CATEGORIES['elements']
        ),
        'disabled' => false,
        'active' => false,
        'hasActiveChild' => false
    ];
}

array_push($items,
    $projectRoutes,
    [
        'slug' => 'reports/{any}',
        'href' => 'reports/list',
        'text' => 'Reports',
        'tooltip' => __('Reports'),
        'icon' => 'q4bikon-reports',
        'resource' => 'Controller_Reports',
        'priority' => Enum_UserPriorityLevel::Project,
        'deepLevel' => 1,
        'showIn' => MENU::CATEGORIES['reports'],
        'disabled' => false,
        'active' => false,
        'hasActiveChild' => false
    ],
    [
        'slug' => 'consultants',
        'href' => 'consultants',
        'text' => 'Users',
        'tooltip' => __('Users'),
        'icon' => 'q4bikon-public',
        'resource' => 'Controller_Consultants',
        'priority' => Enum_UserPriorityLevel::Company,
        'deepLevel' => 1,
        'showIn' => [
            'consultants/{any}'
        ],
        'disabled' => false,
        'active' => false,
        'hasActiveChild' => false
    ],
    [
        'slug' => 'settings',
        'href' => 'settings',
        'text' => 'Settings',
        'tooltip' => __('Settings'),
        'icon' => 'q4bikon-settings2',
        'resource' => 'Controller_Settings',
        'priority' => Enum_UserPriorityLevel::General,
        'deepLevel' => 1,
        'showIn' => [
            'settings/{any}'
        ],
        'disabled' => false,
        'active' => false,
        'hasActiveChild' => false
    ]
);

if(Usr::role() === 'super_admin') {
  $items[] = [
      'slug' => 'info-center',
      'href' => 'info-center',
      'text' => 'info_center',
      'tooltip' => 'Info Center',
      'icon' => 'info-center-icon',
      'resource' => 'Controller_Settings',
      'priority' => Enum_UserPriorityLevel::Project,
      'deepLevel' => 1,
      'showIn' => [
          'info-center/{any}'
      ],
      'disabled' => false,
      'active' => false,
      'hasActiveChild' => false
  ];
}


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
            <?=Menu::createSideBar($items)?>
        </ul>
    </nav>
    <?=Security::mousetrapRandLink()?>
</div>

<div class="sidebar_mobile">
    <div class="sidebar_mobile_header">
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
        <div class="logo">
            <div class="wrap-logo">
                <a href="/" title="logo"><img src="/media/img/logo_variation1-1-t.png" alt="logo"></a>
            </div>
        </div>
        <div class="wrap_sidebar_close">
            <i class="q4bikon-close1 close_mobile_sidebar"></i>
<!--            <span class="close_mobile_sidebar">X</span>-->
        </div>
    </div>


    <div class="search-mobile">
        <div class="sidebar-profile-options get-modal get-user-profile" data-url="<?=URL::site('user/profile')?>">
            <div class="sidebar-mobile-profile-img">
                <img src="/media/img/profile.png" alt="profile picture">
            </div>
            <div class="mobile-profile-name">
                <span class="f_name"><?=Auth::instance()->get_user()->name?></span>
            </div>
            <div class="mobile-profile-role"><?=__(Usr::role())?></div>
        </div>
<!--        <ul class="profile-drop-list">-->
<!--            <li><a href="#" class="get-modal get-user-profile" data-url="--><?//=URL::site('user/profile')?><!--">--><?//=__('My Profile')?><!--</a></li>-->
<!--            <li><a href="--><?//=URL::site('logout')?><!--" >--><?//=__('Logout')?><!--</a></li>-->
<!--        </ul>-->
        <?=Security::mousetrapRandLink()?>

    </div>
    <nav class="sidebar-nav">
        <ul>
            <?=Menu::createSideBar($items)?>
            <li>
                <a
                    onclick="sessionStorage.clear();"
                    href="<?=URL::site('logout')?>"
                    class="sidebar-items"
                    title="<?=__('Logout')?>"
                >
                    <i class="fw-600 icon q4bikon-exit"></i>
                    <div class='sidebar-items_content'>
                        <span class="sidebar-items_title"><?=__('Logout')?></span>
                    </div>
                </a>
            </li>
        </ul>
    </nav>
<!--end responsive sidebar-->
</div>