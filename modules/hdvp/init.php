<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 09.08.2016
 * Time: 23:34
 * Инициализация ядра HDVP
 */
require_once (dirname(__FILE__).DS.'helpers'.EXT);
Event::instance()->listen('onFrameworkInitiated',['HDVP_Core','init']);

