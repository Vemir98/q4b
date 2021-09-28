<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 28.12.2016
 * Time: 22:05
 */


//$items = [
//    [
//        'slug' => 'quality_control/create',
//        'text' => 'Create Quality Control',
//        'tooltip' => 'Create Quality Control',
//        'icon' => 'q4bikon-tick',
//        'resource' => 'Controller_QualityControl',
//        'priority' => Enum_UserPriorityLevel::Project,
//        'disabled' => false,
//        'active' => false
//    ],
//    [
//        'slug' => 'labtests',
//        'text' => 'Lab control menu',
//        'icon' => 'q4bikon-lab',
//        'resource' => 'Controller_LabTests',
//        'priority' => Enum_UserPriorityLevel::Project,
//        'children' => [
//            [
//                'slug' => 'labtests/project/:projectId',
//                'text' => 'Lab control list',
//                'resource' => 'Controller_LabTests',
//                'priority' => Enum_UserPriorityLevel::Project,
//                'disabled' => false,
//                'active' => false
//            ],
//            [
//                'slug' => 'labtests/project/:projectId/elements_type',
//                'text' => 'Elements Type',
//                'resource' => 'Controller_LabTests',
//                'priority' => Enum_UserPriorityLevel::Project,
//                'disabled' => false,
//                'active' => false
//            ],
//            [
//                'slug' => 'labtests/project/:projectId/elements_list',
//                'text' => 'Elements List',
//                'resource' => 'Controller_LabTests',
//                'priority' => Enum_UserPriorityLevel::Project,
//                'disabled' => false,
//                'active' => false
//            ]
//        ],
//        'disabled' => false,
//        'active' => false
//    ],
//    [
//        'slug' => 'dashboard',
//        'text' => 'Dashboard',
//        'tooltip' => 'Dashboard',
//        'icon' => 'q4bikon-uncheked',
//        'resource' => 'Controller_Dashboard',
//        'priority' => Enum_UserPriorityLevel::Project,
//        'disabled' => false,
//        'active' => false
//    ],
//    [
//        'slug' => 'companies',
//        'text' => 'Companies',
//        'tooltip' => 'Companies',
//        'icon' => 'q4bikon-companies',
//        'resource' => 'Controller_Companies',
//        'priority' => Enum_UserPriorityLevel::Corporate,
//        'disabled' => false,
//        'active' => false
//    ],
//    [
//        'slug' => 'projects',
//        'text' => 'Menu _Projects',
//        'tooltip' => 'Tooltip _Projects',
//        'icon' => 'q4bikon-project',
//        'resource' => 'Controller_Projects',
//        'priority' => Enum_UserPriorityLevel::Project,
//        'children' => [
//            [
//                'slug' => 'projects/update/:id/tasks',
//                'text' => 'Tasks',
//                'resource' => 'Controller_Projects',
//                'priority' => Enum_UserPriorityLevel::Project,
//                'disabled' => false,
//                'active' => false
//            ]
//        ],
//        'disabled' => false,
//        'active' => false
//    ],
//    [
//        'slug' => 'plans',
//        'text' => 'Plans',
//        'tooltip' => 'Plans',
//        'icon' => 'q4bikon-file',
//        'resource' => 'Controller_Plans',
//        'priority' => Enum_UserPriorityLevel::Project,
//        'disabled' => false,
//        'active' => false
//    ],
//    [
//        'slug' => 'reports/list',
//        'text' => 'Reports',
//        'tooltip' => 'Reports',
//        'icon' => 'q4bikon-reports',
//        'resource' => 'Controller_Reports',
//        'priority' => Enum_UserPriorityLevel::Project,
//        'disabled' => false,
//        'active' => false
//    ],
//    [
//        'slug' => 'consultants',
//        'text' => 'Menu_Consultants And Auditors',
//        'tooltip' => 'Tooltip_Consultants And Auditors',
//        'icon' => 'q4bikon-public',
//        'resource' => 'Controller_Consultants',
//        'priority' => Enum_UserPriorityLevel::Company,
//        'disabled' => false,
//        'active' => false
//    ],
//    [
//        'slug' => 'settings',
//        'text' => 'Settings',
//        'tooltip' => 'Settings',
//        'icon' => 'q4bikon-settings2',
//        'resource' => 'Controller_Settings',
//        'priority' => Enum_UserPriorityLevel::General,
//        'disabled' => false,
//        'active' => false
//    ],
//    [
//        'slug' => '',
//        'text' => 'Archive',
//        'icon' => 'q4bikon-archive',
//        'resource' => 'Controller_Archive',
//        'priority' => Enum_UserPriorityLevel::Company,
//        'disabled' => false,
//        'active' => false
//    ],
//
//];


