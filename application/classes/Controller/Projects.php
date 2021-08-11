<?php defined('SYSPATH') OR die('No direct script access.');
//UPDATE pr_places pp SET pp.custom_number = IF(pp.type = 'public',CONCAT('PB',pp.number),CONCAT('N',pp.number)) WHERE pp.custom_number IS NULL OR pp.custom_number = ''
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 5:53
 */
class Controller_Projects extends HDVP_Controller_Template
{
    protected $_actions_perms = [
        'list,company,property_item_quality_control_list,plans_list,tracking_list,get_custom_number,plan_list_search' => [
            'GET' => 'read'
        ],
        'create' => [
            'GET' => 'read',
            'POST' => 'create'
        ],
        'update,quality_control_list,quality_control,company_project_update,update_quality_control_plan_image,add_quality_control_image_from_raw_dat,add_quality_control_image_from_raw_data,update_tracking,plans_mailing' => [
            'GET' => 'read',
            'POST' => 'update'
        ],
        'assign_users,update_properties,update_links,update_certifications,create_plan,update_plan,add_edition,place_copy,place_create,place_update,certification_files,update_quality_control_image,copy_plan,update_plan_list,plans_printed,update_quality_control_message' => [
            'POST' => 'update'
        ],
        'update_tasks' => 'tasks',
        'get_images,assign_users,project_properties,object_property_struct,place_update,place_create,plans_professions_list,create_plan,update_plan,add_edition' => [
            'GET' => 'read'
        ],
        'certification_files,download_certification_file,plan_history' => [
            'GET' => 'read'
        ],
        'set_image,copy_property,floor_copy,place_copy,place_create,place_update,copy_plan,certification_files,create_plan,update_plan,add_edition,toggle_notifications' => [
            'GET' => 'update'
        ],
        'delete_image,remove_users,delete_property,floor_delete,place_delete,delete_link,delete_plans_file,delete_certification_file,delete_quality_control_file,plan_delete,delete_tracking,delete_tracking_file,delete_space,quality_control_delete' => [
            'GET' => 'delete'
        ],
        'remove_users' => [
            'POST' => 'delete'
        ],
        'floor_update_title' => [
            'GET' => 'update'
        ],
        'tasks' => [
            'GET' => 'read'
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
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Projects'))->set_url('/projects'));
        }
    }

    public function after()
    {
        if ($this->auto_render === TRUE)
        {
            if($this->request->action() == 'company'){
                if($this->company){
                    Breadcrumbs::add(Breadcrumb::factory()->set_title($this->company->name)->set_url('/companies/update/'.$this->company->id));
                    Breadcrumbs::add(Breadcrumb::factory()->set_title($this->company->name.' '. __('Projects'))->set_url(URL::site('/projects')));
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
            $this->template->content = View::make('projects/list', $result + ['filterProjects' => $filterProjects]);
        }
    }

    public function action_company(){
        $this->_setUsrMinimalPriorityLvl(Enum_UserPriorityLevel::Company);
        $id = (int) $this->request->param('id');
        $company = ORM::factory('Company',$id);
        if( ! $company->loaded()){
            throw new HTTP_Exception_404;
        }
        $filterBy = $this->request->param('status');
        $sortBy = $this->request->param('sorting');
        $export = $this->request->param('export');

        $query = ORM::factory('Project');
        if($this->_user->getRelevantRole('outspread') != Enum_UserOutspread::General){
            $query->where('client_id','=',$this->_user->client_id);
        }
        $query->and_where('company_id','=',$company->id);
        if(!empty($filterBy)){
            $query->and_where('status','=',$filterBy);
        }
        if(!empty($sortBy)){
            $query->with('client');
            $query->order_by($sortBy,'ASC');
        }
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
//            $this->template->content = View::make('projects/list', $result + ['projectsEmptyPlans' => Model_Project::getProjectsWithoutPlansSpecialities()]);
            $this->template->content = View::make('projects/list', $result);
        }
        Breadcrumbs::clear();
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url('/'));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Companies'))->set_url(URL::site('companies')));
        Breadcrumbs::add(Breadcrumb::factory()->set_title($company->name)->set_url(URL::site('companies/update/'.$company->id)));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Projects')));
    }

