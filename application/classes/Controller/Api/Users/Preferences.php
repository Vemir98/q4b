<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 06.12.2021
 * Time: 14:12
 */

class Controller_Api_Users_Preferences extends HDVP_Controller_API
{
    /**
     * Set preferences to user
     * @url https://qforb.net/api/jsopn/v2/{token}/user/{userId}/preferences/set/<type>
     * @method POST
     */
    public function action_set_post(){

        $userId = $this->getUIntParamOrDie($this->request->param('userId'));
        $preferenceType = $this->request->param('type');
        $preferences = Arr::get($_POST, 'preferences');

        try {
            Database::instance()->begin();

            switch ($preferenceType) {

                case Enum_UserPreferencesTypes::Dashboard:
                    $valid = Validation::factory($preferences);

                    $valid
                        ->rule('companies', 'not_empty')
                        ->rule('projects', 'not_empty')
                        ->rule('range', 'not_empty');

                    if (!$valid->check()) {
                        throw API_ValidationException::factory(500, 'missing required field');
                    }

                    $userPreference = Api_DBUsers::getUserPreference($userId, $preferenceType);

                    if($userPreference) {
                        DB::delete('users_preferences')
                            ->where('user_id', '=', $userId)
                            ->and_where('group', '=', $preferenceType)
                            ->execute($this->_db);
                    }

                    foreach ($preferences as $preferenceKey => $preference) {
                        $queryData = [
                            'user_id' => $userId,
                            'group'   => $preferenceType,
                            'key'     => $preferenceKey
                        ];
                        if(is_array($preference)) {
                            $queryData['value'] = implode(',', $preference);
                        } else {
                            $queryData['value'] = $preference;
                        }


                        DB::insert('users_preferences')
                            ->columns(array_keys($queryData))
                            ->values(array_values($queryData))
                            ->execute($this->_db);
                    }
                break;
            }

            Database::instance()->commit();


            $this->_responseData = [
                'status' => 'success'
            ];
        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_set_post] (API_ValidationException)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Incorrect data');
        } catch (Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_set_post] (Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Get preferences for user
     * @url https://qforb.net/api/jsopn/v2/{token}/user/{userId}/preferences/get/<type>
     * @method POST
     */
    public function action_get_get() {
        $userId = $this->getUIntParamOrDie($this->request->param('userId'));
        $preferenceType = $this->request->param('type');

        try {
            $result = [];
            switch ($preferenceType) {
                case Enum_UserPreferencesTypes::Dashboard:
                    $userPreference = Api_DBUsers::getUserPreference($userId, $preferenceType);

                    foreach ($userPreference as $preference) {
                        $result[$preference['preferenceKey']] = explode(',', $preference['preferenceValue']);
                    }
                    break;
            }

            $this->_responseData = [
                'status' => 'success',
                'item' => !empty($result) ? $result : null
            ];
        } catch (Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_get_get] (Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Operation Error');
        }
    }
}