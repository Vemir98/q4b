<?php defined('SYSPATH') OR die('No direct script access.');
use JonnyW\PhantomJs\Client;

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 31.05.2017
 * Time: 14:31
 */
class Controller_Dashboard extends HDVP_Controller_Template
{
    protected $_actions_perms = [
        'index,quality_control_list,plans_list,certifications_list,show_fcc,print,export_pdf' => [
            'GET' => 'read',
            'POST' => 'read'
        ],
        'update_plan' =>[
            'GET' => 'read',
            'POST' => 'update',
        ],
        'approve_certification' =>[
            'POST' => 'update',
        ]
    ];
    public function action_index(){
//        $this->include_editor = true;
//        $requestData = $selectedObjects = [];
//        $selectedCompany = $selectedProject = null;
//        if($this->request->method() == Request::POST) {
//            $this->_checkForAjaxOrDie();
//
//            $requestData = Arr::extract($this->post(),['company','project','objects']);
//            foreach ($requestData as $val){
//                if(empty($val)){
//                    throw new HTTP_Exception_404();
//                }
//            }
//            $companies = $this->_user->availableCompanies();
//            $selectedCompany = $this->_user->getCompanyIfItAvailable($requestData['company']);
////            if($this->_user->getRelevantRole('outspread') == Enum_UserOutspread::General){
////                $selectedCompany = ORM::factory('Company',(int)$requestData['company']);
////                $companies = ORM::factory('Company')->find_all()->as_array();
////            }else{
////                $selectedCompany = $this->_user->client->companies->where('id','=',(int)$requestData['company'])->find();
////                $companies = $this->_user->client->companies->find_all()->as_array();
////            }
//            if( !$selectedCompany OR !$selectedCompany->loaded()){
//                throw new HTTP_Exception_404();
//            }
//
//            $selectedProject = $selectedCompany->projects->where('id','=',$requestData['project'])->find();
//            if( ! $selectedProject->loaded()){
//                throw new HTTP_Exception_404();
//            }
//            if(!is_array($requestData['objects'])){
//                $requestData['objects'] = [$requestData['objects']];
//            }
//            array_walk($requestData['objects'],function(&$item){
//                $item = (int)$item;
//            });
//            $selectedObjects = $selectedProject->objects->where('id','IN',DB::expr('('.implode(',',$requestData['objects']).')'))->find_all();
//            if(!count($selectedObjects)){
//                throw new HTTP_Exception_404();
//            }
//        }else{
//            $companies = $this->_user->availableCompanies();
//            if(!is_array($companies)){
//                $companies = $companies->as_array();
//            }
////            if($this->_user->getRelevantRole('outspread') == Enum_UserOutspread::General){
////                $companies = ORM::factory('Company')->find_all()->as_array();
////                foreach ($companies as $key => $c){
////                    if(!$c->projects->count_all()){
////                        unset($companies[$key]);
////                    }
////                }
////            }else{
////                $companies = $this->_user->client->companies->find_all()->as_array();
////            }
//            foreach ($companies as $key => $c){
//                if(!$c->projects->count_all()){
//                    unset($companies[$key]);
//                }
//            }
//            if(count($companies)){
//                $selectedCompany = $companies[0];
//            }
//
//        }
//
//        $projects = $objects =  $plans = $qualityControls = $certifications = $objectIds = [];
//        if(!empty($companies)){
//            if($this->_user->getRelevantRole('outspread') == Enum_UserOutspread::Project){
//                $usrProjects = [];
//                foreach($this->_user->projects->find_all() as $p){
//                    $usrProjects[] = $p->id;
//                }
//                if(count($usrProjects)){
//                    $usrProjects = '('.implode(',',$usrProjects).')';
//                    foreach ($companies as $cmp){
//                        $projects = Arr::merge($projects,$cmp->projects->where('id','IN',DB::expr($usrProjects))->find_all()->as_array());
//                    }
//                }
//            }else{
//                foreach ($companies as $cmp){
//                    $projects = Arr::merge($projects,$cmp->projects->find_all()->as_array());
//                }
//            }
//
//
//            if(!empty($projects)){
//                if(empty($selectedProject)){
//
//                    if(!empty($selectedCompany)){
//                        if(isset($usrProjects) AND count($usrProjects)){
//                            $selectedProject = $projects[0];
//                        }else{
//                            $selectedProject = $selectedCompany->projects->find();
//                        }
//                    }else{
//                        $selectedProject = $projects[0];
//                    }
//                }
//                if(empty($selectedObjects)){
//                    $selectedObjects = $selectedProject->objects->find_all();
//                }
//
//                foreach ($selectedObjects as $o){
//                    $objectIds []= $o->id;
//                }
//                foreach ($projects as $prj){
//                    $objects = Arr::merge($objects,$prj->objects->find_all()->as_array());
//                }
//
//                $certRequest = ORM::factory('PrCertification')
//                    ->where('prcertification.project_id','=',$selectedProject->id)
//                    ->with('project')
//                    ->with('craft');
//
//
//                $qualityControlsUrl = Route::url('site.dashboard.qualityControlList',[
//                    'lang' => Language::getCurrent()->slug,
//                    'controller' => 'dashboard',
//                    'action' => 'quality_control_list',
//                    'project' => $selectedProject->id,
//                    'status' => Enum_QualityControlApproveStatus::Waiting,
//                    'objects' => implode('-',$objectIds)
//                ]);
//                $qualityControls = Request::factory($qualityControlsUrl)->execute()->body();
//
//                $plansUrl = Route::url('site.dashboard.plansList',[
//                    'lang' => Language::getCurrent()->slug,
//                    'controller' => 'dashboard',
//                    'action' => 'plans_list',
//                    'project' => $selectedProject->id,
//                    'status' => Enum_QualityControlApproveStatus::Waiting,
//                    'objects' => implode('-',$objectIds)
//                ]);
//                $plans =  Request::factory($plansUrl)->execute()->body();
//
//                $certificationsUrl = Route::url('site.dashboard.certificationsList',[
//                    'lang' => Language::getCurrent()->slug,
//                    'controller' => 'dashboard',
//                    'action' => 'certifications_list',
//                    'status' => Enum_ApprovalStatus::Waiting,
//                    'project' => $selectedProject->id
//                ]);
//                $certifications = Request::factory($certificationsUrl)->execute()->body();
//            }
//        }
//
//        $content = View::make('dashboard/main',[
//            'filterView' => View::make('dashboard/filter',[
//                'selectedCompanyId' => !empty($selectedCompany) ? $selectedCompany->id : null,
//                'selectedProjectId' => !empty($selectedProject) ? $selectedProject->id : null,
//                'selectedObjectIds' => $objectIds,
//                'companies' => $companies,
//                'projects' => $projects,
//                'objects' => $objects,
//            ]),
//            'qualityControlsView' => !empty($qualityControls) ? $qualityControls : null,
//            'plansView' => !empty($plans) ? $plans : null,
//            'certificationsView' => !empty($certifications) ? $certifications : null
//        ]);
//
//        if($this->_isAjax){
//            $this->setResponseData('html',$content->render());
//            $this->setResponseData('triggerEvent','dashboardUpdated');
//        }else{
//            $this->template->content = $content;
//        }

        VueJs::instance()->addComponent('dashboard/statistics');
        VueJs::instance()->includeCharts();
        VueJs::instance()->includeDateTimePiker();
        VueJs::instance()->includeMultiselect();

        $translations = [
            'companies' => __('Companies'),
            'projects' => __('Projects'),
            'select_all' => __('select all'),
            'unselect_all' => __('unselect all'),
            'date' => __('Date'),
            'show' => __('Show'),
            'today' => __('Today'),
            'yesterday' => __('Yesterday'),
            '7_days' => __('7 days'),
            'monthly' => __('Monthly'),
            'quarterly' => __('Quarterly'),
            'half_year' => __('Half year'),
            'one_year' => __('one_year'),
            'two_years' => __('two_years'),
            'three_years' => __('three_years'),
            'four_years' => __('four_years'),
            'qc' => __('QC'),
            'places' => __('Places'),
            'certificates' => __('Certificates'),
            'dashboard_new' => __('Dashboard_new'),
            'total' => __('Total'),
            'delivery_protocols' => __('delivery_protocols'),
            'ears' => __('ears'),
            'lab_control_reports' => __('lab_control_reports'),
            'show_full_reports' => __('show_full_reports'),
            'analytics_for' => __('analytics_for'),
            'qc_in_system' => __('qc_in_system'),
            'invalid_qc_in_system' => __('invalid_qc_in_system'),
            'repaired_qc_in_system' => __('repaired_qc_in_system'),
            'other_qc_in_system' => __('other_qc_in_system'),
            'existing_and_for_repair_qc_in_system' => __('existing && for_repair'),
            'places_in_system' => __('places_in_system'),
            'private_places' => __('private_places'),
            'public_places' => __('public_places'),
            'certificates_in_system' => __('certificates_in_system'),
            'not_approved_certificates' => __('not_approved_certificates'),
            'approved_certificates' => __('approved_certificates'),
            'deliveries_done' => __('deliveries_done'),
            'pre_deliveries_done' => __('pre_deliveries_done'),
            'ears_in_system' => __('ears_in_system'),
            'not_appropriate_ears' => __('not_appropriate_ears'),
            'appropriate_ears' => __('appropriate_ears'),
            'lab_controls_sent' => __('lab_controls_sent'),
            'approved_lab_controls' => __('approved_lab_controls'),
            'approved' => __('Approved'),
            'not_appropriate' => __('not_appropriate'),
            'appropriate' => __('appropriate'),
            'private' => __('private'),
            'public' => __('public'),
            'invalid' => __('invalid'),
            'repaired' => __('repaired'),
            'other' => __('other'),
            'delivery' => __('Delivery'),
            'pre_delivery' => __('pre_delivery'),
            'not_approved' => __('non_approve'),
            'not_approved_lab_controls' => __('not_approved_lab_controls'),
            'no_data' => __('no_data'),
            'no_qc' => __('no_qc'),
            'with_qc' => __('with_qc'),
            'qc_report' => __('QC Report'),
            'place_report' => __('Place report'),
            'delivery_report' => __('Delivery report'),
            'export' => __('Export'),
            'report_range' => __('Report Range'),
            'waiting' => __('waiting'),
            'partial_process' => __('partial_process')
        ];

        $filters = Arr::extract($_GET, [
            'projectIds',
            'range'
        ]);

        if($filters['projectIds'] && $filters['range']) {
            VueJs::instance()->addComponent('dashboard/print-pdf');

            $this->template->content = View::make('dashboard/print_pdf', [
                'translations' => $translations,
                'projectIds' => $filters['projectIds'] ?: null,
                'range' => $filters['range'] ?: null,
                'userPreferencesTypes' => Enum_UserPreferencesTypes::toArray()
            ]);
        } else {
            VueJs::instance()->includeJsPDF();

            $this->template->content = View::make('dashboard/index', [
                'translations' => $translations,
                'projectIds' => $filters['projectIds'] ?: null,
                'range' => $filters['range'] ?: null,
                'userPreferencesTypes' => Enum_UserPreferencesTypes::toArray()
            ]);
        }

//                $plansUrl = Route::url('site.dashboard.plansList',[
//                    'lang' => Language::getCurrent()->slug,
//                    'controller' => 'dashboard',
//                    'action' => 'plans_list',
//                    'project' => $selectedProject->id,
//                    'status' => Enum_QualityControlApproveStatus::Waiting,
//                    'objects' => implode('-',$objectIds)
//                ]);
//                $plans =  Request::factory($plansUrl)->execute()->body();
//
//                $certificationsUrl = Route::url('site.dashboard.certificationsList',[
//                    'lang' => Language::getCurrent()->slug,
//                    'controller' => 'dashboard',
//                    'action' => 'certifications_list',
//                    'status' => Enum_ApprovalStatus::Waiting,
//                    'project' => $selectedProject->id
//                ]);
//                $certifications = Request::factory($certificationsUrl)->execute()->body();
//            }
//        }
//
//        $content = View::make('dashboard/main',[
//            'filterView' => View::make('dashboard/filter',[
//                'selectedCompanyId' => !empty($selectedCompany) ? $selectedCompany->id : null,
//                'selectedProjectId' => !empty($selectedProject) ? $selectedProject->id : null,
//                'selectedObjectIds' => $objectIds,
//                'companies' => $companies,
//                'projects' => $projects,
//                'objects' => $objects,
//            ]),
//            'qualityControlsView' => !empty($qualityControls) ? $qualityControls : null,
//            'plansView' => !empty($plans) ? $plans : null,
//            'certificationsView' => !empty($certifications) ? $certifications : null
//        ]);
//
//        if($this->_isAjax){
//            $this->setResponseData('html',$content->render());
//            $this->setResponseData('triggerEvent','dashboardUpdated');
//        }else{
//            $this->template->content = $content;
//        }
    }

