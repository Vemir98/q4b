Vue.component('element-crafts-item', {
    template: `
        <div class="element_type_item">
            <div class="element_type_name">{{ item.name }}</div>
            <div class="element_type_DD">
                <template v-for="(craft, index) in crafts">
                    <div class="element_type_DD_item">
                        <input type="checkbox" :checked="inArray(craft.id, item.crafts)" @change="addOrRemoveCraft(craft.id)"><span class="checkboxImg"><span></span></span>
                        <span class="element_type_DD_item_text">{{ craft.name }}</span>
                    </div>
                </template>          
            </div>
        </div>
`,
    props: {
        trans: {required: true},
        itemData: {required: true},
        cmpCrafts: {required: true},
        index: {required: true},
    },
    components: {

    },
    data() {
        return {
            item: this.itemData,
            editing: false,
            crafts: JSON.parse(JSON.stringify(this.cmpCrafts)),
        }
    },
    computed: {
        currentLang() {
            return $(".header-current-lang").data("lang")
        }
    },
    watch: {
        itemData(val) {
            this.item = val
        },
        cmpCrafts(val) {
            this.crafts = val
        }
    },
    methods: {
        addOrRemoveCraft(id) {
            this.$emit('addOrRemoveCraft', {index: this.index, craftId: id})
        },
        inArray(id, arr) {
            return arr.includes(id)
        },
        focusInput() {
            setTimeout(() => {
                // $(document).find("#input_"+ this.index).focus()
                $($(document).find("#input_"+ this.index)[0]).click()
            }, 100)
        },
        toggleMore() {
            this.$emit('toggleMore', { item: this.item, index: this.index })
        },
        getImage(){
            return this.item.image_id ? "url("+this.item.imgPath+")" : "url('/media/img/camera.png')";
        },
        getDate(timestamp){
            if (timestamp) {
                return moment.unix(timestamp).tz("Asia/Jerusalem").format('DD/MM/YYYY')
            }
        },
        editItem() {
            this.item.more = false;
            this.editing = true;
        },
        updateElement() {
            let url = `/projects/52/labtests/elements/${this.item.id}`;
            qfetch(url, {method: 'PUT', headers: {}, body: {name: this.item.name}})
                .then(response => {
                    this.editing = false
                })
        },
        deleteItem() {
            this.item.more = false;
            this.$emit('deleteItem', { index: this.index })
        }
    },
    mounted() {
        this.focusInput()
    },
    created() {
        // $(document).click(function(event){
        //     console.log(11, $(event.target).hasClass('elements_more_d_down'));
        //     if (!$(event.target).hasClass('elements_more_d_down')) {
        //         $(".elements_more_d_down").fadeOut().removeClass("open");
        //     } else {
        //         $(".elements_more_d_down").fadeOut().addClass("open");
        //
        //     }
        // });

    },
});

