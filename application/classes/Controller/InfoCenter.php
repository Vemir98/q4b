<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 17.11.2021
 * Time: 11:20
 */

use Helpers\PushHelper;


class Controller_InfoCenter extends HDVP_Controller_Template
{
    protected $_actions_perms = [
        'index' => [
            'GET' => 'read',
            'POST' => 'read'
        ]
    ];
    public function action_index() {
        VueJs::instance()->addComponent('info-center/info-center-page');
        VueJs::instance()->addComponent('info-center/info-center-message');
        VueJs::instance()->addComponent('info-center/info-center-history');
        VueJs::instance()->addComponent('info-center/components/modals/message-history-modal');
        VueJs::instance()->addComponent('info-center/components/modals/confirm-popup');
        VueJs::instance()->addComponent('info-center/components/modals/resend-message-dropdown');
        VueJs::instance()->includeMultiselect();

        $translations = [
            'companies' => __('Companies'),
            'select_company' => __('Select Company'),
            'projects' => __('Projects'),
            'show' => __('Show'),
            'info_center' => __('info_center'),
            'filter_by' => __('filter_by:'),
            'select_project' => __('Select project'),
            'your_message' => __('your_message'),
            'today' => __('Today'),
            'yesterday' => __('Yesterday'),
            'send' => __('send'),
            'cancel' => __('Cancel'),
            'edit' => __('Edit'),
            'message_history' => __('message_history'),
            'close' => __('Close'),
            'delete' => __('Delete'),
            'are_you_sure_you_want_to_delete_this_message' => __('are_you_sure_you_want_to_delete_this_message'),
            'confirm' => __('Confirm'),
            'resend' => __('resend'),
            "select_all" => __('select all'),
            "unselect_all" => __('unselect all'),
        ];

        $this->template->content = View::make('info-center/index', [
            'translations' => $translations
        ]);
    }
}