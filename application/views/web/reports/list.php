<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:14
 */
    $isSubcontractor = false;
    $roleName = Auth::instance()->get_user()->getRelevantRole('name');
    $subcontractorsArr = Kohana::$config->load('subcontractors')->as_array();
    if (array_key_exists($roleName, $subcontractorsArr)) {
        $isSubcontractor = true;
    }

?>
<section class="content-projects">
    <div class="desktop-layout">
        <div class="content-projects-list">

            <ul class="q4-list-items">
                <li class="q4-list-item">
                    <figure>
                        <a href="<?=URL::site('reports')?>"><img src="/media/img/reports_icon.svg" alt="<?=__('QC Report')?>"></a>
                        <figcaption><a href="<?=URL::site('reports')?>"><?=__('QC Report')?></a></figcaption>
                    </figure>
                </li>
                <?if(!$isSubcontractor):?>
                    <?if(Usr::can(Usr::READ_PERM,'Controller_QualityReports')):?>
                        <li class="q4-list-item">
                            <figure>
                                <a href="<?=URL::site('reports/quality')?>"><img src="/media/img/qreport.svg" alt="<?=__('Quality Report')?>" style="width: 71%;margin-left: 14px;"></a>
                                <figcaption><a href="<?=URL::site('reports/quality')?>"><?=__('Quality Report')?></a></figcaption>
                            </figure>

                        </li>
                    <?endif;?>
                    <!--                --><?//if(Usr::can(Usr::READ_PERM,'Controller_TasksReports',Enum_UserPriorityLevel::General)):?>
                    <li class="q4-list-item">
                        <figure>
                            <a href="<?=URL::site('reports/tasks')?>"><img src="/media/img/treport.svg" alt="<?=__('Tasks report')?>" style="width: 71%;margin-left: 14px;"></a>
                            <figcaption><a href="<?=URL::site('reports/tasks')?>"><?=__('Tasks report')?></a></figcaption>
                        </figure>

                    </li>
                    <!--                --><?//endif;?>
                    <!--                --><?//if(Usr::can(Usr::READ_PERM,'Controller_TasksReports',Enum_UserPriorityLevel::General)):?>
                    <li class="q4-list-item">
                        <figure>
                            <a href="<?=URL::site('reports/place')?>"><img src="/media/img/preport.svg" alt="<?=__('Place report')?>" style="width: 71%;margin-left: 14px;"></a>
                            <figcaption><a href="<?=URL::site('reports/place')?>"><?=__('Place report')?></a></figcaption>
                        </figure>

                    </li>
                    <!--                --><?//endif;?>
                    <?if(Usr::can(Usr::READ_PERM,'Controller_DeliveryReports',Enum_UserPriorityLevel::General)  OR in_array(strtolower(Auth::instance()->get_user()->email),Kohana::$config->load('deliveryPermissionsEmails')->as_array())):?>
                        <li class="q4-list-item">
                            <figure>
                                <a href="<?=URL::site('reports/delivery')?>"><img src="/media/img/dlreport.svg" alt="<?=__('Delivery report')?>" style="width: 71%;margin-left: 14px;"></a>
                                <figcaption><a href="<?=URL::site('reports/delivery')?>"><?=__('Delivery report')?></a></figcaption>
                            </figure>

                        </li>
                    <?endif;?>
                <?endif?>
                <li class="q4-list-item">
                    <figure>
                        <a href="<?=URL::site('reports/approve_element')?>"><img src="/media/img/approve_element_report_icon.svg" alt="<?=__('approve_element_uppercase')?>"></a>
                        <figcaption><a href="<?=URL::site('reports/approve_element')?>"><?=__('approve_element_uppercase')?></a></figcaption>
                    </figure>
                </li>
                <li class="q4-list-item">
                    <figure>
                        <a href="<?=URL::site('reports/labtests')?>" onclick="sessionStorage.removeItem('labtests-page')"><img src="/media/img/lab-control.svg" alt="<?=__('lab_control_reports')?>" style="width: 75%"></a>
                        <figcaption><a href="<?=URL::site('reports/labtests')?>" onclick="sessionStorage.removeItem('labtests-page')"><?=__('lab_control_reports')?></a></figcaption>
                    </figure>
                </li>
                <?if(Usr::can(Usr::READ_PERM,'Controller_CertificatesReports')):?>
                <li class="q4-list-item">
                    <figure>
                        <a href="<?=URL::site('reports/certificates')?>"><img src="/media/img/certificate.svg" alt="<?=__('certificates_reports')?>" style="width: 75%;margin-top: 7px;"></a>
                        <figcaption><a href="<?=URL::site('reports/certificates')?>"><?=__('certificates_reports')?></a></figcaption>
                    </figure>
                </li>
                <?endif;?>
            </ul>

        </div>
    </div>

</section>
