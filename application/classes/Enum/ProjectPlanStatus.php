<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 25.04.2017
 * Time: 0:37
 */
class Enum_ProjectPlanStatus extends Enum
{
    const ForReference = 'for_reference';
    const ForApproval = 'for_approval';
    const ToTheTender = 'to_the_tender';
    const ToExecute = 'to_execute';
    const Other = 'other';
}