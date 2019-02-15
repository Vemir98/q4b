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
            'POST' => 'update'
        ],
    ];
    public function action_create(){

//        $placeId = (int)$this->request->param('id');
//        $place = ORM::factory('PrPlace',$placeId);
//        if( ! $place->loaded()){
//            throw new HTTP_Exception_404;
//        }
       // $this->project = $place->project;
        View::set_global('_PROJECT', $this->project);
        if($this->request->method() == HTTP_Request::POST){
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
}