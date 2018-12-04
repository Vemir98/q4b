<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 11.11.2016
 * Time: 22:44
 */
class Model_UToken extends ORM
{
    public $_table_name = 'u_tokens';
    protected $_created_column = ['column' => 'created_at', 'format' => true];
    
    protected $_belongs_to = [
        'user' => [
            'model' => 'User',
            'foreign_key' => 'user_id'
        ]
    ];


    public static function makeRegistrationToken($userId){
        $tokens = ORM::factory('UToken')->where('user_id','=',$userId)->and_where('type','=',Enum_UToken::Registration)->find_all();

        if(count($tokens)){
            foreach ($tokens as $token){
                $token->delete();
            }
        }
        return ORM::factory('UToken')->set('user_id',$userId)->set('type',Enum_UToken::Registration)->set('token',md5(uniqid(null,true)))->set('expires',time() + 8640 * 14)->save();
    }

    public static function makeResetPassToken($userId){
        $tokens = ORM::factory('UToken')->where('user_id','=',$userId)->and_where('type','=',Enum_UToken::RestorePassword)->find_all();

        if(count($tokens)){
            foreach ($tokens as $token){
                $token->delete();
            }
        }
        return ORM::factory('UToken')->set('user_id',$userId)->set('type',Enum_UToken::RestorePassword)->set('token',md5(uniqid(null,true)))->set('expires',time() + 8640)->save();
    }

    public static function makeApplicationToken($userId){
        $tokens = ORM::factory('UToken')->where('user_id','=',$userId)->and_where('type','=',Enum_UToken::Application)->find_all();

        if(count($tokens)){
            foreach ($tokens as $token){
                $token->delete();
            }
        }
        return ORM::factory('UToken')->set('user_id',$userId)->set('type',Enum_UToken::Application)->set('token',md5(uniqid(null,true)))->set('expires',time() + Date::YEAR * 5)->save();
    }

    public function isExpire(){
        return $this->expires < time();
    }
}