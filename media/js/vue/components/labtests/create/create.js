Vue.component('pr-labtests-create', {
    template: `
        <div id="labtests-create">
            <div v-if="showLoader" class="loader_backdrop_vue">
                <div class="loader"></div>
            </div>
            <div class="page-title-sec flex-start">
                <div class="page-title"> {{ trans.create_lab_control }} </div>
            </div>
            <div class="filters-section">
                <div class="filters-wraper flex-start">
                    <div class="filter-item">
                        <div class="multiselect-col">
                            <div class="filter-item-label" >{{ trans.structure }}<span>&nbsp;*</span></div>
                            <multiselect 
                                v-model="selectedStructure"
                                :option-height="104" 
                                :placeholder="trans.select_structure" 
                                :disabled="structures.length < 1" 
                                :options="structures" 
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
                    </div>
                    <div class="filter-item">
                        <div class="multiselect-col">
                            <div class="filter-item-label" >{{ trans.floor }}<span>&nbsp;*</span></div>
                            <multiselect 
                                v-model="selectedFloor"
                                :option-height="104" 
                                :placeholder="trans.select_floor" 
                                :disabled="floors.length < 1" 
                                :options="floors" 
                                track-by="id" 
                                label="name" 
                                :searchable="true" 
                                :allow-empty="false"
                                :show-labels="false"
                            >
                                <template slot="singleLabel" slot-scope="props">{{ props.option.custom_name ? props.option.custom_name : props.option.number }}</template>
                                <template slot="option" slot-scope="props">
                                    <span>{{ props.option.custom_name ? props.option.custom_name : props.option.number }}</span>
                                </template>
                                <template slot="option-selected" slot-scope="props">
                                    <span>{{ props.option.custom_name ? props.option.custom_name : props.option.number }}</span>
                                </template>
                            </multiselect>
                        </div>
                    </div>
                    <div class="filter-item">
                        <div class="multiselect-col">
                            <div class="filter-item-label" >{{ trans.place }}</div>
                            <multiselect 
                                v-model="selectedPlace"
                                :option-height="104" 
                                :placeholder="trans.select_place" 
                                :disabled="places.length < 1" 
                                :options="places" 
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
                    </div>
                    <div class="filter-item">
                        <div class="multiselect-col">
                            <div class="filter-item-label" >{{ trans.element }}<span>&nbsp;*</span></div>
                            <multiselect 
                                v-model="selectedElement"
                                :option-height="104" 
                                :placeholder="trans.select_element" 
                                :disabled="elements.length < 1" 
                                :options="elements" 
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
                    </div>
                    <div class="filter-item">
                        <div class="multiselect-col">
                            <div class="filter-item-label" >{{ trans.craft }}<span>&nbsp;*</span></div>
                            <multiselect 
                                v-model="selectedCraft"
                                :option-height="104" 
                                :placeholder="trans.select_craft" 
                                :disabled="elementCrafts.length < 1" 
                                :options="elementCrafts" 
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
                    </div>
                    <div class="filter-item">
                         <div class="ltest-input">
                            <div class="input_item_label ltest-input-label">{{ trans.sample_number }}<span>&nbsp;*</span></div>
                            <input type="number" v-model="sampleNumber" autocomplete="off" :placeholder="trans.enter_certificate_number" class="">
                         </div>
                    </div>
                    <div class="filter-item">
                         <div class="ltest-input">
                            <div class="input_item_label ltest-input-label">{{ trans.essence_of_work }}<span>&nbsp;*</span></div>
                            <input type="text" v-model="essenceOfWork" autocomplete="off" :placeholder="trans.enter_essence_of_work" class="">
                         </div>
                    </div>
                    <div class="filter-item">
                        <div class="multiselect-col">
                            <div class="filter-item-label" >{{ trans.strength_after }}<span>&nbsp;*</span></div>
                            <multiselect 
                                    v-model="strengthAfterOption" 
                                    :option-height="104" 
                                    :placeholder="trans.strength_after" 
                                    :disabled="!strengthAfterOptions.length" 
                                    :options="strengthAfterOptions" 
                                    label="name" 
                                    :searchable="true" 
                                    :allow-empty="false" 
                                    :show-labels="false"
                                >
                                    <template slot="singleLabel" slot-scope="props">{{ props.option }}</template>
                                    <template slot="option" slot-scope="props">
                                        <span>{{ props.option }}</span>
                                    </template>
                                </multiselect> 
                        </div>
                    </div>
                    <div class="filter-item datepicker">
                            <div class="filter-item-label" >{{ trans.create_date }}<span>&nbsp;*</span></div>
                            <date-picker 
                                v-model="time"
                                :lang="langs[currentLang]"
                                :editable="false" 
                                :clearable="false"
                                :disabled="false" 
                                @change="timeChanged" 
                                type="date"
                                format="DD/MM/YYYY"
                            >
                            </date-picker>
                        </div>
                </div>
                <div class="filters-wraper flex-start" v-if="params.length">
                    <div class="filter-item" v-for="param in params">
                         <div class="ltest-input">
                            <div class="input_item_label ltest-input-label">{{ param.clpName }}</div>
                            <input type="number" v-model="param.defaultValue" :placeholder="(trans.enter_the + ' ' + param.clpName)" autocomplete="off" class="">
                         </div>
                    </div>
                </div>
                <div class="filters-wraper certificates-section">
                    <delivery-certificates-section
                        :projectId="projectId"
                        :translations="translations"
                        @deliveryCertificatesUpdated="deliveryCertificates = $event"
                    />
                    <plans-section
                        :projectId="projectId"
                        :translations="translations"
                        :plans="plans"
                        :siteUrl="siteUrl"
                        @openEditor="planImageToEditor"
                        @showLoader="showLoader = true"
                        @hideLoader="showLoader = false"
                    />
                </div>
                <certificates-section
                    :projectId="projectId"
                    :translations="translations"
                    @currentCertificateChanged="currentCertificate = $event"
                />
                <div class="filters-wraper files-section">
                    <div class="labtest_attachment">
                        <div class="ltest-input-label">{{ trans.attached_files }}</div>
                        <div class="attach_file" @click="handleAttachFileClick()"></div>
                        <input type="file" id="fileInput" @change="fileChange($event)" style="opacity: 0" />
                        <div class="ltest_attachment_wraper">
                            <div v-if="images.length" class="ltest_attachment_title">{{ trans.list_of_files }}</div>
                            <template v-for="(image, index) in images">
                                <div class="ltest_attachment_item" v-if="!image.planId" :key="index">
                                    <div v-if="fileIsImage(image.fileName)" class="ltest_attachment_icon"><i class="icon q4bikon-file" @click="openModal(index)"></i></div>
                                    <div v-else class="ltest_attachment_icon">
                                        <a v-if="image.filePath.length > 1" :href="imagePath(image.filePath,image.fileName)" target="_blank"><i class="icon q4bikon-file"></i></a>
                                        <i v-else class="icon q4bikon-file" style="cursor: default"></i>
                                    </div>
                                    <div class="ltest_attachment_right">
                                        <div class="ltest_attachment_name">{{ image.fileName }}</div>
                                        <div class="ltest_attachment_line"></div>
                                        <div class="ltest_attachment_Uploaded">{{ trans.uploaded }}</div>
                                        <button class="remove_image" @click.stop.prevent="deleteImage(index)"></button>
                                    </div>
                                </div>    
                            </template>                                                  
                        </div>
                    </div>
                </div>
                <div class="filter-buttons">
                    <button 
                        class="filter-button generate"
                        :class="{'labtest-disabled': !canCreateLabControl}"
                        @click="createLabControl"
                    >
                    {{ trans.create }}
                    </button>
                </div>
            </div>
            <div id="image-editor" :class="{'hidden': !showEditor}">
                <div class="" id="pixel-perfect-editor"></div>
            </div>
        </div>`,
    props: {
        siteUrl: {required: true},
        projectId: {required: false},
        translations: {required: true},
        statuses: {required: true}
    },
    components: {
        Multiselect: window.VueMultiselect.default,
    },
    data() {
        return {
            trans: JSON.parse(this.translations),
            taskStatuses: JSON.parse(this.statuses),
            showLoader: true,
            // ltStatuses:  this.getStatuses(JSON.parse(this.statuses)),
            structures: [],
            floors: [],
            places: [],
            elements: [],
            crafts: [],
            tasks: [],
            craftsWithParams: [],
            params: [],
            projectPlans: [],
            plans: [],

            strengthAfterOptions: [0, 3, 7, 14, 21, 28, 60, 90],
            selectedProject: null,
            selectedStructure: null,
            selectedFloor: null,
            selectedPlace: null,
            selectedElement: null,
            selectedCraft: null,
            strengthAfterOption: 0,
            sampleNumber: '',
            essenceOfWork: '',
            currentCertificate: null,
            deliveryCertificates: [],
            images: [],
            time: null,
            editor: null,
            showEditor: false,
            editorOpenedFromPlans: false,
            langs: {
                ru: {
                    formatLocale: {
                        months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                        monthsShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
                        weekdays: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
                        weekdaysShort: ['Вoс', 'Пон', 'Вто', 'Сре', 'Чет', 'Пят', 'Суб'],
                        weekdaysMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                        firstDayOfWeek: 0,
                        firstWeekContainsDate: 1,
                    }
                },
                he: {
                    formatLocale: {
                        months: ['ינואר', 'פברואר', 'מרץ', 'אפריל', 'מאי', 'יוני', 'יולי', 'אוגוסט', 'ספטמבר', 'אוקטובר', 'נובמבר', 'דצמבר'],
                        monthsShort: ['ינו', 'פבר', 'מרץ', 'אפר', 'מאי', 'יוני', 'יולי', 'אוג', 'ספט', 'אוק', 'נוב', 'דצמ'],
                        weekdays: ['ראשון', 'שני', 'שלישי', 'רביעי', 'חמישי', 'שישי', 'שבת'],
                        weekdaysShort: ['א\'', 'ב\'', 'ג\'', 'ד\'', 'ה\'', 'ו\'', 'שבת'],
                        weekdaysMin: ['א\'', 'ב\'', 'ג\'', 'ד\'', 'ה\'', 'ו\'', 'שבת'],
                        firstDayOfWeek: 0,
                        firstWeekContainsDate: 1,
                    }
                }
            },
        }
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
        floorsDisabled() {
            return this.selectedStructure.length > 1
        },
        placesDisabled() {
            return (this.selectedStructure.length > 1) || (this.selectedFloor.length > 1)
        },
        canCreateLabControl() {
            return (
                this.selectedProject &&
                this.selectedStructure &&
                this.selectedFloor &&
                this.selectedElement &&
                this.selectedCraft &&
                (this.sampleNumber.length > 0) &&
                (this.currentCertificate?.description.length > 0) &&
                (this.essenceOfWork.length > 0) &&
                this.strengthAfterOption &&
                this.time
            )
        },
        elementCrafts() {
            if(this.selectedElement) {

                let elementCrafts = this.crafts.filter(craft => {
                    return this.selectedElement.crafts.includes(craft.id)
                })

                return elementCrafts.filter(elementCraft => {
                    return (this.tasks.filter(task => {
                        return (task.crafts.filter(taskCraft => {
                            return +taskCraft.id === +elementCraft.id
                        }).length > 0)
                    }).length > 0)
                })
            } else {
                return []
            }
        }
    },
    created() {
        this.getProjectAPI(this.projectId);

        this.setDatePickerDefaultTime();
        this.getStructuresAPI();
        this.getElementsAPI();
        this.getTasksAPI();
    },
    mounted() {
        this.$validator.localize('msg');

        var config = {
            onLoad: (editor) => {
                editor.markup.appendHtml('<button id="close" class="editor-top-buttons editor-close-button">' + this.trans.close + '</button>','TOPBAR_RIGHT',() => {
                    document.querySelector('#close').addEventListener('click',(e) => {
                        this.closeModal(true)
                    })
                })
                editor.markup.appendHtml('<button id="save" class="editor-top-buttons editor-save-button">' + this.trans.save + '</button>','TOPBAR_RIGHT',() => {
                    document.querySelector('#save').addEventListener('click',(e) => {
                        this.saveImage()
                    })
                })
            }
        };
        Object.assign(config, window.EDITOR_CONFIG )
        this.editor = new PixelPerfect('#pixel-perfect-editor',config);

    },
    watch: {
        selectedStructure(structure) {
            if (structure) {
                this.selectedFloor = null;
                this.selectedPlace = null;
                this.floors = [];
                this.places = [];
                this.getStructureFloorsAPI();
            }
        },
        selectedFloor(floor) {
            if (floor) {
                this.selectedPlace = null;
                this.places = [];
                this.getFloorPlacesAPI();
                if(this.selectedCraft) {
                    this.getMatchedPlans(this.selectedCraft)
                }
            }
        },
        selectedPlace(place) {
            if(this.selectedCraft) {
                this.getMatchedPlans(this.selectedCraft)
            }
        },
        selectedElement(element) {
            this.selectedCraft = null;
            this.params = [];
        },
        selectedCraft(craft) {
            this.images = [];
            if(craft) {
                let matchedCraft = this.craftsWithParams.filter(craftWithParams => {
                    return (craftWithParams.craftName === craft.name)
                })

                if(matchedCraft.length > 0) {
                    this.params = matchedCraft[0].params.map(param => {
                        param.clId = matchedCraft[0].id
                        return param;
                    })
                } else {
                    this.params = [];
                }

                this.getMatchedPlans(craft)
            }
        }
    },
    methods: {
        getProjectAPI(id) {
            this.showLoader = true;
            let url = `/projects/${id}/entities/project?fields=id,name,company_id`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.selectedProject = response.item;
                    this.showLoader = false;
                    if(this.selectedProject) {
                        this.getCompanyCraftsAPI(this.selectedProject?.company_id);
                        this.getCraftsWithParamsAPI();
                        this.getProjectPlansAPI();
                    }
                })
        },
        getTasksAPI() {
            this.showLoader = true;
            let url = `/projects/${this.projectId}/tasks/list`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.tasks = response.items.filter(task => {
                        return task.status === this.taskStatuses.Enabled
                    })
                })
        },
        getStructuresAPI() {
            this.showLoader = true;
            let url = `/projects/${this.projectId}/entities/objects?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.structures = response.items;
                    this.showLoader = false;
                })
        },
        getElementsAPI(){
            this.showLoader = true;
            let url = `/projects/${this.projectId}/labtests/elements_with_crafts`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.elements = response.items;
                    this.showLoader = false;
                })
        },
        getStructureFloorsAPI() {
            this.showLoader = true;
            let url = `/projects/entities/objects/${this.selectedStructure.id}/floors?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.floors = response.items;
                    this.showLoader = false;
                })
        },
        getFloorPlacesAPI() {
            this.showLoader = true;
            let url = `/projects/entities/floors/${this.selectedFloor.id}/places?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.places = response.items;
                    this.showLoader = false;
                })
        },
        getCompanyCraftsAPI(companyId) {
            this.showLoader = true;
            let fields="id,name,companyId,catalogNumber,status,relatedId";
            let url = `/companies/${companyId}/entities/crafts?fields=${fields}`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.crafts = response.items.map(craft => {
                        craft.name = craft.name.trim();
                        return craft;
                    });
                    this.showLoader = false;
                })
        },
        getCraftsWithParamsAPI() {
            this.showLoader = true;
            let url = `/projects/labtests/crafts_params`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.craftsWithParams = response;
                    this.showLoader = false;
                })
        },
        getProjectPlansAPI() {
            this.showLoader = true;
            let url = `/projects/entities/plans/${this.projectId}`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.projectPlans = response.items;
                    this.showLoader = false;
                })
        },
        setDatePickerDefaultTime() {
            // let date = new Date();
            // let end = new Date();
            // date.setDate(1);
            // date.setMonth(date.getMonth()-6);
            // end.setDate(end.getDate() + 1);
            this.time = new Date();
        },
        handleAttachFileClick() {
            document.getElementById('fileInput').click()
        },
        fileChange(event){
            let image = event.target.files[0];
            if(image){
                let ext = image.type.split('/')[1];
                if(!['jpe','jpeg','jpg','png','tif','tiff','pdf'].includes(ext)) {
                    ext = image.name.split('.').pop().toLowerCase()
                }
                let fileReader = new FileReader();
                fileReader.onload = (e) => {
                    this.images.unshift({
                        id: null,
                        fileName: Q4U.timestamp() + '.' + ext,
                        fileOriginalName: image.name,
                        filePath: '',
                        src: e.target.result,
                        ext: ext === 'jpg' ? 'jpeg' : ext
                    });
                }
                fileReader.readAsDataURL(image);
            }
        },
        openModal(index){
            this.openedImageIndex = index;
            setTimeout( () => {
                this.editor.resetCanvas(true);
                this.editor.setActiveTool('select')
                this.editor.loadBackgroundImageFromUrl(this.images[index].src , (error) => {
                    if(error){
                        this.closeModal();
                    }
                })
                this.showEditor = true;
                document.getElementsByTagName('body')[0].classList.add("hide_body_scroll");
            }, 1000)
        },
        planImageToEditor(image) {
            this.images.unshift(image)
            this.editorOpenedFromPlans = true;
            this.openModal(0)
        },
        fileIsImage(file){
            let ext = file.split('.').pop().toLowerCase()
            return ['jpe','jpeg','jpg','png','tif','tiff'].includes(ext)
        },
        deleteImage(index){
            if(this.images[index]){
                if(this.images[index].id){
                    this.imagesOld.push(this.images[index].id)
                }
                this.images.splice(index, 1);
            }
        },
        closeModal(canceled){
            this.editor.tool('crop').cancel();
            document.getElementsByTagName('body')[0].classList.remove("hide_body_scroll");
            if (this.images[this.openedImageIndex].planId) {
                this.images.splice(this.openedImageIndex, 1);
            }
            if (this.editorOpenedFromPlans && canceled) {
                this.images.splice(this.openedImageIndex, 1);
            }

            this.editorOpenedFromPlans = false;
            this.openedImageIndex = null;
            this.showEditor = false;
        },
        saveImage(){

            this.editor.tool('crop').cancel();
            let openedIndex = this.openedImageIndex;
            let image = {...this.images[openedIndex]};
            let imgs = this.images;
            let ext = image.ext === 'pdf' ? 'jpeg': image.ext
            image.src = this.editor.getCanvasDataAs(ext);
            if(image.id){
                this.imagesOld.push(image.id)
                image.id = null;
            }

            if(image.planId){
                delete image.planId;
            }
            imgs[openedIndex] = {...image};
            this.images = JSON.parse(JSON.stringify(imgs));
            this.closeModal();
        },
        timeChanged() {},
        getMatchedPlans(craft) {
            let specialityPlans = this.projectPlans.filter(plan => {
                return (plan.crafts.filter(planCraft => {
                    return (+planCraft.id === +craft.id)
                }).length > 0)
            })

            this.plans = specialityPlans.filter(specialityPlan => {
                let hasSameFloor = specialityPlan.floors.filter(planFloor => {
                    return (+planFloor.id === +this.selectedFloor?.id)
                })

                return ((hasSameFloor.length > 0) || ((+specialityPlan.placeId === +this.selectedPlace?.id) && (+this.selectedPlace?.id !== 0)))
            }, this).filter(plan => {
                return (plan.fileOriginalName.endsWith('.jpg') || plan.fileOriginalName.endsWith('.png') || plan.fileOriginalName.endsWith('.jpeg'))
            })
        },
        createLabControl() {
            let formData = {
                floorId: this.selectedFloor.id,
                placeId: this.selectedPlace?.id ? this.selectedPlace.id : null,
                craftId: this.selectedCraft.id,
                elementId: this.selectedElement.id,
                objectId: this.selectedStructure.id,
                planId: null,
                certNumber: this.sampleNumber,
                standard: this.essenceOfWork,
                strengthAfter: this.strengthAfterOption,
                deliveryCert: this.deliveryCertificates.map(delCert => delCert.text + '@#$'),
                createDate: this.time.toLocaleDateString("en-GB") + ' ' + this.time.getHours() + ':' + String(this.time.getMinutes()).padStart(2, "0"),
                slp: this.params.map(param => {
                    return {
                        clId: param.clId,
                        clpId: param.clpId,
                        value: param.defaultValue
                    }
                })
            }

            this.showLoader = true;
            let url = `/projects/${this.projectId}/labtests`;
            qfetch(url, {method: 'POST', headers: {}, body: formData})
                .then(response => {
                    this.showLoader = false;
                    this.createLabControlTicket(response.labtestId)
                })

        },
        createLabControlTicket(labtestId) {
            let formData = {
                number: this.currentCertificate?.labCertificate ? this.currentCertificate.labCertificate : null,
                freshStrength: this.currentCertificate?.freshConcreteStrength ? this.currentCertificate.freshConcreteStrength : null,
                rollStrength: this.currentCertificate?.rollStrength ? this.currentCertificate.rollStrength : null,
                description: this.currentCertificate?.description ? this.currentCertificate.description : null,
                notes: this.currentCertificate?.notes ? this.currentCertificate.notes : null,
                images: this.images
            }
            this.showLoader = true;

            let url = `/projects/${this.projectId}/labtests/${labtestId}/tickets`;
            qfetch(url, {method: 'POST', headers: {}, body: formData})
                .then(response => {
                    this.showLoader = false;
                    window.location.href = this.siteUrl+'/labtests/project/'+this.projectId
                })
        }
    },
});

