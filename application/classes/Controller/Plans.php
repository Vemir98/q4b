<?php defined('SYSPATH') OR die('No direct script access.');
//UPDATE pr_places pp SET pp.custom_number = IF(pp.type = 'public',CONCAT('PB',pp.number),CONCAT('N',pp.number)) WHERE pp.custom_number IS NULL OR pp.custom_number = ''
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 5:53
 */
class Controller_Plans extends HDVP_Controller_Template
{
    protected $_actions_perms = [
        'list,plans_list,tracking_list,get_custom_number,plan_list_search' => [
            'GET' => 'read'
        ],
        'update,update_tracking,plans_mailing' => [
            'GET' => 'read',
            'POST' => 'update'
        ],
        'create_plan,update_plan,add_edition,copy_plan,update_plan_list,plans_printed' => [
            'POST' => 'update'
        ],
        'update_tasks' => 'tasks',
        'get_images,plans_professions_list,create_plan,update_plan,add_edition' => [
            'GET' => 'read'
        ],
        'plan_history' => [
            'GET' => 'read'
        ],
        'set_image,copy_plan,project_objects,create_plan,update_plan,add_edition' => [
            'GET' => 'update'
        ],
        'delete_image,delete_plans_file,plan_delete,delete_tracking,delete_tracking_file' => [
            'GET' => 'delete'
        ],
        'plans_delete' => [
            'POST' => 'delete'
        ]
    ];

    public $company, $project;

    public function before()
    {

        parent::before();
//        if(md5(Request::$client_ip) != '6d5534aa5d02269f1e7a8d2cd196711d')
//        die('Projects page stopped! Maintenance work in backend side!!! <br><a href="/">go to Homepage</a>');
        if ($this->auto_render === TRUE)
        {
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Plans'))->set_url('/plans'));
        }
    }

    public function after()
    {
        if ($this->auto_render === TRUE)
        {
            if($this->request->action() == 'company'){
                if($this->company){
                    Breadcrumbs::add(Breadcrumb::factory()->set_title($this->company->name)->set_url('/companies/update/'.$this->company->id));
                    Breadcrumbs::add(Breadcrumb::factory()->set_title($this->company->name.' '. __('Projects'))->set_url(URL::site('/plans')));
                }
            }
        }

        parent::after();
    }

    public function action_list(){

        $filterBy = $this->request->param('status');
        $sortBy = $this->request->param('sorting');
        $export = $this->request->param('export');

        $query = ORM::factory('Project');
        if($this->_user->getRelevantRole('outspread') != Enum_UserOutspread::General){
            $query->where('client_id','=',$this->_user->client_id);
        }
        if( ! $this->_user->priorityLevelIn(Enum_UserPriorityLevel::Company) AND $this->_user->priorityLevelIn(Enum_UserPriorityLevel::Project)){
            $query = $this->_user->projects;
        }
        if(!empty($filterBy)){
            $query->and_where('status','=',$filterBy);
        }
        if(!empty($sortBy)){
            $query->with('client');
            $query->order_by($sortBy,'ASC');
        }
        $filterProjects = clone($query);
        $filterProjects = $filterProjects->order_by('name','ASC')->find_all();
        $result = $query->findAllWithPagination();
        if(!empty($export)){
            $this->template = null;
            $ws = new Spreadsheet(array(
                'author'       => 'Q4B',
                'title'	       => 'Report',
                'subject'      => 'Subject',
                'description'  => 'Description',
            ));

            $ws->set_active_sheet(0);
            $as = $ws->get_active_sheet();
            $as->setTitle('Report');

            $as->getDefaultStyle()->getFont()->setSize(10);
            $as->getColumnDimension('A')->setWidth(40);
            $as->getColumnDimension('B')->setWidth(40);
            $as->getColumnDimension('C')->setWidth(13);
            $as->getColumnDimension('D')->setWidth(13);
            $as->getColumnDimension('E')->setWidth(250);


            $sh = [
                1 => [__('Project name'),__('Company name'),__('Start Date'),__('End Date'), __('Project Description')],
            ];
            $ws->set_data($sh, false);
            foreach ($result['items'] as $item){
                $sh [] = [$item->name, $item->company->name, date('d/m/Y',$item->start_date), date('d/m/Y',$item->end_date), $item->description];
            }

            $ws->set_data($sh, false);
            $first_letter = PHPExcel_Cell::stringFromColumnIndex(0);
            $last_letter = PHPExcel_Cell::stringFromColumnIndex(count($sh[1])-1);
            $header_range = "{$first_letter}1:{$last_letter}1";
            $ws->get_active_sheet()->getStyle($header_range)->getFont()->setSize(12)->setBold(true);
            $ws->send(['name'=>'report', 'format'=>'Excel5']);
        }else {
            $projectIds = [];
            foreach ($result['items'] as $item){
                $projectIds [] = $item->id;
            }
            $this->template->content = View::make('plans/project-list', $result + ['filterProjects' => $filterProjects]);
        }
    }

