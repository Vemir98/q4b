Vue.component('tasks-list', {
    template: `
        <section class='lt_elements_list tasks'>
            <div v-if="showLoader || copyInProgress" class="loader_backdrop_vue">
                <div class="loader"></div>
            </div>
            <div class="new-styles tasks-container" id="tasks-container">
                <section class='q4b-modules'>
                    <div class="elements_title_sec qb-modules">
                        <div class="lt_page_title">{{ trans.tasks }} / <span class="project_name"> {{ project?.name }}</span></div>
                        <div class="qm-header-btns">
                            <a 
                                :class="[ 'flex-center enabled', {selected: activeTab === 'enabled' }]" 
                                @click="activeTab = 'enabled'"
                                >
                                {{ trans.enabled }}
                            </a>
                            <div style="width: 1px;background-color: rgba(0, 0, 0, 0.2);"></div>
                            <a 
                                 :class="[ 'flex-center disabled-module', { selected: activeTab === 'disabled' }]" 
                                 @click="activeTab = 'disabled'"
                                 >
                                 {{ trans.disabled }}
                            </a>
                            <div style="width: 1px;background-color: rgba(0, 0, 0, 0.2);"></div>
                            <a 
                                 :class="[ 'flex-center disabled-module', { selected: activeTab === 'all' }]" 
                                 @click="activeTab = 'all'"
                                 >
                                 {{ trans.all }}
                            </a>
                        </div>
                    </div>
                    <div class="lt_elements_list_inputs" v-if="items.length">
                        <div class="lt_elements_list_input_headline">{{ trans.copy_to }}:</div>
                        <div class="lt_elements_list_input">             
                           <div class="multiselect-col">
                                <multiselect v-model="selectedCompany"  :option-height="104" :placeholder="trans.select_company" :disabled="(companies.length < 1) || (activeTab !== 'enabled')" :options="companies" track-by="id" label="name" :searchable="true" :allow-empty="false" :show-labels="false">
                                    <template slot="singleLabel" slot-scope="props">{{ props.option.name }}</template>
                                    <template slot="option" slot-scope="props">
                                    <span>{{ props.option.name }}</span>
                                    </template>
                                </multiselect>  
                            </div>                                           
                        </div>
                        <div class="lt_elements_list_input">     
                            <div class="multiselect-col">
                                 <multiselect v-model="selectedProject"  :option-height="104" :placeholder="trans.select_project" :disabled="(cmpProjects.length < 1) || (activeTab !== 'enabled')" :options="cmpProjects" track-by="id" label="name" :searchable="true" :allow-empty="false" :show-labels="false">
                                    <template slot="singleLabel" slot-scope="props">{{ props.option.name }}</template>
                                    <template slot="option" slot-scope="props">
                                    <span>{{ props.option.name }}</span>
                                    </template>
                                 </multiselect>
                            </div>
                        </div>
                        <div class="lt_elements_list_input_button">
                            <button 
                                @click="copy" 
                                :class="{'labtest-disabled': (!selectedProject || copyInProgress || !checkedTasks || activeTab === 'disabled')}"
                            >
                                {{ trans.copy }} 
                            </button> 
                        </div>
                    </div>
                    <div class="labtest_filters">
                        <div class="labtest_filters1">
                            <div class="labtest_filters1_left">
                                <div class="labtest_filter_input">                      
                                    <div class="multiselect-col">
                                        <div class="labtest_filter_input">                           
                                            <label class="table_label" :class="{'labtest-disabled': !crafts.length}">
                                                <span @click="toggleSelectAll('selectedCrafts', 'crafts')">
                                                    <template v-if="selectedCrafts.length < crafts.length">
                                                           {{ trans.select_all }}
                                                    </template>
                                                    <template v-else>
                                                           {{ trans.unselect_all }}
                                                    </template>
                                                </span>
                                            </label>
                                            <multiselect v-model="selectedCrafts"  
                                                    :placeholder="trans.select_specialty" 
                                                    :disabled="!crafts.length" 
                                                    :options="crafts" 
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
                                                    @select="onSelect($event, 'crafts')"
                                                    @remove="onRemove($event, 'crafts')"
                                                    >
                                                    <span class="multiselect-checkbox-label" :class="{'checked': scope.option.checked}"  slot="option" slot-scope="scope" >
                                                        <span class="multiselect-option-icon"><i class="q4bikon-tick"></i><span></span></span>
                                                        <span class="multiselect-option-name">{{ scope.option.name }}</span>
                                                    </span>
                                                  
                                                
                                                    <template slot='selection' slot-scope="{values, option, isOpen}"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ getMultiselectSelectionValue(values) }} </span></template>
                                                    <template slot="tag">{{ '' }}</template>
                                            </multiselect>      
                                        </div>          
                                    </div>
                                </div>
                                <div class="labtest_filter_input">
                                    <div class="multiselect-col">
                                        <div class="labtest_filter_input">                           
                                            <label class="table_label" :class="{'labtest-disabled': !modulesData.length}">
                                                <span @click="toggleSelectAll('selectedModules', 'modulesData')">
                                                    <template v-if="selectedModules.length < modulesData.length">
                                                           {{ trans.select_all }}
                                                    </template>
                                                    <template v-else>
                                                           {{ trans.unselect_all }}
                                                    </template>
                                                </span>
                                            </label>
                                            <multiselect v-model="selectedModules"  
                                                    :placeholder="trans.select_module" 
                                                    :disabled="!modulesData.length" 
                                                    :options="modulesData" 
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
                                                    @select="onSelect($event, 'modulesData')"
                                                    @remove="onRemove($event, 'modulesData')"
                                                    >
                                                    <span class="multiselect-checkbox-label" :class="{'checked': scope.option.checked}"  slot="option" slot-scope="scope" >
                                                        <span class="multiselect-option-icon"><i class="q4bikon-tick"></i><span></span></span>
                                                        <span class="multiselect-option-name">{{ scope.option.name }}</span>
                                                    </span>
                                                  
                                                
                                                    <template slot='selection' slot-scope="{values, option, isOpen}"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ getMultiselectSelectionValue(values) }} </span></template>
                                                    <template slot="tag">{{ '' }}</template>
                                            </multiselect>      
                                        </div> 
                                    </div>
                                </div>
                                <div class="labtest_filter_input">
                                    <button 
                                        class="labtest_filter_show_btn"
                                        @click="setupFilter">
                                        {{ trans.show }}
                                    </button>
                                </div>
                            </div>
                        </div>    
                    </div>
                    <div class="q4b-modules-actions">    
                        <div class="q4b-modules-actions-wraper">
                            <div class="q4b-modules-actions-sec flex-center">
                                <a 
                                  href="#"
                                  class="ceck-all"
                                  v-if="(this.filteredItems.length && (this.activeTab === 'enabled'))"
                                >                    
                                    <input 
                                        type="checkbox" 
                                        :checked="checkedTasks == this.filteredItems.length ? 'checked' : ''"
                                        @click="toggleCheckAllTasks('checkedTasks', 'filteredItems')"
                                    >
                                    <span 
                                        class="checkboxImg"
                                    ><span></span></span>
                                </a>
                                <a 
                                    class="add-new" 
                                    :class="{'labtest-disabled': (createTaskInProgress || activeTab !== 'enabled' )  }" 
                                    @click.prevent="addNewTask"
                                >
                                    <i class=" q4bikon-plus"></i>
                                </a>
                            </div>
                            <div class="q4b-modules-actions-sec flex-center">
                                <span 
                                    class="save" 
                                    :class="{'labtest-disabled': !this.items.length || createTaskInProgress || showLoader || activeTab !== 'enabled'}" 
                                    @click="handleSave"
                                >
                                    {{ trans.save }}                    
                                </span>
                            </div>    
                        </div>    
                    </div>    
                    <div class="tasks-section">    
                        <div class="tasks-wraper">
                            <template v-for="(item, index) in filteredItems">
                                <task-item 
                                    :taskData="item" 
                                    :trans="trans"
                                    :key="item.id"
                                    :ind="index"
                                    :ref="item.id"
                                    :activeTab="activeTab"
                                    :companyCrafts="cmpCrafts"
                                    :modules="modules"
                                    @taskUpdated="taskUpdated"
                                    @togglePopup="togglePopup"
                                    @taskChecked="taskChecked"
                                />
                            </template>  
                        </div>
                    </div>
                </section>
                <div class="modul-popup-wrap" v-if="openPopup">
        <div class="modul-popup">
        <div class="modul-popup-top">
            <span class="modul-popup-headline">{{ trans.modules }}</span>
        </div>
        <div class="modul-popup-main">
            <template v-for="module in modules">
               <div class="modul-popup-item" :class="{checked: selectedModulesOfCraft.includes(module.id)}">
                    <div class="modul-popup-item-left" >
                        <span class="modul-popup-check">
                            <input 
                                type="checkbox" 
                                :checked="selectedModulesOfCraft.includes(module.id) ? 'checked' : ''"
                                @click="toggleModule(module.id)"
                                >
                            <span class="checkboxImg">
                                <span></span>
                            </span>
                        </span>
                        <span class="modul-popup-name">{{ trans[transformTranslationsKey(module.name)] }}</span>
                    </div>
                    <span class="modul-popup-icon" :class="getModuleIconClass(module.id)"></span>
               </div>         
            </template>
        </div>
        <div class="modul-popup-btns">
        <button class="modul-popup-Confirm" @click="changeCraftModules">{{ trans.confirm }}</button>
        <button class="modul-popup-Cancel" @click="openPopup=false" >{{ trans.close }}</button>
        </div>
        
        </div>
        </div>
            </div>
        </section>
`,
    /** Props
     * projectId: 52
     * translations:"{
     * }"
     */
    props: {
        projectId: {required: true},
        translations: {required: true},
    },
    components: {
        Multiselect: window.VueMultiselect.default,
    },

    data() {
        return {
            activeTab: 'enabled',
            project: null,
            items: [],
            showLoader: false,
            copyInProgress: false,
            trans: JSON.parse(this.translations),
            openPopup: false,
            cmpCrafts: [],
            crafts: [],
            modules: [],
            selectedModulesOfCraft: [],
            currentTask: null,
            currentTaskIndex: null,
            currentCraft: null,
            editing: false,
            selectedCompany: null,
            selectedProject: null,
            companies: [],
            cmpProjects: [],
            modulesData: [],
            selectedCrafts: [],
            selectedModules: [],
            selectedCraftsIds: [],
            selectedModulesIds: [],
            checkedTasks: 0,
            firstFilterOfCrafts: false,
            firstFilterOfModules: false
        }
    },
    computed: {
        filteredItems() {
            return this.items.filter((item) => {
                let shouldBeInList = item.status === this.activeTab;

                if(this.activeTab === 'all') {
                    shouldBeInList = true;
                }

                let craftsCheck = false, modulesCheck = false;

                if (this.selectedCraftsIds.length) {
                    if  (item.crafts) {
                        if(!item.crafts.length) {
                            craftsCheck = true;
                            modulesCheck = true;
                        } else {
                            item.crafts.forEach((i) => {
                                if(this.selectedCraftsIds.includes(i.id)) {
                                    craftsCheck = true;
                                    const modules = i.modules
                                    if(modules) {
                                        if(!modules.length) {
                                            modulesCheck = true;
                                        } else {
                                            modules.forEach(m => {
                                                if (this.selectedModulesIds.includes(m)) {
                                                    modulesCheck = true
                                                }
                                            })
                                        }
                                    }
                                }
                            })
                        }
                    }
                }
                // if (this.selectedModulesIds.length) {
                //     if  (item.crafts) {
                //         Object.keys(item.crafts).forEach(craftId => {
                //             const modules = item.crafts[craftId].modules
                //             if(modules) {
                //                 if(!modules.length) {
                //                     modulesCheck = true;
                //                 } else {
                //                     modules.forEach(m => {
                //                         if (this.selectedModulesIds.includes(m)) {
                //                             console.log('moduleID',m,this.selectedModulesIds,item.id,true)
                //                             modulesCheck = true
                //                         }
                //                     })
                //                 }
                //             }
                //         })
                //     }
                // }

                if ( (this.activeTab === 'enabled' && item.id.includes('new_'))) {
                    return true;
                }

                return (shouldBeInList && craftsCheck && modulesCheck)
            });
        },
        createTaskInProgress() {
            let inProgress = false
                this.items.forEach(task => {
                    if (!task.name || !task.crafts.length) {
                        inProgress = true
                    }
                    if (task.crafts && task.crafts.length) {
                        task.crafts.forEach(craft => {
                            const modules = craft.modules
                            if(!modules || !modules.length) {
                                inProgress = true
                            }
                        })
                    }
                })
            return inProgress
        },
    },
    created() {

    },
    mounted() {
        this.getProject(this.projectId);
        this.getTasks(this.projectId);
    },
    watch: {
        selectedCompany(val) {
            if (val) {
                this.getCmpProjects()
            } else {
                this.selectedCompany = null
            }
            this.selectedProject = null;
        },
        selectedCrafts() {
            if(!this.firstFilterOfCrafts) {
                this.setupFilter();
                this.firstFilterOfCrafts = true;
            }

        },
        selectedModules() {
            if(!this.firstFilterOfModules) {
                this.setupFilter();
                this.firstFilterOfModules = true;
            }
        }
    },
    methods: {
        addNewTask() {
            const tasks = [...this.items];
            tasks.unshift({
                id: `new_${+ new Date()}`,
                name: "",
                status: 'enabled',
                crafts: []
            })
            this.items = [...tasks]
        },
        setupFilter() {
            let ids = [];
            this.selectedCrafts.forEach(c => {
                ids.push(c.id)
            })
            this.selectedCraftsIds = ids;

            let mIds = [];
            this.selectedModules.forEach(m => {
                mIds.push(m.id)
            })
            this.selectedModulesIds = mIds
        },
        onSelect(option, objName) {
            let index = this[objName].findIndex(item => item.id==option.id);
            this[objName][index].checked = true;
        },
        onRemove(option, objName) {
            let index = this[objName].findIndex(item => item.id==option.id);
            this[objName][index].checked = false;
        },
        getMultiselectSelectionValue(values, trans) {
            let vals = [];
            values.forEach(val => {
                vals.push(!trans ? val.name : this.trans[val.name])
            });
            return vals.join(', ');
        },
        getCmpProjects(){
            if (!this.selectedCompany) return
            this.showLoader = true;
            let url = `/companies/${this.selectedCompany.id}/entities/projects`;

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.cmpProjects = response.items ? response.items : [];
                    this.showLoader = false;
                })
        },
        getCompanies(){
            this.showLoader = true;
            let url = '/companies/entities/list';

            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.companies = response.items ? response.items : [];
                    this.showLoader = false;
                })
        },
        handleSave() {
            if (!this.items.length) return
            let tasksToCreate = [], tasksToUpdate = []
            this.items.forEach(task => {
                if (task.id.includes('new_')) {
                    tasksToCreate.push(task)
                } else {
                    if(task.updated) {
                        tasksToUpdate.push(task)
                    }
                }
            })

            if (tasksToCreate.length) {
                this.saveTasks(tasksToCreate)
            }
            if (tasksToUpdate.length) {
                this.updateTasks(tasksToUpdate)
            }
        },
        saveTasks(tasks) {
            this.showLoader = true;
            let url = `/projects/${this.project.id}/tasks`;

            qfetch(url, {method: 'POST', headers: {}, body: tasks})
                .then(response => {
                    this.getTasks(this.projectId);
                }).catch(err =>  {
                this.showLoader = false;
            })
        },
        updateTasks(tasks) {
            this.showLoader = true;

            let url = `/projects/${this.project.id}/tasks`;

            qfetch(url, {method: 'PUT', headers: {}, body: {tasks: tasks}})
                .then(response => {
                    this.getTasks(this.projectId);
                }).catch(err =>  {
                this.showLoader = false;
            })
        },
        addOrRemoveCraft(data) {
            let { index, craftId } = data;
            let item = this.items[index];
            let ind = item.crafts.indexOf(craftId);
            if (ind != -1) {
                item.crafts.splice(ind, 1)
            } else {
                item.crafts.push(craftId)
            }
        },
        prepareElCraftsData() {
            let elArr = [];
            this.items.forEach(i => {
                let data = {id: i.id, crafts: i.crafts}
                elArr.push(data)
            })
            return elArr
        },
        copy() {
            let tasksToCopyIds = [];
            this.items.forEach(i => {
                if (i.checked) {
                    tasksToCopyIds.push(i.id)
                }
            })
            if (tasksToCopyIds.length) {
                this.copySelectedTasks(tasksToCopyIds);
            }
        },
        copySelectedTasks(ids) {
            this.copyInProgress = true;
            let data = {
                projectId: this.selectedProject.id,
                ids: ids
            }
            let url = `/projects/${this.project.id}/tasks/copy`;
            qfetch(url, {method: 'POST', headers: {}, body: data})
                .then(() => {
                    this.copyInProgress = false;
                    this.editing = false;
                    this.selectedProject = null;
                }).catch(() => {
                    this.editing = false;
                    this.copyInProgress = false;
            });
        },
        getTasks(projectId) {
            this.showLoader = true;
            let url = `/projects/${projectId}/tasks/list`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.items = response.items;
                    this.items.forEach(i => {
                        i.checked = false
                        i.updated = false
                    })
                    this.showLoader = false;
                })
                .catch(() => {
                    this.showLoader = false;
                })
        },
        getProject(id) {
            this.showLoader = true;
            let url = `/projects/${id}/entities/project?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.project = response.item;
                    this.getCompanies();
                    this.getCompanyCrafts(this.project.company_id);
                    this.getModules(this.project.company_id);
                    this.showLoader = false;
                })
        },
        getModules(companyId) {
            this.showLoader = true;
            let url = `/companies/${companyId}/entities/modules`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.modules = response.items;
                    this.modulesData = JSON.parse(JSON.stringify(this.modules.map(module => {
                        return {
                            id: module.id,
                            name: this.trans[this.transformTranslationsKey(module.name)],
                            status: module.status
                        };
                    })));

                    this.toggleSelectAll('selectedModules', 'modulesData');
                    this.showLoader = false;
                })
        },
        getCompanyCrafts(companyId) {
            this.showLoader = true;
            let fields="id,name,companyId,catalogNumber,status,relatedId";
            let url = `/companies/${companyId}/entities/crafts?fields=${fields}`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    let cmpCrafts = response.items;
                    cmpCrafts.forEach(i => i.checked = false)
                    this.cmpCrafts = cmpCrafts;
                    this.crafts = JSON.parse(JSON.stringify(this.cmpCrafts));
                    this.toggleSelectAll('selectedCrafts', 'crafts');
                    this.showLoader = false;
                })
        },
        toggleCheckAllTasks(checkedCount, list) {
            if (list.length) {
                if (this[checkedCount] < this[list].length) {
                    let newArr = [];
                    this['items'].forEach((i) => {
                        if(i.status === 'enabled') {
                            i.checked = true;
                        }
                        newArr.push(JSON.parse(JSON.stringify(i)))
                    });
                    this['items'] = [...newArr]
                    this[checkedCount] = this[list].length
                } else {
                    let newArr = [];
                    this['items'].forEach((i) => {
                        if(i.status === 'enabled') {
                            i.checked = false;
                        }
                        newArr.push(JSON.parse(JSON.stringify(i)))
                    });
                    this['items'] = [...newArr]
                    this[checkedCount] = 0
                }
            }
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
        togglePopup(data) {
          this.selectedModulesOfCraft = data.craft.modules;
          this.currentTask = data.task;
          this.currentTaskIndex = data.index;
          this.currentCraft = data.craft;
          this.openPopup = !this.openPopup;
        },
        toggleModule(moduleId) {
            if(this.selectedModulesOfCraft.includes(moduleId)) {
                let index = this.selectedModulesOfCraft.findIndex(id => +id === +moduleId);
                this.selectedModulesOfCraft.splice(index, 1);
            } else {
                this.selectedModulesOfCraft.push(moduleId)
            }
        },
        changeCraftModules() {
            // Object.keys(this.currentTask.crafts).forEach(craftId => {
            this.currentTask.crafts.forEach(craft => {
                if(+craft.id === +this.currentCraft.id) {
                    craft.modules = this.selectedModulesOfCraft;

                    try {
                        this.$refs[this.currentTask.id][0].renderCrafts()
                    } catch (e) {
                        console.log(e)
                    }
                }
            })
            this.taskUpdated({ index: this.currentTaskIndex, task: this.currentTask })
            this.resetPopupData();
        },
        getModuleIconClass(moduleId) {
            switch (+moduleId) {
                case 1:
                    return 'q4bikon-reports-module-icon';
                case 2:
                    return 'q4bikon-delivery-module-icon';
                case 3:
                    return 'q4bikon-labtest-mod-icon';
                case 4:
                    return 'q4bikon-element-module-icon';
            }
        },
        resetPopupData() {
            this.currentTask = null;
            this.currentCraft = null;
            this.selectedModulesOfCraft = null;
            this.openPopup = false;
        },
        taskUpdated(data) {
            let { index, task } = data;
            let items = [... this.items];
            let t = this.filteredItems[index];
            if(t.status !== task.status) {
                this.updateTasks([task])
            }
            task.updated = true;

            let itemInd = this.items.findIndex(i => {
                return t.id === i.id;
            })
            items[itemInd] = task;
            this.items = [...items];
        },
        taskChecked(data) {
            let { index, task } = data;
            this.checkedTasks = task.checked ? ++this.checkedTasks : --this.checkedTasks
            this.taskUpdated(data);
        },
        transformTranslationsKey(name) {
            return name.toLowerCase().split(' ').join('_')
        }
    },
});

