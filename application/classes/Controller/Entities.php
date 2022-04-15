<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 25.03.2019
 * Time: 13:26
 */
class Controller_Entities extends HDVP_Controller_Template
{
//    protected $_actions_perms = [
//        'index,companies' => [
//            'GET' => 'read'
//        ],
//        'show' => [
//            'POST' => 'read',
//        ],
//    ];
    protected $_csrfCheck = false;
    protected $_formSecureTknCheck = false;
    public function before()
    {
        parent::before();
        $this->auto_render = false;
    }

    public function action_companies(){
        $params = Arr::get($_GET,'fields');
        if($params){
            $params = explode(',',$params);
            if( ! is_array($params)){
                $params = array($params);
            }
        }

        $companies = $this->_user->relatedCompanies()->find_all();
        $response = [
            'items' => [],
            'count' => count($companies)
        ];

        if((bool)Arr::get($_GET,'selectText') == true){
            $response['items'][] = [
                'id' => 0,
                'name' => __('Select Company'),
            ];
        }

        foreach ($companies as $c){
            if( ! count($params)){
                $cmp = $c->as_array();
            }else{
                $cmp = Arr::extract($c->as_array(), $params);
            }
            array_walk($cmp,function(&$param){
                $param = html_entity_decode($param);
            });
            $response['items'][] = $cmp;
        }

        $this->_responseData = $response;
    }

    public function action_projects(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));

        $params = Arr::get($_GET,'fields');
        if($params){
            $params = explode(',',$params);
            if( ! is_array($params)){
                $params = array($params);
            }
        }
