<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 01.06.2017
 * Time: 11:46
 */
class ORMPaginate
{
    protected $_object;
    protected $_routeParams;
    protected $_paginationSettings = [
        'items_per_page' => 10,
        'view'              => 'pagination/project',
        'current_page'      => ['source' => 'route', 'key'    => 'page'],
    ];

    public function __construct(ORM $object,array $routeParams = null, array $paginationSettings = null)
    {
        $this->_object = clone($object);
        $this->_routeParams = Request::current()->param();
        $this->_routeParams['action'] = Request::current()->action();
        if(!empty($routeParams))
        $this->_routeParams = Arr::merge($this->_routeParams,$routeParams);
        if(!empty($paginationSettings))
        $this->_paginationSettings = Arr::merge($this->_paginationSettings,$paginationSettings);
    }

    /**
     * Возвращает массив [pagination => Pagination, items => [QualityControl,QualityControl,...]]
     * @return array
     */
    public function getData(){
        $obj = clone($this->_object);
        $this->_paginationSettings['total_items'] = $obj->count_all();
        $pagination = Pagination::factory($this->_paginationSettings)
            ->route_params($this->_routeParams);
        return ['pagination' => $pagination, 'items' => $this->_object->limit($pagination->items_per_page)->offset($pagination->offset)->find_all(),'total_items' => $this->_paginationSettings['total_items']];
    }

}