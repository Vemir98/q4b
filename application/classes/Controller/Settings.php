<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.06.2017
 * Time: 12:37
 */
class Controller_Settings extends HDVP_Controller_Template
{
    protected $_actions_perms = [
        'index,apply_to_all,apply_status,object_types,space_types,construct_elements,delete_space_type,test' => [
            'GET' => 'read'
        ],
        'update_crafts,update_professions,update_tasks,object_types,space_types,construct_elements' => [
            'POST' => 'update'
        ]
    ];
    public function before()
    {
        parent::before();
        if ($this->auto_render === TRUE)
        {
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Settings'))->set_url('/settings'));
        }
    }

    public function action_index(){
        //(new Job_Settings_ProcessNewData())->perform();
        $this->template->content = View::make('settings/update',
            [
                'crafts' => ORM::factory('Craft')->order_by('id','DESC')->find_all(),
                'professions' => ORM::factory('Profession')->order_by('id','DESC')->find_all(),
                'professionsSelectedCrafts' =>  ORM::factory('Profession')->getProfCraftsIdsKeyValPairArray(),
                'tasksSelectedCrafts' =>  ORM::factory('Task')->getTaskCraftsIdsKeyValPairArray(),
                'tasks' => ORM::factory('Task')->order_by('id','DESC')->find_all(),
                'tabsDisabled' => Settings::getValue('settingsMode') !== Enum_Status::Enabled
            ]
        );
    }

    /**
     * Обновление/Добавление специальностей
     */
    public function action_update_crafts(){

        $this->_stdCheck();

        $craftsData = $this->getNormalizedPostArr('craft');
        //удаляем явно не валидные новые крафты
        foreach ($craftsData as $key => $val){
            if(!trim($val['name']) AND !is_numeric($key)){
                unset($craftsData[$key]);
            }
        }

        if($this->_user->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->_user->id,192)){
            $this->_setErrors('Invalid request');
        }
        else if(empty($craftsData)){
            $this->_setErrors('No changes detect for update');
        }
        else{
            $craftsArr['added'] = $craftsArr['edited'] = [];
            try{
                Database::instance()->begin();
                foreach($craftsData as $cid => $c){
                    $craft = ORM::factory('Craft',is_numeric($cid) ? $this->getUIntParamOrDie($cid) : null);
                    $craft->values($c,['name','catalog_number','status']);
                    $craft->save();
                    if($craft->status == Enum_Status::Disabled){
                        $craft->remove('professions');
                    }
                    if(is_numeric($cid)){
                        $craftsArr['edited'][] = $craft;
                    }else{
                        $craftsArr['added'][] = $craft;
                    }
                }
                Model_Profession::disableProfessionsWithoutRelation();
                //выстреливаем события
                foreach($craftsArr as $key => $val){
                    if(!empty($val)){
                        foreach ($val as $item){
                            if($key == 'added'){
                                Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $item]);
                                Event::instance()->fire('onCraftAdded',['sender' => $this,'item' => $item]);
                            }else{
                                Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $item]);
                            }
                        }
                    }
                }

                $crafts = ORM::factory('Craft')->order_by('id','DESC')->find_all();
                $professions = ORM::factory('Profession')->order_by('id','DESC')->find_all();
                $this->setResponseData('craftsForm',View::make('settings/crafts/form',['action' => URL::site('settings/update_crafts/'),
                    'items' => $crafts,
                    'secure_tkn' => AesCtr::encrypt($this->_user->id.Text::random('alpha'),$this->_user->id,192)
                ]));
                $this->setResponseData('professionsForm',View::make('settings/professions/form',
                    ['action' => URL::site('settings/update_professions/'.$this->_user->id),
                        'items' => $professions,
                        'items_crafts' => ORM::factory('Profession')->getProfCraftsIdsKeyValPairArray(),
                        'crafts' => $crafts,
                        'secure_tkn' => AesCtr::encrypt($this->_user->id.Text::random('alpha'),$this->_user->id,192)
                    ]));
                $this->setResponseData('triggerEvent','craftsUpdated');
                Database::instance()->commit();
            }catch(ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch(HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }catch (Exception $e){
                Database::instance()->rollback();
                $this->_setErrors('Operation Error');
                //throw $e;
            }
        }
    }


    /**
     * Обновление/Добавление профессий
     */
    public function action_update_professions(){

        $this->_stdCheck();

        $professionsData = [];
        foreach ($this->post() as $key => $value){
            if(preg_match('~profession_(?<isNew>\+)?(?<id>[0-9]+)_(?<field>[a-z_]+)~',$key,$matches))
                if($matches['isNew']){
                    $professionsData['new_'.$matches['id']][$matches['field']] = $value;
                }else{
                    $professionsData[$matches['id']][$matches['field']] = $value;
                }

        }

        //удаляем явно не валидные новые профессии
        foreach ($professionsData as $key => $val){
            if(!trim($val['name']) AND !is_numeric($key)){
                unset($professionsData[$key]);
            }
        }

        if($this->_user->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->_user->id,192)){
            $this->_setErrors('Invalid request');
        }
        else if(empty($professionsData)){
            $this->_setErrors('No changes detect for update');
        }
        else{
            $professionsArr['added'] = $professionsArr['edited'] = [];
            try{
                Database::instance()->begin();
                foreach($professionsData as $pid => $c){
                    $profession = ORM::factory('Profession',is_numeric($pid) ? $this->getUIntParamOrDie($pid) : null);
                    $profession->values($c,['name','catalog_number','status']);
                    if(empty($c['crafts'])){
                        $profession->status = Enum_Status::Disabled;
                    }
                    $profession->save();
                    $profession->remove('crafts');
                    if(($profession->status != Enum_Status::Disabled) AND !empty($c['crafts'])){
                        $profession->add('crafts',$c['crafts']);
                    }
                    if(is_numeric($pid)){
                        $professionsArr['edited'][] = $profession;
                    }else{
                        $professionsArr['added'][] = $profession;
                    }
                }
                //выстреливаем события
                foreach($professionsArr as $key => $val){
                    if(!empty($val)){
                        foreach ($val as $item){
                            if($key == 'added'){
                                Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $item]);
                                Event::instance()->fire('onProfessionAdded',['sender' => $this,'item' => $item]);
                            }else{
                                Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $item]);
                            }
                        }
                    }
                }

                $crafts = ORM::factory('Craft')->order_by('id','DESC')->find_all();
                $professions = ORM::factory('Profession')->order_by('id','DESC')->find_all();
                $this->setResponseData('professionsForm',View::make('settings/professions/form',
                    ['action' => URL::site('settings/update_professions/'),
                        'items' => $professions,
                        'items_crafts' => ORM::factory('Profession')->getProfCraftsIdsKeyValPairArray(),
                        'crafts' => $crafts,
                        'secure_tkn' => AesCtr::encrypt($this->_user->id.Text::random('alpha'),$this->_user->id,192)
                    ]));

