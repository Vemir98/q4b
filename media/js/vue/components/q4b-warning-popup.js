Vue.component('warning-popup', {
    template: `
        <div class="modal modal-cover" @click="$emit('onClose')">
            <div class="q4b-confirm-popup" @click.stop>
                <div class="q4b-confirm-popup-header">
                    <h1>{{ trans.no_search_results }}</h1>
                </div>
                <div class="q4b-confirm-popup-body">
                    <h4>{{ message }}</h4>
                </div>
                <div class="q4b-confirm-popup-footer">
                    <button 
                        class="q4b-cancel-popup-btn" 
                        @click="$emit('onClose')"
                    >{{ trans.close }}</button>
                </div>
            </div>
        </div>
    `,
    props: {
        translations: { required: true },
        message: { required: true },
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

