<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 14.09.2017
 * Time: 8:36
 */
class Controller_Consultants extends HDVP_Controller_Template
{
    public function before()
    {
        parent::before();
        $this->_setUsrMinimalPriorityLvl(Enum_UserPriorityLevel::Company);
        if ($this->auto_render === TRUE)
        {
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Consultants And Auditors'))->set_url('/consultants'));
        }
    }

    public function action_check_email(){
        $this->_checkForAjaxOrDie();
        if($this->request->method() == Request::POST){
            $email = Arr::get($this->post(),'email');
            $validation = new Validation(['email' => $email]);
            $validation->rule('email','not_empty');
            $validation->rule('email','email');
            if(!$validation->check()){
                $this->_setErrors($validation->errors('validation'));
            }else{
                $user = ORM::factory('User',['email' => $email]);
                if(!$user->loaded()){
                    $this->setResponseData('url',URL::site('consultants/create'));
                }else{
                    if($user->getRelevantRole('outspread') != Enum_UserOutspread::Project){
                        $this->_setErrors(['That User can\'t be consultant']);
                    }else{
                        $this->setResponseData('url',URL::site('consultants/update/'.$user->id));
                    }
                }
            }
        }else{
            $this->setResponseData('modal',View::make('consultants/check-email'));
        }

    }

    public function action_list(){
        if($this->_isAjax){
            throw new HTTP_Exception_404();
        }
        $projectID = (int)$this->request->param('project');
        if($projectID){
            $project = ORM::factory('Project',$projectID);
            if( ! $project->loaded()){
                throw new HTTP_Exception_404();
            }
        }

        $this->template->content = View::make('consultants/list',
            [
                'users' => ( ! $projectID) ? Model_Consultant::getAllUsers() : Model_Consultant::getAllUsersForProject($projectID),
                'selectedProject' => $projectID,
                'projects' => ORM::factory('Project')->order_by('name','ASC')->find_all()
            ]);
    }

    public function action_create(){
        $user = ORM::factory('User');
        if($this->request->method() == Request::POST){
            if($this->_user->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->_user->id,192)){
                $this->_setErrors('Invalid request');
            }
            $projectsData = $this->getNormalizedPostArr('project');

            try{
                if(!empty($projectsData)){
                    $projectsData = array_keys($projectsData);
                }else{
                    throw new HDVP_Exception("Projects can not be empty");
                }
                Database::instance()->begin();

                $clientData = Arr::extract($this->post(),
                    [
                        'email',
                        'name',
                        'phone',
                        'password',
                        'password_confirm'
                    ]);

                $passwordsValidation = Validation::factory($clientData);

                $passwordsValidation
                    ->rule('password', 'not_empty')
                    ->rule('password', 'min_length', array(':value', 8))
                    ->rule('password_confirm', 'matches', array(':validation', ':field', 'password'));

                if (!$passwordsValidation->check()) {
                    throw new HDVP_Exception("Password validation error");
                }


                $user->values($this->post(),['email','name','phone','password']);


                $user->username = $user->email;
                $user->status = Enum_UserStatus::Active;
                $user->save();
                $user->remove('projects');
                if(!empty($projectsData)){
                    foreach($projectsData as $projId){
                        $proj = ORM::factory('Project',$projId);
                        if( ! $proj->loaded()){
                            throw new HDVP_Exception('Incorrect Project');
                        }
                        $user->add('projects',$proj->id);
                    }
                }
                $user->add('roles',ORM::factory('Role')->where('name','=','login')->find()->id);
                $user->add('roles',Arr::get($this->post(),'role_id'));
//                Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $user]);
//                Event::instance()->fire('onUserAdded',['sender' => $this,'item' => $user]);
                Database::instance()->commit();
                $this->setResponseData('triggerEvent','usersListUpdated');
                $this->setResponseData('usersList',View::make('consultants/list',
                    [
                        'users' => Model_Consultant::getAllUsers(),
                    ]));
            }catch(ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch(Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }
        }else{
            $companies = $this->_user->availableCompanies();
            $projects = null;
            if(count($companies)){
                foreach ($companies as $comp){
                    $projects[$comp->id] = $comp->projects->find_all();
                }
            }
            $this->setResponseData('modal',View::make('consultants/create',[
                'user' => $user,
                'roles' => ORM::factory('Role')->where('outspread','=',Enum_UserOutspread::Project)->find_all(),
                'companies' => $companies,
                'projects' => $projects,
                'secure_tkn' => AesCtr::encrypt($this->_user->id.Text::random('alpha'),$this->_user->id,192)
            ]));
        }
    }

