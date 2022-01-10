<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 1/13/21
 * Time: 12:12 PM
 */
use Helpers\PushHelper;

class Controller_Api_Notification extends HDVP_Controller_API {
    protected $_checkToken = false;

    public function action_send()
    {
        try {

//            $fpns = new HDVP\FirebasePushNotification();
//            $fpns->notify(['f4XUrR3pQ5ulGWxW_gVPo5:APA91bF_skJlXW4U_H8AmaHfCKq6CKdnLp2AwtnWcDgrloh3_u6eU5Dn8KgsSH2Cvp3M6VsWXGgGmsL1G7GZ3hQ8WhFIwPg6Gh0KRzOa53uY_Z9ca9aY5vICB05XHgNNvybayJs6xV37'], [
//                'type' => 'testType',
//                'action' => 'testAction',
//                'id' => 'testTypeId',
//                'projectId' => 'testProjectId'
//            ]);
//
//            $query = 'SELECT qc.id
//	FROM quality_controls qc
//	LEFT join el_approvals ea on qc.el_approval_id = ea.id
//	WHERE (qc.el_approval_id is not null) and (ea.id is null)';
//
//            $a = array_column(DB::query(Database::SELECT, $query)->execute()->as_array(), 'id');
//
//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($a); echo "</pre>"; exit;



//            $query = 'SELECT
//                ea.id
//                FROM el_approvals ea ORDER BY ea.id DESC';
//
//            $elApprovalIds = array_column(DB::query(Database::SELECT, $query)->execute()->as_array(), 'id');
//
//            $query = "SELECT
//                qc.id,
//                qc.craft_id as craftId,
//                qc.el_approval_id as elApprovalId,
//                ea.element_id as elementId,
//                qc.element_id as qcElementId
//                FROM quality_controls qc
//                INNER JOIN el_approvals ea on qc.el_approval_id = ea.id
//                WHERE qc.el_approval_id IN (:elAppIds)";
//
//            $elApprovalIds = DB::expr(implode(',', $elApprovalIds));
//
//            $elApprovalsQcList =  DB::query(Database::SELECT, $query)
//                ->bind(':elAppIds', $elApprovalIds)
//                ->execute()->as_array();
//
//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($elApprovalsQcList); echo "</pre>"; exit;
//
//            foreach ($elApprovalsQcList as $qc) {
//                $queryData = [
//                    'element_id' => $qc['elementId']
//                ];
//                DB::update('quality_controls')
//                    ->set($queryData)
//                    ->where('id', '=', $qc['id'])
//                    ->execute($this->_db);
//            }

            $this->_responseData = [];
            $this->_responseData['status'] = 'success';
        } catch (Exception $exception) {
            throw API_Exception::factory(500,$exception->getMessage());
        }
    }
}