<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 10.11.2016
 * Time: 12:39
 */
class API_Exception extends HTTP_Exception
{
    protected $_code = 500;

    /**
     * Creates a new translated exception.
     *
     *     throw new Kohana_Exception('Something went terrible wrong, :user',
     *         array(':user' => $user));
     *
     * @param   string  $message    status message, custom content to display with error
     * @param   array   $variables  translation variables
     * @return  void
     */
    public function __construct($message = NULL, array $variables = NULL, Exception $previous = NULL, $code = null)
    {
        if($code != null){
            $this->_code = $code;
        }
        parent::__construct($message,$variables,$previous);
    }

    public static function factory($code, $message = NULL, array $variables = NULL, Exception $previous = NULL)
    {
        $class = 'API_Exception';

        return new $class($message, $variables, $previous,$code);
    }

    public function get_response()
    {
        $description = $this->getMessage();

        $response = Response::factory()
            ->status($this->_code)
            ->body(json_encode(['status' => $this->_code,'message' => $description]))
            ->headers('X-Powered-By','ASP.NET')
            ->headers('X-AspNet-Versionr','2.0.50727')
            ->headers('X-AspNetMvc-Version','1.0')
            ->headers('DevelopedIn','Sunrise DVP')
            ->headers('Content-Type','application/json')
            ->headers('X-Frame-Options','DENY');
        return $response;
    }
}