$items = [
    [
        'slug' => 'dashboard',
        'href' => 'dashboard',
        'text' => 'Dashboard1',
        'tooltip' => 'Dashboard1',
        'icon' => 'q4bikon-uncheked',
        'resource' => 'Controller_Dashboard',
        'priority' => Enum_UserPriorityLevel::Project,
        'showIn' => [
                'dashboard'
        ],
        'disabled' => false,
        'active' => false,
    ],
    [
        'slug' => 'companies',
        'href' => 'companies',
        'text' => 'Companies',
        'tooltip' => 'Companies',
        'icon' => 'q4bikon-companies',
        'resource' => 'Controller_Companies',
        'priority' => Enum_UserPriorityLevel::Corporate,
        'children' => [
            [
                'slug' => 'companies/update/:id?tab=info',
                'href' => 'companies/update/:id?tab=info',
                'text' => 'Info',
                'resource' => 'Controller_Companies',
                'priority' => Enum_UserPriorityLevel::Project,
                'showIn' => [
                    'companies/update/:id'
                ],
                'disabled' => false,
                'active' => false,
            ],
            [
                'slug' => 'companies/update/:id?tab=specialities',
                'href' => 'companies/update/:id?tab=specialities',
                'text' => 'Crafts',
                'resource' => 'Controller_Companies',
                'priority' => Enum_UserPriorityLevel::Project,
                'showIn' => [
                    'companies/update/:id'
                ],
                'disabled' => false,
                'active' => false,
            ],
            [
                'slug' => 'companies/update/:id?tab=professions',
                'href' => 'companies/update/:id?tab=professions',
                'text' => 'Professions',
                'resource' => 'Controller_Companies',
                'priority' => Enum_UserPriorityLevel::Project,
                'showIn' => [
                    'companies/update/:id'
                ],
                'disabled' => false,
                'active' => false,
            ],
            [
                'slug' => 'companies/update/:id?tab=instructions',
                'href' => 'companies/update/:id?tab=instructions',
                'text' => 'Instructions',
                'resource' => 'Controller_Companies',
                'priority' => Enum_UserPriorityLevel::Project,
                'showIn' => [
                    'companies/update/:id'
                ],
                'disabled' => false,
                'active' => false,
            ],
            [
                'slug' => 'companies/update/:id?tab=users',
                'href' => 'companies/update/:id?tab=users',
                'text' => 'Users',
                'resource' => 'Controller_Companies',
                'priority' => Enum_UserPriorityLevel::Project,
                'showIn' => [
                    'companies/update/:id'
                ],
                'disabled' => false,
                'active' => false,
            ],
        ],
        'showIn' => [
            'companies'
        ],
        'disabled' => false,
        'active' => false,
    ],
];

$projectRoutes = [
    'slug' => 'projects',
    'href' => 'projects',
    'text' => 'Menu _Projects',
    'tooltip' => 'Tooltip _Projects',
    'icon' => 'q4bikon-project',
    'resource' => 'Controller_Projects',
    'priority' => Enum_UserPriorityLevel::Project,
    'children' => [
        [
            'slug' => 'projects/update/:projectId?tab=info',
            'href' => 'projects/update/:projectId?tab=info',
            'text' => 'Info',
            'resource' => 'Controller_Projects',
            'priority' => Enum_UserPriorityLevel::Project,
            'showIn' => [
                'projects/update/:projectId',
                'projects/update/:projectId/tasks',
                'labtests/project/:projectId',
                'plans/update/:projectId',
                'labtests/project/:projectId/elements',
                'labtests/project/:projectId/elements_list',
                'labtests/project/:projectId/elements_type',
            ],
            'disabled' => false,
            'active' => false,
        ],
    ],
    'showIn' => [
        'projects'
    ],
    'disabled' => false,
    'active' => false,
];

//if(Usr::can(Usr::TASKS_PERM)) {
    $projectRoutes['children'][] = [
        'slug' => 'projects/update/:projectId/tasks',
        'href' => 'projects/update/:projectId/tasks',
        'text' => 'Tasks',
        'resource' => 'Controller_Projects',
        'priority' => Enum_UserPriorityLevel::Project,
        'showIn' => [
            'projects/update/:projectId',
            'projects/update/:projectId/tasks',
            'labtests/project/:projectId',
            'plans/update/:projectId',
            'labtests/project/:projectId/elements',
            'labtests/project/:projectId/elements_list',
            'labtests/project/:projectId/elements_type',
        ],
        'disabled' => false,
        'active' => false,
    ];
