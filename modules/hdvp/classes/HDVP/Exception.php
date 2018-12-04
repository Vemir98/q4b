<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 10.11.2016
 * Time: 12:39
 */
class HDVP_Exception extends HTTP_Exception
{
    protected $_code = 500;

    public function get_response()
    {
        $view = View::factory('http-errors/hdvp');

        $view->message = $this->getMessage();

        $response = Response::factory()
            ->status(401)
            ->body($view->render());

        return $response;
    }
}