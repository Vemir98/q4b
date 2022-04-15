Vue.component('certifications-old', {
    template: `
        <div class="universal-certification">
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
                                <div v-if="(projectsUrl && selectedProject != null)"><input type="checkbox" id="include-files"  v-model="includeFiles"> <label for="include-files" class="craft-label include-files-checkbox">{{includeFilesTxt}}</label></div>
                            </div>                                                          
                        </div>
                    </div>
            </div>
            <div class="top-bar selectAll-craft">
                <input type="checkbox" id="is-all" @change="toggleSelection" v-model="selectAll"> <label for="is-all" class="craft-label">{{selectAllTxt}}</label>
                <!--<a href="#" @click.prevent="deleteSelected" v-if="hasCheckedItem" style="float: right;">Delete ICON</a>-->
            </div>
            <div class="craft-cert" v-for="craft,idx in items">
                <input type="checkbox" :id="'craftlbl' + craft.id" v-model="craft.checked" @change="craftCheckChanged">
                <label :for="'craftlbl' + craft.id" class="craft-label">{{craft.name}}</label><br>
                <button @click="addNewItem(idx)" class="btn btn-add craft-btn-add">
                    <img src="/media/img/new-images/btn-add.svg" alt="add">
                </button>
                <div class="table craft-table">
                    <table v-if="craft.items.length > 0" id="cert-table">
                        <thead>
                            <th>
                                {{descTxt}}
                            </th>
                            <th>
                                {{fileTxt}}
                            </th>
                            <th>
                                {{uploadDateTxt}}
                            </th>
                            <th>
                                {{statusTxt}}
                            </th>
                            <th>
                                {{moreTxt}}
                            </th>
                        </thead>
                        <tbody>
                            <tr v-for="item, rIdx in craft.items">
                                <td>
                                <span v-if="!item.inEditMode || item.status.val == 'approved'" @dblclick="editModeOn(item)">{{ item.desc }}</span>
                                <input v-else v-model="item.desc" type="text" ref="edit" @change="edited(item)" class="input-editable">                                    
                                </td>
                                <td>
                                    <a href="#" v-if=" ! item.file">                                    
                                        <input v-if="item.status.val != 'approved'" :id="'file_' + item.id" type="file" @change="fileInputChange($event, idx, rIdx)" class="d-none">
                                        <label v-if="item.status.val != 'approved'" :for="'file_' + item.id"><img src="/media/img/new-images/upload-icon.svg" alt="file"></label>
                                    </a>
                                    <a target="_blank" :href="item.file" v-else><img src="/media/img/new-images/pdf-icon.svg" alt="file"></a>
                                </td>
                                <td>
                                    <span v-if="item.file">{{item.uploaded}}</span>
                                    <span v-else>-</span>
                                </td>
                                <td>                                
                                    <!--<select v-model="item.status.val" @change="statusEdited(item)">-->
                                    <!--<option v-for="option in statusOptions" v-bind:value="option.val">{{option.label}}</option>-->
                                    <!--</select>-->
                                    
                                    <div class="multiselect-col">
                                        <multiselect v-model="item.status"  @select="statusEdited(item)"  :options="statusOptions" track-by="val" label="label" :searchable="false" :allow-empty="false"></multiselect>
                                    </div>
                                </td>
                                <td>
                                    <div class="more-box delivery-more-box">
                                        <div class="more-icon" @click="toggleDropdown($event)">
                                            <img src="/media/img/more-vec.png" alt="">
                                        </div>
                                        <div class="edit-box hide">
                                            <span class="delete-btn" @click.prevent="deleteItem(idx,rIdx)">{{deleteTxt}}</span>                   
                                        </div>  
                                    </div>
                                </td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="button-save">
                 <a  class="inline_block_btn orange_button" @click.prevent="save">{{saveTxt}}</a>
            </div>
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
    },
    components: {
        Multiselect: window.VueMultiselect.default
    },
    data() {
        return {
            items: [],
            selectAll: false,
            company: null,
            project: null,
            companies: [],
            projects: [],
            includeFiles: false,
            selectedCompany: null,
            selectedProject: null
        };
    },
    created() {
        axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.get['X-Requested-With'] = 'XMLHttpRequest';
    },
    methods: {

        init: function(){

            axios.get(this.itemsUrl)
                .then(response => {
                    this.items = response.data.items;
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
            var data = {projectId: this.selectedProject ? this.selectedProject.id : null, companyId: this.selectedCompany.id, includeFiles: this.includeFiles,items: []};

            for(var i = 0; i < this.items.length; i++){
                if( ! this.items[i].items.length || !this.items[i].checked) continue;
                for(var j = 0; j < this.items[i].items.length; j++){
                    if( this.items[i].items[j].desc.trim() == "" || this.items[i].items[j].isNew){
                        this.items[i].splice(j,1);
                    }else{
                        data.items.push(this.items[i].items[j]);
                    }
                }
            }

            axios.post(this.copyUrl,JSON.stringify(data)).then(response => {
                for(var i = 0; i < this.items.length; i++){
                    this.items[i].checked = false;
                }
            });
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
        save: function () {
            let formData = new FormData();
            var data = [];
            for(var i = 0; i < this.items.length; i++){
                if( ! this.items[i].items.length) continue;
                for(var j = 0; j < this.items[i].items.length; j++){
                    this.items[i].items[j].inEditMode = false;
                    if( ! this.items[i].items[j].edited) continue;
                    var reg = this.items[i].items[j];
                    // formData.append('items[' + reg.id +'][id]',reg.id);
                    // formData.append('items[' + reg.id +'][desc]',reg.desc);
                    // formData.append('items[' + reg.id +'][status]',reg.status);
                    if(reg.desc.length > 1){
                        data.push(reg);
                        if(reg.newFile){
                            formData.append('files[' + reg.id +']',reg.newFile);
                        }
                    }

                }
            }
            formData.append('Data',JSON.stringify(data));
            axios.post( this.saveUrl,
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            ).then(() => {
                this.init();
                document.getElementById("cert-table").scrollIntoView();
            })
                .catch(function(){
                    console.log('FAILURE!!');
            });


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

