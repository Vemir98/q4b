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


                $emails = [
                    'avia.maccabi@avney-derech.co.il',
                    'eldar5390@gmail.com',
                    'adirr@sh-av.co.il',
                    'eyal@sh-av.co.il',
                    'ori@sh-av.co.il',
                    'shay.y@avney-derech.co.il',
                    'eli.k@avney-derech.co.il',
                    'moshe.s@avney-derech.co.il',
                    'vladimir@avney-derech.co.il',
                    'yael@avney-derech.co.il',
                    'yosi.z@avney-derech.co.il',
                    'harel@avney-derech.co.il',
                    'liron@sh-av.co.il',
                    'andranik@constant-tech.biz',
                    'araqsya@constant-tech.biz',
                    'daniel@avney-derech.co.il',
                    'shnir.yakuv@avney-derech.co.il'
                ];

                $this->_responseData['user']['hasSpecialDeliveryPermission'] = ((Usr::can(Usr::READ_PERM,'Controller_DeliveryReports',Enum_UserPriorityLevel::General))  || (in_array(strtolower(Auth::instance()->get_user()->email), $emails))) ? "1" : "0";
                $this->_responseData['user']['forceUpdateVersionCode'] = 319;

                $this->_responseData['user']['permissions'] = $permissions;

                $this->_responseData['user']['professions'] = [];
                foreach($this->_user->professions->where('status','=','enabled')->find_all() as $prof){
                    $this->_responseData['user']['professions'][] = $prof->name;
                }
            }
        }else{
            throw API_Exception::factory(500,'Incorrect login data');
            //$this->_setErrors('Invalid username or password');
        }
    }

    public function action_demo_login(){
        Auth::instance()->force_login(ORM::factory('User',343));
        $this->_user = Auth::instance()->get_user();
        $utkn = Model_UToken::makeApplicationDemoToken($this->_user->id);
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
}