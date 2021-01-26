<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 1/12/21
 * Time: 12:40 PM
 */
namespace Helpers;

class UrlHelper {
    public static function getUrlWithUriAndHash($base, $uri, $hash=null)
    {
        return $hash ? $base . $uri . '#' . $hash : $base . $uri;
    }

    public static function getUrlWithHash($base, $request, $id)
    {
       return self::getAbsoluteUrl($base, $request) . '#' . $id;
    }

    public static function getAbsoluteUrl($base, $request)
    {
        $requestUrl = $request->url();
        $requestUrl[0] = '';
        return $base . $requestUrl .'?'. $request->getQueryString();
    }

    public static function getUrlLastSegment($url, $delimeter='/')
    {
        $arr = explode($delimeter, $url);
        return isset($arr[count($arr) - 1]) ? $arr[count($arr) - 1] : '';
    }
}