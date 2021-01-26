<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 19.09.2016
 * Time: 11:22
 */
class Model_User extends Model_Auth_User
{
    const Ios = 2;
    const Android = 1;

    protected $_has_many = array(
        'user_tokens' => array('model' => 'User_Token'),
        'roles'       => array('model' => 'Role', 'through' => 'roles_users'),
        'professions' => array('model' => 'CmpProfession', 'through' => 'users_cmp_professions', 'foreign_key' => 'user_id', 'far_key' => 'profession_id'),
        'utokens' => array('model' => 'UToken', 'foreign_key' => 'user_id'),
        'projects' => [
            'model' => 'Project',
            'foreign_key' => 'user_id',
            'far_key' => 'project_id',
            'through' => 'users_projects'
        ],
    );

    protected $_belongs_to = [
        'client' => [
            'model' => 'Client',
            'foreign_key' => 'client_id'
        ],
        'company' => [
            'model' => 'Company',
            'foreign_key' => 'company_id'
        ]
    ];

    /**
     * возвращает экземпляр Model_ACL
     * @return Model_Acl
     */
    protected function _acl(){
        return HDVP_Core::instance()->acl();
    }

    /**
     * Проверяет привилегии пользователя по отношению к определённому ресурсу
     * @param $perm
     * @param $resource
     * @return bool
     */
    public function can($perm,$resource){
        if($this->loaded()){
            $roles = $this->roles->order_by('priority','ASC')->find_all();
        }else{
            $roles = [(object)['name' => 'guest']];
        }


        try{
            foreach($roles as $role){
                if($this->_acl()->isAllowed($role->name, $resource,$perm)){
                    return true;
                }
            }
        }catch (Exception $e){}

        return false;
    }

    /**
     * Переопределение метода создания пользователя
     * Добавляем информацию о тех кто создал с текущую запись в бд
     */
    public function create(Validation $validation = NULL){
        $this->_created_by_column = ['column' => 'created_by', 'value' => Auth::instance()->get_user()->id];
        parent::create($validation);
    }


    /**
     * Возвращает является ли пользователь создателем данной сущности
     * в $model должно быть поле created_by
     * @param ORM $model
     * @return bool
     */
    public function isOwner(ORM $model){
        return (!empty($this->pk()) AND !empty($model->created_by)) AND ($this->pk() == $model->created_by);
    }

    /**
     * Возвращает является ли пользователь гостем
     * @return bool
     */
    public function isGuest(){
        return !$this->loaded() OR !$this->roles->where('outspread','IS NOT',DB::expr('NULL'))->count_all();
    }

    /**
     * Возвращает является ли пользователь клиентом
     * @return bool
     */
    public function isClient(){
        return !!$this->client_id;
    }

    public function is($role){
        if(is_string($role)){
            $roles = $this->roles->order_by('priority','ASC')->find_all();
            foreach ($roles as $r){
                if($role == $r->name) return true;
            }
        }elseif($role instanceof Model_Role){
            $roles = $this->roles->order_by('priority','ASC')->find_all();
            foreach ($roles as $r){
                if($role->name == $r->name) return true;
            }
        }
        return false;
    }

    /**
     * Возвращает релевантную роль (самую качественную)
     * Если указать поле то возвращает значение поля
     * В случае несуществования поля возвращает -1
     * @param null $field
     * @return int|mixed|null
     */
    public function getRelevantRole($field = null){
        $output = $this->getLTCache('_relevantRole');
        if(empty($output)){
            $output = $this->roles->order_by('priority','ASC')->find();
            $this->setLTCache('_relevantRole',$output);
        }
        if(!is_null($field)){
            try{
                return $output->{$field};
            }catch (Exception $e){
                return -1;
            }
        }
        
        return $output;
    }

    /**
     * Проверяет приоритет пользователя выше или раен указанному из Enum_UserPriorityLevel
     * @param $lvl
     * @return bool
     */
    public function priorityLevelIn($lvl){
        return $this->getRelevantRole('priority') <= $lvl;
    }

    /**
     * Возвращает роли равные и ниже своей
     * @return mixed
     */
    public function getMyAndLowerRoles(){
        $role = $this->roles->where('outspread','IS NOT',DB::expr('NULL'))->order_by('priority','ASC')->find();
        return ORM::factory('Role')->where('priority','>=',(int)$role->priority)->and_where('outspread','IS NOT',DB::expr('NULL'))->order_by('priority','ASC')->find_all();
    }

    /**
     * Возвращает идентификаторы ролей равных своей и ниже
     * @return array
     */
    public function getMyAndLowerRolesIds(){
        $output = [];
        $roles = $this->getMyAndLowerRoles();
        if(count($roles)){
            foreach ($roles as $role){
                array_push($output,$role->id);
            }
        }
        return $output;
    }

