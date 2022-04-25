Vue.component('certifications', {
    template: `
        <div class="universal-certification">
            <div v-if="showLoader" class="loader_backdrop_vue">
                <div class="loader"></div>
            </div>
            <div class="universal-certification-copyTo">
                <div class="inline-options justify-center multiselect-parent rm" v-if="companiesUrl || projectsUrl">
                        <label>{{copyTxt}}</label>
                        <div class="multiselect-row">
                            <div class="multiselect-col" v-if="companiesUrl">
                              <multiselect :class="{disabled: !hasCheckedItem}" v-model="selectedCompany" :options="companies" @select="companySelected" :placeholder="selectCompanyTxt" label="name" track-by="id"  :allow-empty="false" :searchable="false" :show-labels="false"></multiselect>
                            </div>
                            <div class="multiselect-col" v-if="projectsUrl">
                                <multiselect :class="{disabled: (companiesUrl && selectedCompany == null) || !hasCheckedItem }"  v-model="selectedProject" :tag-placeholder="selectProjectTxt" :placeholder="selectProjectTxt" label="name" track-by="id" :options="projects" :searchable="false" :show-labels="false"></multiselect>                                
                            </div>
                            <div class=" al-cent with-checkbox">
                                <button class="btn-copy" @click="copyItems" :class="{disabled: selectedCompany == null || !hasCheckedItem || (projectsUrl && selectedProject == null)}" >{{copyBtnTxt}}</button>
<!--                                <div v-if="(projectsUrl && selectedProject != null)"><input type="checkbox" id="include-files"  v-model="includeFiles"> <label for="include-files" class="craft-label include-files-checkbox">{{includeFilesTxt}}</label></div>-->
                            </div>                                                          
                        </div>
                    </div>
            </div>
            <div class="top-bar selectAll-craft">
                <input type="checkbox" id="is-all" @change="toggleSelection" v-model="selectAll"> <label for="is-all" class="craft-label">{{selectAllTxt}}</label>
                <!--<a href="#" @click.prevent="deleteSelected" v-if="hasCheckedItem" style="float: right;">Delete ICON</a>-->
            </div>
            <div class="craft-cert" v-for="craft,idx in items" :key="craft.id">
                <input type="checkbox" :id="'craftlbl' + craft.id" v-model="craft.checked" @change="craftCheckChanged">
                <label :for="'craftlbl' + craft.id" class="craft-label">{{craft.name}}</label><br>
                <button @click="openCreateCertificateModal(craft)" class="btn btn-add craft-btn-add">
                    <img src="/media/img/new-images/btn-add.svg" alt="add">
                </button>
                <div class="table craft-table">
                    <table v-if="craft.items.length > 0" id="cert-table">
                        <thead>
                            <th>
                                {{descTxt}}
                            </th>
                            <th>
                                {{ trans.sample_required }}
                            </th>
                            <th>
                                {{ trans.content }}
                            </th>
                            <th>
                                {{ trans.update_date }}
                            </th>
                            <th>
                                {{ trans.image }}
                            </th>
                            <th>
                                {{ trans.approved_by }}
                            </th>
                            <th>
                                {{ trans.status }}
                            </th>
                            <th>
                                {{moreTxt}}
                            </th>
                        </thead>
                        <tbody>
                            <template v-for="item, rIdx in craft.items">
                                <tr class="parent-tr" :class="{'openParent': item.showChapters}" :key="item.id">
                                    <td class="parent-td" @click="toggleCertificateChapters(idx, rIdx)" >
                                        <span>{{ item.name }}</span>
                                    </td>
                                    <td v-if="item.sampleRequired"><div  class="q4b-checked-icon"></div></td>
                                    <td v-else>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>{{ item.updatedAt ? convertTimestampToDate(item.updatedAt) : '' }}</td>
                                    <td>&nbsp;</td>
                                    <td>
                                        {{ item.approverName }}
                                    </td>
                                    <td>{{ trans[item.status] }}</td>
                                    <td>
                                        <div class="more-box delivery-more-box">
                                            <div class="more-icon" @click="toggleDropdown($event)">
                                                <img src="/media/img/more-vec.png" alt="">
                                            </div>
                                            <div class="edit-box hide">

                                                <span style="color: #7985A5;" @click="openUpdateCertificateModal(craft, item)">{{ trans.edit }}</span>
<!--                                                <span class="delete-btn">{{ trans.export }}</span> -->
                                                <span class="delete-btn" @click.prevent="openDeleteCertificatePopup(item)">{{deleteTxt}}</span> 
                                            </div>  
                                        </div>
                                    </td>
                                </tr>
                                <template v-for="(chapter, chapterIndex) in item.chapters">
                                    <tr class="child-tr" v-if="item.showChapters">
                                        <td scope="row" style="cursor: pointer" @click="openUpdateCertificateModal(craft, item)">{{ chapter.name }}</td>
                                        <td>&nbsp;</td>
                                        <td style="position:relative;">
                                            <div  
                                                class="q4b-content-icon"
                                                @click="showChapterContent(idx, rIdx, chapterIndex)"
                                            >
                                            </div>
                                            <certificate-chapter-content-popup
                                                :translations="translations"
                                                :chapter="chapter"
                                                v-if="chapter.showContent"
                                                @onClose="hideChapterImagesAndContents"
                                            />
                                        </td>
                                        <td>&nbsp;</td>
                                        <td style="position: relative">                                        
                                            <img
                                                v-if="(chapter.images.length > 0)" 
                                                @click="showChapterImages(idx, rIdx, chapterIndex)"
                                                class="certifications-image-icon" 
                                                src="/media/img/new-images/pdf-icon.svg" 
                                                alt="file"
                                            >
                                            <certificate-chapter-images-popup
                                                :translations="translations"
                                                :images="chapter.images"
                                                v-if="chapter.showImages && (chapter.images.length > 0)"
                                                @onClose="hideChapterImagesAndContents"
                                            />
                                        </td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            <create-certification
                v-if="createCertificationModal.display"
                :craft="createCertificationModal.craft"
                :globalChapters="globalChapters"
                :translations="translations"
                @onClose="closeCreateCertificateModal"
                @certificateCreated="createCertificateAPI($event)"
            />
            <update-certification
                v-if="updateCertificationModal.display"
                :craft="updateCertificationModal.craft"
                :globalChapters="globalChapters"
                :craftCertificate="certificateToUpdate"
                :translations="translations"
                :statuses="certificateStatuses"
                :userRole="userRole"
                @onClose="closeUpdateCertificateModal"
                @certificateUpdated="updateCertificateAPI($event)"
            />
            <confirm-popup
                v-if="deleteCertificationPopupDisplay"
                :translations="translations"
                :message="trans.are_you_sure_you_want_to_delete_this_certificate"
                :item="certificateToDelete"
                @onClose="deleteCertificationPopupDisplay = false"
                @onConfirm="deleteCertificateAPI($event)"
            />
        </div>
    `,
    props: {
        itemsUrl: {required: true},
        companiesUrl: {default: null},
        companyId: {default: null},
        projectId: {default: null},
        projectsUrl: {default: null},
        deleteUrl: {default: null},
        saveUrl: {required: true},
        copyUrl: {default: null},
        includeFilesTxt: {default: ''},
        selectAllTxt: {required: true},
        descTxt: {required: true},
        fileTxt: {required: true},
        uploadDateTxt: {required: true},
        statusTxt: {required: true},
        moreTxt: {required: true},
        deleteTxt: {required: true},
        copyTxt: {required: true},
        saveTxt: {required: true},
        copyBtnTxt: {required: true},
        selectCompanyTxt: {required: true},
        selectProjectTxt: {required: true},
        statusOptions: {
            required: true,
            type: Array
        },
        translations: {required: true},
        statuses: {required: true},
        userRole: {required: true}
    },
    components: {
        Multiselect: window.VueMultiselect.default
    },
    data() {
        return {
            showLoader: false,
            items: [],
            crafts: [],
            selectAll: false,
            company: null,
            project: null,
            companies: [],
            projects: [],
            includeFiles: false,
            selectedCompany: null,
            selectedProject: null,
            trans: JSON.parse(this.translations),
            createCertificationModal: {
                display: false,
                craft: null
            },
            updateCertificationModal: {
                display: false,
                craft: null
            },
            deleteCertificationPopupDisplay: false,
            certificateToDelete: null,
            certificateToUpdate: null,
            globalChapters: [],
            certificateStatuses: JSON.parse(this.statuses),
        };
    },
    watch: {
        testSelect1SelectedOption(option) {
            this.testSelect2SelectedOption = this.testSelect2Options.find(item => +item.id === +option.id)
        }
    },
    created() {
        axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
        this.getProjectChaptersAPI();
    },
    methods: {
        init: function(){

            axios.get(this.itemsUrl)
                .then(response => {
                    this.crafts = response.data.items;
                    this.getProjectCertificatesAPI();
                });
            if(this.companiesUrl){
                axios.get(this.companiesUrl)
                    .then(response => {
                        this.companies = response.data.items;
                        if(this.companyId){
                            for(var i = 0; i < this.companies.length; i++){
                                if( ! this.projectId && this.companies[i].id == this.companyId){
                                    this.companies.splice(i,1);
                                    break;
                                }
                            }
                        }
                    });
            }
        },
        toggleDropdown (event) {
            var open = document.getElementsByClassName("edit-box");
            var moreBox = document.getElementsByClassName('more-box');

            window.addEventListener('click', function(e){
                var open = document.getElementsByClassName("edit-box");
                [].forEach.call(open, function(el) {
                    el.classList.add("hide");
                });
            });

            if (event.currentTarget.nextElementSibling.classList.contains("hide")) {
                event.stopPropagation();
                [].forEach.call(open, function(el) {
                    el.classList.add("hide");
                });
                event.currentTarget.nextElementSibling.classList.remove('hide')
            }else {
                [].forEach.call(open, function(el) {
                    el.classList.add("hide");
                });
            };

        },
        copyItems: function(){
            let data = {
                projectId: this.selectedProject ? this.selectedProject.id : null,
                companyId: this.selectedCompany.id,
                includeFiles: this.includeFiles,
                items: []
            };

            for(let i = 0; i < this.items.length; i++){
                if( ! this.items[i].items.length || !this.items[i].checked) continue;
                for(let j = 0; j < this.items[i].items.length; j++){
                    data.items.push(this.items[i].items[j]);
                }
            }
            const requestData = {
                certificatesIds: data.items.map(certificate => certificate.id)
            }

            if(requestData.certificatesIds.length > 0) {
                this.copyCertificatesAPI(data)
            }
        },
        companySelected: function(item){
            if( ! this.projectsUrl) return;
            this.selectedProject = null;
            axios.get(this.projectsUrl + '/' + item.id + '?fields=id,name')
                .then(response => {
                    this.projects = response.data.items;
                    for(var i = 0; i < this.projects.length; i++){
                        if(this.projects[i].id == this.projectId){
                            this.projects.splice(i,1);
                            break;
                        }
                    }
                });
        },
        toggleSelection: function(){
            for(var i = 0; i < this.items.length; i++){
                this.items[i].checked = this.selectAll;
            }
        },
        addNewItem: function (idx) {
            this.items[idx].items.push({
                id : '_' + Math.random().toString(36).substr(2, 9),
                craftId: this.items[idx].id,
                desc: '',
                file: null,
                status: {val: this.statusOptions[0].val, label: this.statusOptions[0].label},
                created: 0,
                companyId: this.companyId,
                projectId: this.projectId,
                edited: true,
                isNew: true,
                checked: false,
                inEditMode: true
            });
        },
        craftCheckChanged: function(){
            for(var i = 0; i < this.items.length; i++){
                if( ! this.items[i].checked){
                    this.selectAll = false;
                    return;
                }
            }
            this.selectAll = true;
        },
        deleteItem: function (idx,rIdx) {
            if(this.items[idx].items[rIdx].isNew == undefined){
                axios.post(this.deleteUrl,JSON.stringify(this.items[idx].items[rIdx])).then(() => {
                    this.items[idx].items.splice(rIdx,1);
                });
            }else{
                this.items[idx].items.splice(rIdx,1);
            }
        },
        edited: function(item){
            item.edited = true;
        },
        editModeOn: function(item){
            item.inEditMode = true;
        },
        statusEdited: function(item){
            if(item.desc.length < 1){
                setTimeout(() => {
                    item.status = {val: this.statusOptions[0].val, label: this.statusOptions[0].label};
                },250)
            }
            this.edited(item);
        },
        fileInputChange: function (e,idx,rIdx) {
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            this.items[idx].items[rIdx].newFile = files[0];
            this.items[idx].items[rIdx].edited = true;
        },
        toggleCertificateChapters(craftIndex, certificateIndex) {
            let items = [].concat(this.items);
            items[craftIndex].items[certificateIndex].showChapters = !items[craftIndex].items[certificateIndex].showChapters;
            this.items = items;
        },
        openCreateCertificateModal(craft) {
            this.createCertificationModal.display = true;
            this.createCertificationModal.craft = craft;
        },
        closeCreateCertificateModal() {
            this.createCertificationModal.display = false;
            this.createCertificationModal.craft = null;
        },
        openUpdateCertificateModal(craft, certificate) {
            this.updateCertificationModal.display = true;
            this.updateCertificationModal.craft = craft;

            // certificate.chapters.forEach(chapter => {
            //     chapter.images = chapter.images.map(image => {
            //         return {
            //             id: image.id,
            //             // fileName: Q4U.timestamp() + '.' + image.ext,
            //             fileName: image.name,
            //             fileOriginalName: image.name,
            //             filePath: '',
            //             src: image.fullPath,
            //             ext: image.ext
            //         }
            //     });
            //     chapter.selectedChapter = this.globalChapters.find(item => {
            //         return +item.id === +chapter.chapterId
            //     })
            // })
            certificate.chaptersUpdated = false;
            this.certificateToUpdate = certificate;
        },
        closeUpdateCertificateModal() {
            this.updateCertificationModal.display = false;
            this.updateCertificationModal.craft = null;
            this.certificateToUpdate = null;
        },
        openDeleteCertificatePopup(certificate) {
            this.deleteCertificationPopupDisplay = true;
            this.certificateToDelete = certificate;
        },
        deleteCertificateAPI(certificate) {
            this.showLoader = true;
            let url = `/projects/certificate/${certificate.id}`;

            qfetch(url, {method: 'DELETE', headers: {}})
                .then(response => {
                    let deletedCertificateCraftId = certificate.id;

                    let items = [].concat(this.items)
                    items.forEach((craft, craftIndex) => {
                        craft.items.forEach((certificate, certIndex) => {
                            if(+certificate.id === +deletedCertificateCraftId) {
                                items[craftIndex].items.splice(certIndex, 1)
                            }
                        })
                    })
                    this.items = items;

                    this.deleteCertificationPopupDisplay = false
                    this.showLoader = false;
                })
        },
        getProjectChaptersAPI() {
            this.showLoader = true;
            let url = `/projects/${this.projectId}/chapters`;

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.globalChapters = response.items;
                    this.showLoader = false;
                })
        },
        getProjectCertificatesAPI() {
            this.showLoader = true;
            let url = `/projects/${this.projectId}/certificates`;

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    response.items = this.buildCertificateModel(response.items);

                    this.items = this.crafts.map(craft => {
                        craft.items = response.items.filter(certificate => {
                            return +certificate.craftId === +craft.id;
                        })
                        return craft;
                    });

                    this.showLoader = false;
                })
        },
        copyCertificatesAPI(requestForm) {
            const requestData = {
                certificatesIds: requestForm.items.map(certificate => certificate.id)
            }

            this.showLoader = true;
            let url = `/companies/${requestForm.companyId}/projects/${requestForm.projectId}/certificates/copy`;

            qfetch(url, {method: 'POST', headers: {}, body: requestData})
                .then(response => {
                    this.showLoader = false;
                })
        },
        createCertificateAPI(certificateForm) {
            this.showLoader = true;
            let url = `/projects/${this.projectId}/certificate`;

            let data = {
                name: certificateForm.name,
                sampleRequired: certificateForm.sampleRequired ? "1" : "0",
                craftId: this.createCertificationModal.craft.id,
                status: 'waiting'
            };


            data.chapters = certificateForm.chapters.map(chapter => {
                return {
                    chapterId: chapter.selectedChapter.id,
                    text: chapter.text,
                    images: chapter.images
                }
            });
            data.participants = certificateForm.participants;

            qfetch(url, {method: 'POST', headers: {}, body: data})
                .then(response => {
                    this.showLoader = false;
                    if(response.id) {
                        this.getProjectCertificateAPI(response.id);
                    }
                })
        },
        updateCertificateAPI(certificateForm) {
            this.showLoader = true;
            let url = `/projects/certificate/${certificateForm.id}`;

            qfetch(url, {method: 'PUT', headers: {}, body: certificateForm})
                .then(response => {
                    this.showLoader = false;
                    this.getProjectCertificateAPI(certificateForm.id)
                })
        },
        getProjectCertificateAPI(certificateId) {
            this.showLoader = true;
            let url = `/projects/certificate/${certificateId}`;

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    response.item = this.buildCertificateModel([response.item])[0];
                    let createdCertificateCraftId = response.item.craftId;
                    let craftIndex = this.items.findIndex(craft => {
                        return +craft.id === +createdCertificateCraftId;
                    })

                    let items = [].concat(this.items)
                    const certificateIndex = items[craftIndex].items.findIndex(item => +item.id === +certificateId)
                    if(certificateIndex !== -1) {
                        items[craftIndex].items[certificateIndex] = response.item;
                    } else {
                        items[craftIndex].items.push(response.item);
                    }
                    this.items = items;
                    this.showLoader = false;
                })
        },
        convertTimestampToDate(timestamp) {
            const date = new Date(+timestamp*1000);
            const month = ((date.getMonth()+1) > 9) ? (date.getMonth()+1) : "0"+(date.getMonth()+1);
            return date.getDate()+ '/' + month + '/' + date.getFullYear();
        },
        getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.ceil(max);
            return Math.floor(Math.random() * (max - min)) + min;
        },
        buildCertificateModel(certificates) {
            return certificates.map(certificate => {
                certificate.showChapters = false;
                certificate.sampleRequired = (certificate.sampleRequired === "1");
                certificate.participants = certificate.participants.map(participant => {
                    return {
                        id: participant.id,
                        uid: this.getRandomInt(100000, 9999999),
                        name: participant.name,
                        position: participant.position
                    }
                })
                certificate.chapters.forEach(chapter => {
                    chapter.uid = this.getRandomInt(100000, 9999999);
                    chapter.images = chapter.images.map(image => {
                        return {
                            id: image.id,
                            // fileName: Q4U.timestamp() + '.' + image.ext,
                            fileName: image.name,
                            fileOriginalName: image.name,
                            filePath: '',
                            src: image.fullPath,
                            ext: image.ext
                        }
                    })
                    chapter.selectedChapter = this.globalChapters.find(item => {
                        return +item.id === +chapter.chapterId
                    })
                    chapter.showContent = false;
                    chapter.showImages = false;
                })
                return certificate;
            })
        },
        showChapterContent(craftIndex, certificateIndex, chapterIndex) {
            this.hideChapterImagesAndContents()
            let items = [].concat(this.items);
            items[craftIndex].items[certificateIndex].chapters[chapterIndex].showContent = !items[craftIndex].items[certificateIndex].chapters[chapterIndex].showContent;
            this.items = items;
        },
        showChapterImages(craftIndex, certificateIndex, chapterIndex) {
            this.hideChapterImagesAndContents()
            let items = [].concat(this.items);
            items[craftIndex].items[certificateIndex].chapters[chapterIndex].showImages = !items[craftIndex].items[certificateIndex].chapters[chapterIndex].showImages;
            this.items = items;
        },
        hideChapterImagesAndContents() {
            let items = [].concat(this.items);
            items.map((craft) => {
                return craft.items.map(certificate => {
                    return certificate.chapters.map(chapter => {
                        chapter.showContent = false;
                        chapter.showImages = false;
                        return chapter;
                    })
                })
            })
            this.items = items;
        }
    },
    computed: {
        hasCheckedItem: function(){
            for(var i = 0; i < this.items.length; i++){
                if(this.items[i].checked === true){
                    return true;
                }
            }
            return false;
        }
    },
    mounted() {
        this.init();
        // window.addEventListener('load', () => {
        //     var parentWidth = $(".scrollable-table").width();
        //     console.log(parentWidth - 40)
        //     $(".scrollable-table").css({
        //         width:  parentWidth - 40 + "px"
        //     })
        // })

    }
});
