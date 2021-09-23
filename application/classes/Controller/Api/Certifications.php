<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 25.07.2020
 * Time: 10:52
 */

class Controller_Api_Certifications extends HDVP_Controller_API
{
    const REGULATIONS_PATH = DOCROOT.'media/data/regulations/';
    const INSTRUCTIONS_PATH = DOCROOT.'media/data/companies/{id}/instructions/';
    const CERTIFICATIONS_PATH = DOCROOT.'media/data/projects/{id}/certifications/';
    public function action_regulations(){
        $crafts = ORM::factory('Craft')->where('status','=',Enum_Status::Enabled)->order_by('name','ASC')->find_all();
        $regData = [];
        foreach ($crafts as $craft){
            $regulations = $craft->regulations->find_all();
            foreach ($regulations as $r){
                $regData[] = [
                    'id' => $r->id,
                    'craftId' => $craft->id,
                    'craftName' => $craft->name,
                    'desc' => $r->desc,
                    'status' => $r->status,
                    'file' => !empty($r->file) ? str_replace('/home/qforbnet/public_html','',self::REGULATIONS_PATH.str_replace('.pdf','.jpg',$r->file)) : null,
                    'createdAt' => $r->created_at,
                    'updatedAt' => $r->updated_at,
                    'createdBy' => $r->created_by,
                    'updatedBy' => $r->updated_by,
                ];
            }
        }

        $this->_responseData['items'] = $regData;
    }

    public function action_instructions(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        $crafts = ORM::factory('CmpCraft')->where('company_id','=',$id)->and_where('status','=',Enum_Status::Enabled)->order_by('name','ASC')->find_all();
        $regData = [];
        foreach ($crafts as $craft){
            $instructions = $craft->instructions->where('project_id','IS',null)->find_all();
            foreach ($instructions as $r){
                $images = [];
//                foreach ($r->images->find_all() as $img){
//                    $images []= $img->path . '/' . $img->name;
//                }
                $regData[] = [
                    'id' => $r->id,
                    'craftId' => $craft->id,
                    'craftName' => $craft->name,
                    'desc' => $r->desc,
                    'status' => $r->status,
                    'file' => strpos($r->file,'fs.qforb.net') === false ?  ($r->file ? Kohana_URL::site('/media/data/companies/' . $r->company_id . '/instructions/'.$r->file,'https') : null) : $r->file,
                    'images' => $images,
                    'createdAt' => $r->created_at,
                    'updatedAt' => $r->updated_at,
                    'createdBy' => $r->created_by,
                    'updatedBy' => $r->updated_by,
                ];
            }
        }

        $this->_responseData['items'] = $regData;
    }

    public function action_certifications(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        $id2 = $this->getUIntParamOrDie($this->request->param('id2'));
        $crafts = ORM::factory('CmpCraft')->where('company_id','=',$id)->and_where('status','=',Enum_Status::Enabled)->order_by('name','ASC')->find_all();
        $regData = [];
        foreach ($crafts as $craft){
            $certifications = $craft->instructions->where('project_id','=',$id2)->find_all();
            foreach ($certifications as $r){
                $images = [];
//                foreach ($r->images->find_all() as $img){
//                    $images []= $img->path . '/' . $img->name;
//                }
                $regData[] = [
                    'id' => $r->id,
                    'craftId' => $craft->id,
                    'craftName' => $craft->name,
                    'desc' => $r->desc,
                    'status' => $r->status,
                    'file' => strpos($r->file,'fs.qforb.net') === false ?  ($r->file ? Kohana_URL::site('/media/data/projects/' . $r->project_id . '/certifications/'.$r->file,'https') : null) : $r->file,
                    'images' => $images,
                    'createdAt' => $r->created_at,
                    'updatedAt' => $r->updated_at,
                    'createdBy' => $r->created_by,
                    'updatedBy' => $r->updated_by,
                ];
            }
        }

        $this->_responseData['items'] = $regData;
    }



    private function instructionsPath($id){
        $dir = str_replace('{id}',$id,self::INSTRUCTIONS_PATH);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    private function certificationsPath($id){
        $dir = str_replace('{id}',$id,self::CERTIFICATIONS_PATH);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }
}