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
                                <div class="q4b-input-text">
                                    <div class="q4b-input-label">{{ trans.certificate_description }}</div>
                                    <input 
                                        type="text"
                                        autocomplete="off"
                                        class="q4b-input-text"
                                        :placeholder="trans.enter_certificate_description"
                                        v-model="certificate.name"
                                    >
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
                            <certificate-participants
                                :translations="translations"
                                :data="[]"
                                :canChange="true"
                                @participantsUpdated="updateCertificateParticipants($event)"
                            />
                        </div>
                        <div class="certificate-chapters-form">
                            <template v-for="(certificateChapter, chapterIndex) in certificate.chapters">
                                <certificate-chapter
                                    :translations="translations"
                                    :certificateChapter="certificateChapter"
                                    :globalChapters="filteredGlobalChapters"
                                    :canChange="true"
                                    @chapterUpdated="updateCertificateChapter($event)"
                                    @chapterDeleted="deleteCertificateChapter($event)"
                                    :key="chapterIndex"
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
                    <div class="modal-footer-buttons">
                        <button 
                            class="q4b-btn-success"
                            :class="{'q4b-disabled': !canCreateCertificate}"
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
            }
        }
    },
    computed: {
        canCreateChapter() {
            return (this.certificate.chapters.length < this.globalChapters.length)
        },
        canCreateCertificate() {
            // return false;
            if(!((this.certificate.name.trim()).length > 0)) return false;

            if(!(this.certificate.participants?.length > 0)) return false;

            if(!(this.certificate.chapters.length > 0)) return false;

            let chaptersValid = true;
            if(this.certificate.chaptersUpdated) this.certificate.chaptersUpdated = false;

            this.certificate.chapters.forEach(chapter => {
                if(!chapter.selectedChapter) chaptersValid = false;
                if(!(chapter.text.length > 0)) chaptersValid = false;
            })

            return chaptersValid;
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
            if(!this.canCreateCertificate) return false;
            certificateForm.craftId = this.craft.id;
            this.$emit('certificateCreated', certificateForm);
            this.$emit('onClose');
        },
        getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.ceil(max);
            return Math.floor(Math.random() * (max - min)) + min;
        },
    },
    mounted() {
    }
});

