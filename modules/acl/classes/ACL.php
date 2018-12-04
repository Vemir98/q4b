<?php defined('SYSPATH') or die('No direct script access.');
/**
 *  ACL class provides a lightweight and flexible access control list for CRM Yes Planet  
 * (ACL)implementation for privileges management. In general, an application 
 *  may utilize such ACL's to control access to certain protected objects 
 *
 * @package    CRM
 * @author     HorizonDVP Team
 * @version    1.0.0
 * @copyright  (c) 2014-2015 CRM Yes Planet Technologies
 * @license    CRM Yes Planet License
 */
class ACL implements ACL_Interface {
    
    /**
     * Rule type deny
     */
    const DENY = FALSE;
    
    /**
     * Rule type allow
     */
    const ALLOW = TRUE;
    
    /**
     * Rule type deny that user set
     */
    const PREM_DENY = 'deny';
    
    /**
     * Rule type allow that user set
     */
    const PERM_ALLOW = 'allow';
    
    /**
     * All priveleges key
     */
    const ALL_PRIVELEGES = '*_all_priveleges';
    
    protected $roles,$resources,$access = array();
    
    /**
     * Adding a new resource to ACL
     * @param mixed $resources  resources
     * @example     $acl->add_resource('clients');
     *              $acl->add_resource('members');
     *              $acl->add_resource('welcome');
     *   
     * @return void
     */
    public function add_resource($resources){
        
        if(is_string($resources)){
            
			$this->resources[$resources] = array();
		}elseif(is_array($resources)){
		  
			foreach($resources as $resource)
			{
				$this->resources[$resource] = array();
			}
		}
    }
    
    /**
     * Adding roles to ACL
     * @param string    $role       role
     * @param mixed     $parents    parent roles
     * @example $acl->add_role('guest');
     *          $acl->add_role('agent','guest');
     *          $acl->add_role('venue_manager','agent');
     *          $acl->add_role('admin','venue_manager');
     *          $acl->add_role('developer','admin');
     * 
     * @return void
     */
    public function add_role($role,$parents = NULL){
        
        if(is_null($parents)){
            
            $this->roles[$role] = array();
        }
        elseif(is_string($parents)){
            
			if($parents == '')
			{
				$this->roles[$role] = array();
			}else{
				
                $this->roles[$role][] = $parents;
			}
			
		}elseif(is_array($parents) AND !empty($parents)){
			
            foreach($parents as $parent)
			{
				$this->roles[$role][] = $parent;
			}
		}else{
			throw new Exception('If role has parents they must be a string or an array of strings');
		}
    }
    
    /**
     * Sets the access to the resource
     * @param string    $role       role
     * @param string    $resource   resource
     * @param string    $access     access type [optional]
     * 
     * @return void
     */
    protected function set_access($role,$resource = null,$privileges = null,$access = self::DENY){
        
        if(!$this->role_exist($role)) return;
        
        //set concrete privileges to concrete resource 
		if(!empty($resource) AND !empty($privileges)){ 
    		//check - whether that resource exists
            if($this->resource_exist($resource)){
                //set access to group of privileges
                if(is_array($privileges)){
                    foreach($privileges as $privilege){
                        $this->access[$role][$resource][$privilege] = $access;
                    }
                //set access to one privilege
                }elseif(is_string($privileges)){
                    $this->access[$role][$resource][$privileges] = $access;
                }  
    		}
        //set to all resources concrete privileges
		}elseif(empty($privileges)){
		    $this->access[$role][$resource][self::ALL_PRIVELEGES] = (self::DENY === $access) ? self::DENY : self::ALLOW;
		}
		
	}
    
    /**
     * Checks that role exists
     */
	protected function role_exist($role){
	   
		return array_key_exists($role,$this->roles);
	}
	
	/**
     * Checks that resource exists
     */
	protected function resource_exist($resource){
		
        return array_key_exists($resource,$this->resources);
	}
    
    /**
     * Allow role to use current resource privileges 
     * @param string     $role          role
     * @param mixed      $resource      resource
     * @example     $this->_acl->allow('guest',array(
     *                                           'members' => array('index','login','logout','register','activate'),
     *                                           'welcome'));
     *              $this->_acl->allow('agent',array(
     *                                           'clients' => array('index','contacts')));
     * 
     * 
     * @return void
     */
    function allow($role,$resources){
        
        if(is_string($resources)){
            
			$this->set_access($role,$resources,null,self::ALLOW);
		}else if(is_array($resources)){
		  
			foreach($resources as $key => $val){
                        
		        if(preg_match('~^[0-9]+$~',$key)){
		            
		            $this->set_access($role,$val,null,self::ALLOW);
		        }else{
		            
		            $this->set_access($role,$key,$val,self::ALLOW);
		        }
		    }
		}
    }
    
    /**
     * Deny role to use current resource privileges 
     * @param string     $role          role
     * @param mixed      $resource      resource
     * 
     * @return void
     */
    function deny($role,$resources){
        
        if(is_string($resources)){
            
			$this->set_access($role,$resources,null,self::DENY);
		}else if(is_array($resources)){
		  
			foreach($resources as $key => $val){
                        
		        if(preg_match('~^[0-9]+$~',$key)){
		            
		            $this->set_access($role,$val,null,self::DENY);
		        }else{
		            
		            $this->set_access($role,$key,$val,self::DENY);
		        }
		    }
		}
    }
    
    /**
     * Checks current role privileges to access a resource
     * @param string    $role       role
     * @param string    $resource   resource
     * @param string    $privilege  privileges [optional]
     */
    function is_allowed($role,$resource,$privilege){
        
        //On first step we check that the resource & role exist
		if($this->role_exist($role) && $this->resource_exist($resource))
		{
			//He has access to something
			if(array_key_exists($role,$this->access))
			{
				//Maybe to this resource
				if(array_key_exists($resource,$this->access[$role]))
				{
					if(array_key_exists($privilege,$this->access[$role][$resource])){
                            //return his status allowed[true] or denied[false]
                            return $this->access[$role][$resource][$privilege];                            
                    }
                    //var_dump($this->access[$role][$resource]);
                    if(isset($this->access[$role][$resource][self::ALL_PRIVELEGES])){
                        return $this->access[$role][$resource][self::ALL_PRIVELEGES];
                    }
                    
                    
                    			
				}			
			}
			
			//Maybe a parent...?
			if(count($this->roles[$role]) > 0)
			{
				//We ask his parents				
				foreach($this->roles[$role] as $parent)
				{
					//We go deeper in the rabbit hole...
					if($this->is_allowed($parent,$resource,$privilege))
					{
						return true;
					}
				}
			}			
		}
		//If we arrive here it means that he's not allowed
		return false;
    }
}