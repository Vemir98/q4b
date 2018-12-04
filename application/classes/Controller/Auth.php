<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 19.09.2016
 * Time: 17:01
 */
class Controller_Auth extends HDVP_Controller_Template
{

    public $template = 'auth/template';

    public function action_login(){

//        $mail = new Mail(Kohana::$config->load('mail'));
//        $mail->to('sur-ser@mail.ru');
//        $mail->from('info@qforb.net');
//        $mail->subject('Q4b user invite You to company team');
//        $mail->body('asas');
//        var_dump($mail->send());
        if($this->_auth->logged_in()) $this->makeRedirect($this->getRedirectUri($this->_auth->get_user()->getRelevantRole('outspread')),302,false);
        if($this->request->method() == HTTP_Request::POST)
        {
            $this->_checkForAjaxOrDie();

            if($this->_auth->logged_in()){
                $this->makeRedirect($this->getRedirectUri($this->_auth->get_user()->getRelevantRole('outspread')),302,false);
                return;
            }
            if($this->_auth->login(Arr::get($this->post(),'login'),Arr::get($this->post(),'pass'),(bool)Arr::get($this->post(),'remember')))
            {
                if($this->_auth->get_user()->status == Enum_UserStatus::Blocked){
                    $this->_auth->logout(TRUE);
                    $this->_setErrors('Your Account is deactivated! Please contact Your Manager');
                }else{
                    if((bool)Arr::get($this->post(),'remember')){
                        Cookie::set('un',AesCtr::encrypt(Arr::get($this->post(),'login'),$_SERVER['SERVER_NAME'],256));
                    }
                    $this->makeRedirect($this->getRedirectUri($this->_auth->get_user()->getRelevantRole('outspread')),302,false);
                }

            }else{
                $this->_setErrors('Invalid username or password');
            }
        }
        else
        {
            if($this->_auth->logged_in()){
                $this->makeRedirect($this->getRedirectUri($this->_auth->get_user()->getRelevantRole('outspread')),302,false);
                return;
            }
            $username = Cookie::get('un',null);
            if($username){
                $username = AesCtr::decrypt($username,$_SERVER['SERVER_NAME'],256);
            }
            $this->template->content = View::make('auth/login',['iagree' => (bool)Cookie::get('iagree',false),'username' => $username]);
        }
    }

    public function action_logout(){
        $this->_auth->logout();
        $this->makeRedirect();
    }

    public function action_accept_invitation(){
        if($this->_auth->logged_in()){
            $this->_auth->logout(TRUE);
            $this->makeRedirect( URL::site(Request::detect_uri(), TRUE) );
        }
        $param = trim($this->request->param('param1'));
        if(empty($param)){
            throw new HTTP_Exception_404;
        }
        $token = ORM::factory('UToken',['token' => $param,'type' => Enum_UToken::Registration]);
        unset($param);
        if( ! $token->loaded()){
            throw new HTTP_Exception_404;
        }
        
        if( $token->isExpire()){
            //todo: Выдать сообщение о том что токен просрочен
            throw new HTTP_Exception_404('token expires');
        }

        $user = $token->user;
        if( ! $user->loaded()){
            deleteTokenAndShow404:
            $token->delete();
            throw new HTTP_Exception_404;
        }

        $user->setStatus(new Enum_UserStatus(Enum_UserStatus::Active));
        try{
            Database::instance()->begin();
            $user->save();
            $token->delete();
            Database::instance()->commit();
            Auth::instance()->force_login($user);
            Session::instance()->set('firstTimeLogin',true);
            $this->makeRedirect('/');
        }catch (Exception $e){
            if( ! $e instanceof HTTP_Exception_Redirect){
                Database::instance()->rollback();
                goto deleteTokenAndShow404;
            }else{
                Session::instance()->set('showProfile',true);
                throw $e;
            }

        }
    }

    public function action_reset_password(){
        if($this->_auth->logged_in()){
            $this->_auth->logout(TRUE);
            $this->makeRedirect( URL::site(Request::detect_uri(), TRUE) );
        }
        $param = trim($this->request->param('param1'));
        if(empty($param)){
            throw new HTTP_Exception_404;
        }
        $token = ORM::factory('UToken',['token' => $param,'type' => Enum_UToken::RestorePassword]);
        unset($param);
        if( ! $token->loaded()){
            throw new HTTP_Exception_404;
        }

        if( $token->isExpire()){
            //todo: Выдать сообщение о том что токен просрочен
            throw new HTTP_Exception_404('token expires');
        }

        $user = $token->user;
        if( ! $user->loaded() OR ($user->status == Enum_UserStatus::Blocked)){
            deleteTokenAndShow404:
            $token->delete();
            throw new HTTP_Exception_404;
        }

        try{
            $token->delete();
            Auth::instance()->force_login($user);
            $this->makeRedirect('/');
        }catch (Exception $e){
            if( ! $e instanceof HTTP_Exception_Redirect){
                goto deleteTokenAndShow404;
            }else{
                Session::instance()->set('showProfile',true);
            }
                throw $e;
            }

        }

    public function action_forgot_password(){
        if(($this->request->method() == HTTP_Request::POST)){
            $this->_checkForAjaxOrDie();
            try{
                $email = Arr::get($this->post(),'email');
                if(empty($email) OR !filter_var($email,FILTER_VALIDATE_EMAIL)){
                    throw new HDVP_Exception('Incorrect Email Address');
                }
                $user = ORM::factory('User',['email' => $email]);
                if( ! $user->loaded()){
                    throw new HDVP_Exception('Incorrect Email');
                }
                if($user->status == Enum_UserStatus::Blocked){
                    throw new HDVP_Exception('Your Account is deactivated! Please contact Your Manager');
                }
                Event::instance()->fire('onPasswordReset',['sender' => $this,'item' => $user]);
                $this->setResponseData('triggerEvent','onPasswordReset');
            }catch(HDVP_Exception $e){
                $this->_setErrors($e->getMessage());
            }catch(Exception $e){
                $this->_setErrors($e->getMessage());
            }
        }else{
            $this->template->content = View::make('auth/forgot-password');
        }

    }

    private function getRedirectUri($outspread){
        $output = '';
        switch ($outspread) {
            case Enum_UserOutspread::General:
                $output = 'dashboard';
                break;
            case Enum_UserOutspread::Corporate:
                $output = 'companies';
                break;
            case Enum_UserOutspread::Company:
                $output = 'companies/update/' . Auth::instance()->get_user()->company_id;
                break;
            case Enum_UserOutspread::Project:
                if(Auth::instance()->get_user()->is('project_visitor'))
                    $output = 'reports';
                else
                    $output = 'projects';
                break;
        }
        return URL::withLang($output,Language::getLangByIso2(Auth::instance()->get_user()->lang)->slug);
    }
}