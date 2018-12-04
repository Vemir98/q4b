<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 19.09.2016
 * Time: 16:49
 */
class HDVP_Controller_Template extends HDVP_Controller
{
    /**
     * @var  View  page template
     */
    public $template = 'template';

    /**
     * @var  boolean  auto render template
     **/
    public $auto_render = TRUE;

    /**
     * Флаг, являетя ли запрос аякс запросом
     * @var bool
     */
    protected $_isAjax = false;

    /**
     * Тело ответа при выключенном авторендере или Ajax запросе
     * @var array
     */
    protected $_responseData = null;

    /**
     * Данные пришедшие от клиента ajax запросом (приходят зашифрованными)
     * @var mixed
     */
    protected $_post = [];

    /**
     * Список загружаемых файлов
     * @var array | null
     */
    protected $_files = [];

    /**
     * Экземпляр класса авторизации
     * @var Auth::instance()
     */
    protected $_auth;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);

        //$this->_auth = Auth::instance();
        if(!$this->_auth->logged_in() AND ($request->action() != 'login' AND $request->action() != 'logout')){
            $tkn = $this->request->headers('Auth-Token');
            if(!empty($tkn)){
                $authToken = ORM::factory('UToken',['token' => $tkn, 'type' => 'application']);
                if($authToken->loaded()){
                    $this->_auth->force_login($authToken->user);
                    $this->_user = $this->_auth->get_user();
                }
            }
        }
        if(Request::current()->is_initial()){
            //если пользователь хотябы раз был на сайте то редиректим на страницу входа иначе страница не найдена
            if(! $this->_isAjax AND !Auth::instance()->logged_in()){
                if(!in_array($this->request->route()->name($this->request->route()),['site.login','site.auth','site.reportsGuestAccess'])){
                    if(Cookie::get('lastVisit',false))
                        //$this->redirect(URL::route('site.login',['lang' => Language::getCurrent()->slug]));
                        throw new HTTP_Exception_401();
                    else
                        throw new HTTP_Exception_404();
                }
            }

            //проверяем пользователь в первый раз заходит на сайт или нет
            if( ! Cookie::get('lastVisit',false)){
                $detectedLang = $this->_detectClientLocale();
                if($detectedLang AND $detectedLang->slug != Language::getCurrent()->slug){
                    Cookie::set('lastVisit',time(),time() + Date::YEAR);
                    $routeParams  = Arr::merge(
                        Request::$current->param(),
                        [
                            'lang' => $detectedLang->slug,
                            'controller' => $request->controller(),
                            'action' => $request->action()]
                    );

                    $this->redirect(URL::route(Route::name($request->route()), $routeParams));
                }
            }else{
                Cookie::set('lastVisit',time(),time() + Date::YEAR);
            }

//        Cookie::set('clientLang',Language::getCurrent()->slug,time() + Date::YEAR);
        }
    }

    /**
     * Loads the template [View] object.
     */
    public function before()
    {
        parent::before();
        View::set_global('_USER',clone($this->_user));
        if($this->request->is_ajax()){
            $this->_isAjax = true;
            $this->auto_render = false;
            if($this->request->method() === HTTP_Request::POST){//todo: обработать исключения
                if(!empty($_FILES)){
                    //$this->_post = JSON::decode(AesCtr::decrypt($_POST['Data'],$_SERVER['SERVER_NAME'],256),true);
                    try{
                        $this->_post = JSON::decode($_POST['Data']);
                        $this->_processFileArray();
                    }catch(Exception $e){
                        $this->_post = null;
                    }

                }else{
                    //$this->_post = JSON::decode(AesCtr::decrypt(file_get_contents("php://input"),$_SERVER['SERVER_NAME'],256),true);
                    try{
                        $this->_post = JSON::decode(file_get_contents("php://input"));
                    }catch (Exception $e){
                        $this->_post = null;
                    }

                }
                
            }
            
            if((empty($this->_post) OR !Security::check(Arr::get($this->_post,'csrf'))) AND $this->request->method() == HTTP_Request::POST){
                $this->_setErrors('Your request data is invalid or expires. Please refresh page');
                $this->_breakActionExecution = true;

            }
        }else{
            $this->_post = $_POST;
        }

        if($this->request->method() === HTTP_Request::POST){
            if(!isset($this->post()['x-form-secure-tkn']) OR $this->post()['x-form-secure-tkn'] !== ""){
                Security::block_client();
                throw new HTTP_Exception_404('blocked');
            }
        }

        if ($this->auto_render === TRUE)
        {
            $this->template = View::make($this->template);
            if($this->request->is_initial()){
                Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url('/'));
            }

        }

    }

    /**
     * Assigns the template [View] as the request response.
     */
    public function after()
    {
        if ($this->auto_render AND ! $this->_isAjax)
        {
//            $minyfy = new MatthiasMullie\Minify\JS(DOCROOT.'media/js/jquery/jquery-3.1.0.min.js',DOCROOT.'media/js/q4u/core.js',DOCROOT.'media/js/q4u/aes.js');
//            $minyfy->minify(DOCROOT.'media/js/all.js');
            $this->response->body($this->template->render());

        }else{
            if($this->_isAjax){
                if($this->auto_render AND $this->template instanceof View){
                    $this->_responseData['content'] = $this->template->render();
                }
                //$this->response->headers('Content-Type','application/json');
                //$this->response->body(AesCtr::encrypt(JSON::encode($this->_responseData),$_SERVER['SERVER_NAME'],256));
                $this->response->body(JSON::encode($this->_responseData));
            }else{
                $this->response->body($this->_responseData);
            }
        }

        parent::after();
    }

    /**
     * Изменяет флаг авторендеринга шаблона
     * @param $flag
     */
    public function auto_render($flag){
        $this->auto_render = !! $flag;
        if($this->auto_render AND is_string($this->template)){
            $this->template = View::make($this->template);
        }
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

    /**
     * Редирект (в основном служит для Ajax запросов чтобы вернуть ответ клиентскому js что надо редиректится)
     * Чтоб програмно сделать редирект лучше использовать метод $this->redirect
     * @param string $uri
     * @param int $code
     */
    public function makeRedirect($uri = '', $code = 302, $processUrl = true)
    {
        if($this->_isAjax)
            $this->_responseData['redirect'] = $processUrl ? URL::site($uri) : $uri;
        else
            $this->redirect($uri,$code);
    }

    private function _processFileArray(){
        foreach ($_FILES as $key => $data){
            if(is_array($data['name'])){
                foreach ($data['name'] as $key1 => $val1){
                    if(is_array($val1)){
                        foreach ($val1 as $key2 => $val2){
                            if(is_array($val2)) throw new HTTP_Exception_404;
                            $this->_files[$this->_fileKeyProcessing($key)][$this->_fileKeyProcessing($key1)][$this->_fileKeyProcessing($key2)] = [
                                'name' => $data['name'][$key1][$key2],
                                'type' => $data['type'][$key1][$key2],
                                'tmp_name' => $data['tmp_name'][$key1][$key2],
                                'error' => $data['error'][$key1][$key2],
                                'size' => $data['size'][$key1][$key2]
                            ];
                        }
                    }else{
                        $this->_files[$this->_fileKeyProcessing($key)][$this->_fileKeyProcessing($key1)] = [
                            'name' => $data['name'][$key1],
                            'type' => $data['type'][$key1],
                            'tmp_name' => $data['tmp_name'][$key1],
                            'error' => $data['error'][$key1],
                            'size' => $data['size'][$key1]
                        ];
                    }
                }
            }
            else{
                $this->_files[$this->_fileKeyProcessing($key)][0] = [
                    'name' => $data['name'],
                    'type' => $data['type'],
                    'tmp_name' => $data['tmp_name'],
                    'error' => $data['error'],
                    'size' => $data['size']
                ];
            }

        }
    }

    private function _fileKeyProcessing($key){
        $key = trim($key);
        if(preg_match('~^\+~',$key)){
            $key = str_replace('+','new_',$key);
        }
        return $key;
    }
    
    public function post(){
        return $this->_post;
    }
    
    public function files(){
        return $this->_files;
    }
    
    public function setResponseData($name,$data){
        if( ! $this->request->is_ajax())
            throw new Kohana_Exception('Response data can be set only in ajax request');
        if(in_array($name,['errors','content'])){
            throw new Kohana_Exception('Can\t set response data name to :name',[':name' => $name]);
        }
        if($data instanceof View){
            $data = $data->render();
        }
        $this->_responseData[$name] = $data;
    }

    private function _detectClientLocale()
    {
        $clientAcceptLanguages = $this->request->client()->getAcceptLanguages();
        $detectedLang = null;
        if(!empty($clientAcceptLanguages)){
            foreach ($clientAcceptLanguages as $lang => $factor){
                if($detectedLang = Language::getLangByIso2($lang) OR $detectedLang = Language::getLangByLocale($lang)){
                    break;
                }
            }
        }

        if(empty($detectedLang)){
            try{
                if($clientIp = $this->request->client()->get_ip_env() OR $clientIp = $this->request->client()->get_ip_server()){
                    $reader   = new \GeoIp2\Database\Reader(APPPATH.'local-storage'.DS.'GeoLite2-City.mmdb');
                    $record = $reader->city($clientIp);
                    $detectedLang = Language::getLangByIso2(strtolower($record->country->isoCode));
                }
            }catch (Exception $e){
                throw $e;
            }

        }
        return $detectedLang;
    }

    protected function _setUsrMinimalPriorityLvl($lvl){
        if(!$this->_user->priorityLevelIn($lvl)){
            throw new HTTP_Exception_403();
        }
    }

    public function getUIntParamOrDie($param){
        $val = (int)$param;
        if(($val != $param) OR ($val < 0)){
            Security::block_client();
            throw new HTTP_Exception_404('blocked');
        }
        return $val;
    }
}