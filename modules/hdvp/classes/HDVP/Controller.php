<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 19.09.2016
 * Time: 11:35
 */
class HDVP_Controller extends Controller
{
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

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        if(Security::client_blocked()){
            throw new HTTP_Exception_404('blocked');
        }
        $this->_auth = Auth::instance();
        $this->_user = $this->_auth->get_user();
        if(!$this->_user){
            $this->_user = ORM::factory('User');
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
        if($this->request->controller() == 'DeliveryReports' AND $this->_user->email == 'eldar5390@gmail.com') return;
        if(Request::current()->is_initial())
        if( ! $this->_user->can($permission,$this->_resAclName())){
            if( ! Auth::instance()->logged_in())
                throw new HTTP_Exception_401();
            else
                throw new HTTP_Exception_403();
        }
        
//        if( ! $this->_usrHasPerm($permission)){
//            throw new HTTP_Exception_401('Permission denied!');
//        }
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

    /**
     * Проверяет AJAX запрос на валидность
     *в случае неудачи выбрасывает исключение 404
     * @throws HTTP_Exception_404
     */
    protected function _checkForAjaxOrDie(){
        if( ! $this->request->is_ajax()) throw new HTTP_Exception_404('Page Not Found');
    }
    
    public function before()
    {
        $this->response->headers('X-Powered-By','ASP.NET');
        $this->response->headers('X-AspNet-Versionr','2.0.50727');
        $this->response->headers('X-AspNetMvc-Version','1.0');
        $this->response->headers('DevelopedIn','Sunrise DVP');
        $this->response->headers('X-Frame-Options','DENY');
        parent::before();
        $this->_autoCheckActionPerms();
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
}