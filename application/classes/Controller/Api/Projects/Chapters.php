<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 28.03.2022
 * Time: 16:27
 */

class Controller_Api_Projects_Chapters extends HDVP_Controller_API
{
    /**
     * Get Project Chapters
     * https://qforb.net/api/json/<appToken>/projects/<projectId>/chapters
     */
    public function action_project_chapters_get()
    {
        try {
            $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));

            $chapters = Api_DBChapters::getProjectChapters($projectId);

            $this->_responseData = [
                'status' => 'success',
                'items' => $chapters,
            ];

        }  catch (Exception $e) {
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_project_chapters_get][test] (Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Operation Error');
        }
    }
}