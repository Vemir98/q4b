Vue.component('confirm-popup', {
    template: `
        <div class="modal modal-cover" @click="$emit('onClose')">
            <div class="confirm-popup" @click.stop>
                <div class="confirm-popup-header">
                    <h1>{{ trans.confirm }}</h1>
                </div>
                <div class="confirm-popup-body">
                    <h4>{{ trans.are_you_sure_you_want_to_delete_this_message }}</h4>
                </div>
                <div class="confirm-popup-footer">
                    <button 
                        class="confirm-popup-btn" 
                        @click="$emit('onConfirm')"
                    >{{ trans.delete }}</button>
                    <button 
                        class="cancel-popup-btn" 
                        @click="$emit('onClose')"
                    >{{ trans.cancel }}</button>
                </div>
            </div>
        </div>
    `,
    props: {
        translations: { required: true }
    },
    computed: {

    },
    data() {
        return {
            trans: JSON.parse(this.translations)
        }
    },
    watch: {

    },
    methods: {

    },
    mounted() {
    }
});

