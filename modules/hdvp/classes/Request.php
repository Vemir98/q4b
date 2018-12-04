<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 02.11.2017
 * Time: 15:49
 */
class Request extends Kohana_Request
{
    /**
     * Processes the request, executing the controller action that handles this
     * request, determined by the [Route].
     *
     * 1. Before the controller action is called, the [Controller::before] method
     * will be called.
     * 2. Next the controller action will be called.
     * 3. After the controller action is called, the [Controller::after] method
     * will be called.
     *
     * By default, the output from the controller is captured and returned, and
     * no headers are sent.
     *
     *     $request->execute();
     *
     * @return  Response
     * @throws  Request_Exception
     * @throws  HTTP_Exception_404
     * @uses    [Kohana::$profiling]
     * @uses    [Profiler]
     */
    public function execute()
    {
        if ( ! $this->_external)
        {
            $processed = Request::process($this, $this->_routes);

            if ($processed)
            {
                // Store the matching route
                $this->_route = $processed['route'];
                $params = $processed['params'];

                // Is this route external?
                $this->_external = $this->_route->is_external();

                if (isset($params['directory']))
                {
                    // Controllers are in a sub-directory
                    $this->_directory = $params['directory'];
                }

                // Store the controller
                $this->_controller = $params['controller'];

                // Store the action
                $this->_action = (isset($params['action']))
                    ? $params['action']
                    : Route::$default_action;

                // These are accessible as public vars and can be overloaded
                unset($params['controller'], $params['action'], $params['directory']);

                // Params cannot be changed once matched
                $this->_params = $params;
            }
        }

        if ( ! $this->_route instanceof Route)
        {
            $pieces = explode('/',$this->_uri);
            if(count($pieces)){
                if($pieces[0] != Language::getUrlUriSegment()){
                    foreach (Language::getAll() as $lang){
                        if($lang->slug == $pieces[0]){
                            Language::setCurrentLang($lang->iso2);
                            I18n::lang($lang->iso2);
                            break;
                        }
                    }
                }
            }
            return HTTP_Exception::factory(404, 'Unable to find a route to match the URI: :uri', array(
                ':uri' => $this->_uri,
            ))->request($this)
                ->get_response();
        }

        if ( ! $this->_client instanceof Request_Client)
        {
            throw new Request_Exception('Unable to execute :uri without a Kohana_Request_Client', array(
                ':uri' => $this->_uri,
            ));
        }

        return $this->_client->execute($this);
    }

    public function getQueryString(){
        $output = null;
        if(count($this->_get)){
            $output = http_build_query($this->_get);
        }

        return $output;

    }
}