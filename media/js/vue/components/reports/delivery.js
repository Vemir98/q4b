Vue.component('report-delivery', {
    template: `
    <div>
        <div class="form" >
           
            <div class="row-flex">
                <div class="multiselect-col">
                    <label class="multiselect-label">{{companyTxt}}</label>
                    <multiselect v-model="selectedCompany" @select="companySelected" :option-height="104" :placeholder="selectCompanyTxt" :disabled="companies.length < 1" :options="companies" track-by="id" label="name" :searchable="true" :allow-empty="false" :show-labels="false">
                        <template slot="singleLabel" slot-scope="props">{{ props.option.name }}</template>
                        <template slot="option" slot-scope="props">
                        <span>{{ props.option.name }}</span>
                        </template>
                        <template slot="option-selected" slot-scope="props">
                        <span>{{ props.option.name }}234234</span>
                    </template>
                    </multiselect>                
                </div>
                <div class="multiselect-col">
                    <label class="multiselect-label">{{projectTxt}}</label>
                    <multiselect v-model="selectedProject" @select="projectSelected" :placeholder="selectProjectsTxt" :disabled="projects.length < 1" :options="projects" track-by="id" label="name" :searchable="true" :allow-empty="false" :show-labels="false">
                        <template slot="singleLabel" slot-scope="props">{{ props.option.name }}</template>
                        <template slot="option" slot-scope="props">
                        <span>{{ props.option.name }}</span>
                        </template>
                    </multiselect>
                </div>
                <div class="multiselect-col">
                    <label class="multiselect-label">{{dateRangeTxt}}</label>
                    <date-picker v-model="time" :lang="langs[currentLang]" :disabled="objects.length < 1" @change="timeChanged" type="date" :range="true" format="DD/MM/YYYY"></date-picker>
                </div>
            </div>
            <div class="row-flex">
                <div class="multiselect-col">
                    <div class="filter-item-label flex-between">
                    {{ structureTxt }}
                        <label class="table_label" :class="{'labtest-disabled': !objects.length}">
                            <span @click="toggleSelectAll('selectedObjects', 'objects')">
                                <template v-if="selectedObjects.length < objects.length">
                                       {{ trans.select_all }}
                                </template>
                                <template v-else>
                                       {{ trans.unselect_all }}
                                </template>
                            </span>
                        </label>
                    </div>
                    <multiselect 
                        v-model="selectedObjects"  
                        :placeholder="selectStructureTxt" 
                        :disabled="objects.length < 1" 
                        :options="objects" 
                        label="name" 
                        track-by="id"
                        :multiple="true" 
                        :hide-selected="false"
                        :close-on-select="false"
                        :clear-on-select="false"
                        :preserve-search="true"
                        :internal-search="true"
                        :taggable="false"
                        :show-labels="false"  
                        @change=""                                     
                        @select="onSelect($event, 'objects')"
                        @remove="onRemove($event, 'objects')"
                        >
                        <span class="multiselect-checkbox-label" :class="{'checked': scope.option.checked}"  slot="option" slot-scope="scope" >
                            <span class="multiselect-option-icon"><i class="q4bikon-tick"></i><span></span></span>
                            <span class="multiselect-option-name">{{ scope.option.name }}</span>
                        </span>
                        <template slot='selection' slot-scope="{values, option, isOpen}"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ getMultiselectSelectionValue(values, false) }} </span></template>
                        <template slot="tag">{{ '' }}</template>
                    </multiselect> 
                </div>
                <div class="multiselect-col">
                    <label class="multiselect-label">{{ trans.type }}</label>
                    <multiselect 
                        v-model="selectedTypes"  
                        :placeholder="trans.select_type" 
                        :disabled="false" 
                        :options="types" 
                        label="name" 
                        track-by="id"
                        :multiple="true" 
                        :hide-selected="false"
                        :close-on-select="false"
                        :clear-on-select="false"
                        :preserve-search="true"
                        :internal-search="true"
                        :taggable="false"
                        :show-labels="false"  
                        @change=""                                     
                        @select="onSelect($event, 'types')"
                        @remove="onRemove($event, 'types')"
                        >
                        <span class="multiselect-checkbox-label" :class="{'checked': scope.option.checked}"  slot="option" slot-scope="scope" >
                            <span class="multiselect-option-icon"><i class="q4bikon-tick"></i><span></span></span>
                            <span class="multiselect-option-name">{{ trans[scope.option.name] }}</span>
                        </span>
                        <template slot='selection' slot-scope="{values, option, isOpen}"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ getMultiselectSelectionValue(values, true) }} </span></template>
                        <template slot="tag">{{ '' }}</template>
                    </multiselect> 
                </div>         
                <div class="multiselect-col w-10">
                    <label class="multiselect-label">{{floorTxt}}</label>
                    <multiselect v-model="selectedFloor" @select="floorSelected" :placeholder="selectFloorTxt" :disabled="floors.length < 1" :options="floors" track-by="id" label="number" :searchable="true" :allow-empty="false" :show-labels="false">
                        <template slot="singleLabel" slot-scope="props">{{ props.option.number }}</template>
                    <template slot="option" slot-scope="props">
                        <span>{{ props.option.number }}</span>
                        </template>
                    </multiselect>
                </div>
                <div class="multiselect-col w-10">
                    <label class="multiselect-label">{{placeTxt}}</label>
                    <multiselect v-model="selectedPlace" :placeholder="selectPlaceTxt" :disabled="places.length < 1" :options="places" track-by="id" label="name" :searchable="true" :allow-empty="false" :show-labels="false">
                        <template slot="singleLabel" slot-scope="props">{{ props.option.name}}</template>
                    <template slot="option" slot-scope="props">
                        <span>{{ props.option.name }}</span>
                        </template>
                    </multiselect>
                </div>

                <div class=" al-cent">
                    <button class="btn-copy"  v-if="canShow()" @click="getReports">{{showTxt}}</button>
                </div>    
            </div>
        </div>
        <hr>
        <div class="delivery-protocols" v-if="items.length > 0">
            <p class="private">{{txtPrivateResult}}</p>
            <p class="public">{{txtPublicResult}}</p>
            <p class="total">{{txtTotalResult}}</p>
        </div>
        <div class="table-pannel-tools-delivery" v-if="items.length > 0">
            <div class="input inp_ch">
                <input type="checkbox" class="terms-checkbox" id="ch1"  @change="toggleSelect" :checked="checkedItemsCount() == items.length">
                <label for="ch1"></label>
            </div>
            <div :class="{disabled: needSelect()}" class="print-email">
                <button class="btn-img" @click="openEmailModal">
                    <img src="/media/img/new-images/mail.svg" alt="email">
                </button>
            </div>
        </div>
        <div class="table"  v-if="items.length > 0">
            <table class="">
                <thead>
                <tr>
                    <th></th>
                    <th>{{reportIdTxt}}</th>
                    <th>{{customerNameTxt}}</th>
                    <th>{{placeTxt}}</th>
                    <th>{{floorTxt}}</th>
                    <th>{{structureTxt}}</th>
                    <th>{{dateTxt}}</th>
                    <th>{{qualityMarkTxt}}</th>
                    <th>{{ trans.type }}</th>
                    <th>{{protocolTxt}}</th>
                    <th>{{moreTxt}}</th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="item in items">
                        <td>
                            <div class="input inp_ch">
                                <input type="checkbox" @change="itemChecked"  :id="item.id" :value="item.id" v-model="item.checked"/>
                                <label :for="item.id"></label>
                            </div>
                        </td>
                        <td>{{item.id}}</td>
                        <td>{{item.customer}}</td>
                        <td>{{item.place}}</td>
                        <td class="minus-he">{{item.floor}}</td>
                        <td>{{item.object}}</td>
                        <td>{{item.date}}</td>
                        <td v-if="item.edited || !item.qualityMark">
                            <div class="input inp_ch quelty_checkbox">
                                <input type="checkbox" class="" :id="item.id + 'qualty'"  v-model="item.qualityMark" @change="itemEdited(item)">
                                <label :for="item.id + 'qualty'"></label>
                            </div>
                        </td>
                        <td v-else>
                            <div class="img quality-td">
                                <img src="/media/img/new-images/quality.png" alt="Quality">
                            </div>
                        </td>
                        <td>{{ trans[item.type] }}</td>
                        <td>
                            <a v-if="item.canCreatePdf" :href="item.protocol" target="_blank">
                                <div class="img">
                                    <img src="/media/img/new-images/protocol.svg" alt="Protocol">
                                </div>
                            </a>
                            <a v-else>
<!--                                <div class="img" style="opacity: 0.2;cursor: auto">-->
<!--                                    <img src="/media/img/new-images/protocol.svg" alt="Protocol">-->
<!--                                </div>-->
                                {{ trans.protocol_not_ready }}
                            </a>
                        </td>
                        <td>
                            <div class="more-box delivery-more-box">
                                <div class="more-icon" @click="toggleDropdown($event)">
                                    <img src="/media/img/more-vec.png" alt="">
                                </div>
                                <div class="edit-box hide">
                                    <a :href="item.print" target="_blank"><span>{{printTxt}}</span></a>
                                    
                                    <a :href="getQCReportUrl(item.qcReport)" target="_blank"><span>{{qcReportTxt}}</span></a>                    
                                </div>  
                            </div>
                        </td>
                    </tr>    
                </tbody>
            </table>
            <div class="button-save"  v-if="canSave()">
                 <a  class="inline_block_btn orange_button" @click="save()">{{saveTxt}}</a>
            </div>
        </div>
        <div v-else-if="requested && items.length < 1">
            <p class="nothing-found">{{ noItemsText }}</p>
        </div>
    </div>
    `,
    props: {
        siteUrl: {required: true},
        companyTxt: {required: true},
        projectTxt: {required: true},
        dateRangeTxt: {required: true},
        structureTxt: {required: true},
        floorTxt: {required: true},
        placeTxt: {required: true},
        showTxt: {required: true},
        reportIdTxt: {required: true},
        customerNameTxt: {required: true},
        dateTxt: {required: true},
        qualityMarkTxt: {required: true},
        protocolTxt: {required: true},
        qcReportTxt: {required: true},
        selectCompanyTxt: {required: true},
        selectProjectsTxt: {required: true},
        selectStructureTxt: {required: true},
        selectFloorTxt: {required: true},
        selectPlaceTxt: {required: true},
        printTxt: {required: true},
        saveTxt: {required: true},
        noItemsText: {required: true},
        moreTxt: {required: true},
        translations: {required:true},
        deliveryReportTypes: {required: true}
    },
    components: {
        Multiselect: window.VueMultiselect.default,
        DatePicker: window.DatePicker
    },
    data() {
        return {
            items: [],
            count: 0,
            requested: false,
            time: [],
            selectedCompany: null,
            selectedProject: null,
            selectedObjects: [],
            selectedFloor: null,
            selectedPlace: null,
            selectedTypes: [],
            companies: [],
            projects: [],
            objects: [],
            floors: [],
            places: [],
            types: [],
            trans: JSON.parse(this.translations),
            txtPrivateResult : "",
            txtPublicResult : "",
            txtTotalResult : "",
            showDropDown: false,
            langs: {
                ru: {
                    formatLocale: {
                        months: ['????????????', '??????????????', '????????', '????????????', '??????', '????????', '????????', '????????????', '????????????????', '??????????????', '????????????', '??????????????'],
                        monthsShort: ['??????', '??????', '??????', '??????', '??????', '??????', '??????', '??????', '??????', '??????', '??????', '??????'],
                        weekdays: ['??????????????????????', '??????????????????????', '??????????????', '??????????', '??????????????', '??????????????', '??????????????'],
                        weekdaysShort: ['??o??', '??????', '??????', '??????', '??????', '??????', '??????'],
                        weekdaysMin: ['????', '????', '????', '????', '????', '????', '????'],
                        firstDayOfWeek: 0,
                        firstWeekContainsDate: 1,
                    }
                },
                he: {
                    formatLocale: {
                        months: ['??????????', '????????????', '??????', '??????????', '??????', '????????', '????????', '????????????', '????????????', '??????????????', '????????????', '??????????'],
                        monthsShort: ['??????', '??????', '??????', '??????', '??????', '????????', '????????', '??????', '??????', '??????', '??????', '??????'],
                        weekdays: ['??????????', '??????', '??????????', '??????????', '??????????', '????????', '??????'],
                        weekdaysShort: ['??\'', '??\'', '??\'', '??\'', '??\'', '??\'', '??????'],
                        weekdaysMin: ['??\'', '??\'', '??\'', '??\'', '??\'', '??\'', '??????'],
                        firstDayOfWeek: 0,
                        firstWeekContainsDate: 1,
                    }
                }
            }

        }
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        }
    },
    created() {
        axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
        var date = new Date();
        date.setDate(1);
        date.setMonth(date.getMonth()-2);
        this.time = [date, new Date()];
    },
    watch: {
      selectedObjects(objects) {
          if(objects.length !== 1) {
              this.floors = [];
              this.places = [];
              this.selectedFloor = null;
              this.selectedPlace = null;
          } else {
              this.objectSelected(objects[0]);
          }
      }
    },
    methods: {
        getQCReportUrl(qcUrl) {
            const from = this.time[0].toLocaleDateString("en-GB");
            const to = this.time[1].toLocaleDateString("en-GB");
            if (!from || !to) {
                return `${qcUrl}`
            }
            return `${qcUrl}?from=${from}&to=${to}`

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
        toggleSelect : function(){

            if(this.checkedItemsCount() < this.items.length){
                for(var i = 0; i < this.items.length; i++){
                    this.items[i].checked = true;
                }
            }else{
                for(var i = 0; i < this.items.length; i++){
                    this.items[i].checked = false;
                }
            }
            this.itemChecked();
        },
        checkedItemsCount : function () {
            var cnt = 0;
            for(var i = 0; i < this.items.length; i++){
                if(this.items[i].checked == true) cnt++;
            }
            return cnt;
        },
        needSelect : function () {
            return this.items.findIndex(x => x.checked == true) < 0;
        },
        companySelected(option){
            this.selectedProject = this.selectedFloor = this.selectedPlace = null;
            this.selectedObjects = [];
            this.projects = this.objects = this.floors = this.places = [];
            axios.get(this.siteUrl + 'entities/projects/' + option.id + '?fields=id,name')
                .then(response => {
                    this.projects = response.data.items;
                });

        },
        projectSelected(option){
            this.selectedFloor = this.selectedPlace = null;
            this.selectedObjects = [];
            this.objects = this.floors = this.places = [];
            window.eventBus.$emit('deliveryProjectSelected', option.id);
            axios.get(this.siteUrl + 'entities/objects/' + option.id + '?fields=id,name')
                .then(response => {
                    this.objects = response.data.items;
                });

        },
        objectSelected(option){
            this.selectedFloor = this.selectedPlace = null;
            this.floors = this.places = [];
            axios.get(this.siteUrl + 'entities/floors/' + option.id + '?fields=id,number')
                .then(response => {
                    this.floors = response.data.items;
                });
        },
        floorSelected(option) {
            this.selectedPlace = null;
            this.places = [];
            axios.get(this.siteUrl + 'entities/places/' + option.id + '?fields=id,number')
                .then(response => {
                    this.places = response.data.items;
                });
        },
        timeChanged(){
        },
        canShow(){
            return this.selectedCompany != null && this.selectedProject != null && this.selectedObjects.length && this.time[0] != null;
        },
        getReports(){
            var data ={
                'company_id' : this.selectedCompany.id,
                'project_id': this.selectedProject.id,
                'object_ids' : this.selectedObjects.map(object => +object.id),
                'from': this.time[0].toLocaleDateString("en-GB"),
                'to': this.time[1].toLocaleDateString("en-GB")
            };

            if(this.selectedFloor != null){
                data['floor_id'] = this.selectedFloor.id;
            }

            if(this.selectedPlace != null){
                data['place_id'] = this.selectedPlace.id;
            }

            if(this.selectedTypes.length){
                data['types'] = this.selectedTypes.map(type => type.name);
            }
            this.requested = false;
            this.items = [];
            axios.post(this.siteUrl + 'reports/delivery/show',JSON.stringify(data))
                .then(response => {

                    for(var i=0; i < response.data.items.length; i++){
                        response.data.items[i].checked = false;
                    }

                    this.items = response.data.items;
                    this.requested = true;
                    this.txtTotalResult = response.data.txtTotalResult;
                    this.txtPublicResult = response.data.txtPublicResult;
                    this.txtPrivateResult = response.data.txtPrivateResult;

                });
        },
        itemEdited(item){
            item.edited = item.qualityMark;
        },
        canSave(){
            for(var key in this.items){
                if(this.items[key].edited){
                    return true;
                }
            }
        },
        save(){
            var data = { items : []};
            for(var i = 0; i < this.items.length; i++){
                if(this.items[i].edited){
                    data.items.push(this.items[i].id);
                    this.items[i].edited = false;
                    this.items[i].qualityMarker = true;
                }
            }

            axios.post(this.siteUrl + 'reports/delivery/save',JSON.stringify(data))
                .then(response => {
                    //var items = response.data.items;
                });
        },
        openEmailModal: function () {
            if( ! this.needSelect()){
                window.eventBus.$emit('showModal', "deliveryEmailModal");
            }
        },
        itemChecked: function () {
            var checked = [];
            this.items.filter(x => x.checked === true).forEach(item => checked.push(item.id));
            window.eventBus.$emit('deliveryReportChecked', checked);
        },
        onSelect(option, objName) {
            let index = this[objName].findIndex(item => +item.id === +option.id);
            this[objName][index].checked = true;
        },
        onRemove(option, objName) {
            let index = this[objName].findIndex(item => +item.id === +option.id);
            this[objName][index].checked = false;
        },
        getMultiselectSelectionValue(values, trans) {
            let vals = [];
            values.forEach(val => {
                vals.push(!trans ? val.name : this.trans[val.name])
            });
            return vals.join(', ');
        },
        toggleSelectAll(selected, list) {
            if (list.length) {
                if (this[selected].length < this[list].length) {
                    this[selected] = this[list].map((i) => {
                        i.checked = true;
                        return i
                    });
                } else {
                    this[selected] = this[list].map((i) => {
                        i.checked = false;
                        return i
                    });
                    this[selected] = []
                }
            }
        },
        getCompanies(){
            let url = '/companies/entities/for_current_user';

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.companies = response.items ? response.items : [];
                    console.log('COMPANIES', this.companies)
                })
        },
    },
    mounted() {
        this.getCompanies();
        this.types = Object.values(JSON.parse(this.deliveryReportTypes)).map((type, typeIndex) => {
            return {
                id: typeIndex+1,
                name: type
            }
        })
        console.log('TYPES', this.types)
    }
});

