<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 19.10.2016
 * Time: 13:39
 */
class Model_StandardFile extends Model_File
{
    //todo:: Проблема с plt ppg mime типами, их нет нужно как то решить вопрос
    protected $_allowed_ext = ['doc','docx','xls','xlsx','pdf','ppg','plt','jpg','jpe','jpeg','png','gif','tif','tiff'];
}