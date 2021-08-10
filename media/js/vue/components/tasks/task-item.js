Vue.component('task-item', {
    template: `
    <div class="task-item " v-if="task">
        <div class="task-top-sec flex-between">
            <div class="task-top-description flex-center">
                <span class="check-task" v-if="!task.id.includes('new_') && (task.status !== 'disabled') && (activeTab === 'enabled')">
                    <input 
                        type="checkbox" 
                        :checked="task.checked ? 'checked' : ''" 
                        @change="checkOrUncheckTask"
                    >
                    <span 
                        class="checkboxImg"
                    ><span></span></span>
                </span>
                <span class="task-headline">{{ trans.task }} </span>
                <span class="task-id">{{ task.id.includes('new_') ? ' - ' : task.id }}</span>
            </div>
            <div class="task-btns">
                <a @click="changeType" v-if="!task.id.includes('new_')">
                    {{ task.status === 'enabled' ? trans.disable : trans.enable }}
                </a>
            </div>
        </div>
        <div class="task-main-sec flex-start">
            <div class="task-description">
                <textarea
                   v-model="task.name"
                   :placeholder="trans.enter_task_description"
                   cols="30"
                   rows="10"
                   :disabled="(task.status === 'disabled' || (activeTab === 'all'))"
                ></textarea>
            </div>
            <div class="task-modules-sec">
                <div class="task-modules-top flex-start">
                    <div class="task-modules-ddown">
                     <div class="multiselect-col">
                        <multiselect 
                            v-model="selectedCrafts"  
                            :placeholder="trans.select_specialty" 
                            :disabled="!crafts.length || (task.status === 'disabled') || (activeTab === 'all')" 
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
                            @select="onSelect($event)"
                            @remove="onRemove($event)"
                            >
                            <span class="multiselect-checkbox-label" :class="{'checked': scope.option.checked}"  slot="option" slot-scope="scope">
                                <span class="multiselect-option-icon"><i class="q4bikon-tick"></i><span></span></span>
                                <span class="multiselect-option-name">{{ scope.option.name }}</span>
                            </span>
                            <template slot='selection' slot-scope="{values, option, isOpen}"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ trans.select_specialty }} </span></template>
                            <template slot="tag">{{ '' }}</template>                          
                        </multiselect>   
                    </div>
                </div>
                </div>
                <div class="task-modules-item-wraper">
                    <template v-for="craft in selectedCrafts">
                        <div class="task-modules-item flex-start" :key="craft.id">
                        <div class="task-craft-item flex-between">
                            <span class="task-craft-name">{{ craft.name }}</span>
                            <button 
                                class="task-craft-remove" 
                                v-if="(task.status !== 'disabled') && (activeTab !== 'all')" 
                                @click="onRemove(craft)"
                            >
                            <i class="q4bikon-close"></i></button>
                        </div>
                        <div class="task-modules-wrap flex-start">
                            <template v-for="module in craft.modules">
                                <div class="task-module">
                                    <button @click="togglePopup(craft)">   
                                        <button><i :class="getModuleIconClass(module)"></i></button></i>
                                    </button>
                                </div>
                            </template>
                            <div 
                                class="task-module-add" 
                                v-if="(craft.modules.length !== modules.length) && (task.status !== 'disabled') && (activeTab === 'enabled')"
                            >
                                <button @click="togglePopup(craft)" >
                                    <i class="q4bikon-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    </template>
                </div>
            </div>
        </div>
    </div>    

`,
    props: {
        ind: {required: true},
        taskData: {required: true},
        trans: {required: true},
        companyCrafts: {required: true},
        modules: {required: true},
        activeTab: {required: true}
    },
    components: {
        Multiselect: window.VueMultiselect.default,
    },
    data() {
        return {
            task: JSON.parse(JSON.stringify(this.taskData)),
            crafts: JSON.parse(JSON.stringify(this.companyCrafts)),
        }
    },
    computed: {
        selectedCrafts: {
            get: function () {
                return this.crafts.filter(i => i.checked).sort((x, y) => x.selectedTimestampForSorting - y.selectedTimestampForSorting)
            },
            set: function (newValue) {
                return newValue
            }
        },
    },
    watch: {
        selectedCrafts(newVal) {
            if(this.task.crafts) {
                this.task.crafts.forEach(c => {
                    this.crafts.forEach((craft, idx) => {
                        if (+craft.id === +c.id) {
                            this.crafts[idx].checked = true;
                            this.crafts[idx].modules = c.modules;
                        }
                    })
                })
            }
        },
        companyCrafts: {
            handler(val) {
                if(val.length) {
                    this.crafts = JSON.parse(JSON.stringify(val));
                    this.renderCrafts();
                }
            },
            deep: true
        },
        taskData(newVal) {
            this.task = JSON.parse(JSON.stringify(newVal));
            this.renderCrafts();
        },
        'task.name': {
            handler() {
                this.updateTask()
            }
        },
        'taskData.checked': {
            handler() {
                this.task = JSON.parse(JSON.stringify(this.taskData));
            },
            deep: true
        }
    },
    methods: {
        updateTaskData() {
            this.task = JSON.parse(JSON.stringify(this.taskData));
        },
        checkOrUncheckTask() {
            this.task.checked = !this.task.checked;
            this.$emit('taskChecked', {
                index: this.ind,
                task: this.task
            })
        },
        getMultiselectSelectionValue(values, trans) {
            let vals = [];
            values.forEach(val => {
                vals.push(!trans ? val.name : this.trans[val.name])
            });
            return vals.join(', ');
        },
        updateTask() {
            this.$emit('taskUpdated', {
                index: this.ind,
                task: this.task
            })
        },
        changeType() {
            this.task.status = this.task.status === 'enabled' ? 'disabled' : 'enabled'
            this.updateTask()
        },
        togglePopup(craft) {
            if(this.task.status === 'disabled' || this.activeTab === 'all') {
                return false;
            }
            if(!craft.modules) craft.modules = [];
            this.$emit('togglePopup', {
                craft: JSON.parse(JSON.stringify(craft)),
                task: JSON.parse(JSON.stringify(this.task)),
                index: this.ind,
            })
        },
        onSelect(craft) {

            let index = this.crafts.findIndex(item => item.id==craft.id);
            this.crafts[index].checked = true;
            this.crafts[index].selectedTimestampForSorting = (+ new Date());
            this.task.render = true;
            this.task.crafts.push({
                id: craft.id,
                modules: [],
            })
            this.updateTask();
        },
        onRemove(craft, fromOutside) {

            let index = this.crafts.findIndex(item => item.id==craft.id);
            this.crafts[index].checked = false;
            delete this.crafts[index].modules
            this.task.render = true;
            this.task.crafts.splice(this.task.crafts.findIndex(item => item.id==craft.id),1);
            this.updateTask();
        },
        renderCrafts() {
            if(this.task.crafts) {
                this.task.crafts.forEach(c => {
                    this.crafts.map(craft => {
                        if (+craft.id === +c.id) {
                            craft.checked = true;
                            craft.modules = c.modules;
                        }
                    })
                })
            }
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
        }
        // focusInput() {
        //     setTimeout(() => {
        //         document.getElementById('input_'+ this.index).focus();
        //     }, 1000)
        // },
        // toggleMore() {
        //     this.$emit('toggleMore', { item: this.item, index: this.index })
        // },
        // getImage(){
        //     return this.item.image_id ? "url("+this.item.imgPath+")" : "url('/media/img/camera.png')";
        // },
        // getDate(timestamp){
        //     if (timestamp) {
        //         return moment.unix(timestamp).tz("Asia/Jerusalem").format('DD/MM/YYYY')
        //     }
        // },
        // editItem() {
        //     this.item.more = false;
        //     this.editing = true;
        //     this.focusInput();
        // },
        // updateElement() {
        //     let url = `/projects/52/labtests/elements/${this.item.id}`;
        //     qfetch(url, {method: 'PUT', headers: {}, body: {name: this.item.name}})
        //         .then(response => {
        //             this.$emit('itemUpdated', { index: this.index, name: this.item.name })
        //             this.editing = false
        //         })
        // },
        // deleteItem() {
        //     this.item.more = false;
        //     this.$emit('deleteItem', { index: this.index })
        // }
    },
    mounted() {
        this.renderCrafts();
    },
});

