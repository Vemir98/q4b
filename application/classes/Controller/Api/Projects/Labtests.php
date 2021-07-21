<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 22.04.2020
 * Time: 13:12
 */

class Controller_Api_Projects_Labtests extends HDVP_Controller_API
{
    /**
     * Get elements list
     * https://qforb.net/api/json/v1/<appToken>/projects/<projectId>/labtests/elements
     * return data [{id,company_id,project_id,name}...]
     */
    public function action_elements_get(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $search = $_GET['search'];
        $elements = Api_DBProjects::getProjectElements($projectId, $search);
        $this->_responseData['items'] = $elements;
    }

    /**
     * returns elements with specialities(crafts)
     * https://qforb.net/api/json/v1/<appToken>/projects/<projectId>/labtests/elements-and-crafts
     * return data [{id,company_id,project_id,name,crafts:{1,2,3...}}...]
     */
    public function action_elements_with_crafts_get(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $elements = Api_DBProjects::getProjectElements($projectId, '');
        $elItems = $elIds = [];
        if(count($elements)){
            foreach ($elements as $el){
                $elItems[$el['id']] = [
                    'id' => $el['id'],
                    'name' => $el['name'],
                    'company_id' => $el['company_id'],
                    'project_id' => $el['project_id'],
                    'crafts' => []
                ];
                $elIds[] = $el['id'];
            }
            $crafts = Api_DBElements::getElementsCrafts($elIds);
            if(count($crafts)){
                foreach ($crafts as $craft){
                    $elItems[$craft['element_id']]['crafts'][] = $craft['id'];
                }
            }
        }
        $this->_responseData['items'] = array_values($elItems);
    }

    /**
     * Create element
     * https://qforb.net/api/json/v1/<appToken>/projects/<projectId>/labtests/elements
     * @throws HTTP_Exception
     */
    public function action_elements_post(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $project = Api_DBProjects::getProjectById($projectId);
        $elements = $_POST;
        try {
            if (!empty($elements)) {
                for ($i=count($elements)-1; $i>=0; $i--) {
                    $el = $elements[$i];
                    $data = [
                        'company_id' => $project[0]['company_id'],
                        'project_id' => $projectId,
                        'name' => Arr::get($el,'name')
                    ];
                    $valid = Validation::factory($data);
                    $valid
                        ->rule('company_id', 'not_empty')
                        ->rule('project_id', 'not_empty')
                        ->rule('name', 'not_empty')
                        //проверка на длину и условие
                        ->rule('name', 'max_length', [':value', '50']);
                    if (!$valid->check()) {
                        throw API_ValidationException::factory(500, 'Incorrect data');
                    }
                    DB::insert('elements')
                        ->columns(array_keys($data))
                        ->values(array_values($data))
                        ->execute($this->_db);
                }
            }
        $elements = Api_DBProjects::getProjectElements($projectId, '');
        $this->_responseData['items'] = $elements;
        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        } catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Updates element
     * https://qforb.net/api/json/v1/<appToken>/projects/<projectId>/labtests/elements/<id>
     * @throws HTTP_Exception
     */
    public function action_elements_put(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        $name = Arr::get($this->put(),'name');
        try {
            $element = Api_DBElements::getElementById($id);
            if(empty($element)){
                throw API_Exception::factory(500,'Incorrect identifier');
            }
            $valid = Validation::factory(['name' => $name]);
            $valid
                ->rule('name', 'not_empty')
                ->rule('name', 'max_length', [':value', '50']);
            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'Incorrect data');
            }
            DB::update('elements')->set(["name" => $name])->where('id', '=', $id)->execute($this->_db);
        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        } catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Deletes element
     * If element used in labtest you cant delete it
     * https://qforb.net/api/json/v1/<appToken>/projects/<projectId>/labtests/elements/<id>
     * @throws HTTP_Exception
     */
    public function action_elements_delete(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        $element = Api_DBElements::getElementById($id);
        if(empty($element)){
            throw API_Exception::factory(500,'Incorrect identifier');
        }
        $labtests = Api_DBLabtests::getLabtestsByElementId($id);
        $res = 0;
        if (empty($labtests)) {
            $res = DB::delete('elements')->where('id', '=', $id)->execute($this->_db);
        }
        $this->_responseData['success'] = $res;

    }


