<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 22.04.2020
 * Time: 13:12
 */

class Controller_Api_Users_Entities extends HDVP_Controller_API
{
    /**
     * Returns all users list with relevant role
     * Returned users data have all fields except password,device_token,os_type
     * All underscore values are in camelcase
     * returned array [items => [{name,email, ..., role}]]
     * if passed in get params fields returned items must have only that fields ?fields=name,role
     * @url https://qforb.net/api/jsopn/v2/{token}/users/list
     * @method GET
     */
    public function action_list_get(){
        try {
            $fields = Arr::get($_GET, 'fields');
            if($fields) {
                $fields = explode(',', $fields);
            }

            $usersList = Api_DBUsers::getUsersList();
            $usersList = $this->getUsersExpandedData($usersList, $fields);

            $this->_responseData = [
                'status' => 'success',
                'items' => $usersList
            ];
        } catch (Exception $e){
            throw API_Exception::factory(500,'Operation Error');
        }

    }

    /**
     * Returns all users list with relevant role for company
     * Returned users data have all fields except password,device_token,os_type
     * All underscore values are in camelcase
     * returned array [items => [{name,email, ..., role}]]
     * if passed in get params fields returned items must have only that fields ?fields=name,role
     * @url https://qforb.net/api/jsopn/v2/{token}/users/company/<id>
     * @method GET
     */
    public function action_company_get(){
        $companyId = $this->getUIntParamOrDie($this->request->param('id'));

        $fields = Arr::get($_GET, 'fields');
        if($fields) {
            $fields = explode(',', $fields);
        }

        try {
            $usersList = Api_DBUsers::getCompanyUsersList($companyId);
            $usersList = $this->getUsersExpandedData($usersList, $fields);

            $this->_responseData = [
                'status' => 'success',
                'items' => $usersList
            ];
        } catch (Exception $e){
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Returns all users list with relevant role for project
     * Returned users data have all fields except password,device_token,os_type
     * All underscore values are in camelcase
     * returned array [items => [{name,email, ..., role}]]
     * if passed in get params fields returned items must have only that fields ?fields=name,role
     * @url https://qforb.net/api/jsopn/v2/{token}/users/project/<id>
     * @method GET
     */
    public function action_project_get(){
        $projectId = $this->getUIntParamOrDie($this->request->param('id'));
        $fields = Arr::get($_GET, 'fields');
        if($fields) {
            $fields = explode(',', $fields);
        }

        try {
            $pUsersList = Api_DBUsers::getProjectUsersList($projectId);
            $companyId = Api_DBCompanies::getProjectCompanyByProjectId($projectId)[0]['company_id'];
            $cUsersList = Api_DBUsers::getCompanyUsersList($companyId);
            $cUsersResult = [];

            foreach ($cUsersList as $cUser) {
                $userRoles = Api_DBUsers::getRolesByUserId($cUser['id']);
                if((array_search('company', array_column($userRoles, 'outspread')) !== FALSE)) {
                    $cUsersResult[] = $cUser;
                }
            }
            $gUsersList = Api_DBUsers::getUsersWithRoleOutspread('general');

            $result = array_unique(array_merge($pUsersList, $gUsersList, $cUsersResult), SORT_REGULAR);

            $usersList = $this->getUsersExpandedData($result, $fields);

            $this->_responseData = [
                'status' => 'success',
                'items' => $usersList
            ];
        } catch (Exception $e){
            throw API_Exception::factory(500,'Operation Error');
        }

    }

    /**
     * Returns all users list for role
     * Returned users data have all fields except password,device_token,os_type
     * All underscore values are in camelcase
     * returned array [items => [{name,email, ...}]]
     * if passed in get params fields returned items must have only that fields ?fields=name,email
     * @url https://qforb.net/api/jsopn/v2/{token}/users/role/<id>
     * @method GET
     */
    public function action_role_get(){
        $roleId = $this->getUIntParamOrDie($this->request->param('id'));
        $fields = Arr::get($_GET, 'fields');
        if($fields) {
            $fields = explode(',', $fields);
        }

        try {
            $usersList = Api_DBUsers::getRoleUsersList($roleId);
            $usersList = $this->getUsersExpandedData($usersList, $fields);

            $this->_responseData = [
                'status' => 'success',
                'items' => $usersList
            ];
        } catch (Exception $e){
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    private function getUsersExpandedData($usersList, $fields) {
            $result = [];
            if(isset($fields) && !empty($fields)) {
                $isFieldsValid = $this->checkFieldsValid($fields, array_merge(array_keys($usersList[0]),['role']));

                if($isFieldsValid) {
                    foreach ($usersList as $userKey => $user) {
                        $filteredUser = [];
                        foreach (array_keys($user) as $key) {
                            if(in_array($key, $fields)) {
                                $filteredUser[$key] = $user[$key];
                            }
                        }
                        if(in_array('role', $fields)) {
                            $userRoles = Api_DBUsers::getRolesByUserId($user['id']);
                            $priority = array_column($userRoles, 'priority');
                            $filteredUser['role'] = $userRoles[array_search(min($priority), $priority)];
                        }
                        $result[] = $filteredUser;
                    }
                } else {
                    $result = [];
                }

            } else {
                foreach ($usersList as $userKey => $user) {
                    $userRoles = Api_DBUsers::getRolesByUserId($user['id']);
                    $priority = array_column($userRoles, 'priority');
                    $usersList[$userKey]['role'] = $userRoles[array_search(min($priority), $priority)];
                }
                $result = $usersList;
            }


            return $result;
    }

    private function checkFieldsValid($fields, $availableFields) {
        $isValid = true;
        foreach ($fields as $field) {
            if(!in_array($field, $availableFields)) {
                $isValid = false;
            }
        }
        return $isValid;
    }
}