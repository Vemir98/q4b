<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 29.04.2020
 * Time: 18:13
 */
?>

<div id="report-delivery" class="new-styles">
    <report-delivery
    site-url="<?=trim(URL::site('','https'),'/')?>/"
    company-txt="<?=__('Company')?>"
    project-txt="<?=__('Project')?>"
    date-range-txt="<?=__('Report Range')?>"
    structure-txt="<?=__('Structure')?>"
    floor-txt="<?=__('Floor')?>"
    place-txt="<?=__('Place')?>"
    show-txt="<?=__('Show')?>"
    report-id-txt="<?=__('Report ID')?>"
    customer-name-txt="<?=__('Customer name')?>"
    date-txt="<?=__('Date')?>"
    quality-mark-txt="<?=__('Quality mark')?>"
    protocol-txt="<?=__('Protocol')?>"
    qc-report-txt="<?=htmlentities(__('QC Report'))?>"
    select-company-txt="<?=__('Select Company')?>"
    select-projects-txt="<?=__('Select project(s)')?>"
    select-structure-txt="<?=__('Select_structure')?>"
    select-floor-txt="<?=__('Select_floor')?>"
    select-place-txt="<?=__('Select_place')?>"
    print-txt="<?=__('Print')?>"
    save-txt="<?=__('Save')?>"
    no-items-text="<?=__('No items to show')?>"
    more-txt="<?=__('More')?>"
    translations='<?=json_encode($translations)?>'
    />
</div>
<div id="send-reports-modal">
    <modal modal-id="deliveryEmailModal">
        <template slot="header"><h3><?=__('Send reports by email')?></h3></template>
        <template slot="body" slot-scope="{confirmed}">
            <send-delivery-reports
                    site-url="<?=trim(URL::site('','https'),'/')?>/"
                    choose-txt="<?=__('Choose whom to send')?>"
                    type-email-txt="<?=htmlentities(__('Please type email'))?>"
                    name-txt="<?=__('Name')?>"
                    profession-txt="<?=__('Profession')?>"
                    email-txt="<?=__('Email')?>"
                    message-txt="<?=__('Message')?>"
                    your-text-txt="<?=__('Your text here')?>"
                    send-to-txt="<?=__('Send to')?>"
                    select-existing-txt="<?=__('Choose from existing users')?>"
                    mailing-url="<?=URL::site('/reports/delivery/send_email','https')?>"
                    :confirmed="confirmed"
            />
        </template>
        <template slot="footer" slot-scope="{confirm}" ><button class="confirm" @click="confirm()"><?=__('Send')?></button>
        </template>
    </modal>
</div>
<script>

    var app = new Vue({
        el: '#report-delivery',
    })


    var sendReportsModal = new Vue({
        el: '#send-reports-modal',
    })
</script>