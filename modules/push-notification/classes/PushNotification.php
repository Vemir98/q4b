<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 12.03.2020
 * Time: 18:33
 */

class PushNotification
{
    public static function notifyElAppUsers($elApprovalId, $users, $projectId, $action) {
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
            'id' => $elApprovalId,
            'projectId' => $projectId
        ]);

        $f = fopen(DOCROOT.'testNotification.txt', 'a');
        if($f) {
            fputs($f, '[PushNotification] - [type=elApproval] - [action='.$action.'] - [id='.$elApprovalId.'] - ['.date("Y-m-d h:i:sa").'] - ['.Auth::instance()->get_user()->id.']'."\n");
        }
        fclose($f);
    }

    public static function notifyQcUsers($qcId, $projectId, $action) {
        $f = fopen(DOCROOT.'testNotification.txt', 'a');
        if($f) {
            fputs($f, 'qc = '.json_encode($projectId, JSON_PRETTY_PRINT)."\n");
            fputs($f, 'qcId = '.json_encode($qcId, JSON_PRETTY_PRINT)."\n");
        }
        fclose($f);

        self::notifyProjectUsers($projectId, 'qc', $action, $qcId);
    }

    private static function notifyProjectUsers($projectId, $type, $action, $typeId) {
        $f = fopen(DOCROOT.'testNotification.txt', 'a');
        if($f) {
            fputs($f, 'projectId = '.json_encode($projectId, JSON_PRETTY_PRINT)."\n");
        }
        fclose($f);
        $project = ORM::factory('Project', $projectId);

        $usersList = $project->users->find_all();
        $userIds = [];

        $usersDeviceTokens = [];

        foreach ($usersList as $user) {
            if($user->device_token) {
                array_push($usersDeviceTokens, $user->device_token);
                array_push($userIds, $user->id);
            }
        }

        $fpns = new HDVP\FirebasePushNotification();
        $fpns->notify($usersDeviceTokens, [
            'type' => $type,
            'action' => $action,
            'id' => $typeId,
            'projectId' => $projectId
        ]);

        $f = fopen(DOCROOT.'testNotification.txt', 'a');
        if($f) {
            fputs($f, '[PushNotification] - [type='.$type.'] - [action='.$action.'] - [id='.$typeId.'] - ['.date("Y-m-d h:i:sa").'] - ['.Auth::instance()->get_user()->id.']'."\n");
            fputs($f, json_encode($userIds, JSON_PRETTY_PRINT)."\n");
        }
        fclose($f);

    }
}