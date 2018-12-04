<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 21.11.2016
 * Time: 15:00
 */
class HTTP_Exception_404 extends Kohana_HTTP_Exception_403
{
    public function get_response()
    {
        if($this->getMessage() == 'blocked'){
            $view = View::factory('http-errors/404_blocked');
        }else{
            $view = View::factory('http-errors/404');

            $view->message = $this->getMessage() ?: '404 Page Not Found';
        }


        $response = Response::factory()
            ->status(404)
            ->body($view->render());

        return $response;
    }
}