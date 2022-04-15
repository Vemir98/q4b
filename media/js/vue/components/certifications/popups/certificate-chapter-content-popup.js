Vue.component('certificate-chapter-content-popup', {
    template: `
        <div class="certificate-chapter-content-popup">
            <div class="q4b-textarea" style="height: 100%; padding-bottom: 23px">
                <div class="q4b-input-label">{{ trans.chapter_content }}</div>
                <textarea readonly style="max-height: none;min-height: auto;height: 98%;border: 0" cols="30" rows="10" :placeholder="trans.enter_chapter_content" v-model="chapter.text"></textarea>
            </div>
        </div>
    `,
    props: {
        translations: {required: true},
        chapter: {required: true}
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

