<?php
use JonnyW\PhantomJs\Client;
use Helpers\PushHelper;


class Controller_Api_Projects_ElApprovals extends HDVP_Controller_API
{
    /**
     * Create Element approval
     * https://qforb.net/api/json/<appToken>/el-approvals
     */
    public function action_index_post(){
        $clientData = Arr::extract($_POST,
            [
                'companyId',
                'projectId',
                'objectId',
                'placeId',
                'floorId',
                'elementId',
                'note',
                'specialities',
                'notify'
            ]);

        try {
            $valid = Validation::factory($clientData);

            $valid
                ->rule('companyId', 'not_empty')
                ->rule('projectId', 'not_empty')
                ->rule('objectId', 'not_empty')
                ->rule('elementId', 'not_empty')
                ->rule('floorId', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'missing required EAR field');
            }

            Database::instance()->begin();
            $queryData = [
                'company_id' => $clientData['companyId'],
                'project_id' => $clientData['projectId'],
                'object_id' => $clientData['objectId'],
                'place_id' => $clientData['placeId'] ?: null,
                'element_id' => $clientData['elementId'],
                'notice' => $clientData['note'],
                'floor_id' => $clientData['floorId'],
                'created_at' => time(),
                'created_by' => Auth::instance()->get_user()->id,
                'updated_at' => time(),
                'updated_by' => Auth::instance()->get_user()->id,
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
                        throw API_ValidationException::factory(500, 'Empty speciality id');
                    }
                    $queryData = [
                        'el_app_id' => $elApprovalId,
                        'craft_id' => $speciality['id'],
                        'notice' => $speciality['note'] ?: null,
                        'created_at' => time(),
                        'created_by' => Auth::instance()->get_user()->id,
                        'updated_at' => time(),
                        'updated_by' => Auth::instance()->get_user()->id,
                    ];

                    $specialityId = DB::insert('el_approvals_crafts')
                        ->columns(array_keys($queryData))
                        ->values(array_values($queryData))
                        ->execute($this->_db)[0];

                    if(!empty($speciality['signatures'])) {

                        $elApprovalCraftSignatureImagesPath = DOCROOT.'media/data/projects/'.$clientData['projectId'].'/el-approvals';
                        if(!file_exists($elApprovalCraftSignatureImagesPath)) {
                            mkdir($elApprovalCraftSignatureImagesPath, 0777, true);
                        }

                        foreach ($speciality['signatures'] as $signature) {
                            $valid = Validation::factory($signature);

                            $valid
                                ->rule('name', 'not_empty')
                                ->rule('position', 'not_empty')
                                ->rule('image', 'not_empty');

                            $imageData = [
                                'fileName' => $elApprovalId.'_'.$speciality['id'].'_'.uniqid().'.png',
                                'fileOriginalName' => $elApprovalId.'_'.$speciality['id'].'_'.time().'.png',
                                'filePath' => null,
                                'src' => $signature['image'],
                                'ext' => 'png'
                            ];
                            $file = $this->_b64Arr([$imageData], $elApprovalCraftSignatureImagesPath);

                            if (!$valid->check()) {
                                throw API_ValidationException::factory(500, 'Empty speciality data');
                            }

                            $queryData = [
                              'el_app_id' => $elApprovalId,
                              'el_app_craft_id' => $specialityId,
                              'name' => $signature['name'],
                              'position' => $signature['position'],
                              'image' => str_replace(DOCROOT,'',$elApprovalCraftSignatureImagesPath.'/'.$file[0]['name']),
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
                                throw API_ValidationException::factory(500, 'Empty task data');
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
                    } else {
                        throw API_ValidationException::factory(500, 'Empty tasks in some speciality');
                    }
                }
            } else {
                throw API_ValidationException::factory(500, 'Empty specialities');
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

            Database::instance()->commit();

            PushNotification::notifyElAppUsers($elApprovalId, Enum_NotifyAction::Created);
//            $this->sendNotificationToUsers($elApprovalId);

            $this->_responseData = [
                'status' => 'success',
                'id' => $elApprovalId
            ];
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
            $elApproval = Api_DBElApprovals::getElApprovalById($elApprovalId)[0];

            if(!$elApproval) {
                Kohana::$log->add(Log::ERROR, 'Incorrect EAR id');
                throw API_ValidationException::factory(500, 'Incorrect EAR id');
            }

            if($elApproval['status'] === 'approved') {
                Kohana::$log->add(Log::ERROR, 'Can\'t modify readable EAR');
                throw API_ValidationException::factory(500, 'Can\'t modify readable EAR');
            }

            $clientData = Arr::extract($this->put(),
                [
                    'specialities'
                ]);

            if(!empty($clientData['specialities'])) {
                foreach ($clientData['specialities'] as $speciality) {
                    $valid = Validation::factory($speciality);

                    $valid
                        ->rule('id', 'not_empty')
                        ->rule('tasks', 'not_empty');

                    if (!$valid->check()) {
                        Kohana::$log->add(Log::ERROR, 'missing required field in some task');
                        throw API_ValidationException::factory(500, 'missing required field in some task');
                    }

                    $elApprovalCraftId = $speciality['id'];
                    Database::instance()->begin();

                    $queryData = [
                        'notice' => $speciality['notice'] ?: ""
                    ];

                    DB::update('el_approvals_crafts')
                        ->set($queryData)
                        ->where('id', '=', $elApprovalCraftId)
                        ->execute($this->_db);

                    if(!empty($speciality['deletedSignatures'])) {
                        foreach ($speciality['deletedSignatures'] as $signatureId) {
                            DB::delete('el_app_signatures')
                                ->where('id', '=', $signatureId)
                                ->execute($this->_db);
                        }
                    }

                    if(!empty($speciality['signatures'])) {
                        $elApprovalCraftSignatureImagesPath = DOCROOT.'media/data/projects/'.$elApproval['projectId'].'/el-approvals';
                        if(!file_exists($elApprovalCraftSignatureImagesPath)) {
                            mkdir($elApprovalCraftSignatureImagesPath, 0777, true);
                        }

                        foreach ($speciality['signatures'] as $signature) {
                            $valid = Validation::factory($signature);

                            $valid
                                ->rule('name', 'not_empty')
                                ->rule('position', 'not_empty')
                                ->rule('image', 'not_empty');

                            if (!$valid->check()) {
                                Kohana::$log->add(Log::ERROR, 'missing required field in signatures');
                                throw API_ValidationException::factory(500, 'missing required field in signatures');
                            }

                            if(!$signature['id']) {
                                $imageData = [
                                    'fileName' => $elApprovalId.'_'.$elApprovalCraftId.'_'.uniqid().'.png',
                                    'fileOriginalName' => $elApprovalId.'_'.$elApprovalCraftId.'_'.time().'.png',
                                    'filePath' => null,
                                    'src' => $signature['image'],
                                    'ext' => 'png'
                                ];
                                $file = $this->_b64Arr([$imageData], $elApprovalCraftSignatureImagesPath);
                                $queryData = [
                                    'el_app_id' => $elApprovalId,
                                    'el_app_craft_id' => $elApprovalCraftId,
                                    'name' => $signature['name'],
                                    'position' => $signature['position'],
                                    'image' => str_replace(DOCROOT,'',$elApprovalCraftSignatureImagesPath.'/'.$file[0]['name']),
                                    'created_at' => time(),
                                    'created_by' => Auth::instance()->get_user()->id
                                ];

                                DB::insert('el_app_signatures')
                                    ->columns(array_keys($queryData))
                                    ->values(array_values($queryData))
                                    ->execute($this->_db);
                            }
                        }
                    }

                    if(!empty($speciality['tasks'])) {
                        foreach ($speciality['tasks'] as $task) {
                            $valid = Validation::factory($task);

                            $valid
                                ->rule('id', 'not_empty')
                                ->rule('appropriate', 'not_empty');

                            if (!$valid->check()) {
                                Kohana::$log->add(Log::ERROR, 'missing required field in tasks');
                                throw API_ValidationException::factory(500, 'missing required field in tasks');
                            }

                            $queryData = [
                                'appropriate' => $task['appropriate']
                            ];

                            DB::update('el_app_crafts_tasks')
                                ->set($queryData)
                                ->where('id', '=', $task['id'])
                                ->execute($this->_db);
                        }
                    }
                    $this->updateElementApprovalCraft($speciality['id']);

                    Database::instance()->commit();
                }

                $this->updateElementApproval($elApprovalId);
                $f = fopen(DOCROOT.'testNotification.txt', 'a');
                if($f) {
                    fputs($f, '[zapros index.put]'."\n");
                }
                fclose($f);

            }

            $this->_responseData = [
                'status' => "success",
            ];
        } catch (Exception $e){
            Database::instance()->rollback();
//            throw API_Exception::factory(500,'Operation Error');
            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$e->getMessage()]); echo "</pre>"; exit;
        }
    }

    /**
     * Get Element approval
     * https://qforb.net/api/json/<appToken>/el-approvals/<id>
     */
    public function action_index_get(){
        $elApprovalId = $this->getUIntParamOrDie($this->request->param('id'));
        try {

            $status = Arr::get($_GET, 'status');

            $filters = [];
            if($status) {
                if(!in_array($status,Enum_ApprovalStatus::toArray(),true)) {
                    throw API_ValidationException::factory(500, 'Invalid status');
                }

                $filters['status'] = $status;
            }

            $elApproval = Api_DBElApprovals::getElApprovalById($elApprovalId, $filters)[0];

//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$elApproval]); echo "</pre>"; exit;

//            if(empty($elApproval)) {
//                throw API_Exception::factory(500,'Incorrect EAR id');
//            }

//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$elApproval]); echo "</pre>"; exit;
            $elApproval = $this->getApproveElementsExpandedData([$elApproval])[0];

//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($elApproval); echo "</pre>"; exit;
            $this->_responseData = [
                'status' => "success",
                'item' => $elApproval
            ];
        }  catch (Exception $e){
            throw API_Exception::factory(500,'Operation Error');
//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$e->getMessage()]); echo "</pre>"; exit;
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
            $limit = 20;
            $params = array_diff(Arr::merge(Request::current()->param(),['page' => '']),array(''));
            $page = isset(Request::current()->param()['page']) && Request::current()->param()['page'] ? Request::current()->param()['page'] : 1;

            $filters = Arr::extract($_POST,
                [
                    'companyId',
                    'projectId',
                    'objectIds',
                    'placeIds',
                    'elementIds',
                    'specialityIds',
                    'managerStatuses',
                    'statuses',
                    'positions',
                ]);

            $filters['from'] = $_POST['from'] ? DateTime::createFromFormat('d/m/Y H:i',$_POST['from'] . ' 00:00')->getTimestamp() : null;
            $filters['to'] = $_POST['to'] ? DateTime::createFromFormat('d/m/Y H:i',$_POST['to'] . ' 23:59')->getTimestamp() : null;

            $valid = Validation::factory($filters);

            $valid
                ->rule('companyId', 'not_empty')
                ->rule('projectId', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'missing required field companyId or projectId');
            }

            $count = Api_DBElApprovals::getElApprovalsListCount($filters)[0]['reportsCount'];

            $pagination = Pagination::factory(array(
                    'total_items'    => $count,
                    'items_per_page' => $limit,
                )
            )
                ->route_params($params);

            $elApprovals = Api_DBElApprovals::getElApprovalsList($pagination->items_per_page, $pagination->offset, $filters, true);

            $elApprovals = $this->getApproveElementsExpandedData($elApprovals);

            $this->_responseData = [
                'status' => 'success',
                'items' => $elApprovals,
                'pagination' => ['total' => $count, 'offset' => $pagination->offset, 'limit' => $limit],
            ];
        }  catch (Exception $e) {
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * returns element approvals filtered list (return only EAR's which available to current user)
     * returned data will be as rows[{}]
     * https://qforb.net/api/json/<appToken>/el-approvals/list/user
     */
    public function action_list_user_post() {
        try {
            $filters = Arr::extract($_POST,
                [
                    'companyId',
                    'projectId',
                    'objectIds',
                    'placeIds',
                    'elementIds',
                    'specialityIds',
                    'statuses',
                    'positions',
                ]);

            $valid = Validation::factory($filters);

            $valid
                ->rule('companyId', 'not_empty')
                ->rule('projectId', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'missing required field companyId or projectId');
            }

            $elApprovals = Api_DBElApprovals::getElApprovalsByUserId($filters, Auth::instance()->get_user()->id);
            $elApprovals = $this->getApproveElementsExpandedData($elApprovals);

            $this->_responseData = [
                'status' => 'success',
                'items' => $elApprovals
            ];
        } catch (Exception $e){
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * returns element approvals filtered list (return only EAR's which available to current user)
     * returned data will be as rows[{}]
     * https://qforb.net/api/json/<appToken>/el-approvals/list/user
     */
    public function action_list_user_get() {
        try {
            $filters = Arr::extract($_GET,
                [
                    'companyId',
                    'projectId',
                    'objectIds',
                    'placeIds',
                    'elementIds',
                    'specialityIds',
                    'statuses',
                    'positions',
                ]);

            $valid = Validation::factory($filters);

            $valid
                ->rule('companyId', 'not_empty')
                ->rule('projectId', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'missing required field companyId or projectId');
            }

            $elApprovals = Api_DBElApprovals::getElApprovalsByUserId($filters, Auth::instance()->get_user()->id);
            $elApprovals = $this->getApproveElementsExpandedData($elApprovals);

            $this->_responseData = [
                'status' => 'success',
                'items' => $elApprovals
            ];
        } catch (Exception $e){
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * returns specific speciality of element approval
     * https://qforb.net/api/json/<appToken>/el-approvals/<el_app_id>/specialities/<craftId>
     */
    public function action_speciality_get(){
        $elApprovalId = $this->getUIntParamOrDie($this->request->param('elAppId'));
        $elApprovalSpecialityId = $this->getUIntParamOrDie($this->request->param('craftId'));

        try {
            $elApproval = Api_DBElApprovals::getElApprovalById($elApprovalId);

            if(empty($elApproval)) {
                throw API_Exception::factory(500,'Incorrect ear id');
            }

            $elApproval = $this->getApproveElementsExpandedData($elApproval);

            $elApprovalCraft = null;

            foreach ($elApproval[0]['specialities'] as $speciality) {
                if((int)$speciality['id'] === $elApprovalSpecialityId) {
                    $elApprovalCraft = $speciality;
                }
            }

            if(!$elApprovalCraft) {
                throw API_Exception::factory(500,'Incorrect craft id');
            }

            $this->_responseData = [
                'status' => 'success',
                'item' => $elApprovalCraft
            ];

        } catch (Exception $e) {
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * returns specific speciality of element approval
     * https://qforb.net/api/json/<appToken>/el-approvals/<el_app_id>/add_signature(/<craftId>)
     */
    public function action_add_signature_post()
    {
        $elAppId = $this->getUIntParamOrDie($this->request->param('elAppId'));
        $elAppCraftId = $_GET['craftId'] ?: null;

        try {
            $elApproval = Api_DBElApprovals::getElApprovalById($elAppId)[0];

            if(!$elApproval) {
                throw API_ValidationException::factory(500, 'Incorrect EAR id');
            }

            $this->_responseData = [];
            if($elApproval['status'] === Enum_ApprovalStatus::Approved) {
                DB::delete('el_app_signatures')
                    ->where('el_app_id', '=', $elAppId)
                    ->and_where('el_app_craft_id', 'IS', NULL)
                    ->execute($this->_db);

                $this->_responseData = [
                    'status' => 'success'
                ];

            } else {
                $clientData = Arr::extract($_POST,
                    [
                        'name',
                        'position',
                        'image'
                    ]);

                $valid = Validation::factory($clientData);

                $valid
                    ->rule('name', 'not_empty')
                    ->rule('position', 'not_empty')
                    ->rule('image', 'not_empty');

                if (!$valid->check()) {
                    throw API_ValidationException::factory(500, 'missing required field in signatures');
                }

                $elApprovalCraftSignatureImagePath = DOCROOT.'media/data/projects/'.$elApproval['projectId'].'/el-approvals';

                if(!file_exists($elApprovalCraftSignatureImagePath)) {
                    mkdir($elApprovalCraftSignatureImagePath, 0777, true);
                }

                $imageData = [
                    'fileName' => $elAppId.'_'.uniqid().'.png',
                    'fileOriginalName' => $elAppId.'_'.time().'.png',
                    'filePath' => null,
                    'src' => $clientData['image'],
                    'ext' => 'png'
                ];
                $file = $this->_b64Arr([$imageData], $elApprovalCraftSignatureImagePath);

                $queryData = [
                    'el_app_id' => $elAppId,
                    'el_app_craft_id' => $elAppCraftId,
                    'name' => $clientData['name'],
                    'position' => $clientData['position'],
                    'image' => str_replace(DOCROOT,'',$elApprovalCraftSignatureImagePath.'/'.$file[0]['name']),
                    'created_at' => time(),
                    'created_by' => Auth::instance()->get_user()->id
                ];

                Database::instance()->begin();

                DB::insert('el_app_signatures')
                    ->columns(array_keys($queryData))
                    ->values(array_values($queryData))
                    ->execute($this->_db);

                $this->updateElementApproval($elAppId);

                if($elAppCraftId) {
                    $this->updateElementApprovalCraft($clientData['id']);
                }

                Database::instance()->commit();

                $managerSignature = Api_DBElApprovals::getElApprovalManagerSignatureByElAppId($elAppId)[0];

                $this->_responseData = [
                    'status' => 'success',
                    'item' => $managerSignature
                ];
            }
        } catch (Exception $e) {
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * returns list of positions of EAR signatures by Project id
     * https://qforb.net/api/json/<appToken>/el-approvals/positions/<id>
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
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Update manager status of EAR
     * https://qforb.net/api/json/<appToken>/el-approvals/<id>/status
     */
    public function action_status_put(){
        try {
            $elApprovalId = $this->getUIntParamOrDie($this->request->param('id'));
            $status = Arr::get($this->put(), 'status');

            if(!in_array($status,Enum_ApprovalStatus::toArray(),true)) {
                throw API_ValidationException::factory(500, 'Invalid status');
            }

            if(($this->_user->getRelevantRole('name') !== 'super_admin') && ($status === 'waiting')) {
                throw API_Exception::factory(500,'Operation Error');
            }

            $elApproval = Api_DBElApprovals::getElApprovalById($elApprovalId)[0];

            if(!$elApproval) {
                throw API_Exception::factory(500,'Invalid ear id');
            }

            if( ($elApproval['appropriate'] === '0') && ($status === Enum_ApprovalStatus::Approved) ) {
                throw API_Exception::factory(500,'Can\'t update Ear manager status to approved');
            }

//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($elApproval); echo "</pre>"; exit;

            Database::instance()->begin();

            $queryData = [
                'status' => $status,
                'updated_at' => time(),
                'updated_by' => Auth::instance()->get_user()->id,
            ];

            DB::update('el_approvals')
                ->set($queryData)
                ->where('id', '=', $elApprovalId)
                ->execute($this->_db);

            $this->updateElementApproval($elApprovalId);
            Database::instance()->commit();


            $this->_responseData = [
                'status' => "success"
            ];
        } catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$e->getMessage()]); echo "</pre>"; exit;
        }
    }

    /**
     * Update note of EAR
     * https://qforb.net/api/json/<appToken>/el-approvals/<id>/note
     */
    public function action_note_put(){
        try {
            $elApprovalId = $this->getUIntParamOrDie($this->request->param('id'));
            $note = Arr::get($this->put(), 'note');

            $elApproval = Api_DBElApprovals::getElApprovalById($elApprovalId);

            if(!$elApproval) {
                throw API_ValidationException::factory(500, 'Incorrect EAR id');
            }

//            if($elApproval[0]['status'] === 'approved') {
//                throw API_ValidationException::factory(500, 'Can\'t modify readable EAR');
//            }

            $queryData = [
                'notice' => $note,
                'updated_at' => time(),
                'updated_by' => Auth::instance()->get_user()->id,
            ];

            Database::instance()->begin();


            DB::update('el_approvals')
                ->set($queryData)
                ->where('id', '=', $elApprovalId)
                ->execute($this->_db);

            $this->updateElementApproval($elApprovalId);

            Database::instance()->commit();

            $this->_responseData = [
                'status' => "success"
            ];
        } catch (Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, 'el app note update log: ' . json_encode([$e->getMessage()], JSON_PRETTY_PRINT));
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * delete EAR
     * https://qforb.net/api/json/<appToken>/el-approvals/<id>/delete
     */
    public function action_remove_delete(){
        try {
            $elApprovalId = $this->getUIntParamOrDie($this->request->param('id'));

            Database::instance()->begin();

            DB::delete('el_approvals')->where('id', '=', $elApprovalId)->execute($this->_db);

            PushNotification::notifyElAppUsers($elApprovalId, Enum_NotifyAction::Deleted);

            Database::instance()->commit();

            $this->_responseData = [
                'status' => "success"
            ];
        } catch (Exception $e){
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
                array_push($responseData, $user['userId']);
            }
            $this->_responseData = [
                'status' => "success",
                'items' => $responseData
            ];
        } catch (Exception $e){
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Update list of users to be informed
     * https://qforb.net/api/json/<appToken>/el-approvals/<id>/notifications
     */
    public function action_notifications_put(){
        $elApprovalId = $this->getUIntParamOrDie($this->request->param('id'));

        try {
            $userIds = Arr::get($this->put(), 'userIds');
            $uniqueUserIds = array_unique($userIds);

            if(count($userIds) !== count($uniqueUserIds)) {
                throw API_Exception::factory(500,'Incorrect Data');
            }

            if(!empty($uniqueUserIds)) {
                Database::instance()->begin();

                DB::delete('el_approvals_notifications')
                    ->where('ell_app_id', '=', $elApprovalId)
                    ->execute($this->_db);

                foreach ($uniqueUserIds as $userId) {
                    $queryData = [
                        'ell_app_id' => $elApprovalId,
                        'user_id' => $userId
                    ];

                    DB::insert('el_approvals_notifications')
                        ->columns(array_keys($queryData))
                        ->values(array_values($queryData))
                        ->execute($this->_db);
                }

                $this->updateElementApproval($elApprovalId);

                Database::instance()->commit();

                $this->_responseData = [
                    'status' => "success"
                ];
            } else {
                throw API_Exception::factory(500,'Incorrect identifier');
            }
        } catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * exports EAR as xls
     * https://qforb.net/api/json/<appToken>/projects/<project_id>/el-approvals/export_xls
     */
    public function action_export_xls_get(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));

        if ($lang = Arr::get($_GET, 'lang')) {
            I18n::lang($lang);
            Language::setCurrentLang($lang);
        }

        $filters = [
            'projectId' => $projectId,
            'statuses' => json_decode($_GET['statuses']),
            'objectIds' => json_decode($_GET['objectIds']),
            'floorIds' => json_decode($_GET['floorIds']),
            'placeIds' => json_decode($_GET['placeIds']),
            'elementIds' => json_decode($_GET['elementIds']),
            'specialityIds' => json_decode($_GET['specialityIds']),
            'companyId' => json_decode($_GET['companyId']),
            'positions' => json_decode($_GET['positions']),
        ];

        $filters['from'] = $_GET['from'] ? DateTime::createFromFormat('d/m/Y H:i',$_GET['from'] . ' 00:00')->getTimestamp() : null;
        $filters['to'] = $_GET['to'] ? DateTime::createFromFormat('d/m/Y H:i',$_GET['to'] . ' 23:59')->getTimestamp() : null;

        $elApprovals = Api_DBElApprovals::getElApprovalsList(null,null, $filters);

        $elApprovals = $this->getApproveElementsExpandedData($elApprovals);


        if (!empty($elApprovals)) {
            $ws = new Spreadsheet(array(
                'author'       => 'Q4B',
                'title'	       => 'Report',
                'subject'      => 'Subject',
                'description'  => 'Description',
            ));

            $ws->set_active_sheet(0);
            $as = $ws->get_active_sheet();
            $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];

            $as->setTitle('Element approval reports');

            $as->getDefaultStyle()->getFont()->setSize(10);
            foreach ($cols as $col) {
                if($col === 'E') {
                    $as->getColumnDimension("$col")->setWidth(40);
                } else {
                    $as->getColumnDimension("$col")->setWidth(20);
                }
            }
            $as->getRowDimension('1')->setRowHeight(80);
            $as->getRowDimension('2')->setRowHeight(25);
            $as->setAutoFilter('A2:K2');

            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setName('Logo');
            $objDrawing->setDescription('Logo');
            $objDrawing->setPath(DOCROOT. 'media/img/q4b_logo.png');
            $objDrawing->setResizeProportional(true);
            $objDrawing->setWidth(60);
            $objDrawing->setHeight(100);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setOffsetX(10);
            $objDrawing->setWorksheet($as);

            $objDrawingSec = new PHPExcel_Worksheet_Drawing();
            $objDrawingSec->setPath(DOCROOT. 'media/img/q4b_quality.png');
            $objDrawingSec->setName('quality logo');
            $objDrawingSec->setCoordinates('B1');
            $objDrawingSec->setWorksheet($as);
            $objDrawingSec->setResizeProportional(true);
            $objDrawingSec->setHeight(90);
            $objDrawingSec->setOffsetX(40);
            $objDrawingSec->setOffsetY(10);

            $excelRows = [
                2 => [
                    __('check_number'),
                    __('check_date'),
                    __('Structure'),
                    __('Element_item'),
                    __('notes_description'),
                    __('Craft'),
                    __('Floor'),
                    __('Status'),
                    __('approval_date'),
                    __('Position'),
                    __('signer_name'),
                    __('Signature'),
                ],
            ];
            if (Language::getCurrent()->direction == 'rtl') {
                foreach ($cols as $col) {
                    $as->getStyle($col)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $as->getStyle($col)->getFont()->setSize(10);
                }
            }
            foreach ($cols as $col) {
                $as->getStyle($col)
                    ->getAlignment()
                    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            }
            $ws->set_data($excelRows, false);

            foreach ($elApprovals as $elApproval){
                $elAppRow =  [
                    $elApproval['id'],
                    date('d/m/Y', $elApproval['createdAt']),
                    $elApproval['objectName'],
                    $elApproval['elementName'],
                    $elApproval['notice'],
                    '',
                    $elApproval['floorName'] ?: $elApproval['floorNumber'],
                    $elApproval['appropriate'] ? __('appropriate') : __('not_appropriate'),
                    !empty($elApproval['managerSignature']) ? date('d/m/Y', $elApproval['managerSignature']['createdAt']) : '',
                    !empty($elApproval['managerSignature']) ? $elApproval['managerSignature']['position'] : '',
                    !empty($elApproval['managerSignature']) ? $elApproval['managerSignature']['name'] : '',
                    ''
                ];

                if(!empty($elApproval['managerSignature'])) {
                    $imagePath = null;

                    if(file_exists(DOCROOT.$elApproval['managerSignature']['image'])) {
                        $imagePath = DOCROOT.$elApproval['managerSignature']['image'];
                    }
                    if($imagePath) {
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('managerSignature');
                        $objDrawing->setDescription('managerSignature');
                        $objDrawing->setPath($imagePath);
                        $objDrawing->setResizeProportional(true);
                        $objDrawing->setWidth(60);
                        $objDrawing->setHeight(60);
                        $objDrawing->setCoordinates('L'.(count($excelRows)+2));
                        $objDrawing->setOffsetX(10);
                        $objDrawing->setWorksheet($as);
                    }
                } else {
                    $elAppRow[] = '';
                }

                $excelRows[] = $elAppRow;


                foreach ($elApproval['specialities'] as $speciality) {
                    $elAppCraftRow = [
                        '',
                        '',
                        '',
                        '',
                        '',
                        $speciality['craftName'],
                        '',
                        $speciality['appropriate'] ? __('appropriate') : __('not_appropriate'),
                        !empty($speciality['signatures']) ? date('d/m/Y', $speciality['signatures'][0]['createdAt']) : date('d/m/Y', $speciality['updatedAt']),
                        !empty($speciality['signatures']) ? $speciality['signatures'][0]['position'] : '',
                        !empty($speciality['signatures']) ? $speciality['signatures'][0]['creatorName'] : '',
                    ];

                    if(!empty($speciality['signatures'])) {
                        $imagePath = null;
                        for($count = count($speciality['signatures']) - 1; $count >= 0; $count--) {
                            if(file_exists(DOCROOT.$speciality['signatures'][$count]['image'])) {
                                $imagePath = DOCROOT.$speciality['signatures'][$count]['image'];
                                break;
                            }
                        }
                        if($imagePath) {
                            $objDrawing = new PHPExcel_Worksheet_Drawing();
                            $objDrawing->setName('signature');
                            $objDrawing->setDescription('signature');
                            $objDrawing->setPath($imagePath);
                            $objDrawing->setResizeProportional(true);
                            $objDrawing->setWidth(60);
                            $objDrawing->setHeight(60);
                            $objDrawing->setCoordinates('L'.(count($excelRows)+2));
                            $objDrawing->setOffsetX(10);
                            $objDrawing->setWorksheet($as);
                        }
                    } else {
                        $elAppCraftRow[] = '';
                    }
                    $excelRows[] = $elAppCraftRow;
                }
             }

            for($i = 0;$i <= count($excelRows); $i++) {
                $as->getRowDimension((string)($i+2))->setRowHeight(60);
            }
            $ws->set_data($excelRows, false);
            $first_letter = PHPExcel_Cell::stringFromColumnIndex(0);
            $last_letter = PHPExcel_Cell::stringFromColumnIndex(count($excelRows[2])-1);
            $header_range = "{$first_letter}2:{$last_letter}2";
            $ws->get_active_sheet()->getStyle($header_range)->getFont()->setSize(14)->setBold(true);
            $ws->get_active_sheet()->getStyle('K1:K999')
                ->getAlignment()->setWrapText(true);
            $ws->get_active_sheet()->getStyle('E1:E999')
                ->getAlignment()->setWrapText(true);
            $ws->get_active_sheet()->getStyle('A1:A999')
                ->getAlignment()->setWrapText(true);
            $ws->rtl(Language::getCurrent()->direction === 'rtl');
            $ws->send(['name'=>'element-approval-reports', 'format'=>'Excel5']);
        }
    }

    /**
     * exports EAR as pdf
     * https://qforb.net/api/json/<appToken>/projects/<project_id>/el-approvals/export_pdf
     */
    public function action_export_pdf_get(){

        $filters = Arr::extract($_GET, [
            'projectId',
            'statuses',
            'objectIds',
            'floorIds',
            'placeIds',
            'elementIds',
            'specialityIds',
            'companyId',
            'positions'
        ]);

        $lang = Arr::get($_GET,'lang', 'en');

        $valid = Validation::factory($filters);

        $valid
            ->rule('companyId', 'not_empty')
            ->rule('projectId', 'not_empty');

        if (!$valid->check()) {
            throw API_ValidationException::factory(500, 'Incorrect data');
        }

        $filePath = $this->_makePdf(URL::withLang('reports/approve_element', $lang,'https').'?'.http_build_query($filters));

        header('Location: '.URL::withLang($filePath,'en'));exit;
    }

    private function _makePdf($url){
        $client = Client::getInstance();
        $client->getEngine()->setPath(DOCROOT.'phantomjs-2.1.1-linux-x86_64/bin/phantomjs');
//        $client->getEngine()->setPath('/home/qforbnet/www/phantomjs-2.1.1-linux-x86_64/bin/phantomjs');
        $client->getEngine()->addOption('--cookies-file=cook.txt');


        $request = $client->getMessageFactory()->createCaptureRequest('https://qforb.sunrisedvp.systems', 'GET');
//        $request = $client->getMessageFactory()->createCaptureRequest('https://qforb.net', 'GET');

        $request->addHeader('Pjsbot76463', '99642');

        $response = $client->getMessageFactory()->createResponse();
        $client->send($request, $response);

        /**
         * @see JonnyW\PhantomJs\Http\CaptureRequest
         **/
        $request = $client->getMessageFactory()->createPdfRequest($url, 'GET',15000);
        $request->addHeader('Pjsbot76463', '99642');

        $filePath = 'media/data/el-approval-tmp/'.uniqid().'.pdf';
        $request->setOutputFile(DOCROOT.$filePath);
        $request->setFormat('A4');
//        $request->setOrientation('landscape');
        $request->setViewportSize(1920, 1690);
        $request->setPaperSize(1960, 1750);
        /**
         * @see JonnyW\PhantomJs\Http\Response
         **/
        $response = $client->getMessageFactory()->createResponse();
        $request->setDelay(3);
        // Send the request
        $client->send($request, $response);
        return $filePath;
    }

    /**
     * returns element approvals with expanded data (specialities,tasks,signatures,qc)
     * */
    private function getApproveElementsExpandedData($elApprovals) :array
    {
        $elApprovalIds = array_column($elApprovals, 'id');

        if(!empty($elApprovalIds)) {
            $qualityControls = Api_DBElApprovals::getElApprovalQCListByIds($elApprovalIds);
            $specialities = Api_DBElApprovals::getElApprovalCraftsByElAppIds($elApprovalIds);
            $specialityIds = array_column($specialities, 'id');
            $tasks = Api_DBElApprovals::getElApprovalCraftsTasksByCraftIds($specialityIds);
            $signatures = Api_DBElApprovals::getElApprovalCraftsSignaturesByCraftIds($specialityIds);

            foreach ($elApprovals as $elAppKey => $elApproval) {
                $elApprovals[$elAppKey]['specialities'] = [];

                foreach ($specialities as $speciality) {
                    if($speciality['elAppId'] === $elApproval['id']) {
                        array_push($elApprovals[$elAppKey]['specialities'], $speciality);
                        $currentSpecialityKey = count($elApprovals[$elAppKey]['specialities']) - 1;

                        $elApprovals[$elAppKey]['specialities'][$currentSpecialityKey]['qualityControl'] = null;
                        $elApprovals[$elAppKey]['specialities'][$currentSpecialityKey]['tasks'] = [];
                        $elApprovals[$elAppKey]['specialities'][$currentSpecialityKey]['signatures'] = [];

                        foreach ($qualityControls as $qc) {
                            if(($qc['craftId'] === $speciality['craftId']) && ($qc['elApprovalId'] === $elApproval['id'])) {
                                $elApprovals[$elAppKey]['specialities'][$currentSpecialityKey]['qualityControl'] = $qc['id'];
                            }
                        }
                        foreach ($tasks as $task) {
                            if($task['ellAppCraftId'] === $speciality['id']) {
                                array_push($elApprovals[$elAppKey]['specialities'][$currentSpecialityKey]['tasks'], $task);
                            }
                        }
                        foreach ($signatures as $signature) {
                            if(($signature['elAppCraftId'] === $speciality['id']) && ($signature['elAppId'] === $elApproval['id'])) {
                                array_push($elApprovals[$elAppKey]['specialities'][$currentSpecialityKey]['signatures'], $signature);
                            }
                        }
                    }
                }
                $elApprovals[$elAppKey]['managerSignature'] = Api_DBElApprovals::getElApprovalManagerSignatureByElAppId($elApproval['id'])[0];
            }
            return $elApprovals;
        } else {
            return [];
        }
    }

    /**
     * update EAR (updated_at, updated_by)
     * */
    private function updateElementApproval($elApprovalId) {
        $queryData = [
            'updated_at' => time(),
            'updated_by' => Auth::instance()->get_user()->id
        ];


        DB::update('el_approvals')
            ->set($queryData)
            ->where('id', '=', $elApprovalId)
            ->execute($this->_db);

//        $this->sendNotificationToUsers($elApprovalId);
        PushNotification::notifyElAppUsers($elApprovalId, Enum_NotifyAction::Updated);
    }

    /**
     * update craft of EAR (updated_at, updated_by)
     * */
    private function updateElementApprovalCraft($elApprovalCraftId) {
        $queryData = [
            'updated_at' => time(),
            'updated_by' => Auth::instance()->get_user()->id
        ];

        DB::update('el_approvals_crafts')
            ->set($queryData)
            ->where('id', '=', $elApprovalCraftId)
            ->execute($this->_db);
    }

//    PushNotification::notifyElAppUsers($elApprovalId);

//    private function sendNotificationToUsers($elApprovalId) {
//
////      $elApproval = Api_DBElApprovals::getElApprovalById($elApprovalId);
//
//      $users = Api_DBElApprovals::getElApprovalUsersListForNotify($elApprovalId);
//
//      $usersDeviceTokens = [];
//
//      foreach ($users as $user) {
//          if($user['deviceToken']) {
//              array_push($usersDeviceTokens, $user['deviceToken']);
//          }
//      }
////
////        echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$usersDeviceTokens]); echo "</pre>"; exit;
//
//        $timestamp = time();
//
////        $usersDeviceTokens = ['f5bWjICSSMiE40tO7w5RF2:APA91bGGAwSYAYz5t7b1l8jnC385xjLGne5FkWh2LxHQ9W19AflFCnNHsLo8nF1Ydn9_w3dd2a1BmhGFPfLlmGMrWmB0z3k5hQ77bq0zljFxPQAasA9tBjA45rXHb-uXZ6NFgQKklP0i'];
////            Kohana::$log->add(Log::ERROR, 'from elApprovals: ' . json_encode([$users], JSON_PRETTY_PRINT));
//
//        PushHelper::test([
//            'lang' => \Language::getCurrent()->iso2,
//            'action' => 'elApproval',
//            'usersDeviceTokens' => $usersDeviceTokens
//        ], $timestamp );
//
//
//    }
}