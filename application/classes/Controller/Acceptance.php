<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 10.03.2020
 * Time: 11:09
 */

class Controller_Acceptance extends HDVP_Controller_Template
{
    protected $_actions_perms = [
        'list,get_rms_list,get_companies,get_ti_list,get_te_list' => [
            'GET' => 'read'
        ],
        'update_rms_list,delete_rm,get_projects,copy_to_project,update_ti_list,delete_ti,update_te_list,delete_te' => [
            'POST' => 'update'
        ],
    ];

    protected $_csrfCheck = false;
    protected $_formSecureTknCheck = false;


    public function action_list(){
        $this->template->content = [Auth::instance()->get_user()->email];
    }

    public function action_get_rms_list(){
        $this->auto_render = false;
        $id = (int)$this->request->param('projectId');
        $type = Arr::get($_GET, 'type');;
        if(!in_array($type, Enum_ReserveMaterialTypes::toArray())) {
            throw API_ValidationException::factory(500, 'invalid param type');
        }

        $model = ORM::factory('ReserveMaterial');
        $count = $model->where('project_id','=', $id ? $id : null)->and_where('type', '=', $type)->count_all();
        $items = $model->where('project_id','=', $id ? $id : null)->and_where('type', '=', $type)->find_all();
        $response = [
            'items' => [],
            'count' => $count
        ];
        if(!empty($items)){
            foreach ($items as $item){
                $response['items'][] = array(
                    'id' => $item->id,
                    'text' => $item->text,
                    'quantity' => $item->quantity,
                    'size' => $item->size,
                    'checked' => false,
                    'inEditMode' => false,
                    'isNew' => false,
                    'edited' => false,
                    'type' => $item->type
                );
            }
        }
        $this->_responseData = $response;
    }

    public function action_update_rms_list(){
        $this->auto_render = false;
        $data = $this->post();
        $items = $data['items'];
        $create = [];
        $update = [];
        $model = ORM::factory('ReserveMaterial');
        if(count($items)){
            foreach ($items as $d){
                if(!in_array($d['type'], Enum_ReserveMaterialTypes::toArray())) {
                    throw API_ValidationException::factory(500, 'invalid param type');
                }
                if($d['isNew']){
                    $create[] = array(
                        'text' => $d['text'],
                        'id' => $d['id'],
                        'quantity' => $d['quantity'],
                        'size' => $d['size'],
                        'project_id' => $data['project'] ? $data['project'] : null,
                        'type' => $d['type']
                    );
                }else{
                    $update[] = array(
                        'id' => $d['id'],
                        'text' => $d['text'],
                        'quantity' => $d['quantity'],
                        'size' => $d['size'],
                        'project_id' => $data['project'] ? $data['project'] : null,
                        'type' => $d['type']
                    );
                }
            }
        }
        $resp = [];
        if(count($create)){
            foreach ($create as $c){
                $query = DB::insert($model->table_name(),array('text','project_id','quantity','size','type'));
                $query->values(Arr::extract($c,['text','project_id','quantity','size','type']));
                $res = $query->execute();
                $resp[] = ['id' => $res[0], 'oldId' => $c['id']];
            }

        }

        if(count($update)){
            foreach ($update as $u){
                $query = DB::update($model->table_name());
                $query->set(array('text' => $u['text'],'quantity' => $u['quantity'],'size' => $u['size'], 'type' => $u['type']))
                    ->where('id', '=', $u['id'])
                    ->execute();
                $resp[] = ['id' => $u['id'], 'oldId' => $u['id']];
            }
        }
        $this->_responseData = $resp;
    }

    public function action_delete_rm(){
        $this->auto_render = false;
        $data = $this->post();
        $model = ORM::factory('ReserveMaterial');

        $ids = "(".implode(',',$data).")";

        DB::delete($model->table_name())->where('id','IN',DB::expr($ids))->execute();
    }

    public function action_get_ti_list(){
        $this->auto_render = false;
        $id = (int)$this->request->param('projectId');
        $type = Arr::get($_GET, 'type');
        if(!in_array($type, Enum_TransferableItemsTypes::toArray())) {
            throw API_ValidationException::factory(500, 'invalid param type');
        }

        $model = ORM::factory('TransferableItems');
        $count = $model->where('project_id','=', $id ? $id : null)->and_where('type', '=', $type)->count_all();
        $items = $model->where('project_id','=', $id ? $id : null)->and_where('type', '=', $type)->find_all();
        $response = [
            'items' => [],
            'count' => $count
        ];
        if(!empty($items)){
            foreach ($items as $item){
                $response['items'][] = array(
                    'id' => $item->id,
                    'text' => $item->text,
                    'quantity' => $item->quantity,
                    'checked' => false,
                    'inEditMode' => false,
                    'isNew' => false,
                    'edited' => false,
                    'type' => $item->type
                );
            }
        }
        $this->_responseData = $response;
    }

