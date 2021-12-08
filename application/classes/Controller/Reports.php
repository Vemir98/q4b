<?php defined('SYSPATH') OR die('No direct script access.');

use Helpers\PushHelper;

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 04.04.2017
 * Time: 11:46
 */
class Controller_Reports extends HDVP_Controller_Template
{
    const STATUS_EXISTING_AND_FOR_REPAIR = Enum_QualityControlStatus::Existing.' && '.Enum_QualityControlApproveStatus::ForRepair;

    public function before()
    {
        parent::before();
        if ($this->auto_render === TRUE)
        {
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Reports'))->set_url('/reports/list'));
        }
    }

    public function action_index(){
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('QC Report'))->set_url('/reports'));
        $this->_checkPermOrFail('read');
        $this->template->content = $this->searchForm();
    }

    public function action_list(){
        $this->template->content = View::make('reports/list');
    }

    public function action_advanced_options(){
        $this->_checkForAjaxOrDie();
        $this->_checkPermOrFail('read');
        if($this->request->method() !== Request::POST){
            throw new HTTP_Exception_404;
        }

        $data = Arr::extract($this->post(),['company','project']);
        $data['company'] = (int)$data['company'];
        $data['project'] = (int)$data['project'];
        try{
            $this->company = ORM::factory('Company',$data['company']);
            if(empty($data['company']) OR !$this->company->loaded()){
                throw new HDVP_Exception('Incorrect company or company not selected');
            }

            $this->project = ORM::factory('Project',$data['project']);
            if(empty($data['project'])){
                throw new HDVP_Exception('Incorrect project or project not selected');

            }

            $filterData = [
                'props' => [],
                'elementTypes' => Enum_ProjectPlaceType::toArray(),
                'qcStages' => Enum_ProjectStage::toArray(),
                'profs' => []
            ];

            foreach($this->project->objects->find_all() as $obj){
                $tmpType = $obj->type;
                $filterData['props'][] = Arr::extract(Arr::merge($obj->as_array(), ['type' => $tmpType->name]),['id','name','type','bigger_floor','smaller_floor']);
            }

            foreach($this->project->qc_professions->distinct('id')->find_all() as $prof){
                $filterData['profs'][] = Arr::extract($prof->as_array(),['id','name']);
            }
            $filterData['custom_variable'] = Arr::get($this->post(),'custom_variable');

            if(!count($filterData['profs'])){
                $this->_setErrors(__('qc_didnt_found'));
            }else{
                $this->setResponseData('advancedReportsHtml',View::make('reports/advanced-filter',$filterData)->render());
                $this->setResponseData('triggerEvent','showAdvancedReports');
            }
        }catch (HDVP_Exception $e){
            $this->_setErrors($e->getMessage());
        }catch (Exception $e){
            $this->_setErrors('Operation Error');
        }


    }

    public function action_generate(){
        $this->include_editor = true;
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('QC Report'))->set_url('/reports'));
        if(Request::current()->is_initial()){
            $this->_checkPermOrFail('read');
        }

        // company, project, crafts,

        $data = Arr::extract(Request::current()->query(),[
            'company',
            'project',
            'crafts',
            'statuses',
            'from',
            'to',
            'approval_status',
            'sort_by_crafts',
            'del_rep_id',
            'elements',
            'condition_level',
            'condition_list',
            'el_app_id'
        ]);

        $data['company'] = (int)$data['company'];
        $data['project'] = (int)$data['project'];

        $advancedData = Arr::extract(Request::current()->query(),[
            'object_id',
            'floors',
            'place_type',
            'place_number',
            'custom_number',
            'space',
            'project_stage',
            'profession_id',
            'advanced'
            ]);


        if(!empty($advancedData['project_stage'])){
            foreach ($advancedData['project_stage'] as $key => $ps){
                $advancedData['project_stage'][$key] = '"'.$ps.'"';
            }
        }

        $this->company = ORM::factory('Company',$data['company']);
        if(empty($data['company']) OR !$this->company->loaded()){
            throw new HTTP_Exception_404();
        }

        $this->project = ORM::factory('Project',$data['project']);

        if(empty($data['project'])){
            throw new HTTP_Exception_404();

        }

        if(empty($data['crafts'])){
            throw new HDVP_Exception('Crafts not selected');
        }

        $craftsParams = [
            'mngrApprovalStatuses' => [
                Enum_QualityControlApproveStatus::Waiting => null,
                Enum_QualityControlApproveStatus::ForRepair => null,
                Enum_QualityControlApproveStatus::Approved => null,
            ],
            'statuses' => [
                Enum_QualityControlStatus::Existing => null,
                self::STATUS_EXISTING_AND_FOR_REPAIR => null,
                Enum_QualityControlStatus::Normal => null,
                Enum_QualityControlStatus::Repaired => null,
                Enum_QualityControlStatus::Invalid => null,
            ],
            'percents' => [
                Enum_QualityControlStatus::Existing => null,
                self::STATUS_EXISTING_AND_FOR_REPAIR => null,
                Enum_QualityControlStatus::Normal => null,
                Enum_QualityControlStatus::Repaired => null,
                Enum_QualityControlStatus::Invalid => null,
            ]
        ];

        $qcs = null;
        $approvalStatusQuery = null;
        $qcs = ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id);
        if(in_array($data['approval_status'],Enum_QualityControlApproveStatus::toArray())){
            $qcs->and_where('qualitycontrol.approval_status', '=', $data['approval_status']);
            $approvalStatusQuery = ' AND qualitycontrol.approval_status = "'.$data['approval_status'].'" ';
            View::set_global('_APPROVAL_STATUS',true);
        }

        $filteredCraftsListQuery = [];
        $tasks = [];

        if($data['el_app_id']) {
//            $tasks = $this->project->getTasksByModuleName('Approve Element')->where('prtask.status','=',Enum_Status::Enabled)->find_all();
            $qcs->and_where('qualitycontrol.el_approval_id', '=', $data['el_app_id']);
        }
//        elseif ($data['del_rep_id']) {
//            $tasks = $this->project->getTasksByModuleName('Delivery Report')->where('prtask.status','=',Enum_Status::Enabled)->find_all();
//        } else {
        $tasks = $this->project->getTasksByModuleName('Quality Control')->where('prtask.status','=',Enum_Status::Enabled)->find_all();
//        }

        if(!empty($data['statuses'])){
            if(!is_array($data['statuses'])){
                $data['statuses'] = [$data['statuses']];
            }
            $qcs->and_where_open();
            $statusQuery = 'AND (';
            for ($i = 0; $i < count($data['statuses']);$i++){
                if(!$i){
                    $statusQuery .= 'qualitycontrol.status="'.$data['statuses'][$i].'" ';
                    $qcs->where('qualitycontrol.status', '=', $data['statuses'][$i]);
                }else{
                    $statusQuery .= 'OR qualitycontrol.status="'.$data['statuses'][$i].'" ';
                    $qcs->or_where('qualitycontrol.status', '=', $data['statuses'][$i]);
                }
            }
            $statusQuery .= ')';
            $qcs->and_where_close();
            $filteredCraftsListQuery['and'][] = $statusQuery;
            unset($statusQuery);
        }
        if(empty($data['crafts'])){
            throw new HDVP_Exception('Select Craft(s)');
        }
        if(!is_array($data['crafts'])){
            $data['crafts'] = [$data['crafts']];
        }
        array_walk($data['crafts'],function(&$item){
            $item = (int)$item;
        });
        $qcs->and_where('qualitycontrol.craft_id', 'IN', DB::expr('('.implode(',',$data['crafts']).')'));

        if(!empty($data['condition_level']) AND $data['condition_level'] != 'all'){
            $qcs->and_where('qualitycontrol.severity_level', '=', $data['condition_level']);
        }

        if(!empty($data['condition_list']) AND $data['condition_list'] != 'all'){
            $qcs->and_where('qualitycontrol.condition_list', '=', $data['condition_list']);
        }

        $floorsNeedJoin = true;
        try{
            $data['from'] = DateTime::createFromFormat('d/m/Y H:i',$data['from'].' 00:00')->getTimestamp();
            $data['to'] = DateTime::createFromFormat('d/m/Y H:i',$data['to'].' 23:59')->getTimestamp();
        }catch(Exception $e){
            throw new HTTP_Exception_404();
        }

        if(!empty($data['elements'])) {
            $qcs->join('el_approvals','LEFT');
            $qcs->on('qualitycontrol.el_approval_id','=','el_approvals.id');
            $filteredCraftsListQuery['join'][] = 'LEFT JOIN el_approvals ON qualitycontrol.el_approval_id = el_approvals.id';
            $qcs->and_where_open();
            $elementsQuery = 'AND (';
            $qcs->where('el_approvals.element_id','IN',DB::expr('('.implode(',',$data['elements']).')'));
            $elementsQuery .= 'el_approvals.element_id IN ('.implode(',',$data['elements']).') ';
            if(in_array(0,$data['elements'])) {
                $qcs->or_where('qualitycontrol.el_approval_id','IS',null);
                $elementsQuery .= 'OR qualitycontrol.el_approval_id IS NULL';
            }
            $elementsQuery .= ')';
            $qcs->and_where_close();
            $filteredCraftsListQuery['and'][] = $elementsQuery;
            unset($elementsQuery);
        }
