<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 22.04.2020
 * Time: 13:12
 */

class Controller_Api_Roles_Entities extends HDVP_Controller_API
{
    /**
     * Returns specialities Names of subcontractors
     * @url https://qforb.net/api/jsopn/v2/{token}/roles/subcontractors/crafts
     * @method GET
     */
    public function action_subcontractors_crafts_get(){
        try {
            $result = [];
            $subcontractorsCrafts = Kohana::$config->load('subcontractors')->as_array();

            foreach ($subcontractorsCrafts as $roleName => $subcontractorsCraft) {
                $result[$roleName] = $subcontractorsCraft['specialties'];
            }

            $this->_responseData = [
                'status' => 'success',
                'item' => $result
            ];
        } catch (Exception $e){
            throw API_Exception::factory(500,'Operation Error');
        }

    }
}