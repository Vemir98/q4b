Vue.component('certificate-chapter', {
    template: `
        <div class="certificate-chapter" :ref="'certificateChapter_'+certificateChapter.uid">
            <div class="certificate-chapter-actions">
                <div
                    class="q4b-remove-image-icon"
                    :class="{'q4b-disabled': !canChange}"
                    @click="removeCertificateChapter"
                >
                </div>
            </div>
            <div class="certificate-chapter-data">
                <div>
                    <div class="filter-item">
                        <div class="multiselect-col">
                            <div class="q4b-input-label">{{ trans.chapter }}</div>
                            <multiselect 
                                v-model="selectedChapter"
                                :option-height="104" 
                                :placeholder="trans.choose_chapter"
                                v-validate.immediate="'required'"
                                name="certificateChapterName"
                                :disabled="!canChange" 
                                :options="globalChapters" 
                                track-by="id" 
                                label="name" 
                                :searchable="true" 
                                :allow-empty="false" 
                                :show-labels="false"
                            >
                                <template slot="singleLabel" slot-scope="props">{{ props.option.name }}</template>
                                <template slot="option" slot-scope="props">
                                    <span>{{ props.option.name }}</span>
                                </template>
                                <template slot="option-selected" slot-scope="props">
                                    <span>{{ props.option.name }}</span>
                                </template>
                            </multiselect>
                        </div>
                        <span v-show="errors.has('certificateChapterName') && showErrors" class="q4b-error-text">{{ errors.first('certificateChapterName') }}</span>
                    </div>
                    <file-control
                        :translations="translations"
                        :data="chapter.images"
                        :includeEditor="false"
                        :canChange="canChange"
                        :allowedFormats="['png','jpeg','jpg']"
                        @filesUpdated="updateChapterImages($event)"
                    />
                </div>
                <div class="certificate-chapter-content">
                    <div class="q4b-textarea" style="height: 100%; padding-bottom: 23px">
                        <div class="q4b-input-label">{{ trans.chapter_content }}</div>
                        <textarea
                            :class="{'q4b-textarea-error-border': (errors.has('certificateChapterContent') && showErrors)}"
                            style="max-height: none;min-height: auto;height: 100%"
                            cols="30"
                            rows="10"
                            :readonly="!canChange"
                            :placeholder="trans.enter_chapter_content"
                            v-model="chapterText"
                            name="certificateChapterContent"
                            v-validate.immediate="'required'"
                        ></textarea>
                    </div>
                    <span v-show="errors.has('certificateChapterContent') && showErrors" class="q4b-error-text">{{ errors.first('certificateChapterContent') }}</span>
                </div>
            </div>
        </div>
    `,
    props: {
        translations: {required: true},
        globalChapters: {required: true},
        certificateChapter: {required: true},
        canChange: {required: true},
        showErrors: {required: true},
        scrollToChapter: {required: false}
    },
    data() {
        return {
            trans: JSON.parse(this.translations),
            chapter: JSON.parse(JSON.stringify(this.certificateChapter)),
            initialChapter: null,
            selectedChapter: null,
            chapterText: '',
            chapterImages: []
        }
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
    },
    components: {
        Multiselect: window.VueMultiselect.default,
    },
    created() {
        this.initialChapter = JSON.parse(JSON.stringify(this.chapter))
    },
    mounted() {
        if(!this.certificateChapter.id) {
            this.$refs['certificateChapter_'+this.certificateChapter.uid].scrollIntoView({block: "center", behavior: "smooth"})
        }

        this.chapterText = this.chapter.text;
        this.selectedChapter = this.chapter.selectedChapter;
        this.chapterImages = this.chapter.images;
    },
    watch: {
        selectedChapter(chapter) {
            if(!this.canChange) return false;
            this.chapter.selectedChapter = chapter;
            this.chapter.chapterId = chapter.id;
            this.$emit('chapterUpdated', this.chapter);
        },
        chapterText(chapterText) {
            if(!this.canChange) return false;
            this.chapter.text = chapterText;
            this.$emit('chapterUpdated', this.chapter);
        },
        scrollToChapter(value) {
            if(value) {
                this.$refs['certificateChapter_'+this.certificateChapter.uid].scrollIntoView({block: "center", behavior: "smooth"})
                this.$emit('scrolled')
            }
        }
    },
    methods: {
        updateChapterImages(images) {
            if(!this.canChange) return false;
            this.chapter.images = images;
            this.$emit('chapterUpdated', this.chapter);
        },
        removeCertificateChapter() {
            if(!this.canChange) return false;
            this.$emit('chapterDeleted', this.chapter)
        }
    },
});

