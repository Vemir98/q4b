<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 25.03.2019
 * Time: 13:26
 */
use JonnyW\PhantomJs\Client;
class Controller_DeliveryReports extends HDVP_Controller_Template
{
    protected $_actions_perms = [
        'index,go_to_qc_report,get_pdf,print,test' => [
            'GET' => 'read',
        ],
        'show,save,send_email' => [
            'POST' => 'read',
        ],
    ];

    protected $_csrfCheck = false;
    protected $_formSecureTknCheck = false;

    public function before()
    {
        parent::before();
        if ($this->auto_render === TRUE)
        {
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Reports'))->set_url('/reports/list'));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Delivery report'))->set_url('/reports/delivery'));
        }
    }
    public function action_index(){
        VueJs::instance()->addComponent('reports/delivery');

        VueJs::instance()->addComponent('reports/send-delivery-reports');
        VueJs::instance()->addComponent('ui/modal');
        VueJs::instance()->includeDateTimePiker();
        VueJs::instance()->includeMultiselect();


        $translations = [
            'type' => __('Type'),
            'Delivery' => __('Delivery'),
            'select_type' => __('Select type'),
            'Pre-delivery' => __('pre_delivery'),
            'select_all' => __('select all'),
            'unselect_all' => __('unselect all'),
            'protocol_not_ready' => __('protocol_not_ready')
        ];

        $this->template->content = View::make('reports/delivery/main', ['translations' => $translations]);
    }

    public function action_show(){
        $this->auto_render = false;
        $data = Arr::extract($this->post(),['company_id','project_id','object_ids','floor_id','place_id','from','to','types']);

        if(empty($data['company_id']) OR empty($data['project_id']) OR empty($data['object_ids'])){
            throw new HTTP_Exception_404();
        }
        try{
            $data['from'] = DateTime::createFromFormat('d/m/Y H:i',$data['from'].' 00:00')->getTimestamp();
            $data['to'] = DateTime::createFromFormat('d/m/Y H:i',$data['to'].' 23:59')->getTimestamp();
        }catch(Exception $e){
            throw new HTTP_Exception_404();
        }

        $query = ORM::factory('DeliveryReport')
            ->where('company_id','=', $data['company_id'])
            ->and_where('project_id','=',$data['project_id'])
            ->and_where('object_id','IN', DB::expr('('.implode(',',$data['object_ids']).')'))
            ->and_where('created_at','>',$data['from'])
            ->and_where('created_at','<',$data['to']);

        if(!empty($data['floor_id'])){
            $query->and_where('floor_id','=',$data['floor_id']);
        }
        if(!empty($data['place_id'])){
            $query->and_where('place_id','=',$data['place_id']);
        }

        if(!empty($data['types'])) {
            $query->and_where('is_pre_delivery', 'IN', DB::expr('('.implode(',',$data['types']).')'));
        }
        $query->order_by('created_at','DESC');


        $delReports = $query->find_all();


        $places = ORM::factory('PrPlace')->where('object_id','IN', DB::expr('('.implode(',',$data['object_ids']).')'))->find_all();
        $simpleStat = [
            'protocols' => [
                'public' => 0,
                'private' => 0,
                'total' => 0,
            ],
            'places' => [
                'public' => 0,
                'private' => 0,
                'total' => 0,
            ]];
        foreach ($places as $place){
            if($place->type == 'public'){
                $simpleStat['places']['public']++;
            }else{
                $simpleStat['places']['private']++;
            }
        }
        $simpleStat['places']['total'] = $simpleStat['places']['private'] + $simpleStat['places']['public'];


        $items = [];
        foreach ($delReports as $r){
            $place = $r->place;

            $delReport = [
                'id' => $r->id,
                'customer' => $r->customers->find()->full_name,
                'place' => str_replace("'"," ",$place->name.' ('.$place->custom_number.')'),
                'floor' => $r->floor->number,
                'object' => $r->object->name,
                'date' => date('d/m/Y',$r->created_at),
                'qualityMark' => (bool)$r->quality,
                'protocol' => URL::site('reports/delivery/get_pdf/'.$r->id),
                'qcReport'=> URL::site('reports/delivery/go_to_qc_report/'.$r->id),
                'print'=> URL::site('reports/delivery/print/'.$r->id),
                'edited' => false,
                'isPreDelivery' => $r->is_pre_delivery
            ];

            $delReport['canCreatePdf'] = false;

            if(!is_null($r->expected_qc_count) && !is_null($r->qc_count)) {
                if((int)$r->expected_qc_count === (int)$r->qc_count) {
                    $delReport['canCreatePdf'] = true;
                }
            } else {
                $delReport['canCreatePdf'] = true;
            }

            $items[] = $delReport;


            if($places->type == 'public'){
                $simpleStat['protocols']['public']++;
            }else{
                $simpleStat['protocols']['private']++;
            }
            $simpleStat['protocols']['total'] = $simpleStat['protocols']['private'] + $simpleStat['protocols']['public'];
        }

        $this->_responseData['items'] = $items;
        $this->_responseData['txtPrivateResult'] = __(':protocols Protocols out of :places Places (private)',[
            ':protocols' => $simpleStat['protocols']['private'],
            ':places' => $simpleStat['places']['private']
        ]);
        $this->_responseData['txtPublicResult'] = __(':protocols Protocols out of :places Places (public)',[
            ':protocols' => $simpleStat['protocols']['public'],
            ':places' => $simpleStat['places']['public']
        ]);
        $this->_responseData['txtTotalResult'] = __(':protocols Protocols out of :places Places (total)',[
            ':protocols' => $simpleStat['protocols']['total'],
            ':places' => $simpleStat['places']['total']
        ]);

    }

    public function action_go_to_qc_report(){
        $this->auto_render = false;
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        $report = ORM::factory('DeliveryReport',$id);
        $query = '?';
        $params = Arr::extract(Request::current()->query(),['from','to']);
        foreach ($report->company->crafts->find_all() as $craft){
            $query .= 'crafts[]=' . $craft->id .'&';
        }
        //$query .='place_id='.$report->place->id.'&place_type='.$report->place->type.'&floors[]='.$report->floor->number.'&place_number='.$report->place->number.'&object_id[]='.$report->object->id.'&company='.$report->company->id.'&project='.$report->project->id.'&statuses[]=existing&statuses[]=normal&statuses[]=invalid&statuses[]=repaired&approval_status=all&from=29/04/2010&to=04/06/2070&project_stage[]=pr_stage_9&profession_id=all&advanced=1';
        $query .='x-form-secure-tkn=&company='.$report->company->id.'&project='.$report->project->id.'&statuses[]=existing&statuses[]=normal&statuses[]=invalid&statuses[]=repaired&approval_status=all&from='.$params['from'].'&to='.$params['to'].'&object_id[]='.$report->object->id.'&place_number=&place_id='.$report->place->id.'&place_type='.$report->place->type.'&project_stage[]=pr_stage_1&project_stage[]=pr_stage_2&project_stage[]=pr_stage_3&project_stage[]=pr_stage_4&project_stage[]=pr_stage_5&project_stage[]=pr_stage_6&project_stage[]=pr_stage_7&project_stage[]=pr_stage_8&project_stage[]=pr_stage_9&project_stage[]=pr_stage_10&project_stage[]=pr_stage_11&project_stage[]=pr_stage_12&profession_id=all&del_rep_id='.$report->id;

        //echo URL::site('reports/generate','https').$query;
        header("Location: ".URL::site('reports/generate','https').$query);die;
    }

    public function action_save(){
        $data = Arr::get($this->post(),'items',[]);
        if( ! is_array($data)){
            $data = [$data];
        }

        foreach ($data as $id){
            $rep = ORM::factory('DeliveryReport',$id);
            $rep->quality = 1;
            $this->addQualityMark($rep->pk());
            $rep->save();
        }

        $this->_responseData['items'] = $data;
    }

    public function action_print(){
        $this->auto_render = false;
        $report = ORM::factory('DeliveryReport',$this->request->param('id'));

        if($report->is_pre_delivery) {
            echo View::make('reports/delivery/pre_delivery_print',['report' => $report]);
        } else {
            echo View::make('reports/delivery/print',['report' => $report]);
        }
    }

    public function action_get_pdf(){
//        echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r(['mtav']); echo "</pre>"; exit;
        $this->auto_render = false;
        $report = ORM::factory('DeliveryReport',$this->request->param('id'));
        if( file_exists((DOCROOT.'media/data/delivery-reports/'.$report->pk().'/file.pdf'))){
            header("Location: https://qforb.net/media/data/delivery-reports/".$report->pk()."/file.pdf");die;
//            header("Location: https://qforb.sunrisedvp.systems/media/data/delivery-reports/".$report->pk()."/file.pdf");die;
        }
//        die('azaza');
        $this->_makePdf($report);
        if( ! file_exists((DOCROOT.'media/data/delivery-reports/'.$report->pk().'/file.pdf'))){
            header("Refresh:0");die;
        }
        header("Location: https://qforb.net/media/data/delivery-reports/".$report->pk()."/file.pdf");die;
    }

    private function _makePdf($report){

        $client = Client::getInstance();
        $client->getEngine()->setPath(DOCROOT.'phantomjs-2.1.1-linux-x86_64/bin/phantomjs');
        $client->getEngine()->addOption('--ignore-ssl-errors=true');

//        $client->getEngine()->setPath('/home/qforbnet/www/phantomjs-2.1.1-linux-x86_64/bin/phantomjs');
        $client->getEngine()->addOption('--cookies-file=cook.txt');


        $request = $client->getMessageFactory()->createCaptureRequest('https://qforb.net', 'GET');
        $request->addHeader('Pjsbot76463', '99642');
        $response = $client->getMessageFactory()->createResponse();
        $client->send($request, $response);

        /**
         * @see JonnyW\PhantomJs\Http\CaptureRequest
         **/
        $request = $client->getMessageFactory()->createPdfRequest(URL::withLang('/reports/delivery/print/'.$report->pk(),'he','https'), 'GET',15000);
        $request->addHeader('Pjsbot76463', '99642');

        $request->setOutputFile((DOCROOT.'media/data/delivery-reports/'.$report->pk().'/file.pdf'));
        $request->setFormat('A4');
//        $request->setOrientation('landscape');
        $request->setViewportSize(575, 1200);
        $request->setPaperSize(575, 1200);
        /**
         * @see JonnyW\PhantomJs\Http\Response
         **/
        $response = $client->getMessageFactory()->createResponse();
//        $request->setDelay(3);
        // Send the request
        $client->send($request, $response);
        exec("chmod -R 777 /home/qforbnet/www/media/data/delivery-reports/".$report->pk() . '/file.pdf');
    }

    public function action_test(){
        set_time_limit(0);
        $this->auto_render = false;
        //$converter = new PDFConverter(DOCROOT.'qpdf/1.pdf');
        //var_dump($converter->convertToJPG());
        //var_dump($converter->getOutputFiles());
        //$converter->test();
        //$pdf = ZendPdf\PdfDocument::load(DOCROOT.'media/data/delivery-reports/104/file.pdf');
        $pdf = ZendPdf\PdfDocument::load(DOCROOT.'qpdf/file.pdf');
        $image = ZendPdf\Resource\Image\ImageFactory::factory(DOCROOT.'media/img/new-images/quality.png');
        $page = $pdf->pages[0];

        $y2 = $page->getHeight() - 58;
        $y1 = $y2-38;
        $x1 = 160;
        $x2 = 195;

        foreach ($pdf->pages as $idx => $page){
            if($idx == 1){
                $y1 += 10;
                $y2 += 10;
            }
            $page->drawImage($image, $x1, $y1, $x2, $y2);
        }


        $pdf->save(DOCROOT.'qpdf/file.pdf');

    }

    public function addQualityMark($reportId){
        set_time_limit(0);
        if( ! file_exists(DOCROOT.'media/data/delivery-reports/'.$reportId.'/file.pdf')) return;
        $pdf = ZendPdf\PdfDocument::load(DOCROOT.'media/data/delivery-reports/'.$reportId.'/file.pdf');
        $image = ZendPdf\Resource\Image\ImageFactory::factory(DOCROOT.'media/img/new-images/quality.png');
        $page = $pdf->pages[0];

        $y2 = $page->getHeight() - 62;
        $y1 = $y2-34;
        $x1 = 165;
        $x2 = 200;

        foreach ($pdf->pages as $idx => $page){
            if($idx == 1){
                $y1 += 10;
                $y2 += 10;
            }
            $page->drawImage($image, $x1, $y1, $x2, $y2);
        }


        $pdf->save(DOCROOT.'media/data/delivery-reports/'.$reportId.'/file.pdf');
    }

    public function action_send_email(){
        $this->auto_render = false;
        $this->_checkForAjaxOrDie();

        $data = Arr::extract($this->post(),['emails','reports','message','project']);

        $project = ORM::factory('Project',(int)$data['project']);

        if(count($data['emails']) AND count($data['reports']) AND $project->loaded()){
//            Queue::enqueue('mailing','Job_Report_SendDeliveryReportsEmail',[
//                'emails' => $data['emails'],
//                'reports' => $data['reports'],
//                'subject' => 'Q4b report share email for project - '.$project->name,
//                'project' => $project->name,
//                'message' => trim(strip_tags($data['message'])),
//                'user' => ['name' => $this->_user->name, 'email' => $this->_user->email],
//                'image' => ($project->image_id) ? ("https://qforb.net/" .$project->main_image->originalFilePath()) : null,
//                'view' => 'emails/report/delivery-report',
//                'lang' => Language::getCurrent()->iso2,
//            ],\Carbon\Carbon::now()->addSeconds(30)->timestamp);
            ob_start();
            $mail = new Mail(Kohana::$config->load('mail'));
            foreach ($data['emails'] as $email){
                $mail->to($email);
            }
            $mail->to($this->_user->email);
            $mail->from('info@qforb.net',$this->_user->name);
            $mail->subject(html_entity_decode(__(':user | דוח מסירות | :project',[':user' => $this->_user->name, ':project' => $project->name])));
            $mail->reply($this->_user->email,$this->_user->name);
            $mail->body(View::factory('emails/report/delivery-report',[
                'message' => $data['message'],
                'user' => ['name' => $this->_user->name, 'email' => $this->_user->email],
                'image' => ($project->image_id) ? ("https://qforb.net/" .$project->main_image->originalFilePath()) : null,
                'reports' => ORM::factory('DeliveryReport')->where('id','IN',DB::expr('('.implode(',',$data['reports']).')'))->find_all()
            ])->render());
            $mail->send();
            ob_get_clean();
            echo "OK";
        }
    }
}