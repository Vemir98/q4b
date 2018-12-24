<?defined('SYSPATH') OR die('No direct script access.');?>

<div id="consultants-email-modal" data-backdrop="static" data-keyboard="false" class="modal fade in" role="dialog">

    <div class="modal-dialog q4_project_modal consultants-email-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form class="q4_form" action="<?=URL::site('/consultants/check_email')?>" method="post">
            	<input type="hidden" value="" name="x-form-secure-tkn"/>
                <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                        <div class="clear"></div>
                    </div>
                    <div class="q4_modal_sub_header">
                        <h3><?=__('Enter user email address to proceed')?></h3>
                    </div>
                </div>
                <div class="modal-body bb-modal q4-modal-body-small">
                    <label class="table_label"><?=__('Email')?></label>
                    <input type="text" name="email" class="q4-form-input q4_email" value="">
                </div>
                <div class="modal-footer text-center">
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="#" class="q4-btn-lg light-blue-bg q4-modal-dismiss check-email" data-toggle="modal" data-url="<?=URL::site('/consultants/check_email')?>"><?=__('Check and Continue')?></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>