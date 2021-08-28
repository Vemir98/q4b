<?php
use JonnyW\PhantomJs\Client;


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

            Database::instance()->begin();
            $queryData = [
                'company_id' => $clientData['company_id'],
                'project_id' => $clientData['project_id'],
                'object_id' => $clientData['object_id'],
                'place_id' => $clientData['place_id'] ?: null,
                'element_id' => $clientData['element_id'],
                'floor_id' => $clientData['floor_id'],
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
                        throw API_ValidationException::factory(500, 'Incorrect data');
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

                        $elApprovalCraftSignatureImagesPath = DOCROOT.'media/data/projects/'.$clientData['project_id'].'/el-approvals';
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
                                throw API_ValidationException::factory(500, 'Incorrect data');
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
                    } else {
                        throw API_ValidationException::factory(500, 'Incorrect data');
                    }
                }
            } else {
                throw API_ValidationException::factory(500, 'Incorrect data');
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
            $elApproval = Api_DBElApprovals::getElApprovalById($elApprovalId);

            if(!$elApproval) {
                throw API_ValidationException::factory(500, 'Incorrect data1');
            }

            if($elApproval[0]['status'] === 'approved') {
                throw API_ValidationException::factory(500, 'Cant modify readable data');
            }

            $clientData = Arr::extract($this->put(),
                [
                    'id',
                    'project_id',
                    'notice',
                    'signatures',
                    'deleted_signatures',
                    'tasks',
                ]);


            $valid = Validation::factory($clientData);
            $valid
                ->rule('id', 'not_empty')
                ->rule('project_id', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'Incorrect data22');
            }

            $elApprovalCraftId = $clientData['id'];

            Database::instance()->begin();
            $queryData = [
                'notice' => $clientData['notice']
            ];

            DB::update('el_approvals_crafts')
                ->set($queryData)
                ->where('id', '=', $elApprovalCraftId)
                ->execute($this->_db);

            if(!empty($clientData['deleted_signatures'])) {
                foreach ($clientData['deleted_signatures'] as $signatureId) {
                    DB::delete('el_app_signatures')
                        ->where('id', '=', $signatureId)
                        ->execute($this->_db);
                }
            }

            if(!empty($clientData['signatures'])) {
                $elApprovalCraftSignatureImagesPath = DOCROOT.'media/data/projects/'.$clientData['project_id'].'/el-approvals';
                if(!file_exists($elApprovalCraftSignatureImagesPath)) {
                    mkdir($elApprovalCraftSignatureImagesPath, 0777, true);
                }

                foreach ($clientData['signatures'] as $signature) {
                    $valid = Validation::factory($signature);

                    $valid
                        ->rule('name', 'not_empty')
                        ->rule('position', 'not_empty')
                        ->rule('image', 'not_empty');

                    if (!$valid->check()) {
                        throw API_ValidationException::factory(500, 'Incorrect data3');
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

            if(!empty($clientData['tasks'])) {
                foreach ($clientData['tasks'] as $task) {
                    $valid = Validation::factory($task);

                    $valid
                        ->rule('id', 'not_empty')
                        ->rule('appropriate', 'not_empty');

                    if (!$valid->check()) {
                        throw API_ValidationException::factory(500, 'Incorrect data4');
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

            $this->updateElementApproval($elApprovalId);
            $this->updateElementApprovalCraft($clientData['id']);

            Database::instance()->commit();
            $this->_responseData = [
                'status' => "success",
            ];
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

            if(empty($elApproval)) {
                throw API_Exception::factory(500,'Incorrect identifier');
            }

            $elApproval = $this->getApproveElementsExpandedData($elApproval);
            $this->_responseData = [
                'status' => "success",
                'items' => $elApproval
            ];
        }  catch (Exception $e){
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
            $limit = 20;
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
                    'managerStatuses',
                    'statuses',
                    'positions',
                ]);

//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($filters); echo "</pre>"; exit;
            $filters['from'] = $_POST['from'] ? DateTime::createFromFormat('d/m/Y H:i',$_POST['from'] . ' 00:00')->getTimestamp() : null;
            $filters['to'] = $_POST['to'] ? DateTime::createFromFormat('d/m/Y H:i',$_POST['to'] . ' 23:59')->getTimestamp() : null;

            $valid = Validation::factory($filters);

            $valid
                ->rule('company_id', 'not_empty')
                ->rule('project_id', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'Incorrect data');
            }

            $count = count(Api_DBElApprovals::getElApprovalsList(null,null, $filters));

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
     * returns element approvals filtered list
     * returned data will be as rows[{}],pages: {total,offset,limit}
     * https://qforb.net/api/json/<appToken>/el-approvals/list
     * https://qforb.net/api/json/<appToken>/el-approvals/list/user
     */
    public function action_list_user_post() {
        try {

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

            $valid = Validation::factory($filters);

            $valid
                ->rule('company_id', 'not_empty')
                ->rule('project_id', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'Incorrect data');
            }

            $elApprovals = Api_DBElApprovals::getUserElementApprovals($filters);
            $elApprovals = $this->getApproveElementsExpandedData($elApprovals);

            $this->_responseData = [
                'status' => 'success',
                'items' => $elApprovals
            ];
        } catch (Exception $e){
            throw API_Exception::factory(500,'Operation Error');
//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$e->getMessage()]); echo "</pre>"; exit;
        }
    }

    /**
     * returns specific speciality of element approval
     * https://qforb.net/api/json/<appToken>/el-approvals/<el_app_id>/specialities/<craft_id>
     *
     */
    public function action_speciality_get(){
        $elApprovalId = $this->getUIntParamOrDie($this->request->param('el_app_id'));
        $elApprovalSpecialityId = $this->getUIntParamOrDie($this->request->param('craft_id'));

        try {
            $elApprovalCraft = Api_DBElApprovals::getElApprovalCraftByCraftId($elApprovalSpecialityId);
            $qualityControls = Api_DBElApprovals::getQualityControlsListByElAppId($elApprovalId);


            $elApprovalCraft[0]['signatures'] = Api_DBElApprovals::getElApprovalCraftsSignaturesByCraftIds($elApprovalSpecialityId);
            $elApprovalCraft[0]['tasks'] = Api_DBElApprovals::getElApprovalCraftsTasksByCraftIds($elApprovalSpecialityId);

            $qcKeys = array_keys(array_column($qualityControls, 'craft_id'), $elApprovalCraft[0]['craft_id']);
            if(!empty($qcKeys)) {
                $elApprovalCraft[0]['quality_controls'] = [];
                foreach ($qcKeys as $qcKey) {
                    $qualityControls[$qcKey]['images'] = Api_DBElApprovals::getQualityControlImages($qualityControls[$qcKey]['id']);
                    $elApprovalCraft[0]['quality_controls'] = $qualityControls[$qcKey];
                }
            } else {
                $elApprovalCraft[0]['quality_controls'] = null;
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
     * returns list of positions of element approval report signatures by Project id
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
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Update manager status of element approval report
     * https://qforb.net/api/json/<appToken>/el-approvals/<id>/status
     */
    public function action_status_put(){
        try {
            $elApprovalId = $this->getUIntParamOrDie($this->request->param('id'));
            $status = Arr::get($this->put(), 'status');

            if(!in_array($status,Enum_ApprovalStatus::toArray(),true)) {
                throw API_ValidationException::factory(500, 'Incorrect data');
            }

            if(($this->_user->getRelevantRole('name') !== 'super_admin') && ($status === 'waiting')) {
                throw API_Exception::factory(500,'Operation Error');
            }
            $queryData = [
                'status' => $status,
                'updated_at' => time(),
                'updated_by' => Auth::instance()->get_user()->id,
            ];

            DB::update('el_approvals')
                ->set($queryData)
                ->where('id', '=', $elApprovalId)
                ->execute($this->_db);

            $this->_responseData = [
                'status' => "success"
            ];
        } catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * delete element approval report
     * https://qforb.net/api/json/<appToken>/el-approvals/<id>/delete
     */
    public function action_remove_delete(){
        try {
            $elApprovalId = $this->getUIntParamOrDie($this->request->param('id'));

            DB::delete('el_approvals')->where('id', '=', $elApprovalId)->execute($this->_db);

            $this->_responseData = [
                'status' => "success"
            ];
        } catch (Exception $e){
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
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Updated list of users to be informed
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

                DB::delete('el_approvals_notifications')->where('ell_app_id', '=', $elApprovalId)->execute($this->_db);
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
     * exports element approval report as xls
     * https://qforb.net/api/json/<appToken>/projects/<project_id>/el-approvals/export_xls
     */
    public function action_export_xls_get(){
        $projectId = $this->getUIntParamOrDie($this->request->param('project_id'));

        if ($lang = Arr::get($_GET, 'lang')) {
            I18n::lang($lang);
            Language::setCurrentLang($lang);
        }

        $filters = [
            'project_id' => $projectId,
            'statuses' => json_decode($_GET['statuses']),
            'object_ids' => json_decode($_GET['object_ids']),
            'floor_ids' => json_decode($_GET['floor_ids']),
            'place_ids' => json_decode($_GET['place_ids']),
            'element_ids' => json_decode($_GET['element_ids']),
            'speciality_ids' => json_decode($_GET['speciality_ids']),
            'company_id' => json_decode($_GET['company_id']),
            'positions' => json_decode($_GET['positions']),
        ];

        $filters['from'] = $_GET['from'] ? DateTime::createFromFormat('d/m/Y H:i',$_GET['from'] . ' 00:00')->getTimestamp() : null;
        $filters['to'] = $_GET['to'] ? DateTime::createFromFormat('d/m/Y H:i',$_GET['to'] . ' 23:59')->getTimestamp() : null;

        $elApprovals = Api_DBElApprovals::getElApprovalsList(null,null, $filters);

        $elApprovals = $this->getApproveElementsExpandedData($elApprovals);

//        echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($elApprovals); echo "</pre>"; exit;

        if (!empty($elApprovals)) {
            $ws = new Spreadsheet(array(
                'author'       => 'Q4B',
                'title'	       => 'Report',
                'subject'      => 'Subject',
                'description'  => 'Description',
            ));

            $ws->set_active_sheet(0);
            $as = $ws->get_active_sheet();
            $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];

            $as->setTitle('Element approval reports');

            $as->getDefaultStyle()->getFont()->setSize(10);
            foreach ($cols as $col) {
                $as->getColumnDimension("$col")->setWidth(20);
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
                $excelRows[] = [
                    $elApproval['id'],
                    date('d/m/Y', $elApproval['created_at']),
                    $elApproval['object_name'],
                    $elApproval['element_name'],
                    '',
                    $elApproval['floor_name'] ?: $elApproval['floor_number'],
                    $elApproval['status'],
                    '',
                    '',
                    '',
                    ''
                ];
                foreach ($elApproval['specialities'] as $speciality) {
                    $row = [
                        '',
                        '',
                        '',
                        '',
                        $speciality['craft_name'],
                        '',
                        $speciality['appropriate'] ? __('appropriate') : __('not_appropriate'),
                        !empty($speciality['signatures']) ? date('d/m/Y', $speciality['signatures'][0]['created_at']) : date('d/m/Y', $speciality['updated_at']),
                        !empty($speciality['signatures']) ? $speciality['signatures'][0]['position'] : '',
                        !empty($speciality['signatures']) ? $speciality['signatures'][0]['creator_name'] : '',
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
                            $objDrawing->setCoordinates('K'.(count($excelRows)+2));
                            $objDrawing->setOffsetX(10);
                            $objDrawing->setWorksheet($as);
                        }
                    } else {
                        $row[] = '';
                    }
                    $excelRows[] = $row;
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
            $ws->rtl(Language::getCurrent()->direction === 'rtl');
            $ws->send(['name'=>'element-approval-reports', 'format'=>'Excel5']);
        }
    }

    public function action_export_pdf_get(){

        $filters = Arr::extract($_GET, [
            'project_id',
            'statuses',
            'object_ids',
            'floor_ids',
            'place_ids',
            'element_ids',
            'speciality_ids',
            'company_id',
            'positions'
        ]);

        $lang = Arr::get($_GET,'lang', 'en');

        $valid = Validation::factory($filters);

        $valid
            ->rule('company_id', 'not_empty')
            ->rule('project_id', 'not_empty');

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
    private function getApproveElementsExpandedData($elApprovals)
    {
        foreach ($elApprovals as $elAppKey => $elApproval) {
            $elApprovals[$elAppKey]['specialities'] = Api_DBElApprovals::getElApprovalCraftsByElAppId($elApproval['id']);
            $qualityControls = Api_DBElApprovals::getQualityControlsListByElAppId($elApproval['id']);

            foreach ($elApprovals[$elAppKey]['specialities'] as $specialityKey => $speciality) {
                $elApprovals[$elAppKey]['specialities'][$specialityKey]['signatures'] = Api_DBElApprovals::getElApprovalCraftsSignaturesByCraftIds($speciality['id']);
                $elApprovals[$elAppKey]['specialities'][$specialityKey]['tasks'] = Api_DBElApprovals::getElApprovalCraftsTasksByCraftIds($speciality['id']);

                $qcKeys = array_keys(array_column($qualityControls, 'craft_id'), $speciality['craft_id']);
                if (!empty($qcKeys)) {
                    $elApprovals[$elAppKey]['specialities'][$specialityKey]['quality_control'] = [];
                    foreach ($qcKeys as $qcKey) {
                        $qualityControls[$qcKey]['images'] = Api_DBElApprovals::getQualityControlImages($qualityControls[$qcKey]['id']);
                        $elApprovals[$elAppKey]['specialities'][$specialityKey]['quality_control'] = $qualityControls[$qcKey];
                    }
                } else {
                    $elApprovals[$elAppKey]['specialities'][$specialityKey]['quality_control'] = null;
                }
            }
        }
        return $elApprovals;
    }

    private function updateElementApproval($elApprovalId) {
        $queryData = [
            'updated_at' => time(),
            'updated_by' => Auth::instance()->get_user()->id
        ];

        DB::update('el_approvals')
            ->set($queryData)
            ->where('id', '=', $elApprovalId)
            ->execute($this->_db);
    }

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
}