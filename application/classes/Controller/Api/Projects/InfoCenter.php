<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 17.11.2021
 * Time: 11:20
 */
use Helpers\PushHelper;


class Controller_Api_Projects_InfoCenter extends HDVP_Controller_API
{
    /**
     * Get Project messages with histories
     * https://qforb.net/api/json/<appToken>/projects/<projectId>/messages
     */
    public function action_project_messages_index_get()
    {
        try {
            $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));

            $projectMessages = $this->getProjectMessagesExpandedData($projectId);

            $this->_responseData = [
                'status' => 'success',
                'items' => $projectMessages,
            ];

        }  catch (Exception $e) {
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_project_messages_index_get] (Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Get Projects messages with histories
     * https://qforb.net/api/json/<appToken>/projects/<projectId>/messages
     */
    public function action_projects_message_index_post()
    {
        try {

            $projectIds = Arr::get($_POST,'projectIds');

            $result = [];

            foreach ($projectIds as $projectId) {

                $projectData = [
                    'id' => $projectId,
                    'messages' => $this->getProjectMessagesExpandedData($projectId)
                ];

                array_push($result, $projectData);
            }

            $this->_responseData = [
                'status' => 'success',
                'items' => $result,
            ];

        }  catch (Exception $e) {
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_projects_message_index_post] (Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Create Message for project users
     * https://qforb.net/api/json/<appToken>/projects/messages/create
     */
    public function action_projects_message_create_post()
    {
        $clientData = Arr::extract($_POST,
            [
                'message',
                'projectIds'
            ]);
        $clientData['message'] = trim($clientData['message']);

        try {
            $valid = Validation::factory($clientData);

            $valid
                ->rule('message', 'not_empty')
                ->rule('projectIds', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'missing required data');
            }

            Database::instance()->begin();

            $queryData = [
                'created_at' => time(),
                'created_by' => Auth::instance()->get_user()->id
            ];

            $messageId = DB::insert('projects_messages')
                ->columns(array_keys($queryData))
                ->values(array_values($queryData))
                ->execute($this->_db)[0];

            if($messageId) {
                foreach ($clientData['projectIds'] as $projectId) {
                    $queryData = [
                        'project_id' => $projectId,
                        'pm_id' => $messageId
                    ];

                    DB::insert('projects_projects_messages')
                        ->columns(array_keys($queryData))
                        ->values(array_values($queryData))
                        ->execute($this->_db);
                }

                $queryData = [
                    'pm_id' => $messageId,
                    'text'  => $clientData['message'],
                    'created_at' => time(),
                    'created_by' => Auth::instance()->get_user()->id
                ];

                DB::insert('projects_messages_contents')
                    ->columns(array_keys($queryData))
                    ->values(array_values($queryData))
                    ->execute($this->_db);
            }

            $messageData = Api_DBInfoCenter::getProjectMessages([$messageId])[0];
            $projectMessagesHistoryData = Api_DBInfoCenter::getProjectMessagesHistory([$messageId]);
            $messageData['history'] = $projectMessagesHistoryData;

            $messageProjectIds = array_column(Api_DBInfoCenter::getMessageProjects($messageId), 'projectId');
            PushNotification::notifyInfoCenterMessageUsers($messageId, $messageProjectIds, Enum_NotifyAction::Created);

            Database::instance()->commit();

            $this->_responseData = [
                'status' => 'success',
                'id' => $messageId,
                'item' => $messageData
            ];

        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_projects_message_create_post] (API_ValidationException)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Incorrect data');
        } catch (Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_projects_message_create_post] (Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Delete Message
     * https://qforb.net/api/json/<appToken>/projects/messages/<messageId>
     */
    public function action_projects_message_index_get()
    {
        $messageId = $this->getUIntParamOrDie($this->request->param('messageId'));

        try {
            Database::instance()->begin();

            $messageData = Api_DBInfoCenter::getProjectMessages([$messageId])[0];
            $projectMessagesHistoryData = Api_DBInfoCenter::getProjectMessagesHistory([$messageId]);
            $messageData['history'] = $projectMessagesHistoryData;

            Database::instance()->commit();

            $this->_responseData = [
                'status' => "success",
                'item' => $messageData
            ];
        } catch (Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_projects_message_resend_post] (Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Operation Error');
        }
    }


    /**
     * Resend Message
     * https://qforb.net/api/json/<appToken>/projects/messages/<messageId>/resend
     */
    public function action_projects_message_resend_post()
    {
        $messageId = $this->getUIntParamOrDie($this->request->param('messageId'));

        $projectIds = Arr::get($_POST,'projectIds');

        $missingProjectIds = [];
        try {
            Database::instance()->begin();

            $messageProjectIds = array_column(Api_DBInfoCenter::getMessageProjects($messageId), 'projectId');

            foreach ($projectIds as $projectId) {
                if(!in_array($projectId, $messageProjectIds)) {
                    array_push($missingProjectIds, $projectId);
                    $queryData = [
                        'project_id' => $projectId,
                        'pm_id' => $messageId
                    ];

                    DB::insert('projects_projects_messages')
                        ->columns(array_keys($queryData))
                        ->values(array_values($queryData))
                        ->execute($this->_db);
                }
            }

            Database::instance()->commit();

            PushNotification::notifyInfoCenterMessageUsers($messageId, $missingProjectIds, Enum_NotifyAction::Created);

            $this->_responseData = [
                'status' => "success"
            ];
        } catch (Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_projects_message_resend_post] (Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Delete Message
     * https://qforb.net/api/json/<appToken>/projects/messages/<messageId>/delete
     */
    public function action_projects_message_delete_delete()
    {
        $messageId = $this->getUIntParamOrDie($this->request->param('messageId'));

        try {
            Database::instance()->begin();

            DB::delete('projects_messages')->where('id', '=', $messageId)->execute($this->_db);

            Database::instance()->commit();

            $this->_responseData = [
                'status' => "success"
            ];
        } catch (Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_projects_message_delete_delete] (Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Delete Message History
     * https://qforb.net/api/json/<appToken>/projects/messages/histories/<historyId>/delete
     */
    public function action_projects_message_history_delete_delete()
    {
        $messageHistoryId = $this->getUIntParamOrDie($this->request->param('historyId'));

        try {
            Database::instance()->begin();
            $messageId = Api_DBInfoCenter::getMessagesIdByHistoryId($messageHistoryId)[0]['messageId'];

            if(!$messageId) {
                throw API_Exception::factory(500,'Invalid history id');
            }

            DB::delete('projects_messages_contents')->where('id', '=', $messageHistoryId)->execute($this->_db);

            $messageHistory = Api_DBInfoCenter::getProjectMessagesHistory([$messageId]);

            $messageProjectIds = array_column(Api_DBInfoCenter::getMessageProjects($messageId), 'projectId');

            if(count($messageHistory) === 0) {
                DB::delete('projects_messages')->where('id', '=', $messageId)->execute($this->_db);
            }

            Database::instance()->commit();

            PushNotification::notifyInfoCenterMessageUsers($messageId, $messageProjectIds, (count($messageHistory) === 0) ? Enum_NotifyAction::Deleted : Enum_NotifyAction::Updated);

            $this->_responseData = [
                'status' => "success"
            ];
        } catch (Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_projects_message_history_delete_delete] (Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Edit Message (add new history into message)
     * https://qforb.net/api/json/<appToken>/projects/messages/histories/<historyId>/edit
     */
    public function action_projects_message_history_edit_post()
    {
        $messageHistoryId = $this->getUIntParamOrDie($this->request->param('historyId'));
        $messageId = Api_DBInfoCenter::getMessagesIdByHistoryId($messageHistoryId)[0]['messageId'];

        if(!$messageId) {
            throw API_Exception::factory(500,'Invalid history id');
        }

        $clientData = Arr::extract($_POST,
            [
                'message',
                'projectIds'
            ]);
        $clientData['message'] = trim($clientData['message']);

        try {
            $valid = Validation::factory($clientData);

            $valid
                ->rule('message', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'missing required data');
            }

            Database::instance()->begin();

            $queryData = [
                'pm_id' => $messageId,
                'text'  => $clientData['message'],
                'parent_id' => $messageHistoryId,
                'created_at' => time(),
                'created_by' => Auth::instance()->get_user()->id
            ];

            DB::insert('projects_messages_contents')
                ->columns(array_keys($queryData))
                ->values(array_values($queryData))
                ->execute($this->_db);

            Database::instance()->commit();

            $messageData = Api_DBInfoCenter::getProjectMessages([$messageId])[0];
            $projectMessagesHistoryData = Api_DBInfoCenter::getProjectMessagesHistory([$messageId]);
            $messageData['history'] = $projectMessagesHistoryData;

            $messageProjectIds = array_column(Api_DBInfoCenter::getMessageProjects($messageId), 'projectId');
            PushNotification::notifyInfoCenterMessageUsers($messageId, $messageProjectIds, Enum_NotifyAction::Updated);


            $this->_responseData = [
                'status' => "success",
                'item' => $messageData
            ];

        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        } catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    private function getProjectMessagesExpandedData($projectId): array
    {
        $projectMessagesIds = array_column(Api_DBInfoCenter::getProjectMessagesIds($projectId), 'messageId');

        $projectMessagesData = [];
        if(!empty($projectMessagesIds)) {
            $projectMessagesData = Api_DBInfoCenter::getProjectMessages($projectMessagesIds);
            $projectMessagesHistoryData = Api_DBInfoCenter::getProjectMessagesHistory($projectMessagesIds);

            foreach ($projectMessagesData as &$message) {
                $message['history'] = [];
                foreach ($projectMessagesHistoryData as &$history) {
                    if((int)$history['pmId'] === (int)$message['id']) {
                        array_push($message['history'], $history);
                    }
                }
            }
        }

        return $projectMessagesData;
    }
}