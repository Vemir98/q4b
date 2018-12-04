<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.09.2016
 * Time: 6:18
 */
class Controller_ACL_ResManager extends Controller_Template
{
    public $template = 'acl/res-manager';
    public function action_index(){
        $resources = ORM::factory('ACL_Resource')->find_all();
        $privileges = ORM::factory('ACL_Privilege')->find_all();
        $roles = ORM::factory('Role')->find_all();
        $this->template->content = View::factory('acl/index')->set('resources',$resources)->set('privileges',$privileges)->set('roles',$roles);
    }

    public function action_edit_res(){
        $id = $this->request->param('id');
        $resource = ORM::factory('ACL_Resource',$id);
        if( ! $resource->loaded())throw new HTTP_Exception_404;

        if(!empty($_POST['submit'])){
            try{
                Database::instance()->begin();
                $resource->set('alias',$_POST['alias'])->set('name',$_POST['name'])->save();
                $resource->remove('privileges');
                if(!empty($_POST['privileges'])){
                    foreach ($_POST['privileges'] as $pr){
                        $resource->add('privileges',$pr);
                    }
                }
                Database::instance()->commit();
                $this->redirect(Route::url('acl.resources-manager',[]));
            }catch(ORM_Validation_Exception $e){
                Database::instance()->rollback();
                var_dump($e->errors('validation'));
            }catch (Exception $e){
                if ( ! $e instanceof HTTP_Exception_Redirect){
                    Database::instance()->rollback();
                    var_dump($e->getMessage());
                }else{
                    throw $e;
                }
            }
        }


        $privileges = ORM::factory('ACL_Privilege')->find_all();
        $privList = [];
        if($privileges->count()){
            foreach ($privileges as $pr){
                $privList[$pr->id] = $pr->name;
            }
        }

        $resPriv = $resource->privileges->find_all();
        $selectedPriv = [];
        if($resPriv->count()){
            foreach ($resPriv as $rp){
                $selectedPriv []= $rp->id;
            }
        }
        $this->template->content = View::factory('acl/res_edit')->set('resource',$resource)->set('privileges',$privList)->set('selected',$selectedPriv);
    }

    public function action_add_res(){
        if(!empty($_POST['submit'])){
            $resource = ORM::factory('ACL_Resource');
            $data = Arr::extract($_POST,['alias','name','privileges']);
            try{
                Database::instance()->begin();
                $resource->values(Arr::extract($data,['alias','name']))->save();
                if(!empty($data['privileges'])){
                    foreach ($data['privileges'] as $pr){
                        $resource->add('privileges',$pr);
                    }
                }
                Database::instance()->commit();
                $this->redirect(Route::url('acl.resources-manager',[]));
            }catch(ORM_Validation_Exception $e){
                Database::instance()->rollback();
                var_dump($e->errors('validation'));
            }catch (Exception $e){
                if ( ! $e instanceof HTTP_Exception_Redirect){
                    Database::instance()->rollback();
                    var_dump($e->getMessage());
                }else{
                    throw $e;
                }
            }
        }


        $privileges = ORM::factory('ACL_Privilege')->find_all();
        $privList = [];
        if($privileges->count()){
            foreach ($privileges as $pr){
                $privList[$pr->id] = $pr->name;
            }
        }
        $this->template->content = View::factory('acl/res_add')->bind('data',$data)->set('privileges',$privList);
    }

    public function action_remove_res(){
        $id = $this->request->param('id');
        $privilege = ORM::factory('ACL_Resource',$id);
        if( ! $privilege->loaded())throw new HTTP_Exception_404;
        $privilege->delete();
        $this->redirect(Route::url('acl.resources-manager',[]));
    }

    public function action_add_priv(){
        if(!empty($_POST['submit'])){
            $privilege = ORM::factory('ACL_Privilege');
            $data = Arr::extract($_POST,['alias','name']);
            try{
                $privilege->values(Arr::extract($data,['alias','name']))->save();
                $this->redirect(Route::url('acl.resources-manager',[]));
            }catch(ORM_Validation_Exception $e){
                var_dump($e->errors('validation'));
            }catch (Exception $e){
                if ( ! $e instanceof HTTP_Exception_Redirect){
                    Database::instance()->rollback();
                    var_dump($e->getMessage());
                }else{
                    throw $e;
                }
            }
        }
        $this->template->content = View::factory('acl/priv_add')->bind('data',$data);
    }

    public function action_edit_priv(){
        $id = $this->request->param('id');
        $privilege = ORM::factory('ACL_Privilege',$id);
        if( ! $privilege->loaded())throw new HTTP_Exception_404;

        if(!empty($_POST['submit'])){
            try{
                $privilege->set('alias',$_POST['alias'])->set('name',$_POST['name'])->save();
                $this->redirect(Route::url('acl.resources-manager',[]));
            }catch(ORM_Validation_Exception $e){
                var_dump($e->errors('validation'));
            }catch (Exception $e){
                if ( ! $e instanceof HTTP_Exception_Redirect){
                    var_dump($e->getMessage());
                }else{
                    throw $e;
                }
            }
        }
        $this->template->content = View::factory('acl/priv_edit')->set('privilege',$privilege);
    }

    public function action_remove_priv(){
        $id = $this->request->param('id');
        $resource = ORM::factory('ACL_Privilege',$id);
        if( ! $resource->loaded())throw new HTTP_Exception_404;
        $resource->delete();
        $this->redirect(Route::url('acl.resources-manager',[]));
    }

    public function action_mng_role_priv(){
        $id = $this->request->param('id');
        $role = ORM::factory('Role',$id);
        if( ! $role->loaded())throw new HTTP_Exception_404;

        if(!empty($_POST['submit'])){
            $privs = Arr::get($_POST,'privileges');
            try{
                Database::instance()->begin();
                DB::query(Database::DELETE,'DELETE FROM roles_resources_privileges WHERE roles_resources_privileges.role_id ='.$id)->execute();
                if(!empty($privs)){
                    foreach ($privs as $p){
                        DB::query(Database::INSERT,'INSERT INTO roles_resources_privileges (resource_privilege_id,role_id) VALUES ('.$p.','.$id.')')->execute();
                    }
                }
                Database::instance()->commit();

            }catch(Exception $e){
                Database::instance()->rollback();
                var_dump($e->getMessage());
            }
        }

        $rolePrivs = [];
        $tmpRolePrivs = DB::query(Database::SELECT,'SELECT
  roles_resources_privileges.resource_privilege_id
FROM roles_resources_privileges
  INNER JOIN roles
    ON roles_resources_privileges.role_id = roles.id 
    WHERE role_id = '.$id)->execute()->as_array();
        if(count($tmpRolePrivs)){
            foreach ($tmpRolePrivs as $trp){
                $rolePrivs [] = $trp['resource_privilege_id'];
            }
        }
        $resPrivs = DB::query(Database::SELECT,
            'SELECT
  CONCAT_WS("::",resources.name,`privileges`.name) name,
  resources_privileges.id
FROM resources_privileges
  INNER JOIN `privileges`
    ON resources_privileges.privilege_id = `privileges`.id
  INNER JOIN resources
    ON resources_privileges.resource_id = resources.id ORDER BY name ASC'
        )->execute()->as_array();

        $this->template->content = View::factory('acl/mng_role_priv')
            ->set('role',$role)
            ->set('rolePrivs',$rolePrivs)
            ->set('resPrivs',$resPrivs);
    }
}