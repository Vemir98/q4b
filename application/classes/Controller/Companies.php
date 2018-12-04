<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 22.09.2016
 * Time: 15:51
 */
class Controller_Companies extends HDVP_Controller_Template
{

    /**
     * если данный массив задан то
     * контроллер автоматически проверяет права пользователя
     * к рессурсу
     * пример:
     * [
    'index' => [
    'GET' => [
    'view'
    ],
    'POST' => [
    'create'
    ]
    ],
    'posts' => 'view'

    ]
     * @var array
     */
    protected $_actions_perms = [
        'list' => [ //список компаний
            'GET' => 'read'
        ],
        'create' => [
            'GET' => 'read',
            'POST' => 'create'
        ],
        'update' => [
            'GET' => 'read',
            'POST' => 'update'
        ],
        'update_crafts,update_professions,update_users,update_standards,update_links,standard_files' => [
            'POST' => 'update'
        ],
        'user_details,standard_files,download_standards_file,crafts_with_professions' => [
            'GET' => 'read'
        ],
        'reset_user_password,invite_user' => [
            'GET' => 'update'
        ],
        'delete_link,delete_standards_file' => [
            'GET' => 'delete'
        ]
    ];

    public $company;

    public function before()
    {
        parent::before();
        if ($this->auto_render === TRUE)
        {
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Companies'))->set_url('/companies'));
        }
    }

