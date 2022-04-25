Vue.component('certificate-chapter-images-popup', {
    template: `
        <div class="certificate-chapter-images-popup">
        <div style="display: flex;justify-content: space-between;">
            <div class="q4b-input-label">{{ trans.attached_files }}</div>
            <div
                style="display: flex;justify-content: space-between;"
                class="q4b-remove-image-icon"
                @click="$emit('onClose')"
            >
            </div>
        </div>
            <div class="q4b-textarea" style="height: 100%;display: flex;flex-direction: column;justify-content: center;">
                <div class="certificate-chapter-images-list">
                    <img v-for="image in images" :key="image.id" :src="image.src" alt="">
                </div>
            </div>
        </div>
    `,
    props: {
        translations: {required: true},
        images: {required: true}
    },
    data() {
        return {
            trans: JSON.parse(this.translations),
        }
    },
    computed: {

    },
    components: {

    },
    created() {

    },
    mounted() {
    },
    watch: {

    },
    methods: {

    },
});