    /**
     * assign elements to specialities(crafts)
     * https://qforb.net/api/json/v1/<appToken>/projects/<projectId>/labtests/elements/assign
     */
    public function action_elements_assign_put(){
        $data = array_values($this->put());
        if (!empty($data)) {
            try {
                $dataToInsert = [];
                $elCraftsToDelete = [];
                foreach ($data as $el) {
                    $elCraftsToDelete[] = $el['id'];
                    if (!empty($el['crafts'])) {
                        foreach ($el['crafts'] as $craft) {
                            $dataToInsert[] = [
                                $el['id'],
                                $craft
                            ];
                        }
                    }
                }
                DB::delete('elements_cmp_crafts')->where('element_id', 'IN', $elCraftsToDelete)->execute($this->_db);
                if (!empty($dataToInsert)) {
                    $query = DB::insert('elements_cmp_crafts', array('element_id', 'craft_id'));
                    foreach ($dataToInsert as $val) {
                        $query->values($val);
                    }
                    $query->execute($this->_db);
                }
            } catch (API_ValidationException $e){
                Database::instance()->rollback();
                throw API_Exception::factory(500,'Incorrect data');
            } catch (Exception $e){
                Database::instance()->rollback();
                throw API_Exception::factory(500,'Operation Error');
            }
        }
    }

    /**
     * Create Labtest
     * https://qforb.net/api/json/v1/<appToken>/projects/1/labtests
     */
    public function action_index_post(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        //post data
        $ltData = Arr::extract($_POST, [
            'floor_id',
            'place_id',
            'craft_id',
            'element_id',
            'plan_id',
            'standard',
            'strength_after',
            'delivery_cert',
            'create_date',
            'cert_number'
        ]);

        try {
            Database::instance()->begin();
            $project = Api_DBProjects::getProjectById($projectId);
            if(empty($project)){
                throw API_Exception::factory(500,'Incorrect identifier');
            }
            $ltData['project_id'] = $projectId;
            $ltData['building_id'] = Arr::get($_POST, 'object_id');
            $ltData['delivery_cert'] =  str_replace('@#$', PHP_EOL, implode("", $ltData['delivery_cert']));
            $ltData['status'] = 'waiting';
            if (!$ltData['place_id']) {
                unset($ltData['place_id']);
            }

            $date = DateTime::createFromFormat('d/m/Y H:i',$ltData['create_date']);
            if($date == null){
                file_put_contents(DOCROOT.'date_err.log',$ltData['create_date']);
                throw API_Exception::factory(500,'Incorrect date format');
            }
            $ltData['create_date'] = $date->getTimestamp();
            $ltData['created_at'] = time();
            $ltData['updated_at'] = time();
            $ltData['created_by'] = Auth::instance()->get_user()->id;
            $valid = Validation::factory($ltData);
            $valid
                ->rule('project_id', 'not_empty')
                ->rule('building_id', 'not_empty')
                ->rule('floor_id', 'not_empty')
                ->rule('element_id', 'not_empty')
                ->rule('standard', 'not_empty')
                ->rule('strength_after', 'not_empty')
                ->rule('created_at', 'not_empty')
                ->rule('created_by', 'not_empty')
                ->rule('create_date', 'not_empty');
            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'Incorrect data');
            }
            $result = DB::insert('labtests')
                ->columns(array_keys($ltData))
                ->values(array_values($ltData))
                ->execute($this->_db);
            $labtest_id = $result[0];
            $slp = Arr::get($_POST, 'slp');

            if ($slp && !empty($slp)) {
                $slpData = [];
                foreach($slp as $item) {
                    $d = [
                        'labtest_id' => $labtest_id,
                        'cl_id' => $item['cl_id'],
                        'clp_id' => $item['clp_id'],
                        'value' => (int) $item['value']
                    ];
                    $valid = Validation::factory($d);
                    $valid
                        //проверка на пустату полей
                        ->rule('labtest_id', 'not_empty')
                        ->rule('cl_id', 'not_empty')
                        ->rule('clp_id', 'not_empty')
                        ->rule('value', 'not_empty');
                    if (!$valid->check()) {
                        throw API_ValidationException::factory(500, 'Incorrect data');
                    }
                    $slpData[] = $d;

                }
                if (!empty($slpData)) {
                    $query = DB::insert('labtest_clp')->columns(['labtest_id', 'cl_id', 'clp_id', 'value']);
                    foreach ($slpData as $d) {
                        $query->values(array_values($d));
                    }
                    $query->execute($this->_db);
                }
            }