    public function action_update(){

        $this->project = ORM::factory('Project',(int)$this->request->param('id'));
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }
        $this->company = $this->project->company;
        if($this->request->method() == HTTP_Request::POST)
        {
            $this->_checkForAjaxOrDie();
            try{
                $data = Arr::extract($this->post(),['name','address','project_id','owner','description','start_date','end_date','status','stage']);
                array_walk($data,function(&$item){
                    $item = trim($item);
                });

                //проверяем и преобразуем даты
                if(empty($data['start_date']) OR empty($data['end_date'])){
                    throw new HDVP_Exception('Dates must not be empty');
                }
                $data['start_date'] = DateTime::createFromFormat('d/m/Y',$data['start_date'])->getTimestamp();
                $data['end_date'] = DateTime::createFromFormat('d/m/Y',$data['end_date'])->getTimestamp();

                if(!$data['start_date'] OR !$data['end_date']){
                    throw new HDVP_Exception('Incorrect date format');
                }

                if($data['start_date'] > $data['end_date']){
                    throw new HDVP_Exception('End date must be a greater than start date');
                }

                if(($this->_user->getRelevantRole('outspread') != Enum_UserOutspread::General) AND $this->_user->client_id != $this->company->client_id){
                    throw new HDVP_Exception('You have no privileges to update project in this company');
                }

                $data['company_id'] = $this->company->id;
                $this->project->values($data);
                Database::instance()->begin();
                $this->project->save();
                $this->project->makeProjectPaths();
                if(!empty($this->files()) AND !empty($this->files()['images'])){
                    foreach ($this->files()['images'] as $key => $image){
                        $uploadedFiles[] = [
                            'name' => str_replace($this->project->imagesPath().DS,'',Upload::save($image,null,$this->project->imagesPath())),
                            'original_name' => $image['name'],
                            'ext' => Model_File::getFileExt($image['name']),
                            'mime' => $image['type'],
                            'path' => str_replace(DOCROOT,'',$this->project->imagesPath()),
                            'token' => md5($image['name']).base_convert(microtime(false), 10, 36),
                        ];
                    }
                }
                if(!empty($uploadedFiles)){
                    foreach ($uploadedFiles as $image){
                        $image = ORM::factory('Image')->values($image)->save();
                        if( ! $this->project->image_id){
                            $this->project->image_id = $image->id;
                            $this->project->save();
                        }
                        $this->project->add('images', $image->pk());
                    }
                }
                Database::instance()->commit();
                Event::instance()->fire('onProjectUpdated',['sender' => $this,'item' => $this->project]);
                Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $this->project]);

            }catch(HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }catch(ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch(Exception $e){
                Database::instance()->rollback();
//                $this->_setErrors('Operation Error');
                $this->_setErrors($e->getMessage());
            }
        }else{
            $this->company = $this->project->company;
            $professionId = [$this->project->company->professions->where('status','=',Enum_Status::Enabled)->order_by('cmpprofession.name','ASC')->find()->id];

            View::set_global('_PROJECT', $this->project);

            $objects = $this->project->objects->order_by('id','DESC')->find_all();
            $objectTypes = ORM::factory('PrObjectType')->find_all()->as_array('id','alias');

            foreach ($objectTypes as $ot){
                $projectObjectsCount[$ot] = 0;
            }

            foreach ($objects as $o){
                $projectObjectsCount[$objectTypes[$o->type_id]]++;
            }

            if($this->_user->getRelevantRole('outspread') != Enum_UserOutspread::General){
                $companies = $this->_user->client->companies->find_all();
            }else{
                $companies = ORM::factory('Company')->find_all();
            }

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->project->name));
            $this->template->content = View::make('plans/update')
                ->set('plansView', View::make('plans/plans/list',
                    $this->_getPlanListPaginatedData($this->project, null, null)
                ));
        }
    }

    public function action_get_images(){
        $this->_checkForAjaxOrDie();
        $id = (int)$this->request->param('id');
        $this->project = ORM::factory('Project',$id);
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }
        View::set_global('_PROJECT',$this->project);
        $this->setResponseData('modal',
            View::make('projects/images/list')
                ->set('images',$this->project->images->find_all())
                ->set('main_image_id',$this->project->image_id)
            );

    }

    public function action_set_image(){
        $this->_checkForAjaxOrDie();
        $id = (int)$this->request->param('param1');
        $imageId = (int)$this->request->param('param2');
        $this->project = ORM::factory('Project',$id);
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }

        $image = $this->project->images->where('id','=',$imageId)->find();
        if( ! $image->loaded()){
            throw new HTTP_Exception_404;
        }

        $this->project->image_id = $image->id;
        $this->project->save();
    }

    public function action_delete_image(){
        $this->_checkForAjaxOrDie();
        $id = (int)$this->request->param('param1');
        $imageId = (int)$this->request->param('param2');
        $this->project = ORM::factory('Project',$id);
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }

        $image = $this->project->images->where('id','=',$imageId)->find();
        if( ! $image->loaded()){
            throw new HTTP_Exception_404;
        }
        $this->project->remove('images',$image->id);
        $image->delete();
    }

    //todo:: Not Used
    public function action_plans_professions_list(){
        $this->_checkForAjaxOrDie();

        $this->project = ORM::factory('Project',(int)$this->request->param('id'));
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }
        $this->company = $this->project->company;
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }

        $professions = [];
        $plans = $this->project->plans->find_all();
        foreach($plans as $plan){
            if( ! in_array($plan->profession_id,$professions)){
                $professions[] = $plan->profession_id;
            }
        }

        View::set_global('_PROJECT', $this->project);
        View::set_global('_COMPANY', $this->company);
        $this->setResponseData('modal',View::make('plans/plans/professions-list',[
            'items' => $this->company->professions->where('status','=',Enum_Status::Enabled)->find_all(),
            'selected' => $professions
        ]));

    }

    public function action_plan_list_search(){
        $projectId = (int)$this->request->param('id');
        $this->project = ORM::factory('Project',$projectId);

        if(!$this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404();
        }

        try{
            $search = base64_decode($this->request->param('search'));
        }catch (Exception $e){
            throw new HTTP_Exception_404();
        }

        if(!empty($search)){
            $preResult = Model_PrPlan::getPreResultItems($this->project->id);
            $srch = new Search($search,$preResult,
                array(
                    //'name' => 0.3,
                    'original_name' => 0.3,
                    'cname' => 0.3,
                    'custom_number' => 0.3,
                ),
                false,
                1);
            if(count(count($srch->result()))){
                foreach ($srch->result() as $item){
                    $planIdis[] = $item['id'];
                }
            }
            if(!empty($planIdis)){
                $planIdis = '('.implode(',',$planIdis).')';
                $query = $this->project->plans->where('id','IN',DB::expr($planIdis));
            }else{
                $query = $this->project->plans->where('project_id','=','not found');
            }
        }else{
            $this->project->plans->where('project_id','=','not found');
        }

        $withFileCount = 0;
        $withoutFileCount = 0;

        if(isset($query)){
            $withFileCount = clone($query);
            $withFileCount = $withFileCount->and_where('prplan.has_file','=',1)->find_all()->count();
            $withoutFileCount = clone($query);
            $withoutFileCount = $withoutFileCount->and_where('prplan.has_file','=',0)->find_all()->count();

        }

        $paginationSettings = [
            'items_per_page' => 15,
            'view'              => 'pagination/project',
            'current_page'      => ['source' => 'route', 'key'    => 'page'],
        ];
        $result = (new ORMPaginate($query,null,$paginationSettings))->getData();

        View::set_global('_PROJECT', $this->project);
        $this->setResponseData('plans',View::make('plans/plans/list',
            [   'items' => $result['items'],
                'pagination' => $result['pagination'],
                'objects' => $this->project->objects->find_all(),
                'professions' => $this->project->company->professions->where('status','=',Enum_Status::Enabled)->order_by('cmpprofession.name','ASC')->find_all(),
                'floorsFilter' => $this->project->getObjectsBiggerAndSmallerFloors(),
                'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192),
                'withFileCount' => $withFileCount,
                'withoutFileCount' => $withoutFileCount,
                'planCount' => $withoutFileCount + $withFileCount,
            ]
        ));
    }

    public function action_plans_list(){
        $projectId = (int)$this->request->param('project_id');
        $objectId = (int)$this->request->param('object_id');
        $professionIds = $this->request->param('professions');
        $withFile = (int)$this->request->param('with_file');
        $floorIds = $this->request->param('floors');
        $this->project = ORM::factory('Project',$projectId);

        if(!$this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404();
        }

        if(!empty($floorIds) OR $floorIds === '0'){
            $floorIds = explode('_',$floorIds);
            if(!is_array($floorIds)){
                $floorIds = [$floorIds];
            }
            array_walk($floorIds,function(&$item){
                $item = (int)$item;
            });
        }

        if(!empty($objectId)){
            $object = $this->project->objects->where('id','=',$objectId)->find();
            if(!$object->loaded()){
                throw new HTTP_Exception_404();
            }
        }

        if(!empty($professionIds)){
            $professionIds = explode('-',$professionIds);
            if(!is_array($professionIds)){
                $professionIds = [$professionIds];
            }
            array_walk($professionIds,function(&$item){
                $item = (int)$item;
            });
        }

        View::set_global('_PROJECT', $this->project);

        $this->setResponseData('plans',View::make('plans/plans/list',
            $this->_getPlanListPaginatedData($this->project, isset($object) ? $object : null, !empty($professionIds) ? $professionIds : null, !empty($floorIds) ? $floorIds : null, null, !empty($withFile) ? $withFile : null)
        ));
    }

    public function action_plan_delete(){
        $projectId = (int)$this->request->param('param1');
        $planId = (int)$this->request->param('param2');

        $this->project = ORM::factory('Project',$projectId);
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)) throw new HTTP_Exception_404;
        $plan = $this->project->plans->where('id','=',$planId)->find();
        if( ! $plan->loaded()) throw new HTTP_Exception_404;
        Event::instance()->fire('beforePlanDelete',['sender' => $this,'item' => $plan]);
        try{
            if($plan->hasQualityControl()){
                throw new HDVP_Exception('Cant delete the Plan, because it contained in Quality Control');
            }
            $plans = ORM::factory('PrPlan')->where('scope','=',$plan->scope)->order_by('id','Desc')->find_all();
            foreach ($plans as $p){
                if($p->hasQualityControl()){
                    continue;
                }
                $p->delete();
            }
        }catch (HDVP_Exception $e){
            $this->_setErrors($e->getMessage());
        }
    }

    public function action_plans_delete(){
        $projectId = (int)$this->request->param('id');
        $plansIds = Arr::get($this->post(), 'plans');
        $this->project = ORM::factory('Project',$projectId);
        $professionId = Arr::get($this->post(), 'professionId');
        $objectId = Arr::get($this->post(), 'objectId');
        $object = $this->project->objects->where('id','=',$objectId)->find();

        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)) throw new HTTP_Exception_404;
        $plans = ORM::factory('PrPlan')->where('id','IN', $plansIds)->find_all();
        foreach ($plans as $plan) {
            if( ! $plan->loaded()) throw new HTTP_Exception_404;
            Event::instance()->fire('beforePlanDelete',['sender' => $this,'item' => $plan]);
            try{
                if($plan->hasQualityControl()){
                    throw new HDVP_Exception('Cant delete the Plan, because it contained in Quality Control');
                }
                foreach ($plans as $p){
                    if($p->hasQualityControl()){
                        continue;
                    }
                    $p->delete();
                }

                $this->setResponseData('html', View::make('plans/update')
                    ->set('plansView', View::make('plans/plans/list',
                        $this->_getPlanListPaginatedData($this->project, $objectId ? $object : null, $professionId ? [$professionId] : null)
                    )));
            }catch (HDVP_Exception $e){
                $this->_setErrors($e->getMessage());
            }
        }
    }

    public function action_update_plan_list(){
        if(($this->request->method() != HTTP_Request::POST) OR !(int)$this->request->param('id')){
            throw new HTTP_Exception_404;
        }
        $this->_checkForAjaxOrDie();
        $this->project = ORM::factory('Project',$this->getUIntParamOrDie($this->request->param('id')));
        if( ! $this->project->loaded() OR empty($this->post()) OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }
        
        $plansData = [];
        foreach ($this->post() as $key => $value){
            if(preg_match('~plan_(?<isNew>\+)?(?<id>[0-9]+)_(?<field>[a-z_]+)~',$key,$matches))
                if($matches['isNew']){
                    $plansData['new_'.$matches['id']][$matches['field']] = $value;
                }else{
                    $plansData[$matches['id']][$matches['field']] = $value;
                }
        }

        //удаляем явно не валидные новые профессии
        foreach ($plansData as $key => $val){
            if(!trim($val['name']) AND !is_numeric($key)){
                unset($plansData[$key]);
            }
        }

        $plansFilesData = [];
        foreach ($_FILES as $key => $value){
            if(preg_match('~plan_(?<isNew>\+)?(?<id>[0-9]+)_(?<field>[a-z_]+)~',$key,$fileMatches))
            {
                if($fileMatches['isNew']){
                    $plansFilesData['new_'.$fileMatches['id']][$fileMatches['field']] = $value;
                }else{
                    $plansFilesData[$fileMatches['id']][$fileMatches['field']] = $value;
                }
            }
        }

        //удаляем явно не валидные
        foreach ($plansFilesData as $key => $val){
            if(empty($val['file']) AND !is_numeric($key)){
                unset($plansFilesData[$key]);
            }
        }

        if($this->project->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->project->id,192)){
            $this->_setErrors('Invalid request');
        }
        else if(empty($plansData)){
            $this->_setErrors('No changes detect for update');
        }
        else{
            $plansArr['added'] = $plansArr['edited'] = $placeErrors = [];
            try{
                Database::instance()->begin();
                $objects = [];

                foreach($plansData as $pid => $c){
                    $plan = $this->project->plans->where('id','=',$this->getUIntParamOrDie($pid))->find();
                    if(! $plan->loaded()){
                        throw new HDVP_Exception('Incorrect plan identifier');
                    }

                    if($plan->hasQualityControl()) continue;

                    if(isset($plansFilesData[$this->getUIntParamOrDie($pid)])){
                        $fileInfo = $plansFilesData[$this->getUIntParamOrDie($pid)]['file'];

                        $this->project->makeProjectPaths();

                        $fileData = [
                            'name' => str_replace($this->project->plansPath().DS,'',Upload::save($fileInfo,null,$this->project->plansPath())),
                            'original_name' => $fileInfo['name'],
                            'ext' => Model_File::getFileExt($fileInfo['name']),
                            'mime' => $fileInfo['type'],
                            'path' => str_replace(DOCROOT,'',$this->project->plansPath()),
                            'token' => md5($fileInfo['name']).base_convert(microtime(false), 10, 36),
                        ];
                        $file = ORM::factory('PlanFile')->values($fileData)->save();
                        $plan->add('files', $file->pk());
//                        Event::instance()->fire('onPlanFileAdded',['sender' => $this,'item' => $file]);
                        $fs = new FileServer();
                        if(strtolower($file->ext) == 'pdf' OR strpos('.pdf',$file->name)) {
                            $fs->addLazyPdfTask(
                                'https://qforb.net/' . $file->path . '/' . $file->name,
                                'https://qforb.net/fileserver/planaddcallback?planId=' . $plan->id . '&fileId=' . $file->id,
                                1,
                                1
                            );
                        }else{
                            $fs->addLazySimpleImageTask('https://qforb.net/' . $file->path . '/' . $file->name,$file->id);
                        }
                        $plan->has_file = 1;
                    }

                    $planFile = $plan->file();

//                    if($planFile->loaded()){ // todo:: Understand WTF ?
//                        $planFile->customName(Arr::get($c,'name'));
//
//                        $planFile->sheet_number = Arr::get($c,'sheet_number');
//                        $planFile->save();
//                    }else{
                        $plan->sheet_number = Arr::get($c,'sheet_number');
                        $plan->name = Arr::get($c,'name');
//                    }

                    $plan->delivered_at = Arr::get($c,'delivered_at');
                    $plan->received_at = Arr::get($c,'received_at');

                    $plan->date = DateTime::createFromFormat('d/m/Y',Arr::get($c,'date'))->getTimestamp();

                    if(Arr::get($c,'edition') !== ''){
                        $plan->edition = Arr::get($c,'edition');
                    }

                    if(!empty($c['place_type'])){
                        $place = ORM::factory('PrPlace',['object_id' => $plan->object_id,'object_id','custom_number' => $c['custom_number'], 'type' => $c['place_type']]);
                        if( ! $place->loaded()){
                            //throw new HDVP_Exception('Incorrect place data');
                            $placeErrors[] = $pid;
                        }else{
                            $plan->place_id = $place->id;
                        }
                    }

                    $plan->save();

                    if(isset($c['floors'])){
                        $plan->remove('floors');
                        if(!$plan->place_id){
                            if(!empty($c['floors']) OR $c['floors'] == '0'){
                                if(isset($objects[$plan->object_id])){
                                    $object = $objects[$plan->object_id];
                                }else{
                                    $object = ORM::factory('PrObject',$plan->object_id);
                                    $objects[$object->id] = $object;
                                }
                                if(!is_array($c['floors'])){
                                    $c['floors'] = [$c['floors']];
                                }
                                $floors = $object->floors->where('number','IN',DB::expr('('.implode(',',$c['floors']).')'))->find_all();
                                if(count($floors) != count($c['floors'])){
                                    throw new HDVP_Exception('Incorrect floor numbers');
                                }
                                foreach ($floors as $floor){
                                    $plan->add('floors',$floor);
                                }
                            }
                        }
                        
                    }

                    // Disabled on Plans LIST
//                    if(!empty($c['crafts']) OR $c['crafts'] == '0'){
//                        if(empty($this->company)){
//                            $this->company = $this->project->company;
//                        }
//                        $plan->remove('crafts');
//                        if(!is_array($c['crafts'])){
//                            $c['crafts'] = [$c['crafts']];
//                        }
//
//                        $crafts = $this->company->crafts->where('id','IN',DB::expr('('.implode(',',$c['crafts']).')'))->find_all();
//
//                        if(count($crafts) != count($c['crafts'])){
//                            throw new HDVP_Exception('Incorrect crafts');
//                        }
//                        foreach ($crafts as $craft){
//                            $plan->add('crafts',$craft);
//                        }
//                    }

                    $plansArr[] = $plan;
                }

                //выстреливаем события
                foreach ($plansArr as $item){
                    Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $item]);
                }
                $this->setResponseData('triggerEvent','planListUpdated');
                Database::instance()->commit();
                if($fs instanceof FileServer){
                    $fs->sendLazyTasks();
                }
            }catch(ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch(HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }catch (Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }finally{
                if(!empty($placeErrors)){
                    $this->setResponseData('placeErrorsList',$placeErrors);
                    $this->setResponseData('triggerEvent','placeError');
                }
            }
        }
    }

    protected function _getPlanListPaginatedData($project,$object = null, array $professions = null, array $floors = null, $place_custom_number = null, $withFile = null){
        $query = $project->plans;

        if(!empty($floors)){
            $query
                ->join(['pr_floors_pr_plans','pfpp'])
                ->on('prplan.id','=','pfpp.plan_id')
                ->join(['pr_floors','pf'])
                ->on('pfpp.floor_id','=','pf.id')
                ->and_where('pf.number','IN',DB::expr('('.implode(',',$floors).')'));
        }

        if(!empty($place_custom_number)){
            $query
                ->join(['pr_places','ppl'])
                ->on('prplan.place_id','=','ppl.id')
                ->and_where('ppl.custom_number','=',$place_custom_number);
        }

        $query->where('prplan.id','IN',DB::expr(' (SELECT max(pp.id) id FROM pr_plans pp WHERE pp.project_id='.$project->id.' GROUP BY pp.scope ORDER BY pp.id DESC)'));

        if(!empty($object)){
            $query->and_where('prplan.object_id','=',$object->id);
        }

        if(!empty($professions)){
            $query->and_where('profession_id','IN',DB::expr('('.implode(',',$professions).')'));
        }

        $withFileCount = clone($query);
        $withFileCount = $withFileCount->and_where('prplan.has_file','=',1)->find_all()->count();
        $withoutFileCount = clone($query);
        $withoutFileCount = $withoutFileCount->and_where('prplan.has_file','=',0)->find_all()->count();

        if(!empty($withFile)){
            if($withFile == 1){
//                $query
//                    ->join(['pr_plans_files','ppf'])
//                    ->on('prplan.id','=','ppf.plan_id')
//                    ->group_by('prplan.id');
                $query->and_where('prplan.has_file','=',1);
            }else{
//                $query
//                    ->join(['pr_plans_files','ppf'], 'left outer')
//                    ->on('prplan.id','=','ppf.plan_id')
//                    ->group_by('prplan.id')
//                    ->where('ppf.plan_id', '=', null);
                $query->and_where('prplan.has_file','=',0);
            }
        }

        $query->order_by('created_at','DESC');

        $paginationSettings = [
            'items_per_page' => 30,
            'view'              => 'pagination/project',
            'current_page'      => ['source' => 'route', 'key'    => 'page'],
        ];

        // DISTINCT NOT WORKING IF HAVE A 2 GROUP BY
        if(empty($withFile)){
            $query->distinct(true);
        }

        $result = (new ORMPaginate($query,null,$paginationSettings))->getData();

        return [
            'items' => $result['items'],
            'pagination' => $result['pagination'],
            'objects' => $this->project->objects->find_all(),
            'professions' => $this->project->company->professions->where('status','=',Enum_Status::Enabled)->order_by('cmpprofession.name','ASC')->find_all(),
            'floorsFilter' => $this->project->getObjectsBiggerAndSmallerFloors(),
            'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$project->id,192),
            'withFileCount' => $withFileCount,
            'withoutFileCount' => $withoutFileCount,
            'planCount' => $withoutFileCount + $withFileCount,
        ];
    }

    public function action_create_plan(){
        $this->_checkForAjaxOrDie();

        $this->project = ORM::factory('Project',(int)$this->request->param('id'));
        $object = ORM::factory('PrObject',(int)$this->request->param('object_id'));
        $professionId = (int)$this->request->param('profession_id');

        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }

        $this->company = $this->project->company;
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }

        View::set_global('_PROJECT', $this->project);
        View::set_global('_COMPANY', $this->company);
        if($this->request->method() == Request::POST){
            $data = Arr::extract($this->post(),['profession_id','project_id','company_id']);

            $plansData = [];
            foreach ($this->post() as $key => $value){
                if(preg_match('~plan_(?<isNew>\+)?(?<id>[0-9]+)_(?<field>[a-z_]+)~',$key,$matches)){
                    if($matches['isNew']){
                        $plansData['new_'.$matches['id']][$matches['field']] = $value;
                    }else{
                        $plansData[$matches['id']][$matches['field']] = $value;
                    }
                }
            }

            //удаляем явно не валидные новые профессии
            foreach ($plansData as $key => $val){
                if(!trim($val['name']) AND !is_numeric($key)){
                    unset($plansData[$key]);
                }
            }

            try{
                if($data['project_id'] != $this->project->id OR $data['company_id'] != $this->company->id){
                    throw new HDVP_Exception('Invalid data');
                }

                $this->project->makeProjectPaths();
                Database::instance()->begin();

                foreach($plansData as $pid => $c) {
                    $data['object_id'] = Arr::get($c, 'structure');
                    $data['date'] = time();//DateTime::createFromFormat('d/m/Y',$data['date'])->getTimestamp();
                    $plan = ORM::factory('PrPlan')->values($data);
                    $plan->scope = Model_PrPlan::getNewScope();
                    $plan->project_id = $this->project->id;
                    $plan->sheet_number = Arr::get($c,'sheet');
                    $plan->name = Arr::get($c,'name');
                    $plan->edition = 1; // todo:: WTF ???
                    $plan->save();
                    $object = ORM::factory('PrObject',$plan->object_id);

                    if(is_string($c['floors'])){
                        $c['floors'] = json_decode($c['floors']);
                    }

                    $dataFloors = $c['floors'];

                    $floors = $object->floors->where('number','IN',DB::expr('('.implode(',',$dataFloors).')'))->find_all();
                    if(count($floors) != count($c['floors'])){
                        throw new HDVP_Exception('Incorrect floor numbers');
                    }
                    $plan->remove('floors');
                    foreach ($floors as $floor){
                        $plan->add('floors',$floor);
                    }

                    $this->setResponseData('triggerEvent','projectPlanCreated');
                    $this->setResponseData('id',$plan->id);
                    Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $plan]);
                }

                Database::instance()->commit();
            }catch (ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch (HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }catch (Exception $e){
                Database::instance()->rollback();
                $this->_setErrors('Operation Error');
            }

        }else{
            $this->setResponseData('modal',View::make('plans/plans/create',[
                'professions' => $this->company->professions->where('status','=',Enum_Status::Enabled)->order_by('cmpprofession.name','ASC')->find_all(),
                'objects' => $this->project->objects->find_all(),
                'project' => ['id' => $this->project->id, 'name' => $this->project->name],
                'company' => ['id' => $this->company->id, 'name' => $this->company->name],
                'date' => date('d/m/Y H:i'),
                'action' => URL::site('plans/create_plan/'.$this->project->id),
                'object' => $object,
                'professionId' => $professionId,
            ]));
        }
    }

    /**
     * @throws HTTP_Exception_404
     * @throws Kohana_Exception
     */
    public function action_update_plan(){
        $this->_checkForAjaxOrDie();
        $this->project = ORM::factory('Project',(int)$this->request->param('param1'));
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }
        $this->company = $this->project->company;
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }
        $plan = ORM::factory('PrPlan',(int)$this->request->param('param2'));
        if( ! $plan->loaded()){
            throw new HTTP_Exception_404;
        }

        View::set_global('_PROJECT', $this->project);
        View::set_global('_COMPANY', $this->company);
        if($this->request->method() == Request::POST){
            try{
                Database::instance()->begin();
                $data = Arr::extract($this->post(),['name','edition','description','object_id','date','profession_id','scale','status','sheet_number']);
                $data['place_id'] = 0;
                if(Arr::get($this->post(),'place_number')){
                    $placeData = Arr::extract($this->post(),['place_number']);
                    $placeData['place_number'] = (int)$placeData['place_number'];
                    if(!$placeData['place_number']){
                        throw new HDVP_Exception('Incorrect data set');//todo:: заблокировать пользователя
                    }
                    $place = ORM::factory('PrPlace',['object_id' => Arr::get($this->post(),'object_id'),'id' => $placeData['place_number']]);
                    if( ! $place->loaded()){
                        throw new HDVP_Exception('Incorrect place data');
                    }
                    $data['place_id'] = $place->id;
                    unset($placeData);
                }
                $data['date'] = DateTime::createFromFormat('d/m/Y',$data['date'])->getTimestamp();
                $plan->values($data);
                $plan->project_id = $this->project->id;
                $plan->save();
                $f = $plan->file();
                if($f->loaded()){
                    $f->customName($data['name']);

                    $f->save();
                }
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
                        throw new HDVP_Exception('Incorrect floor numbers');
                    }
                    $plan->remove('floors');
                    foreach ($floors as $floor){
                        $plan->add('floors',$floor);
                    }

                }else{
                    $plan->remove('floors');
                }
                $this->project->makeProjectPaths();
                if(!empty($this->files()) AND !empty($this->files()['images'])){
                    foreach ($this->files()['images'] as $key => $file){
                        $uploadedFiles[] = [
                            'name' => str_replace($this->project->plansPath().DS,'',Upload::save($file,null,$this->project->plansPath())),
                            'original_name' => $file['name'],
                            'ext' => Model_File::getFileExt($file['name']),
                            'mime' => $file['type'],
                            'path' => str_replace(DOCROOT,'',$this->project->plansPath()),
                            'token' => md5($file['name']).base_convert(microtime(false), 10, 36),
                        ];
                    }
                }
                if(!empty($uploadedFiles)){
                    foreach ($uploadedFiles as $file){
                        $file = ORM::factory('PlanExtraFile')->values($file)->save();
                        $plan->add('extra_files', $file->pk());
                        Event::instance()->fire('onPlanExtraFileAdded',['sender' => $this,'item' => $file]);
                    }
                }
                $plan->remove('crafts');
                if(!empty($this->post()['crafts'])){
                    $plan->add('crafts',$this->post()['crafts']);
                }
                Database::instance()->commit();

                $query = clone($this->project->plans);
                $withFileCount = clone($query);
                $withFileCount = $withFileCount->and_where('prplan.has_file','=',1)->find_all()->count();
                $withoutFileCount = clone($query);
                $withoutFileCount = $withoutFileCount->and_where('prplan.has_file','=',0)->find_all()->count();

                $this->setResponseData('projectPlansForm',View::make('plans/plans/list',
                    [   'items' => $this->project->plans->order_by('created_at','Desc')->find_all(),
                        'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192),
                        'withFileCount' => $withFileCount,
                        'withoutFileCount' => $withoutFileCount,
                        'planCount' => $withoutFileCount + $withFileCount,
                    ]));
                $this->setResponseData('triggerEvent','projectPlansUpdated');
                Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $plan]);
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
            $object = $this->project->objects->where('id','=',$plan->object_id)->find();
            $places = $object->places->find_all();

            $trackingItems = $plan->getScopeEditionsTrackings();

            $this->setResponseData('modal',View::make('plans/plans/update_new',[
                'professions' => $this->company->professions->where('status','=',Enum_Status::Enabled)->with('crafts')->find_all(),
                'action' => URL::site('plans/update_plan/'.$this->project->id.'/'.$plan->id),
                'item' => $plan,
                'places' => $places,
                'trackingItems' => $trackingItems,
//                'trackingItems' => $plan->trackings->order_by('created_at','DESC')->find_all(),
            ]));
        }
    }

    public function action_add_edition(){
        $this->_checkForAjaxOrDie();
        $this->project = ORM::factory('Project',(int)$this->request->param('param1'));
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }
        $this->company = $this->project->company;
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }
        $plan = ORM::factory('PrPlan',(int)$this->request->param('param2'));

        View::set_global('_PROJECT', $this->project);
        View::set_global('_COMPANY', $this->company);
        if($this->request->method() == Request::POST){
            if(empty($this->files()) OR empty($this->files()['file'])){
                throw new HTTP_Exception_404;
            }
            try{
                Database::instance()->begin();
                $data = Arr::extract($this->post(),['edition','description','date','scale','status']);
                $data['date'] = DateTime::createFromFormat('d/m/Y',$data['date'])->getTimestamp();
                $newPlan = ORM::factory('PrPlan')->values($data);
                $newPlan->name = $plan->name;
                $newPlan->sheet_number = $plan->sheet_number;
                $newPlan->place_id = $plan->place_id;
                $newPlan->scope = $plan->scope;
                $newPlan->project_id = $plan->project_id;
                $newPlan->object_id = $plan->object_id;
                $newPlan->profession_id = $plan->profession_id;
                $newPlan->save();
                if( ! $newPlan->place_id){
                    $object = ORM::factory('PrObject',$newPlan->object_id);
                    $floors = $plan->floors->find_all();
                    foreach ($floors as $floor){
                        $newPlan->add('floors',$floor);
                    }

                }
                foreach ($plan->crafts->find_all() as $craft){
                    $newPlan->add('crafts',$craft);
                }
                $this->project->makeProjectPaths();
                if(!empty($this->files()) AND !empty($this->files()['file'])){
                    $fileData = $_FILES['file'];
                    $uploadedFiles[] = [
                        'name' => str_replace($this->project->plansPath().DS,'',Upload::save($fileData,null,$this->project->plansPath())),
                        'original_name' => $fileData['name'],
                        'ext' => Model_File::getFileExt($fileData['name']),
                        'mime' => $fileData['type'],
                        'path' => str_replace(DOCROOT,'',$this->project->plansPath()),
                        'token' => md5($fileData['name']).base_convert(microtime(false), 10, 36),
                    ];
                }
                if(!empty($uploadedFiles)){
                    foreach ($uploadedFiles as $file){
                        $PlanFile = ORM::factory('PlanFile')->values($file)->save();
                        $newPlan->add('files', $PlanFile->pk());
//                        Event::instance()->fire('onPlanFileAdded',['sender' => $this,'item' => $PlanFile]);
                        $fs = new FileServer();
                        if(strtolower($file->ext) == 'pdf' OR strpos('.pdf',$file->name)) {
                            $fs->addLazyPdfTask(
                                'https://qforb.net/' . $PlanFile->path . '/' . $PlanFile->name,
                                'https://qforb.net/fileserver/planaddcallback?planId=' . $plan->id . '&fileId=' . $PlanFile->id,
                                1,
                                1
                            );
                        }else{
                            $fs->addLazySimpleImageTask('https://qforb.net/' . $PlanFile->path . '/' . $PlanFile->name,$PlanFile->id);
                        }
                        $newPlan->has_file = 1;
                        $newPlan->save();
                    }
                    $f = $plan->file(); // todo:: WTF ?
                    if($f->hasCustomName()){
                        $PlanFile->customName($f->customName());
                    }
                }
                Database::instance()->commit();
                $fs->sendLazyTasks();
            }catch (ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch (HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }catch (Exception $e){
                Database::instance()->rollback();
                $this->_setErrors('Operation Error');
            }

            $this->setResponseData('projectPlansForm',View::make('plans/plans/list',
                $this->_getPlanListPaginatedData($this->project)));
            $this->setResponseData('triggerEvent','projectPlansUpdated');
            Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $plan]);
        }else{
            $this->setResponseData('modal',View::make('plans/plans/add-edition',[
                'professions' => $this->company->professions->where('status','=',Enum_Status::Enabled)->with('crafts')->find_all(),
                'action' => URL::site('plans/add_edition/'.$this->project->id.'/'.$plan->id),
                'item' => $plan,
                'historyItems' => ORM::factory('PrPlan')->where('scope','=',$plan->scope)->and_where('id','<>',$plan->id)->order_by('created_at','DESC')->find_all(),

            ]));
        }
    }

    public function action_copy_plan(){
        $this->_checkForAjaxOrDie();

        $projectId = (int) $this->request->param('project_id');
        $objectId = (int) $this->request->param('object_id');
        $this->project = ORM::factory('Project',$projectId);

        $projects = ORM::factory('Project');
        if($this->_user->getRelevantRole('outspread') != Enum_UserOutspread::General){
            $projects->where('client_id','=',$this->_user->client_id);
        }
        if( ! $this->_user->priorityLevelIn(Enum_UserPriorityLevel::Company) AND $this->_user->priorityLevelIn(Enum_UserPriorityLevel::Project)){
            $projects = $this->_user->projects;
        }

        $projects = $projects->order_by('name','ASC')->find_all();

        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }
        $this->company = $this->project->company;
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }

        $professions = [];
        $plans = $this->project->plans->where('object_id', '=', $objectId)->find_all();

        foreach($plans as $plan){
            if( ! in_array($plan->profession_id,$professions)){
                $professions[$plan->profession_id] = $plan->getProfession()->name;
            }
        }

        View::set_global('_PROJECT', $this->project);
        View::set_global('_COMPANY', $this->company);

        if($this->request->method() == Request::POST){
            $copyToProjectId = (int)Arr::get($this->post(),'project_id') ? (int)Arr::get($this->post(),'project_id') : (int)$projectId;
            $copyToObjectId = (int)Arr::get($this->post(),'object_id') ? (int)Arr::get($this->post(),'object_id') : (int)$objectId;

            $copyToProfessions = Arr::get($this->post(),'professions');
            $selectedPlans = Arr::get($this->post(),'selected_plans');
            $copyToProject = ORM::factory('Project', $copyToProjectId);
            $copyToObject = $copyToProject->objects->where('id', '=', $copyToObjectId)->find();

            if(! $copyToObject->loaded()){
                throw new HTTP_Exception_404;
            }

            try{
                Database::instance()->begin();

                if(! is_array($copyToProfessions)){
                    $copyToProfessions = [$copyToProfessions];
                }

                $professionsIds = '('.implode(',',$copyToProfessions).')';
                $q = $this->project->plans;
                if (Arr::get($this->post(),'professions')) {
                    $q = $q->where('profession_id', 'IN', DB::expr($professionsIds));
                }
                if ($objectId) {
                    $q = $q->where('object_id', '=', $objectId);
                }
                if ($this->project->id) {
                    if (!$selectedPlans) {
                        $q = $q->where('prplan.id','IN',DB::expr(' (SELECT max(pp.id) id FROM pr_plans pp WHERE pp.project_id='.$this->project->id.' GROUP BY pp.scope ORDER BY pp.id DESC)'));
                    } else {
                        $q = $q->where('prplan.id','IN',DB::expr(' (SELECT max(pp.id) id FROM pr_plans pp WHERE pp.project_id='.$this->project->id.' AND pp.id IN ('.$selectedPlans.') GROUP BY pp.scope ORDER BY pp.id DESC)'));
                    }
                }

                $plansToCopy = $q->find_all();
//                $copyToPlans = $this->project
//                    ->plans
//                    ->where('profession_id', 'IN', DB::expr($professionsIds))
//                    ->where('object_id', '=', $objectId)
//                    ->where('prplan.id','IN',DB::expr(' (SELECT max(pp.id) id FROM pr_plans pp WHERE pp.project_id='.$this->project->id.' GROUP BY pp.scope ORDER BY pp.id DESC)'))
//                    ->where('prplan.id', 'IN', $copyToSelectedPlans)
//                    ->find_all();


                foreach ($plansToCopy as $planToCopy) {
                    if ($selectedPlans && !(int)Arr::get($this->post(),'project_id') && !(int)Arr::get($this->post(),'object_id')) {
                        $planToCopy->name .= ' (copy)';
                    }
                    $planToCopy->cloneIntoObject(clone $copyToObject);
                }

                Database::instance()->commit();
                
                $this->setResponseData('projectPlansForm',View::make('plans/plans/list',
                    $this->_getPlanListPaginatedData($this->project, isset($object) ? $object : null, !empty($professionIds) ? $professionIds : null)
                    ));
                $this->setResponseData('triggerEvent','projectPlansUpdated');
                
                Event::instance()->fire('onPlanCopy',['sender' => $this,'item' => $plan]);
            }catch (Exception $e){
                Database::instance()->rollback();
                throw $e;
            }

        }else{
            $this->setResponseData('modal',View::make('plans/plans/copy-modal',[
                'professions' => $professions,
                'projects' => $projects,
                'objects' => $this->project->objects->find_all(),
                'action' => URL::site('plans/copy_plan/'. $projectId .'/object_id/'. $objectId)
            ]));
        }
    }

    public function action_project_objects(){
        $this->_checkForAjaxOrDie();

        $projectId = (int) $this->request->param('project_id');
        $this->project = ORM::factory('Project',$projectId);

        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }

        $objects = $this->project->objects->find_all();

        View::set_global('_PROJECT', $this->project);

        $this->setResponseData('objects',View::make('plans/plans/project-objects-select-options',[
            'objects' => $objects,
        ]));
    }

    /**
     * todo:: Can't find where is used
     *
     * @throws HTTP_Exception_404
     * @throws Kohana_Exception
     */
    public function action_plan_history(){
        $this->_checkForAjaxOrDie();
        $this->project = ORM::factory('Project',(int)$this->request->param('param1'));
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }
        $this->company = $this->project->company;
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }
        View::set_global('_PROJECT', $this->project);
        View::set_global('_COMPANY', $this->company);
        $plan = ORM::factory('PrPlan',(int)$this->request->param('param2'));
        $items = ORM::factory('PrPlan')->where('scope','=',$plan->scope)->find_all();
        $this->setResponseData('modal',View::make('plans/plans/history',[
            'plan' => $plan,
            'items' => $items,
        ]));
    }

    public function action_delete_plans_file(){
        $this->_checkForAjaxOrDie();
        $token = trim($this->request->param('token'));
        $projectId = (int) $this->request->param('param1');
        $planId = (int) $this->request->param('param2');
        $plan = ORM::factory('PrPlan',['id' => $planId, 'project_id' => $projectId]);

        if( ! $plan->loaded()){
            throw new HTTP_Exception_404;
        }
        $file = $plan->extra_files->where('token','=',$token)->find();

        if( ! $file->loaded() OR !file_exists($file->path.'/'.$file->name) OR !is_file($file->path.'/'.$file->name)){
            throw new HTTP_Exception_404;
        }
        $file->status = Enum_FileStatus::Deleted;
        $file->save();
        Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $plan]);
    }

    public function action_plans_printed(){
        $projectId = $this->getUIntParamOrDie($this->request->param('id'));
        $this->project = ORM::factory('Project',$projectId);
        $plans = $this->getNormalizedPostArr('plans');

        if(!empty($plans)){
            $plans = array_keys($plans);
        }

        try{
            if(!$this->project->loaded() OR !$this->_user->canUseProject($this->project)) {
                throw new HDVP_Exception('Access Denied');
            }
            Database::instance()->begin();
            if(!empty($plans)){
                $tracking = ORM::factory('PlanTracking');
                $tracking->project_id = $this->project->id;
                $tracking->save();
                $tracking->add('plans',$plans);
                $this->setResponseData('id',$tracking->id);
            }else{
                throw new HDVP_Exception('Plans can not be empty');
            }
            Database::instance()->commit();
        }catch (ORM_Validation_Exception $e){
            Database::instance()->rollback();
            $this->_setErrors($e->errors('validation'));
        }catch (HDVP_Exception $e){
            Database::instance()->rollback();
            $this->_setErrors($e->getMessage());
        }catch (Exception $e){
            Database::instance()->rollback();
            $this->_setErrors('Operation Error');
        }
    }

    public function action_plans_mailing(){
        $projectId = $this->getUIntParamOrDie($this->request->param('id'));
        $this->project = ORM::factory('Project',$projectId);
        if(!$this->project->loaded() OR !$this->_user->canUseProject($this->project)) {
            throw new HDVP_Exception('Access Denied');
        }
        if($this->request->method() == Request::POST){
            if($this->project->id != AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->project->id,192)){
                $this->_setErrors('Invalid request');
            }
            $plans = $this->getNormalizedPostArr('plans');
            if(!empty($plans)){
                $plans = array_keys($plans);
            }else{
                return;
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
                    Queue::enqueue('mailing','Job_Plan_SendPlansEmail',[
                        'emails' => $emailsList,
                        'item' => $this->project->id,
                        'message' => trim(strip_tags(Arr::get($this->post(),'message'))),
                        'view' => 'emails/projects/plans',
                        'user' => ['name' => $this->_user->name, 'email' => $this->_user->email],
                        'plans' => $plans,
                        'lang' => Language::getCurrent()->iso2,
                    ],\Carbon\Carbon::now()->addSeconds(30)->timestamp);
                }
            }
        }else{
            $autocompleteMailiList = [];
            foreach ($this->project->company->users->find_all() as $usr){
                $autocompleteMailiList[$usr->email] = $usr->email;
            }

            $role = ORM::factory('Role',['outspread' => Enum_RoleOutspread::Super]);
            foreach ($role->users->find_all() as $usr){
                $autocompleteMailiList[$usr->email] = $usr->email;
            }
            $this->setResponseData('modal',
                View::make('plans/plans/mailing',[
                    'items' => $this->project->users->find_all(),
                    'autocompleteMailList' => $autocompleteMailiList,
                    'secure_tkn' => AesCtr::encrypt($this->project->id,$this->project->id,192)]));
        }
    }

    public function action_tracking_list(){
        $this->_checkForAjaxOrDie();
        $projectId = $this->getUIntParamOrDie($this->request->param('id'));
        $this->project = ORM::factory('Project',$projectId);
        if( !$this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }
        try{
            $search = base64_decode($this->request->param('search'));
        }catch (Exception $e){
            throw new HTTP_Exception_404();
        }

        $filter = $this->request->param('filter','received_date');
        $profession = $this->getUIntParamOrDie($this->request->param('profession'));
        $from = $this->request->param('from',date('Y-m-d',time() - 86400 * 14));
        $to = $this->request->param('to',date('Y-m-d'));
        $trackIdis = [];
        if(!empty($search)){
            $preResult = Model_PlanTracking::getPreResultItems($this->project->id,$profession);
            $srch = new Search($search,$preResult,
                array(
                    'id' => 0.3,
                    'plan_names' => 0.3,
                    'recipient' => 0.3,
                ),
                true,
                1);
            if(count(count($srch->result()))){
                foreach ($srch->result() as $item){
                    $trackIdis[] = $item['id'];
                }
            }
            if(!empty($trackIdis)){
                $trackIdis = '('.implode(',',$trackIdis).')';
                $query = ORM::factory('PlanTracking')->where('project_id','=',$projectId)->and_where('id','IN',DB::expr($trackIdis));
            }else{
                $query = ORM::factory('PlanTracking')->where('project_id','=','not found');
            }
        }else{
            $query = ORM::factory('PlanTracking')->where('plantracking.project_id','=',$this->project->id)
            ->join(['plans_trackings','pt'])
            ->on('plantracking.id','=','pt.tracking_id')
            ->join(['pr_plans','pp'])
            ->on('pt.plan_id','=','pp.id');
        }
        if(empty($search) AND $profession){
            $query->and_where('pp.profession_id','=',$profession);
        }
        if($from){
            $query->and_where('plantracking.'.$filter,'>=',strtotime($from));
        }
        if($to){
            $query->and_where('plantracking.'.$filter,'<=',strtotime($to) + Date::DAY);
        }
        //->group_by('plantracking.id')
        $query = $query->order_by('plantracking.created_at','DESC')->distinct(true);
        $result = (new ORMPaginate($query))->getData();
        View::set_global('_PROJECT',$this->project);
        $this->setResponseData('html',View::make('plans/plans/date-tracking',$result));

        $this->auto_render = false;
        //var_dump(count($result['items']));

    }

    /**
     * todo:: Can't find where is used
     *
     * @throws HTTP_Exception_404
     * @throws Kohana_Exception
     */
    public function action_plan_tracking(){
        $this->_checkForAjaxOrDie();
        $projectId = $this->getUIntParamOrDie($this->request->param('param1'));
        $planId = $this->getUIntParamOrDie($this->request->param('param2'));
        $this->project = ORM::factory('Project',$projectId);
        if( !$this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }
        $plan = ORM::factory('PrPlan',$planId);
        if( ! $plan->loaded()){
            throw new HTTP_Exception_404;
        }
        $trackingInfo = $plan->tracking->order_by('created','DESC')->find_all();
        $this->setResponseData('tracking',View::make('plans/plans/tracking',[
            'items' => $trackingInfo
        ]));
    }

    public function action_update_tracking(){
        $this->_checkForAjaxOrDie();
        $trackingId = $this->getUIntParamOrDie($this->request->param('id'));
        $tracking = ORM::factory('PlanTracking',$trackingId);
        if( ! $tracking->loaded()){
            throw new HTTP_Exception_404();
        }
        $this->project = $tracking->project;
        if($this->request->method() == Request::POST){
            $data = Arr::extract($this->post(),['departure_date','received_date','recipient','comments']);
            $data['departure_date'] = !empty($data['departure_date']) ? DateTime::createFromFormat('d/m/Y',$data['departure_date'])->getTimestamp() : null;
            $data['received_date'] = !empty($data['received_date']) ? DateTime::createFromFormat('d/m/Y',$data['received_date'])->getTimestamp() : null;
            $fs = new FileServer();
            try{//todo::добавить загрузку файла
                Database::instance()->begin();
                $tracking->values($data);

                if(!empty($this->files()) AND isset($this->files()['file'][0])){
                    $this->project->makeProjectPaths();
                    $valid = Validation::factory($_FILES)->setValidationTrackingRules('file');
                    $namePaths = explode('.',$this->files()['file'][0]['name']);
                    $ext = end($namePaths);
                    if(strtolower($ext) != 'pdf'){
                        $valid->rule('file', 'Upload::image');
                    }
                    if( !$valid->check()){
                        throw new ORM_Validation_Exception('validation', $valid);
                    }
                    $file = Upload::save($this->files()['file'][0],null,str_replace(DOCROOT,'',$this->project->dateTrackingPath()));
                    $tracking->file = $file;
                }
                $tracking->save();
                Database::instance()->commit();
                if(!empty($tracking->file)){
                    $fs->addFileTask('https://qforb.net/' . $tracking->file,'https://qforb.net/fileserver/callbackplantrackingfile?fileId=' . $tracking->pk());
                }
            }catch (ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch (HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }catch (Exception $e){
                Database::instance()->rollback();
                $this->_setErrors('Operation Error');
            }
        }else{
            $this->setResponseData('modal',View::make('plans/plans/tracking-modal',[
                'item' => $tracking
            ]));
        }

    }

    public function action_delete_tracking_file(){
        $this->_checkForAjaxOrDie();
        $trackingId = $this->getUIntParamOrDie($this->request->param('id'));
        $tracking = ORM::factory('PlanTracking',$trackingId);
        if( ! $tracking->loaded()){
            throw new HTTP_Exception_404();
        }
        if( ! strpos($tracking->file,'fs.qforb.net') === false){
            (new FileServer())->deleteFile($tracking->file);
        }else{

            @unlink(DOCROOT . $tracking->file);
        }
        $tracking->file = null;
        $tracking->save();
    }

    public function action_delete_tracking(){
        $this->_checkForAjaxOrDie();
        $trackingId = $this->getUIntParamOrDie($this->request->param('id'));
        $tracking = ORM::factory('PlanTracking',$trackingId);
        if( ! $tracking->loaded()){
            throw new HTTP_Exception_404();
        }
        $tracking->delete();
    }

    /**
     * todo:: Can't find where is used
     *
     * @throws HTTP_Exception_404
     * @throws Kohana_Exception
     */
    public function action_register_tracking(){
        $this->_checkForAjaxOrDie();
        $projectId = $this->getUIntParamOrDie($this->request->param('id'));
        $plans = Arr::get($this->post(),'plans');
        $this->project = ORM::factory('Project',$projectId);
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project) OR empty($plans)){
            throw new HTTP_Exception_404;
        }

        try{
            Database::instance()->begin();
            $tracking = ORM::factory('PlanTracking');
            $tracking->save();
            $tracking->add('plans',$plans);
            Database::instance()->commit();
        }catch (ORM_Validation_Exception $e){
            Database::instance()->rollback();
            $this->_setErrors($e->errors('validation'));
        }catch (HDVP_Exception $e){
            Database::instance()->rollback();
            $this->_setErrors($e->getMessage());
        }catch (Exception $e){
            Database::instance()->rollback();
            $this->_setErrors('Operation Error');
        }

    }

    public function action_get_custom_number(){
        $objectId = $this->getUIntParamOrDie($this->request->param('param1'));
        $object = ORM::factory('PrObject',$objectId);

        if( ! $object->loaded()){
            throw new HTTP_Exception_404();
        }
        $query = $object->places;
        $data = Arr::extract($_POST,['number','custom_number','type']);
        if(strlen($data['number'])){
            $query->where('number','=',$data['number']*1);
        }else{
            if(strlen($data['custom_number']) === false)
                throw new HTTP_Exception_404();
            $query->where('custom_number','=',':cn')->set(':cn',$data['custom_number']);
        }

        if(!in_array($data['type'],Enum_ProjectPlaceType::toArray())){
            throw new HTTP_Exception_404();
        }

        $place = $query->and_where('type','=',$data['type'])->find();
        $this->setResponseData('number',strlen($data['number']) ? $place->custom_number : $place->number);
    }

    protected function getNormalizedPostArr($arrKey){
        $output = [];
        foreach ($this->post() as $key => $value){
            if(preg_match('~'.$arrKey.'_(?<isNew>\+)?(?<id>[0-9a-z]+)_(?<field>[a-z_]+)~',$key,$matches))
                if($matches['isNew']){
                    $output['new_'.$matches['id']][$matches['field']] = $value;
                }else{
                    $output[$matches['id']][$matches['field']] = $value;
                }

        }
        return $output;
    }
}