    public function action_quality_control_list(){
        $this->auto_render = false;
        $projectId = (int)$this->request->param('project');
        $objectIds = $this->request->param('objects');
        $status = $this->request->param('status');

        if(empty($projectId) OR empty($objectIds)){
            throw new HTTP_Exception_404();
        }

        $objectIds = explode('-',$objectIds);
        if(!is_array($objectIds)){
            $objectIds = [$objectIds];
        }
        array_walk($objectIds,function(&$item){
            $item = (int)$item;
        });

        $project = ORM::factory('Project',$projectId);
        if( ! $project->loaded()){
            throw new HTTP_Exception_404;
        }
        $objects = $project->objects->where('id','IN',DB::expr('('.implode(',',$objectIds).')'))->find_all();
        if(!count($objects) OR !(count($objects) == count($objectIds))){
            throw new HTTP_Exception_404();
        }


        $request = ORM::factory('QualityControl')
            ->where('qualitycontrol.project_id','=',$project->id)
            ->and_where('qualitycontrol.object_id','IN',DB::expr('('.implode(',',$objectIds).')'))
            ->join('pr_places')
            ->on('qualitycontrol.place_id','=','pr_places.id')
            ->join('pr_floors')
            ->on('qualitycontrol.floor_id','=','pr_floors.id')
            ->with('project')
            ->with('object')
            ->with('floor')
            ->with('place')
            ->with('craft');
        $qcRequest = clone ($request);
        if(!empty($status)){
            if(!in_array($status,Enum_QualityControlApproveStatus::toArray())){
                throw new HTTP_Exception_404();
            }
            $qcRequest->and_where('approval_status','=',$status);
        }
        //полная лажа
        //здание/конструкция, этажи с меньшего к большему, помещения с меньшего номера к большему ( для private и public по очереди)
        $request->order_by('qualitycontrol.project_id','ASC')
                ->order_by('qualitycontrol.object_id','ASC')
                ->order_by('pr_floors.number','ASC')
                ->order_by('pr_places.number','ASC')
                ->order_by('qualitycontrol.place_type','ASC')
                ->order_by('qualitycontrol.created_at','DESC');
        $qcRequest->order_by('qualitycontrol.project_id','ASC')
            ->order_by('qualitycontrol.object_id','ASC')
            ->order_by('pr_floors.number','ASC')
            ->order_by('pr_places.number','ASC')
            ->order_by('qualitycontrol.place_type','ASC')
            ->order_by('qualitycontrol.created_at','DESC');
        if(Arr::get($_GET,'export_qc_list') != 1) {
            $qualityControls = (new ORMPaginate($qcRequest))->getData();
        }else{
            $qualityControls['items'] = $qcRequest->find_all();
        }
        $qualityControls['statuses'] = ['all' =>'All'] + Enum_QualityControlApproveStatus::toArray();//изменил Сергей по просьбе Давида
        foreach ($qualityControls['statuses'] as $key => &$val){
            $tmpUrlParams = Request::current()->param();
            $tmpRequest = clone($request);
            unset($tmpUrlParams['status'],$tmpUrlParams['page']);
            if($key == 'all'){
                $output = [
                    'count' => $tmpRequest->count_all(),
                    'url' => Route::url('site.dashboard.qualityControlList',$tmpUrlParams,'https'),
                    'text' => $val
                ];
            }else{
                $tmpUrlParams['status'] = $val;
                $output = [
                    'count' => $tmpRequest->and_where('approval_status','=',$val)->count_all(),
                    'url' => Route::url('site.dashboard.qualityControlList',$tmpUrlParams,'https'),
                    'text' => $val
                ];
            }
            $val = $output;
        }

        $content = View::make('dashboard/quality-controls/list',[
            'data' => $qualityControls,
        ]);

        if(Request::current()->is_initial()){
            if(Arr::get($_GET,'export_qc_list') == 1){
                $this->_export_qc_list($qualityControls['items']);
            }else{
                $this->setResponseData('html',$content->render());
            }
        }else{
            $this->response->body($content->render());
        }
    }

