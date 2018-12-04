<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 15.02.2018
 * Time: 17:23
 */
class Controller_Api_Auth extends HDVP_Controller_API
{
    protected $_checkToken = false;

    /**
     * @title Получение токена
     * @desc Авторизация пользователя и создание <b>токена</b> для последующих запросов к API.
     * при каждой авторизации пользователя, для него генерируется новый токен и все данные
     * пришедшие до этого от клиента обнуляются.
     * После успешной авторизации необходимо сохранить полученный токен у себя на устройстве и в последующем использовать его при запросах к API
     * @param username - Email пользователя
     * @param password - пароль пользователя
     * @return Возвращает данные о пользователе, роль пользователя и его привилегии
     * @method POST
     * @url http://constructmngr/api/json/v1/login
     * @throws API_Exception 500
     * @response sdfdsf
     */
    public function action_login(){
        if($this->request->method() != Request::POST){
            throw new API_Exception('Incorrect Request');
        }
        $data = Arr::extract($_POST,['username','password']);
        if(empty($data['username']) OR empty($data['password'])){
            throw new API_Exception('Incorrect Username or Password');
        }
        if($this->_auth->login($data['username'],$data['password'])) {
            if ($this->_auth->get_user()->status == Enum_UserStatus::Blocked) {
                $this->_auth->logout(TRUE);
                throw new API_Exception('Your Account is deactivated! Please contact Your Manager');
            } else {
                $this->_user = $this->_auth->get_user();
                $utkn = Model_UToken::makeApplicationToken($this->_user->id);
                $permissions = [];
                $this->_responseData['user'] = Arr::toCamelCase($this->_user->as_array());
                unset($this->_responseData['user']['id'], $this->_responseData['user']['password'], $this->_responseData['user']['logins']);
                $this->_responseData['user']['token'] = $utkn->as_array()['token'];
                $this->_responseData['user']['role'] = $this->_user->getRelevantRole()->as_array();
                unset($this->_responseData['role']['id']);

                $acl = HDVP_Core::instance()->acl();
                $privileges = ORM::factory('ACL_Privilege')->find_all();
                foreach ($privileges as $priv) {
                    foreach ($acl->getResources() as $res) {
                        if ($this->_user->can($priv->alias, $res)) {
                            $permissions[$priv->alias][] = strtolower(str_ireplace('Controller_','',$res));
                        }
                    }
                }
                $permissions = array_diff($permissions,array(''));

                $this->_responseData['user']['permissions'] = $permissions;
            }
        }else{
            throw API_Exception::factory(500,'Incorrect login data');
            //$this->_setErrors('Invalid username or password');
        }
    }
}