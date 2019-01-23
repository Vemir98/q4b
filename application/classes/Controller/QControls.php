<?php defined('SYSPATH') OR die('No direct script access.');
//UPDATE pr_places pp SET pp.custom_number = IF(pp.type = 'public',CONCAT('PB',pp.number),CONCAT('N',pp.number)) WHERE pp.custom_number IS NULL OR pp.custom_number = ''
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 5:53
 */
class Controller_QControl extends HDVP_Controller_Template
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
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Projects'))->set_url('/quality_control'));
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

    public function action_property_item_quality_control_list(){
        $this->_checkForAjaxOrDie();
        $id = (int) $this->request->param('id');
        $status = $this->request->param('status');

        $place = ORM::factory('PrPlace',$id);
        if(!$place->loaded()){
            throw new HTTP_Exception_404();
        }

        $query = $place->quality_control;
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
            $query->where('approval_status','=',$status);
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
        $filepath = DOCROOT.ltrim($file->getImageLink(),'/');
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
                if($mime1 != $mime){var_dump($mime);
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
        try{
            Database::instance()->begin();
            $f = $this->saveBase64Image($this->post()['source'],$this->post()['name'],$qc->project->qualityControlPath());
            $qc->add('images', $f->pk());
            Database::instance()->commit();
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
        $file =$qc->images->where('token','=',$token)->find();

        if( ! $file->loaded() OR !file_exists($file->path.'/'.$file->name) OR !is_file($file->path.'/'.$file->name)){
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
}