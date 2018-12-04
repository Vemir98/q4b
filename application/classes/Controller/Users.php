<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 19.09.2016
 * Time: 22:27
 */
class Controller_Users extends HDVP_Controller_Template
{
    
    public function action_list(){

    }

    /**
     * Создание пользователя
     * todo:: убрать роли стоящие выше по иерархии для менеджеров
     * @throws HTTP_Exception_404
     * @throws HTTP_Exception_Redirect
     * @throws Kohana_Exception
     */
    public function action_create(){


        if($this->request->method() == HTTP_Request::POST)
        {
            $this->_checkForAjaxOrDie();
            $dataRoles = Arr::get($this->post(),'roles');
            $user = ORM::factory('User');
            try{
                Database::instance()->begin();
                if(empty($dataRoles)) throw new Exception('Roles can not be empty');
                $user->create_user($this->post(),['email','username','password']);
                foreach ($dataRoles as $r){
                    $user->add('roles',$r);
                }
                Database::instance()->commit();
                Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $user]);
                $this->makeRedirect('users');
            }catch(ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch(Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }
        }
        else
        {
            $rolesTmp = ORM::factory('Role')->where('name','<>','guest')->find_all();
            $roles = [];
            foreach($rolesTmp as $role){
                $roles[$role->id] = $role->name;
            }

            $this->template->content = View::make('users/create')->set('roles',$roles);
        }
        

    }
}