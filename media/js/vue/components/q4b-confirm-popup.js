Vue.component('confirm-popup', {
    template: `
        <div class="modal modal-cover" @click="$emit('onClose')">
            <div 
                class="q4b-confirm-popup" 
                @click.stop
                :style="{width: (width || baseWidth)}"
                >
                <div class="q4b-confirm-popup-header">
                    <h1>{{ trans.confirm }}</h1>
                </div>
                <div class="q4b-confirm-popup-body">
                    <h4>{{ message }}</h4>
                </div>
                <div class="q4b-confirm-popup-footer">
                    <button 
                        class="q4b-confirm-popup-btn" 
                        @click="$emit('onConfirm', item)"
                    >{{ confirmText }}</button>
                    <button 
                        class="q4b-cancel-popup-btn" 
                        @click="$emit('onClose')"
                    >{{ cancelText }}</button>
                </div>
            </div>
        </div>
    `,
    props: {
        translations: { required: true },
        message: { required: true },
        item: { required: true },
        confirmText: {required: true},
        cancelText: {required: true},
        width: {required: false}
    },
    computed: {

    },
    data() {
        return {
            trans: JSON.parse(this.translations),
            baseWidth: '400px'
        }
    },
    watch: {

    },
    methods: {

    },
    mounted() {
        console.log(`'modal barev`)
    }
});