            Database::instance()->commit();
            $this->_responseData['labtest_id'] = $labtest_id;
        }catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        }catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,$e->getMessage());
        }
    }

    /**
     * Delete Labtest
     * https://qforb.net/api/json/v1/<appToken>/projects/1/labtests
     */
    public function action_index_delete() {
        $id = (int)$this->request->param('id');
        $labtest = Api_DBLabtests::getLabtestById($id);
        if(empty($labtest)){
            throw API_Exception::factory(500,'Incorrect identifier');
        }
        $tickets = Api_DBLabtests::getLabtestTicketsById($id);
        try {
            Database::instance()->begin();
            $ticketsIds = [];
            if (!empty($tickets)) {
                foreach ($tickets as $ticket) {
                    $ticketsIds[] = $ticket['id'];
                }
            }
            if (!empty($ticketsIds)) {
                DB::delete('labtests_tickets_files')->where('ticket_id', 'IN', $ticketsIds)->execute($this->_db);;
            }
            DB::delete('labtests_tickets')->where('labtest_id', '=', $id)->execute($this->_db);;
            DB::delete('labtests')->where('id', '=', $id)->execute($this->_db);

            Database::instance()->commit();
        } catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }


    }
    /**
     * if passed id returns concrette labtest else returns paginated list of filtered labtests
     * need to pass filter params as GET param
     * GET params
     * from(int),to(int),status(string),object_id(int),floor_id(int),place_id(int),element_id(int),craft_id(int)
     * if returns list returned data will be as rows[{}],pages: {total,offset,limit}
     * else it will be object params
     * https://qforb.net/api/json/v1/<appToken>/projects/1/labtests/<id>
     * https://qforb.net/api/json/v1/<appToken>/projects/1/labtests/<id>/page/<page>
     */
    public function action_index_get(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $id = (int)$this->request->param('id');
        if($id){
            $labtest = Api_DBLabtests::getLabtestById($id);
            $this->_responseData['data'] = $labtest;
        }else{
            $limit = 12;
            $params = array_diff(Arr::merge(Request::current()->param(),['page' => '']),array(''));
            $page = isset(Request::current()->param()['page']) && Request::current()->param()['page'] ? Request::current()->param()['page'] : 1;

            $filterParams = [
                'project_id' => $projectId,
                'status' => json_decode($_GET['status']),
                'building_id' => json_decode($_GET['object_id']),
                'floor_id' => json_decode($_GET['floor_id']),
                'place_id' => json_decode($_GET['place_id']),
                'element_id' => json_decode($_GET['element_id']),
                'craft_id' => json_decode($_GET['craft_id']),
                'search' => $_GET['search'] ? $_GET['search'] : null,
            ];

            try{
                $filterParams['from'] = $_GET['from'] ? DateTime::createFromFormat('d/m/Y H:i',$_GET['from'] . ' 00:00')->getTimestamp() : null;
                $filterParams['to'] = $_GET['to'] ? DateTime::createFromFormat('d/m/Y H:i',$_GET['to'] . ' 23:59')->getTimestamp() : null;
            }catch(Exception $e){
                throw new HTTP_Exception_404();
            }

            $count = count(Api_DBLabtests::getLabtestsListWithRelations($filterParams));
            $pagination = Pagination::factory(array(
                    'total_items'    => $count,
                    'items_per_page' => $limit,
                )
            )
                ->route_params($params);

            $items = Api_DBLabtests::getLabtestsListPaginate($pagination->items_per_page, $pagination->offset, $filterParams);
            $this->_responseData = ['pagination' => ['total' => $count, 'page' => $page, 'limit' => $limit], 'items' => $items];
        }

    }

    /**
     * update labtest data
     * @throws HTTP_Exception
     */
    public function action_index_put(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $id = (int)$this->request->param('id');
        $lt = Arr::get($this->put(), 'labtest');
        $slp = Arr::get($this->put(), 'labtestCraftParams');

        $ltData = Arr::extract($lt, [
            'floor_id',
            'place_id',
            'craft_id',
            'element_id',
            'plan_id',
            'cert_number',
            'standard',
            'strength_after',
            'delivery_cert',
            'building_id',
        ]);

        try {
            Database::instance()->begin();

            $project = Api_DBProjects::getProjectById($projectId);
            $labtest = Api_DBLabtests::getLabtestById($id);
            if(empty($project) || empty($labtest)){
                throw API_Exception::factory(500,'Incorrect identifier');
            }
            $ltData['project_id'] = $projectId;

            $ltData['updated_at'] = time();
            $ltData['updated_by'] = Auth::instance()->get_user()->id;
            $valid = Validation::factory($ltData);
            $valid
                //проверка на пустату полей
                ->rule('project_id', 'not_empty')
                ->rule('building_id', 'not_empty')
                ->rule('floor_id', 'not_empty')
                ->rule('element_id', 'not_empty')
                ->rule('standard', 'not_empty')
                ->rule('strength_after', 'not_empty');
            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'Incorrect data');
            }
            DB::update('labtests')->set($ltData)->where('id', '=', $id)->execute($this->_db);

            DB::delete('labtest_clp')->where('labtest_id', '=', $id)->execute($this->_db);

            if (!empty($slp)) {
                $slpData = [];
                foreach($slp as $item) {
                    $d = [
                        'labtest_id' => $id,
                        'cl_id' => $item['cl_id'],
                        'clp_id' => $item['clp_id'],
                        'value' => $item['value']
                    ];
                    $valid = Validation::factory($d);
                    $valid
                        ->rule('labtest_id', 'not_empty')
                        ->rule('cl_id', 'not_empty')
                        ->rule('clp_id', 'not_empty')
                        ->rule('value', 'not_empty');
                    if (!$valid->check()) {
                        throw API_ValidationException::factory(500, 'Incorrect data');
                    }
                    $slpData[] = $d;

                }

                if (!empty($slpData)) {

                    $query = DB::insert('labtest_clp')->columns(['labtest_id', 'cl_id', 'clp_id', 'value']);
                    foreach ($slpData as $d) {
                        $query->values(array_values($d));
                    }
                    $query->execute($this->_db);
                }
            }

            Database::instance()->commit();
            $this->_responseData['labtest_id'] = $id;
        }catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        }catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * exports filtered labtests
     */
    public function action_export_xls_get(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $project = Api_DBProjects::getProjectById($projectId);
        $id = (int)$this->request->param('id');
        if ($lang = Arr::get($_GET, 'lang')) {
            I18n::lang($lang);
            Language::setCurrentLang($lang);
        }
        $filterParams = [
            'project_id' => $projectId,
            'status' => json_decode($_GET['status']),
            'building_id' => json_decode($_GET['object_id']),
            'floor_id' => json_decode($_GET['floor_id']),
            'place_id' => json_decode($_GET['place_id']),
            'element_id' => json_decode($_GET['element_id']),
            'craft_id' => json_decode($_GET['craft_id']),
            'search' => $_GET['search'] ? $_GET['search'] : null,
        ];

        try{
            $filterParams['from'] = $_GET['from'] ? DateTime::createFromFormat('d/m/Y H:i',$_GET['from'] . ' 00:00')->getTimestamp() : null;
            $filterParams['to'] = $_GET['to'] ? DateTime::createFromFormat('d/m/Y H:i',$_GET['to'] . ' 23:59')->getTimestamp() : null;
        }catch(Exception $e){
            throw new HTTP_Exception_404();
        }

        $labtests = Api_DBLabtests::getLabtestsListWithRelations($filterParams);
        $labtestIds = [];
        foreach($labtests as $labtest) {
            $labtestIds[] = $labtest['id'];
        }
        $labtestParams = Api_DBLabtests::getLabtestsClp($labtestIds);
        $formattedLabtestParamValues = [];
        foreach($labtestParams as $p) {
            $key = "{$p['labtest_id']}_{$p['clp_id']}";
            $value = $p['value'];
            $formattedLabtestParamValues[$key] = $value;
        }
        $params  = Api_DBLabtests::getLabTestCraftParams();

        if (!empty($labtests)) {
            $ws = new Spreadsheet(array(
                'author'       => 'Q4B',
                'title'	       => 'Report',
                'subject'      => 'Subject',
                'description'  => 'Description',
            ));

            $ws->set_active_sheet(0);
            $as = $ws->get_active_sheet();
            $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];

            $as->setTitle('Lab Control');

            $as->getDefaultStyle()->getFont()->setSize(10);

            $as->getColumnDimension('T')->setWidth(30);
            $as->getColumnDimension('S')->setWidth(30);
            $as->getColumnDimension('R')->setWidth(30);
            $as->getColumnDimension('Q')->setWidth(30);
            $as->getColumnDimension('P')->setWidth(40);
            $as->getColumnDimension('O')->setWidth(40);
            $as->getColumnDimension('N')->setWidth(20);
            $as->getColumnDimension('M')->setWidth(37);
            $as->getColumnDimension('L')->setWidth(35);
            $as->getColumnDimension('K')->setWidth(35);
            $as->getColumnDimension('J')->setWidth(20);
            $as->getColumnDimension('I')->setWidth(30);
            $as->getColumnDimension('H')->setWidth(25);
            $as->getColumnDimension('G')->setWidth(30);
            $as->getColumnDimension('F')->setWidth(35);
            $as->getColumnDimension('E')->setWidth(20);
            $as->getColumnDimension('D')->setWidth(20);
            $as->getColumnDimension('C')->setWidth(30);
            $as->getColumnDimension('B')->setWidth(20);
            $as->getColumnDimension('A')->setWidth(35);
            $as->getRowDimension('1')->setRowHeight(80);
            $as->getRowDimension('2')->setRowHeight(25);
            $as->setAutoFilter('A2:T2');
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
            $sh = [
                2 => [
                    __('Project name'),
                    __('Lab control'),
                    __('Lab certificate number'),
                    __('Create Date'),
                    __('Update Date'),
                    __('Craft'),
                    __('Structure'),
                    __('Floor/Level'),
                    __('Element_item'),
                    __('Status'),
                    __('Delivery certificates'),
                    __('Essence of work/standard'),
                    __('Fresh concrete strength'),
                    __('Roll strength'),
                    __('Description'),
                    __('Notes'),
                    __('Strength after/result'),
                ],
            ];
            foreach ($params as $key=>$p) {
                array_push($sh[2], __($p['name']));
            }
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
            $ws->set_data($sh, false);
            foreach ($labtests as $item){
                $levelsRange = $item['smaller_floor'].'-'.$item['bigger_floor'];
                $fName = $item['floor_custom_name'];
                $fNumb = $item['floor_number'];
                $floor = $fName ? $fName.' ('.$fNumb.') '  : $fNumb;
                $row =  [
                    $project[0]['name'],
                    $item['id'],
                    $item['ticketNumber'],
                    date('d/m/Y',$item['create_date']),
                    date('d/m/Y',$item['updated_at']),
                    $item['craftName'],
                    $item['building_name'],
                    $floor,
                    $item['element_name'],
                    __($item['status']),
                    $item['delivery_cert'],
                    $item['standard'],
                    $item['freshStrength'],
                    $item['rollStrength'],
                    $item['description'],
                    $item['notes'],
                    $item['strength_after'],
                ];
                foreach ($params as $p) {
                    $key = "{$item['id']}_{$p['id']}";
                    $value = isset($formattedLabtestParamValues[$key]) ? $formattedLabtestParamValues[$key] : '';
                    $row[] = $value;
                }
                $sh [] = $row;
            }

            $ws->set_data($sh, false);
            $first_letter = PHPExcel_Cell::stringFromColumnIndex(0);
            $last_letter = PHPExcel_Cell::stringFromColumnIndex(count($sh[2])-1);
            $header_range = "{$first_letter}2:{$last_letter}2";
            $ws->get_active_sheet()->getStyle($header_range)->getFont()->setSize(14)->setBold(true);
            $ws->get_active_sheet()->getStyle('K1:K999')
                ->getAlignment()->setWrapText(true);
            $ws->get_active_sheet()->getStyle('L1:L999')
                ->getAlignment()->setWrapText(true);
            $ws->get_active_sheet()->getStyle('O1:O999')
                ->getAlignment()->setWrapText(true);
            $ws->get_active_sheet()->getStyle('P1:P999')
                ->getAlignment()->setWrapText(true);
            $ws->rtl(Language::getCurrent()->direction === 'rtl');
            $ws->send(['name'=>'lab-report', 'format'=>'Excel5']);
        }
    }

    /**
     * get labtest ticket
     */
    public function action_tickets_get(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $labtestId = $this->getUIntParamOrDie($this->request->param('id'));
        $tickets = Api_DBLabtests::getLabtestTicketsById($labtestId);
        $this->_responseData['items'] = $tickets;
    }

    /**
     * create labtest ticket
     */
        public function action_tickets_post(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $labtestId = $this->getUIntParamOrDie($this->request->param('id'));

        $data = Arr::extract($_POST,[
            'number',
            'fresh_strength',
            'roll_strength',
            'description',
            'notes',
            'status'
        ]);
        $data['status'] = $data['status'] ? $data['status'] : 'waiting';
        $imgData = isset($_POST['images']) ? $_POST['images'] : [];
        $data['labtest_id'] = $labtestId;
        try {
        $project = Api_DBProjects::getProjectById($projectId);
        $labtest = Api_DBLabtests::getLabtestById($labtestId);

        if(empty($project) || empty($labtest)){
            throw API_Exception::factory(500,'Incorrect identifier');
        }
        DB::update('labtests')->set(["status" => $data['status']])->where('id', '=', $labtestId)->execute($this->_db);
        $uploadedFiles = [];
        $data['created_at'] = time();
        $data['updated_at'] = time();
        $data['created_by'] = Auth::instance()->get_user()->id;
        $valid = Validation::factory($data);
        $valid->rule('labtest_id', 'not_empty');

        if (!$valid->check()) {
            throw API_ValidationException::factory(500, 'Incorrect data');
        }
        $result = DB::insert('labtests_tickets')
            ->columns(array_keys($data))
            ->values(array_values($data))
            ->execute($this->_db);
        $ticketId = $result[0];


        $ltTicketPath = DOCROOT.'media/data/projects/'.$projectId.'/labtest-tickets';
        $files = $this->_b64Arr($imgData, $ltTicketPath);
        if(!empty($files)){
        foreach ($files as $image){
            $uploadedFiles[] = [
                'name' => $image['name'],
                'original_name' => $image['tmp_name'],
                'ext' => $image['type'],
                'mime' => $image['type'],
                'path' => str_replace(DOCROOT,'',$ltTicketPath),
                'token' => md5($image['name']).base_convert(microtime(false), 10, 36),
                ];
            }
        }
        $fs = new FileServer();
        if(!empty($uploadedFiles)){
            for ($i=count($uploadedFiles)-1; $i>=0; $i--) {
                $image = $uploadedFiles[$i];
                $image['created_at'] = time();
                $image['created_by'] = Auth::instance()->get_user()->id;
                $res = DB::insert('files')
                    ->columns(array_keys($image))
                    ->values(array_values($image))
                    ->execute($this->_db);
                $imgId = $res[0];

                DB::insert('labtests_tickets_files')
                    ->columns(['file_id', 'ticket_id' ])
                    ->values([$imgId, $ticketId])
                    ->execute($this->_db);
                $fs->addLazySimpleImageTask('https://qforb.net/' . $image['path'] . '/' . $image['name'],$imgId);
            }
            $fs->sendLazyTasks();
        }

        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        }catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,$e->getMessage());
        }
    }

    /**
     * update labtest ticket
     */
    public function action_tickets_put(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $labtestId = $this->getUIntParamOrDie($this->request->param('id'));
        $ticketId = $this->getUIntParamOrDie($this->request->param('ticketId'));
        $data = Arr::extract($this->put(),[
            'number',
            'fresh_strength',
            'roll_strength',
            'description',
            'notes',
            'status'
        ]);
        $imgData = Arr::get($this->put(), 'images');
        $imagesOld = Arr::get($this->put(), 'imagesOld');
        $data['labtest_id'] = $labtestId;

        try {
            $project = Api_DBProjects::getProjectById($projectId);
            $labtest = Api_DBLabtests::getLabtestById($labtestId);
            $ticket = Api_DBLabtests::getLabtestTicket($labtestId, $ticketId);

            if(empty($project) || empty($labtest) || empty($ticket)){
                throw API_Exception::factory(500,'Incorrect identifier');
            }

            $uploadedFiles = [];
            $data['updated_at'] = time();
            $data['updated_by'] = Auth::instance()->get_user()->id;

            DB::update('labtests_tickets')->set($data)->where('id', '=', $ticketId)->execute($this->_db);
            DB::update('labtests')->set(["status" => $data['status']])->where('id', '=', $labtestId)->execute($this->_db);

            $ltTicketPath = DOCROOT.'media/data/projects/'.$projectId.'/labtest-tickets';
            $files = $this->_b64Arr($imgData, $ltTicketPath);
            if(!empty($files)){
                foreach ($files as $image){
                    $uploadedFiles[] = [
                        'name' => $image['name'],
                        'original_name' => $image['tmp_name'],
                        'ext' => $image['type'],
                        'mime' => $image['type'],
                        'path' => str_replace(DOCROOT,'',$ltTicketPath),
                        'token' => md5($image['name']).base_convert(microtime(false), 10, 36),
                    ];
                }
            }
            $fs = new FileServer();
            if(!empty($uploadedFiles)){
                for ($i=count($uploadedFiles)-1; $i>=0; $i--) {
                    $image = $uploadedFiles[$i];
                    $image['created_at'] = time();
                    $image['created_by'] = Auth::instance()->get_user()->id;
                    $res = DB::insert('files')
                        ->columns(array_keys($image))
                        ->values(array_values($image))
                        ->execute($this->_db);
                    $imgId = $res[0];

                    DB::insert('labtests_tickets_files')
                        ->columns(['file_id', 'ticket_id' ])
                        ->values([$imgId, $ticketId])
                        ->execute($this->_db);
                    $fs->addLazySimpleImageTask('https://qforb.net/' . $image['path'] . '/' . $image['name'],$imgId);
                }
                $fs->sendLazyTasks();

            }

            if (!empty($imagesOld)) {
                DB::delete('files')->where('id', 'IN', $imagesOld)->execute($this->_db);
                DB::delete('labtests_tickets_files')->where('file_id', 'IN', $imagesOld)->execute($this->_db);
            }

        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        }catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * tickets history for labtest
     */
    public function action_tickets_history_get(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $labtestId = $this->getUIntParamOrDie($this->request->param('id'));
        $tickets = Api_DBLabtests::getLabtestTicketsById($labtestId);
        $this->_responseData['items'] = $tickets;
    }

    /**
     * Returns craft_labtests with params
     * return data array [{name,craft_name,params:[{clId,name,defaultValue,valueType},{clId,name,defaultValue,valueType}...]}]
     * https://qforb.net/api/json/v1/<appToken>/projects/labtests/crafts_params
     */
    public function action_crafts_params_get(){
        $fields = Arr::get($_GET,'fields');
        if ($lang = Arr::get($_GET, 'lang')) {
            I18n::lang($lang);
            Language::setCurrentLang($lang);
        }
        if(!empty($fields)){
            $fields = explode(',',$fields);
        }

        $craftsWithParams = Api_DBLabtests::getLabTestCraftsWithParams();
        $craftItems = [];

        if (!empty($craftsWithParams)) {
            $hasId = count($fields) ? (bool) in_array('id', $fields) : true;
            $hasName = count($fields) ? (bool) in_array('name', $fields) : true;
            $hasCraftName = count($fields) ? (bool) in_array('craftName', $fields) : true;
            $hasClpId = count($fields) ? (bool) in_array('clpId', $fields) : true;
            $hasClpName = count($fields) ? (bool) in_array('clpName', $fields) : true;
            $hasDefaultVal = count($fields) ? (bool) in_array('defaultValue', $fields) : true;
            $hasValType = count($fields) ? (bool) in_array('valueType', $fields) : true;
            foreach ($craftsWithParams as $item){
                if(!isset($craftItems[$item['id']])){
                    $cArr = [];
                    if ($hasId) $cArr['id'] = $item['id'];
                    if ($hasName) $cArr['name'] = $item['name'];
                    if ($hasCraftName) $cArr['craftName'] = $item['craftName'];
                    $craftItems[$item['id']] = $cArr;
                }
                $pArr = [];
                if ($hasClpId) $pArr['clpId'] = $item['clpId'];
                if ($hasClpName) $pArr['clpName'] = __($item['clpName']);
                if ($hasDefaultVal) $pArr['defaultValue'] = $item['defaultValue'];
                if ($hasValType) $pArr['valueType'] = $item['valueType'];
                $craftItems[$item['id']]['params'][] = $pArr;
            }
        }

        $this->_responseData = array_values($craftItems);
    }

    /**
     * copy project elements with bounded crafts to another project(s)
     * if in pointet project not isset elemets or elements will be created
     * if in selected projects comapny crafts not isset crafts will be created
     * need to use transaction
     */
    public function action_copy_elements_post(){
        $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
        $project = Api_DBProjects::getProjectById($projectId);
        $data = $_POST;
        $elements = Api_DBProjects::getProjectElements($projectId, '');
        $cmpCrafts = Api_DBCompanies::getCompanyCrafts($project[0]['company_id'], ['name']);
        $elNames = $craftNames = $fromCmpCraftsNames = $elementsCmpCraftsArr = [];
        $elsToInsert = [];
        $craftsToInsert = [];
        if ($elements) {
            foreach ($elements as $el) {
                $elNames[] = trim($el['name']);
            }
        }
        if ($cmpCrafts) {
            foreach ($cmpCrafts as $craft) {
                $craftNames[] = trim($craft['name']);
            }
        }
        if (!empty($data['elements'])) {
            try {
                Database::instance()->begin();

                foreach ($data['elements'] as $el) {
                    if (!in_array(trim($el['name']), $elNames)) {
                        $elsToInsert[] = [
                          'company_id' => $project[0]['company_id'],
                          'project_id' => $projectId,
                          'name' => $el['name'],
                        ];
                    }
                    if ($data['companyId'] !== $project[0]['company_id']) {
                        if (!empty($el['crafts'])) {
                            foreach ($el['crafts'] as $c) {
//                                $fromCmpCraftsNames[] = trim($c['name']);
                                if (!in_array(trim($c['name']), $craftNames)) {
                                    $craftsToInsert[] = $c['name'];
                                }
                            }
                        }
                    }
                }
//                $fromCmpCraftsNames = array_unique($fromCmpCraftsNames);
                $craftsToInsert = array_unique($craftsToInsert);
                if (!empty($elsToInsert)) {
                    $query = DB::insert('elements', array('company_id', 'project_id', 'name'));
                    foreach ($elsToInsert as $val) {
                        $query->values(array_values($val));
                    }
                    $query->execute($this->_db);
                }
                if (!empty($craftsToInsert)) {
                    $query = DB::insert('cmp_crafts', array('company_id', 'name', 'status'));
                    foreach ($craftsToInsert as $val) {
                        $query->values([$project[0]['company_id'], $val, Enum_Status::Enabled]);
                    }
                    $query->execute($this->_db);
                }
                $elements = Api_DBProjects::getProjectElements($projectId, '');
                $cmpCrafts = Api_DBCompanies::getCompanyCrafts($project[0]['company_id'], ['id', 'name']);
                foreach($data['elements'] as $el) {
                    if (!empty($el['crafts'])) {
                        $prElem = array_filter($elements, function($innerArray) use($el) {
                            return (trim($innerArray['name']) == trim($el['name'])); //Поиск по первому значению
                        });
                        foreach($el['crafts'] as $c) {
                            $craft = array_filter($cmpCrafts, function($innerArray) use($c) {
                                return (trim($innerArray['name']) == trim($c['name'])); //Поиск по первому значению
                            });
                            $elementsCmpCraftsArr[] = ["element_id" => array_values($prElem)[0]['id'], "craft_id" => array_values($craft)[0]['id']];
                        }
                    }
                }
                if (!empty($elementsCmpCraftsArr)) {
                    $query = DB::insert('elements_cmp_crafts', array('element_id', 'craft_id'));
                    foreach ($elementsCmpCraftsArr as $val) {
                        $query->values(array_values($val));
                    }
                    $query->execute($this->_db);
                }
                Database::instance()->commit();
            } catch (API_ValidationException $e){
                Database::instance()->rollback();
                throw API_Exception::factory(500,'Incorrect data');
            } catch (Exception $e){
                Database::instance()->rollback();
                throw API_Exception::factory(500,'Operation Error');
            }
        }
    }
    /**
     *returns list of bound craft to labstests
     * https://qforb.net/api/json/v1/<appToken>/projects/labtests/bound_crafts
     */
    public function action_bound_crafts_get(){
        $fields = Arr::get($_GET,'fields');
        if(!empty($fields)){
            $fields = explode(',',$fields);
            $fields = Arr::decamelize($fields);
        }

        $items = Api_DBLabtests::getLabTestCrafts($fields);
        if(!empty($items)){
            foreach ($items as $item){
                if( ! count($fields)){
                    $obj = $item;
                }else{
                    $obj = Arr::extract($item, $fields);
                }
                array_walk($obj,function(&$param){
                    $param = html_entity_decode($param);
                });
                $response['items'][] = $obj;
            }
        }
        $this->_responseData = $response;
    }

    /**
     *returns bound crafts params
     * https://qforb.net/api/json/v1/<appToken>/projects/labtests/bound_craft_params/<id>
     */
    public function action_bound_craft_params_get(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        $fields = Arr::get($_GET,'fields');
        if(!empty($fields)){
            $fields = explode(',',$fields);
            $fields = Arr::decamelize($fields);
        }

        $items  = Api_DBLabtests::getLabTestCraftParams($id, $fields);
        if(!empty($items)){
            foreach ($items as $item){
                if( ! count($fields)){
                    $obj = $item;
                }else{
                    $obj = Arr::extract($item, $fields);
                }
                if (empty($fields) || in_array('name', $fields)) {
                    $obj['name_en'] = I18n::get($obj['name'], 'en');
                    $obj['name_he'] = I18n::get($obj['name'], 'he');
                    $obj['name_ru'] = I18n::get($obj['name'], 'ru');
                }

                array_walk($obj,function(&$param){
                    $param = html_entity_decode($param);
                });
                $response['items'][] = $obj;
            }
        }
        $this->_responseData = $response;
    }
}