<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:08
 */

Route::set('site.companiesWithFilters','companies(/status/<status>)(/sorting/<sorting>)(/page/<page>)(/export/<export>)',['status' => implode('|',Enum_CompanyStatus::toArray()),'sorting' => 'name|type', 'page' => '[0-9]+','export' => 'excel'])
    ->defaults([
        'controller' => 'companies',
        'action' => 'list'
    ]);

Route::set('site.companies2params','companies/<action>/<company_id>/<id>',['action' => '[a-z0-9_]+', 'company_id' => '[0-9]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'companies',
    ]);
Route::set('site.companies','companies(/<action>(/<id>))',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'companies',
        'action' => 'list'
    ]);
Route::set('site.companies.files','companies/<action>/<param1>/<param2>/<token>',['action' => 'download_standards_file|delete_standards_file', 'param1' => '[0-9]+', 'param2' => '[0-9]+','token' => '[a-z0-9]+'])
    ->defaults([
        'controller' => 'companies',
    ]);