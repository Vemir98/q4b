<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 17.11.2016
 * Time: 5:19
 */
class Enum_UserStatus extends Enum
{
    const Pending = 'pending';
    const Active = 'active';
    const Blocked = 'blocked';
    const Deleted = 'deleted';
}