Vue.component('create-certification', {
    template: `
        <div class="modal modal-cover">
            <div class="q4b-modal" @click.stop>
                <div class="q4b-modal-header">
                    <h1>{{ trans.create_certificate_for }} {{ craft.name }}</h1>
                </div>
                <div class="modal-separator"></div>
                <div class="q4b-modal-body">
                    <div class="create-certificate-form">
                        <div class="certificate-form">
                            <div class="certificate-data">
                                <div class="q4b-input-text" ref="certificateDescription">
                                    <div class="q4b-input-label">{{ trans.certificate_description }}</div>
                                    <input 
                                        type="text"
                                        autocomplete="off"
                                        class="q4b-input-text"
                                        :class="{'q4b-input-error-border': (errors.has('certificateDescription') && showErrors)}"
                                        v-validate.immediate="'required'"
                                        name="certificateDescription"
                                        :placeholder="trans.enter_certificate_description"
                                        v-model="certificate.name"
                                    >
                                    <span v-show="errors.has('certificateDescription') && showErrors" class="q4b-error-text">{{ errors.first('certificateDescription') }}</span>
                                </div>
                                <div class="filter-item-checkbox" style="margin-top: 30px">
                                    <span class="check-task">
                                        <input
                                            type="checkbox"
                                            style="position: relative !important;right: 0"
                                            v-model="certificate.sampleRequired"
                                        >
                                        <span class="checkboxImg"></span>
                                    </span>
                                    <div class="filter-item-label flex-between">{{ trans.sample_required }}</div>
                                </div>
                            </div>
                            <div ref="certificateParticipants">
                                <certificate-participants
                                    :translations="translations"
                                    :data="[]"
                                    :canChange="true"
                                    :showErrors="showErrors"
                                    @participantsUpdated="updateCertificateParticipants($event)"
                                />
                            </div>
                        </div>
                        <div class="certificate-chapters-form">
                            <template v-for="(certificateChapter, chapterIndex) in certificate.chapters">
                                <certificate-chapter
                                    :translations="translations"
                                    :certificateChapter="certificateChapter"
                                    :globalChapters="filteredGlobalChapters"
                                    :canChange="true"
                                    :showErrors="showErrors"
                                    :scrollToChapter="(scrollToChapter === chapterIndex)"
                                    @chapterUpdated="updateCertificateChapter($event)"
                                    @chapterDeleted="deleteCertificateChapter($event)"
                                    @scrolled="scrollToChapter = null"
                                    :key="certificateChapter.uid"
                                />
                            </template>
                        </div>
                    </div>
                </div>
                <div class="modal-separator"></div>
                <div class="q4b-modal-footer">
                    <button
                        class="q4b-btn-success add-chapter-btn"
                        :class="{'q4b-disabled': !canCreateChapter}"
                        @click="addCertificateChapter"
                    >{{ trans.add_chapter }}</button>
                    <span v-show="errors.has('certificateChaptersRequired') && showErrors" class="q4b-error-text">{{ errors.first('certificateChaptersRequired') }}</span>
                    <div class="modal-footer-buttons">
<!--                        <button -->
<!--                            class="q4b-btn-success"-->
<!--                            :class="{'q4b-disabled': !canCreateCertificate}"-->
<!--                            @click="createCertificate(certificate)"-->
<!--                        >{{ trans.create }}</button>-->
                        <button 
                            class="q4b-btn-success"
                            @click="createCertificate(certificate)"
                        >{{ trans.create }}</button>
                        <button 
                            class="q4b-modal-close-btn" 
                            @click="$emit('onClose')"
                        >{{ trans.cancel }}</button>
                    </div>
                </div>
            </div>
        </div>
    `,
    props: {
        translations: { required: true },
        craft: {required: true},
        globalChapters: { required: true }
    },
    data() {
        return {
            trans: JSON.parse(this.translations),
            certificate: {
                name: '',
                sampleRequired: false,
                chapters: [],
                participants: [],
                chaptersUpdated: false
            },
            showErrors: false,
            scrollToChapter: null
        }
    },
    computed: {
        canCreateChapter() {
            return (this.certificate.chapters.length < this.globalChapters.length)
        },
        filteredGlobalChapters() {
            if(this.certificate.chaptersUpdated) this.certificate.chaptersUpdated = false;

            return this.globalChapters.filter(globalChapter => {
                return (this.certificate.chapters.filter(certificateChapter => {
                    return globalChapter.id === certificateChapter?.selectedChapter?.id
                }).length === 0)
            })
        }
    },
    watch: {

    },
    methods: {
        canCreateCertificate() {
            // return false;

            let valid = true;

            if(!((this.certificate.name.trim()).length > 0)) {
                valid = false;
            }

            if(!(this.certificate.participants?.length > 0)) {
                this.errors.add({
                    field: 'certificateParticipantsRequired',
                    msg: this.trans.participants_required
                })
                valid = false;
            } else {
                this.errors.remove('certificateParticipantsRequired')
            }


            if(!(this.certificate.chapters?.length > 0)) {
                this.errors.add({
                    field: 'certificateChaptersRequired',
                    msg: this.trans.chapters_required
                })
                valid = false;
            } else {
                this.errors.remove('certificateChaptersRequired')
            }

            if(this.certificate.chaptersUpdated) this.certificate.chaptersUpdated = false;

            this.certificate.chapters.forEach((chapter, chapterIndex) => {
                if(!chapter.selectedChapter) {
                    this.errors.add({
                        field: `certificateChapterName_${chapterIndex}`,
                        msg: 'azaza'
                    });
                    valid = false;
                } else {
                    this.errors.remove(`certificateChapterName_${chapterIndex}`)
                }

                if(!(chapter.text.length > 0)) {
                    this.errors.add({
                        field: `certificateChapterContent_${chapterIndex}`,
                        msg: 'azaza'
                    });
                    valid = false;
                } else {
                    this.errors.remove(`certificateChapterContent_${chapterIndex}`)
                }
            })

            return valid;
        },
        addCertificateChapter() {
            if(!this.canCreateChapter) return false

            const chapterData = {
                id: null,
                uid: this.getRandomInt(100000, 9999999),
                selectedChapter: null,
                text: '',
                images: []
            }
            this.certificate.chapters.push(chapterData)
            this.errors.remove('certificateChaptersRequired')
            this.showErrors = false;
        },
        updateCertificateChapter(chapterData) {
            this.certificate.chapters.forEach((certificateChapter, index) => {
                if(certificateChapter.uid === chapterData?.uid) {
                    this.certificate.chapters[index] = chapterData;
                    this.certificate.chaptersUpdated = true;
                }
            })
        },
        updateCertificateParticipants(certificateParticipants) {
            this.certificate.participants = certificateParticipants;
        },
        deleteCertificateChapter(chapterToDelete) {
            this.certificate.chapters.forEach((certificateChapter, index) => {
                if(certificateChapter.uid === chapterToDelete?.uid) {
                    this.certificate.chapters.splice(index, 1)
                    this.certificate.chaptersUpdated = true;
                }
            })
        },
        createCertificate(certificateForm) {
            if(!this.canCreateCertificate()) {
                this.showErrors = true;
                this.scrollToError();
                return false;
            } else {
                this.showErrors = false;
            }
            certificateForm.craftId = this.craft.id;
            this.$emit('certificateCreated', certificateForm);
            this.$emit('onClose');
        },
        scrollToError() {
            if(this.errors.has('certificateDescription')) {
                this.$refs['certificateDescription'].scrollIntoView({block: "center", behavior: "smooth"})
                return true;
            }

            if(this.errors.has('certificateParticipantsRequired')) {
                this.$refs['certificateParticipants'].scrollIntoView({block: "center", behavior: "smooth"})
                return true;
            }

            this.certificate.chapters.some((chapter, chapterIndex) => {
                if(this.errors.has(`certificateChapterName_${chapterIndex}`)) {
                    this.scrollToChapter = chapterIndex;
                    this.errors.remove(`certificateChapterName_${chapterIndex}`)
                    return true;
                }

                if(this.errors.has(`certificateChapterContent_${chapterIndex}`)) {
                    this.scrollToChapter = chapterIndex;
                    this.errors.remove(`certificateChapterContent_${chapterIndex}`)

                    return true;
                }
            })
        },
        getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.ceil(max);
            return Math.floor(Math.random() * (max - min)) + min;
        },
    },
    mounted() {
        this.$validator.localize('msg');
    }
});