    public function action_update_ti_list(){
        $this->auto_render = false;
        $data = $this->post();
        $items = $data['items'];
        $create = [];
        $update = [];
        $model = ORM::factory('TransferableItems');
        if(count($items)){
            foreach ($items as $d){
                if(!in_array($d['type'], Enum_ReserveMaterialTypes::toArray())) {
                    throw API_ValidationException::factory(500, 'invalid param type');
                }
                if($d['isNew']){

                    $create[] = array(
                        'text' => $d['text'],
                        'id' => $d['id'],
                        'quantity' => $d['quantity'],
                        'project_id' => $data['project'] ? $data['project'] : null,
                        'type' => $d['type']
                    );
                }else{
                    $update[] = array(
                        'id' => $d['id'],
                        'text' => $d['text'],
                        'quantity' => $d['quantity'],
                        'project_id' => $data['project'] ? $data['project'] : null,
                        'type' => $d['type']
                    );
                }
            }
        }
        $resp = [];
        if(count($create)){
            foreach ($create as $c){
                $query = DB::insert($model->table_name(),array('text','project_id','quantity','type'));
                $query->values(Arr::extract($c,['text','project_id','quantity','type']));
                $res = $query->execute();
                $resp[] = ['id' => $res[0], 'oldId' => $c['id']];
            }

        }

        if(count($update)){
            foreach ($update as $u){
                $query = DB::update($model->table_name());
                $query->set(array('text' => $u['text'],'quantity' => $u['quantity'], 'type' => $u['type']))
                    ->where('id', '=', $u['id'])
                    ->execute();
                $resp[] = ['id' => $u['id'], 'oldId' => $u['id']];
            }
        }
        $this->_responseData = $resp;
    }

    public function action_delete_ti(){
        $this->auto_render = false;
        $data = $this->post();
        $model = ORM::factory('TransferableItems');

        $ids = "(".implode(',',$data).")";

        DB::delete($model->table_name())->where('id','IN',DB::expr($ids))->execute();
    }

    public function action_get_companies(){
        $this->auto_render = false;

        $model = ORM::factory('Company');
        $items = $model->find_all();
        $response = [
            'items' => [],
            'count' => count($items)
        ];
        if(!empty($items)){
            foreach ($items as $item){
                $response['items'][] = array(
                    'id' => $item->id,
                    'name' => html_entity_decode($item->name),
                );
            }
        }
        $this->_responseData = $response;
    }

    public function action_get_projects(){
        $data = $this->post();
        if(empty($data['id'])) throw new HTTP_Exception_404();

        $model = ORM::factory('Project');
        $items = $model->where('company_id','=',$data['id'])->find_all();
        $response = [
            'items' => [],
            'count' => count($items)
        ];
        if(!empty($items)){
            foreach ($items as $item){
                $response['items'][] = array(
                    'id' => $item->id,
                    'name' => html_entity_decode($item->name),
                );
            }
        }
        $this->_responseData = $response;
    }

    public function action_copy_to_project(){
        $data = $this->post();
        $modelName = null;

        switch ($data['type']){
            case 'rm': {
                $modelName = 'ReserveMaterial';
                break;
            }
            case 'ti': {
                $modelName = 'TransferableItems';
                break;
            }
            case 'te': {
                $modelName = 'STexts';
                break;
            }
        }

        $model = ORM::factory($modelName);

        $itemIds = '('.(count($data['ids']) > 1 ? implode(',',$data['ids']) : $data['ids'][0]).')';
        $items = $model->where('id', 'IN',DB::expr($itemIds))->find_all();

        foreach ($data['projects'] as $p){
            foreach ($items as $item){
                $m = ORM::factory($modelName);
                $itemArr = $item->as_array();
                unset($itemArr['id']);
                $m->values($item->as_array(),array_keys($itemArr));
                $m->project_id = $p;
                $m->save();
            }
        }

        $this->_responseData = $data;
    }

    public function action_get_te_list(){
        $this->auto_render = false;
        $id = (int)$this->request->param('projectId');

        $model = ORM::factory('STexts');
        $count = $model->where('project_id','=', $id ? $id : null)->count_all();
        $items = $model->where('project_id','=', $id ? $id : null)->find_all();
        $response = [
            'items' => [],
            'count' => $count
        ];
        if(!empty($items)){
            foreach ($items as $item){
                $response['items'][] = array(
                    'id' => $item->id,
                    'text' => $item->text,
                    'type' => array('key' => $item->type, 'text' => __($item->type)),
                    'checked' => false,
                    'inEditMode' => false,
                    'isNew' => false,
                    'edited' => false,
                );
            }
        }
        foreach (Enum_STextType::toArray() as $t){
            $response['types'][] = array(
                'key' => $t,
                'text' => __($t)
            );
        }

        $this->_responseData = $response;
    }

    public function action_update_te_list(){
        $this->auto_render = false;
        $data = $this->post();
        $items = $data['items'];
        $create = [];
        $update = [];
        $model = ORM::factory('STexts');
        if(count($items)){
            foreach ($items as $d){
                if($d['isNew']){
                    $create[] = array(
                        'text' => $d['text'],
                        'id' => $d['id'],
                        'type' => $d['type']['key'],
                        'project_id' => $data['project'] ? $data['project'] : null,
                    );
                }else{
                    $update[] = array(
                        'id' => $d['id'],
                        'text' => $d['text'],
                        'type' => $d['type']['key'],
                        'project_id' => $data['project'] ? $data['project'] : null,
                    );
                }
            }
        }
        $resp = [];
        if(count($create)){
            foreach ($create as $c){
                $query = DB::insert($model->table_name(),array('text','type','project_id'));
                $query->values(Arr::extract($c,['text','type','project_id']));
                $res = $query->execute();
                $resp[] = ['id' => $res[0], 'oldId' => $c['id']];
            }

        }

        if(count($update)){
            foreach ($update as $u){
                $query = DB::update($model->table_name());
                $query->set(array('text' => $u['text'],'type' => $u['type']))
                    ->where('id', '=', $u['id'])
                    ->execute();
                $resp[] = ['id' => $u['id'], 'oldId' => $u['id']];
            }
        }
        $this->_responseData = $resp;
    }

    public function action_delete_te(){
        $this->auto_render = false;
        $data = $this->post();
        $model = ORM::factory('STexts');

        $ids = "(".implode(',',$data).")";

        DB::delete($model->table_name())->where('id','IN',DB::expr($ids))->execute();
    }
}