    /**
     * Возвращает роли равные и ниже своей
     * @return mixed
     */
    public function getMyAndLowerRolesAsKeyValPairArray(){
        $roles = $this->getMyAndLowerRoles();
        $output = null;
        if(count($roles)){
            foreach ($roles as $r){
                $output[$r->id] = $r->name;
            }
        }
        return $output;
    }
    
    public function addProfession($id){
        $profRel = ORM::factory('UserCmpProfRelation');
        $profRel->user_id = $this->pk();
        $profRel->profession_id = $id;
        $profRel->save();
    }

    /**
     * Возвращает текущую профессию пользователя
     * @param null $field
     * @return int|mixed|null
     */
    public function getProfession($field = null){
        $output = $this->getLTCache('_lastProfession');
        if(empty($output)){
            
            $output = $this->professions->order_by('users_cmp_professions.id','DESC')->find();
            $this->setLTCache('_lastProfession',$output);
        }
        if(!is_null($field)){
            try{
                return $output->{$field};
            }catch (Exception $e){
                return -1;
            }
        }

        return $output;
    }
    
    /**
     * Установка статуса пользователя
     * @param Enum_UserStatus $status
     * @return $this
     */
    public function setStatus(Enum_UserStatus $status){
        $this->status = $status;
        return $this;
    }

    public function rules()
    {
        return array(
            'username' => array(
                array('not_empty'),
                array('max_length', array(':value', 32)),
                array(array($this, 'unique'), array('username', ':value')),
            ),
            'password' => array(
                array('not_empty'),
            ),
            'email' => array(
                array('not_empty'),
                array('email'),
                array(array($this, 'unique'), array('email', ':value')),
            ),
        );
    }

    public function hasClient(){
        return (bool) $this->client_id;
    }

    public function availableCompanies($asORMQuery = false){
        $output = null;
        switch ($this->getRelevantRole('outspread')){
            case Enum_UserOutspread::General:
                $query = ORM::factory('Company');
                $output = $asORMQuery ? $query : $query->find_all();
                break;
            case Enum_UserOutspread::Corporate:
                $query = $this->client->companies;
                $output = $asORMQuery ? $query : $query->find_all();
                break;
            case Enum_UserOutspread::Company:
                $output = $asORMQuery ? ORM::factory('Company')->where('id','=',$this->company_id) : $query = [$this->company];
                break;

            case Enum_UserOutspread::Project:
                if($this->company_id){
                    $projects = $this->projects->where('company_id','!=',$this->company_id)->find_all();
                    if(!empty($projects)) {
                        $cmpIds = [];
                        foreach ($projects as $p) {
                            $cmpIds[] = $p->company_id;
                        }
                    }
                    $cmpIds[] = $this->company_id;

                    $expr = '('.implode(',',$cmpIds).')';
                    $output = $asORMQuery ? ORM::factory('Company')->where('id','IN',DB::expr($expr)) : ORM::factory('Company')->where('id','IN',DB::expr($expr))->find_all();
                }else{
                    $output = [];
                    $projects = $this->projects->find_all();
                    if(!empty($projects)){
                        $cmpIds = [];
                        foreach ($projects as $p){
                            $cmpIds[] = $p->company_id;
                        }
                        if(count($cmpIds)){
                            $expr = '('.implode(',',$cmpIds).')';
                            $output = $asORMQuery ? ORM::factory('Company')->where('id','IN',DB::expr($expr)) : ORM::factory('Company')->where('id','IN',DB::expr($expr))->find_all();
                        }
                    }
                }
                break;
        }
        return $output;
    }

    public function canUseProject(Model_Project $project){
        switch ($this->getRelevantRole('outspread')){
            case Enum_UserOutspread::General:
                return true;
            case Enum_UserOutspread::Corporate:
                return (bool)$this->client->companies->where('id','=',$project->company_id)->count_all();
            case Enum_UserOutspread::Company:
                return $this->company_id == $project->company_id;
        }
        //for Enum_UserOutspread::Project and consultants
        if($this->projects->where('id','=',$project->id)->count_all()){
            return true;
        }

        return false;
    }

    public function getCompanyIfItAvailable($company_id){
        $id = (int)$company_id;
        $companies = $this->availableCompanies();
        if(count($companies)){
            foreach ($companies as $c){
                if($c->id == $id){
                    return $c;
                }
            }
        }
        return null;
    }

    public static function needNotify($userId, $projectId){
        return (bool)count(DB::query(Database::SELECT,'SELECT * FROM users_projects up WHERE up.user_id='.(int)$userId.' AND up.project_id='.(int)$projectId.' AND up.notify_changes')->execute()->as_array());
    }

}