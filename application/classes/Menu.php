<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 25.05.2021
 * Time: 10:53
 */

class Menu
{
    public static function setActiveItems(&$items, $hasParent = false){
        foreach ($items as $key => &$item){

            $hrefWithoutQuery = explode('?', $item['href'])[0];
            if(strpos($hrefWithoutQuery,':') !== false){
                preg_match('~\/?\:(?<param>[^/]+)~',$hrefWithoutQuery,$matches);

                if(!empty($matches['param'])){
                    if(!empty(Request::current()->param($matches['param']))) {
                        $item['slug'] = str_replace(':' . $matches['param'],Request::current()->param($matches['param']),$item['slug']);
                        $item['href'] = str_replace(':' . $matches['param'],Request::current()->param($matches['param']),$item['href']);

                        foreach ($item['showIn'] as $routeKey => $route) {
                            $item['showIn'][$routeKey] = str_replace(':' . $matches['param'],Request::current()->param($matches['param']),$item['showIn'][$routeKey]);
                        }
                    }
                }else{
                    $item['disabled'] = true;
                }
            }

            $showTab = false;

            foreach ($item['showIn'] as $routeKey => $route) {
//                if((!empty($item['slug']) AND strpos(URL::site(Request::detect_uri(), TRUE) . URL::query(),URL::site($route)) !== false)) {
                if((!empty($item['slug']) AND Request::detect_uri() === URL::site($route))) {
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