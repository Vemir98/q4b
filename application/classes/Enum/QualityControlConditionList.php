<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 25.04.2017
 * Time: 1:24
 */
class Enum_QualityControlConditionList extends Enum
{
    const ForImmediateTreatment = 'for_immediate_treatment';
    const DoNotGoThroughStage = 'do_not_go_th_stage';
    const DoNotProceedWithoutSvApprove = 'do_not_prc_without_suv_approve';
}