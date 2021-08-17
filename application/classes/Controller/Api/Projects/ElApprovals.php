<?php


class Controller_Api_Projects_ElApprovals extends HDVP_Controller_API
{
    /**
     * Create Element approval
     * https://qforb.net/api/json/<appToken>/el-approvals
     */
    public function action_index_post(){
        $clientData = Arr::extract($_POST,
            [
                'company_id',
                'project_id',
                'object_id',
                'place_id',
                'floor_id',
                'element_id',
                'specialities',
                'notify'
            ]);

        try {
            $valid = Validation::factory($clientData);

            $valid
                ->rule('company_id', 'not_empty')
                ->rule('project_id', 'not_empty')
                ->rule('object_id', 'not_empty')
                ->rule('element_id', 'not_empty')
                ->rule('floor_id', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'Incorrect data');
            }

            $queryData = [
                'company_id' => $clientData['company_id'],
                'project_id' => $clientData['project_id'],
                'object_id' => $clientData['object_id'],
                'place_id' => $clientData['place_id'] ?: null,
                'element_id' => $clientData['element_id'],
                'floor_id' => $clientData['floor_id'],
                'created_at' => time(),
                'created_by' => Auth::instance()->get_user()->id,
            ];

            $elApprovalId = DB::insert('el_approvals')
                ->columns(array_keys($queryData))
                ->values(array_values($queryData))
                ->execute($this->_db)[0];

            if(!empty($clientData['specialities'])) {
                foreach ($clientData['specialities'] as $speciality) {
                    $valid = Validation::factory($speciality);

                    $valid->rule('id', 'not_empty');
                    if (!$valid->check()) {
                        throw API_ValidationException::factory(500, 'Incorrect data');
                    }
                    $queryData = [
                        'el_app_id' => $elApprovalId,
                        'craft_id' => $speciality['id'],
                        'notice' => $speciality['note'] ?: null,
                        'created_at' => time(),
                        'created_by' => Auth::instance()->get_user()->id,
                    ];

                    $specialityId = DB::insert('el_approvals_crafts')
                        ->columns(array_keys($queryData))
                        ->values(array_values($queryData))
                        ->execute($this->_db)[0];

                    if(!empty($speciality['signatures'])) {
                        foreach ($speciality['signatures'] as $signature) {
                            $valid = Validation::factory($signature);

                            $valid
                                ->rule('name', 'not_empty')
                                ->rule('position', 'not_empty')
                                ->rule('image', 'not_empty');

                            if (!$valid->check()) {
                                throw API_ValidationException::factory(500, 'Incorrect data');
                            }

                            $queryData = [
                              'el_app_id' => $elApprovalId,
                              'el_app_craft_id' => $specialityId,
                              'name' => $signature['name'],
                              'position' => $signature['position'],
                              'image' => $signature['image'],
                              'created_at' => time(),
                              'created_by' => Auth::instance()->get_user()->id
                            ];

                            DB::insert('el_app_signatures')
                                ->columns(array_keys($queryData))
                                ->values(array_values($queryData))
                                ->execute($this->_db);
                        }
                    }

                    if(!empty($speciality['tasks'])) {
                        foreach ($speciality['tasks'] as $task) {
                            $valid = Validation::factory($task);

                            $valid
                                ->rule('id', 'not_empty')
                                ->rule('status', 'not_empty');
                            if (!$valid->check()) {
                                throw API_ValidationException::factory(500, 'Incorrect data');
                            }

                            $queryData = [
                                'el_app_craft_id' => $specialityId,
                                'task_id' => $task['id'],
                                'appropriate' => $task['status']
                            ];

                            DB::insert('el_app_crafts_tasks')
                                ->columns(array_keys($queryData))
                                ->values(array_values($queryData))
                                ->execute($this->_db);
                        }
                    }
                }
            }
            if(!empty($clientData['notify'])) {
                foreach ($clientData['notify'] as $userId) {
                    if($userId) {
                        $queryData = [
                            'ell_app_id' => $elApprovalId,
                            'user_id' => $userId
                        ];

                        DB::insert('el_approvals_notifications')
                            ->columns(array_keys($queryData))
                            ->values(array_values($queryData))
                            ->execute($this->_db);
                    }
                }
            }
            $this->_responseData = [];
            $this->_responseData['status'] = 'success';
        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        } catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Update Element approval
     * https://qforb.net/api/json/<appToken>/el-approvals/<id>
     */
    public function action_index_put(){
        $elApprovalId = $this->getUIntParamOrDie($this->request->param('id'));
        try {
            $elApproval = Api_DBElApprovals::getElApprovalById($elApprovalId);

            if(empty($elApproval)){
                throw API_Exception::factory(500,'Incorrect identifier');
            }

            $clientData = Arr::extract($this->put(),
                [
                    'company_id',
                    'project_id',
                    'object_id',
                    'place_id',
                    'floor_id',
                    'element_id',
                    'specialities',
                    'notify'
                ]);

            // signatures, task_status, note,

            echo "<pre>";print_r($clientData);echo "</pre>";die;

        } catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Get Element approval
     * https://qforb.net/api/json/<appToken>/el-approvals/<id>
     */
    public function action_index_get(){
        $elApprovalId = $this->getUIntParamOrDie($this->request->param('id'));
        try {
            $elApproval = Api_DBElApprovals::getElApprovalById($elApprovalId);

            if(empty($elApproval)){
                throw API_Exception::factory(500,'Incorrect identifier');
            }
            $elApproval[0]['specialities'] = Api_DBElApprovals::getElApprovalCraftsByElAppId($elApprovalId);
            foreach ($elApproval[0]['specialities'] as $key => $speciality) {
                $elApproval[0]['specialities'][$key]['signatures'] = Api_DBElApprovals::getElApprovalCraftsSignaturesByCraftIds($speciality['id']);
                $elApproval[0]['specialities'][$key]['tasks'] = Api_DBElApprovals::getElApprovalCraftsTasksByCraftIds($speciality['id']);
            }
            $this->_responseData = [
                'status' => "success",
                'items' => $elApproval
            ];
        }  catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * returns element approvals filtered list
     * returned data will be as rows[{}],pages: {total,offset,limit}
     * https://qforb.net/api/json/<appToken>/el-approvals/list
     * https://qforb.net/api/json/<appToken>/el-approvals/list/page/<page>
     */
    public function action_list_post(){
        try {
            $limit = 12;
            $params = array_diff(Arr::merge(Request::current()->param(),['page' => '']),array(''));
            $page = isset(Request::current()->param()['page']) && Request::current()->param()['page'] ? Request::current()->param()['page'] : 1;

            $filters = Arr::extract($_POST,
                [
                    'company_id',
                    'project_id',
                    'object_ids',
                    'place_ids',
                    'element_ids',
                    'speciality_ids',
                    'statuses',
                    'positions',
                ]);

            $filters['from'] = $_POST['from'] ? DateTime::createFromFormat('d/m/Y H:i',$_POST['from'] . ' 00:00')->getTimestamp() : null;
            $filters['to'] = $_POST['to'] ? DateTime::createFromFormat('d/m/Y H:i',$_POST['to'] . ' 23:59')->getTimestamp() : null;

            $valid = Validation::factory($filters);

            $valid
                ->rule('company_id', 'not_empty')
                ->rule('project_id', 'not_empty');

            $count = count(Api_DBElApprovals::getElApprovalsList(null,null, $filters));

            $pagination = Pagination::factory(array(
                    'total_items'    => $count,
                    'items_per_page' => $limit,
                )
            )
                ->route_params($params);

            $elApprovals = Api_DBElApprovals::getElApprovalsList($pagination->items_per_page, $pagination->offset, $filters, true);

            foreach ($elApprovals as $elAppKey => $elApproval) {
                $elApprovals[$elAppKey]['specialities'] = Api_DBElApprovals::getElApprovalCraftsByElAppId($elApproval['id']);

                foreach ($elApprovals[$elAppKey]['specialities'] as $specialityKey => $speciality) {
                    $elApprovals[$elAppKey]['specialities'][$specialityKey]['signatures'] = Api_DBElApprovals::getElApprovalCraftsSignaturesByCraftIds($speciality['id']);
                    $elApprovals[$elAppKey]['specialities'][$specialityKey]['tasks'] = Api_DBElApprovals::getElApprovalCraftsTasksByCraftIds($speciality['id']);
                }
            }
            $this->_responseData = [
                'status' => 'success',
                'items' => $elApprovals,
                'pagination' => ['total' => $count, 'page' => $page, 'limit' => $limit],
            ];
        }  catch (Exception $e) {
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }


    /**
     * returns list of positions of El Approval report signatures by Project id
     * https://qforb.net/api/json/<appToken>/el-approvals/positions/<project-id>
     */
    public function action_positions_get(){
        $projectId = $this->getUIntParamOrDie($this->request->param('id'));
        try {
            $responseData = [];
            $positions = Api_DBElApprovals::getElApprovalCraftsSignaturesPositionsListByProjectId($projectId);

            foreach ($positions as $positionKey => $position) {
                $responseData[$positionKey]['id'] = $position['id'];
                $responseData[$positionKey]['name'] = $position['position'];
            }
            $this->_responseData = [
                'status' => "success",
                'items' => $responseData
            ];
        }  catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * returns list of users to be informed
     * https://qforb.net/api/json/<appToken>/el-approvals/<id>/notifications
     */
    public function action_notifications_get(){
        $elApprovalId = $this->getUIntParamOrDie($this->request->param('id'));

        try {
            $responseData = [];
            $usersList = Api_DBElApprovals::getElApprovalUsersListForNotify($elApprovalId);

            foreach ($usersList as $user) {
                array_push($responseData, $user['user_id']);
            }
            $this->_responseData = [
                'status' => "success",
                'items' => $responseData
            ];
        } catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Updated list of users to be informed
     * https://qforb.net/api/json/<appToken>/el-approvals/<id>/notifications
     */
    public function action_notifications_put(){
        $elApprovalId = $this->getUIntParamOrDie($this->request->param('id'));

        $userIds = Arr::get($this->put(), 'userIds');

        echo '<pre>';print_r($userIds);echo '</pre>';die;

    }
}