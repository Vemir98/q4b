Vue.component('project-item', {
    template: ` 
       <a class="lt_project_item" :href="url + item.id + '?tab=info'">
       <div class="">
       
                <div class="lt_project_item_top">
                    <div class="lt_project_item_img">
                        <img :src="image" />
                        <div class="lt_project_item_logo"><img src="/media/img/q4blogo.png" alt=""></div>
                        <div class="lt_project_item_name"> {{ item.name }}</div>
                        
                    </div>
                </div>
                <div class="lt_project_item_bottom">
                    <div class="lt_project_item_props_wraper">
                        <div class="lt_project_item_prop">
                            <span class="lt_project_item_prop_name">{{ trans.company }}</span>
                            <span class="lt_project_item_prop_value" v-html="item.comp_name"></span>
                        </div>

                        <div class="lt_project_item_prop">
                            <span class="lt_project_item_prop_name">{{ trans.status }} </span>
                            <span class="lt_project_item_prop_value">{{ item.status ? trans[item.status] : '' }} </span>
                        </div>

                        <div class="lt_project_item_prop">
                            <span class="lt_project_item_prop_name">{{ trans.start_date }} </span>
                            <span class="lt_project_item_prop_value">{{getDate(item.startDate)}}</span>
                        </div>

                        <div class="lt_project_item_prop">
                            <span class="lt_project_item_prop_name">{{ trans.end_date }} </span>
                            <span class="lt_project_item_prop_value">{{getDate(item.endDate)}}</span>
                        </div>
                    </div>
                    <div class="lt_project_item_desc" :class="{'open': open }">
                        <div class="lt_project_item_desc_owerwrap" :id="'item_'+item.id"  @click.stop.prevent="toggleDescr">
                            <div v-html="item.description"></div>
                        </div>
                    </div>
                </div>

            </div>
        </a>
        `,
    props: {
        item: {required: true},
        trans: {required: true},
        image: {required: true},
    },
    components: {

    },
    data() {
        return {
            open: false
        }
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        },
        url() {
            let urlTo = '/projects/update/';
            if (this.currentLang !== 'en') {
                urlTo = `/${this.currentLang}/projects/update/`
            }
            return urlTo
        },

    },
    mounted() {
        // setTimeout(() => {
            this.isToggle();
        // }, 100)
    },
    methods: {
        isToggle() {
            let el = document.getElementById('item_'+this.item.id);
            if (el) {
                let child = el.firstChild;

                if (el && child) {
                    if(child.offsetHeight - el.offsetHeight > 5) {
                        el.classList.add('show-toggler')
                    }
                }
            }
        },
        getImage(){
            return '/media/img/project_default'+ (Math.floor(Math.random() * 4) + 1) +".png";
            // return this.item.image_id && this.item.imgPath ? this.item.imgPath : '/media/img/project_default'+ (Math.floor(Math.random() * 4) + 1) +".png";
        },
        getDate(timestamp){
            if (timestamp) {
                return moment.unix(timestamp).tz("Asia/Jerusalem").format('DD/MM/YYYY')
            }
        },
        toggleDescr(){
            this.open = !this.open;
        }
    },
});

