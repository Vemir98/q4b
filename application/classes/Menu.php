<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 25.05.2021
 * Time: 10:53
 */

class Menu
{
    const CATEGORIES = array(
        'dashboard' => [
            'dashboard'
        ],
        'companies' => [
            'companies/update/:id?tab=info',
            'companies/update/:id?tab=instructions',
            'companies/update/:id?tab=professions',
            'companies/update/:id?tab=specialities',
            'companies/update/:id?tab=users',
        ],
        'projects' => [
            'projects/update/:projectId?tab=info',
            'projects/update/:projectId?tab=structures',
            'projects/update/:projectId?tab=delivery-items',
            'projects/update/:projectId?tab=users',
            'projects/update/:projectId?tab=certificates_old',
            'projects/update/:projectId?tab=certificates',
            'projects/update/:projectId/tasks',
            'plans/update/:projectId',
            'labtests/project/:projectId/elements',
        ],
        'labtests' => [
            'labtests/create/:projectId',
            'labtests/project/:projectId',
            'labtests/project/:projectId/edit/:id'
        ],
        'elements' => [
            'labtests/project/:projectId/elements_list',
            'labtests/project/:projectId/elements_type',
            'reports/approve_element/:projectId',
            'reports/approve_element/:projectId/create',
        ],
        'reports' => [
            'reports',
            'reports/list',
            'reports/approve_element',
            'reports/quality/{any}',
            'reports/tasks',
            'reports/place',
            'reports/delivery',
            'reports/generate/{any}',
            'reports/labtests/{any}',
        ]
    );

    public static function setActiveItems(&$items, &$parentItem = null){
        foreach ($items as $key => &$item){
            foreach ($item['showIn'] as $routeKey => $route) {
                if(strpos($route,':') !== false){
                    preg_match_all('~\/?\:(?<param>[^/|?]+)~',$route,$matches);

                    if(!empty($matches['param'])){
                            foreach ($matches['param'] as $matchedParam) {
                                $hasAny = false;
                                if(strpos($route,'/{any}') !== false) {
                                    $anyExploded = explode('/{any}',$route);
                                    $explodedRoute = explode(':', $anyExploded[0]);
                                    $hasAny = true;
                                } else {
                                    $explodedRoute = explode(':', $route);
                                }

                                foreach ($explodedRoute as $routePartKey => $routePart) {
                                    $explodedRoutePart = explode('/', $routePart);
                                    if(strpos($explodedRoutePart[0],'?') !== false) {
                                        $hasQueryParams = explode('?', $explodedRoutePart[0]);
                                        if(!empty(Request::current()->param($matchedParam)) && !empty(Request::current()->param($hasQueryParams[0]))) {
                                            $hasQueryParams[0] = Request::current()->param($hasQueryParams[0]);
                                            $explodedRoutePart[0] = implode('?', $hasQueryParams);
                                        }
                                    } else {
                                        if(!empty(Request::current()->param($matchedParam)) && !empty(Request::current()->param($explodedRoutePart[0]))) {
                                            $explodedRoutePart[0] = Request::current()->param($explodedRoutePart[0]);
                                        }
                                    }
                                    $explodedRoute[$routePartKey] = implode('/', $explodedRoutePart);
                                }
                                if(strpos($item['slug'],'/{any}') !== false) {
                                    $item['slug'] = implode('', $explodedRoute).($hasAny ? '/{any}' : '');
                                } else {
                                    if($route === $item['slug']) {
                                        $item['slug'] = implode('', $explodedRoute).($hasAny ? '/{any}' : '');
                                    }
                                }

                                if($route === $item['href']) {
                                    $item['href'] = implode('', $explodedRoute).($hasAny ? '/{any}' : '');
                                }
                                $item['showIn'][$routeKey] = implode('', $explodedRoute).($hasAny ? '/{any}' : '');
                            }
                    }else{
                        $item['disabled'] = true;
                    }
                }
            }

            $showTab = false;

            foreach ($item['showIn'] as $routeKey => &$route) {
                if(strpos($route,'/{any}') !== false) {
                    $route = explode('/{any}', $route)[0];

                    if(!empty($item['slug']) && strpos(Request::detect_uri(). URL::query(), URL::site($route)) === 0) {
                        $showTab = true;
                        break;
                    }
                } else {
                    if((!empty($item['slug']) AND Request::detect_uri(). URL::query() === URL::site($route))) {

                        $showTab = true;
                        break;
                    }
                }
            }


            if(!empty($item['slug']) AND $showTab) {

                if(strpos($item['slug'],'/{any}') !== false) {
                    $item['slug'] = explode('/{any}', $item['slug'])[0];
                    if(strpos(Request::detect_uri(). URL::query(), URL::site($item['slug'])) === 0) {
                        $item['active'] = true;
                    }
                } else {
                    if(URL::site($item['slug']) === Request::detect_uri(). URL::query()) {
                        $item['active'] = true;
                    }
                }

                if($parentItem) {
                    $parentItem['active'] = true;
                    $parentItem['hasActiveChild'] = true;
                }
            }else{
                $item['active'] = false;
                if($parentItem){
                    $item['disabled'] = true;
                }
            }

            if(!empty($item['children'])){
               self::setActiveItems($item['children'], $item);
            }

            if($item['disabled']){
                $item['slug'] = '#';
                $item['href'] = '#';
            }
        }
    }

    public static function createSideBar($items, $hasParent = false) {
        $result = "";
        foreach ($items as $item) {
            $result .= "<li>";

            $href = !empty($item->href) ? URL::site($item->href) : '#';
            $hasSubMenu = (isset($item->children) ? 'has-submenu' : '');
            $isActive = $item->active ? 'active' : '';
            $isDisabled = $item->disabled ? 'hidden' : '';
            $isChild = $hasParent ? 'sub-items' : '';
            $isNestedChild = ($item->deepLevel > 2) ? 'sidebar-sub-item_title' : '';
            $hasIcon = $hasParent ? '' : $item->icon;
            $arrowIcon = '';

            if(isset($item->children)) {
                if(Language::getCurrent()->direction === 'rtl') {
                    $arrowIcon = 'q4bikon-arrow_left1';
                } else {
                    $arrowIcon = 'q4bikon-arrow_right1';
                }
                if($item->hasActiveChild) {
                    $arrowIcon = 'q4bikon-arrow_bottom1';
                }
            }

            $result .= "<a href='$href' class='$hasSubMenu sidebar-items $isChild $isActive $isDisabled' title='$item->tooltip'>";
            $result .=      "<i class='fw-600 icon $hasIcon' style='font-weight: 600'></i>";
            $result .=      "<div class='sidebar-items_content'>
                                <span class='sidebar-items_title $isNestedChild'>".__($item->text)."</span>";

            if($arrowIcon) {
                $result .= "<i class='fw-600 icon $arrowIcon sidebar-item_icon'></i>";
            }

            $result .=   "</div>
                        </a>";

            if(isset($item->children) && count($item->children)) {
                $result .= "<ul class='submenu'>";
                $result .= self::createSideBar($item->children, true);
                $result .= "</ul>";
            }
            $result .= "</li>";
        }
        return $result;
    }
}