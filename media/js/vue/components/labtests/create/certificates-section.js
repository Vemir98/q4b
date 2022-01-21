Vue.component('certificates-section', {
    template: `
        <div class="labtest-certificates">
            <div class="labtest-certificates-title">{{ trans.certificates }}</div>
            <div class="labtest-certificates-header">
                <div class="labtest-certificates-text-area-fields">
                    <div class="labtest-certificates-text-area-field">
                        <div class="input_item_label ltest-input-label">{{ trans.description }}</div>
                        <div class="labtest_edit_textarea">
                            <textarea cols="30" rows="10" @input="descriptionChanged">{{ currentCertificate.description }}</textarea>
                        </div>
                    </div>
                    <div class="labtest-certificates-text-area-field">
                        <div class="input_item_label ltest-input-label">{{ trans.notes }}</div>
                        <div class="labtest_edit_textarea">
                            <textarea cols="30" rows="10" @input="notesChanged">{{ currentCertificate.notes }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="filters-wraper flex-start labtest-certificates-filters">
                    <div class="filter-item">
                        <div class="ltest-input">
                            <div class="input_item_label ltest-input-label">{{ trans.lab_certificate }}</div>
                            <input type="text" v-model="currentCertificate.labCertificate" autocomplete="off" :placeholder="trans.lab_certificate">
                        </div>
                    </div>
                    <div class="filter-item">
                        <div class="ltest-input">
                            <div class="input_item_label ltest-input-label">{{ trans.fresh_concrete_strength }}</div>
                            <input type="number" v-model="currentCertificate.freshConcreteStrength" autocomplete="off" :placeholder="trans.fresh_concrete_strength">
                        </div>
                    </div>
                    <div class="filter-item">
                        <div class="ltest-input">
                            <div class="input_item_label ltest-input-label">{{ trans.roll_strength }}</div>
                            <input type="number" v-model="currentCertificate.rollStrength" autocomplete="off" :placeholder="trans.roll_strength">
                        </div>
                    </div>
                </div>
            </div>
            <div class="labtest-certificates-body"></div>
        </div>
        `,
    props: {
        projectId: {required: false},
        translations: {required: true}
    },
    components: {
        Multiselect: window.VueMultiselect.default,
    },
    data() {
        return {
            trans: JSON.parse(this.translations),
            currentCertificate: {
                description: '',
                notes: '',
                labCertificate: '',
                freshConcreteStrength: '',
                rollStrength: '',
            },
            certificates: [],
        }
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
        canCreateCertificate() {
            return (this.currentDeliveryCertificateText.length > 0)
        }
    },
    created() {
    },
    mounted() {

    },
    watch: {
        currentCertificate: {
            handler: function(cert) {
                this.$emit('currentCertificateChanged', cert)
            },
            deep: true
        }
    },
    methods: {
        descriptionChanged(event) {
            this.currentCertificate.description = event.target.value;
        },
        notesChanged(event) {
            this.currentCertificate.notes = event.target.value;
        },
        getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.ceil(max);
            return Math.floor(Math.random() * (max - min)) + min;
        }
    },
});

