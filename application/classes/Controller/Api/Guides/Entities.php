<?php


class Controller_Api_Guides_Entities extends HDVP_Controller_API
{
    /**
     * Returns Guides data
     * @url https://qforb.net/api/json/v2/{token}/guides/<guideType>
     * @method GET
     */
    public function action_guides_get()
    {
        try {
            $guideType = $this->request->param('guideType');

            if(!in_array($guideType, Enum_GuideTypes::toArray())) {
                throw API_ValidationException::factory(500, 'Incorrect guide type');
            }

            $guides = Api_DBGuides::getGuides($guideType);

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