//craft


        if(!empty($advancedData['advanced'])) {

            if (!empty($advancedData['object_id'])) {
                if(!is_array($advancedData['object_id'])){
                    $advancedData['object_id'] = [$advancedData['object_id']];
                }
                array_walk($advancedData['object_id'],function(&$item){
                    $item = (int)$item;
                });
                $qcs->and_where('qualitycontrol.object_id', 'IN', DB::expr('('.implode(',',$advancedData['object_id']).')'));
                $filteredCraftsListQuery['and'][] = 'AND qualitycontrol.object_id IN ('.implode(',',$advancedData['object_id']).')';
            }

            if($advancedData['place_type'] != 'all'){
                $qcs->and_where('qualitycontrol.place_type', '=', $advancedData['place_type']);
                $filteredCraftsListQuery['and'][] = 'AND qualitycontrol.place_type ="'.$advancedData['place_type'].'"';
            }

            if(empty($advancedData['place_number']) AND empty($advancedData['custom_number'])){
                if(!empty($advancedData['floors']) AND $advancedData['floors'] !== '0'){
                    if(!is_array($advancedData['floors'])){
                        $advancedData['floors'] = [$advancedData['floors']];
                    }
                    array_walk($advancedData['floors'],function(&$item){
                        $item = (int)$item;
                    });
                    $floorsNeedJoin = false;
                    $qcs->join('pr_floors','INNER');
                    $qcs->on('qualitycontrol.floor_id','=','pr_floors.id');
                    $qcs->and_where('pr_floors.number','IN',DB::expr('('.implode(',',$advancedData['floors']).')'));

                    $filteredCraftsListQuery['join'][] = 'INNER JOIN pr_floors pf ON qualitycontrol.floor_id = pf.id';
                    $filteredCraftsListQuery['and'][] = 'AND pf.number IN ('.implode(',',$advancedData['floors']).')';
                }
            }else{
                    if($advancedData['space'] != 'all') {
                        $advancedData['space'] = (int)$advancedData['space'];
                        $qcs->and_where('qualitycontrol.space_id','=',$advancedData['space']);
                        $filteredCraftsListQuery['and'][] = 'AND qualitycontrol.space_id ="'.$advancedData['space'].'"';
                    }
            }


            if(!empty($advancedData['project_stage'])){
                if(count($advancedData['project_stage']) != count(Enum_ProjectStage::toArray()) AND count($advancedData['project_stage'])){
                    $qcs->and_where('project_stage', 'IN', DB::expr('('.implode(',',$advancedData['project_stage']).')'));
                    $filteredCraftsListQuery['and'][] = 'AND qualitycontrol.project_stage IN ('.implode(',',$advancedData['project_stage']).')';
                }
            }


                if($advancedData['profession_id'] != 'all'){
                    $qcs->and_where('profession_id', '=', (int)$advancedData['profession_id']);
                    $filteredCraftsListQuery['and'][] = 'AND qualitycontrol.profession_id ="'.$advancedData['profession_id'].'"';
                }

            if(!empty($filteredCraftsListQuery['join'])){
                $filteredCraftsListQuery['join'] = implode(' ',$filteredCraftsListQuery['join']);
            }
            if(!empty($filteredCraftsListQuery['and'])){
                $filteredCraftsListQuery['and'] = implode(' ',$filteredCraftsListQuery['and']);
            }

            $query = 'SELECT 
                cc.id, cc.name, count(craft_id) `count`
                FROM quality_controls qualitycontrol
                JOIN cmp_crafts cc ON qualitycontrol.craft_id = cc.id
                '.($filteredCraftsListQuery['join'] ?: null).'
                WHERE qualitycontrol.project_id = '.$data['project'].'
                AND qualitycontrol.craft_id IN ('.implode(',',$data['crafts']).')
AND (qualitycontrol.due_date BETWEEN '.$data['from'].' AND '.$data['to'].') AND cc.status="'.Enum_Status::Enabled.'"
AND cc.company_id='.$data['company'].' '.($filteredCraftsListQuery['and'] ?: null).' GROUP BY qualitycontrol.craft_id
';

//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$query]); echo "</pre>"; exit;


            $filteredCraftsList = DB::query(Database::SELECT, $query)->execute()->as_array('id');
        }else{
            if(!empty($data['del_rep_id'])){
                $qcs->and_where('qualitycontrol.del_rep_id','=',(int)$data['del_rep_id']);
                $filteredCraftsListQuery['and'][] = 'AND qualitycontrol.del_rep_id ="'.(int)$data['del_rep_id'].'"';
            }

            if(!empty($data['condition_level']) AND $data['condition_level'] != 'all'){
                $filteredCraftsListQuery['and'][] = 'AND qualitycontrol.severity_level ="'.$data['condition_level'].'"';
            }

            if(!empty($data['condition_list']) AND $data['condition_list'] != 'all'){
                $filteredCraftsListQuery['and'][] = 'AND qualitycontrol.condition_list ="'.$data['condition_list'].'"';
            }

            if(!empty($filteredCraftsListQuery['and'])){
                $filteredCraftsListQuery['and'] = implode(' ',$filteredCraftsListQuery['and']);
            }

//            echo "line: ".__LINE__." ".__FILE__."<pre>"; echo($filteredCraftsListQuery['join']); echo "</pre>"; exit;

            $query = '
                SELECT cc.id, cc.name, count(craft_id) `count`
                FROM quality_controls qualitycontrol
                JOIN cmp_crafts cc ON qualitycontrol.craft_id = cc.id 
                '. implode(' ',$filteredCraftsListQuery['join']) .'
                WHERE 
                qualitycontrol.project_id = '.$data['project'].' AND qualitycontrol.craft_id IN ('.implode(',',$data['crafts']).') AND (qualitycontrol.due_date BETWEEN '.$data['from'].' AND '.$data['to'].') AND cc.status="'.Enum_Status::Enabled.'" AND cc.company_id='.$data['company'].' '.($filteredCraftsListQuery['and'] ?: null).$approvalStatusQuery.' GROUP BY qualitycontrol.craft_id';


//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$query]); echo "</pre>"; exit;

            $filteredCraftsList = DB::query(Database::SELECT, $query)->execute()->as_array('id');
        }
        if($floorsNeedJoin){
            $qcs->join('pr_floors','INNER');
            $qcs->on('qualitycontrol.floor_id','=','pr_floors.id');
        }

        $qcs->join('pr_places','INNER');
        $qcs->on('qualitycontrol.place_id','=','pr_places.id');
        //$filteredCraftsListQuery['join'][] = 'INNER JOIN pr_places pp ON qc.place_id = pp.id';

        if($data['sort_by_crafts']){
            $qcs->join('cmp_crafts','INNER');//---
            $qcs->on('qualitycontrol.craft_id','=','cmp_crafts.id');//---
        }

        $qcs->and_where('qualitycontrol.due_date','>=',$data['from']);
        $qcs->and_where('qualitycontrol.due_date','<=',$data['to']);
        $paginationSettings = [
            'items_per_page' => 7,
            'view'              => 'pagination/project-redesigned',
            'current_page'      => ['source' => 'query_string', 'key'    => 'page'],
        ];

        if($data['sort_by_crafts']) {
            $qcs->order_by('cmp_crafts.name', 'ASC');//--1
        }
