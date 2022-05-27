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
                $appToken = Model_UToken::makeApplicationToken($this->_user->id);
                $this->_responseData['user'] = $this->getUserData($this->_user, $appToken->as_array()['token']);
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

    public static function getUserData($user, $token) {
        $userArray = $user->as_array();
        $permissions = [];
        unset($userArray['client']);
        $response = Arr::toCamelCase($userArray);
        unset($response['id'], $response['password'], $response['logins']);
        $response['token'] = $token;
        $response['role'] = $user->getRelevantRole()->as_array();
//        unset($this->_responseData['role']['id']);

        $acl = HDVP_Core::instance()->acl();
        $privileges = ORM::factory('ACL_Privilege')->find_all();
        foreach ($privileges as $priv) {
            foreach ($acl->getResources() as $res) {
                if ($user->can($priv->alias, $res)) {
                    $permissions[$priv->alias][] = strtolower(str_ireplace('Controller_','',$res));
                }
            }
        }
        $permissions = array_diff($permissions,array(''));

        $emails = Kohana::$config->load('deliveryPermissionsEmails')->as_array();

        $response['hasSpecialDeliveryPermission'] = ((Usr::can(Usr::READ_PERM,'Controller_DeliveryReports',Enum_UserPriorityLevel::General))  || (in_array(strtolower(Auth::instance()->get_user()->email), $emails))) ? "1" : "0";
        $response['forceUpdateVersionCode'] = 319;

        $subcontractors = Kohana::$config->load('subcontractors')->as_array();
        $userRoleName = $user->getRelevantRole('name');
        $isSubContractor = false;
        if (array_key_exists($userRoleName, $subcontractors)) {
//                    echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($subcontractors[$userRoleName]); echo "</pre>"; exit;
            if($userRoleName === 'project_general_subcontractor') {
                $response['isGeneralSubcontractor'] = true;
            } else {
                $response['isSubcontractor'] = true;
            }

            $response['subcontractorSpecialities'] = $subcontractors[$userRoleName]['specialties'];
        }

        $response['permissions'] = $permissions;

        $response['professions'] = [];
        foreach($user->professions->where('status','=','enabled')->find_all() as $prof){
            $response['professions'][] = $prof->name;
        }
        return $response;
    }
}