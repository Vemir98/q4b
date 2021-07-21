Vue.component('elements-type', {
    template: `
        <section class='lt_elements_type'>
            <div class="elements_title_sec">
                <div class="lt_page_title">{{ trans.elements_type }} / <span class="project_name"> {{ project ? project.name : '' }} </span></div>
                <div class="elements_title_left">
                    
                    <form class="elements-form" action="">
                        <i class="q4bikon-search1 icon"></i>
                        <input type="text" v-on:input="handleSearch()" v-model="search" class="qc-id-to-show q4-form-input" :placeholder="trans.search + '...'"
                            value="">
                    </form>
                </div>
            </div>
            <div class="element_list_wrap">
                <div class="element_add">
                    <button :class="{'labtest-disabled': !enabledSave }" @click="addItem"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                            fill="none">
                            <path
                                d="M18.75 7.5H12.5V1.25C12.5 0.5625 11.9375 0 11.25 0H8.75C8.0625 0 7.5 0.5625 7.5 1.25V7.5H1.25C0.5625 7.5 0 8.0625 0 8.75V11.25C0 11.9375 0.5625 12.5 1.25 12.5H7.5V18.75C7.5 19.4375 8.0625 20 8.75 20H11.25C11.9375 20 12.5 19.4375 12.5 18.75V12.5H18.75C19.4375 12.5 20 11.9375 20 11.25V8.75C20 8.0625 19.4375 7.5 18.75 7.5Z"
                                fill="#F99C19" />
                        </svg>
                    </button>
                </div>
                <div class="element_table" :class="{'empty': !showLoader && !items.length}">
                    <div v-if="showLoader" class="loader_backdrop">
                        <div class="loader"></div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ trans.description }}</th>
                                <th>{{ trans.more }}</th>
        
                            </tr>
                        </thead>
                        <tbody>                       
                            <element-item v-show="!showLoader" v-for="(item, index) in itemsSearched"
                                :trans="trans"
                                :itemData="item"
                                :index="index"
                                @toggleMore="toggleMore"
                                @deleteItem="deleteItem"
                                @itemUpdated="itemUpdated">                           
                            </element-item>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="elements_save_btn">
                <button :class="{'labtest-disabled': !enabledSave }" v-show="showSave" @click="save">{{ trans.save }}</button>
            </div>
            <confirm-modal 
                v-if="needToConfirm"
                :msg="msg" 
                :trans="trans" 
                :deletable="trans.element"
                :deletable-id="deletable.name"
                :modal-data="modalData"
                @closeConfirm="needToConfirm=false"
                @deleteConfirmed="deleteConfirmed"
                >
            </confirm-modal>
        </section>
`,
    /** Props
     * projectId: 52
     * translations:"{
     * "elements_type":"Elements Type",
     * "description":"Description",
     * "more":"More",
     * "edit":"Edit",
     * "delete":"Delete",
     * "save":"Save",
     * "search":"Search"
     * }"
     */
    props: {
        projectId: {required: true},
        translations: {required: false},
    },

    data() {
        return {
            project: null,
            items: [],
            showLoader: true,
            trans: JSON.parse(this.translations),
            search: '',
            itemsSearched: [],
            needToConfirm: false,
            deletable: null,
            modalData: null,
            msg: ""
        }
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
        enabledSave() {
            let valid = true;
            this.itemsSearched.forEach(item => {
                this.itemsSearched.forEach(i => {
                    if (!i.name) valid = false
                })
            })
            return valid;
        },
        showSave() {
            let show = false;
            this.itemsSearched.forEach(item => {
                this.itemsSearched.forEach(i => {
                    if (!i.id) show = true
                })
            })
            return show;
        }
    },
    mounted() {
        if (this.projectId) {
            this.getProject(this.projectId);
        }
    },
    watch: {
        projectId(val) {
            if(val) {
                this.getProject(val);
                this.getElements();
            }
        },
        project(val) {
            if(val) {
                this.getElements();
            }
        }
    },
    methods: {
        /**
         *  Bulk save if element(s) added
         * */
        save() {
            let newElements = [];
            this.itemsSearched.forEach(i => {
                if (!i.id) {
                    newElements.push(i)
                }
            })
            if (newElements.length) {
                this.sendSaveRequest(newElements);
            }
        },
        handleSearch() {
            this.itemsSearched = this.items.filter((item) => {
                let n = item.name;
                let s = this.search;
                return n.toLowerCase().includes(s.toLowerCase());
            })
        },
        addItem() {
            let newItem = { name: '' };
            this.itemsSearched.unshift(newItem);
            this.focusInput(0)
        },
        focusInput() {
            setTimeout((ind) => {
                document.getElementById('input_0').focus();
            }, 0)
        },
        toggleMore(data) {
            let {item, index} = data;
            let obj = {...item};
            obj.more = !obj.more;
            item = {...obj};
            if (obj.more) {
                var items = this.itemsSearched.map((item) => {
                    item.more = false
                    return item;
                });
            } else {
                var items = [...this.itemsSearched];
            }
            items[index] = obj;
            this.itemsSearched = [...items];
        },
        itemUpdated(data) {
            let { index, name } = data;
            let items = [... this.itemsSearched];
            items[index].name = name;
            console.log(22, items[index]);
            this.itemsSearched = [... items];
            let itemInd = this.items.findIndex(i => {
                return items[index].id === i.id;
            })
            this.items[itemInd].name = name;
        },
        deleteItem(data) {
            this.needToConfirm = true;
            let { index } = data;
            this.deletable = this.itemsSearched[index];
            this.modalData = data;
            this.msg = `${this.trans.are_you_sure_to_delete}`
        },
        deleteConfirmed(data) {
            this.needToConfirm = false;
            let { index } = data;
            let items = [... this.itemsSearched];
            let obj = items[index];
            if (obj.id) {
                this.sendDeleteRequest(obj.id, () => {
                    let itemInd = this.items.findIndex(i => {
                        return obj.id === i.id;
                    })
                    if (itemInd) this.items.splice(itemInd, 1)
                    items.splice(index,1);
                    this.itemsSearched = [...items];
                })
            } else {
                items.splice(index,1);
                this.itemsSearched = [...items];
            }
        },
        sendDeleteRequest(id, callback) {
            let url = `/projects/${this.project.id}/labtests/elements/${id}`;
            qfetch(url, {method: 'DELETE', headers: {}})
                .then(response => {
                    if (parseInt(response.success) && callback) {
                        callback()
                    }
                })
        },
        sendSaveRequest(newElements) {
            let url = `/projects/${this.project.id}/labtests/elements`;

            qfetch(url, {method: 'POST', headers: {}, body: newElements})
                .then(response => {
                    this.search = '';
                    this.items = response.items;
                    this.items.forEach(i => i.more = false)
                    this.handleSearch();
                })
        },

        getProject(id) {
            let url = `/projects/${id}/entities/project?fields=id,name`;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    this.project = response.item;
                })
        },
        getElements(){
            this.showLoader = true;
            let url = `/projects/${this.project.id}/labtests/elements`;
            let param = encodeURIComponent('?search='+ this.search)
            url +=  param;
            qfetch(url, {method: 'GET', headers: {}})
                .then(response => {
                    let items = response.items;
                    items.forEach(i => i.more = false)
                    this.items = items;
                    this.itemsSearched = items;
                    setTimeout(() => {
                        this.showLoader = false;
                    }, 100)
                })
        },
    },

});

