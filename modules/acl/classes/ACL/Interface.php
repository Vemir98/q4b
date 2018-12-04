<?php defined('SYSPATH') or die('No direct script access.');
/**
 *  CRM ACL interface
 *
 * @package    CRM
 * @author     HorizonDVP Team
 * @version    1.0.0
 * @copyright  (c) 2014-2015 CRM Yes Planet Technologies
 * @license    CRM Yes Planet License
 */
interface ACL_Interface {
    
    function add_role($role,$parents);
    function add_resource($resources);
    function allow($role,$resource);
    function deny($role,$resource);
    function is_allowed($role,$resource,$privilege);
}