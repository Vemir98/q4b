<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 12.03.2020
 * Time: 18:33
 */

class PushNotification
{
    public static function notifyElAppUsers($elApprovalId, $action) {
        $users = Api_DBElApprovals::getElApprovalUsersListForNotify($elApprovalId);

        $usersDeviceTokens = [];

        foreach ($users as $user) {
            if($user['deviceToken']) {
                array_push($usersDeviceTokens, $user['deviceToken']);
            }
        }

        $fpns = new HDVP\FirebasePushNotification();
        $fpns->notify($usersDeviceTokens, [
            'type' => 'elApproval',
            'action' => $action,
            'id' => $elApprovalId
        ]);

        $f = fopen(DOCROOT.'testNotification.txt', 'a');
        if($f) {
            fputs($f, '[PushNotification] - [type=elApproval] - [action='.$action.'] - [id='.$elApprovalId.'] - ['.date("Y-m-d h:i:sa").']'."\n");
        }
        fclose($f);
    }

    public static function notifyQcUsers($qcId, $action) {
        $qc = ORM::factory('QualityControl', $qcId);

        self::notifyProjectUsers($qc->project->id, 'qc', $action, $qcId);
    }

    private static function notifyProjectUsers($projectId, $type, $action, $typeId) {
        $project = ORM::factory('Project', $projectId);

        $usersList = $project->users->find_all();

        $usersDeviceTokens = [];

        foreach ($usersList as $user) {
            if($user->device_token) {
                array_push($usersDeviceTokens, $user->device_token);
            }
        }

        $fpns = new HDVP\FirebasePushNotification();
        $fpns->notify($usersDeviceTokens, [
            'type' => $type,
            'action' => $action,
            'id' => $typeId
        ]);

        $f = fopen(DOCROOT.'testNotification.txt', 'a');
        if($f) {
            fputs($f, '[PushNotification] - [type='.$type.'] - [action='.$action.'] - [id='.$typeId.'] - ['.date("Y-m-d h:i:sa").']'."\n");
        }
        fclose($f);

    }
}