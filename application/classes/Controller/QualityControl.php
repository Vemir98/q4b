<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 17.11.2016
 * Time: 5:43
 */
class Controller_QualityControl extends HDVP_Controller_Template
{
    protected $_actions_perms = [
        'create' => [
            'GET' => 'read',
            'POST' => 'create'
        ],
        'get_objects,get_places,get_place_data,get_places_for_floor,get_plans' => [
            'GET' => 'read'
        ]
    ];

    public $company, $project;

    public function action_create(){

        View::set_global('_PROJECT', $this->project);
        if($this->request->method() == HTTP_Request::POST){
            $placeId = (int)Arr::get($this->post(),'place_id');
            $place = ORM::factory('PrPlace',$placeId);
            if( ! $place->loaded()){
                throw new HTTP_Exception_404;
            }
             $this->project = $place->project;

            $this->_checkForAjaxOrDie();
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

            $projects = ORM::factory('Project');
            if($this->_user->getRelevantRole('outspread') != Enum_UserOutspread::General){
                $projects->where('client_id','=',$this->_user->client_id);
            }
            if( ! $this->_user->priorityLevelIn(Enum_UserPriorityLevel::Company) AND $this->_user->priorityLevelIn(Enum_UserPriorityLevel::Project)){
                $projects = $this->_user->projects;
            }

            $projects->order_by('name','ASC');

            $this->template->content = View::make('qc/create-form',[
                'projects' => $projects->find_all()
            ]);
        }

    }

    public function action_get_objects(){
        $projectId = (int)$this->request->param('id');
        $project = ORM::factory('Project',$projectId);
        if( ! $project->loaded()){
            throw new HTTP_Exception_404;
        }

        $places = [];
        foreach($project->places->find_all() as $place){
            $places[] = [
                'id' => $place->id,
                'name' => str_replace("'"," ",$place->name.' ('.$place->custom_number.') '.__('str_').' '.$place->object->name.' '.__('floor').' '.$place->floor->number),
            ];
        }
        $this->setResponseData('places',json_encode($places));
        $this->setResponseData('items',View::make('qc/objects-select-options',['items' => $project->objects->find_all()])->render());
    }

    public function action_get_places(){
        $objectId = (int)$this->request->param('id');
        $object = ORM::factory('PrObject',$objectId);
        if( ! $object->loaded()){
            throw new HTTP_Exception_404;
        }
        $places = [];
        foreach($object->places->find_all() as $place){
            $places[] = [
                'id' => $place->id,
                'name' => $place->name.' ('.$place->custom_number.')',
            ];
        }
        $this->setResponseData('items',json_encode($places));
    }

    public function action_get_places_for_floor(){
        $objectId = (int)$this->request->param('param1');
        $floorNumber = (int)$this->request->param('param2');
        $object = ORM::factory('PrObject',$objectId);
        if( ! $object->loaded()){
            throw new HTTP_Exception_404;
        }
        $floor = $object->floors->where('number','=',$floorNumber)->find();
        if( ! $floor->loaded()){
            throw new HTTP_Exception_404;
        }
        $places = [];
        foreach($floor->places->find_all() as $place){
            $places[] = [
                'id' => $place->id,
                'name' => $place->name.' ('.$place->custom_number.')',
            ];
        }
        $this->setResponseData('items',json_encode($places));
    }

    public function action_get_place_data(){
        $placeId = (int)$this->request->param('id');
        $place = ORM::factory('PrPlace',$placeId);
        if( ! $place->loaded()){
            throw new HTTP_Exception_404;
        }
        $project = $place->project;
        $output = [];
        $floor = $place->floor;
        $output = [
            'placeNumber' => $place->number,
            'customNumber' => $place->custom_number,
            'floor' => $floor->number
        ];



        $output['crafts'] = View::make('qc/crafts-select-options',['item' => $project])->render();
        $output['tasks'] = View::make('qc/tasks-select-options',['project' => $project,'items' => $project->tasks->where('status','=',Enum_Status::Enabled)->find_all()])->render();
        $output['professions'] = View::make('qc/professions-select-options',['item' => $project])->render();
        $output['spaces'] = View::make('qc/spaces-select-options',['spaces' => $place->spaces->find_all()])->render();
        $output['object'] = $place->object->id;

        $this->setResponseData('item',json_encode($output));
    }

    public function action_get_plans(){
        $placeId = (int)$this->request->param('param1');
        $craftId = (int)$this->request->param('param2');
        $place = ORM::factory('PrPlace',$placeId);
        if( ! $place->loaded()){
            throw new HTTP_Exception_404;
        }

        $craft = ORM::factory('CmpCraft',$craftId);
        if( ! $craft->loaded()){
            throw new HTTP_Exception_404;
        }

        $scopes = $plans = $output = [];

        foreach ($place->plans->order_by('id','DESC')->find_all() as $item){
            if(in_array($item->scope,$scopes)) continue;
            $scopes[] = $item->scope;
            if(!$item->crafts->where('craft_id','=',$craftId)->count_all()){
                continue;
            }
            $plans[$item->id] = $item;
        }

        foreach($place->floor->plans->order_by('id','DESC')->find_all() as $item){
            if(in_array($item->scope,$scopes)) continue;
            $scopes[] = $item->scope;
            if(!$item->crafts->where('craft_id','=',$craftId)->count_all()){
                continue;
            }
            $plans[$item->id] = $item;
        }

//        foreach ($craft->plans->where('place_id','=',$place->id)->order_by('id','DESC')->find_all() as $item){
//            if(in_array($item->scope,$scopes)) continue;
//            $scopes[] = $item->scope;
//            $plans[$item->id] = $item;
//        }
//
//        foreach($place->floor->plans->order_by('id','DESC')->find_all() as $item){
//            if(in_array($item->scope,$scopes)) continue;
//            $scopes[] = $item->scope;
//            $plans[$item->id] = $item;
//        }


        $output['planList'] = View::make('qc/plan-items',['plans' => $plans])->render();
        $this->setResponseData('item',json_encode($output));
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
}