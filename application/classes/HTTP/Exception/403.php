<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 21.11.2016
 * Time: 15:00
 */
class HTTP_Exception_403 extends Kohana_HTTP_Exception_403
{
    public function get_response()
    {
        $view = View::factory('http-errors/403');

        $view->message = $this->getMessage() ?: '403 Permission Denied';

        $response = Response::factory()
            ->status(403)
            ->body($view->render());

        return $response;
    }
}