//                $this->setResponseData('usersForm',View::make('settings/users/form',
//                    ['action' => URL::site('settings/update_users/'.$this->company->id),
//                        'items' => ORM::factory('User')->where('client_id','=',$this->company->client->id)->and_where('company_id','=',$this->company->id)->order_by('id','DESC')->find_all(),
//                        'professions' => $professions,
//                        'roles' => Auth::instance()->get_user()->getMyAndLowerRolesAsKeyValPairArray(),
//                        'secure_tkn' => AesCtr::encrypt($this->company->id.Text::random('alpha'),$this->company->id,192)
//                    ]));
                $this->setResponseData('triggerEvent','professionsUpdated');
                Database::instance()->commit();
            }catch(ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch(HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }catch (Exception $e){
                Database::instance()->rollback();
                $this->_setErrors('Operation Error');
                //$this->_setErrors($e->getMessage());
            }
        }
    }

    public function action_update_tasks(){
        $this->_stdCheck();

        $tasksData = [];
        foreach ($this->post() as $key => $value){
            if(preg_match('~task_(?<isNew>\+)?(?<id>[0-9]+)_(?<field>[a-z_]+)~',$key,$matches))
                if($matches['isNew']){
                    $tasksData['new_'.$matches['id']][$matches['field']] = $value;
                }else{
                    $tasksData[$matches['id']][$matches['field']] = $value;
                }

        }

        //удаляем явно не валидные новые профессии
        foreach ($tasksData as $key => $val){
            if(!trim($val['name']) AND !is_numeric($key)){
                unset($tasksData[$key]);
            }
        }

        if($this->_user->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->_user->id,192)){
            $this->_setErrors('Invalid request');
        }
        else if(empty($tasksData)){
            $this->_setErrors('No changes detect for update');
        }
        else{
            $tasksArr['added'] = $tasksArr['edited'] = [];
            try{
                Database::instance()->begin();
                foreach($tasksData as $pid => $c){
                    $task = ORM::factory('Task',is_numeric($pid) ? $this->getUIntParamOrDie($pid) : null);
                    $task->values($c,['name','status']);
                    if(empty($c['crafts'])){
                        $task->status = Enum_Status::Disabled;
                    }
                    $task->save();
                    $task->remove('crafts');
                    if(!empty($c['crafts'])){
                        $task->add('crafts',$c['crafts']);
                    }
                    if(is_numeric($pid)){
                        $tasksArr['edited'][] = $task;
                    }else{
                        $tasksArr['added'][] = $task;
                    }
                }
                //выстреливаем события
                foreach($tasksArr as $key => $val){
                    if(!empty($val)){
                        foreach ($val as $item){
                            if($key == 'added'){
                                Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $item]);
                                Event::instance()->fire('onTaskAdded',['sender' => $this,'item' => $item]);
                            }else{
                                Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $item]);
                            }
                        }
                    }
                }

                $crafts = ORM::factory('Craft')->order_by('id','DESC')->find_all();
                $tasks = ORM::factory('Task')->order_by('id','DESC')->find_all();
                $this->setResponseData('tasksForm',View::make('settings/tasks/form',
                    ['action' => URL::site('settings/update_tasks/'),
                        'items' => $tasks,
                        'items_crafts' => ORM::factory('Task')->getTaskCraftsIdsKeyValPairArray(),
                        'crafts' => $crafts,
                        'secure_tkn' => AesCtr::encrypt($this->_user->id.Text::random('alpha'),$this->_user->id,192)
                    ]));

                $this->setResponseData('triggerEvent','tasksUpdated');
                Database::instance()->commit();
            }catch(ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch(HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }catch (Exception $e){
                Database::instance()->rollback();
//                $this->_setErrors('Operation Error');
                $this->_setErrors($e->getMessage());
            }
        }
    }

    public function action_object_types(){
        $this->_checkForAjaxOrDie();
        if($this->request->method() == Request::POST){
            $data = $this->getNormalizedPostArr('object');
            //удаляем явно не валидные данные
            foreach ($data as $key => $val){
                if(!trim($val['name']) AND !is_numeric($key)){
                    unset($data[$key]);
                }
            }

            if($this->_user->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->_user->id,192)){
                $this->_setErrors('Invalid request');
            }else if(empty($data)){
                $this->_setErrors('No changes detect for update');
            }else{
                $dataArr['added'] = $dataArr['edited'] = [];
                try{
                    Database::instance()->begin();
                    foreach($data as $dataId => $c){
                        $objectType = ORM::factory('PrObjectType',is_numeric($dataId) ? $this->getUIntParamOrDie($dataId) : null);
                        $objectType->values($c,is_numeric($dataId) ? ['name'] : ['name','alias']);
                        $objectType->save();
                        if(is_numeric($dataId)){
                            $dataArr['edited'][] = $objectType;
                        }else{
                            $dataArr['added'][] = $objectType;
                        }
                    }

                    //выстреливаем события
                    foreach($dataArr as $key => $val){
                        if(!empty($val)){
                            foreach ($val as $item){
                                if($key == 'added'){
                                    Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $item]);
                                }else{
                                    Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $item]);
                                }
                            }
                        }
                    }


                    $this->setResponseData('typesForm',View::make('settings/objects/types-form',[
                        'action' => URL::site('settings/object_types'),
                        'items' => ORM::factory('PrObjectType')->order_by('id','DESC')->find_all(),
                        'secure_tkn' => AesCtr::encrypt($this->_user->id.Text::random('alpha'),$this->_user->id,192)
                    ]));
                    $this->setResponseData('triggerEvent','objectTypesUpdated');
                    Database::instance()->commit();
                }catch(ORM_Validation_Exception $e){
                    Database::instance()->rollback();
                    $this->_setErrors($e->errors('validation'));
                }catch(HDVP_Exception $e){
                    Database::instance()->rollback();
                    $this->_setErrors($e->getMessage());
                }catch (Exception $e){
                    Database::instance()->rollback();
                    $this->_setErrors('Operation Error');
                    //throw $e;
                }
            }
        }else{
            $content = View::make('settings/objects/types',[
                'form' => View::make('settings/objects/types-form',[
                    'action' => URL::site('settings/object_types'),
                    'items' => ORM::factory('PrObjectType')->order_by('id','DESC')->find_all(),
                    'secure_tkn' => AesCtr::encrypt($this->_user->id.Text::random('alpha'),$this->_user->id,192)
                ])
            ]);
            $this->setResponseData('modal',$content->render());
        }
    }

    public function action_space_types(){
        $this->_checkForAjaxOrDie();
        if($this->request->method() == Request::POST){
            $data = $this->getNormalizedPostArr('space');
            //удаляем явно не валидные данные
            foreach ($data as $key => $val){
                if(!trim($val['name']) AND !is_numeric($key)){
                    unset($data[$key]);
                }
            }

            if($this->_user->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->_user->id,192)){
                $this->_setErrors('Invalid request');
            }else if(empty($data)){
                $this->_setErrors('No changes detect for update');
            }else{
                $dataArr['added'] = $dataArr['edited'] = [];
                try{
                    Database::instance()->begin();
                    foreach($data as $dataId => $c){
                        $objectType = ORM::factory('PrSpaceType',is_numeric($dataId) ? $this->getUIntParamOrDie($dataId) : null);
                        $objectType->values($c,['name']);
                        $objectType->save();
                        if(is_numeric($dataId)){
                            $dataArr['edited'][] = $objectType;
                        }else{
                            $dataArr['added'][] = $objectType;
                        }
                    }

                    //выстреливаем события
                    foreach($dataArr as $key => $val){
                        if(!empty($val)){
                            foreach ($val as $item){
                                if($key == 'added'){
                                    Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $item]);
                                }else{
                                    Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $item]);
                                }
                            }
                        }
                    }


                    $this->setResponseData('typesForm',View::make('settings/space/types-form',[
                        'action' => URL::site('settings/space_types'),
                        'items' => ORM::factory('PrSpaceType')->order_by('id','DESC')->find_all(),
                        'secure_tkn' => AesCtr::encrypt($this->_user->id.Text::random('alpha'),$this->_user->id,192)
                    ]));
                    $this->setResponseData('triggerEvent','spaceTypesUpdated');
                    Database::instance()->commit();
                }catch(ORM_Validation_Exception $e){
                    Database::instance()->rollback();
                    $this->_setErrors($e->errors('validation'));
                }catch(HDVP_Exception $e){
                    Database::instance()->rollback();
                    $this->_setErrors($e->getMessage());
                }catch (Exception $e){
                    Database::instance()->rollback();
                    $this->_setErrors('Operation Error');
                    //throw $e;
                }
            }
        }else{
            $content = View::make('settings/space/types',[
                'form' => View::make('settings/space/types-form',[
                    'action' => URL::site('settings/space_types'),
                    'items' => ORM::factory('PrSpaceType')->order_by('id','DESC')->find_all(),
                    'secure_tkn' => AesCtr::encrypt($this->_user->id.Text::random('alpha'),$this->_user->id,192)
                ])
            ]);
            $this->setResponseData('modal',$content->render());
        }
    }

    public function action_delete_space_type(){
        $this->_checkForAjaxOrDie();
        $id = (int) $this->request->param('id');
        $spaceType =  ORM::factory('PrSpaceType',$id);
        if( ! $spaceType->loaded() OR $id == 1){
            throw new HTTP_Exception_404();
        }
        $spaceType->delete();
    }

    public function action_construct_elements(){
        $this->_checkForAjaxOrDie();
        if($this->request->method() == Request::POST){
            $data = $this->getNormalizedPostArr('element');
            //удаляем явно не валидные данные
            foreach ($data as $key => $val){
                if(!trim($val['name']) AND !is_numeric($key)){
                    unset($data[$key]);
                }
            }

            if($this->_user->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->_user->id,192)){
                $this->_setErrors('Invalid request');
            }else if(empty($data)){
                $this->_setErrors('No changes detect for update');
            }else{
                $dataArr['added'] = $dataArr['edited'] = [];
                try{
                    Database::instance()->begin();
                    foreach($data as $dataId => $c){
                        $constructEl = ORM::factory('ConstructElement',is_numeric($dataId) ? $this->getUIntParamOrDie($dataId) : null);
                        if($constructEl->id == 1 OR $constructEl->id == 2) continue;
                        $constructEl->values($c,['name','icon','space_count']);
                        $constructEl->save();
                        if(is_numeric($dataId)){
                            $dataArr['edited'][] = $constructEl;
                        }else{
                            $dataArr['added'][] = $constructEl;
                        }
                    }

                    //выстреливаем события
                    foreach($dataArr as $key => $val){
                        if(!empty($val)){
                            foreach ($val as $item){
                                if($key == 'added'){
                                    Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $item]);
                                }else{
                                    Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $item]);
                                }
                            }
                        }
                    }


                    $this->setResponseData('typesForm',View::make('settings/construct-elements/form',[
                        'action' => URL::site('settings/construct_elements'),
                        'items' => ORM::factory('ConstructElement')->order_by('id','DESC')->find_all(),
                        'secure_tkn' => AesCtr::encrypt($this->_user->id.Text::random('alpha'),$this->_user->id,192)
                    ]));
                    $this->setResponseData('triggerEvent','constructElementsUpdated');
                    Database::instance()->commit();
                }catch(ORM_Validation_Exception $e){
                    Database::instance()->rollback();
                    $this->_setErrors($e->errors('validation'));
                }catch(HDVP_Exception $e){
                    Database::instance()->rollback();
                    $this->_setErrors($e->getMessage());
                }catch (Exception $e){
                    Database::instance()->rollback();
                    $this->_setErrors('Operation Error');
                    //throw $e;
                }
            }
        }else{
            $content = View::make('settings/construct-elements/elements',[
                'form' => View::make('settings/construct-elements/form',[
                    'action' => URL::site('settings/construct_elements'),
                    'items' => ORM::factory('ConstructElement')->order_by('id','DESC')->find_all(),
                    'secure_tkn' => AesCtr::encrypt($this->_user->id.Text::random('alpha'),$this->_user->id,192)
                ])
            ]);
            $this->setResponseData('modal',$content->render());
        }
    }

    public function action_apply_to_all(){
        if(file_exists(APPPATH.'local-storage/settings.txt')){
            $item = ORM::factory('Settings',['key' => 'settingsMode']);
            $item->val = Enum_Status::Disabled;
            Queue::enqueue('settings','Job_Settings_ProcessNewData',null,\Carbon\Carbon::now()->addSeconds(5)->timestamp);
            $item->save();
        }
        $this->makeRedirect(URL::withLang('settings',Language::getCurrent()->slug));
    }

    public function action_apply_status(){
        $this->_checkForAjaxOrDie();
        if($this->request->method() != Request::GET){
            throw new HTTP_Exception_404();
        }
        $this->setResponseData('status',(int)!file_exists(APPPATH.'local-storage/settings.txt'));
    }

    public function action_test(){
        $this->auto_render = false;
        $filePath = APPPATH.'local-storage/settings.txt';
        try{
            $rawData = file_get_contents($filePath);
            $data = null;
            if($rawData !== false){
                $data = json_decode($rawData,true);
                echo '<pre>';
                var_dump($data);
            }

            $data['crafts'][] = [23 => 'added'];
            $data['crafts'][] = [12 => 'updated'];
            file_put_contents($filePath,json_encode($data));
        }catch (Exception $e){
            var_dump($e->getMessage());
        }
    }

    protected function getNormalizedPostArr($arrKey){
        $output = [];
        foreach ($this->post() as $key => $value){
            if(preg_match('~'.$arrKey.'_(?<isNew>\+)?(?<id>[0-9]+)_(?<field>[a-z_]+)~',$key,$matches))
                if($matches['isNew']){
                    $output['new_'.$matches['id']][$matches['field']] = $value;
                }else{
                    $output[$matches['id']][$matches['field']] = $value;
                }

        }
        return $output;
    }
    protected function _stdCheck(){

        if(($this->request->method() != HTTP_Request::POST)){
            throw new HTTP_Exception_404;
        }
        $this->_checkForAjaxOrDie();
    }
}