//}

if(!Auth::instance()->get_user()->is('project_adviser')) {
    $projectRoutes['children'][] = [
        'slug' => 'projects/update/:projectId?tab=structures',
        'href' => 'projects/update/:projectId?tab=structures',
        'text' => 'Structures',
        'resource' => 'Controller_Projects',
        'priority' => Enum_UserPriorityLevel::Project,
        'showIn' => [
            'projects/update/:projectId',
            'projects/update/:projectId/tasks',
            'labtests/project/:projectId',
            'plans/update/:projectId',
            'labtests/project/:projectId/elements',
            'labtests/project/:projectId/elements_list',
            'labtests/project/:projectId/elements_type',
        ],
        'disabled' => false,
        'active' => false,
    ];
}

if(Usr::role() == 'super_admin') {
    $projectRoutes['children'][] = [
        'slug' => 'projects/update/:projectId?tab=delivery-items',
        'href' => 'projects/update/:projectId?tab=delivery-items',
        'text' => 'Delivery Items1',
        'resource' => 'Controller_Projects',
        'priority' => Enum_UserPriorityLevel::Project,
        'showIn' => [
            'projects/update/:projectId',
            'labtests/project/:projectId',
            'projects/update/:projectId/tasks',
            'plans/update/:projectId',
            'labtests/project/:projectId/elements',
            'labtests/project/:projectId/elements_list',
            'labtests/project/:projectId/elements_type',
        ],
        'disabled' => false,
        'active' => false,
    ];
}

if(!Auth::instance()->get_user()->is('project_adviser')) {
    $projectRoutes['children'][] = [
        'slug' => 'projects/update/:projectId?tab=certificates',
        'href' => 'projects/update/:projectId?tab=certificates',
        'text' => 'Certificates',
        'resource' => 'Controller_Projects',
        'priority' => Enum_UserPriorityLevel::Project,
        'showIn' => [
            'projects/update/:projectId',
            'projects/update/:projectId/tasks',
            'labtests/project/:projectId',
            'plans/update/:projectId',
            'labtests/project/:projectId/elements',
            'labtests/project/:projectId/elements_list',
            'labtests/project/:projectId/elements_type',
        ],
        'disabled' => false,
        'active' => false,
    ];
    if(!Auth::instance()->get_user()->is('project_supervisor')) {
        $projectRoutes['children'][] = [
            'slug' => 'projects/update/:projectId?tab=users',
            'href' => 'projects/update/:projectId?tab=users',
            'text' => 'Users',
            'resource' => 'Controller_Projects',
            'priority' => Enum_UserPriorityLevel::Project,
            'showIn' => [
                'projects/update/:projectId',
                'projects/update/:projectId/tasks',
                'labtests/project/:projectId',
                'plans/update/:projectId',
                'labtests/project/:projectId/elements',
                'labtests/project/:projectId/elements_list',
                'labtests/project/:projectId/elements_type',
            ],
            'disabled' => false,
            'active' => false,
        ];
    }
}



$projectRoutes['children'][] = [
    'slug' => 'plans/update/:projectId',
    'href' => 'plans/update/:projectId',
    'text' => 'Plans',
    'icon' => 'q4bikon-file',
    'resource' => 'Controller_Plans',
    'priority' => Enum_UserPriorityLevel::Project,
    'showIn' => [
        'projects/update/:projectId',
        'projects/update/:projectId/tasks',
        'labtests/project/:projectId',
        'plans/update/:projectId',
        'labtests/project/:projectId/elements',
        'labtests/project/:projectId/elements_list',
        'labtests/project/:projectId/elements_type',
    ],
    'disabled' => false,
    'active' => false,
];