    protected function _export_qc_list($qcs){
        $ws = new Spreadsheet(array(
            'author'       => 'Q4B',
            'title'	       => 'Report',
            'subject'      => 'Subject',
            'description'  => 'Description',
        ));

        $ws->set_active_sheet(0);
        $as = $ws->get_active_sheet();
        $as->setTitle('Quality Controls');

        $as->getDefaultStyle()->getFont()->setSize(10);
        $as->getColumnDimension('A')->setWidth(10);
        $as->getColumnDimension('B')->setWidth(40);
        $as->getColumnDimension('C')->setWidth(13);
        $as->getColumnDimension('D')->setWidth(13);
        $as->getColumnDimension('E')->setWidth(40);
        $as->getColumnDimension('F')->setWidth(15);
        $as->getColumnDimension('G')->setWidth(14);
        $as->getColumnDimension('H')->setWidth(8);
        $as->getColumnDimension('I')->setWidth(20);
        $as->getColumnDimension('J')->setWidth(40);


        $sh = [
            1 => [__('Id'),__('Price'),__('Description'),__('Created Date'),__('Status'), __('Crafts'), __('Element number'), __('Element type'), __('Floor'), __('Structure'), __('Project')],
        ];
        $ws->set_data($sh, false);
        foreach ($qcs as $item){
            $sh [] = [$item->id,null,html_entity_decode($item->description), date('d/m/Y',$item->created_at), __($item->status), $item->craft->name, $item->place->number, __($item->place->type),$item->floor->number, $item->object->name, $item->project->name];
        }

        $ws->set_data($sh, false);
        $first_letter = PHPExcel_Cell::stringFromColumnIndex(0);
        $last_letter = PHPExcel_Cell::stringFromColumnIndex(count($sh[1])-1);
        $header_range = "{$first_letter}1:{$last_letter}1";
        $ws->get_active_sheet()->getStyle($header_range)->getFont()->setSize(12)->setBold(true);
        $ws->send(['name'=>'QualityControls', 'format'=>'Excel5']);
    }