    public function action_update(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        $user = ORM::factory('User',$id);
        if(!$user->loaded()){
            throw new HTTP_Exception_404();
        }
        if($this->request->method() == Request::POST){
            if($user->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$user->id,192)){
                $this->_setErrors('Invalid request');
            }
            $projectsData = $this->getNormalizedPostArr('project');

            try{
                if(!empty($projectsData)){
                    $projectsData = array_keys($projectsData);
                }else{
                    throw new HDVP_Exception("Projects can not be empty");
                }
                Database::instance()->begin();

                $userData = Arr::extract($this->post(),['name','phone','password','password_confirm']);

                $passwordNotEmptyCheck = Validation::factory($userData);
                $passwordConfirmNotEmptyCheck = Validation::factory($userData);
                $passwordsValidationCheck = Validation::factory($userData);

                $passwordNotEmptyCheck
                    ->rule('password', 'not_empty');
                $passwordConfirmNotEmptyCheck
                    ->rule('password_confirm', 'not_empty');

                $passwordsValidationCheck
                    ->rule('password', 'not_empty')
                    ->rule('password', 'min_length', array(':value', 8))
                    ->rule('password_confirm', 'matches', array(':validation', ':field', 'password'));

                $user->set('name', $userData['name']);
                $user->set('phone', $userData['phone']);

                if($passwordNotEmptyCheck->check() || $passwordConfirmNotEmptyCheck->check()) {
                    if ($passwordsValidationCheck->check()) {
                        $user->set('password', $userData['password']);
                    } else {
                        throw new HDVP_Exception("Password validation error");
                    }
                }

                $user->save();
                $user->remove('projects');
                if(!empty($projectsData)){
                    foreach($projectsData as $projId){
                        $proj = ORM::factory('Project',$projId);
                        if( ! $proj->loaded()){
                            throw new HDVP_Exception('Incorrect Project');
                        }
                        $user->add('projects',$proj->id);
                    }
                }
                Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $user]);
                Event::instance()->fire('onUserUpdated',['sender' => $this,'item' => $user]);
                Database::instance()->commit();
                $this->setResponseData('triggerEvent','usersListUpdated');
                $this->setResponseData('usersList',View::make('consultants/list',
                    [
                        'users' => Model_Consultant::getAllUsers(),
                    ]));
            }catch(ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch(Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }
        }else{
            $companies = $this->_user->availableCompanies();
            $projects = null;
            $projectIds = null;
            if(count($companies)){
                foreach ($companies as $comp){
                    $projects[$comp->id] = $comp->projects->find_all();
                    if(!empty($projects[$comp->id])){
                        foreach ($projects[$comp->id] as $proj){
                            $projectIds[] = $proj->id;
                        }
                    }
                }
            }
            if(count($projectIds)){
                $projectIds = '('.implode(',',$projectIds).')';
                $tmp = $user->projects->where('id','IN',DB::expr($projectIds))->find_all();
                $projectIds = null;
                if(count($tmp)){
                    foreach ($tmp as $p){
                        $projectIds[] = $p->id;
                    }
                }
                unset($tmp);
            }else{
                $projectIds = null;
            }
            $this->setResponseData('modal',View::make('consultants/update',[
                'user' => $user,
                'userRole' => $user->getRelevantRole('name'),
                //'roles' => ORM::factory('Role')->where('outspread','=',Enum_UserOutspread::Project)->find_all(),
                'companies' => $companies,
                'projects' => $projects,
                'selectedProjects' => $projectIds,
                'secure_tkn' => AesCtr::encrypt($user->id.Text::random('alpha'),$user->id,192)
            ]));
        }
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