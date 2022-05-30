<?php
ini_set('max_execution_time', 0);

/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 1/13/21
 * Time: 12:12 PM
 */
use Helpers\PushHelper;

class Controller_Api_Notification extends HDVP_Controller_API {
    protected $_checkToken = false;

    public function action_send()
    {
        try {

            $query = "SELECT id, TRIM(name) as name, company_id, status FROM cmp_crafts";


            $result = DB::query(Database::SELECT,$query)->execute()->as_array();

            $crafts = [];

            foreach ($result as $craft) {
                if(!$crafts[$craft['company_id']]) {
                    $crafts[$craft['company_id']] = [];
                }
                if(!$crafts[$craft['company_id']][$craft['name']]) {
                    $crafts[$craft['company_id']][$craft['name']] = [];
                }
                $crafts[$craft['company_id']][$craft['name']][] = $craft['id'];
            }

//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($result); echo "</pre>"; exit;


            $this->_responseData = [];
            $this->_responseData['status'] = 'success';
            $this->_responseData['item'] = $crafts;

        } catch (Exception $exception) {
            Database::instance()->rollback();
            throw API_Exception::factory(500,$exception->getMessage());
        }
    }
}