    public function action_create(){
        if($this->request->method() == HTTP_Request::POST)
        {
            $this->_checkForAjaxOrDie();

            try{
                $data = Arr::extract($this->post(),['name','address','company_id','project_id','owner','description','start_date','end_date']);
                array_walk($data,function(&$item){
                    $item = trim($item);
                });
                //var_dump($data);die;
                $objects = Arr::extract($this->post(),['building','parking','object']);
                //проверяем объекты
                foreach ($objects as $key => $val){
                    if($val <= 0){
                        unset($objects[$key]);
                    }else{
                        settype($objects[$key],'integer');
                    }
                }
                unset($key,$val);
                if(empty($objects)){
                    throw new HDVP_Exception('Project must have a minimum one Building or Parking or Object');
                }
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

                //проверяем компанию
                $this->company = ORM::factory('Company',(int)$data['company_id']);
                if( ! $this->company->loaded()){
                    throw new HDVP_Exception('Incorrect Company');
                }

                if(($this->_user->getRelevantRole('outspread') != Enum_UserOutspread::General) AND $this->_user->client_id != $this->company->client_id){
                    throw new HDVP_Exception('You have no privileges to add project in this company');
                }

                $data['company_id'] = $this->company->id;
                $data['status'] = Enum_ProjectStatus::Active;
                $data['client_id'] = $this->company->client_id;
                Database::instance()->begin();
                $project = ORM::factory('Project');
                $project->values($data);
                $project->status = Enum_ProjectStatus::Active;
                $project->save();
                $project->makeProjectPaths();
                if(!empty($this->files()) AND !empty($this->files()['images'])){
                    foreach ($this->files()['images'] as $key => $image){
                        $uploadedFiles[] = [
                            'name' => str_replace($project->imagesPath().DS,'',Upload::save($image,null,$project->imagesPath())),
                            'original_name' => $image['name'],
                            'ext' => Model_File::getFileExt($image['name']),
                            'mime' => $image['type'],
                            'path' => str_replace(DOCROOT,'',$project->imagesPath()),
                            'token' => md5($image['name']).base_convert(microtime(false), 10, 36),
                        ];
                    }
                }
                if(!empty($uploadedFiles)){
                    foreach ($uploadedFiles as $image){
                        $image = ORM::factory('Image')->values($image)->save();
                        if( ! $project->image_id){
                            $project->image_id = $image->id;
                            $project->save();
                        }
                        $project->add('images', $image->pk());
                    }
                }

                foreach ($objects as $type => $cnt){
                    if($cnt > 0){
                        $prType = ORM::factory('PrObjectType',['alias' => $type]);
                        if( ! $prType->loaded()){
                            throw new HDVP_Exception('Incorrect object type');
                        }

                        while ($cnt-- > 0){
                            //создаем объект (здание,парковка и тд...)
                            $object = ORM::factory('PrObject');
                            $object->project_id = $project->id;
                            $object->type_id = $prType->id;
                            $object->start_date = $project->start_date;
                            $object->end_date = $project->end_date;
                            $object->save();
                            Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $object, 'client_id' => (int)$project->client_id]);
                            //todo: в дальнейшем добавить кол-во этажей и всё что с вязанно сними по умолчанию из настроек (помещения, комнаты...)
                            //этаж
//                            $floor = ORM::factory('PrFloor');
//                            $floor->number = 0;
//                            $floor->object_id = $object->id;
//                            $floor->project_id = $project->id;
//                            $floor->save();
//
//                            //помещения
//                            $placesCount = 1;
//                            while ($placesCount-- > 0){
//                                $place = ORM::factory('PrPlace');
//                                $place->project_id = $project->id;
//                                $place->object_id = $object->id;
//                                $place->floor_id = $floor->id;
//                                $place->save();
//
//                                //комната
//                                $space = ORM::factory('PrSpace');
//                                $space->place_id = $place->id;
//                                $space->save();
//                            }
                        }
                    }

                }
                $project->add('users',Auth::instance()->get_user()->id);
                Event::instance()->fire('onProjectAdded',['sender' => $this,'item' => $project]);
                Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $project]);
                Database::instance()->commit();
                $this->makeRedirect('projects/update/'.$project->pk());


            }catch(HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }catch(ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch(Exception $e){
                Database::instance()->rollback();
                $this->_setErrors('Operation Error');
                //$this->_setErrors($e->getMessage());
            }


        }else{
            if($this->_user->getRelevantRole('outspread') != Enum_UserOutspread::General){
                $companies = $this->_user->client->companies->find_all();
            }else{
                $companies = ORM::factory('Company')->find_all();
            }

            Breadcrumbs::add(Breadcrumb::factory()->set_title('New Project'));
            $this->template->content = View::make('projects/create')
            ->set('companies',$companies);
        }

    }

    public function action_company_project_update(){
        $this->action_update();
        Breadcrumbs::clear();
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url('/'));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Companies'))->set_url(URL::site('companies')));
        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->project->company->name)->set_url(URL::site('companies/update/'.$this->project->company->id)));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Projects'))->set_url(URL::site('projects/company/'.$this->project->company->id)));
        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->project->name));
    }

    public function action_update(){
        $this->include_editor = true;
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
            VueJs::instance()->addComponent('certifications/universal-certification');
            VueJs::instance()->includeMultiselect();
            $this->template->content = View::make('projects/update')
                ->set('companies',$companies)
                ->set('objectsCount',$projectObjectsCount)
                ->set('users',$this->project->users->find_all())
                ->set('tasks',$this->project->tasks->order_by('id','DESC')->find_all());

            VueJs::instance()->addComponent('tabs');
            VueJs::instance()->addComponent('reserve-materials');
            VueJs::instance()->addComponent('transferable-items');
            VueJs::instance()->addComponent('texts');
            VueJs::instance()->includeMultiselect();
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

    public function action_assign_users(){
        $this->_checkForAjaxOrDie();
        $this->project = ORM::factory('Project',(int)$this->request->param('id'));
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

            if($this->project->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->project->id,192)){
                throw new HDVP_Exception('Invalid request');
            }

            $users = $this->project->users->find_all();
            $usersData = $this->getNormalizedPostArr('users');
            if(!empty($usersData)){
                $usersData = array_keys($usersData);
            }
            try{
                Database::instance()->begin();
                //Если уже существуют прикреплённые пользователи к проекту
                if(!empty($users)){
                    foreach ($users as $user){
                        $foundKey = array_search($user->id,$usersData);
                        if($foundKey !== false){
                            unset($usersData[$foundKey]);
                        }
                    }

                }

                if(!empty($usersData)){
                    foreach($usersData as $userId){
                        if($this->_user->is('super_admin') AND $this->_user->id == $userId){
                            $this->project->add('users',$this->_user->id);
                        }else{
                            $newUser = ORM::factory('User',['id' => (int)$userId,'company_id' => $this->project->company_id]);
                            if( ! $newUser->loaded()){
                                //todo:: тревожно уже можно начать блокировать пользователя
                                throw new HDVP_Exception('Incorrect User');
                            }
                            $this->project->add('users',$newUser->id);
                        }
                    }
                }
                Database::instance()->commit();
                $this->setResponseData('projectUsersForm',
                    View::make('projects/users/form',
                    ['action' => URL::site('projects/remove_users/'.$this->project->id),
                        'items' => $this->project->users->find_all(),
                        'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192)
                    ]));
                $this->setResponseData('triggerEvent','projectUsersUpdated');
            }catch (HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }catch(Exception $e){
                Database::instance()->rollback();
                $this->_setErrors('Operation Error');
            }




        }else{
            $selected = [];
            $projectUsers = $this->project->users->find_all();
            foreach ($projectUsers as $usr){
                if(!in_array($usr->id,$selected)){
                    $selected []= $usr->id;
                }
            }
            $this->setResponseData('modal',View::make('projects/users/add-user',[
                'items' => $this->company->users->find_all(),
                'selected' => $selected,
                'action' => URL::site('projects/assign_users/'.$this->project->id),
                'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192)
            ]));
        }



    }

    public function action_remove_users(){
        $this->_checkForAjaxOrDie();
        $this->project = ORM::factory('Project',(int)$this->request->param('id'));
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }
        $this->company = $this->project->company;
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }

        View::set_global('_PROJECT', $this->project);
        View::set_global('_COMPANY', $this->company);

        if($this->request->method() != Request::POST){
            throw new HTTP_Exception_404;
        }

            if($this->project->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->project->id,192)){
                throw new HDVP_Exception('Invalid request');
            }

            $users = $this->project->users->find_all();
            $usersData = $this->getNormalizedPostArr('users');
            if(!empty($usersData)){
                $usersData = array_keys($usersData);
            }
            try{
                Database::instance()->begin();
                //Если уже существуют прикреплённые пользователи к проекту
                if(!empty($users)){
                    foreach ($users as $user){
                        $foundKey = array_search($user->id,$usersData);
                        if($foundKey !== false){
                            if(Auth::instance()->get_user()->id != $user->id){
                                $this->project->remove('users',$user->id);
                            }else{
                                throw new HDVP_Exception('You can\'t remove yourself');
                            }

                        }
                    }

                }
                Database::instance()->commit();
                $this->setResponseData('projectUsersForm',
                    View::make('projects/users/form',
                        ['action' => URL::site('projects/remove_users/'.$this->project->id),
                            'items' => $this->project->users->find_all(),
                            'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192)
                        ]));
                $this->setResponseData('triggerEvent','projectUsersUpdated');
            }catch (HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }catch(Exception $e){
                Database::instance()->rollback();
                $this->_setErrors('Operation Error');
            }
    }

    public function action_toggle_notifications(){

        $projectId = (int)$this->request->param('projectId');
        $userId = (int)$this->request->param('userId');
        $project = ORM::factory('Project',$projectId);
        if( ! $project->loaded()){
            throw new HTTP_Exception();
        }
        $user = $project->users->where('id','=',$userId)->find();
        if( ! $user->loaded()){
            throw new HTTP_Exception();
        }

        DB::query(Database::UPDATE,'UPDATE users_projects SET notify_changes = IF(notify_changes = 1, 0, 1) WHERE user_id = '.$user->id.' AND project_id = '.$project->id)->execute();
    }

    public function action_update_properties(){

        $this->project = ORM::factory('Project',(int)$this->request->param('id'));
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }
        $this->company = $this->project->company;
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }

        $propertiesData = $this->getNormalizedPostArr('property');

        if($this->project->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->project->id,192)){
            $this->_setErrors('Invalid request');
        }
        else if(empty($propertiesData)){
            $this->_setErrors('No changes detect for update');
        }
        else{
            try{
                Database::instance()->begin();
                $typeStairway = ORM::factory('ConstructElement',['id' => 2]);
                $typeApartment = ORM::factory('ConstructElement',1);
                $defaultSpaceId = ORM::factory('PrSpaceType')->order_by('id','ASC')->limit(1)->find()->id;
                if(!($typeStairway->loaded() AND $typeApartment->loaded() )){
                    throw new HDVP_Exception('Database corrupt, some data is missing');
                }

                foreach ($propertiesData as $objId => $val){
                    $val['start_date'] = DateTime::createFromFormat('d/m/Y',$val['start_date'])->getTimestamp();
                    $val['end_date'] = DateTime::createFromFormat('d/m/Y',$val['end_date'])->getTimestamp();
                    if(is_numeric($objId)){
                        $object = ORM::factory('PrObject',$objId);
                        if($object->bigger_floor OR abs($object->smaller_floor) OR $object->places->count_all() OR ($object->state == Enum_ObjectState::Approved)){
                            $object->values($val,['type_id','name','start_date','end_date']);
                        }else{
                            if($val['places_count'] <= 0) continue;
                            $object->values($val,['type_id','name','smaller_floor','bigger_floor','places_count','start_date','end_date']);

                        }
                    }else{
                        $object = ORM::factory('PrObject');
                        $object->project_id = $this->project->id;
                        $object->state = Enum_ObjectState::NotApproved;
                        $object->values($val,['type_id','name','smaller_floor','bigger_floor','places_count','start_date','end_date','state']);
                        $object->save();
                        Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $object, 'client_id' => (int)$this->project->client_id]);
                    }

                        if($object->state != Enum_ObjectState::Approved){
                            $fcnt = $object->getFloorsCount();
                            $floorsArr = $this->getFloorsArray($fcnt,$object->places_count);
                            $floorNumber = $object->smaller_floor;
                            $placeNumber = 1;
                            $placeOrder = 1;
                            $privPlaceNumber = $object->smaller_floor;
                            if($floorNumber < 0){
                                for($i = 0; $i > $floorNumber; $i--){
                                    $placeNumber -= $floorsArr[abs($i)+1];
                                    $placeNumber++;
                                }
                                $placeNumber -=1;
                            }

                            foreach ($floorsArr as $placesCount){
                                $floor = ORM::factory('PrFloor');
                                $floor->number = $floorNumber++;
                                $floor->object_id = $object->id;
                                $floor->project_id = $object->project_id;
                                $floor->save();

                                //помещения
                                for ($i = 0; $i < $placesCount; $i++){
                                    if($placeNumber == 0) $placeNumber = 1;
                                    $place = ORM::factory('PrPlace');
                                    $place->project_id = $object->project_id;
                                    $place->object_id = $object->id;
                                    $place->floor_id = $floor->id;
                                    $place->ordering = $placeOrder++;
                                    if(($floor->number >=0 AND $i === 0) OR ($floor->number < 0 AND $i === $placesCount-1)){
                                        if($privPlaceNumber == 0) $privPlaceNumber = 1;
                                        $place->number = $privPlaceNumber++;
                                        $place->type = Enum_ProjectPlaceType::PublicS;
                                        $place->custom_number = 'PB'.$place->number;
                                        $place->icon = $typeStairway->icon;
                                        $place->name = $typeStairway->name;
                                    }else{
                                        $place->number = $placeNumber++;
                                        $place->type = Enum_ProjectPlaceType::PrivateS;
                                        $place->custom_number = 'N'.$place->number;
                                        $place->icon = $typeApartment->icon;
                                        $place->name = $typeApartment->name;
                                    }



                                    $place->save();
                                    $spaceCount = ($place->icon == $typeApartment->icon) ? $typeApartment->space_count : $typeStairway->space_count;

                                    while($spaceCount--){
                                        //комната
                                        $space = ORM::factory('PrSpace');
                                        $space->place_id = $place->id;
                                        $space->type_id = $defaultSpaceId;
                                        $space->save();
                                    }

                                }
                            }

                            $object->state = Enum_ObjectState::Approved;
                            $object->save();
                            $object->updateDynamicDataColumns();
                        }else{
                            $object->save();

                        }
                    Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $object, 'client_id' => (int)$this->project->client_id]);



                }
                Database::instance()->commit();
                View::set_global('_PROJECT', $this->project);

                $content = View::make('projects/property/form',
                    ['action' => URL::site('projects/update_properties/'.$this->project->id),
                        'items' => $this->project->objects->order_by('id','DESC')->find_all(),
                        'itemTypes' => ORM::factory('PrObjectType')->find_all(),
                        'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192)
                    ]);

                $this->setResponseData('propsForm',$content);
                $this->setResponseData('triggerEvent','propsUpdated');
            }catch (Exception $e){
                throw $e;
            }

        }

    }

    public function action_project_properties(){
        $id = (int)$this->request->param('id');

        $this->_checkForAjaxOrDie();
        $this->project = ORM::factory('Project',(int)$this->request->param('id'));
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }
        $this->company = $this->project->company;
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }

        View::set_global('_PROJECT', $this->project);

        $content = View::make('projects/property/form',
            ['action' => URL::site('projects/update_properties/'.$this->project->id),
                'items' => $this->project->objects->order_by('id','DESC')->find_all(),
                'itemTypes' => ORM::factory('PrObjectType')->find_all(),
                'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192)
            ]);

        $this->setResponseData('projectObjects',$content);
    }

    public function action_delete_property(){
        $this->_checkForAjaxOrDie();
        $projectId = (int)$this->request->param('param1');
        $objectId = (int)$this->request->param('param2');


        $this->project = ORM::factory('Project',$projectId);
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }
        $object = $this->project->objects->where('id','=',$objectId)->find();
        if( ! $object->loaded()){
            throw new HTTP_Exception_404;
        }

        try{
            if($object->plans->count_all() > 0){
                throw new HDVP_Exception(__('object_delete_restrict'));
            }
            $object->delete();
            Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $object, 'client_id' => (int)$this->project->client_id]);
        }catch (HDVP_Exception $e){
            $this->_setErrors($e->getMessage());
        }
        catch (Exception $e){
            throw $e;
        }

    }

    public function action_object_property_struct(){

        $companyId = (int)$this->request->param('param1');
        $projectId = (int)$this->request->param('param2');
        $objectId = (int)$this->request->param('param3');

        $this->_checkForAjaxOrDie();
        $this->company = ORM::factory('Company',$companyId);
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }
        $this->project = $this->company->projects->where('id','=',$projectId)->find();
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }


        $this->setResponseData('struct',$this->getObjectStruct($objectId));
    }

    public function action_property_item_quality_control_list(){
        $this->_checkForAjaxOrDie();
        $id = (int) $this->request->param('id');
        $crafts = $this->request->param('crafts');
        $status = $this->request->param('status');

        $place = ORM::factory('PrPlace',$id);
        if(!$place->loaded()){
            throw new HTTP_Exception_404();
        }

//        $query = $place->quality_control->where('craft_id','IN',DB::expr('('.$crafts.')'));
        $query = $place->quality_control; // todo:: Changed by ArmSALArm
        $selectedStatus = 'all';

        $qualityControls['statuses'] = ['all' =>'All'] + Enum_QualityControlApproveStatus::toArray();
        foreach ($qualityControls['statuses'] as $key => &$val){
            $tmpUrlParams = Request::current()->param();
            $tmpRequest = clone($query);
            $tmpUrlParams['status'] = $status;
            if($key == 'all'){
                $tmpUrlParams['status'] = null;
                $output = [
                    'count' => $tmpRequest->count_all(),
                    'url' => Route::url('site.projectsObjectQualityControl',$tmpUrlParams,'https'),
                    'text' => $val
                ];
            }else{
                $tmpUrlParams['status'] = $val;
                $output = [
                    'count' => $tmpRequest->and_where('approval_status','=',$val)->count_all(),
                    'url' => Route::url('site.projectsObjectQualityControl',$tmpUrlParams,'https'),
                    'text' => $val
                ];
            }
            $val = $output;
        }


        if(!empty($status)){
            $query->and_where('approval_status','=',$status);
            $selectedStatus = $status;
        }
        $query->order_by('id','DESC');

        $items = $query->find_all();



        $content = View::make('projects/property/item/quality-controls-list',[
            'items' => $items,
            'selectedStatus' => $selectedStatus,
            'filterData' => $qualityControls,
        ]);

        $this->setResponseData('modal',$content->render());
    }

    public function action_copy_property(){
        $this->_checkForAjaxOrDie();
        $projectId = (int) $this->request->param('param1');
        $objectId = (int) $this->request->param('param2');
        $this->project = ORM::factory('Project',$projectId);
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }
        $object = $this->project->objects->where('id','=',$objectId)->find();
        if( ! $object->loaded()){
            throw new HTTP_Exception_404;
        }
        try{
            Database::instance()->begin();
            $object->copy();
            Database::instance()->commit();
            View::set_global('_PROJECT', $this->project);
            $content = View::make('projects/property/form',
                ['action' => URL::site('projects/update_properties/'.$this->project->id),
                    'items' => $this->project->objects->order_by('id','DESC')->find_all(),
                    'itemTypes' => ORM::factory('PrObjectType')->find_all(),
                    'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192)
                ]);
            $this->setResponseData('propsForm',$content);
            $this->setResponseData('triggerEvent','propsUpdated');
            Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $object, 'client_id' => (int)$this->project->client_id]);
        }catch (Exception $e){
            Database::instance()->rollback();
            throw $e;
        }


    }

    public function action_floor_update_title(){
        $projectId = $this->request->param('param1');
        $objectId = $this->request->param('param2');
        $floorId = $this->request->param('param3');
        $this->project = ORM::factory('Project',$projectId);
        $customName = $this->request->query('custom_name');
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project))
            throw new HTTP_Exception_404;
        $object = $this->project->objects->where('id','=',$objectId)->find();
        if( ! $object->loaded()) throw new HTTP_Exception_404;
        $floor = $object->floors->where('id','=',$floorId)->find();
        if( ! $floor->loaded()) throw new HTTP_Exception_404;
        try{
            $floor->custom_name = $customName;
            $floor->save();
            $this->setResponseData('struct',$this->getObjectStruct($object->id));
            Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $object, 'client_id' => (int)$this->project->client_id]);
        }catch (Exception $e){
            throw $e;
        }

    }

    public function action_floor_copy(){

        $projectId = $this->request->param('param1');
        $objectId = $this->request->param('param2');
        $floorId = $this->request->param('param3');
        $this->project = ORM::factory('Project',$projectId);
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project))
            throw new HTTP_Exception_404;
        $object = $this->project->objects->where('id','=',$objectId)->find();
        if( ! $object->loaded()) throw new HTTP_Exception_404;
        $floor = $object->floors->where('id','=',$floorId)->find();
        if( ! $floor->loaded()) throw new HTTP_Exception_404;
        try{
            Database::instance()->begin();
            $object->copyFloorToUp($floor);
            Database::instance()->commit();
            $this->setResponseData('struct',$this->getObjectStruct($object->id));
            Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $object, 'client_id' => (int)$this->project->client_id]);
        }catch (Exception $e){
            Database::instance()->rollback();
            throw $e;
        }

    }

    public function action_floor_delete(){
        $projectId = $this->request->param('param1');
        $objectId = $this->request->param('param2');
        $floorId = $this->request->param('param3');
        $this->project = ORM::factory('Project',$projectId);
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project))
            throw new HTTP_Exception_404;
        $object = $this->project->objects->where('id','=',$objectId)->find();
        if( ! $object->loaded()) throw new HTTP_Exception_404;
        $floor = $object->floors->where('id','=',$floorId)->find();
        if( ! $floor->loaded()) throw new HTTP_Exception_404;
        try{
            Database::instance()->begin();
            if($floor->plans->count_all() > 0){
                throw new HDVP_Exception('Cant delete floor because it has a plan');
            }
            $object->deleteFloor($floor);
            Database::instance()->commit();
            $this->setResponseData('struct',$this->getObjectStruct($object->id));
            Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $object, 'client_id' => (int)$this->project->client_id]);
        }catch (HDVP_Exception $e){
            Database::instance()->rollback();
            $this->_setErrors($e->getMessage());
        }
        catch (Exception $e){
            Database::instance()->rollback();
            throw $e;
        }
    }

    public function action_place_copy(){
        $objectId = $this->request->param('param1');
        $floorId = $this->request->param('param2');
        $placeId = $this->request->param('param3');

        $object = ORM::factory('PrObject',$objectId);
        if( ! $object->loaded()) throw new HTTP_Exception_404;
        $floor = $object->floors->where('id','=',$floorId)->find();
        if( ! $floor->loaded()) throw new HTTP_Exception_404;
        $place = $object->places->where('id','=',$placeId)->find();
        if( ! $place->loaded()) throw new HTTP_Exception_404;
        try{
            Database::instance()->begin();
            $floor->copyPlaceToUp($place);
            Database::instance()->commit();
            $this->setResponseData('struct',$this->getObjectStruct($object->id));
            Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $object, 'client_id' => (int)$object->project->client_id]);
        }catch (Exception $e){
            throw $e;
        }
    }

    public function action_place_delete(){
        $objectId = $this->request->param('param1');
        $floorId = $this->request->param('param2');
        $placeId = $this->request->param('param3');

        $object = ORM::factory('PrObject',$objectId);
        if( ! $object->loaded()) throw new HTTP_Exception_404;
        $floor = $object->floors->where('id','=',$floorId)->find();
        if( ! $floor->loaded()) throw new HTTP_Exception_404;
        $place = $object->places->where('id','=',$placeId)->find();
        if( ! $place->loaded()) throw new HTTP_Exception_404;
        try{
            Database::instance()->begin();
            if($floor->places->count_all() <= 1){
                throw new HDVP_Exception('Floor must have minimum one place');
            }
            if($place->plans->count_all()){
                throw new HDVP_Exception('Cant delete place, because it has a plan');
            }
            if($place->quality_control->count_all()){
                throw new HDVP_Exception('Cant delete place, because it has a quality controls');
            }
            $floor->deletePlace($place);
            Database::instance()->commit();
            $this->setResponseData('struct',$this->getObjectStruct($object->id));
            Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $object, 'client_id' => (int)$object->project->client_id]);
        }catch (HDVP_Exception $e){
            Database::instance()->rollback();
            $this->_setErrors($e->getMessage());
        }
        catch (Exception $e){
            Database::instance()->rollback();
            throw $e;
        }
    }

    public function action_place_create(){

        $objectId = $this->request->param('param1');
        $floorId = $this->request->param('param2');
        $placeId = $this->request->param('param3');
        $object = ORM::factory('PrObject',$objectId);
        if( ! $object->loaded()) throw new HTTP_Exception_404;
        $floor = $object->floors->where('id','=',$floorId)->find();
        if( ! $floor->loaded()) throw new HTTP_Exception_404;
        $place = $object->places->where('id','=',$placeId)->find();
        if( ! $place->loaded()) throw new HTTP_Exception_404;
        if($this->request->method() == HTTP_Request::POST){
            try{
                $spacesData = $this->getNormalizedPostArr('space');
                if(empty($spacesData)){
                    throw new HDVP_Exception('Element must have minimum one space');
                }
                Database::instance()->begin();
                $place = $floor->addPlaceAfter($place,Arr::extract($this->post(),['name','icon','type','custom_number']));
                $place->custom_number = ($place->type == 'public') ? ('PB'.$place->number) :  ('N'.$place->number);
                $place->save();
                $spaces = [];
                foreach ($spacesData as $sp){
                    $space = ORM::factory('PrSpace');//->values($sp + ['place_id' => $place->id],['name','type_id','place_id'])->save();
                    $space->place_id = $place->id;
                    $space->desc = $sp['desc'];
                    $space->type_id = $sp['type'];
                    $space->save();
                }
                Database::instance()->commit();
                $this->setResponseData('struct',$this->getObjectStruct($object->id));
                $this->setResponseData('triggerEvent','placeCreated');
                Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $object, 'client_id' => (int)$object->project->client_id]);
            }catch (HDVP_Exception $e){
                Database::instance()->rollback();
                throw $e;
            }catch (ORM_Validation_Exception $e){
                Database::instance()->rollback();
                throw $e;
            }catch(Exception $e){
                Database::instance()->rollback();
                throw $e;
            }
        }else{

            $this->setResponseData('modal',
                View::make('modals/simple-modal',
                    [
                        'content' => View::make('projects/places/create',[
                            'action' => URL::site('projects/place_create/'.$floor->object_id.'/'.$floor->id.'/'.$place->id),
                            'defaultSpaceId' => sprintf('%013.0f', microtime(1)*1000 ),
                            'place' => $place,
                            'floor' => $floor,
                            'placeTypes' => ORM::factory('ConstructElement')->find_all(),
                            'spaceTypes' => ORM::factory('PrSpaceType')->find_all()
                        ])
                    ]
                )
            );
        }

    }

    public function action_place_update(){
        $objectId = $this->request->param('param1');
        $floorId = $this->request->param('param2');
        $placeId = $this->request->param('param3');
        $object = ORM::factory('PrObject',$objectId);
        if( ! $object->loaded()) throw new HTTP_Exception_404;
        $floor = $object->floors->where('id','=',$floorId)->find();
        if( ! $floor->loaded()) throw new HTTP_Exception_404;
        $place = $object->places->where('id','=',$placeId)->find();
        if( ! $place->loaded()) throw new HTTP_Exception_404;
        if($this->request->method() == HTTP_Request::POST){
            try{
                $spacesData = $this->getNormalizedPostArr('space');
                if(empty($spacesData)){
                    throw new HDVP_Exception('Element must have minimum one space');
                }
                Database::instance()->begin();

                foreach ($spacesData as $sid => $sp){
                    $space = ORM::factory('PrSpace',is_numeric($sid) ? $this->getUIntParamOrDie($sid) : null);
                    $space->place_id = $place->id;
                    $space->desc = $sp['desc'];
                    $space->type_id = $sp['type'];
                    $space->save();
                }

                $type = Arr::get($this->post(),'type');
                $needUpdate = false;
                if(empty($type)){
                    throw new HTTP_Exception_404;
                }

                $needUpdate = ($type != $place->type);
                $place->values(Arr::extract($this->post(),['name','icon','type','custom_number']))->save();
                if($needUpdate)
                $place->floor->placeTypeChanged($place);
                $qcs = ORM::factory('QualityControl')->where('place_id','=',$place->id)->find_all();
                foreach ($qcs as $q){
                    $q->place_type = $place->type;
                    $q->save();
                }
                Database::instance()->commit();
                $this->setResponseData('struct',$this->getObjectStruct($object->id));
                $this->setResponseData('triggerEvent','placeCreated');
                Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $object, 'client_id' => (int)$object->project->client_id]);
            }catch (HDVP_Exception $e){
                Database::instance()->rollback();
                throw $e;
            }catch (ORM_Validation_Exception $e){
                Database::instance()->rollback();
                throw $e;
            }catch(Exception $e){
                Database::instance()->rollback();
                throw $e;
            }
        }else{

            $this->setResponseData('modal',
                View::make('modals/simple-modal',
                    [
                        'content' => View::make('projects/places/update',[
                            'action' => URL::site('projects/place_update/'.$floor->object_id.'/'.$floor->id.'/'.$place->id),
                            'place' => $place,
                            'floor' => $floor,
                            'spaceTypes' => ORM::factory('PrSpaceType')->find_all(),
                            'placeTypes' => ORM::factory('ConstructElement')->find_all(),
                        ])
                    ]
                )
            );
        }
    }

    public function action_delete_space(){
        $placeId = $this->getUIntParamOrDie($this->request->param('param1'));
        $spaceId = $this->getUIntParamOrDie($this->request->param('param2'));
        $place = ORM::factory('PrPlace',$placeId);
        if( ! $place->loaded()){
            throw new HTTP_Exception_404();
        }

        $space = $place->spaces->where('id','=',$spaceId)->find();
        if( ! $space->loaded()){
            throw new HTTP_Exception_404();
        }

        if($place->spaces->count_all() > 1){
            $space->delete();
            Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $place->object, 'client_id' => (int)$place->project->client_id]);
        }else{
            throw new HDVP_Exception('Place must have at least 1 space');
        }


    }

    public function action_quality_control_list(){
        $objectId = $this->request->param('param1');
        $floorId = $this->request->param('param2');
        $placeId = $this->request->param('param3');
        $object = ORM::factory('PrObject',$objectId);
        if( ! $object->loaded()) throw new HTTP_Exception_404;
        $floor = $object->floors->where('id','=',$floorId)->find();
        if( ! $floor->loaded()) throw new HTTP_Exception_404;
        $place = $object->places->where('id','=',$placeId)->find();
        if( ! $place->loaded()) throw new HTTP_Exception_404;
        if($this->request->method() == HTTP_Request::POST){
            try{
                $spacesData = $this->getNormalizedPostArr('space');
                if(empty($spacesData)){
                    throw new HDVP_Exception('Element must have minimum one space');
                }
                Database::instance()->begin();
                $place = $floor->addPlaceAfter($place,Arr::extract($this->post(),['name','icon','type']));
                $spaces = [];
                foreach ($spacesData as $sp){
                    $space = ORM::factory('PrSpace');//->values($sp + ['place_id' => $place->id],['name','type_id','place_id'])->save();
                    $space->place_id = $place->id;
                    $space->desc = $sp['desc'];
                    $space->type_id = $sp['type'];
                    $space->save();
                }
                Database::instance()->commit();
                $this->setResponseData('struct',$this->getObjectStruct($object->id));
                $this->setResponseData('triggerEvent','placeCreated');
            }catch (HDVP_Exception $e){
                Database::instance()->rollback();
                throw $e;
            }catch (ORM_Validation_Exception $e){
                Database::instance()->rollback();
                throw $e;
            }catch(Exception $e){
                Database::instance()->rollback();
                throw $e;
            }
        }else{

            $this->setResponseData('modal',
                View::make('modals/simple-modal',
                    [
                        'content' => View::make('projects/places/quality-control-list',[
                            'action' => URL::site('projects/place_create/'.$floor->object_id.'/'.$floor->id.'/'.$place->id),
                            'defaultSpaceId' => sprintf('%013.0f', microtime(1)*1000 ),
                            'place' => $place,
                            'floor' => $floor,
                            'placeTypes' => ['Public' => Enum_ProjectPlaceType::PublicS,'Private' => Enum_ProjectPlaceType::PrivateS]
                        ])
                    ]
                )
            );
        }
    }

    public function action_update_tasks(){
        $this->_checkForAjaxOrDie();
        $companyId = (int)$this->request->param('param1');
        $projectId = (int)$this->request->param('param2');


        $this->company = ORM::factory('Company',$companyId);
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }
        $this->project = $this->company->projects->where('id','=',$projectId)->find();
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }

        $tasksData = $this->getNormalizedPostArr('task');

        //удаляем явно не валидные данные
        foreach ($tasksData as $key => $val){
            if(!trim($val['name']) AND !is_numeric($key)){
                unset($tasksData[$key]);
            }
        }

        if(!empty($tasksData)){
            try{
                Database::instance()->begin();
                foreach($tasksData as $tId => $t){
                    $task = ORM::factory('PrTask',is_numeric($tId) ? (int)$tId : null);
                    if( ! is_numeric($tId) AND empty($t['crafts'])){
                        throw new HDVP_Exception('Crafts can\'t be empty');
                    }

                    //todo: проверить пренодлежат ли крафты к данной компании



                    if( ! is_numeric($tId)){
                        $task->values(Arr::merge($t,['project_id' => $this->project->id]),['project_id','name','status']);
                        $task->save();
                        $task->add('crafts',$t['crafts']);
                    }else{
                        $task->values($t,['name','status']);
                        $task->save();
                        $task->remove('crafts');
                        if(!empty($t['crafts'])){
                            if(!is_array($t['crafts'])){
                                $t['crafts'] = [$t['crafts']];
                            }
                            $task->add('crafts',$t['crafts']);
                        }
                    }
                }
                Database::instance()->commit();
                View::set_global('_PROJECT', $this->project);
                $this->setResponseData('tasksForm',
                    View::make('projects/tasks/form',
                        ['action' => URL::site('projects/update_tasks/'.$this->company->id.'/'.$this->project->id),
                            'items' => $this->project->tasks->order_by('id','DESC')->find_all(),
                            'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192)
                        ])
                );
                Event::instance()->fire('onTasksUpdated',['sender' => $this,'item' => $this->project]);
                $this->setResponseData('triggerEvent','tasksUpdated');
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

        }
    }

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
                $professions [] = $plan->profession_id;
            }
        }

        View::set_global('_PROJECT', $this->project);
        View::set_global('_COMPANY', $this->company);
        $this->setResponseData('modal',View::make('projects/plans/professions-list',[
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


        $paginationSettings = [
            'items_per_page' => 15,
            'view'              => 'pagination/project',
            'current_page'      => ['source' => 'route', 'key'    => 'page'],
        ];
        $result = (new ORMPaginate($query,null,$paginationSettings))->getData();

        View::set_global('_PROJECT', $this->project);
        $this->setResponseData('plans',View::make('projects/plans/list',
            [   'items' => $result['items'],
                'pagination' => $result['pagination'],
                'objects' => $this->project->objects->find_all(),
                'professions' => $this->project->company->professions->where('status','=',Enum_Status::Enabled)->order_by('cmpprofession.name','ASC')->find_all(),
                'floorsFilter' => $this->project->getObjectsBiggerAndSmallerFloors(),
                'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192)
            ]
        ));
    }

    public function action_plans_list(){
        $projectId = (int)$this->request->param('project_id');
        $objectId = (int)$this->request->param('object_id');
        $professionIds = $this->request->param('professions');
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
        $this->setResponseData('plans',View::make('projects/plans/list',
            $this->_getPlanListPaginatedData($this->project, isset($object) ? $object : null, !empty($professionIds) ? $professionIds : null, !empty($floorIds) ? $floorIds : null)
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
                    $planFile = $plan->file();
                    if($planFile->loaded()){
                        $planFile->customName(Arr::get($c,'name'));
                    }
                    $plan->edition = Arr::get($c,'edition');


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

                    if(!empty($c['crafts']) OR $c['crafts'] == '0'){
                        if(empty($this->company)){
                            $this->company = $this->project->company;
                        }
                        $plan->remove('crafts');
                        if(!is_array($c['crafts'])){
                            $c['crafts'] = [$c['crafts']];
                        }

                        $crafts = $this->company->crafts->where('id','IN',DB::expr('('.implode(',',$c['crafts']).')'))->find_all();

                        if(count($crafts) != count($c['crafts'])){
                            throw new HDVP_Exception('Incorrect crafts');
                        }
                        foreach ($crafts as $craft){
                            $plan->add('crafts',$craft);
                        }
                    }

                    $plansArr[] = $plan;
                }
                //выстреливаем события
                foreach ($plansArr as $item){
                    Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $item]);
                }
                Database::instance()->commit();
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

    protected function _getPlanListPaginatedData($project,$object = null, array $professions = null, array $floors = null, $place_custom_number = null){
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
        $query->order_by('created_at','DESC');
        $paginationSettings = [
            'items_per_page' => 15,
            'view'              => 'pagination/project',
            'current_page'      => ['source' => 'route', 'key'    => 'page'],
        ];
        $query->distinct(true);
        $result = (new ORMPaginate($query,null,$paginationSettings))->getData();
        return [   'items' => $result['items'],
            'pagination' => $result['pagination'],
            'objects' => $this->project->objects->find_all(),
            'professions' => $this->project->company->professions->where('status','=',Enum_Status::Enabled)->order_by('cmpprofession.name','ASC')->find_all(),
            'floorsFilter' => $this->project->getObjectsBiggerAndSmallerFloors(),
            'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$project->id,192)
        ];
    }
    public function action_create_plan(){
        $this->_checkForAjaxOrDie();
        $this->project = ORM::factory('Project',(int)$this->request->param('id'));
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
            $data = Arr::extract($this->post(),['object_id','profession_id','project_id','company_id']);
            try{
                if($data['project_id'] != $this->project->id OR $data['company_id'] != $this->company->id){
                    throw new HDVP_Exception('Invalid data');
                }

                if(!isset($_FILES['file'])){
                    throw new HDVP_Exception('Plan must have a file');
                }else{
                    $this->project->makeProjectPaths();
                }

                $fileData = [
                    'name' => str_replace($this->project->plansPath().DS,'',Upload::save($_FILES['file'],null,$this->project->plansPath())),
                    'original_name' => $_FILES['file']['name'],
                    'ext' => Model_File::getFileExt($_FILES['file']['name']),
                    'mime' => $_FILES['file']['type'],
                    'path' => str_replace(DOCROOT,'',$this->project->plansPath()),
                    'token' => md5($_FILES['file']['name']).base_convert(microtime(false), 10, 36),
                ];

                Database::instance()->begin();

                $data['date'] = time();//DateTime::createFromFormat('d/m/Y',$data['date'])->getTimestamp();
                $plan = ORM::factory('PrPlan')->values($data);
                $plan->scope = Model_PrPlan::getNewScope();
                $plan->project_id = $this->project->id;
                $plan->save();

                $file = ORM::factory('PlanFile')->values($fileData)->save();
                $plan->add('files', $file->pk());
                Event::instance()->fire('onPlanFileAdded',['sender' => $this,'item' => $file]);
                $this->setResponseData('triggerEvent','projectPlanCreated');
                $this->setResponseData('id',$plan->id);
                Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $plan]);
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
            $this->setResponseData('modal',View::make('projects/plans/create',[
                'professions' => $this->company->professions->where('status','=',Enum_Status::Enabled)->find_all(),
                'objects' => $this->project->objects->find_all(),
                'project' => ['id' => $this->project->id, 'name' => $this->project->name],
                'company' => ['id' => $this->company->id, 'name' => $this->company->name],
                'date' => date('d/m/Y H:i'),
                'action' => URL::site('projects/create_plan/'.$this->project->id)
            ]));
        }
    }

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
                $data = Arr::extract($this->post(),['name','edition','description','object_id','date','profession_id','scale','status']);
                $data['place_id'] = 0;
                if(Arr::get($this->post(),'place_number')){
                    $placeData = Arr::extract($this->post(),['place_number','place_type']);
                    $placeData['place_number'] = (int)$placeData['place_number'];
                    if(!$placeData['place_number'] OR !in_array($placeData['place_type'],Enum_ProjectPlaceType::toArray())){
                        throw new HDVP_Exception('Incorrect data set');//todo:: заблокировать пользователя
                    }
                    $place = ORM::factory('PrPlace',['object_id' => Arr::get($this->post(),'object_id'),'number' => $placeData['place_number'], 'type' => $placeData['place_type']]);
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
                        throw new HDVP_Exception('Incorrect flor numbers');
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
                $this->setResponseData('projectPlansForm',View::make('projects/plans/list',
                    [   'items' => $this->project->plans->order_by('created_at','Desc')->find_all(),
                        'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192)
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
            $this->setResponseData('modal',View::make('projects/plans/update',[
                'professions' => $this->company->professions->where('status','=',Enum_Status::Enabled)->with('crafts')->find_all(),
                'action' => URL::site('projects/update_plan/'.$this->project->id.'/'.$plan->id),
                'item' => $plan,
                'trackingItems' => $plan->trackings->order_by('created_at','DESC')->find_all()
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
                $data = Arr::extract($this->post(),['edition','description','date','scale','status','sheet_number']);
                $data['date'] = DateTime::createFromFormat('d/m/Y',$data['date'])->getTimestamp();
                $newPlan = ORM::factory('PrPlan')->values($data);
                $newPlan->name = $plan->name;
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
                        $file = ORM::factory('PlanFile')->values($file)->save();
                        $newPlan->add('files', $file->pk());
                        Event::instance()->fire('onPlanFileAdded',['sender' => $this,'item' => $file]);
                    }
                    $f = $plan->file();
                    if($f->hasCustomName()){
                        $file->customName($f->customName());
                    }
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

            $this->setResponseData('projectPlansForm',View::make('projects/plans/list',
                $this->_getPlanListPaginatedData($this->project)));
            $this->setResponseData('triggerEvent','projectPlansUpdated');
            Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $plan]);
        }else{
            $this->setResponseData('modal',View::make('projects/plans/add-edition',[
                'professions' => $this->company->professions->where('status','=',Enum_Status::Enabled)->with('crafts')->find_all(),
                'action' => URL::site('projects/add_edition/'.$this->project->id.'/'.$plan->id),
                'item' => $plan,
                'historyItems' => ORM::factory('PrPlan')->where('scope','=',$plan->scope)->and_where('id','<>',$plan->id)->order_by('created_at','DESC')->find_all(),

            ]));
        }
    }

    public function action_copy_plan(){
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

        if($this->request->method() == Request::POST){
            $object_id = (int)Arr::get($this->post(),'object_id');
            $object = $this->project->objects->where('id','=',$object_id)->find();
            if( ! $object->loaded()){
                throw new HTTP_Exception_404;
            }
            try{
                Database::instance()->begin();
                $plan->cloneIntoObject($object);
                Database::instance()->commit();
                $this->setResponseData('projectPlansForm',View::make('projects/plans/list',
                    $this->_getPlanListPaginatedData($this->project, isset($object) ? $object : null, !empty($professionIds) ? $professionIds : null)
                    ));
                $this->setResponseData('triggerEvent','projectPlansUpdated');
                Event::instance()->fire('onPlanCopy',['sender' => $this,'item' => $plan]);
            }catch (Exception $e){
                Database::instance()->rollback();
                throw $e;
            }

        }else{
            $this->setResponseData('modal',View::make('projects/plans/copy-modal',[
                'objects' => $this->project->objects->find_all(),
                'action' => URL::site('projects/copy_plan/'.$this->project->id.'/'.$plan->id)
            ]));

        }


    }

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
        $this->setResponseData('modal',View::make('projects/plans/history',[
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
                View::make('projects/plans/mailing',[
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
        $this->setResponseData('html',View::make('projects/plans/date-tracking',$result));

        $this->auto_render = false;
        //var_dump(count($result['items']));

    }

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
        $this->setResponseData('tracking',View::make('projects/plans/tracking',[
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
            $this->setResponseData('modal',View::make('projects/plans/tracking-modal',[
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
        if( ! strpos($tracking->file,'fs.qforb.net')){
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


    public function action_quality_control(){
        $this->_checkForAjaxOrDie();
        $placeId = (int)$this->request->param('id');
        $place = ORM::factory('PrPlace',$placeId);
        if( ! $place->loaded()){
            throw new HTTP_Exception_404;
        }
        $this->project = $place->project;
        View::set_global('_PROJECT', $this->project);
        if($this->request->method() == HTTP_Request::POST){
            $formData = Arr::extract($this->post(),['space_id','status','project_stage','due_date','description','tasks','profession_id','craft_id','severity_level','condition_list','plan_id','message']);
            if(!empty(trim($formData['description'])))
            $formData['description'] = '['.date('d/m/Y').'] '.$formData['description'].PHP_EOL;
            $formData['due_date'] = DateTime::createFromFormat('d/m/Y',$formData['due_date'])->getTimestamp();
            $formData['space_id'] = $place->spaces->where('id','=',(int)$formData['space_id'])->find()->id;
            $message = $formData['message'];
            if($formData['status'] != Enum_QualityControlStatus::Invalid){
                $formData['severity_level']= $formData['condition_list'] = null;
            }
            try{
                Database::instance()->begin();
                if(empty($formData['tasks'])){
                    throw new HDVP_Exception('Choose Tasks');
                }
                $this->project->makeProjectPaths();

                if(!empty($this->files()) AND !empty($this->files()['images'])){
                    foreach ($this->files()['images'] as $key => $image){
                        $uploadedFiles[] = [
                            'name' => str_replace($this->project->qualityControlPath().DS,'',Upload::save($image,null,$this->project->qualityControlPath())),
                            'original_name' => $image['name'],
                            'ext' => Model_File::getFileExt($image['name']),
                            'mime' => $image['type'],
                            'path' => str_replace(DOCROOT,'',$this->project->qualityControlPath()),
                            'token' => md5($image['name']).base_convert(microtime(false), 10, 36),
                        ];
                    }
                }
                $qc = ORM::factory('QualityControl');
                $qc->values($formData);
                $qc->place_id = $place->id;
                $qc->project_id = $place->project_id;
                $qc->object_id = $place->object_id;
                $qc->floor_id = $place->floor_id;
                $qc->place_type = $place->type;
                $qc->save();
                if(!empty($uploadedFiles)){
                    foreach ($uploadedFiles as $idx => $image){
                        $image = ORM::factory('Image')->values($image)->save();
                        $qc->add('images', $image->pk());

                        $img = new JBZoo\Image\Image($this->project->qualityControlPath().DS.$image->name);
                        $img->saveAs($this->project->qualityControlPath().DS.$image->name,50);
                    }
                }
                $imgData = $this->getNormalizedPostArr('images');
                if(!empty($imgData)){
                    foreach ($imgData as $img){
                        if(isset($img['name'])){
                            $imgData = Arr::extract($img,['source','name']);
                            $image = $this->saveBase64Image($imgData['source'],$imgData['name'],$qc->project->qualityControlPath());
                            $qc->add('images', $image->pk());
                        }else{
                            if(!isset($img['id'])) throw  new HTTP_Exception_404;
                            $imgData = Arr::extract($img,['source','id']);
                            $file = ORM::factory('PlanFile',$imgData['id']);
                            if( ! $file->loaded()) throw new HDVP_Exception('Incorrect file Identifier');
                            $filename = $file->getName();
                            $tmp = explode('.',$filename);
                            if(count($tmp) > 1){
                                unset($tmp[count($tmp)-1]);
                            }
                            $filename = implode('.',$tmp).'.png';
                            $image = $this->saveBase64Image($imgData['source'],$filename,$qc->project->qualityControlPath());
                            $qc->add('images', $image->pk());
                        }

                    }
                }
                $qc->add('tasks',$formData['tasks']);
                if(!empty(trim($message)))
                    ORM::factory('QcComment')->values(['message' => $message, 'qcontrol_id' => $qc->pk()])->save();
                Database::instance()->commit();
                $this->setResponseData('struct',$this->getObjectStruct($place->object_id));
                $this->setResponseData('triggerEvent','qualityControlCreated');
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
            $scopes = [];
            $plans = [];

            foreach ($place->plans->order_by('id','DESC')->find_all() as $item){
                if(in_array($item->scope,$scopes)) continue;
                $scopes[] = $item->scope;
                $plans[$item->id] = $item;
            }

            foreach($place->floor->plans->order_by('id','DESC')->find_all() as $item){
                if(in_array($item->scope,$scopes)) continue;
                $scopes[] = $item->scope;
                $plans[$item->id] = $item;
            }

//            $placePlans = [];
//            foreach ($plans as $pp){
//                if($pp->place_id == $place->id){
//                    $placePlans[$pp->id] = $pp;
//                }
//            }
//
//            if(!empty($placePlans)){
//                $plans = $placePlans;
//                unset($placePlans);
//            }

            $this->setResponseData('modal',View::make('projects/quality-controls/form',
                [
                    'item' => $place,
                    'plans' => $plans,
                    'tasks' =>$this->project->tasks->where('status','=',Enum_Status::Enabled)->find_all(),
                    'usedTasks' => $this->project->usedTasks($place->id),
                ]));
        }

    }

    public function action_quality_control_delete(){
        $this->_checkForAjaxOrDie();
        $qcId = (int)$this->request->param('id');
        $qc = ORM::factory('QualityControl',$qcId);
        if( ! $qc->loaded()){
            throw new HTTP_Exception_404;
        }
        if( ! $this->_user->is('super_admin')){
            throw new HTTP_Exception_403();
        }
        $qc->delete();
    }

    public function action_update_quality_control_message(){
        $this->_checkForAjaxOrDie();
        $this->auto_render = false;
        $qcId = (int)$this->request->param('param1');
        $commentId = (int)$this->request->param('param2');
        $qc = ORM::factory('QualityControl',$qcId);
        if(!$qc->loaded()){
            throw new HTTP_Exception_404();
        }

        $comment = $qc->comments->where('id','=',$commentId)->find();

        if(!$comment->loaded()){
            throw new HTTP_Exception_404();
        }

        $comment->message = Arr::get($_POST,'message');
        $comment->save();
    }

    public function action_update_quality_control_image(){
        $this->_checkForAjaxOrDie();
        $this->auto_render = false;
        $qcId = (int)$this->request->param('param1');
        $fileId = (int)$this->request->param('param2');
        $qc = ORM::factory('QualityControl',$qcId);
        if(!$qc->loaded()){
            throw new HTTP_Exception_404();
        }

        $file = $qc->images->where('id','=',$fileId)->find();

        if(!$file->loaded()){
            throw new HTTP_Exception_404();
        }
        try{
            $data = explode( ',', $this->post()['source']);
            if(count($data) != 2){
                throw new HDVP_Exception('Operation Error');
            }
            $file->replaceSourceWithBase64String($data[1],100);
        }catch (HDVP_Exception $e){
            $this->_setErrors($e->getMessage());
        }catch (Exception $e){
            $this->_setErrors('Operation Error');
        }

    }

    public function action_update_quality_control_plan_image(){
        $this->auto_render = false;
        $qcId = (int)$this->request->param('param1');
        $fileId = (int)$this->request->param('param2');
        $qc = ORM::factory('QualityControl',$qcId);
        if(!$qc->loaded()){
            throw new HTTP_Exception_404();
        }

        $file = ORM::factory('PlanFile',$fileId);

        if(!$file->loaded()){
            throw new HTTP_Exception_404();
        }

        $mime = $file->mime != 'application/pdf' ? $file->mime : 'image/jpeg';
        $filepath = $file->getImageLink();
        if($this->request->method() === Request::POST){
            $this->_checkForAjaxOrDie();
            try{
                $data = explode( ',', $this->post()['source']);
                if(count($data) != 2){
                    throw new HDVP_Exception('Operation Error');
                }
                $source = base64_decode($data[1]);
                $fifo = finfo_open();
                $mime1 = finfo_buffer($fifo, $source, FILEINFO_MIME_TYPE);
                if($mime1 != $mime){
                    throw new HDVP_Exception(__('invalid_mime_type'));
                }
                $tmpName = tempnam(sys_get_temp_dir(), Text::random());
                if(!file_put_contents($tmpName,$source)){
                    throw new HDVP_Exception(__('cant_write_file'));
                }
                $ext = File::ext_by_mime($mime);
                if(!Valid::check_file_signature($tmpName,$ext)){
                    throw new HDVP_Exception(__('cant_write_file'));
                }
                try{
                    Database::instance()->begin();
                    $imgk = new Imagick($tmpName);
                    $imgk2 = new Imagick($filepath);
                    $imgk->setImageFormat ($imgk2->getImageFormat());
                    unset($imgk2);
                    $qcPath = $qc->project->qualityControlPath();
                    $tmpName1 = explode('.',$file->original_name);
                    if(count($tmpName1) > 1)
                    unset($tmpName1[count($tmpName1)-1]);
                    if(in_array($ext,['jpe','jpeg'])){
                        $ext = 'jpg';
                    }
                    $origName = (implode('.',$tmpName1).'.'.$ext);
                    $name = uniqid('cpy_').'.'.$ext;
                    $imgk->writeImage(rtrim($qcPath,DS).DS.$name);
                    unset($imgk);
                    unlink($tmpName);
                    $f = ORM::factory('Image');
                    $f->name = $name;
                    $f->original_name = $origName;
                    $f->mime = $mime;
                    $f->ext = $ext;
                    $f->path = str_replace(DOCROOT,'',rtrim($qcPath,DS));
                    $f->token = md5($name).base_convert(microtime(false), 10, 36);
                    $f->status = Enum_FileStatus::Active;
                    $f->save();
                    $qc->add('images', $f->pk());
                    Database::instance()->commit();
                    $fs = new FileServer();
                    $fs->addSimpleImageTask('https://qforb.net/' . $f->path . '/' . $f->name,$f->id);
                    $this->setResponseData('images',
                        View::make('projects/quality-controls/images-list',
                            [
                                'items' => $qc->images->where('status','=',Enum_FileStatus::Active)->order_by('id','DESC')->find_all(),
                                'projectId' => $qc->project_id,
                                'qcId' => $qc->id
                            ]
                        )
                    );
                }catch (ORM_Validation_Exception $e){
                    Database::instance()->rollback();
                    @unlink($tmpName);
                    //var_dump($e->errors('validation'));
                    throw new HDVP_Exception('Operation Error');
                }
            }catch (HDVP_Exception $e){
                Database::instance()->rollback();
                @unlink($tmpName);
                $this->_setErrors($e->getMessage());
            }catch (Exception $e){
                Database::instance()->rollback();
                @unlink($tmpName);
                $this->_setErrors('Operation Error');
            }

        }else{//Вывод файла
            $this->response->headers([
               'Content-Type' => $mime,
               //'Content-Length' => filesize($file->fullFilePath()),
            ]);
            readfile($filepath);
        }

    }

    public function action_add_quality_control_image_from_raw_data(){
        $this->_checkForAjaxOrDie();
        $qcId = $this->request->param('id');
        $qc = ORM::factory('QualityControl',$qcId);
        if(!$qc->loaded()){
            throw new HTTP_Exception_404();
        }
        $fs = new FileServer();
        try{
            Database::instance()->begin();
            $f = $this->saveBase64Image($this->post()['source'],$this->post()['name'],$qc->project->qualityControlPath());
            $qc->add('images', $f->pk());
            Database::instance()->commit();
            $fs->addSimpleImageTask('https://qforb.net/' . $f->path . '/' . $f->name,$f->pk());
            $this->setResponseData('filePath',$f->originalFilePath());
            $this->setResponseData('id',$f->id);
        }catch (ORM_Validation_Exception $e){
            Database::instance()->rollback();
            throw new HDVP_Exception('Operation Error');
        }catch (HDVP_Exception $e){
            Database::instance()->rollback();
            $this->_setErrors($e->getMessage());
        }catch (Exception $e){
            Database::instance()->rollback();
            $this->_setErrors('Operation Error');
        }
    }

    public function saveBase64Image($base64String, $name, $path,$quality = 50){
        $data = explode( ',', $base64String);
        if(count($data) != 2 OR empty($name)){
            throw new HDVP_Exception('Operation Error');
        }

        $img = new JBZoo\Image\Image($base64String);
        $name = uniqid().'.jpg';
        $img->saveAs(rtrim($path,DS).DS.$name,$quality);

        $f = ORM::factory('Image');
        $f->name = $name;
        $f->original_name = $name;
        $f->mime = 'image/jpeg';
        $f->ext = 'jpg';
        $f->path = str_replace(DOCROOT,'',rtrim($path,DS));
        $f->token = md5($name).base_convert(microtime(false), 10, 36);
        $f->status = Enum_FileStatus::Active;
        $f->save();
        return $f;
    }

    public function action_update_certifications(){

        $this->_stdCheck();

        $certificationsData = $this->getNormalizedPostArr('certification');

        //удаляем явно не валидные данные
//        foreach ($certificationsData as $key => $val){
//            if(!trim($val['name']) AND !is_numeric($key)){
//                unset($certificationsData[$key]);
//            }
//        }
        if($this->project->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->project->id,192)){
            $this->_setErrors('Invalid request');
        }
        else if(empty($certificationsData)){
            $this->_setErrors('No changes detect for update');
        }else{
            $uploadedFiles = [];
            $certArr = ['added' => [], 'edited' => [], 'files' => []];
            $dir = $this->project->certificationsPath();
            try{
                Database::instance()->begin();
                $this->project->makeProjectPaths();
                if(!empty($this->files()) AND !empty($this->files()['files'])){
                    foreach ($this->files()['files'] as $key => $val){
                        foreach ($val as $file){
                            $uploadedFiles[$key][] = [
                                'name' => str_replace($dir.DS,'',Upload::save($file,null,$dir)),
                                'original_name' => $file['name'],
                                'ext' => Model_File::getFileExt($file['name']),
                                'mime' => $file['type'],
                                'path' => str_replace(DOCROOT,'',$dir),
                                'token' => md5($file['name']).base_convert(microtime(false), 10, 36),
                            ];
                        }
                    }
                }

                foreach ($certificationsData as $certId => $cd){

                    $certData = Arr::extract($cd,['craft_id','name']);
                    $certData['date'] = time();
                    if(!$certData['date']){
                        throw new HDVP_Exception('Incorrect date');
                    }
                    if(!empty($certData['craft_id'])){
                        $craft = ORM::factory('CmpCraft')->where('id','=',$certData['craft_id'])->and_where('company_id','=',$this->project->company->id)->find();
                        if( ! $craft->loaded()){
                            throw new HDVP_Exception('Incorrect craft identifier');
                        }

                        $certData['craft_id'] = $craft->id;
                    }else{
                        unset($certData['craft_id']);
                    }

                    if(is_numeric($certId)){//обновление
                        $cert = ORM::factory('PrCertification',$certId);

                        if( ! $cert->loaded() OR $cert->project_id != $this->project->id){
                            throw new HDVP_Exception('Incorrect certification identifier'.$certId);
                        }

                        if(empty($uploadedFiles[$certId]) AND $cert->name == $certData['name']){
                            continue;
                        }

                        if(empty($uploadedFiles[$certId])){
                            unset($certData['date']);
                        }else{
                            $certData['approved_by'] = null;
                            $certData['approved_at'] = null;
                        }

                        $cert->values($certData);
                        if($cert->changed()){
                            $certArr['updated'][] = $cert;
                        }
                        $cert->save();

                        if(!empty($uploadedFiles[$certId])){
                            foreach ($uploadedFiles[$certId] as $file){
                                $file = ORM::factory('StandardFile')->values($file)->save();
                                $certArr['files'][] = $file;
                                $cert->add('files', $file->pk());
                            }
                        }


                    }else{//создание
                        if(empty($uploadedFiles[$certId]) AND empty(trim($certData['name']))){
                            continue;
                        }
                        $cert = ORM::factory('PrCertification');
                        $cert->project_id = $this->project->id;
                        $cert->values($certData);
                        $cert->save();

                        if(!empty($uploadedFiles[$certId])){
                            foreach ($uploadedFiles[$certId] as $file){
                                $file = ORM::factory('CertificationFile')->values($file)->save();
                                $certArr['files'][] = $file;
                                $cert->add('files', $file->pk());
                            }
                        }
                    }
                }

                //выстреливаем события
                foreach($certArr as $key => $val){
                    if(!empty($val)){
                        foreach ($val as $item){
                            if($key == 'added'){
                                Event::instance()->fire('onCertificationAdded',['sender' => $this,'item' => $item]);
                                Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $item]);
                            }
                            else if($key == 'files'){
                                Event::instance()->fire('onFileUploaded',['sender' => $this,'item' => $item]);
                            }else{
                                Event::instance()->fire('onCertificationUpdated',['sender' => $this,'item' => $item]);
                                Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $item]);
                            }
                        }
                    }
                }
                $this->setResponseData('certificationsForm',View::make('projects/certifications/form',
                    ['action' => URL::site('projects/update_certifications/'.$this->project->id),
                        'crafts' => $this->project->company->crafts->where('status', '=', Enum_Status::Enabled)->find_all(),
                        'certs' => $this->project->certifications->where('craft_id','IS',DB::expr('NULL'))->find_all(),
                        'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192)
                    ]));
                $this->setResponseData('triggerEvent','certificationsUpdated');
                Database::instance()->commit();
            }catch(ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
                if(!empty($uploadedFiles) AND is_array($uploadedFiles)){
                    foreach ($uploadedFiles as $uf){
                        if(!empty($uf) AND is_array($uf)){
                            foreach ($uf as $f){
                                @unlink($f['name']);
                            }
                        }
                    }
                }
            }catch(HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
                if(!empty($uploadedFiles) AND is_array($uploadedFiles)){
                    foreach ($uploadedFiles as $uf){
                        if(!empty($uf) AND is_array($uf)){
                            foreach ($uf as $f){
                                @unlink($f['name']);
                            }
                        }
                    }
                }
            }catch (Exception $e){
                Database::instance()->rollback();
                $this->_setErrors('Operation Error');
            }
        }
    }
    public function action_certification_files(){
        if(!(int)$this->request->param('param1') OR !(int)$this->request->param('param2')){
            throw new HTTP_Exception_404;
        }
        $certId = (int)$this->request->param('param2');
        $projectId = (int)$this->request->param('param1');
        $this->_checkForAjaxOrDie();
        $this->project = ORM::factory('Project',$projectId);
        if( ! $this->project->loaded()){
            throw new HTTP_Exception_404;
        }
        $item = ORM::factory('PrCertification',['id' => $certId, 'project_id' => $projectId]);
        if( ! $item->loaded()){
            throw new HTTP_Exception_404;
        }
        if($this->request->method() == HTTP_Request::POST) {
            if(empty($this->files())){
                throw new HTTP_Exception_404;
            }
            $uploadedFiles = [];
            $stdArr = ['files' => []];
            $dir = DOCROOT.'media/data/projects/'.$this->project->id.'/certifications';
            View::set_global('_PROJECT', $this->project);
            try{
                Database::instance()->begin();
                if( ! is_dir($dir)){
                    mkdir($dir,0777,true);
                }

                if(!empty($this->files()) AND !empty($this->files()['files'])){
                    foreach ($this->files()['files'] as $key => $file){
                        $uploadedFiles[] = [
                            'name' => str_replace($dir.DS,'',Upload::save($file,null,$dir)),
                            'original_name' => $file['name'],
                            'ext' => Model_File::getFileExt($file['name']),
                            'mime' => $file['type'],
                            'path' => str_replace(DOCROOT,'',$dir),
                            'token' => md5($file['name']).base_convert(microtime(false), 10, 36),
                        ];
                    }
                }



                if(!empty($uploadedFiles)){
                    foreach ($uploadedFiles as $file){
                        $file = ORM::factory('StandardFile')->values($file)->save();
                        $stdArr['files'][] = $file;
                        $item->add('files', $file->pk());
                    }
                    $item->approval_status = Enum_CertificationsApprovalStatus::Waiting;
                    $item->save();
                }
                Database::instance()->commit();
                $this->setResponseData('modal',
                    View::make('modals/simple-modal',
                        [
                            'content' => View::make('projects/certifications/files',
                                [
                                    'action' => URL::site('projects/certification_files/'.$this->project->id.'/'.$item->id),
                                    'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192),
                                    'item' => $item,
                                    'files' => $item->files->where('status','=',Enum_FileStatus::Active)->find_all(),
                                    'downloadLinkUri' => URL::site('/projects/download_certification_file/'.$projectId,'https'),
                                    'deleteLinkUri' => URL::site('/projects/delete_certification_file/'.$projectId,'https')
                                ]
                            )
                        ]
                    )
                );
                $this->setResponseData('triggerEvent','renewCertificationModal');
                $this->setResponseData('certificationsForm',View::make('projects/certifications/form',
                    ['action' => URL::site('projects/update_certifications/'.$this->project->id),
                        'crafts' => $this->project->company->crafts->where('status', '=', Enum_Status::Enabled)->find_all(),
                        'certs' => $this->project->certifications->where('craft_id','IS',DB::expr('NULL'))->find_all(),
                        'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192)
                    ]));
            }catch(ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
                if(!empty($uploadedFiles) AND is_array($uploadedFiles)){
                    foreach ($uploadedFiles as $uf){
                        @unlink($uf['name']);
                    }
                }
            }catch(HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
                if(!empty($uploadedFiles) AND is_array($uploadedFiles)){
                    foreach ($uploadedFiles as $uf){
                        @unlink($uf['name']);
                    }
                }
            }catch (Exception $e){
                Database::instance()->rollback();throw $e;
                $this->_setErrors('Operation Error');
            }


        }else{
            $this->setResponseData('modal',
                View::make('modals/simple-modal',
                    [
                        'content' => View::make('projects/certifications/files',
                            [
                                'action' => URL::site('projects/certification_files/'.$this->project->id.'/'.$item->id),
                                'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192),
                                'item' => $item,
                                'files' => $item->files->where('status','=',Enum_FileStatus::Active)->find_all(),
                                'downloadLinkUri' => URL::site('/projects/download_certification_file/'.$projectId,'https').'/',
                                'deleteLinkUri' => URL::site('/projects/delete_certification_file/'.$projectId,'https').'/'
                            ]
                        )
                    ]
                )
            );
        }
    }
    public function action_download_certification_file(){
        $this->auto_render = false;
        $token = trim($this->request->param('token'));
        $projectId = (int) $this->request->param('param1');
        $certId = (int) $this->request->param('param2');
        $cert = ORM::factory('PrCertification',['id' =>$certId, 'project_id' => $projectId]);

        if( !$cert->loaded()){
            throw new HTTP_Exception_404;
        }
        $file =$cert->files->where('token','=',$token)->find();

        if( ! $file->loaded() OR !file_exists($file->path.'/'.$file->name) OR !is_file($file->path.'/'.$file->name)){
            throw new HTTP_Exception_404;
        }

        // ограничиваем скорость скачивания файла в килобайтах (=> 10 kb/s)
        $downloadSpeed = 150;

        header('Cache-control: private');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: filename='.rawurlencode($file->original_name));
        header('Content-Disposition: filename*=utf-8\'\''.rawurlencode($file->original_name));
        header("Accept-Ranges: bytes");
        $range = 0;
        $size = filesize($file->path.'/'.$file->name);
        if(isset($_SERVER['HTTP_RANGE'])) {
            list($a, $range)=explode("=",$_SERVER['HTTP_RANGE']);
            str_replace($range, "-", $range);
            $size2 = $size - 1;
            $new_length = $size - $range;
            header("HTTP/1.1 206 Partial Content");
            header("Content-Length: $new_length");
            header("Content-Range: bytes $range$size2/$size");
        } else {
            $size2=$size-1;
            header("Content-Range: bytes 0-$size2/$size");
            header("Content-Length: ".$size);
        }

        flush();
        $f = fopen($file->path.'/'.$file->name, "r");

        while(!feof($f))
        {
            echo fread($f, round( ($downloadSpeed ?: 1024 * 3) * 1024));
            flush();
            ob_flush();
            sleep(1);
        }
        fclose($f);
    }

    public function action_delete_certification_file(){
        $this->_checkForAjaxOrDie();
        $token = trim($this->request->param('token'));
        $projectId = (int) $this->request->param('param1');
        $certId = (int) $this->request->param('param2');
        $cert = ORM::factory('PrCertification',['id' =>$certId, 'project_id' => $projectId]);

        if( !$cert->loaded()){
            throw new HTTP_Exception_404;
        }
        $file =$cert->files->where('token','=',$token)->find();

        if( ! $file->loaded() OR !file_exists($file->path.'/'.$file->name) OR !is_file($file->path.'/'.$file->name)){
            throw new HTTP_Exception_404;
        }
        $file->status = Enum_FileStatus::Deleted;
        $file->save();
    }

    public function action_delete_quality_control_file(){
        $this->_checkForAjaxOrDie();
        $token = trim($this->request->param('token'));
        $projectId = (int) $this->request->param('param1');
        $qcId = (int) $this->request->param('param2');
        $qc = ORM::factory('QualityControl',['id' =>$qcId, 'project_id' => $projectId]);

        if( !$qc->loaded()){
            throw new HTTP_Exception_404;
        }
        $file = $qc->images->where('token','=',$token)->find();

        if( ! $file->loaded()){
            throw new HTTP_Exception_404;
        }
        $file->status = Enum_FileStatus::Deleted;
        $file->save();
    }
    public function action_update_links(){
        $this->_stdCheck();
        if($this->project->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->project->id,192)){
            throw new HDVP_Exception('Invalid request');
        }
        $linksData = $this->getNormalizedPostArr('link');

        //удаляем явно не валидные данные
        foreach ($linksData as $key => $val){
            if(!trim($val['name']) AND !is_numeric($key)){
                unset($linksData[$key]);
            }
        }

        if(!empty($linksData)){
            try{
                Database::instance()->begin();
                foreach ($linksData as $lid => $l){
                    $link = ORM::factory('Link',is_numeric($lid) ? (int)$lid : null);
                    $link->values($l,['name','url']);
                    $link->save();
                    if( ! is_numeric($lid))
                        $linkIds []= $link->id;
                }
                if(!empty($linkIds))
                    $this->project->add('links',$linkIds);
                Database::instance()->commit();
                $this->setResponseData('linksForm',View::make('projects/links/form',
                    ['action' => URL::site('projects/update_links/'.$this->project->id),
                        'items' => $this->project->links->order_by('id','DESC')->find_all(),
                        'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192)
                    ]));
                $this->setResponseData('triggerEvent','linksUpdated');
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
    }


    public function action_delete_link(){
        if(!(int)$this->request->param('param1') OR !(int)$this->request->param('param2')){
            throw new HTTP_Exception_404;
        }
        $lnkId = (int)$this->request->param('param2');
        $projectId = (int)$this->request->param('param1');
        $this->_checkForAjaxOrDie();
        if($this->request->method() != HTTP_Request::GET) {
            throw new HTTP_Exception_404;
        }
        $this->project = ORM::factory('Project',$projectId);
        if( ! $this->project->loaded() OR !$this->_user->canUseProject($this->project)){
            throw new HTTP_Exception_404;
        }

        $item = $this->project->links->where('id','=',$lnkId)->find();
        if( ! $item->loaded()){
            throw new HTTP_Exception_404;
        }

        $item->delete();
        View::set_global('_PROJECT', $this->project);
        $this->setResponseData('linksForm',View::make('projects/links/form',
            ['action' => URL::site('projects/update_links/'.$this->project->id),
                'items' => $this->project->links->order_by('id','DESC')->find_all(),
                'secure_tkn' => AesCtr::encrypt($this->project->id.Text::random('alpha'),$this->project->id,192)
            ]));
        $this->setResponseData('triggerEvent','linksUpdated');
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

    protected function getObjectStruct($objectId){
//        $output = Cache::instance('file')->get('object_'.$objectId.'_struct');
//        if(!$output){
//            $object = ORM::factory('PrObject',$objectId);
//            $floors = $object->floors->order_by('number','DESC')->with('places')->find_all();
//            $output = View::make('projects/property/struct/form',['item' => $object, 'itemFloors' => $floors])->render();
//            Cache::instance('file')->set('object_'.$objectId.'_struct',$output,Date::DAY);
//        }
//
//
//        return $output;
        $object = ORM::factory('PrObject',$objectId);
            $floors = $object->floors->order_by('number','DESC')->with('places')->find_all();
        return View::make('projects/property/struct/form',['item' => $object, 'itemFloors' => $floors])->render();
    }

    protected function getFloorsArray($floors,$places){
        $output = [];

        for($i = 1; $i <= ceil($places / $floors); $i++ ){
            for ($j = 1; $j <= $floors; $j++){
                if(array_sum($output) == $places) break;
                if(!isset($output[$j])){
                    $output[$j] = 1;
                }else{
                    $output[$j] +=1;
                }
            }
        }

        if(count($output) >= 3){
            if($output[1] > $output[count($output)]){
                for($i = 2; $i < count($output); $i++){
                    if($output[$i] < $output[1]){
                        if($output[count($output)] == 1) break;
                        while ($output[count($output)] > 1 AND $output[$i] < $output[1]){
                            $output[$i] +=1;
                            $output[count($output)] -=1;
                        }
                    }
                }
            }
        }

        return $output;
    }

    protected function _stdCheck(){

        if(($this->request->method() != HTTP_Request::POST) OR !(int)$this->request->param('id')){
            throw new HTTP_Exception_404;
        }
        $this->_checkForAjaxOrDie();
        $this->project = ORM::factory('Project',(int)$this->request->param('id'));
        if( ! $this->project->loaded()){
            throw new HTTP_Exception_404;
        }
        $this->company = $this->project->company;
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }

        View::set_global('_PROJECT', $this->project);
        View::set_global('_COMPANY', $this->company);
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
    public function action_tasks()
    {
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        $project = ORM::factory('Project',$id);
        if( ! $project->loaded()){
            throw new HTTP_Exception_404;
        }

        $translations = [
            "enabled" => __("task_enabled"),
            "disabled" => __("task_disabled"),
            "enable" => __("task_enable"),
            "disable" => __("task_disable"),
            "all" => __("All"),
            "copy_to" => __("Copy to"),
            "copy" => __("Copy"),
            "show" => __("Show"),
            "save" => __("Save"),
            "modules" => __("Modules"),
            "task" => __("Task"),
            "tasks" => __("Tasks"),
            "task_description" => __("Task_description"),
            "select_specialty" => __("Select_specialty"),
            "select_module" => __('Select Module'),
            "select_company" => __("Select Company"),
            "select_project" => __("Select project"),
            "enter_task_description" => __("Enter task description"),
            "select_all" => __('select all'),
            "unselect_all" => __('unselect all'),
            "confirm" => __('Confirm'),
            "close" => __('Close'),
            "quality_control" => __('Quality control'),
            "delivery_report" => __('task_deliver_report'),
            "lab_control" => __('Lab control'),
            "approve_element" => __('Approve element'),
        ];

        VueJs::instance()->addComponent('tasks/tasks-list');
        VueJs::instance()->addComponent('tasks/task-item');
        VueJs::instance()->includeMultiselect();
        Breadcrumbs::add(Breadcrumb::factory()->set_title($project->name)->set_url(URL::site('projects/update/'.$project->id)));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__("Tasks")));


        $this->template->content = View::make('tasks/tasks-list', ['projectId' => $project->id, 'translations' => $translations]);
    }

}