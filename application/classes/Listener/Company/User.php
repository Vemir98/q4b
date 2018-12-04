<?php defined('SYSPATH') OR die('No direct script access.');
use \Carbon\Carbon as Carbon;
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.11.2016
 * Time: 13:30
 */
class Listener_Company_User
{

    public static function added($event,$sender,Model_User $user){
        Model_UToken::makeRegistrationToken($user->id);
        $token = $user->utokens->where('type','=',Enum_UToken::Registration)->find()->token;
        Queue::enqueue('mailing','Job_User_InvitationEmail',[
            'email' => $user->email,
            'currentUser' => Auth::instance()->get_user()->username,
            'url' => URL::site('accept_invitation/'.$token,'https'),
            'view' => 'emails/user/invitation',
            'lang' => Language::getCurrent()->iso2,
        ], Carbon::now()->addSeconds(30)->timestamp);
    }

    /**
     * @param $event
     * @param $sender
     * @param Model_User $user (eventArgs)
     */
    public static function updated($event,$sender,Model_User $user){

       // var_dump($user->email);
    }

    /**
     * Приглашение пользователя
     * @param $event
     * @param $sender
     * @param Model_User $user
     */
    public static function invite($event,$sender,Model_User $user){
        Model_UToken::makeRegistrationToken($user->id);
        $token = $user->utokens->where('type','=',Enum_UToken::Registration)->find()->token;
        Queue::enqueue('mailing','Job_User_InvitationEmail',[
            'email' => $user->email,
            'currentUser' => Auth::instance()->get_user()->username,
            'url' => URL::site('accept_invitation/'.$token,'https'),
            'view' => 'emails/user/invitation',
            'lang' => Language::getCurrent()->iso2,
        ], Carbon::now()->addSeconds(30)->timestamp);
    }
}