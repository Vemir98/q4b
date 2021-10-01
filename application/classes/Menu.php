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
            'projects/update/:projectId?tab=certificates',
            'projects/update/:projectId/tasks',
            'plans/update/:projectId',
            'labtests/project/:projectId/elements'
        ],
        'labtests' => [
            'labtests/project/:projectId',
        ],
        'elements' => [
            'labtests/project/:projectId/elements_list',
            'labtests/project/:projectId/elements_type',
            'reports/approve_element/:projectId'
        ]
    );

    public static function setActiveItems(&$items, $hasParent = false){
        foreach ($items as $key => &$item){
            foreach ($item['showIn'] as $routeKey => $route) {
                if(strpos($route,':') !== false){
                    preg_match('~\/?\:(?<param>[^/|?]+)~',$route,$matches);
                    if(!empty($matches['param'])){
                        if(!empty(Request::current()->param($matches['param']))) {
                            if($route === $item['slug']) {
                                $item['slug'] = str_replace(':' . $matches['param'],Request::current()->param($matches['param']),$item['slug']);
                            }
                            if($route === $item['href']) {
                                $item['href'] = str_replace(':' . $matches['param'],Request::current()->param($matches['param']),$item['href']);

                            }
                            $item['showIn'][$routeKey] = str_replace(':' . $matches['param'],Request::current()->param($matches['param']),$item['showIn'][$routeKey]);
                        }
                    }else{
                        $item['disabled'] = true;
                    }
                }
            }

            $showTab = false;

            foreach ($item['showIn'] as $routeKey => $route) {
                if((!empty($item['slug']) AND Request::detect_uri(). URL::query() === URL::site($route))) {
                    $showTab = true;
                    break;
                }
            }


            if(!empty($item['slug']) AND $showTab){
                if(URL::site($item['slug']) === Request::detect_uri(). URL::query()) {
                    $item['active'] = true;
                }
            }else{
                $item['active'] = false;
                if($hasParent){
                    $item['disabled'] = true;
                }
            }

            if(!empty($item['children'])){
               self::setActiveItems($item['children'], true);
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
            $hasIcon = $hasParent ? '' : $item->icon;


            $result .= "<a href='$href' class='$hasSubMenu sidebar-items $isChild $isActive $isDisabled' title='$item->tooltip'>";

            if(!$hasParent) {
                $result .=      "<i class='fw-600 icon $hasIcon' style='font-weight: 600'></i>";
            }

            $result .=      "<span class='sidebar-items_title'>".__($item->text)."</span>";
            $result .= "</a>";

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