<?php


class Controller_Api_Projects_ElApprovals extends HDVP_Controller_API
{
    /**
     * Create Element approval
     * https://qforb.net/api/json/<appToken>/el-approvals
     */
    public function action_index_post(){
        die('POST Create El Approval');
    }

    /**
     * Update Element approval
     * https://qforb.net/api/json/<appToken>/el-approvals/<id>
     */
    public function action_index_put(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        die('PUT Update El Approval - id='.$id);
    }

    /**
     * Get Element approval
     * https://qforb.net/api/json/<appToken>/el-approvals/<id>
     */
    public function action_index_get(){
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        die('GET Get El Approval data - id='.$id);
    }

    /**
     * returns element approvals filtered list
     * returned data will be as rows[{}],pages: {total,offset,limit}
     * https://qforb.net/api/json/<appToken>/el-approvals/list
     * https://qforb.net/api/json/<appToken>/el-approvals/list/page/<page>
     */
    public function action_list_post(){
        die('POST Get El Approvals filtered list');
    }

    /**
     * returns list of users to be informed
     * https://qforb.net/api/json/<appToken>/el-approvals/notifications
     */
    public function action_notifications_get(){
        die('GET Get El Approvals notifications');
    }

    /**
     * Updated list of users to be informed
     * https://qforb.net/api/json/<appToken>/el-approvals/notifications
     */
    public function action_notifications_put(){
        die('PUT El Approvals notifications');
    }
}