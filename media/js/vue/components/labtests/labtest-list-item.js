Vue.component('labtest-list-item', {
    template: `
        <tr>
            <td scope="row" class="element_name">{{ item.id }}</td>
            <td>{{ item.cert_number }}</td>
            <td>{{ getDate(item.create_date) }}</td>
            <td>{{ item.building_name }}</td>
            <td v-if="item.floor_custom_name">{{ item.floor_custom_name }} <span class="bidi-override">({{ item.floor_number }})</span></td>
            <td v-else> <span class="bidi-override">{{ item.floor_number }}</span></td>
            <td>{{ item.element_name }}</td>
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
                      <div class="element_edit"><a @click="edit()">{{ trans.edit }}</a></div>
                      <div class="element_delete" @click="deleteItem()">{{ trans.delete }}</div>
                  </div>
              </div>
          </td>

        </tr>

`,
    props: {
        projectId: {required: true},
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
        edit() {
            let url = `/labtests/project/${this.projectId}/edit/${this.item.id}`;
            if (this.currentLang !== 'en') {
                url = `/${this.currentLang}${url}`;
            }
            this.$destroy();
            window.location.href = url;
        },
        getEditLink() {
            let url = `/labtests/project/${this.projectId}/edit/${this.item.id}`;
            if (this.currentLang !== 'en') {
                url = `/${this.currentLang}${url}`;
            }
            return url
        },
        updateElement() {
            let url = `/projects/${this.projectId}/labtests/elements/${this.item.id}`;
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
});

