<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 17.11.2016
 * Time: 5:43
 */
class Controller_User extends HDVP_Controller_Template
{
    protected $_actions_perms = [
        'profile,agree_terms' => [
            'GET' => 'read',
            'POST' => 'update'
        ],
    ];
    public function action_profile(){
        $this->_checkForAjaxOrDie();
        if(($this->request->method() == HTTP_Request::POST)){
            try{
                if(!empty(trim(Arr::get($this->post(),'password'))) AND !empty(trim(Arr::get($this->post(),'password_confirm')))){
                    $this->_user->update_user(Arr::extract($this->post(),['password','password_confirm']));
                }
                $lang = Language::getLangByIso2(Arr::get($this->post(),'lang','en'));
                $this->_user->lang = $lang->iso2;
                    $this->_user->values($this->post(),['name','phone'])->save();
                Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $this->_user]);
                Event::instance()->fire('onUserUpdated',['sender' => $this,'item' => $this->_user]);
                $this->setResponseData('triggerEvent','profileUpdated');
            }catch(ORM_Validation_Exception $e){
                $this->_setErrors($e->errors('validation'));
            }catch(Exception $e){
                $this->_setErrors($e->getMessage());
            }
        }else{
            $this->setResponseData('modal', View::make('user/profile',['user' => $this->_user, 'languages' => Language::getAll(), 'selectedLang' => Language::getLangByIso2($this->_user->lang)]));
        }


    }
    public function action_agree_terms(){
        $this->_checkForAjaxOrDie();
        if(($this->request->method() != HTTP_Request::POST)){
            throw new HTTP_Exception_404();
        }

        $this->_user->terms_agreed = 1;
        $this->_user->save();
        $this->setResponseData('triggerEvent','termsAgreed');
    }
}