<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 22.11.2016
 * Time: 5:53
 */
class Route extends Kohana_Route
{
    /**
     * Stores a named route and returns it. The "action" will always be set to
     * "index" if it is not defined.
     *
     *     Route::set('default', '(<controller>(/<action>(/<id>)))')
     *         ->defaults(array(
     *             'controller' => 'welcome',
     *         ));
     *
     * @param   string  $name           route name
     * @param   string  $uri            URI pattern
     * @param   array   $regex          regex patterns for route keys
     * @return  Route
     */
    public static function set($name, $uri = NULL, $regex = [])
    {
        $extraRules = Language::getRouteRule();
        if(!empty($extraRules)){
            if(strlen($uri)){
                $uri = '(<lang>/)'.$uri;
            }else if(count($extraRules)){
                $uri = '(<lang>)';
            }

            if(is_null($regex)){
                $regex = [];
            }
            $regex += $extraRules;
        }
        return Route::$_routes[$name] = (new Route($uri, $regex))->defaults(['lang' => Language::getDefault()->slug]);
    }

    /**
     * Provides default values for keys when they are not present. The default
     * action will always be "index" unless it is overloaded here.
     *
     *     $route->defaults(array(
     *         'controller' => 'welcome',
     *         'action'     => 'index'
     *     ));
     *
     * If no parameter is passed, this method will act as a getter.
     *
     * @param   array   $defaults   key values
     * @return  $this or array
     */
    public function defaults(array $defaults = NULL)
    {
        if ($defaults === NULL)
        {
            return $this->_defaults;
        }
        $this->_defaults = Arr::merge($this->_defaults, $defaults);

        return $this;
    }

    /**
     * Create a URL from a route name. This is a shortcut for:
     *
     *     echo URL::site(Route::get($name)->uri($params), $protocol);
     *
     * @param   string  $name       route name
     * @param   array   $params     URI parameters
     * @param   mixed   $protocol   protocol string or boolean, adds protocol and domain
     * @return  string
     * @since   3.0.7
     * @uses    URL::site
     */
    public static function url($name, array $params = NULL, $protocol = NULL)
    {
        if(!empty($params))
        $params = array_diff($params,array(''));
        $route = Route::get($name);

        // Create a URI with the route and convert it to a URL
        if ($route->is_external())
            return $route->uri($params);
        else{
            if(!empty($params['lang'])){
                $lang = $params['lang'];
                unset($params['lang']);
                return URL::withLang($route->uri($params),$lang, $protocol);
            }else{
                return URL::site($route->uri($params), $protocol);
            }
        }

    }
}