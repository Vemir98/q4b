Vue.component('transferable-items', {
    template: `
        <div class="row ">
            <div class="col-lg-12">
                <div class="panel-tools">
                    <div class="add-block">
                        <a class="add-block-text" @click="addRm"><img src="/media/img/add-vec.png" alt="add" class="add"><span class="">{{ addText }}</span></a>
                    </div>
                             
                    <div class="inline-options multiselect-parent rm" v-if="!inProjectMode()">
                        <label>{{copyText}}</label>
                        <div class="multiselect-row">
                            <div class="multiselect-col">
                              <multiselect :class="{disabled: needSelect() || selectedProjects.lenght < 1}" v-model="selectedCompany" :options="companies" @select="companySelected" :placeholder="selectCompanyTxt" label="name" track-by="id"  :searchable="false" :allow-empty="false" :show-labels="false"></multiselect>
                            </div>
                            <div class="multiselect-col multiselect-label">
                                <multiselect :class="{disabled: selectedCompany == null || needSelect() || selectedProjects.lenght < 1}"  v-model="selectedProjects" :close-on-select="false" :tag-placeholder="chooseProjectsTxt" :placeholder="chooseProjectsTxt" label="name" track-by="id" :options="projects" :multiple="true" :taggable="true" @tag="addTag"></multiselect>                                
                            </div>
                            <div class=" al-cent">
                                <button class="btn-copy" @click="copyToProject" :class="{disabled: selectedCompany == null || needSelect() || selectedProjects.lenght < 1}" >{{copyBtnTxt}}</button>
                            </div>                                                           
                        </div>
                    </div>
                </div>
                <hr>
                <div class="table-pannel-tools ">
                    <div class="input inp_ch">
                        <input type="checkbox" class="terms-checkbox" id="ch2"  v-model="selectAll" @change="toggleSelect" >
                        <label for="ch2"></label>
                    </div>
                    <a class="delete-checked" @click="deleteChecked"><span class="button" :class="{disabled: needSelect() || selectedProjects.lenght < 1}"><img src="/media/img/del-vec.png" alt="delete"></span></a>
                </div>
                <table class="acceptance">
                    <thead>
                    <tr>
                        <th class="td-100"></th>
                        <th>{{ thName2 }}</th>
                        <th>{{ thName3 }}</th>
                        <th class="td-200 select-unselect-col">
                         {{moreTxt}}
                        </th>
                    </tr>
                    </thead>
                    <tbody v-if="items && items.length > 0">
                    <tr v-for="item in items" :class="{selected: item.checked}">
                        <td align="center">
                            <div class="input inp_ch">
                                <input type="checkbox" class="terms-checkbox" id="item.id" :value="item.id"  v-model="item.checked"   :id="item.id">
                                <label :for='item.id'></label>
                            </div>
                        </td>

                        <td>
                            <span v-if="!item.inEditMode" @dblclick="editRm(item)">{{ item.text }}</span>
                            <input v-else v-model="item.text" type="text" ref="edit" class="input-editable">
                        </td>                        
                        <td>
                            <input v-model="item.quantity" type="number" @change="edited(item)" class="input-editable w-75">
                        </td>
                        <td>
                            <div class="more-box">
                                <div class="more-icon" @click="toggleDropdown($event)">
                                    <img src="/media/img/more-vec.png" alt="">
                                </div>
                                <div class="edit-box hide" >
                                    <span class="edit-btn" @click="editRm(item)">{{editTxt}}</span>
                                    <span class="delete-btn" @click="deleteRm(item)">{{deleteTxt}}</span>                             
                                </div>  
                            </div>
                        </td>
                        
                    </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <th colspan="4">
                                <span style=" display: block; text-align: center;color: #000">{{ noItemsText }}</span>
                            </th>
                        </tr>
                    </tbody>

                </table>
                <div class="button-save">
                    <a @click="saveAll" class="inline_block_btn orange_button" :class="{'button-disabled': !hasEditedOrNewElement()}">{{saveText}}</a>
                </div>
            </div>            
        </div>       
        
    `,
    props: {
        thName1: {required: true},
        thName2: {required: true},
        thName3: {required: true},
        selectAllTxt: {required: true},
        unselectAllTxt: {required: true},
        addText: {required: true},
        noItemsText: {required: true},
        saveText: {required: true},
        copyText: {required: true},
        projectsUrl: {required: true},
        companiesUrl: {required: true},
        deleteUrl: {required: true},
        copyUrl: {required: true},
        updateUrl: {required: true},
        listUrl: {required: true},
        copyBtnTxt: {required: true},
        selectCompanyTxt: {required: true},
        chooseProjectsTxt: {required: true},
        itemsListTxt: {required: true},
        moreTxt: {required: true},
        editTxt: {required: true},
        deleteTxt: {required: true},
        projectId : {},
        type: {required: true},
        translations: {required: true}
    },
    components: {
        Multiselect: window.VueMultiselect.default
    },
    data() {
        return {
            items: [],
            count: 0,
            selectedCompany: null,
            companies: [],
            selectedProjects: [],
            projects: [],
            checkedItems: [],
            showDropDown: false,
            selectAll: false,
            trans: JSON.parse(this.translations),
        };
    },
    created() {
        axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
    },
    methods: {
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
        addRm : function () {
            this.editModeOffForAll();
            var idx = this.items.findIndex(x => x.text.trim() === "" && x.isNew == true);
            if(idx <= 0){
                this.items.push({id : '_' + Math.random().toString(36).substr(2, 9), text : "", checked : false, inEditMode : true, isNew: true, edited: true, type: this.type});
                this.focusInput();
            }else{
                this.editRm(this.items[idx]);
            }
        },
        editRm : function (item) {

            var open = document.getElementsByClassName("edit-box");

            [].forEach.call(open, function(el) {
                el.classList.add("hide");
            });



            if(item.inEditMode){
                item.inEditMode = false;
            }else{
                this.editModeOffForAll();
                let idx = this.items.findIndex(x => x.id === item.id);
                this.items[idx].inEditMode = !this.items[idx].inEditMode;
                this.items[idx].edited = true;
                this.focusInput();
            }
        },
        edited : function (item) {
            item.edited = true;
        },
        deleteRm: function (item) {
            var open = document.querySelectorAll(".edit-box");
            [].forEach.call(open, function(el) {
                el.classList.add("hide");
            });
            let idx = this.items.findIndex(x => x.id === item.id);
            if(!item.isNew){
                var delItem = [];
                delItem.push(this.items[idx].id);
                axios.post(this.deleteUrl,JSON.stringify(delItem));
            }
            this.items.splice(idx,1);
        },
        saveAll: function () {
            var data = {project: this.projectId, items: []};
            for(var i = 0; i < this.items.length; i++){
                if(this.items[i].isNew || this.items[i].edited){
                    if(this.items[i].text.trim() == ""){
                        this.items.splice(i,1);
                    }else{
                        data.items.push(this.items[i]);
                    }

                }
            }

            if(data.items.length){
                axios.post(this.updateUrl,JSON.stringify(data)).then(response => {
                    var data = response.data;
                    if(data.length > 0){
                        for(var i = 0; i < data.length; i++){
                            let idx = this.items.findIndex(x => x.id == data[i].oldId);
                            this.items[idx]['inEditMode'] = this.items[idx]['isNew'] = this.items[idx]['edited'] = false;
                            this.items[idx].id = data[i].id;
                        }
                    }
                });

                for(var i = 0; i < this.items.length; i++){
                    this.items[i].inEditMode = false;
                    this.items[i].isNew = false;
                }
            }
        },
        editModeOffForAll : function(){
            for(var i = 0; i < this.items.length; i++){
                this.items[i].inEditMode = false;
            }
        },
        focusInput : function () {
            this.$nextTick(() => {
                this.$refs.edit[0].focus();
                this.$refs.edit[0].select();

            });
        },
        companySelected : function (item) {
            this.selectedProjects = [];
            axios.post(this.projectsUrl,JSON.stringify({'id' : item.id}))
                .then(response => {
                    this.projects = response.data.items;
                });
        },
        addTag : function () {

        },
        deleteChecked : function(){
            var dellItems = [];
            while (true){
                let idx = this.items.findIndex(x => x.checked == true);
                if(idx < 0) break;
                let item = this.items[idx];
                if( ! item.isNew){
                    dellItems.push(item.id);
                }
                this.items.splice(idx,1);
            }
            if(dellItems.length > 0){
                axios.post(this.deleteUrl,JSON.stringify(dellItems));
            }
        },
        toggleSelect : function(){
            let idx = this.items.findIndex(x => x.checked == true);

            if (!this.selectAll) {
                for(var i = 0; i < this.items.length; i++){
                    this.items[i].checked = false;
                }
            }else{
                for(var i = 0; i < this.items.length; i++){
                    this.items[i].checked = true;
                }
            }
        },
        needSelect : function () {
            return this.items.findIndex(x => x.checked == true) < 0;
        },
        hasEditedOrNewElement : function(){
            return this.items.findIndex(x => x.isNew == true) > -1 || this.items.findIndex(x => x.edited == true) > -1;
        },

        copyToProject : function () {
            var idList = [];
            for(var i = 0; i < this.items.length; i++){
                if(this.items[i].checked && !this.items[i].isNew){
                    idList.push(this.items[i].id);
                }
            }
            var company = this.selectedCompany.id;
            var projects = [];
            for(var i = 0; i < this.selectedProjects.length; i++){
                projects.push(this.selectedProjects[i].id);
            }
            for(var i = 0; i < this.items.length; i++){
                this.items[i].checked = false;
            }
            var checkboxses = document.getElementsByClassName("terms-checkbox");
            [].forEach.call(checkboxses, function(el) {
                el.checked = false;
            });
            this.selectedCompany = null;
            this.selectedProjects = [];


            axios.post(this.copyUrl,JSON.stringify({'ids' : idList, 'company': company, 'projects' : projects, 'type': 'ti'}))
                .then(response => {
                    // this.projects = response.data.items;
                });
        },
        inProjectMode : function () {
            return this.projectId != undefined && this.projectId > 0;
        }
    },
    mounted() {
        axios.get(this.listUrl)
            .then(response => {
                this.items = response.data.items;
                this.count = response.data.count;
            });

        axios.get(this.companiesUrl)
            .then(response => {
                this.companies = response.data.items;
            });
    }
});


