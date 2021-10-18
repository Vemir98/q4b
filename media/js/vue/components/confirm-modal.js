Vue.component('confirm-modal', {
    template: `
        <div>
            <div class="modal in" data-backdrop="static" data-keyboard="false" role="dialog" style="display: block;">
                <div class="modal-dialog confirmation-dialog">
                    <div class="modal-content">
                        <div class="confirmation-modal-header">
                            <button type="button" class="close" @click="$emit('closeConfirm')">
                                <i class="q4bikon-close"></i>
                            </button>
                            <div class="clear"></div>
                        </div>
                        <div class="confirmation-modal-body text-center">
<!--                            <p class="red">{{ msg }} <span class="confirmation-object">"{{ deletable }}"</span>?</p>-->
                            <p :class="[confirmButtonText ? 'orange' : 'red']">{{ msg }} {{ deletable }} {{ deletableId }} ?</p>
                        </div>
                        <div class="confirmation-modal-footer">
                            <a class="btn btn-cancel" @click="$emit('closeConfirm')">{{ trans.cancel }}</a>
                            <a :class="['btn','btn-confirm', (confirmButtonText ? 'orange' : 'red')]" @click="$emit('deleteConfirmed', modalData)">{{ confirmButtonText ? confirmButtonText : trans.delete }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop in"></div>
        </div>
`,

    props: {
        trans: {required: true},
        msg: {required: true},
        deletable: {required: false},
        deletableId: {required: false},
        modalData: {required: false},
        confirmButtonText: {required: false}
    },

    data() {
        return {
           //
        }
    },
    methods: {
      //
    },

});

