<?php


class Controller_Api_Modules_Guides_Entities extends HDVP_Controller_API
{
    /**
     * Returns quality control data
     * All underscore values are in camelcase
     * returned array [{name,email, ..., role}]
     * if passed in get params fields returned items must have only that fields ?fields=place_id,space_id
     * @url https://qforb.net/api/json/v2/{token}/quality-controls/get/<id>
     * @method GET
     */
    public function action_guides_get()
    {
        try {
            $guideType = $this->request->param('guideType');

            if(!in_array($guideType, Enum_GuideTypes::toArray())) {
                throw API_ValidationException::factory(500, 'Incorrect guide type');
            }

            $guides = Api_DBModules::getModulesGuides($guideType);

            $response = [];

            foreach ($guides as $guide) {
                $response[] = [
                    'type' => $guide['type'],
                    'title' => $guide['title'],
                    'description' => $guide['description'],
                    'ordering' => $guide['ordering'],
                    'fileId' => $guide['fileId'],
                    'moduleId' => $guide['moduleId'],
                    'fileUrl' => $guide['filePath'].'/'.$guide['fileName']
                ];
            }
            $this->_responseData = [
                'status' => "success",
                'items' => $response
            ];

        } catch (Exception $e){
            throw API_Exception::factory(500,'Operation Error');
//            throw API_Exception::factory(500,$e->getMessage());
        }
    }
}