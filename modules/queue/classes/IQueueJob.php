<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.10.2016
 * Time: 1:45
 */
interface IQueueJob
{
    /**
     * Выполнение задачи
     * @return bool
     */
    function perform();
}