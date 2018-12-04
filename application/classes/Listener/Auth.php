<?php defined('SYSPATH') OR die('No direct script access.');
use \Carbon\Carbon as Carbon;
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 18.11.2016
 * Time: 6:10
 */
class Listener_Auth
{
    public static function resetPassword($event,$sender,Model_User $user){
        Model_UToken::makeResetPassToken($user->id);
        $token = $user->utokens->where('type','=',Enum_UToken::RestorePassword)->find()->token;
        Queue::enqueue('mailing','Job_Auth_ResetPasswordEmail',[
            'email' => $user->email,
            'url' => URL::site('reset_password/'.$token,'http'),
            'view' => 'emails/user/reset-password',
            'lang' => Language::getCurrent()->iso2,
        ], Carbon::now()->addSeconds(10)->timestamp);
    }
}