<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.11.2016
 * Time: 13:35
 * Дефолтные слушатели событий в системе
 */
return [
    'onUserUpdated' => [
        'Listener_Company_User::updated'
    ],
    'onUserAdded' => [
        'Listener_Company_User::added'
    ],
    'onPasswordReset' => [
        'Listener_Auth::resetPassword'
    ],
    'onInviteUser' => [
        'Listener_Company_User::invite'
    ],
    'onReportQueryTokenAdded' => [
        'Listener_Report_QueryToken::registerExpiresCheckJob'
    ],
    'onPlanFileAdded' => [
        'Listener_Plan_File::pdfToImage'
    ],
    'onCraftAdded' => [
        'Listener_Settings_Craft::added'
    ],
    'onProfessionAdded' => [
        'Listener_Settings_Profession::added'
    ],
    'onTaskAdded' => [
        'Listener_Settings_Task::added'
    ],
    'onItemUpdated' => [
        'Listener_API_Cache::clear'
    ],
    'onItemAdded' => [
        'Listener_API_Cache::clear'
    ],
    'onPlanCopy' => [
        'Listener_API_Cache::clear'
    ],
    'beforePlanDelete' => [
        'Listener_API_Cache::clear'
    ],
    'onTasksUpdated' => [
        'Listener_API_Cache::clearTasksCache'
    ],
];