    public function action_plans_list(){
        $this->auto_render = false;
        $projectId = (int)$this->request->param('project');
        $objectIds = $this->request->param('objects');
        $status = $this->request->param('status');

        if(empty($projectId) OR empty($objectIds)){
            throw new HTTP_Exception_404();
        }

        $objectIds = explode('-',$objectIds);
        if(!is_array($objectIds)){
            $objectIds = [$objectIds];
        }
        array_walk($objectIds,function(&$item){
            $item = (int)$item;
        });

        $project = ORM::factory('Project',$projectId);
        if( ! $project->loaded()){
            throw new HTTP_Exception_404;
        }
        $objects = $project->objects->where('id','IN',DB::expr('('.implode(',',$objectIds).')'))->find_all();
        if(!count($objects) OR !(count($objects) == count($objectIds))){
            throw new HTTP_Exception_404();
        }


        $request = ORM::factory('PrPlan')
            ->where('prplan.project_id','=',$project->id)
            ->and_where('prplan.object_id','IN',DB::expr('('.implode(',',$objectIds).')'))
            ->with('project')
            ->with('object')
            ->with('profession');
        $plRequest = clone ($request);
        if(!empty($status)){
            if(!in_array($status,Enum_QualityControlApproveStatus::toArray())){
                throw new HTTP_Exception_404();
            }
            $plRequest->and_where('approval_status','=',$status);
        }
        $plans = (new ORMPaginate($plRequest))->getData();
        $plans['statuses'] = ['all' => 'All'] + Enum_QualityControlApproveStatus::toArray();//изменил Сергей по просьбе Давида
        foreach ($plans['statuses'] as $key => &$val){
            $tmpUrlParams = Request::current()->param();
            $tmpRequest = clone($request);
            unset($tmpUrlParams['status'],$tmpUrlParams['page']);
            if($key == 'all'){
                $output = [
                    'count' => $tmpRequest->count_all(),
                    'url' => Route::url('site.dashboard.plansList',$tmpUrlParams,'https'),
                    'text' => $val
                ];
            }else{
                $tmpUrlParams['status'] = $val;
                $output = [
                    'count' => $tmpRequest->and_where('approval_status','=',$val)->count_all(),
                    'url' => Route::url('site.dashboard.plansList',$tmpUrlParams,'https'),
                    'text' => $val
                ];
            }
            $val = $output;
        }

        $content = View::make('dashboard/plans/list',[
            'data' => $plans,
        ]);

        if(Request::current()->is_initial()){
            $this->setResponseData('html',$content->render());
        }else{
            $this->response->body($content->render());
        }


    }

