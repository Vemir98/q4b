<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 13.05.2019
 * Time: 7:45
 */

class Model_PushNotification extends ORM
{
    const Daily = 1;
    const MultipleADay = 2;
    const Weekly = 3;
    const Monthly = 4;

    protected $_table_name = 'push_notifications';
}