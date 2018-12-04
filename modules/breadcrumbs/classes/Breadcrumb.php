<?php defined('SYSPATH') or die('No direct script access.');
/**
 *  Breadcrumb class
 *
 * @package    CRM
 * @author     HorizonDVP Team
 * @version    1.0.0
 * @copyright  (c) 2014-2015 CRM Yes Planet Technologies
 * @license    CRM Yes Planet License
 */
class Breadcrumb {

	/**
    * Breadcrumb Title
    * @var string
    */
    private $title = "";
    
    /**
    * Breadcrumb Link
    * @var string
    */
    private $url = NULL;
    
    /**
    * Breadcrumb factory
    * 
    * @return Breadcrumb
    */
    public static function factory()
    {
        return new Breadcrumb;
    }
    
    /**
    * Set crumb title
    * @param string $title
    * 
    * @return object
    */
    public function set_title($title = "")
    {
        if ( ! is_string($title) AND ! is_numeric($title) AND ! (is_object($title) AND method_exists($title, "__toString")))
        throw new Kohana_Exception("Breadcrumb title is not numeric or a string.");
        
        $this->title = (string) $title;
        
        return $this;
    }
    
    /**
    * Get crumb title
    * 
    * @return string
    */
    public function get_title()
    {
        //return HTML::chars($this->title);
        return $this->title;
    }
    
    /**
    * Set crumb link
    * 
    * @return object
    */
    public function set_url($url = "")
    {
        $this->url = $url;
        
        return $this;
    }
    
    /**
    * Get crumb ink
    * 
    * @return string
    */
    public function get_url()
    {
        return HTML::chars($this->url);
    }
}