$projectRoutes['children'][] = [
    'slug' => 'labtests',
    'href' => 'labtests/project/:projectId',
    'text' => 'Lab control menu',
    'icon' => 'q4bikon-lab',
    'resource' => 'Controller_LabTests',
    'priority' => Enum_UserPriorityLevel::Project,
    'children' => [
        [
            'slug' => 'labtests/create/:projectId',
            'href' => 'labtests/create/:projectId',
            'text' => 'Create Lab Control',
            'resource' => 'Controller_LabTests',
            'priority' => Enum_UserPriorityLevel::Project,
            'showIn' => [
                'labtests/project/:projectId',
            ],
            'disabled' => false,
            'active' => false,
        ],
        [
            'slug' => 'labtests/project/:projectId',
            'href' => 'labtests/project/:projectId',
            'text' => 'Lab control list',
            'resource' => 'Controller_LabTests',
            'priority' => Enum_UserPriorityLevel::Project,
            'showIn' => [
                'labtests/project/:projectId',
            ],
            'disabled' => false,
            'active' => false,
        ],
    ],
    'showIn' => [
        'projects/update/:projectId',
        'projects/update/:projectId/tasks',
        'labtests/project/:projectId',
        'plans/update/:projectId',
        'labtests/project/:projectId/elements',
        'labtests/project/:projectId/elements_list',
        'labtests/project/:projectId/elements_type',
    ],
    'disabled' => false,
    'active' => false,
];

$projectRoutes['children'][] = [
    'slug' => 'elements',
    'href' => 'labtests/project/:projectId/elements_list',
    'text' => 'elements',
    'resource' => 'Controller_LabTests',
    'priority' => Enum_UserPriorityLevel::Project,
    'children' => [
        [
            'slug' => 'labtests/project/:projectId/elements_type',
            'href' => 'labtests/project/:projectId/elements_type',
            'text' => 'Elements Type',
            'resource' => 'Controller_LabTests',
            'priority' => Enum_UserPriorityLevel::Project,
            'showIn' => [
                'labtests/project/:projectId/elements_list',
                'labtests/project/:projectId/elements_type',
            ],
            'disabled' => false,
            'active' => false,
        ],
        [
            'slug' => 'labtests/project/:projectId/elements_list',
            'href' => 'labtests/project/:projectId/elements_list',
            'text' => 'Elements List',
            'resource' => 'Controller_LabTests',
            'priority' => Enum_UserPriorityLevel::Project,
            'showIn' => [
                'labtests/project/:projectId/elements_list',
                'labtests/project/:projectId/elements_type',
            ],
            'disabled' => false,
            'active' => false,
        ],
        [
            'slug' => 'reports/approve_element',
            'href' => 'reports/approve_element',
            'text' => 'Report of Elements',
            'resource' => 'Controller_LabTests',
            'priority' => Enum_UserPriorityLevel::Project,
            'showIn' => [
                'labtests/project/:projectId/elements_list',
                'labtests/project/:projectId/elements_type',
                'reports/list'
            ],
            'disabled' => false,
            'active' => false,
        ],
    ],
    'showIn' => [
        'projects/update/:projectId',
        'projects/update/:projectId/tasks',
        'plans/update/:projectId',
        'labtests/project/:projectId',
        'labtests/project/:projectId/elements',
        'labtests/project/:projectId/elements_list',
        'labtests/project/:projectId/elements_type',
    ],
    'disabled' => false,
    'active' => false,
];


array_push($items,
    $projectRoutes,
    [
        'slug' => 'reports/list',
        'href' => 'reports/list',
        'text' => 'Reports',
        'tooltip' => 'Reports',
        'icon' => 'q4bikon-reports',
        'resource' => 'Controller_Reports',
        'priority' => Enum_UserPriorityLevel::Project,
        'showIn' => [
            'reports/list'
        ],
        'disabled' => false,
        'active' => false,
    ],
    [
        'slug' => 'consultants',
        'href' => 'consultants',
        'text' => 'Users',
        'tooltip' => 'Users',
        'icon' => 'q4bikon-public',
        'resource' => 'Controller_Consultants',
        'priority' => Enum_UserPriorityLevel::Company,
        'showIn' => [
            'consultants'
        ],
        'disabled' => false,
        'active' => false
    ],
    [
        'slug' => 'settings',
        'href' => 'settings',
        'text' => 'Settings',
        'tooltip' => 'Settings',
        'icon' => 'q4bikon-settings2',
        'resource' => 'Controller_Settings',
        'priority' => Enum_UserPriorityLevel::General,
        'showIn' => [
            'settings'
        ],
        'disabled' => false,
        'active' => false,
    ]
);

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
            <a
                href="<?=!empty($i->href) ? URL::site($i->href) : '#'?>"
                class="sidebar-items <?=(!empty($i->href) AND strpos(URL::site(Request::detect_uri(), TRUE) . URL::query(),URL::site($i->href)) !== false) ? 'active' : ''?>"
                title="<?=__($i->tooltip)?>">
                <i class="icon <?=$i->icon?>"></i>
                <span class="sidebar-items_title"><?=__($i->text)?></span>
            </a>
        </li>
        <?endforeach?>
    </ul>
<!--end responsive sidebar-->
</div>