    public function action_certifications_list(){
        $this->auto_render = false;
        $projectId = (int)$this->request->param('project');
        $status = $this->request->param('status');

        if(empty($projectId)){
            throw new HTTP_Exception_404();
        }


        $project = ORM::factory('Project',$projectId);
        if( ! $project->loaded()){
            throw new HTTP_Exception_404;
        }


        $request = ORM::factory('PrCertification')
            ->where('prcertification.project_id','=',$project->id)
            ->with('project')
            ->with('craft');
        $plRequest = clone ($request);
        if(!empty($status)){
            if(!in_array($status,Enum_ApprovalStatus::toArray())){
                throw new HTTP_Exception_404();
            }
            $plRequest->and_where('approval_status','=',$status);
        }
        $plans = (new ORMPaginate($plRequest))->getData();
        $plans['statuses'] = ['all' => 'All'] + Enum_ApprovalStatus::toArray();//изменил Сергей по просьбе Давида
        foreach ($plans['statuses'] as $key => &$val){
            $tmpUrlParams = Request::current()->param();
            $tmpRequest = clone($request);
            unset($tmpUrlParams['status'],$tmpUrlParams['page']);
            if($key == 'all'){
                $output = [
                    'count' => $tmpRequest->count_all(),
                    'url' => Route::url('site.dashboard.certificationsList',$tmpUrlParams,'https'),
                    'text' => $val
                ];
            }else{
                $tmpUrlParams['status'] = $val;
                $output = [
                    'count' => $tmpRequest->and_where('approval_status','=',$val)->count_all(),
                    'url' => Route::url('site.dashboard.certificationsList',$tmpUrlParams,'https'),
                    'text' => $val
                ];
            }
            $val = $output;
        }

        $content = View::make('dashboard/certifications/list',[
            'data' => $plans,
        ]);

        if(Request::current()->is_initial()){
            $this->setResponseData('html',$content->render());
        }else{
            $this->response->body($content->render());
        }


    }

