<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 12.03.2020
 * Time: 18:33
 */

class PushNotification
{
    public static function notifyElAppUsers($elApprovalId, $users, $projectId, $action)
    {
        $usersDeviceTokens = self::getUsersDeviceTokens($users);

        if(!empty($usersDeviceTokens[Enum_UserOsTypes::Android])) {
            self::notifyUsers($usersDeviceTokens[Enum_UserOsTypes::Android], Enum_NotifyTypes::ElApproval, $action, $elApprovalId, $projectId, Enum_UserOsTypes::Android);
        }

        if(!empty($usersDeviceTokens[Enum_UserOsTypes::Ios])) {
            self::notifyUsers($usersDeviceTokens[Enum_UserOsTypes::Ios], Enum_NotifyTypes::ElApproval, $action, $elApprovalId, $projectId, Enum_UserOsTypes::Ios);
        }
    }

    public static function notifyQcUsers($qcId, $projectId, $action)
    {
        self::notifyProjectUsers($projectId, Enum_NotifyTypes::Qc, $action, $qcId);
    }

    public static function notifyInfoCenterMessageUsers($messageId, $projectIds, $action)
    {
        foreach ($projectIds as $projectId) {
            self::notifyProjectUsers($projectId, Enum_NotifyTypes::InfoCenter, $action, $messageId);
        }
    }

    private static function notifyProjectUsers($projectId, $type, $action, $typeId)
    {
        $project = ORM::factory('Project', $projectId);

        $usersDeviceTokens = self::getUsersDeviceTokens($project->users->find_all());

        if(!empty($usersDeviceTokens[Enum_UserOsTypes::Android])) {
            self::notifyUsers($usersDeviceTokens[Enum_UserOsTypes::Android], $type, $action, $typeId, $projectId, Enum_UserOsTypes::Android);
        }

        if(!empty($usersDeviceTokens[Enum_UserOsTypes::Ios])) {
            self::notifyUsers($usersDeviceTokens[Enum_UserOsTypes::Ios], $type, $action, $typeId, $projectId, Enum_UserOsTypes::Ios);
        }
    }

    private static function getUsersDeviceTokens($usersList) :array
    {
        $androidDeviceTokens = [];
        $iosDeviceTokens = [];

        foreach ($usersList as $user) {
            if(is_object($user)) {
                Kohana::$log->add(Log::ERROR, 'obj');
                if ($user->device_token) {
                    switch ($user->os_type) {
                        case Enum_UserOsTypes::Android:
                            $androidDeviceTokens[] = $user->device_token;
                            break;
                        case Enum_UserOsTypes::Ios:
                            $iosDeviceTokens[] = $user->device_token;
                            break;
                    }
                }
            } else {
                if ($user['deviceToken']) {
                    switch ($user['osType']) {
                        case Enum_UserOsTypes::Android:
                            $androidDeviceTokens[] = $user['deviceToken'];
                            break;
                        case Enum_UserOsTypes::Ios:
                            $iosDeviceTokens[] = $user['deviceToken'];
                            break;
                    }
                }
            }
        }
            Kohana::$log->add(Log::ERROR, json_encode([
            Enum_UserOsTypes::Android => $androidDeviceTokens,
            Enum_UserOsTypes::Ios => $iosDeviceTokens
        ], JSON_PRETTY_PRINT));

        return [
            Enum_UserOsTypes::Android => $androidDeviceTokens,
            Enum_UserOsTypes::Ios => $iosDeviceTokens
        ];
    }

    private static function notifyUsers($userTokens, $type, $action, $typeId, $projectId, $osType)
    {
        $fpns = new HDVP\FirebasePushNotification();
        $fpns->notify($userTokens, [
            'type' => $type,
            'action' => $action,
            'id' => $typeId,
            'projectId' => $projectId
        ], $osType);

        $f = fopen(DOCROOT . 'testNotification.txt', 'a');
        if ($f) {
            fputs($f, '[PushNotification] - [type=' . $type . '] - [action=' . $action . '] - [id=' . $typeId . '] - [' . date("Y-m-d h:i:sa") . '] - [' . Auth::instance()->get_user()->id . '] - ['. $projectId .'] - [' .$osType. ']' . "\n");
        }
        fclose($f);
    }
}