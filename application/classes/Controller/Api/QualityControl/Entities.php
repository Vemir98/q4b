<?php


class Controller_Api_QualityControl_Entities extends HDVP_Controller_API
{
    /**
     * Returns quality control data
     * All underscore values are in camelcase
     * returned array [{name,email, ..., role}]
     * if passed in get params fields returned items must have only that fields ?fields=place_id,space_id
     * @url https://qforb.net/api/json/v2/{token}/quality-controls/get/<id>
     * @method GET
     */
    public function action_get_get()
    {
        $qcId = $this->getUIntParamOrDie($this->request->param('qcId'));

        try {

            $fields = Arr::get($_GET,'fields');
            $all = Arr::get($_GET,'all');

//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$forMobile]); echo "</pre>"; exit;

            if(!empty($fields)){
                $fields = Arr::decamelize(explode(',',$fields));
            }

            $qc = Api_DBQualityControl::getQcById($qcId, $fields, $all)[0];

            if($qc) {
                if(empty($fields)) {
                    $qc['files'] = $this->getQcImages($qcId);
                    $qc['tasks'] = $this->getQcTasks($qcId);
                } else {
                    if(in_array('files', $fields)) {
                        $qc['files'] = $this->getQcImages($qcId);
                    }
                    if(in_array('tasks', $fields)) {
                        $qc['tasks'] = $this->getQcTasks($qcId);
                    }
                }
            }

            $this->_responseData = [
                'status' => "success",
                'item' => $qc
            ];

        } catch (Exception $e){
//            throw API_Exception::factory(500,'Operation Error');
            throw API_Exception::factory(500,$e->getMessage());
        }
    }

    private function getQcImages($qcId): array
    {
        $result = [];
        $tasks = Api_DBQualityControl::getQcImages($qcId);

        foreach ($tasks as $task) {
            $result[] = $task['path'].'/'.$task['name'];
        }

        return $result;
    }

    private function getQcTasks($qcId): array
    {
        $result = [];
        $tasks = Api_DBQualityControl::getQcTasks($qcId);

        foreach ($tasks as $task) {
            $result[] = $task['taskId'];
        }

        return $result;
    }
}