    public function action_approve_certification(){
        if($this->request->method() != Request::POST){
            throw new HTTP_Exception_404();
        }
        $id = (int)$this->request->param('id');
        $cert = ORM::factory('PrCertification',$id);
        if( ! $this->_user->is('super_admin')) {
            if (!$cert->loaded() OR $cert->approval_status != Enum_ApprovalStatus::Waiting) {
                throw new HTTP_Exception_404();
            }
        }
        if( ! $this->_user->is('super_admin')) {
            $cert->approval_status = Enum_ApprovalStatus::Approved;
        }else{
            $cert->approval_status = $cert->approval_status != Enum_ApprovalStatus::Approved ? Enum_ApprovalStatus::Approved : Enum_ApprovalStatus::Waiting;
        }


        $cert->approved_by = $this->_user->id;
        $cert->approved_at = time();
        $cert->save();
    }

    public function action_update_plan(){

        $this->_checkForAjaxOrDie();

        $plan = ORM::factory('PrPlan',(int)$this->request->param('id'));
        $project = $plan->project;
        $company = $project->company;
        View::set_global('_PROJECT', $project);
        View::set_global('_COMPANY', $company);
        if($this->request->method() == Request::POST){
            try{
                Database::instance()->begin();
                $data = Arr::extract($this->post(),['name','edition','description','object_id','date','profession_id','scale','status','approval_status']);
                if(Arr::get($this->post(),'number')){
                    $place = ORM::factory('PrPlace',['object_id' => Arr::get($this->post(),'object_id'),'number' => Arr::get($this->post(),'number')]);
                    if( ! $place->loaded()){
                        throw new HDVP_Exception('Incorrect number');
                    }
                    $data['place_id'] = $place->id;
                }
                $data['date'] = DateTime::createFromFormat('d/m/Y',$data['date'])->getTimestamp();
                $plan->values($data);
                $plan->project_id = $project->id;
                $plan->save();
                if( ! $plan->place_id){
                    if(!isset($this->post()['floors']) OR (empty($this->post()['floors']) AND $this->post()['floors'] !== '0')){
                        throw new HDVP_Exception('You must choose floors or number');
                    }
                    $object = ORM::factory('PrObject',$plan->object_id);
                    if(is_string($this->post()['floors'])){
                        $dataFloors = [$this->post()['floors']];
                        $dataFloors = array_diff($dataFloors,array(''));
                    }else{
                        $dataFloors = $this->post()['floors'];
                    }
                    $floors = $object->floors->where('number','IN',DB::expr('('.implode(',',$dataFloors).')'))->find_all();
                    if(count($floors) != count($this->post()['floors'])){
                        throw new HDVP_Exception('Incorrect flor numbers');
                    }
                    $plan->remove('floors');
                    foreach ($floors as $floor){
                        $plan->add('floors',$floor);
                    }

                }else{
                    $plan->remove('floors');
                }
                $project->makeProjectPaths();
                if(!empty($this->files()) AND !empty($this->files()['images'])){
                    foreach ($this->files()['images'] as $key => $file){
                        $uploadedFiles[] = [
                            'name' => str_replace($project->plansPath().DS,'',Upload::save($file,null,$project->plansPath())),
                            'original_name' => $file['name'],
                            'ext' => Model_File::getFileExt($file['name']),
                            'mime' => $file['type'],
                            'path' => str_replace(DOCROOT,'',$project->plansPath()),
                            'token' => md5($file['name']).base_convert(microtime(false), 10, 36),
                        ];
                    }
                }
                if(!empty($uploadedFiles)){
                    foreach ($uploadedFiles as $file){
                        $file = ORM::factory('PlanFile')->values($file)->save();
                        $plan->add('files', $file->pk());
                        Event::instance()->fire('onPlanFileAdded',['sender' => $this,'item' => $file]);
                    }
                }
                Database::instance()->commit();
                $this->setResponseData('triggerEvent','dashboardPlansUpdated');
            }catch (ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch (HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }catch (Exception $e){
                Database::instance()->rollback();
                throw $e;
                $this->_setErrors('Operation Error');
            }
        }else{
            $this->setResponseData('modal',View::make('dashboard/plans/update',[
                'crafts' => $company->crafts->where('status','=',Enum_Status::Enabled)->order_by('name')->with('professions')->find_all(),
                'professions' => $company->professions->where('status','=',Enum_Status::Enabled)->with('crafts')->find_all(),
                'action' => URL::site('dashboard/update_plan/'.$plan->id),
                'item' => $plan
            ]));
        }

    }

    public function action_print(){
        $this->auto_render = false;

        echo View::make('dashboard/print_pdf');
    }

    public function action_export_pdf(){
        try {
            $filters = Arr::extract($_GET, [
                'projectIds',
                'range'
            ]);

            $lang = Arr::get($_GET,'lang', 'en');

            $valid = Validation::factory($filters);

            $valid
                ->rule('projectIds', 'not_empty')
                ->rule('range', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'Incorrect data');
            }

            $filePath = $this->_makePdf(URL::withLang('dashboard', $lang,'https').'?'.http_build_query($filters));

            header('Location: '.URL::withLang($filePath,'en'));exit;
        } catch (API_ValidationException $e){
            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$e->getMessage()]); echo "</pre>"; exit;
        } catch (Exception $e){
            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$e->getMessage()]); echo "</pre>"; exit;
        }

    }

    private function _makePdf($url){
        try {
            $client = Client::getInstance();

            $client->getEngine()->setPath(DOCROOT.'phantomjs-2.1.1-linux-x86_64/bin/phantomjs');
            $client->getEngine()->addOption('--ignore-ssl-errors=true');

//        $client->getEngine()->setPath('/home/qforbnet/www/phantomjs-2.1.1-linux-x86_64/bin/phantomjs');
            $client->getEngine()->addOption('--cookies-file=cook.txt');


//            $request = $client->getMessageFactory()->createCaptureRequest('https://qforb.sunrisedvp.systems', 'GET');
            $request = $client->getMessageFactory()->createCaptureRequest('https://qforb.net', 'GET');

            $request->addHeader('Pjsbot76463', '99642');

            $response = $client->getMessageFactory()->createResponse();
            $client->send($request, $response);

            /**
             * @see JonnyW\PhantomJs\Http\CaptureRequest
             **/
            $request = $client->getMessageFactory()->createPdfRequest($url, 'GET',15000);
            $request->addHeader('Pjsbot76463', '99642');

            $uniqId = uniqid();
            $filePath = 'media/data/dashboard/statistics/'.$uniqId.'.pdf';
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
            exec("chmod -R 777 /home/qforbnet/www/media/data/dashboard/statistics/".$uniqId.'pdf');
            // Send the request
            $client->send($request, $response);
//            exec("chmod -R 777 ".DOCROOT.$filePath);
//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r("chmod -R 777 ".DOCROOT.'media/data/dashboard/statistics'); echo "</pre>"; exit;
//            exec("chmod -R 777 ".DOCROOT.'media/data/dashboard/statistics');

            return $filePath;
        }  catch (Exception $e){
            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$e->getMessage()]); echo "</pre>"; exit;
        }

    }

}