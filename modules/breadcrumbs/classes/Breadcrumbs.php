<?php defined('SYSPATH') or die('No direct script access.');
/**
 *  Breadcrumbs class
 *
 * @package    CRM
 * @author     HorizonDVP Team
 * @version    1.0.0
 * @copyright  (c) 2014-2015 CRM Yes Planet Technologies
 * @license    CRM Yes Planet License
 */
class Breadcrumbs {

 	/**
    * Breadcrumbs
    * @var array
    */
    private static $breadcrumbs = array();
    
    /**
    * Clear
    * @var array
    */
    public static function clear()
    {
        self::$breadcrumbs = array();
    }
    
    /**
    * Get
    *
    * @return array Breadcrumbs
    */
    public static function get()
    {
        return self::$breadcrumbs;
    }

    /**
     * Get last crumb title
     *
     * @return string last crumb title
     */
    public static function get_last_crumb_title()
    {
        return (count(self::$breadcrumbs)) ? self::$breadcrumbs[count(self::$breadcrumbs)-1]->get_title() : null;
    }
    
    /**
    * Add
    * @param Breadcrumb $crumb
    * 
    * @return void
    */
    public static function add(Breadcrumb $crumb)
    {
        array_push(self::$breadcrumbs, $crumb);
    }

    /**
     * Render breadcrumbs
     *
     * @param string $template path to breadcrumb template
     * @return string
     */
    public static function render($template = "breadcrumbs/layout")
    {
        return View::make('breadcrumbs/layout')->set('breadcrumbs', self::$breadcrumbs)->render();
    }
}