    public function action_list(){
        $this->_setUsrMinimalPriorityLvl(Enum_UserPriorityLevel::Corporate);
        $filterBy = $this->request->param('status');
        $sortBy = $this->request->param('sorting');
        $export = $this->request->param('export');

        $query = ORM::factory('Company');
        if($this->_user->getRelevantRole('outspread') != Enum_UserOutspread::General){
            $query->where('client_id','=',$this->_user->client_id);
        }
        if(!empty($filterBy)){
            $query->and_where('status','=',$filterBy);
        }
        if(!empty($sortBy)){
            $query->with('client');
            $query->order_by($sortBy,'ASC');
        }
        $filterCompanies = clone($query);
        $filterCompanies = $filterCompanies->order_by('name','ASC')->find_all();
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
            $as->getColumnDimension('B')->setWidth(10);
            $as->getColumnDimension('C')->setWidth(13);
            $as->getColumnDimension('D')->setWidth(15);
            $as->getColumnDimension('E')->setWidth(250);


            $sh = [
                1 => [__('Company name'),__('Projects'),__('Country'),__('Segment Type'), __('Company Description')],
            ];
            $ws->set_data($sh, false);
            foreach ($result['items'] as $item){
                $sh [] = [$item->name, $item->projects->count_all(), $item->country->name, __($item->client->type), $item->description];
            }

            $ws->set_data($sh, false);
            $first_letter = PHPExcel_Cell::stringFromColumnIndex(0);
            $last_letter = PHPExcel_Cell::stringFromColumnIndex(count($sh[1])-1);
            $header_range = "{$first_letter}1:{$last_letter}1";
            $ws->get_active_sheet()->getStyle($header_range)->getFont()->setSize(12)->setBold(true);
            $ws->send(['name'=>'report', 'format'=>'Excel5']);
        }else{
            $this->template->content = View::make('companies/list',$result + ['filterCompanies' => $filterCompanies]);
        }


    }

    /**
     * Создание новой компании
     */
    public function action_create(){

        if($this->request->method() == HTTP_Request::POST)
        {
            $this->_checkForAjaxOrDie();
            //todo:: добавить функционал для того чтоб можно было к существующему клиенту добавлять компании
            
            try{
                Database::instance()->begin();
                if(!$this->_user->client_id){
                    if($this->_user->getRelevantRole('outspread') == Enum_UserOutspread::General){
                        $client_id = ORM::factory('Client')->values($this->post(),['type'])->save()->pk();
                    }else{
                        throw new HDVP_Exception('Permission Denied!!!');
                    }

                }else{
                    $client_id = $this->_user->client_id;
                }


                //Проверяем прикреплён ли логотип к форме
                if(!empty($this->files()) AND isset($this->files()['logo'])){
                    $valid = Validation::factory($_FILES)->setValidationImageRules('logo');
                    if( !$valid->check()){
                        throw new ORM_Validation_Exception('validation', $valid);
                    }
                    $logo = Upload::save($_FILES['logo'],null,'media/data/companies/logos');
                }

                $this->company = ORM::factory('Company')->values($this->post(),['name','address','description','status','company_id','country_id']);
                if(!empty($logo)){
                    $this->company->set('logo',$logo);
                }
                $this->company->client_id = $client_id;
                $this->company->save();
                Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $this->company]);
                Database::instance()->commit();
                $this->makeRedirect('companies/update/'.$this->company->pk());
            }catch(ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch(HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }catch (Exception $e){
                Database::instance()->rollback();
               $this->_setErrors('Operation Error');

            }
            

        }else{
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('New company')));
            $countries = ORM::factory('Country')->find_all();
            
            $this->template->content = View::make('companies/create')
                ->set('countries',Model_Country::getKeyValuePair($countries))
                ->set('clientCountryId',Model_Country::getClientCountryId($countries));
        }


    }

    public function action_update(){
        if(!(int)$this->request->param('id')){
            throw new HTTP_Exception_404;
        }

        $this->company = ORM::factory('Company',$this->getUIntParamOrDie($this->request->param('id')));
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }

        View::set_global('_COMPANY', $this->company);
        if($this->request->method() == HTTP_Request::POST) {//var_dump($this->files()['logo']);die;
            $this->_checkForAjaxOrDie();
            //Проверяем запрос пришол с страницы редактирования именно этой компании или нет
            if($this->company->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->company->id,192)){
                $this->_setErrors('Invalid request');
            }else{
                try{
                    Database::instance()->begin();
                    //Проверяем прикреплён ли логотип к форме
                    if(!empty($this->files()) AND isset($this->files()['logo'][0])){
                        $valid = Validation::factory($_FILES)->setValidationImageRules('logo');
                        if( !$valid->check()){
                            throw new ORM_Validation_Exception('validation', $valid);
                        }
                        $logo = Upload::save($this->files()['logo'][0],null,'media/data/companies/logos');
                    }

                    $this->company->values($this->post(),['name','address','description','status','company_id','country_id']);
                    if(!empty($logo)){
                        //удаляем прежний логотип
                        if(!empty($this->company->logo)){
                            @unlink($this->company->logo);
                        }
                        $this->company->set('logo',$logo);
                    }
                    $this->company->save();
                    Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $this->company]);
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
                }
            }
            
        }
        else{// НЕ POST запрос

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->company->name));
            $countries = ORM::factory('Country')->find_all();

            $roles = $this->_user->getMyAndLowerRoles();
            $userRoles = null;
            $cmpClientType = $this->company->client->type;
            if(count($roles)){
                foreach ($roles as $r){
                    if(($cmpClientType != Enum_ClientType::Corporate AND $r->outspread == Enum_UserOutspread::Corporate) OR $r->outspread == Enum_UserOutspread::General){
                        continue;
                    }
                    $userRoles[$r->id] = $r->name;

                }
            }

            $this->template->content = View::make('companies/update')
                ->set('countries',Model_Country::getKeyValuePair($countries))
                ->set('clientCountryId',$this->company->country_id)
                ->set('company',$this->company)
                ->set('crafts',$this->company->crafts->order_by('id','DESC')->find_all())
                ->set('professions',$this->company->professions->order_by('id','DESC')->find_all())
                ->set('professionsSelectedCrafts',ORM::factory('CmpProfession')->getProfCraftsIdsKeyValPairArray($this->company->id))
                ->set('userRoles',$userRoles)
                ->set('users',ORM::factory('User')->where('client_id','=',$this->company->client->id)->and_where('company_id','=',$this->company->id)->order_by('id','DESC')->find_all())
                ->set('standards',$this->company->standards->find_all());
        }
            
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

        if($this->company->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->company->id,192)){
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
                    $craft = ORM::factory('CmpCraft',is_numeric($cid) ? $this->getUIntParamOrDie($cid) : null);
                    $craft->company_id = $this->company->id;
                    if($craft->related_id){
                        $craft->values($c,['catalog_number','status']);
                    }else{
                        $craft->values($c,['name','catalog_number','status']);
                    }
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
                Model_CmpProfession::disableProfessionsWithoutRelation($this->company->id);
                //выстреливаем события
                foreach($craftsArr as $key => $val){
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

                $crafts = $this->company->crafts->order_by('id','DESC')->find_all();
                $professions = $this->company->professions->order_by('id','DESC')->find_all();
                $this->setResponseData('craftsForm',View::make('companies/crafts/form',['action' => URL::site('companies/update_crafts/'.$this->company->id),
                    'items' => $crafts,
                    'secure_tkn' => AesCtr::encrypt($this->company->id.Text::random('alpha'),$this->company->id,192)
                ]));
                $this->setResponseData('professionsForm',View::make('companies/professions/form',
                    ['action' => URL::site('companies/update_professions/'.$this->company->id),
                        'items' => $professions,
                        'items_crafts' => ORM::factory('CmpProfession')->getProfCraftsIdsKeyValPairArray($this->company->id),
                        'crafts' => $crafts,
                        'secure_tkn' => AesCtr::encrypt($this->company->id.Text::random('alpha'),$this->company->id,192)
                    ]));
                $this->setResponseData('usersForm',View::make('companies/users/form',
                    ['action' => URL::site('companies/update_users/'.$this->company->id),
                        'items' => ORM::factory('User')->where('client_id','=',$this->company->client->id)->and_where('company_id','=',$this->company->id)->order_by('id','DESC')->find_all(),
                        'professions' => $professions,
                        'roles' => Auth::instance()->get_user()->getMyAndLowerRolesAsKeyValPairArray(),
                        'secure_tkn' => AesCtr::encrypt($this->company->id.Text::random('alpha'),$this->company->id,192)
                    ]));
                $this->setResponseData('triggerEvent','craftsUpdated');
                Database::instance()->commit();
                //todo: вернуть контент табов для специализаций и профессий
            }catch(ORM_Validation_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->errors('validation'));
            }catch(HDVP_Exception $e){
                Database::instance()->rollback();
                $this->_setErrors($e->getMessage());
            }catch (Exception $e){
                Database::instance()->rollback();
                $this->_setErrors('Operation Error');
            }
        }
    }

    public function action_crafts_with_professions(){
        $this->_checkForAjaxOrDie();
        $this->company = ORM::factory('Company',$this->getUIntParamOrDie($this->request->param('id')));
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }

        View::set_global('_COMPANY', $this->company);
        $this->setResponseData('craftsList',
        View::make('companies/crafts/crafts-list',[
           'items' => $this->company->craftsWithProfessionsFlag()
        ]));
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

        if($this->company->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->company->id,192)){
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
                    $profession = ORM::factory('CmpProfession',is_numeric($pid) ? $this->getUIntParamOrDie($pid) : null);
                    $profession->company_id = $this->company->id;
                    if($profession->related_id){
                        $profession->values($c,['catalog_number','status']);
                    }else{
                        $profession->values($c,['name','catalog_number','status']);
                    }
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
                            }else{
                                Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $item]);
                            }
                        }
                    }
                }

                $crafts = $this->company->crafts->order_by('id','DESC')->find_all();
                $professions = $this->company->professions->order_by('id','DESC')->find_all();
                $this->setResponseData('professionsForm',View::make('companies/professions/form',
                    ['action' => URL::site('companies/update_professions/'.$this->company->id),
                        'items' => $professions,
                        'items_crafts' => ORM::factory('CmpProfession')->getProfCraftsIdsKeyValPairArray($this->company->id),
                        'crafts' => $crafts,
                        'secure_tkn' => AesCtr::encrypt($this->company->id.Text::random('alpha'),$this->company->id,192)
                    ]));

                $this->setResponseData('usersForm',View::make('companies/users/form',
                    ['action' => URL::site('companies/update_users/'.$this->company->id),
                        'items' => ORM::factory('User')->where('client_id','=',$this->company->client->id)->and_where('company_id','=',$this->company->id)->order_by('id','DESC')->find_all(),
                        'professions' => $professions,
                        'roles' => Auth::instance()->get_user()->getMyAndLowerRolesAsKeyValPairArray(),
                        'secure_tkn' => AesCtr::encrypt($this->company->id.Text::random('alpha'),$this->company->id,192)
                    ]));
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
            }
        }
    }

    /**
     * Обновление/Добавление пользователей
     */
    public function action_update_users(){

        $this->_stdCheck();
        if(($this->request->method() != HTTP_Request::POST AND in_array($this->_user->getRelevantRole('name'),['company_infomanager','corporate_infomanager']))){
            throw new HTTP_Exception_403();
        }
        $usersData = $this->getNormalizedPostArr('user');
        //удаляем явно не валидныых новых пользователей
        foreach ($usersData as $key => $val){
            if(!trim($val['email']) AND !trim($val['name']) AND !is_numeric($key)){
                unset($usersData[$key]);
            }
        }
        if($this->company->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->company->id,192)){
            $this->_setErrors('Invalid request');
        }
        else if(empty($usersData)){
            $this->_setErrors('No changes detect for update');
        }else{
            $usersArr = ['added' => [], 'edited' => [], 'logout' => []];
            $allowedRoles = $this->_user->getMyAndLowerRoles();
            $cmpProfs = $this->company->professions->find_all();
            $loginRole = ORM::factory('Role')->where('name','=','login')->find()->id;
            try{
                Database::instance()->begin();
                foreach ($usersData as $uid => $u){
                    $u = Arr::extract($u,['name','email','phone','role','profession','status']);
                    $usrProf = $usrRole = null;
                    foreach ($allowedRoles as $urole){
                        if($u['role'] == $urole->id){
                            $usrRole = $urole;
                            unset($u['role']);
                            break;
                        }
                    }
                    foreach ($cmpProfs as $cprof){
                        if($u['profession'] == $cprof->id){
                            $usrProf = $cprof;
                            unset($u['profession']);
                            break;
                        }
                    }

                    if( ! ($usrRole instanceof ORM)){
                        throw new HDVP_Exception('Incorrect role');
                    }

                    if( ! ($usrProf instanceof ORM)){
                        throw new HDVP_Exception('Incorrect profession');
                    }

                    if(is_numeric($uid)){//обновление пользователя
                        $user = ORM::factory('user',$this->getUIntParamOrDie($uid));
                        if(!$user->loaded()){
                            throw new HDVP_Exception('Incorrect user identifier');
                        }
                        if($this->_user->getRelevantRole('outspread') != Enum_UserOutspread::General){
                            if($user->client_id != $this->company->client->pk()){
                                //todo: тревога!!! блокировка пользователя
                                throw new HDVP_Exception('Incorrect user client identifier');
                            }

                            if((bool)$this->_user->company_id AND $this->_user->company_id != $user->company_id){
                                //todo: тревога!!! блокировка пользователя
                                throw new HDVP_Exception('Incorrect user company identifier');
                            }

                        }

                        if($user->email != $u['email'] OR $user->getRelevantRole('id') != $usrRole->id){
                            $usersArr['logout'][$user->id] = $user;
                        }

                        $user->set('username',$u['email'])
                            ->set('name',$u['name'])
                            ->set('status',$u['status'])
                            ->set('email',$u['email']);
                        if(!empty($u['phone'])){
                            $user->set('phone',$u['phone']);
                        }

                        if($user->getRelevantRole()->outspread != Enum_RoleOutspread::Super AND $usrRole->outspread == Enum_RoleOutspread::Super){
                            throw new HDVP_Exception('Can\'t upgrade user standard role to general');
                        }
                        if($user->changed() OR !empty($usersArr['logout'][$user->id])){
                            $usersArr['edited'][] = $user;
                        }
                        
                    }else{//создание пользователя
                        $user = ORM::factory('User');
                        $user->set('username',$u['email'])
                            ->set('name',$u['name'])
                            ->set('email',$u['email'])
                            ->set('password',Text::random())
                            ->set('company_id',$this->company->id);
                        if($this->_user->getRelevantRole('outspread') != Enum_UserOutspread::General){
                            if($this->_user->client_id != $this->company->client->pk()){
                                //todo: тревога!!! блокировка пользователя
                                throw new HDVP_Exception('Incorrect user client identifier');
                            }

                            if((bool)$this->_user->company_id AND $this->_user->company_id != $this->company->id){
                                //todo: тревога!!! блокировка пользователя
                                throw new HDVP_Exception('Incorrect user company identifier');
                            }
                            $user->set('client_id',$this->company->client->id);
                        }else{
                            $user->set('client_id',$this->company->client->id);
                        }
                        

                    }
                    if($usrRole->outspread == Enum_RoleOutspread::Super){
                        throw new HDVP_Exception('Cant\'t create or update user with general role');
                    }
//                    if(!in_array($usrRole->outspread,[Enum_RoleOutspread::Company,Enum_RoleOutspread::Project])){
//                        $user->company_id = $this->company->id;
//                    }else{
//                        $user->company_id = 0;
//                    }

                    $user->save();

                    if(is_numeric($uid)){//обновление пользователя
                        if($user->getRelevantRole('id') != $usrRole->id){
                            $user->remove('roles',$user->getRelevantRole('id'));
                            $user->add('roles',$usrRole->id);
                        }
                        if($user->getProfession('id') != $usrProf->id){
                            $user->addProfession($usrProf->id);
                        }

                    }else{//создание пользователя
//                        Model_UToken::makeRegistrationToken($user->id);
                        $user->add('roles',[$usrRole->id,$loginRole]);
                        $user->addProfession($usrProf->id);
                        $usersArr['added'][] = $user;
                    }
                }

                //выстреливаем события
                foreach($usersArr as $key => $val){
                    if(!empty($val)){
                        foreach ($val as $item){
                            if($key == 'added'){
                                Event::instance()->fire('onUserAdded',['sender' => $this,'item' => $item]);
                                Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $item]);
                            }
                            else if($key == 'logout'){
                                Event::instance()->fire('onAuthExpires',['sender' => $this,'item' => $item]);
                            }else{
                                Event::instance()->fire('onUserUpdated',['sender' => $this,'item' => $item]);
                                Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $item]);
                            }
                        }
                    }
                }

                $respUsers = ORM::factory('User')->where('client_id','=',$this->company->client->id)->and_where('company_id','=',$this->company->id)->order_by('id','DESC')->find_all();
                $roles = $this->_user->getMyAndLowerRoles();
                $userRoles = null;
                $cmpClientType = $this->company->client->type;
                if(count($roles)){
                    foreach ($roles as $r){
                        if(($cmpClientType != Enum_ClientType::Corporate AND $r->outspread == Enum_UserOutspread::Corporate) OR $r->outspread == Enum_UserOutspread::General){
                            continue;
                        }
                        $userRoles[$r->id] = $r->name;

                    }
                }
                $this->setResponseData('usersForm',View::make('companies/users/form',
                    ['action' => URL::site('companies/update_users/'.$this->company->id),
                        'items' => $respUsers,
                        'professions' => $this->company->professions->where('status','=',Enum_Status::Enabled)->order_by('id','DESC')->find_all(),
                        'roles' => $userRoles,
                        'secure_tkn' => AesCtr::encrypt($this->company->id.Text::random('alpha'),$this->company->id,192)
                    ]));
                $this->setResponseData('standardsForm',View::make('companies/standards/form',
                    ['action' => URL::site('companies/update_standards/'.$this->company->id),
                        'items' => $this->company->standards->find_all(),
                        'users' => $respUsers,                        
                        'secure_tkn' => AesCtr::encrypt($this->company->id.Text::random('alpha'),$this->company->id,192)
                    ]));
                $this->setResponseData('triggerEvent','usersUpdated');

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
        //var_dump($usersData);
    }

    public function action_update_standards(){

        $this->_stdCheck();

        $standardsData = $this->getNormalizedPostArr('standard');

        //удаляем явно не валидные данные
        foreach ($standardsData as $key => $val){
            if(!trim($val['name']) AND !is_numeric($key)){
                unset($standardsData[$key]);
            }
        }
        if($this->company->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->company->id,192)){
            $this->_setErrors('Invalid request');
        }
        else if(empty($standardsData)){
            $this->_setErrors('No changes detect for update');
        }else{
            $uploadedFiles = [];
            $stdArr = ['added' => [], 'edited' => [], 'files' => []];
            $dir = DOCROOT.'media/data/companies/'.$this->company->id.'/standards';
            try{
                Database::instance()->begin();
                if( ! is_dir($dir)){
                    mkdir($dir,0777,true);
                }

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

                foreach ($standardsData as $stdId => $sd){
                    $stdData = Arr::extract($sd,['name','organisation','number','submission_place','responsible_person']);
                    if(is_numeric($stdId)){//обновление
                        $std = ORM::factory('CmpStandard',$this->getUIntParamOrDie($stdId));
                        
                        if( ! $std->loaded() OR $std->company_id != $this->company->id){
                            throw new HDVP_Exception('Incorrect standard identifier');
                        }
                        
                        $user = ORM::factory('User',$stdData['responsible_person']);
                        //todo: спросить какие пользователи могут быть ответственными, кто в данной компании или и те кто в корпорации
                        if( ! $user->loaded() OR ($user->company_id != $this->company->id AND $user->client_id != $this->company->client->id)){
                            throw new HDVP_Exception('Incorrect user identifier');
                        }

                        $stdData['responsible_person'] = $user->id;
                        
                        $std->values($stdData);
                        if($std->changed()){
                            $stdArr['updated'][] = $std;
                        }
                        $std->save();

                        if(!empty($uploadedFiles[$stdId])){
                            foreach ($uploadedFiles[$stdId] as $file){
                                $file = ORM::factory('StandardFile')->values($file)->save();
                                $stdArr['files'][] = $file;
                                $std->add('files', $file->pk());
                            }
                        }


                    }else{//создание
                        $std = ORM::factory('CmpStandard');

                        $user = ORM::factory('User',$stdData['responsible_person']);
                        if( ! $user->loaded() OR $user->company_id != $this->company->id OR $user->client_id != $this->company->client->id){
                            throw new HDVP_Exception('Incorrect user identifier');
                        }

                        $stdData['responsible_person'] = $user->id;
                        $stdData['company_id'] = $this->company->id;

                        $std->values($stdData);
                        $std->save();
                        
                        if(!empty($uploadedFiles[$stdId])){
                            foreach ($uploadedFiles[$stdId] as $file){
                                $file = ORM::factory('StandardFile')->values($file)->save();
                                $stdArr['files'][] = $file;
                                $std->add('files', $file->pk());
                            }
                        }
                    }
                }

                //выстреливаем события
                foreach($stdArr as $key => $val){
                    if(!empty($val)){
                        foreach ($val as $item){
                            if($key == 'added'){
                                Event::instance()->fire('onStandardAdded',['sender' => $this,'item' => $item]);
                                Event::instance()->fire('onItemAdded',['sender' => $this,'item' => $item]);
                            }
                            else if($key == 'files'){
                                Event::instance()->fire('onFileUploaded',['sender' => $this,'item' => $item]);
                            }else{
                                Event::instance()->fire('onStandardUpdated',['sender' => $this,'item' => $item]);
                                Event::instance()->fire('onItemUpdated',['sender' => $this,'item' => $item]);
                            }
                        }
                    }
                }
                $this->setResponseData('standardsForm',View::make('companies/standards/form',
                    ['action' => URL::site('companies/update_standards/'.$this->company->id),
                        'items' => $this->company->standards->find_all(),
                        'users' => ORM::factory('User')->where('client_id','=',$this->company->client->id)->and_where('status','=',Enum_UserStatus::Active)->find_all(),
                        'secure_tkn' => AesCtr::encrypt($this->company->id.Text::random('alpha'),$this->company->id,192)
                    ]));
                $this->setResponseData('triggerEvent','standardsUpdated');
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

    public function action_update_links(){
        $this->_stdCheck();
        if($this->company->id != (int)AesCtr::decrypt(Arr::get($this->post(),'secure_tkn'),$this->company->id,192)){
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
                    $link = ORM::factory('Link',is_numeric($lid) ? $this->getUIntParamOrDie($lid) : null);
                    $link->values($l,['name','url']);
                    $link->save();
                    if( ! is_numeric($lid))
                    $linkIds []= $link->id;
                }
                if(!empty($linkIds))
                $this->company->add('links',$linkIds);
                Database::instance()->commit();
                $this->setResponseData('linksForm',View::make('companies/links/form',
                    ['action' => URL::site('companies/update_links/'.$this->company->id),
                        'items' => $this->company->links->order_by('id','DESC')->find_all(),
                        'secure_tkn' => AesCtr::encrypt($this->company->id.Text::random('alpha'),$this->company->id,192)
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
        if(!(int)$this->request->param('company_id') OR !(int)$this->request->param('id')){
            throw new HTTP_Exception_404;
        }
        $lnkId = $this->getUIntParamOrDie($this->request->param('id'));
        $companyId = $this->getUIntParamOrDie($this->request->param('company_id'));
        $this->_checkForAjaxOrDie();
        if($this->request->method() != HTTP_Request::GET) {
            throw new HTTP_Exception_404;
        }
        $this->company = ORM::factory('Company',$companyId);
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }

        $item = $this->company->links->where('id','=',$lnkId)->find();
        if( ! $item->loaded()){
            throw new HTTP_Exception_404;
        }

        $item->delete();
        View::set_global('_COMPANY', $this->company);
        $this->setResponseData('linksForm',View::make('companies/links/form',
            ['action' => URL::site('companies/update_links/'.$this->company->id),
                'items' => $this->company->links->order_by('id','DESC')->find_all(),
                'secure_tkn' => AesCtr::encrypt($this->company->id.Text::random('alpha'),$this->company->id,192)
            ]));
        $this->setResponseData('triggerEvent','linksUpdated');
    }



    public function action_user_details(){
        if(!(int)$this->request->param('company_id') OR !(int)$this->request->param('id')){
            throw new HTTP_Exception_404;
        }
        $userId = $this->getUIntParamOrDie($this->request->param('id'));
        $companyId = $this->getUIntParamOrDie($this->request->param('company_id'));
        //todo: доработать чтобы работало правильно
        if($this->_user->client_id AND $this->_user->company_id AND $this->_user->company_id != $companyId){
            throw new HTTP_Exception_404;
        }
        $this->_checkForAjaxOrDie();
        $this->company = ORM::factory('Company',$companyId);
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }

        $item = ORM::factory('User',['id' => $userId, 'company_id' => $companyId]);
        if( ! $item->loaded()){
            throw new HTTP_Exception_404;
        }


        $this->setResponseData('modal',
            View::make('modals/simple-modal',
                [
                    'content' => View::make('companies/users/details',
                        [
                            'action' => URL::site('companies/update_users/'.$this->company->id),
                            'secure_tkn' => AesCtr::encrypt($this->company->id.Text::random('alpha'),$this->company->id,192),
                            'item' => $item,
                            'professions' => $this->company->professions->order_by('id','DESC')->find_all(),
                            'roles' => Auth::instance()->get_user()->getMyAndLowerRolesAsKeyValPairArray(),
                            'companyId' => $companyId
                        ]
                    )
                ]
            )
        );
    }

    public function action_invite_user(){
        if(!(int)$this->request->param('company_id') OR !(int)$this->request->param('id')){
            throw new HTTP_Exception_404;
        }
        $userId = $this->getUIntParamOrDie($this->request->param('id'));
        $companyId = $this->getUIntParamOrDie($this->request->param('company_id'));
        //todo: доработать чтобы работало правильно
        if($this->_user->client_id AND $this->_user->company_id AND $this->_user->company_id != $companyId){
            throw new HTTP_Exception_404;
        }
        $this->_checkForAjaxOrDie();
        $this->company = ORM::factory('Company',$companyId);
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }

        $item = ORM::factory('User',['id' => $userId, 'company_id' => $companyId]);
        if( ! $item->loaded() OR $item->status != Enum_UserStatus::Pending){
            throw new HTTP_Exception_404;
        }
        Event::instance()->fire('onInviteUser',['sender' => $this,'item' => $item]);
    }

    public function action_reset_user_password(){
        if(!(int)$this->request->param('company_id') OR !(int)$this->request->param('id')){
            throw new HTTP_Exception_404;
        }
        $userId = $this->getUIntParamOrDie($this->request->param('id'));
        $companyId = $this->getUIntParamOrDie($this->request->param('company_id'));
        //todo: доработать чтобы работало правильно
        if($this->_user->client_id AND $this->_user->company_id AND $this->_user->company_id != $companyId){
            throw new HTTP_Exception_404;
        }
        $this->_checkForAjaxOrDie();
        $this->company = ORM::factory('Company',$companyId);
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }

        $item = ORM::factory('User',['id' => $userId, 'company_id' => $companyId]);
        if( ! $item->loaded() OR $item->status != Enum_UserStatus::Active){
            throw new HTTP_Exception_404;
        }

        Event::instance()->fire('onPasswordReset',['sender' => $this,'item' => $item]);
    }


    public function action_standard_files(){
        if(!(int)$this->request->param('company_id') OR !(int)$this->request->param('id')){
            throw new HTTP_Exception_404;
        }
        $stdId = $this->getUIntParamOrDie($this->request->param('id'));
        $companyId = $this->getUIntParamOrDie($this->request->param('company_id'));
        $this->_checkForAjaxOrDie();
        $this->company = ORM::factory('Company',$companyId);
        if( ! $this->company->loaded()){
            throw new HTTP_Exception_404;
        }
        $item = ORM::factory('CmpStandard',['id' => $stdId, 'company_id' => $companyId]);
        if( ! $item->loaded()){
            throw new HTTP_Exception_404;
        }
        if($this->request->method() == HTTP_Request::POST) {
            if(empty($this->files())){
                throw new HTTP_Exception_404;
            }
            $uploadedFiles = [];
            $stdArr = ['files' => []];
            $dir = DOCROOT.'media/data/companies/'.$this->company->id.'/standards';
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
                }
                Database::instance()->commit();
                $this->setResponseData('modal',
                    View::make('modals/simple-modal',
                        [
                            'content' => View::make('companies/standards/files',
                                [
                                    'action' => URL::site('companies/standard_files/'.$this->company->id.'/'.$item->id),
                                    'secure_tkn' => AesCtr::encrypt($this->company->id.Text::random('alpha'),$this->company->id,192),
                                    'item' => $item,
                                    'files' => $item->files->where('status','=',Enum_FileStatus::Active)->find_all(),
                                    'downloadLinkUri' => URL::site('/companies/download_standards_file/'.$companyId).'/',
                                    'deleteLinkUri' => URL::site('/companies/delete_standards_file/'.$companyId).'/'
                                ]
                            )
                        ]
                    )
                );
                $this->setResponseData('triggerEvent','renewStandardModal');
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
                Database::instance()->rollback();
                $this->_setErrors('Operation Error');
            }


        }else{
            $this->setResponseData('modal',
                View::make('modals/simple-modal',
                    [
                        'content' => View::make('companies/standards/files',
                            [
                                'action' => URL::site('companies/standard_files/'.$this->company->id.'/'.$item->id),
                                'secure_tkn' => AesCtr::encrypt($this->company->id.Text::random('alpha'),$this->company->id,192),
                                'item' => $item,
                                'files' => $item->files->where('status','=',Enum_FileStatus::Active)->find_all(),
                                'downloadLinkUri' => URL::site('/companies/download_standards_file/'.$companyId),
                                'deleteLinkUri' => URL::site('/companies/delete_standards_file/'.$companyId)
                            ]
                        )
                    ]
                )
            );
        }
    }

    public function action_download_standards_file(){
        $this->auto_render = false;
        $token = trim($this->request->param('token'));
        $companyId = $this->getUIntParamOrDie($this->request->param('param1'));
        $standardId = $this->getUIntParamOrDie($this->request->param('param2'));
        $standard = ORM::factory('CmpStandard',['id' => $standardId, 'company_id' => $companyId]);

        if( ! $standard->loaded()){
            throw new HTTP_Exception_404;
        }
        $file = $standard->files->where('token','=',$token)->find();

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
    public function action_delete_standards_file(){
        $this->auto_render = false;
        $token = trim($this->request->param('token'));
        $companyId = $this->getUIntParamOrDie($this->request->param('param1'));
        $standardId = $this->getUIntParamOrDie($this->request->param('param2'));
        $standard = ORM::factory('CmpStandard',['id' => $standardId, 'company_id' => $companyId]);

        if( ! $standard->loaded()){
            throw new HTTP_Exception_404;
        }
        $file = $standard->files->where('token','=',$token)->find();

        if( ! $file->loaded() OR !file_exists($file->path.'/'.$file->name) OR !is_file($file->path.'/'.$file->name)){
            throw new HTTP_Exception_404;
        }
        $file->status = Enum_FileStatus::Deleted;
        $file->save();
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

        if(($this->request->method() != HTTP_Request::POST) OR !(int)$this->request->param('id')){
            throw new HTTP_Exception_404;
        }
        $this->_checkForAjaxOrDie();
        $this->company = ORM::factory('Company',$this->getUIntParamOrDie($this->request->param('id')));
        if( ! $this->company->loaded() OR empty($this->post())){
            throw new HTTP_Exception_404;
        }
        
        View::set_global('_COMPANY', $this->company);
    }
}