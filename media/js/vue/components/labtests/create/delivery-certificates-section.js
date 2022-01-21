Vue.component('delivery-certificates-section', {
    template: `
        <div class="labtest-delivery-certificates">
            <div class="labtest-certificates-title">{{ trans.delivery_certificates }}</div>
            <div class="labtest-delivery-certificates-header">
                <div class="ltest-input">
                    <div class="input_item_label ltest-input-label">{{ trans.delivery_certificates }}</div>
                    <input type="text" v-model="currentDeliveryCertificateText" autocomplete="off" :placeholder="trans.enter_delivery_certificate">
                </div>
                <div 
                    :class="['ltest-add-btn', {'labtest-disabled': (!canCreateDeliveryCertificate)}]"
                    @click="createDeliveryCertificate"
                >
                    {{ trans.add }}
                </div>
            </div>
            <div class="labtest-delivery-certificates-body">
                <div class="labtest-delivery-certificates-list">
                    <div 
                        class="labtest-delivery-certificates-item" 
                        v-for="delCert in deliveryCertificates" 
                        :key="delCert.uid"
                    >
                        <div class="labtest-delivery-certificate-text">{{ delCert.text }}</div>
                        <div class="labtest-delete-icon" @click="removeDeliveryCertificate(delCert)"></div>
                    </div>
                </div>
            </div>
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
            currentDeliveryCertificateText: '',
            deliveryCertificates: [],
        }
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
        canCreateDeliveryCertificate() {
            return (this.currentDeliveryCertificateText.length > 0)
        }
    },
    created() {
    },
    mounted() {

    },
    watch: {

    },
    methods: {
        createDeliveryCertificate() {
            this.deliveryCertificates.push({
                id: this.getRandomInt(10000, 99999),
                text: this.currentDeliveryCertificateText
            })
            this.currentDeliveryCertificateText = '';
            this.$emit('deliveryCertificatesUpdated', this.deliveryCertificates)
        },
        removeDeliveryCertificate(certificate) {
            const certIndex = this.deliveryCertificates.findIndex(delCert => +delCert.id === +certificate.id);

            this.deliveryCertificates.splice(certIndex,1);
            this.$emit('deliveryCertificatesUpdated', this.deliveryCertificates)
        },
        getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.ceil(max);
            return Math.floor(Math.random() * (max - min)) + min;
        }
    },
});

