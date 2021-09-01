Vue.component('labtest-item', {
    template: `
        <tr>
            <td scope="row" class="element_name">{{ item.testNumber }}</td>
            <td>{{ item.ticketNumber }}</td>
            <td>{{ getDate(item.createdAt) }}</td>
            <td>{{ item.buildingName }}</td>
            <td>{{ item.floorCustomName ? item.floorCustomName : item.floorNumber }} / <span class="bidi-override">{{ item.smallerFloor }}-{{ item.biggerFloor }}</span></td>
            <td>{{ item.elementName }}</td>
            <td>
                <div class="labtest_description">
                    {{ item.standard }}
                </div>
            </td>
            <td>{{ trans[item.status] }}</td>
            <td class='elements_more'>
              <div class="elements_moreS">
                  <div class="elements_more_btn">
                      <button @click.stop="toggleMore(item, index)">
                          <svg xmlns="http://www.w3.org/2000/svg" width="3" height="11"
                              viewBox="0 0 3 11" fill="none">
                              <circle cx="1.5" cy="1.5" r="1.5" fill="#1EBCE8" />
                              <circle cx="1.5" cy="5.5" r="1.5" fill="#1EBCE8" />
                              <circle cx="1.5" cy="9.5" r="1.5" fill="#1EBCE8" />
                          </svg>
                      </button>

                  </div>
                  <div class="elements_more_d_down" :class="{'open': item.more}">
                      <div class="element_edit" v-if="item.id" @click="editItem"><a :href="getEditLink()">{{ trans.edit }}</a></div>
                      <div class="element_delete" @click="deleteItem">{{ trans.delete }}</div>
                  </div>
              </div>
          </td>

        </tr>

`,
    props: {
        trans: {required: true},
        itemData: {required: true},
        index: {required: true},
    },
    components: {

    },
    data() {
        return {
            item: this.itemData,
            editing: false
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
        }
    },
    methods: {
        toggleMore() {
            this.$emit('toggleMore', { item: this.item, index: this.index })
        },
        getDate(timestamp){
            if (timestamp) {
                return moment.unix(timestamp).tz("Asia/Jerusalem").format('DD/MM/YYYY')
            }
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
        getDate(timestamp){
            if (timestamp) {
                return moment.unix(timestamp).tz("Asia/Jerusalem").format('DD/MM/YYYY')
            }
        },
        editItem() {
            this.item.more = false;
            this.editing = true;
        },
        getEditLink() {
            let url = `/labtests/project/52/edit/${this.item.id}`;
            return url
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

