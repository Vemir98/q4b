<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 18.04.2022
 * Time: 12:00
 */
class Controller_CertificatesReports extends HDVP_Controller_Template
{
    protected $_actions_perms = [
        'index' => [
            'GET' => 'read',
            'POST' => 'read'
        ]
    ];
    public function before()
    {
        parent::before();
//        if ($this->auto_render === TRUE)
//        {
//            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Reports'))->set_url('/reports/list'));
//            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Delivery report'))->set_url('/reports/delivery'));
//        }
    }

    public function action_index()
    {
//        die('mtav');
        VueJs::instance()->addComponent('reports/certificates/index');
        VueJs::instance()->addComponent('reports/certificates/generate');
        VueJs::instance()->addComponent('reports/certificates/list');

        VueJs::instance()->includeMultiselect();
        $translations = [
            "select_all" => __('select all'),
            "unselect_all" => __('unselect all'),
            "crafts" => __('Crafts'),
            "status" => __('Status'),
            "set_statuses" => __('set_statuses'),
            "set_specialities" => __('set_specialities'),
            'waiting' => __('waiting'),
            'approved' => __('Approved'),
            'sample_required' => __('sample_required'),
            "generate" => __('Generate'),
            "select_company" => __('Select Company'),
            "select_project" => __('Select project'),
            'certificates' => __('Certificates'),
            "company_name" => __('company name'),
            "owner" => __('Owner'),
            "project_name" => __('Project name'),
            "start_date" => __('Start Date'),
            "end_date" => __('End Date'),
            "project_id" => __('Project ID'),
            "project_status" => __('project status'),
            "address" => __('Address'),
            "print" => __('Print'),
            "export" => __('Export'),
            'approved_by' => __('Approved by'),
            'description' => __('Description1'),
            'update_date' => __('Update Date'),
            'content' => __('content'),
            'image' => __('Image'),
            'more' => __('More'),
            "company" => __('Company'),
            "project" => __('Project'),
            'participants_list' => __('participants_list'),
            'chapters_list' => __('chapters_list'),
            'total' => __('Total')
        ];


        $this->template->content = View::make('reports/certificates/index', [
            'translations' => $translations,
            'userProfession' => $this->_user->professions->find()->name,
            'userRole' =>  $this->_user->getRelevantRole('name')
        ]);
    }
}