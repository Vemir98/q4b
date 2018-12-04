<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 14.06.2017
 * Time: 19:50
 */
class Controller_Security extends HDVP_Controller
{
    public function action_block(){
        Security::block_client();
        throw new HTTP_Exception_404('blocked');
    }
}