//        $qcs->order_by('qualitycontrol.project_id','ASC');
//        $qcs->order_by('qualitycontrol.object_id','ASC');
//        $qcs->order_by('pr_floors.number','ASC');
//        $qcs->order_by('pr_places.number','ASC');
        $qcs->order_by('qualitycontrol.created_at','DESC');

       // $qcs->order_by('qualitycontrol.place_id','ASC');
        View::set_global('_COMPANY',$this->company);
        View::set_global('_PROJECT',$this->project);
        if($this->request->method() != Request::POST){
            if(Arr::get(Request::current()->query(),'export',0)){
                $this->template = null;
                $this->_export_report($qcs->find_all());
                return;
            }
        $result = (new ORMPaginate($qcs,null,$paginationSettings))->getData();
        $qcsTotal = $result['items'];


        $qcElementNames = [];
        foreach ($qcsTotal as $qcKey => $qc) {
            if($qc->el_approval_id) {
                $element = Api_DBElApprovals::getElApprovalElementByElAppId($qc->el_approval_id);
                $qcElementNames[$qcKey] = $element[0]['name'];
            } elseif($qc->element_id) {
                $element = ORM::factory('Element')->where('id', '=', $qc->element_id)->find();
                $qcElementNames[$qcKey] = $element->name;
            }
        }
        $report = [];
        $craftsList = DB::query(Database::SELECT,'SELECT cc.id, cc.name, count(craft_id) `count` FROM quality_controls qualitycontrol JOIN cmp_crafts cc ON qualitycontrol.craft_id = cc.id WHERE qualitycontrol.project_id = '.$data['project'].' AND qualitycontrol.craft_id IN ('.implode(',',$data['crafts']).') AND cc.status="'.Enum_Status::Enabled.'" AND cc.company_id='.$data['company'].$approvalStatusQuery.' GROUP BY qualitycontrol.craft_id')->execute()->as_array('id');

        foreach($this->company->crafts->where('status','=',Enum_Status::Enabled)->find_all() as $c){
            if( ! isset($craftsList[$c->id])){
                $craftsList[$c->id] = $c->as_array();
            }
        }

        $craftsIds = array_column($craftsList, 'id');


        $craftsCertifications = DB::query(Database::SELECT,'SELECT * FROM certifications c WHERE c.cmp_craft_id IN ('.implode(',', $craftsIds).') AND c.project_id = '.$this->project->id)->execute()->as_array();

        foreach ($craftsList as $craftKey => $craftListItem) {
            $craftCerts = [];
            $craftApprovedCerts = [];
            foreach ($craftsCertifications as $cert) {
                if((int)$cert['cmp_craft_id'] === (int)$craftListItem['id']) {
                    $craftCerts[] = $cert;
                    if($cert['status'] === 'approved') {
                        $craftApprovedCerts[] = $cert;
                    }
                }
            }
            $craftsList[$craftKey]['certs'] = $craftCerts;
            $craftsList[$craftKey]['approvedCerts'] = $craftApprovedCerts;
        }


//        $this->setResponseData('reportHtml',View::make('reports/generated',['qcs' => $qcs, 'crafts' => $data['crafts']])->render());
//        $this->setResponseData('triggerEvent','reportGenerated');

        if(count($qcsTotal)){
            if(count($data['crafts']) > 1){
                $craftsParams['mngrApprovalStatuses'] = [
                    Enum_QualityControlApproveStatus::Waiting => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('approval_status','=',Enum_QualityControlApproveStatus::Waiting)->count_all(),
                    Enum_QualityControlApproveStatus::ForRepair => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('approval_status','=',Enum_QualityControlApproveStatus::ForRepair)->count_all(),
                    Enum_QualityControlApproveStatus::Approved => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('approval_status','=',Enum_QualityControlApproveStatus::Approved)->count_all(),
                ];
                $craftsParams['statuses'] = [
                    Enum_QualityControlStatus::Existing => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('status','=',Enum_QualityControlStatus::Existing)->count_all(),
                    self::STATUS_EXISTING_AND_FOR_REPAIR => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('status','=',Enum_QualityControlStatus::Existing)->and_where('approval_status','=',Enum_QualityControlApproveStatus::ForRepair)->count_all(),
                    Enum_QualityControlStatus::Normal => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('status','=',Enum_QualityControlStatus::Normal)->count_all(),
                    Enum_QualityControlStatus::Repaired => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('status','=',Enum_QualityControlStatus::Repaired)->count_all(),
                    Enum_QualityControlStatus::Invalid => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('status','=',Enum_QualityControlStatus::Invalid)->count_all(),
                ];

                $filteredCraftsParams['mngrApprovalStatuses'] = [
                    Enum_QualityControlApproveStatus::Waiting => (clone $qcs)->and_where('qualitycontrol.approval_status','=',Enum_QualityControlApproveStatus::Waiting)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->and_where('qualitycontrol.craft_id', 'IN', DB::expr('('.implode(',',$data['crafts']).')'))->count_all(),
                    Enum_QualityControlApproveStatus::ForRepair => (clone $qcs)->and_where('qualitycontrol.approval_status','=',Enum_QualityControlApproveStatus::ForRepair)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->and_where('qualitycontrol.craft_id', 'IN', DB::expr('('.implode(',',$data['crafts']).')'))->count_all(),
                    Enum_QualityControlApproveStatus::Approved => (clone $qcs)->and_where('qualitycontrol.approval_status','=',Enum_QualityControlApproveStatus::Approved)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->and_where('qualitycontrol.craft_id', 'IN', DB::expr('('.implode(',',$data['crafts']).')'))->count_all(),
                ];
                $filteredCraftsParams['statuses'] = [
                    Enum_QualityControlStatus::Existing => (clone $qcs)->and_where('qualitycontrol.status','=',Enum_QualityControlStatus::Existing)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->and_where('qualitycontrol.craft_id', 'IN', DB::expr('('.implode(',',$data['crafts']).')'))->count_all(),
                    self::STATUS_EXISTING_AND_FOR_REPAIR => (clone $qcs)->and_where('qualitycontrol.status','=',Enum_QualityControlStatus::Existing)->and_where('qualitycontrol.approval_status','=',Enum_QualityControlApproveStatus::ForRepair)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->and_where('qualitycontrol.craft_id', 'IN', DB::expr('('.implode(',',$data['crafts']).')'))->count_all(),
                    Enum_QualityControlStatus::Normal => (clone $qcs)->and_where('qualitycontrol.status','=',Enum_QualityControlStatus::Normal)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->and_where('qualitycontrol.craft_id', 'IN', DB::expr('('.implode(',',$data['crafts']).')'))->count_all(),
                    Enum_QualityControlStatus::Repaired => (clone $qcs)->and_where('qualitycontrol.status','=',Enum_QualityControlStatus::Repaired)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->and_where('qualitycontrol.craft_id', 'IN', DB::expr('('.implode(',',$data['crafts']).')'))->count_all(),
                    Enum_QualityControlStatus::Invalid => (clone $qcs)->and_where('qualitycontrol.status','=',Enum_QualityControlStatus::Invalid)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->and_where('qualitycontrol.craft_id', 'IN', DB::expr('('.implode(',',$data['crafts']).')'))->count_all(),
                ];

            }else{
                $craftsParams['mngrApprovalStatuses'] = [
                    Enum_QualityControlApproveStatus::Waiting => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('craft_id','=',(int)$data['crafts'][0])->and_where('approval_status','=',Enum_QualityControlApproveStatus::Waiting)->count_all(),
                    Enum_QualityControlApproveStatus::ForRepair => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('craft_id','=',(int)$data['crafts'][0])->and_where('approval_status','=',Enum_QualityControlApproveStatus::ForRepair)->count_all(),
                    Enum_QualityControlApproveStatus::Approved => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('craft_id','=',(int)$data['crafts'][0])->and_where('approval_status','=',Enum_QualityControlApproveStatus::Approved)->count_all(),
                ];
                $craftsParams['statuses'] = [
                    Enum_QualityControlStatus::Existing => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('craft_id','=',(int)$data['crafts'][0])->and_where('status','=',Enum_QualityControlStatus::Existing)->count_all(),
                    self::STATUS_EXISTING_AND_FOR_REPAIR => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('craft_id','=',(int)$data['crafts'][0])->and_where('status','=',Enum_QualityControlStatus::Existing)->and_where('approval_status','=',Enum_QualityControlApproveStatus::ForRepair)->count_all(),
                    Enum_QualityControlStatus::Normal => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('craft_id','=',(int)$data['crafts'][0])->and_where('status','=',Enum_QualityControlStatus::Normal)->count_all(),
                    Enum_QualityControlStatus::Repaired => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('craft_id','=',(int)$data['crafts'][0])->and_where('status','=',Enum_QualityControlStatus::Repaired)->count_all(),
                    Enum_QualityControlStatus::Invalid => ORM::factory('QualityControl')->where('qualitycontrol.project_id','=',$this->project->id)->and_where('craft_id','=',(int)$data['crafts'][0])->and_where('status','=',Enum_QualityControlStatus::Invalid)->count_all(),
                ];

                $filteredCraftsParams['mngrApprovalStatuses'] = [
                    Enum_QualityControlApproveStatus::Waiting => (clone $qcs)->and_where('qualitycontrol.craft_id','=',(int)$data['crafts'][0])->and_where('qualitycontrol.approval_status','=',Enum_QualityControlApproveStatus::Waiting)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->count_all(),
                    Enum_QualityControlApproveStatus::ForRepair => (clone $qcs)->and_where('qualitycontrol.craft_id','=',(int)$data['crafts'][0])->and_where('qualitycontrol.approval_status','=',Enum_QualityControlApproveStatus::ForRepair)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->count_all(),
                    Enum_QualityControlApproveStatus::Approved => (clone $qcs)->and_where('qualitycontrol.craft_id','=',(int)$data['crafts'][0])->and_where('qualitycontrol.approval_status','=',Enum_QualityControlApproveStatus::Approved)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->count_all(),
                ];
                $filteredCraftsParams['statuses'] = [
                    Enum_QualityControlStatus::Existing => (clone $qcs)->and_where('qualitycontrol.craft_id','=',(int)$data['crafts'][0])->and_where('qualitycontrol.status','=',Enum_QualityControlStatus::Existing)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->count_all(),
                    self::STATUS_EXISTING_AND_FOR_REPAIR => (clone $qcs)->and_where('qualitycontrol.craft_id','=',(int)$data['crafts'][0])->and_where('qualitycontrol.status','=',Enum_QualityControlStatus::Existing)->and_where('qualitycontrol.approval_status','=',Enum_QualityControlApproveStatus::ForRepair)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->count_all(),
                    Enum_QualityControlStatus::Normal =>  (clone $qcs)->and_where('qualitycontrol.craft_id','=',(int)$data['crafts'][0])->and_where('qualitycontrol.status','=',Enum_QualityControlStatus::Normal)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->count_all(),
                    Enum_QualityControlStatus::Repaired => (clone $qcs)->and_where('qualitycontrol.craft_id','=',(int)$data['crafts'][0])->and_where('qualitycontrol.status','=',Enum_QualityControlStatus::Repaired)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->count_all(),
                    Enum_QualityControlStatus::Invalid => (clone $qcs)->and_where('qualitycontrol.craft_id','=',(int)$data['crafts'][0])->and_where('qualitycontrol.status','=',Enum_QualityControlStatus::Invalid)->and_where('qualitycontrol.due_date','BETWEEN',DB::expr($data['from'].' AND '.$data['to']))->count_all(),
                ];
            }

//            foreach ($filteredCraftsParams as $k => $vArr){
//                foreach ($vArr as $k1 => $q){
//                    if(!empty($advancedData['advanced'])) {
//                        if (!empty($advancedData['object_id'])) {
//                            $filteredCraftsParams[$k][$k1]->and_where('qualitycontrol.object_id', 'IN', DB::expr('('.implode(',',$advancedData['object_id']).')'));
//                        }
//                        if($advancedData['place_type'] != 'all') {
//                            $filteredCraftsParams[$k][$k1]->and_where('qualitycontrol.place_type', '=', $advancedData['place_type']);
//                        }
//                        if(empty($advancedData['place_number']) AND empty($advancedData['custom_number'])){
//                            if(!empty($advancedData['floors'])){
//                                $filteredCraftsParams[$k][$k1]->join('pr_floors','INNER');
//                                $filteredCraftsParams[$k][$k1]->on('qualitycontrol.floor_id','=','pr_floors.id');
//                                $filteredCraftsParams[$k][$k1]->and_where('pr_floors.number','IN',DB::expr('('.implode(',',$advancedData['floors']).')'));
//                            }
//                        }else{
//                            $filteredCraftsParams[$k][$k1]->and_where('qualitycontrol.space_id','=',$advancedData['space']);
//                        }
//                        if(!empty($advancedData['project_stage'])){
//                            if(count($advancedData['project_stage']) != count(Enum_ProjectStage::toArray()) AND count($advancedData['project_stage'])){
//                                $filteredCraftsParams[$k][$k1]->and_where('project_stage', 'IN', DB::expr('('.implode(',',$advancedData['project_stage']).')'));
//                            }
//                        }
//                        if($advancedData['profession_id'] != 'all'){
//                            $filteredCraftsParams[$k][$k1]->and_where('profession_id', '=', (int)$advancedData['profession_id']);
//                        }
//                    }
//                    if(!empty($data['condition_level']) AND $data['condition_level'] != 'all'){
//                        $filteredCraftsParams[$k][$k1]->and_where('qualitycontrol.severity_level', '=', $data['condition_level']);
//                    }
//
//                    if(!empty($data['condition_list']) AND $data['condition_list'] != 'all'){
//                        $filteredCraftsParams[$k][$k1]->and_where('qualitycontrol.condition_list', '=', $data['condition_list']);
//                    }
//
//                    if($floorsNeedJoin){
//                        $filteredCraftsParams[$k][$k1]->join('pr_floors','INNER');
//                        $filteredCraftsParams[$k][$k1]->on('qualitycontrol.floor_id','=','pr_floors.id');
//                    }
//                    $filteredCraftsParams[$k][$k1]->join('pr_places','INNER');
//                    $filteredCraftsParams[$k][$k1]->on('qualitycontrol.place_id','=','pr_places.id');
//                    $filteredCraftsParams[$k][$k1] = $filteredCraftsParams[$k][$k1]->count_all();
//                }
//            }

            $totalStatuses = array_sum($craftsParams['statuses']);
            $craftsParams['percents'] = [
                Enum_QualityControlStatus::Existing => round($craftsParams['statuses'][Enum_QualityControlStatus::Existing] * 100 / $totalStatuses),
                self::STATUS_EXISTING_AND_FOR_REPAIR => round($craftsParams['statuses'][self::STATUS_EXISTING_AND_FOR_REPAIR] * 100 / $totalStatuses),
                Enum_QualityControlStatus::Normal => round($craftsParams['statuses'][Enum_QualityControlStatus::Normal] * 100 / $totalStatuses),
                Enum_QualityControlStatus::Repaired => round($craftsParams['statuses'][Enum_QualityControlStatus::Repaired] * 100 / $totalStatuses),
                Enum_QualityControlStatus::Invalid => round($craftsParams['statuses'][Enum_QualityControlStatus::Invalid] * 100 / $totalStatuses),
            ];

            foreach ($filteredCraftsParams['statuses'] as $key => &$val){
                if($key !== self::STATUS_EXISTING_AND_FOR_REPAIR && !in_array($key,$data['statuses']) || ($key === self::STATUS_EXISTING_AND_FOR_REPAIR && !in_array(Enum_QualityControlStatus::Existing, $data['statuses']))){
                    $val = 0;
                }
            }
            $filteredTotalStatuses = array_sum($filteredCraftsParams['statuses']);
            $filteredCraftsParams['percents'] = [
                Enum_QualityControlStatus::Existing => round($filteredCraftsParams['statuses'][Enum_QualityControlStatus::Existing] * 100 / $filteredTotalStatuses),
                self::STATUS_EXISTING_AND_FOR_REPAIR => round($filteredCraftsParams['statuses'][self::STATUS_EXISTING_AND_FOR_REPAIR] * 100 / $filteredTotalStatuses),
                Enum_QualityControlStatus::Normal => round($filteredCraftsParams['statuses'][Enum_QualityControlStatus::Normal] * 100 / $filteredTotalStatuses),
                Enum_QualityControlStatus::Repaired => round($filteredCraftsParams['statuses'][Enum_QualityControlStatus::Repaired] * 100 / $filteredTotalStatuses),
                Enum_QualityControlStatus::Invalid => round($filteredCraftsParams['statuses'][Enum_QualityControlStatus::Invalid] * 100 / $filteredTotalStatuses),
            ];
            $tparams = Request::current()->query();
            if(isset($tparams['page'])){
                unset($tparams['page']);
            }
            $tparams = JSON::encode($tparams);
            if(Request::current()->is_initial())
            if(empty(Session::instance()->get('token')) OR (md5(Session::instance()->get('token')->query) != md5($tparams))){

                $token = ORM::factory('ReportQueryToken')->values(['token' => uniqid(),'query' => $tparams,'expires' => time() + 86400*14])->save();
                Event::instance()->fire('onReportQueryTokenAdded',['sender' => $this,'item' => $token]);
                Session::instance()->set('token',$token);
            }

        }

//        $qcs = (object)$qcs;

            if(Request::current()->is_initial()){
                if( ! empty(Session::instance()->get('token')))
                    $sendReportsEmailUrl = URL::site('reports/send_reports/'.$this->project->id.'/'.Session::instance()->get('token')->token);
                else
                    $sendReportsEmailUrl = null;

                $this->template->content = View::make('reports/qc-report/generated',
                    [
                        'qcs' => $qcsTotal,
                        'tasks' => $tasks,
                        'pagination' => $result['pagination'],
                        'crafts' => $data['crafts'],
                        'craftsParams' => $craftsParams,
                        'filteredCraftsParams' => $filteredCraftsParams,
                        'searchForm' => $this->searchForm(true),
                        'sendReportsEmailUrl'=> $sendReportsEmailUrl,
                        'craftsList' => $craftsList,
                        'filteredCraftsList' => $filteredCraftsList,
                        'del_rep_id' => (int)$data['del_rep_id'],
                        'qcElementNames' => $qcElementNames
                    ]);
            }
            else{
                $this->template = View::make('reports/qc-report/guest-content',
                    [
                        'qcs' => $qcsTotal,
                        'tasks' => $tasks,
                        'pagination' => $result['pagination'],
                        'crafts' => $data['crafts'],
                        'craftsParams' => $craftsParams,
                        'filteredCraftsParams' => $filteredCraftsParams,
                        'craftsList' => $craftsList,
                        'filteredCraftsList' => $filteredCraftsList,
                        'data' => json_decode($tparams,true),
                        'qcElementNames' => $qcElementNames
                    ]);
            }
        }else{
            $qcs = $qcs->find_all();
            $this->setResponseData('html',View::make('reports/print',['qcs' => $qcs, 'tasks' => $tasks]));
        }

    }

    public function action_guest_access(){
        $token = $this->request->param('token');
        $reportQueryToken = ORM::factory('ReportQueryToken',['token' => $token]);
        if( ! $reportQueryToken->loaded()){
            throw new HTTP_Exception_404();
        }
        $data = JSON::decode($reportQueryToken->query,true);
        $page = Arr::get(Request::current()->query(),'page');
        if($page){
            $data['page'] = $page;
        }

        $content = Request::factory(URL::site('reports/generate'))->query($data)->execute()->body();

        $replacement = 'reports/guest_access/'.$token;
        $pattern = '~\<a\shref\=\".*(reports\/generate[^"]+)~';
        preg_match_all($pattern,$content,$matches);
        if(!empty($matches)){
            foreach ($matches[1] as $m){
                preg_match('~page=([0-9]+)~',$m,$matches1);
                if(!empty($matches1)){
                    $content = str_replace($m,$replacement.'?page='.$matches1[1][0],$content);
                }else{
                    $content = str_replace($m,$replacement,$content);
                }
            }
        }

        $content = str_ireplace('&amp;page=','?page=',$content);

        $this->template = View::make('reports/guest-template');

        $this->template->content = $content;
    }

    public function action_get_spaces(){//num_type = pn|pcn
        $this->_checkPermOrFail('read');
        $this->auto_render = false;
        $properties = $this->request->param('properties');
        $number = $this->request->param('number');
        $num_type = $this->request->param('num_type');
        $type = $this->request->param('type');
        if(!empty($properties)){
            $properties = explode('-',$properties);
            if(count($properties))
                array_walk($properties,function(&$item){
                    $item = (int)$item;
                });

        }
        $output = '';
        if($num_type == 'pn'){
            $places = ORM::factory('PrPlace')->where('number','=',$number)->and_where('object_id','IN',DB::expr('('.implode(',',$properties).')'))->and_where('type','=',$type)->find_all();
        }else if($num_type == 'pcn'){
            $places = ORM::factory('PrPlace')->where('custom_number','=',$number)->and_where('object_id','IN',DB::expr('('.implode(',',$properties).')'))->and_where('type','=',$type)->find_all();
        }

        if(count($places)){
            foreach ($places as $p){
                $output .= '<option value="all">'.__('All').'</option>';
                foreach ($p->spaces->find_all() as $s){
                    $output .= '<option value="'.$s->id.'">'.$s->type->name.' -'.$s->desc.'</option>';
                }

            }
        }
        $this->setResponseData('options',$output);
    }

    public function action_quality_control(){
        $this->_checkPermOrFail('update');
        $this->_checkForAjaxOrDie();
        $qcId = (int)$this->request->param('id');
        $qc = ORM::factory('QualityControl',$qcId);
        $auth = Auth::instance()->get_user();
        if( ! $qc->loaded() OR !$this->_user->canUseProject($qc->project)){
            throw new HTTP_Exception_404;
        }

        if($this->request->method() == HTTP_Request::POST){
            $data = Arr::extract($this->post(),['approval_status','status','due_date','description', 'dialog', 'severity_level','condition_list','plan_id','project_stage','craft_id','tasks','profession_id','craft_id','message']);
            $dt = \Carbon\Carbon::now()->format('d/m/Y H:i');
            $qcDesc = $qc->getDialog($qc->description, '@##', '@##');
            $data['description'] .= strlen($qcDesc) > 0 ?  "@##" . $qcDesc : '';
            $data['description'] .= isset($data['dialog']) && strlen($data['dialog']) > 0 ? "@##". $dt . " - ". $auth->name. "\n" .$data['dialog'] ."\n\n" : "";
            $data['plan_id'] *= 1;
            try{
                Database::instance()->begin();
                if(empty($data['tasks'])){
                    throw new HDVP_Exception('Choose Tasks');
                }
                if(empty($data['approval_status'])){
                    throw new HDVP_Exception('Approval status can not be empty');
                }
//                if(empty($data['plan_id'])){
//                    throw new HDVP_Exception('Plan can not be empty');
//                }
                if(!in_array($data['approval_status'],Enum_QualityControlApproveStatus::toArray())){
                    throw new HDVP_Exception('Incorrect approval status');
                }
//                if(($data['status'] != Enum_QualityControlStatus::Invalid) and ($data['status'] != Enum_QualityControlStatus::Repaired)){
//                    $data['severity_level'] = $data['condition_list'] = null;
//                }

                $data['due_date'] = DateTime::createFromFormat('d/m/Y',$data['due_date'])->getTimestamp();
                $project = $qc->project;
                $usersList = $qc->project->users->find_all();
                $project->makeProjectPaths();
                if(!empty($this->files()) AND !empty($this->files()['images'])){
                    foreach ($this->files()['images'] as $key => $image){
                        $uploadedFiles[] = [
                            'name' => str_replace($project->qualityControlPath().DS,'',Upload::save($image,null,$project->qualityControlPath())),
                            'original_name' => $image['name'],
                            'ext' => Model_File::getFileExt($image['name']),
                            'mime' => $image['type'],
                            'path' => str_replace(DOCROOT,'',$project->qualityControlPath()),
                            'token' => md5($image['name']).base_convert(microtime(false), 10, 36),
                        ];
                    }
                }
                if($qc->userHasExtraPrivileges($this->_user)){
                    if($qc->craft_id != (int)$data['craft_id']){
                        if(empty($data['craft_id'])){
                            throw new HDVP_Exception('Speciality can not be empty');
                        }
                        $qc->craft_id = (int)$data['craft_id'];
                    }
                }
                $qc->approval_status = $data['approval_status'];
                $qc->due_date = $data['due_date'];
                $qc->status = $data['status'];
                $qc->severity_level = $data['severity_level'];
                $qc->condition_list = $data['condition_list'];
                $qc->description = $data['description'];
                $qc->plan_id = $data['plan_id'];
                $qc->profession_id = $data['profession_id'];
                $qc->project_stage = $data['project_stage'];
                $qc->approved_by = Auth::instance()->get_user()->id;
                $qc->approved_at = time();
                $qc->save();
                $fs = new FileServer();
                if(!empty($uploadedFiles)){
                    foreach ($uploadedFiles as $image){
                        $image = ORM::factory('Image')->values($image)->save();
                        $qc->add('images', $image->pk());

//                        $img = new JBZoo\Image\Image($qc->project->qualityControlPath().DS.$image->name);
//                        $img->saveAs($qc->project->qualityControlPath().DS.$image->name,50);
                        $fs->addLazySimpleImageTask('https://qforb.net/' . $image->path . '/' . $image->name,$image->id);
                    }
                }
                $qc->remove('tasks');
                $qc->add('tasks',$data['tasks']);

                if(!empty(trim($data['message'])))
                ORM::factory('QcComment')->values(['message' => $data['message'], 'qcontrol_id' => $qc->pk()])->save();

                PushNotification::notifyQcUsers($qc->id, $qc->project->id, Enum_NotifyAction::Updated);

                if($qc->el_approval_id) {
                    $users = Api_DBElApprovals::getElApprovalUsersListForNotify($qc->el_approval_id);
                    $elApproval = Api_DBElApprovals::getElApprovalById($qc->el_approval_id)[0];


                    PushNotification::notifyElAppUsers($qc->el_approval_id, $users, $elApproval['projectId'], Enum_NotifyAction::Updated);
                }

                $this->setResponseData('triggerEvent','qualityControlUpdated');
                Database::instance()->commit();
                $fs->sendLazyTasks();
            }catch (ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch (HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }catch (Exception $e){
                //throw $e;
                Database::instance()->rollback();
                $this->_setErrors('Operation Error');
            }
        }else{
            $scopes = [];
            $plans = [];


            foreach ($qc->place->plans->order_by('id','DESC')->find_all() as $item){
                if(in_array($item->scope,$scopes)) continue;
                $scopes[] = $item->scope;
                $plans[$item->id] = $item;
            }

            foreach($qc->place->floor->plans->where('object_id','=',$qc->place->object_id)->order_by('id','DESC')->find_all() as $item){
                if(in_array($item->scope,$scopes)) continue;
                $scopes[] = $item->scope;
                $plans[$item->id] = $item;
            }
            $isSubcontractor = false;
            $roleName = Auth::instance()->get_user()->getRelevantRole('name');
            $subcontractorsArr = Kohana::$config->load('subcontractors')->as_array();
            if (array_key_exists($roleName, $subcontractorsArr)) {
                $isSubcontractor = true;
            }

            $this->setResponseData('modal',View::make('reports/quality-control',
                [
                    'item' => $qc,
                    'itemStatuses' => !$isSubcontractor ? Enum_QualityControlStatus::toArray() : Enum_QualityControlStatusSubcontractor::toArray(),
                    'itemPlace' => $qc->place,
                    'itemPlaceSpaces' => $qc->place->spaces->find_all(),
                    'itemConditionLevels' => Enum_QualityControlConditionLevel::toArray(),
                    'itemConditionList' => Enum_QualityControlConditionList::toArray(),
                    'approveStatusList' => !$isSubcontractor ? Enum_QualityControlApproveStatus::toArray() : Enum_QualityControlApproveStatusSubcontractor::toArray(),
                    'itemTasks' => $qc->tasks->find_all(),
                    'createUsr' => $qc->createUser,
                    'updateUsr' => $qc->updateUser,
                    'approveUsr' => $qc->approveUser,
                    'project' => $qc->project,
                    'projectStages' => Enum_ProjectStage::toArray(),
                    'tasks' => $qc->project->getTasksByModuleName('Quality Control')->where('prtask.status','=',Enum_Status::Enabled)->find_all(),
                    'professions' => $qc->project->company->professions->where('status','=',Enum_Status::Enabled)->find_all(),
                    'crafts' => $qc->project->company->crafts->where('status','=',Enum_Status::Enabled)->order_by('name')->find_all(),
                    'plan' => $qc->plan,
                    'planFiles' => $qc->plan->files->find_all(),
                    'itemImages' => $qc->images->where('status','=',Enum_FileStatus::Active)->order_by('created_at','DESC')->find_all(),
                    'formAction' => URL::site('/reports/quality_control/'.$qc->id),
                    'plans' => $plans,
                    'usedTasks' => $qc->project->usedTasks($qc->place->id),
                ]));

        }
    }

    public function action_quality_control_print()
    {
        $this->_checkPermOrFail('read');
        $this->_checkForAjaxOrDie();
        $qcId = (int)$this->request->param('id');
        $qc = ORM::factory('QualityControl',$qcId);
        if( ! $qc->loaded()){
            throw new HTTP_Exception_404;
        }
        $isSubcontractor = false;
        $roleName = Auth::instance()->get_user()->getRelevantRole('name');
        $subcontractorsArr = Kohana::$config->load('subcontractors')->as_array();
        if (array_key_exists($roleName, $subcontractorsArr)) {
            $isSubcontractor = true;
        }

        $this->setResponseData('html',View::make('reports/quality-control-print',
            [
                'item' => $qc,
                'itemStatuses' => !$isSubcontractor ? Enum_QualityControlStatus::toArray() : Enum_QualityControlStatusSubcontractor::toArray(),
                'itemPlace' => $qc->place,
                'itemPlaceSpaces' => $qc->place->spaces->find_all(),
                'itemConditionLevels' => Enum_QualityControlConditionLevel::toArray(),
                'itemConditionList' => Enum_QualityControlConditionList::toArray(),
                'approveStatusList' => !$isSubcontractor ? Enum_QualityControlApproveStatus::toArray() : Enum_QualityControlApproveStatusSubcontractor::toArray(),
                'itemTasks' => $qc->tasks->find_all(),
                'createUsr' => $qc->createUser,
                'updateUsr' => $qc->updateUser,
                'approveUsr' => $qc->approveUser,
                'project' => $qc->project,
                'projectStages' => Enum_ProjectStage::toArray(),
                'tasks' => $qc->project->tasks->where('status','=',Enum_Status::Enabled)->find_all(),
                'professions' => $qc->project->company->professions->where('status','=',Enum_Status::Enabled)->find_all(),
                'crafts' => $qc->project->company->crafts->where('status','=',Enum_Status::Enabled)->order_by('name')->find_all(),
                'plan' => $qc->plan,
                'planFiles' => $qc->plan->files->find_all(),
                'itemImages' => $qc->images->where('status','=',Enum_FileStatus::Active)->find_all()
            ]));
    }

    public function action_quality_control_mailing()
    {
        $this->_checkForAjaxOrDie();
        $this->_checkPermOrFail('read');
        $qcId = (int)$this->request->param('id');
        $qc = ORM::factory('QualityControl',$qcId);
        if( ! $qc->loaded()){
            throw new HTTP_Exception_404;
        }

        if($this->request->method() == Request::POST){
            if($qc->id != AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$qc->id,192)){
                $this->_setErrors('Invalid request');
            }
            $emailsList = [];
            foreach ($this->post() as $key => $value){
                if(strpos($key,'emails') !== false){
                    $emailsList[] = $value;
                }

            }
            if(!empty($emailsList)){
                foreach ($emailsList as $key => $email){
                    if(!Valid::email($email)){
                        unset($emailsList[$key]);
                    }
                }

                if(count($emailsList)){
                    Queue::enqueue('mailing','Job_Report_SendFccEmail',[
                        'emails' => $emailsList,
                        'item' => $qc->id,
                        'message' => trim(strip_tags(Arr::get($this->post(),'message'))),
                        'view' => 'emails/report/quality-control',
                        'user' => ['name' => $this->_user->name, 'email' => $this->_user->email],
                        'lang' => Language::getCurrent()->iso2,
                    ],\Carbon\Carbon::now()->addSeconds(30)->timestamp);
                }
            }
        }else{
//            if($this->_user->getRelevantRole('outspread') == Enum_UserOutspread::General){
//                $users = ORM::factory('User')->order_by('id','DESC')->find_all();
//            }else{
//                $role = ORM::factory('Role',['outspread' => Enum_RoleOutspread::Super]);
//                $ids = [];
//                foreach ($role->users->find_all() as $usr){
//                    $ids[] = $usr->id;
//                }
//
//                if($this->_user->client_id){
//                    //$users = ORM::factory('User')->where('client_id','=',$this->_user->client_id)->and_where('client_id','=',)->order_by('id','DESC')->find_all();
//                    $users = ORM::factory('User')->where('client_id','=',$this->_user->client_id)->order_by('id','DESC');
//                }else{
//                    $users = ORM::factory('User')->where('client_id','=',$this->_user->client_id)->order_by('id','DESC');
//                }
//                if(!empty($ids)){
//                    $users->and_where('id','IN',DB::expr('('.implode(',',$ids).')'));
//                }
//                $users = $users->find_all();
//            }
            $autocompleteMailiList = [];
            foreach ($qc->project->company->users->find_all() as $usr){
                $autocompleteMailiList[$usr->email] = $usr->email;
            }

            $role = ORM::factory('Role',['outspread' => Enum_RoleOutspread::Super]);
            foreach ($role->users->find_all() as $usr){
                $autocompleteMailiList[$usr->email] = $usr->email;
            }
            $this->setResponseData('modal',View::make('reports/quality-control-mailing',['items' => $qc->project->users->find_all(),'autocompleteMailList' => $autocompleteMailiList, 'qc' => $qc, 'secure_tkn' => AesCtr::encrypt($qc->id,$qc->id,192)]));
        }
    }

    public function action_send_reports(){
        $this->_checkForAjaxOrDie();
        $this->_checkPermOrFail('read');
        $projectId = $this->getUIntParamOrDie($this->request->param('id'));
        $tok = $this->request->param('token');
        $token = ORM::factory('ReportQueryToken',['token' => $tok]);
        if(!$token->loaded()){
            throw new HTTP_Exception_404();
        }
        $this->project = ORM::factory('Project',$projectId);
        if(! $this->project->loaded()){
            throw new HTTP_Exception_404();
        }

        if($this->request->method() == Request::POST){
            if($tok != AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$tok,192)){
                $this->_setErrors('Invalid request');
            }
            $emailsList = [];
            foreach ($this->post() as $key => $value){
                if(strpos($key,'emails') !== false){
                    $emailsList[] = $value;
                }

            }
            $projectId = (int)Arr::get($this->post(),'project_id');
            $project = ORM::factory('Project',$projectId);
            if( ! $project->loaded()){
                $this->_setErrors('Incorrect Project');
                return;
            }

            if(!empty($emailsList)){
                foreach ($emailsList as $key => $email){
                    if(!Valid::email($email)){
                        unset($emailsList[$key]);
                    }
                }

                if(count($emailsList)){
                    Queue::enqueue('mailing','Job_Report_SendReportsEmail',[
                        'emails' => $emailsList,
                        'subject' => 'Q4b report share email for project - '.$project->name,
                        'project' => $project->name,
                        'message' => trim(strip_tags(Arr::get($this->post(),'message'))),
                        'link' => URL::site('/reports/guest_access/'.$token->token,'https'),
                        'user' => ['name' => $this->_user->name, 'email' => $this->_user->email],
                        'image' => ($project->image_id) ? ("https://qforb.net/" .$project->main_image->originalFilePath()) : null,
                        'view' => 'emails/report/guest-access',
                        'lang' => Language::getCurrent()->iso2,
                        'expires' => \Carbon\Carbon::createFromTimestamp($token->expires)->format('d/m/Y H:i')
                    ],\Carbon\Carbon::now()->addSeconds(30)->timestamp);
                }else{
                    $this->_setErrors('Empty Mail list');
                }

            }else{
                $this->_setErrors('Empty Mail list');
            }
        }else{
//            if($this->_user->getRelevantRole('outspread') == Enum_UserOutspread::General){
//                $users = ORM::factory('User')->order_by('id','DESC')->find_all();
//            }else{
//                $role = ORM::factory('Role',['outspread' => Enum_RoleOutspread::Super]);
//                $ids = [];
//                foreach ($role->users->find_all() as $usr){
//                    $ids[] = $usr->id;
//                }
//
//                if($this->_user->client_id){
//                    //$users = ORM::factory('User')->where('client_id','=',$this->_user->client_id)->and_where('client_id','=',)->order_by('id','DESC')->find_all();
//                    $users = ORM::factory('User')->where('client_id','=',$this->_user->client_id)->order_by('id','DESC');
//                }else{
//                    $users = ORM::factory('User')->where('client_id','=',$this->_user->client_id)->order_by('id','DESC');
//                }
//                if(!empty($ids)){
//                    $users->and_where('id','IN',DB::expr('('.implode(',',$ids).')'));
//                }
//                $users = $users->find_all();
//            }
            $autocompleteMailiList = [];
            $companyAdmins = [];
            foreach ($this->project->company->users->find_all() as $usr){
                $autocompleteMailiList[$usr->email] = $usr->email;
                if($usr->getRelevantRole('name') == 'company_admin'){
                    $companyAdmins[] = $usr;
                }
            }

            $role = ORM::factory('Role',['outspread' => Enum_RoleOutspread::Super]);
                foreach ($role->users->find_all() as $usr){
                    $autocompleteMailiList[$usr->email] = $usr->email;
                }


            $items = $this->project->users->find_all()->as_array() + $companyAdmins;



            $this->setResponseData('modal',View::make('reports/send-reports',['items' => $items,'autocompleteMailList' => $autocompleteMailiList,'secure_tkn' => AesCtr::encrypt($tok,$tok,192)]));
        }
    }

    public function searchForm($hidden = false){
//        if($this->_user->getRelevantRole('outspread') == Enum_UserOutspread::General){
//            $companies = ORM::factory('Company')->find_all();
//        }elseif($this->_user->getRelevantRole('outspread') != Enum_UserOutspread::Corporate){
//            $companies = $this->_user->client->companies->find_all();
//        }else{ //Outspread Company || Project $this->_user->getRelevantRole('outspread') != Enum_UserOutspread::Company
//            $companies = [$this->_user->company];
//        }
        $companies = $this->_user->availableCompanies();
        $items = [];
        foreach($companies as $comp){
            $items[$comp->id] = [
                'id' => $comp->id,
                'name' => $comp->name,
                'projects' => [],
                'crafts' => [],
                'status' => $comp->status
            ];

            if($this->_user->getRelevantRole('outspread') == Enum_UserOutspread::Project){
                $usrProjects = $this->_user->projects->find_all();
                $usrProjectsArr = [];
                foreach($usrProjects as $pr){
                    $usrProjectsArr [] = $pr->id;
                }

                foreach ($comp->projects->find_all() as $proj){
                    if(!in_array($proj->id,$usrProjectsArr)) continue;
                    $items[$comp->id]['projects'][$proj->id] = [
                        'id' => $proj->id,
                        'name' => $proj->name,
                        'status' => $proj->status,
                        'elements' => Api_DBProjects::getProjectElements($proj->id, '')
                    ];
                }
            }else{
                foreach ($comp->projects->find_all() as $proj){
                    $items[$comp->id]['projects'][$proj->id] = [
                        'id' => $proj->id,
                        'name' => $proj->name,
                        'status' => $proj->status,
                        'elements' => Api_DBProjects::getProjectElements($proj->id, '')
                    ];
                }
            }

            foreach($comp->crafts->where('status','=',Enum_Status::Enabled)->getFilteredCrafts()->find_all() as $craft){
                $items[$comp->id]['crafts'][$craft->id] = [
                    'id' => $craft->id,
                    'name' => $craft->name
                ];
            }

            if(empty($items[$comp->id]['projects']) OR empty($items[$comp->id]['crafts'])){
                unset($items[$comp->id]);
            }
        }

        return View::make('reports/search-form',['data' => json_encode($items), 'items' => $items, 'hidden' => $hidden]);
    }

    protected function _export_report($qcs){
        $ws = new Spreadsheet(array(
            'author'       => 'Q4B',
            'title'	       => 'Report',
            'subject'      => 'Subject',
            'description'  => 'Description',
        ));

        $ws->set_active_sheet(0);
        $as = $ws->get_active_sheet();
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];
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

        $as->setTitle('Report');

        $as->getDefaultStyle()->getFont()->setSize(10);

        $as->getColumnDimension('Q')->setWidth(12);
        $as->getColumnDimension('P')->setWidth(60);
        $as->getColumnDimension('O')->setWidth(60);
        $as->getColumnDimension('N')->setWidth(15);
        $as->getColumnDimension('M')->setWidth(16);
        $as->getColumnDimension('L')->setWidth(19);
        $as->getColumnDimension('K')->setWidth(19);
        $as->getColumnDimension('J')->setWidth(19);
        $as->getColumnDimension('I')->setWidth(45);
        $as->getColumnDimension('H')->setWidth(20);
        $as->getColumnDimension('G')->setWidth(22);
        $as->getColumnDimension('F')->setWidth(14);
        $as->getColumnDimension('E')->setWidth(20);
        $as->getColumnDimension('D')->setWidth(9);
        $as->getColumnDimension('C')->setWidth(17);
        $as->getColumnDimension('B')->setWidth(25);
        $as->getColumnDimension('A')->setWidth(21);
        $as->getRowDimension('1')->setRowHeight(80);
        $as->getRowDimension('2')->setRowHeight(22);
        $as->setAutoFilter('A2:P2');
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
            1 => [],
            2 => [
                __('QC Id'),
                __('Project'),
                __('Structure'),
                __('Floor'),
                __('Space'),
                __('Element type'),
                __('Element number'),
                __('Stage'),
                __('Crafts'),
                __('Create Date'),
                __('Update Date'),
                __('Due Date'),
                __('Severity Level'),
                __('Conditions List'),
                __('Description'),
                __('Corrective action/Performed work'),
                __('Status')
            ],
        ];
        $ws->set_data($sh, false);
        foreach ($qcs as $item){
            $sh [] = [
                $item->id,
                $item->project->name,
                $item->object->name,
                $item->floor->custom_name ? $item->floor->custom_name . ' (' . $item->floor->number . ')' : $item->floor->number,
                $item->space->type->name,
                __($item->place->type),
                $item->place->custom_number,
                __($item->project_stage),
                $item->craft->name,
                date('d/m/Y',$item->created_at),
                date('d/m/Y',$item->updated_at),
                date('d/m/Y',$item->due_date),
                __($item->severity_level),
                __($item->condition_list),
                $item->getDesc(html_entity_decode($item->description), "@##"),
                $item->getDialog(html_entity_decode($item->description), "@##", "\n"),
                __($item->status),
            ];
        }
        $ws->set_data($sh, false);
        $first_letter = PHPExcel_Cell::stringFromColumnIndex(0);
        $last_letter = PHPExcel_Cell::stringFromColumnIndex(count($sh[2])-1);
        $header_range = "{$first_letter}2:{$last_letter}2";
        $ws->get_active_sheet()->getStyle($header_range)->getFont()->setSize(14)->setBold(true);

