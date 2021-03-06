<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 22.04.2020
 * Time: 13:12
 */

class Controller_Api_Acceptance extends HDVP_Controller_API
{
    public function action_add(){
        //var_dump($this->_pFArr());die;
        $data = Arr::extract($_POST,[
            'company_id',
            'project_id',
            'object_id',
            'floor_id',
            'place_id',
            'comment',
            'customers',
            'reserve_materials',
            'transferable_items',
            'larder',
            'parking',
            'qc_count',
            'unique_token'
        ]);

        Kohana::$log->add(Log::ERROR,'[CREATE DELIVERY clientData]'.json_encode($data, JSON_PRETTY_PRINT));


        $existingReportId = null;

        if($data['unique_token']) {
            $report = ORM::factory('DeliveryReport', ['unique_token' => $data['unique_token']]);
            if($report->loaded()) {
                $existingReportId = $report->pk();
            }
        }

        try {
            if(is_null($existingReportId)) {
                $company = ORM::factory('Company',$data['company_id']);
                if( ! $company->loaded()) throw new HTTP_Exception_404;
                $project = $company->projects->where('id','=',$data['project_id'])->find();
                if( ! $project->loaded()) throw new HTTP_Exception_404;
                if( !$this->_user->canUseProject($project)) throw new HTTP_Exception_404;
                $object = $project->objects->where('id','=',$data['object_id'])->find();
                if( ! $object->loaded()) throw new HTTP_Exception_404;
                $floor = $object->floors->where('id','=',$data['floor_id'])->find();
                if( ! $floor->loaded()) throw new HTTP_Exception_404;
                $place = $floor->places->where('id','=',$data['place_id'])->find();
                if( ! $place->loaded()) throw new HTTP_Exception_404;

                if( ! count($data['customers'])) throw API_Exception::factory(500,'Empty Customers');
                if( $project->s_texts->count_all() < 3) throw API_Exception::factory(500,'Project Texts Empty');

                $qcCountValid = Validation::factory($data);

                $qcCountValid->rule('qc_count', 'not_empty');

                $files = $this->_pFArr();
                if( ! count($files['customer_files'])){
                    throw API_Exception::factory(500,'Incorrect data');
                }
                if( ! count($files['signature'])){
                    throw API_Exception::factory(500,'Incorrect data');
                }
                if(empty($files['dev_app_files'])){
                    throw API_Exception::factory(500,'Incorrect data');
                }
                Database::instance()->begin();
                $data['s_text_type_1'] = $project->s_texts->where('type','=','s_text_type_1')->find()->text;
                $data['s_text_type_2'] = $project->s_texts->where('type','=','s_text_type_2')->find()->text;
                $data['s_text_type_3'] = $project->s_texts->where('type','=','s_text_type_3')->find()->text;
                $data['s_text_type_4'] = $project->s_texts->where('type','=','s_text_type_4')->find()->text;
                $data['type'] = Enum_DeliveryReportTypes::Delivery;

                $report = ORM::factory('DeliveryReport');
                $report->values($data, [
                    'company_id',
                    'project_id',
                    'object_id',
                    'floor_id',
                    'place_id',
                    'comment',
                    's_text_type_1',
                    's_text_type_2',
                    's_text_type_3',
                    'larder',
                    'parking',
                    'type'
                ]);
                if ($qcCountValid->check()) {
                    $report->expected_qc_count = $data['qc_count'];
                    $report->qc_count = 0;
                }
                if($data['unique_token']) {
                    $report->unique_token = $data['unique_token'];
                }

                $report->save();
                if($files['wm_pic'][0])
                    $report->wm_pic = str_replace($report->stdFilePath().DS,'',Upload::save($files['wm_pic'][0],null,$report->stdFilePath()));
                if($files['el_pic'][0])
                    $report->el_pic = str_replace($report->stdFilePath().DS,'',Upload::save($files['el_pic'][0],null,$report->stdFilePath()));
                $report->signature = str_replace($report->stdFilePath().DS,'',Upload::save($files['signature'][0],null,$report->stdFilePath()));
                $report->save();

                for ($i=0; $i < count($data['customers']); $i++){
                    $c = ORM::factory('Customer');

                    if(empty($files['customer_files'][$i])){
                        throw new API_Exception(500,'Incorrect data');
                    }
                    $data['customers'][$i]['file'] = str_replace($c->dir().DS,'',Upload::save($files['customer_files'][$i],null,$c->dir()));
                    $c->values($data['customers'][$i],['full_name','id_number','phone_number','email','file']);
                    $c->save();

                    $report->add('customers',$c->pk());
                }

                foreach ($files['dev_app_files'] as $idx => $image){
                    $uploadedFile = [
                        'name' => str_replace($report->davAppFilePath().DS,'',Upload::save($image,null,$report->davAppFilePath())),
                        'original_name' => $image['name'],
                        'ext' => Model_File::getFileExt($image['name']),
                        'mime' => $image['type'],
                        'path' => str_replace(DOCROOT,'',$report->davAppFilePath()),
                        'token' => md5($image['name']).base_convert(microtime(false), 10, 36),
                    ];
                    $image = ORM::factory('Image')->values($uploadedFile)->save();
                    $report->add('devAppFiles', $image->pk());
                }

                if(!empty($files['poa_files'])){
                    foreach ($files['poa_files'] as $idx => $image){
                        $uploadedFile = [
                            'name' => str_replace($report->poaFilePath().DS,'',Upload::save($image,null,$report->poaFilePath())),
                            'original_name' => $image['name'],
                            'ext' => Model_File::getFileExt($image['name']),
                            'mime' => $image['type'],
                            'path' => str_replace(DOCROOT,'',$report->poaFilePath()),
                            'token' => md5($image['name']).base_convert(microtime(false), 10, 36),
                        ];
                        $image = ORM::factory('Image')->values($uploadedFile)->save();
                        $report->add('poaFiles', $image->pk());
                    }
                }

                if(!empty($data['reserve_materials'])){
                    foreach ($data['reserve_materials'] as $rm){
                        $r = ORM::factory('DReserveMaterial');
                        $r->values($rm);
                        $r->report_id = $report->pk();
                        $r->save();
                    }
                }

                if(!empty($data['transferable_items'])){
                    foreach ($data['transferable_items'] as $ti){
                        $t = ORM::factory('DTransferableItem');
                        $t->values($ti);
                        $t->report_id = $report->pk();
                        $t->save();
                    }
                }


                $this->_responseData['id'] = $report->pk();
                Database::instance()->commit();

                if ($qcCountValid->check()) {
                    Queue::enqueue('CheckDeliveryReportsQcIntegrity','Job_Report_CheckDeliveryReportsQcIntegrity',[
                        'reportId' => $report->pk(),
                    ],\Carbon\Carbon::now()->addSeconds(60)->timestamp);
                } else {

                    Queue::enqueue('DeliveryReportsPDF','Job_Report_DeliveryReportsPDF',[
                        'report' => $report->pk(),
                    ],\Carbon\Carbon::now()->addSeconds(60)->timestamp);
                }
            } else {
                $this->_responseData['id'] = $existingReportId;
            }
        } catch (ORM_Validation_Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR,'[CREATE DELIVERY ERROR ORM_Validation_Exception]'.$e->getMessage());
            throw API_Exception::factory(500,$e->errors('validation'));
        }catch (HDVP_Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR,'[CREATE DELIVERY ERROR HDVP_Exception]'.$e->getMessage());
            throw API_Exception::factory(500,$e->getMessage());
        }catch (Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR,'[CREATE DELIVERY ERROR Exception]'.$e->getMessage());
            throw API_Exception::factory(500,$e->getMessage());
        }
    }

    public function action_pre_add(){
        $data = Arr::extract($_POST,[
            'company_id',
            'project_id',
            'object_id',
            'floor_id',
            'place_id',
            'comment',
            'customers',
            'reserve_materials',
            'transferable_items',
            'larder',
            'parking',
            'qc_count',
            'unique_token'
        ]);

        Kohana::$log->add(Log::ERROR,'[CREATE PRE_DELIVERY clientData]'.json_encode($data, JSON_PRETTY_PRINT));


        $existingReportId = null;

        if($data['unique_token']) {
            $report = ORM::factory('DeliveryReport', ['unique_token' => $data['unique_token']]);
            if($report->loaded()) {
                $existingReportId = $report->pk();
            }
        }

        try {
            if (is_null($existingReportId)) {
                $company = ORM::factory('Company', $data['company_id']);
                if (!$company->loaded()) throw new HTTP_Exception_404;
                $project = $company->projects->where('id', '=', $data['project_id'])->find();
                if (!$project->loaded()) throw new HTTP_Exception_404;
                if (!$this->_user->canUseProject($project)) throw new HTTP_Exception_404;
                $object = $project->objects->where('id', '=', $data['object_id'])->find();
                if (!$object->loaded()) throw new HTTP_Exception_404;
                $floor = $object->floors->where('id', '=', $data['floor_id'])->find();
                if (!$floor->loaded()) throw new HTTP_Exception_404;
                $place = $floor->places->where('id', '=', $data['place_id'])->find();
                if (!$place->loaded()) throw new HTTP_Exception_404;

                $qcCountValid = Validation::factory($data);

                $qcCountValid->rule('qc_count', 'not_empty');

                if ($project->s_texts->count_all() < 3) throw API_Exception::factory(500, 'Project Texts Empty');

                $files = $this->_pFArr();
//            if( ! count($files['customer_files'])){
//                throw API_Exception::factory(500,'Incorrect data');
//            }
                if( ! count($files['signature'])){
                    throw API_Exception::factory(500,'Incorrect data1');
                }
//            if(empty($files['dev_app_files'])){
//                throw API_Exception::factory(500,'Incorrect data');
//            }
                Database::instance()->begin();
                $data['s_text_type_1'] = $project->s_texts->where('type','=','s_text_type_1')->find()->text;
                $data['s_text_type_2'] = $project->s_texts->where('type','=','s_text_type_2')->find()->text;
                $data['s_text_type_3'] = $project->s_texts->where('type','=','s_text_type_3')->find()->text;
                $data['s_text_type_4'] = $project->s_texts->where('type','=','s_text_type_4')->find()->text;
                $data['type'] = Enum_DeliveryReportTypes::PreDelivery;
                $report = ORM::factory('DeliveryReport');
                $report->values($data, [
                    'company_id',
                    'project_id',
                    'object_id',
                    'floor_id',
                    'place_id',
                    'comment',
                    's_text_type_1',
                    's_text_type_2',
                    's_text_type_3',
                    's_text_type_4',
                    'larder',
                    'parking',
                    'type'
                ]);
                if ($qcCountValid->check()) {
                    $report->expected_qc_count = $data['qc_count'];
                    $report->qc_count = 0;
                }
                if($data['unique_token']) {
                    $report->unique_token = $data['unique_token'];
                }
                $report->save();
                if($files['wm_pic'][0])
                    $report->wm_pic = str_replace($report->stdFilePath().DS,'',Upload::save($files['wm_pic'][0],null,$report->stdFilePath()));
                if($files['el_pic'][0])
                    $report->el_pic = str_replace($report->stdFilePath().DS,'',Upload::save($files['el_pic'][0],null,$report->stdFilePath()));
                $report->signature = str_replace($report->stdFilePath().DS,'',Upload::save($files['signature'][0],null,$report->stdFilePath()));
                $report->save();

                for ($i=0; $i < count($data['customers']); $i++){
                    $c = ORM::factory('Customer');

//                if(empty($files['customer_files'][$i])){
//                    throw new API_Exception(500,'Incorrect data');
//                }

                    $queryDataKeys = ['full_name','id_number','phone_number','email'];

                    if(isset($files['customer_files'][$i]) && !empty($files['customer_files'][$i])) {
                        $data['customers'][$i]['file'] = str_replace($c->dir().DS,'',Upload::save($files['customer_files'][$i],null,$c->dir()));
                        array_push($queryDataKeys, 'file');
                    }
                    $c->values($data['customers'][$i],$queryDataKeys);
//                ['full_name','id_number','phone_number','email','file']
                    $c->save();

                    $report->add('customers',$c->pk());
                }

                foreach ($files['dev_app_files'] as $idx => $image){
                    $uploadedFile = [
                        'name' => str_replace($report->davAppFilePath().DS,'',Upload::save($image,null,$report->davAppFilePath())),
                        'original_name' => $image['name'],
                        'ext' => Model_File::getFileExt($image['name']),
                        'mime' => $image['type'],
                        'path' => str_replace(DOCROOT,'',$report->davAppFilePath()),
                        'token' => md5($image['name']).base_convert(microtime(false), 10, 36),
                    ];
                    $image = ORM::factory('Image')->values($uploadedFile)->save();
                    $report->add('devAppFiles', $image->pk());
                }

                if(!empty($files['poa_files'])){
                    foreach ($files['poa_files'] as $idx => $image){
                        $uploadedFile = [
                            'name' => str_replace($report->poaFilePath().DS,'',Upload::save($image,null,$report->poaFilePath())),
                            'original_name' => $image['name'],
                            'ext' => Model_File::getFileExt($image['name']),
                            'mime' => $image['type'],
                            'path' => str_replace(DOCROOT,'',$report->poaFilePath()),
                            'token' => md5($image['name']).base_convert(microtime(false), 10, 36),
                        ];
                        $image = ORM::factory('Image')->values($uploadedFile)->save();
                        $report->add('poaFiles', $image->pk());
                    }
                }

                if(!empty($data['reserve_materials'])){
                    foreach ($data['reserve_materials'] as $rm){
                        $r = ORM::factory('DReserveMaterial');
                        $r->values($rm);
                        $r->report_id = $report->pk();
                        $r->save();
                    }
                }

                if(!empty($data['transferable_items'])){
                    foreach ($data['transferable_items'] as $ti){
                        $t = ORM::factory('DTransferableItem');
                        $t->values($ti);
                        $t->report_id = $report->pk();
                        $t->save();
                    }
                }

                $f = fopen(DOCROOT . 'testQcIntegrity', 'a');
                if ($f) {
                    fputs($f, '['.date("Y-m-d h:i:sa").'] [HASAV STE]' . "\n\n");
                }
                fclose($f);
                if ($qcCountValid->check()) {
                    Kohana::$log->add(Log::ERROR,'IF QC COUNT');

                    Queue::enqueue('CheckDeliveryReportsQcIntegrity','Job_Report_CheckDeliveryReportsQcIntegrity',[
                        'reportId' => $report->pk(),
                    ],\Carbon\Carbon::now()->addSeconds(60)->timestamp);
                } else {
                    Kohana::$log->add(Log::ERROR,'ELSE QC COUNT');

                    Queue::enqueue('DeliveryReportsPDF','Job_Report_DeliveryReportsPDF',[
                        'report' => $report->pk(),
                    ],\Carbon\Carbon::now()->addSeconds(60)->timestamp);
                }

                $this->_responseData['id'] = $report->pk();
                Database::instance()->commit();
            } else {
                $this->_responseData['id'] = $existingReportId;
            }
        } catch (ORM_Validation_Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR CREATE PRE_DELIVERY (ORM_Validation_Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Incorrect data');
        }catch (HDVP_Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR CREATE PRE_DELIVERY (HDVP_Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Incorrect data');
        }catch (Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR CREATE PRE_DELIVERY (Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,$e->getMessage());
        }
    }

    public function action_public_add(){
        $data = Arr::extract($_POST,[
            'company_id',
            'project_id',
            'object_id',
            'floor_id',
            'place_id',
            'comment',
            'customers',
            'reserve_materials',
            'transferable_items',
            'qc_count',
            'unique_token'
        ]);

        Kohana::$log->add(Log::ERROR,'[CREATE PUBLIC_DELIVERY clientData]'.json_encode($data, JSON_PRETTY_PRINT));


        $existingReportId = null;

        if($data['unique_token']) {
            $report = ORM::factory('DeliveryReport', ['unique_token' => $data['unique_token']]);
            if($report->loaded()) {
                $existingReportId = $report->pk();
            }
        }

        try {
            if (is_null($existingReportId)) {
                $company = ORM::factory('Company', $data['company_id']);
                if (!$company->loaded()) throw new HTTP_Exception_404;
                $project = $company->projects->where('id', '=', $data['project_id'])->find();
                if (!$project->loaded()) throw new HTTP_Exception_404;
                if (!$this->_user->canUseProject($project)) throw new HTTP_Exception_404;
                $object = $project->objects->where('id', '=', $data['object_id'])->find();
                if (!$object->loaded()) throw new HTTP_Exception_404;
                $floor = $object->floors->where('id', '=', $data['floor_id'])->find();
                if (!$floor->loaded()) throw new HTTP_Exception_404;
                $place = $floor->places->where('id', '=', $data['place_id'])->find();
                if (!$place->loaded()) throw new HTTP_Exception_404;

                $qcCountValid = Validation::factory($data);

                $qcCountValid->rule('qc_count', 'not_empty');

                if ($project->s_texts->count_all() < 3) throw API_Exception::factory(500, 'Project Texts Empty');

                $files = $this->_pFArr();
//            if( ! count($files['customer_files'])){
//                throw API_Exception::factory(500,'Incorrect data');
//            }
                if( ! count($files['signature'])){
                    throw API_Exception::factory(500,'Incorrect data1');
                }

                Database::instance()->begin();
                $data['s_text_type_1'] = $project->s_texts->where('type','=','s_text_type_1')->find()->text;
                $data['s_text_type_2'] = $project->s_texts->where('type','=','s_text_type_2')->find()->text;
                $data['s_text_type_9'] = $project->s_texts->where('type','=','s_text_type_9')->find()->text;
                $data['type'] = Enum_DeliveryReportTypes::PublicDelivery;
                $report = ORM::factory('DeliveryReport');
                $report->values($data, [
                    'company_id',
                    'project_id',
                    'object_id',
                    'floor_id',
                    'place_id',
                    'comment',
                    's_text_type_1',
                    's_text_type_2',
                    's_text_type_9',
                    'larder',
                    'parking',
                    'type'
                ]);
                if ($qcCountValid->check()) {
                    $report->expected_qc_count = $data['qc_count'];
                    $report->qc_count = 0;
                }
                if($data['unique_token']) {
                    $report->unique_token = $data['unique_token'];
                }
                $report->save();

                $report->signature = str_replace($report->stdFilePath().DS,'',Upload::save($files['signature'][0],null,$report->stdFilePath()));
                $report->save();

                for ($i=0; $i < count($data['customers']); $i++){
                    $c = ORM::factory('Customer');

//                if(empty($files['customer_files'][$i])){
//                    throw new API_Exception(500,'Incorrect data');
//                }

                    $queryDataKeys = ['full_name','id_number','phone_number','email'];

                    if(isset($files['customer_files'][$i]) && !empty($files['customer_files'][$i])) {
                        $data['customers'][$i]['file'] = str_replace($c->dir().DS,'',Upload::save($files['customer_files'][$i],null,$c->dir()));
                        array_push($queryDataKeys, 'file');
                    }
                    $c->values($data['customers'][$i],$queryDataKeys);
//                ['full_name','id_number','phone_number','email','file']
                    $c->save();

                    $report->add('customers',$c->pk());
                }

                if(!empty($data['reserve_materials'])){
                    foreach ($data['reserve_materials'] as $rm){
                        $r = ORM::factory('DReserveMaterial');
                        $r->values($rm);
                        $r->report_id = $report->pk();
                        $r->save();
                    }
                }

                if(!empty($data['transferable_items'])){
                    foreach ($data['transferable_items'] as $ti){
                        $t = ORM::factory('DTransferableItem');
                        $t->values($ti);
                        $t->report_id = $report->pk();
                        $t->save();
                    }
                }

                $f = fopen(DOCROOT . 'testQcIntegrity', 'a');
                if ($f) {
                    fputs($f, '['.date("Y-m-d h:i:sa").'] [HASAV STE]' . "\n\n");
                }
                fclose($f);
                if ($qcCountValid->check()) {
                    Kohana::$log->add(Log::ERROR,'IF QC COUNT');

                    Queue::enqueue('CheckDeliveryReportsQcIntegrity','Job_Report_CheckDeliveryReportsQcIntegrity',[
                        'reportId' => $report->pk(),
                    ],\Carbon\Carbon::now()->addSeconds(60)->timestamp);
                } else {
                    Kohana::$log->add(Log::ERROR,'ELSE QC COUNT');

                    Queue::enqueue('DeliveryReportsPDF','Job_Report_DeliveryReportsPDF',[
                        'report' => $report->pk(),
                    ],\Carbon\Carbon::now()->addSeconds(60)->timestamp);
                }

                $this->_responseData['id'] = $report->pk();
                Database::instance()->commit();
            } else {
                $this->_responseData['id'] = $existingReportId;
            }
        } catch (ORM_Validation_Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR CREATE PUBLIC_DELIVERY (ORM_Validation_Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Incorrect data');
        }catch (HDVP_Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR CREATE PUBLIC_DELIVERY (HDVP_Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,'Incorrect data');
        }catch (Exception $e){
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR CREATE PUBLIC_DELIVERY (Exception)]: ' . $e->getMessage());
            throw API_Exception::factory(500,$e->getMessage());
        }
    }

    public function action_data(){
        $projectID = $this->getUIntParamOrDie($this->request->param('id'));
        $project = ORM::factory('Project',$projectID);
        if( ! $project->loaded() OR !$this->_user->canUseProject($project)){
            throw new HTTP_Exception_404;
        }
        $this->_responseData['texts'] = $this->_responseData['reserve_materials'] = $this->_responseData['transferable_items'] = [];
        foreach ($project->s_texts->find_all() as $text){
            $this->_responseData['texts'][] = Arr::extract($text->as_array(), ['text','type']);
        }

        foreach ($project->reserve_materials->find_all() as $rm){
            $this->_responseData['reserve_materials'][] = Arr::extract($rm->as_array(), ['text','quantity','size','type']);
        }

        foreach ($project->transferable_items->find_all() as $ti){
            $this->_responseData['transferable_items'][] = Arr::extract($ti->as_array(), ['text','quantity','type']);
        }

    }
}