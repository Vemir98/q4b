<?php
set_time_limit(0);
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 06.03.2018
 * Time: 14:07
 */
class HDVP_Controller_API extends Controller
{
    const CACHE_DIR = APPPATH.'local-storage/api/';

    protected $_responseDataItemsForcedAsArray = true;

    public $auto_render = false;

    /**
     * Тело ответа при выключенном авторендере или Ajax запросе
     * @var array
     */
    protected $_responseData = null;

    /**
     * Экземпляр класса Auth
     * @var Auth
     */
    protected $_auth;

    /**
     * Модель пользователя
     * @var Model_User
     */
    protected $_user;

    /**
     * Модель клиента
     * @var Model_Client
     */
    protected $_client;

    /**
     * Флаг для прерывания выполнения экшена
     * (можно использовать только в методе before)
     * @var bool
     */
    protected $_breakActionExecution = false;

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
    protected $_actions_perms = null;

    protected $_checkToken = true;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        if(Security::client_blocked()){
            throw API_Exception::factory(404,'blocked');
        }
        $this->_auth = Auth::instance();
        $this->_user = $this->_auth->get_user();
        if(!$this->_user){
            $this->_user = ORM::factory('User');
        }
        if($this->_user->getRelevantRole('outspread') != Enum_UserOutspread::General) {
            $this->_client = $this->_user->client;
        }
        I18n::lang(Language::setCurrentLang($request->param('lang',Language::getDefault()->slug),'iso2')->getCurrent()->iso2);
    }

    /**
     * Возвращает отформатированное имя контроллера для проверки в ACL
     * @return string
     */
    protected function _resAclName(){
        return implode('_',array_diff(['Controller',$this->request->directory(),$this->request->controller()],array('')));
    }

    /**
     * Проверяет права для роли текущего пользователя
     * и в случае неудачи выбрасывает исключение
     * @param $permission
     * @throws HTTP_Exception_401
     * @throws HTTP_Exception_403
     */
    protected function _checkPermOrFail($permission){
        if(Request::current()->is_initial())
            if( ! $this->_user->can($permission,$this->_resAclName())){
                if( ! Auth::instance()->logged_in())
                    throw API_Exception::factory(401,'Not Authorized');//Forbidden
                else
                    throw API_Exception::factory(403,'Forbidden');
            }
    }

    /**
     * Проверяет текущий пользователь имеет право на определённое действие
     * @param $permission
     * @return bool
     */
    protected function _usrHasPerm($permission){
        return $this->_user->can($permission,$this->_resAclName());
    }


    /**
     * Автоматическая проверка прав доступа если они установленны
     * в переменной $this->_action_perms и выбрасывает исключение в случае неудачи
     * @throws HTTP_Exception_401
     */
    protected function _autoCheckActionPerms(){
        if( ! $this->_actions_perms) return;
        $checked = false;
        foreach($this->_actions_perms as $action => $perms){
            if(strpos($action,',')){
                $act = explode(',',$action);
            }else{
                $act = [$action];
            }
            foreach ($act as $a){
                if($this->request->action() !== $a) continue;
                if(is_array($perms)){ //если установлен отдельный доступ для HTTP методов (GET, POST, PUT и тд.)
                    foreach($perms as $req_method => $perm){
                        if($this->request->method() == strtoupper($req_method)){
                            $this->_checkPermOrFail($perm);
                            $checked = true;
                            break;
                        }
                    }
                }else{ //достум общий для экшена
                    $this->_checkPermOrFail($perms);
                    $checked = true;
                    break;
                }
            }
        }

        if( ! $checked)
            $this->_checkPermOrFail('you have no permission');
    }

    public function before()
    {
        $this->response->headers('Content-Type','application/json');
        $this->response->headers('X-Powered-By','ASP.NET');
        $this->response->headers('X-AspNet-Versionr','2.0.50727');
        $this->response->headers('X-AspNetMvc-Version','1.0');
        $this->response->headers('DevelopedIn','Sunrise DVP');
        $this->response->headers('X-Frame-Options','DENY');
        parent::before();
        if($this->_checkToken){
            $this->checkAppToken();
        }
        $this->_autoCheckActionPerms();
    }

    /**
     * Устанавливает ошибки для отвера клиенту
     * @param $errors
     */
    protected  function _setErrors($errors){

        if(is_string($errors)){
            $this->_responseData['errors'][] = $errors;
        }else{
            // var_dump($errors);
            foreach ($errors as $key => $val){
                if(is_string($val)){
                    if($this->_isAjax)
                        $this->_responseData['errors'][$key] = $val;
                    else
                        Message::error($val);
                }elseif (is_array($val)){
                    foreach ($val as $k => $v){
                        if($this->_isAjax)
                            $this->_responseData['errors'][$k] = $v;
                        else
                            Message::error($val);
                    }
                }
            }
        }
    }

    protected function _setUsrMinimalPriorityLvl($lvl){
        if(!$this->_user->priorityLevelIn($lvl)){
            throw API_Exception::factory(403,'Forbidden');
        }
    }

    public function getUIntParamOrDie($param){
        $val = (int)$param;
        if(($val != $param) OR ($val < 0)){
            Security::block_client();
            throw API_Exception::factory(404,'blocked');
        }
        return $val;
    }

    /**
     * Assigns the template [View] as the request response.
     */
    public function after()
    {
        if($this->_responseDataItemsForcedAsArray AND !empty($this->_responseData['items'])){
            $this->_responseData['items'] = array_values($this->_responseData['items']);
        }
        array_walk_recursive($this->_responseData,function(&$item,$key){
            if(!empty($item))
            $item = html_entity_decode($item);
        });
        $this->response->body(JSON::encode($this->_responseData));
        parent::after();
    }

    public function execute()
    {
        // Execute the "before action" method
        $this->before();
        if(!$this->_breakActionExecution){
            // Determine the action to use
            $action = 'action_'.$this->request->action();

            // If the action doesn't exist, it's a 404
            if ( ! method_exists($this, $action))
            {
                throw HTTP_Exception::factory(404,
                    'The requested URL :uri was not found on this server.',
                    array(':uri' => $this->request->uri())
                )->request($this->request);
            }

            // Execute the action itself
            $this->{$action}();
        }

        // Execute the "after action" method
        $this->after();
        // Return the response
        return $this->response;
    }

    public function getOrPost($param){
        if(!is_array($param)){
            $output = Arr::get($_GET,$param);
            if(is_null($output)){
                $output = Arr::get($_POST,$param);
            }
        }else{
            $output = Arr::extract($_GET,$param);
            $tmp = array_diff($output,array(''));
            if(empty($tmp)){
                $output = Arr::extract($_POST,$param);
            }
        }

        return $output;
    }

    public function checkAppToken(){
        $tkn = $this->request->param('appToken');
        if(empty($tkn) OR strlen($tkn) !== 32){
            throw API_Exception::factory(401,'Not Authorized');
        }
        $tkn = ORM::factory('UToken',['token' => $tkn, 'type' => Enum_UToken::Application]);
        if( ! $tkn->loaded()){
            throw API_Exception::factory(401,'Not Authorized');
        }
        $this->_auth->force_login($tkn->user);
        $this->_user = $this->_auth->get_user();

    }
}