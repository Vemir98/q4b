<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 22.11.2016
 * Time: 6:55
 */
class URL extends Kohana_URL
{
    /**
     * Fetches an absolute site URL based on a URI segment.
     *
     *     echo URL::site('foo/bar');
     *
     * @param   string  $uri        Site URI to convert
     * @param   mixed   $protocol   Protocol string or [Request] class to use protocol from
     * @param   boolean $index		Include the index_page in the URL
     * @return  string
     * @uses    URL::base
     */
    public static function site($uri = '', $protocol = NULL, $index = TRUE)
    {
        // Chop off possible scheme, host, port, user and pass parts
        $path = preg_replace('~^[-a-z0-9+.]++://[^/]++/?~', '', trim($uri, '/'));

        if ( ! UTF8::is_ascii($path))
        {
            // Encode all non-ASCII characters, as per RFC 1738
            $path = preg_replace_callback('~([^/]+)~', 'URL::_rawurlencode_callback', $path);
        }

        if(preg_match('~^'.Language::getUrlUriSegment().'~',$path)){
            return URL::base($protocol, $index).$path;
        }else{
            $url = URL::base($protocol, $index).implode('/',[Language::getUrlUriSegment(),$path]);
            return rtrim($url,'/');
        }
    }

    public static function withLang($uri = '',$langSlug = null, $protocol = NULL, $index = TRUE)
    {
        // Chop off possible scheme, host, port, user and pass parts
        $path = preg_replace('~^[-a-z0-9+.]++://[^/]++/?~', '', trim($uri, '/'));

        if ( ! UTF8::is_ascii($path))
        {
            // Encode all non-ASCII characters, as per RFC 1738
            $path = preg_replace_callback('~([^/]+)~', 'URL::_rawurlencode_callback', $path);
        }
        if(preg_match('~^'.$langSlug.'~',$path)){
            return URL::base($protocol, $index).$path;
        }else{
            $url = URL::base($protocol, $index).($langSlug != Language::getDefault()->slug ? implode('/',[$langSlug,$path]) : $path);
            return $url != '/' ? rtrim($url,'/') : $url;
        }

    }

    public static function route($name, array $params = NULL, $protocol = NULL)
    {
        $route = Route::get($name);

        // Create a URI with the route and convert it to a URL
        if ($route->is_external())
            return $route->uri($params);
        else
            return (!empty($params['lang']) AND $params['lang'] !== Language::getCurrent()->slug) ? URL::withLang($route->uri($params),$params['lang'], $protocol) : URL::site($route->uri($params), $protocol);
    }
}