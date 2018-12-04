<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 12.08.2016
 * Time: 16:35
 */
class Model_Acl extends Zend_Acl
{
    //TODO:: Добавить кэширование
    public function __construct()
    {
        $resPriv = $this->getResourcesWithPrivileges();
        $roles = $this->getRoles();
        $rolesPriv = $this->getRolesPrivileges();
        $resources = [];
        $res = [];
        foreach ($resPriv as $r){
            $resources[$r['res_priv_id']] = [
                'res_name' => $r['res_name'],
                'res_alias' => $r['res_alias'],
                'priv_alias' => $r['priv_alias'],
                'priv_name' => $r['priv_name']
            ];
            if( !array_key_exists($r['res_alias'],$res)){
                $res[$r['res_alias']] = 1;
                $this->addResource(new Zend_Acl_Resource($r['res_alias']));
            }

        }

        foreach ($roles as $r){
            $this->addRole(new Zend_Acl_Role($r['name']));
        }
        foreach ($rolesPriv as $r){
            $this->allow($roles[$r['role_id']]['name'],$resources[$r['res_priv_id']]['res_alias'],$resources[$r['res_priv_id']]['priv_alias']);
        }
    }

    public function getResourcesWithPrivileges(){
        return DB::query(Database::SELECT,' 
            SELECT r.alias res_alias, r.name res_name, p.alias priv_alias, p.name priv_name, rp.id res_priv_id
            FROM resources r
            INNER JOIN resources_privileges rp
            ON r.id = rp.resource_id
            INNER JOIN `privileges` p
            ON rp.privilege_id = p.id
        ')->execute()->as_array();
    }

    public function getRoles(){
        return DB::query(Database::SELECT,'SELECT id, `name` FROM roles')->execute()->as_array('id');
    }

    public function getRolesPrivileges(){
        return DB::query(Database::SELECT,'
                  SELECT r.id role_id, rrp.resource_privilege_id res_priv_id 
                  FROM roles r, roles_resources_privileges rrp
                  WHERE r.id = rrp.role_id
                  ')->execute()->as_array();
    }
}