//        $model = ORM::factory('Project');
//        $items = $model->where('company_id','=',$id)->find_all();
        $items = $this->_user->relatedProjects($id);
        $response = [
            'items' => [],
            'count' => count($items)
        ];

        if((bool)Arr::get($_GET,'selectText') == true){
            $response['items'][] = [
                'id' => 0,
                'name' => __('Select Project'),
            ];
        }

        if(!empty($items)){
            foreach ($items as $item){

                if( ! count($params)){
                    $proj = $item->as_array();
                }else{
                    $proj = Arr::extract($item->as_array(), $params);
                }
                array_walk($proj,function(&$param){
                    $param = html_entity_decode($param);
                });

                $response['items'][] = $proj;
            }
        }
        $this->_responseData = $response;
    }

    public function action_objects(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));

        $params = Arr::get($_GET,'fields');
        if($params){
            $params = explode(',',$params);
            if( ! is_array($params)){
                $params = array($params);
            }
        }

        $model = ORM::factory('PrObject');
        $items = $model->where('project_id','=',$id)->find_all();
        $response = [
            'items' => [],
            'count' => count($items)
        ];

        if((bool)Arr::get($_GET,'selectText') == true){
            $response['items'][] = [
                'id' => 0,
                'name' => __('Select Object'),
            ];
        }

        if(!empty($items)){
            foreach ($items as $item){

                if( ! count($params)){
                    $obj = $item->as_array();
                }else{
                    $obj = Arr::extract($item->as_array(), $params);
                }
                array_walk($obj,function(&$param){
                    $param = html_entity_decode($param);
                });
                $response['items'][] = $obj;
            }
        }
        $this->_responseData = $response;
    }

    public function action_floors(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));

        $params = Arr::get($_GET,'fields');
        if($params){
            $params = explode(',',$params);
            if( ! is_array($params)){
                $params = array($params);
            }
        }

        $model = ORM::factory('PrFloor');
        $items = $model->where('object_id','=',$id)->order_by('number','ASC')->find_all();
        $response = [
            'items' => [],
            'count' => count($items)
        ];

        if((bool)Arr::get($_GET,'selectText') == true){
            $response['items'][] = [
                'id' => 0,
                'number' => __('Select Floor'),
            ];
        }

        if(!empty($items)){
            foreach ($items as $item){

                if( ! count($params)){
                    $flr = $item->as_array();
                }else{
                    $flr = Arr::extract($item->as_array(), $params);
                }
                array_walk($flr,function(&$param){
                    $param = html_entity_decode($param);
                });
                $response['items'][] = $flr;
            }
        }
        $this->_responseData = $response;
    }

    public function action_places(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));

        $params = Arr::get($_GET,'fields');
        if($params){
            $params = explode(',',$params);
            if( ! is_array($params)){
                $params = array($params);
            }
        }
        $floor = ORM::factory('PrFloor',$id);
        if( ! $floor->loaded()) throw new HTTP_Exception_404();
        $object = $floor->object;
        $model = ORM::factory('PrPlace');
        $items = $model->where('floor_id','=',$id)->order_by('number','ASC')->find_all();
        $response = [
            'items' => [],
            'count' => count($items)
        ];

        if((bool)Arr::get($_GET,'selectText') == true){
            $response['items'][] = [
                'id' => 0,
                'number' => __('Select Floor'),
            ];
        }

        if(!empty($items)){
            foreach ($items as $item){

                if( ! count($params)){
                    $pls = $item->as_array();
                }else{
                    $pls = Arr::extract($item->as_array(), $params);
                }
                $pls['name'] = str_replace("'"," ",$item->name.' ('.$item->custom_number.')');
                array_walk($pls,function(&$param){
                    $param = html_entity_decode($param);
                });
                $response['items'][] = $pls;
            }
        }
        $this->_responseData = $response;
    }

    public function action_email_users_list(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));

        $project = ORM::factory('Project',$id);

        $this->_responseData['autocomplete'] = [];
        foreach ($project->company->users->find_all() as $usr){
            $this->_responseData['autocomplete'][$usr->email] = $usr->email;
        }

        $role = ORM::factory('Role',['outspread' => Enum_RoleOutspread::Super]);
        foreach ($role->users->find_all() as $usr){
            $this->_responseData['autocomplete'][$usr->email] = $usr->email;
        }
        $this->_responseData['autocomplete'] = array_keys($this->_responseData['autocomplete']);
        $this->_responseData['projectUsers'] = [];
        foreach ($project->users->find_all() as $usr){
            $this->_responseData['projectUsers'][] = array(
                'name' => htmlentities($usr->name),
                'profession' => htmlentities($usr->getProfession('name')),
                'email' => $usr->email,
            );
        }
    }

    public function action_regulations(){
        $crafts = ORM::factory('Craft')->where('status','=',Enum_Status::Enabled)->order_by('name','ASC')->find_all();
        $craftsData = [];
        foreach ($crafts as $craft){
            $regulations = $craft->regulations->find_all();
            $regData = [];
            foreach ($regulations as $r){
                $regData[] = [
                    'id' => $r->id,
                    'craftId' => $craft->id,
                    'desc' => $r->desc,
                    'status' => array('val' => $r->status, 'label' => __($r->status)),
                    'file' => strpos($r->file,'fs.qforb.net') === false ?  ($r->file ? '/media/data/regulations/'.$r->file : null) : $r->file,
                    'uploaded' => $r->uploaded ? date('d/m/Y',$r->uploaded) : null,
                    'created' => $r->created_at,
                    'edited' => false,
                    'inEditMode' => false,
                ];
            }
            $craftsData[] = [
                'id' => $craft->id,
                'name' => $craft->name,
                'items' => $regData,
                'checked' => false
            ];
        }

        $this->_responseData['items'] = $craftsData;
    }

    public function action_instructions(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        $crafts = ORM::factory('CmpCraft')->where('company_id','=',$id)->and_where('status','=',Enum_Status::Enabled)->order_by('name','ASC')->find_all();
        $craftsData = [];
        foreach ($crafts as $craft){
            $instructions = $craft->instructions->where('project_id','IS',null)->find_all();
            $regData = [];
            foreach ($instructions as $r){
                $regData[] = [
                    'id' => $r->id,
                    'craftId' => $craft->id,
                    'desc' => $r->desc,
                    'companyId' => $r->company_id,
                    'status' => array('val' => $r->status, 'label' => __($r->status)),
                    'file' => strpos($r->file,'fs.qforb.net') === false ?  ($r->file ? '/media/data/companies/' . $r->company_id . '/instructions/'.$r->file : null) : $r->file,
                    'created' => $r->created_at,
                    'uploaded' => $r->uploaded ? date('d/m/Y',$r->uploaded) : null,
                    'edited' => false,
                    'inEditMode' => false,
                ];
            }
            $craftsData[] = [
                'id' => $craft->id,
                'name' => $craft->name,
                'items' => $regData,
                'checked' => false
            ];
        }

        $this->_responseData['items'] = $craftsData;
    }

    public function action_certifications_old(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        $id2 = $this->getUIntParamOrDie($this->request->param('id2'));
        $crafts = ORM::factory('CmpCraft')->where('company_id','=',$id)->and_where('status','=',Enum_Status::Enabled)->order_by('name','ASC')->find_all();
        $craftsData = [];
        foreach ($crafts as $craft){
            $certifications = $craft->instructions->where('project_id','=',$id2)->find_all();
            $regData = [];
            foreach ($certifications as $r){
                $regData[] = [
                    'id' => $r->id,
                    'craftId' => $craft->id,
                    'companyId' => $r->company_id,
                    'projectId' => $r->project_id,
                    'desc' => $r->desc,
                    'status' => array('val' => $r->status, 'label' => __($r->status)),
                    'file' => strpos($r->file,'fs.qforb.net') === false ?  ($r->file ? '/media/data/projects/' . $r->project_id . '/certifications/'.$r->file : null) : $r->file,
                    'created' => $r->created_at,
                    'uploaded' => $r->uploaded ? date('d/m/Y',$r->uploaded) : null,
                    'edited' => false,
                    'inEditMode' => false,
                ];
            }
            $craftsData[] = [
                'id' => $craft->id,
                'name' => $craft->name,
                'items' => $regData,
                'checked' => false
            ];
        }

        $this->_responseData['items'] = $craftsData;
    }

    public function action_certifications(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        $id2 = $this->getUIntParamOrDie($this->request->param('id2'));
        $crafts = ORM::factory('CmpCraft')->where('company_id','=',$id)->and_where('status','=',Enum_Status::Enabled)->order_by('name','ASC')->find_all();
        $craftsData = [];
        foreach ($crafts as $craft){
//            $certifications = $craft->instructions->where('project_id','=',$id2)->find_all();
//            $regData = [];
//            foreach ($certifications as $r){
//                $regData[] = [
//                    'id' => $r->id,
//                    'craftId' => $craft->id,
//                    'companyId' => $r->company_id,
//                    'projectId' => $r->project_id,
//                    'desc' => $r->desc,
//                    'status' => array('val' => $r->status, 'label' => __($r->status)),
//                    'file' => strpos($r->file,'fs.qforb.net') === false ?  ($r->file ? '/media/data/projects/' . $r->project_id . '/certifications/'.$r->file : null) : $r->file,
//                    'created' => $r->created_at,
//                    'uploaded' => $r->uploaded ? date('d/m/Y',$r->uploaded) : null,
//                    'edited' => false,
//                    'inEditMode' => false,
//                ];
//            }
            $craftsData[] = [
                'id' => $craft->id,
                'name' => $craft->name,
//                'items' => $regData,
                'checked' => false
            ];
        }

        $this->_responseData['items'] = $craftsData;
    }
}