//        $count = count($sh);
//        for ($i = 3; $i <= $count ; $i++) {
//            $az[] = $i;
//        }
//        $az = array_slice($az, 0, $count);
//        for ($i = 0; $i < count($az); $i++) {
//            foreach ($colsToWrapText as $col) {
//                $ws->get_active_sheet()->getStyle($col.$az[$i])
//                    ->getAlignment()->setWrapText(true);
//            }
//        }

        $as->getStyle('A1:Q'.count($sh))->getBorders()->applyFromArray(
            array(
                'inside' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '3A3B3C'
                    )
                )
            )
        );

        $as->getStyle('A3:Q'.count($sh))->getBorders()->applyFromArray(
            array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array(
                        'rgb' => '3A3B3C'
                    )
                )
            )
        );
        $ws->get_active_sheet()->getStyle('N1:O999')
            ->getAlignment()->setWrapText(true);
        $ws->rtl(Language::getCurrent()->direction === 'rtl');
        $ws->send(['name'=>'report', 'format'=>'Excel5']);
    }

    public function action_tasks(){
        if(Route::name(Request::current()->route()) != 'site.reports.tasks') throw new HTTP_Exception_404;
        $this->auto_render = false;
        $projectId = (int)$this->request->param('projectId');
        $objectId = (int)$this->request->param('objectId');
        $floorId = (int)$this->request->param('floorId');
        $placeId = (int)$this->request->param('placeId');
        $output = [];
        $viewInfo = [];
        if($placeId){
            $place = ORM::factory('PrPlace',$placeId);
            if(!$place->loaded()){
                throw new HTTP_Exception_404;
            }
            $project = $place->project;
            $company = $project->company;
            $crafts = $company->crafts->where('status','=',Enum_Status::Enabled)->find_all();

            $craftTotalTasksCnt = DB::query(Database::SELECT,'
            SELECT ptc.craft_id, COUNT(ptc.craft_id) cnt FROM pr_tasks_crafts ptc INNER JOIN pr_tasks pt ON ptc.task_id = pt.id WHERE pt.project_id='.$project->id.' AND pt.status = "enabled" GROUP BY ptc.craft_id
            ')->execute()->as_array('craft_id');

            $craftUsedTasksCnt = DB::query(Database::SELECT,'
             SELECT tbl1.craft_id, COUNT(tbl1.craft_id) cnt FROM ( SELECT ptc.task_id,ptc.craft_id, CONCAT(ptc.task_id,"-",ptc.craft_id) as tc, qc.project_id
            FROM pr_tasks_crafts ptc
            INNER JOIN qcontrol_pr_tasks qpt ON ptc.task_id = qpt.task_id
            INNER JOIN quality_controls qc ON qpt.qcontrol_id=qc.id
            WHERE qc.place_id = '.$place->id.'
            GROUP BY tc) AS tbl1
  GROUP BY tbl1.craft_id
            ')->execute()->as_array('craft_id');

            foreach ($crafts as $c){
                $output['data'][$c->id] = [
                    'id' => $c->id,
                    'name' => $c->name,
                    'total' => $craftTotalTasksCnt[$c->id]['cnt'],
                    'used' => $craftUsedTasksCnt[$c->id]['cnt']
                ];

                if(isset($craftTotalTasksCnt[$c->id]) AND isset($craftUsedTasksCnt[$c->id])){
                    $output['data'][$c->id]['percent'] = round($craftUsedTasksCnt[$c->id]['cnt'] * 100 / $craftTotalTasksCnt[$c->id]['cnt'],2);
                }else{
                    $output['data'][$c->id]['percent'] = 0;
                }
            }
            //var_dump($craftTotalTasksCnt,$craftUsedTasksCnt);
            $viewInfo['path'] = 'reports/tasks/place';
            $viewInfo['trigger'] = 'tasksReportPlace';
            $output['project'] = $project;
            $output['company'] = $company;
            $output['object'] = $place->object;
            $output['floor'] = $place->floor;
            $output['place'] = $place;
        }elseif ($floorId){
            $floor = ORM::factory('PrFloor',$floorId);
            if(!$floor->loaded()){
                throw new HTTP_Exception_404;
            }
            $placesCount = ['public' => $floor->places->where('type','=','public')->count_all(),'private' => $floor->places->where('type','=','private')->count_all()];
            $project = $floor->project;
            $company = $project->company;
            $crafts = $company->crafts->where('status','=',Enum_Status::Enabled)->find_all();

            $craftTotalTasksCnt = DB::query(Database::SELECT,'
            SELECT ptc.craft_id, COUNT(ptc.craft_id) cnt FROM pr_tasks_crafts ptc INNER JOIN pr_tasks pt ON ptc.task_id = pt.id WHERE pt.project_id='.$project->id.' AND pt.status = "enabled" GROUP BY ptc.craft_id
            ')->execute()->as_array('craft_id');

            $craftUsedTasksCnt['public'] = DB::query(Database::SELECT,'
             SELECT tbl1.craft_id, COUNT(tbl1.craft_id) cnt FROM ( SELECT ptc.task_id,ptc.craft_id, CONCAT(ptc.task_id,"-",ptc.craft_id) as tc, qc.project_id
            FROM pr_tasks_crafts ptc
            INNER JOIN qcontrol_pr_tasks qpt ON ptc.task_id = qpt.task_id
            INNER JOIN quality_controls qc ON qpt.qcontrol_id=qc.id
            WHERE qc.floor_id = '.$floor->id.'
            AND qc.place_type = "public"
            GROUP BY tc) AS tbl1
  GROUP BY tbl1.craft_id
            ')->execute()->as_array('craft_id');

            $craftUsedTasksCnt['private'] = DB::query(Database::SELECT,'
             SELECT tbl1.craft_id, COUNT(tbl1.craft_id) cnt FROM ( SELECT ptc.task_id,ptc.craft_id, CONCAT(ptc.task_id,"-",ptc.craft_id) as tc, qc.project_id
            FROM pr_tasks_crafts ptc
            INNER JOIN qcontrol_pr_tasks qpt ON ptc.task_id = qpt.task_id
            INNER JOIN quality_controls qc ON qpt.qcontrol_id=qc.id
            WHERE qc.floor_id = '.$floor->id.'
            AND qc.place_type = "private"
            GROUP BY tc) AS tbl1
  GROUP BY tbl1.craft_id
            ')->execute()->as_array('craft_id');
            $viewInfo['path'] = 'reports/tasks/floor';
            $viewInfo['trigger'] = 'tasksReportFloor';
            $output['project'] = $project;
            $output['company'] = $company;
            $output['object'] = $floor->object;
            $output['floor'] = $floor;
        }elseif ($objectId){
            $object = ORM::factory('PrObject',$objectId);
            if(!$object->loaded()){
                throw new HTTP_Exception_404;
            }
            $placesCount = ['public' => $object->places->where('type','=','public')->count_all(),'private' => $object->places->where('type','=','private')->count_all()];
            $project = $object->project;
            $company = $project->company;
            $crafts = $company->crafts->where('status','=',Enum_Status::Enabled)->find_all();

            $craftTotalTasksCnt = DB::query(Database::SELECT,'
            SELECT ptc.craft_id, COUNT(ptc.craft_id) cnt FROM pr_tasks_crafts ptc INNER JOIN pr_tasks pt ON ptc.task_id = pt.id WHERE pt.project_id='.$project->id.' AND pt.status = "enabled" GROUP BY ptc.craft_id
            ')->execute()->as_array('craft_id');

            $craftUsedTasksCnt['public'] = DB::query(Database::SELECT,'
             SELECT tbl1.craft_id, COUNT(tbl1.craft_id) cnt FROM ( SELECT ptc.task_id,ptc.craft_id, CONCAT(ptc.task_id,"-",ptc.craft_id) as tc, qc.project_id
            FROM pr_tasks_crafts ptc
            INNER JOIN qcontrol_pr_tasks qpt ON ptc.task_id = qpt.task_id
            INNER JOIN quality_controls qc ON qpt.qcontrol_id=qc.id
            WHERE qc.object_id = '.$object->id.'
            AND qc.place_type = "public"
            GROUP BY tc) AS tbl1
  GROUP BY tbl1.craft_id
            ')->execute()->as_array('craft_id');

            $craftUsedTasksCnt['private'] = DB::query(Database::SELECT,'
             SELECT tbl1.craft_id, COUNT(tbl1.craft_id) cnt FROM ( SELECT ptc.task_id,ptc.craft_id, CONCAT(ptc.task_id,"-",ptc.craft_id) as tc, qc.project_id
            FROM pr_tasks_crafts ptc
            INNER JOIN qcontrol_pr_tasks qpt ON ptc.task_id = qpt.task_id
            INNER JOIN quality_controls qc ON qpt.qcontrol_id=qc.id
            WHERE qc.object_id = '.$object->id.'
            AND qc.place_type = "private"
            GROUP BY tc) AS tbl1
  GROUP BY tbl1.craft_id
            ')->execute()->as_array('craft_id');
            $viewInfo['path'] = 'reports/tasks/object';
            $viewInfo['trigger'] = 'tasksReportObject';
            $output['project'] = $project;
            $output['company'] = $company;
            $output['object'] = $object;
        }elseif ($projectId){
            $project = ORM::factory('Project',$projectId);
            if(!$project->loaded()){
                throw new HTTP_Exception_404;
            }
            $placesCount = ['public' => $project->places->where('type','=','public')->count_all(),'private' => $project->places->where('type','=','private')->count_all()];
            $company = $project->company;
            $crafts = $company->crafts->where('status','=',Enum_Status::Enabled)->find_all();

            $craftTotalTasksCnt = DB::query(Database::SELECT,'
            SELECT ptc.craft_id, COUNT(ptc.craft_id) cnt FROM pr_tasks_crafts ptc INNER JOIN pr_tasks pt ON ptc.task_id = pt.id WHERE pt.project_id='.$project->id.' AND pt.status = "enabled" GROUP BY ptc.craft_id
            ')->execute()->as_array('craft_id');

            $craftUsedTasksCnt['public'] = DB::query(Database::SELECT,'
             SELECT tbl1.craft_id, COUNT(tbl1.craft_id) cnt FROM ( SELECT ptc.task_id,ptc.craft_id, CONCAT(ptc.task_id,"-",ptc.craft_id) as tc, qc.project_id
            FROM pr_tasks_crafts ptc
            INNER JOIN qcontrol_pr_tasks qpt ON ptc.task_id = qpt.task_id
            INNER JOIN quality_controls qc ON qpt.qcontrol_id=qc.id
            WHERE qc.project_id = '.$project->id.'
            AND qc.place_type = "public"
            GROUP BY tc) AS tbl1
  GROUP BY tbl1.craft_id
            ')->execute()->as_array('craft_id');

            $craftUsedTasksCnt['private'] = DB::query(Database::SELECT,'
             SELECT tbl1.craft_id, COUNT(tbl1.craft_id) cnt FROM ( SELECT ptc.task_id,ptc.craft_id, CONCAT(ptc.task_id,"-",ptc.craft_id) as tc, qc.project_id
            FROM pr_tasks_crafts ptc
            INNER JOIN qcontrol_pr_tasks qpt ON ptc.task_id = qpt.task_id
            INNER JOIN quality_controls qc ON qpt.qcontrol_id=qc.id
            WHERE qc.project_id = '.$project->id.'
            AND qc.place_type = "private"
            GROUP BY tc) AS tbl1
  GROUP BY tbl1.craft_id
            ')->execute()->as_array('craft_id');
            $viewInfo['path'] = 'reports/tasks/project';
            $viewInfo['trigger'] = 'tasksReportProject';
            $output['project'] = $project;
            $output['company'] = $company;


        }else{
            throw new HTTP_Exception_404;
        }

        if(!$placeId){
            foreach ($crafts as $c){
                if(isset($craftTotalTasksCnt[$c->id]) AND isset($craftUsedTasksCnt['public'][$c->id])){
                    $output['data']['public'][$c->id]['percent'] = round($craftUsedTasksCnt['public'][$c->id]['cnt'] * 100 / ($craftTotalTasksCnt[$c->id]['cnt'] * $placesCount['public']),2);
                    $output['data']['public'][$c->id] += [
                        'id' => $c->id,
                        'name' => $c->name,
                        'total' => $craftTotalTasksCnt[$c->id]['cnt'] * $placesCount['public'],
                        'used' => $craftUsedTasksCnt['public'][$c->id]['cnt']
                    ];
                }
                if(isset($craftTotalTasksCnt[$c->id]) AND isset($craftUsedTasksCnt['private'][$c->id])){
                    $output['data']['private'][$c->id]['percent'] = round($craftUsedTasksCnt['private'][$c->id]['cnt'] * 100 / ($craftTotalTasksCnt[$c->id]['cnt'] * $placesCount['private']),2);
                    $output['data']['private'][$c->id]+= [
                        'id' => $c->id,
                        'name' => $c->name,
                        'total' => $craftTotalTasksCnt[$c->id]['cnt'] * $placesCount['private'],
                        'used' => $craftUsedTasksCnt['private'][$c->id]['cnt']
                    ];
                }
            }
            usort($output['data']['public'],function($a,$b){
                if($a['percent'] == $b['percent']){
                    return 0;
                }

                return ($a['percent'] > $b['percent']) ? -1 : 1;
            });
            usort($output['data']['private'],function($a,$b){
                if($a['percent'] == $b['percent']){
                    return 0;
                }

                return ($a['percent'] > $b['percent']) ? -1 : 1;
            });
        }else{
            usort($output['data'],function($a,$b){
                if($a['percent'] == $b['percent']){
                    return 0;
                }

                return ($a['percent'] > $b['percent']) ? -1 : 1;
            });
        }

        if($this->request->is_ajax()){
            $this->setResponseData('triggerEvent',$viewInfo['trigger']);
            $this->setResponseData('html',View::make($viewInfo['path'],$output));
        }else{
            echo View::make($viewInfo['path'],$output);
        }
    }

//    private function sendNotificationToUsers($usersList) {
//
//        $usersDeviceTokens = [];
////
//        foreach ($usersList as $user) {
//            if($user->device_token) {
//                array_push($usersDeviceTokens, $user->device_token);
//            }
//        }

//        echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$usersDeviceTokens]); echo "</pre>"; exit;


//        $timestamp = time();

//        $usersDeviceTokens = ['f5bWjICSSMiE40tO7w5RF2:APA91bGGAwSYAYz5t7b1l8jnC385xjLGne5FkWh2LxHQ9W19AflFCnNHsLo8nF1Ydn9_w3dd2a1BmhGFPfLlmGMrWmB0z3k5hQ77bq0zljFxPQAasA9tBjA45rXHb-uXZ6NFgQKklP0i'];

//        PushHelper::test([
//            'lang' => \Language::getCurrent()->iso2,
//            'action' => 'qc',
//            'usersDeviceTokens' => $usersDeviceTokens
//        ], $timestamp );
//    }
}