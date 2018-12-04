<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 25.04.2017
 * Time: 1:17
 */
class Enum_QualityControlConditionLevel extends Enum
{
    const NotCompatibleWithCraft = 'not_compatible_with_craft';
    const NotStandard = 'not_standard';
    const DoesNotMatch = 'does_not_match';
    const NotCompatibleWithPlan = 'not_compatible_with_plan';
}