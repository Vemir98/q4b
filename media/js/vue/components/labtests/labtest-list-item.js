Vue.component('labtest-list-item', {
    template: `
        <tr>
            <td scope="row" class="element_name">{{ item.id }}</td>
            <td>{{ item.certNumber }}</td>
            <td>{{ getDate(item.createDate) }}</td>
            <td>{{ item.buildingName }}</td>
            <td v-if="item.floor_custom_name">{{ item.floorCustomName }} <span class="bidi-override">({{ item.floorNumber }})</span></td>
            <td v-else> <span class="bidi-override">{{ item.floorNumber }}</span></td>
            <td>{{ item.elementName }}</td>
            <td>
                <div class="labtest_description">
                    {{ item.standard }}
                </div>
            </td>
            <td>{{ item.ticketNumber }}</td>
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
        fromProjects: {required: true},
        trans: {required: true},
        itemData: {required: true},
        index: {required: true},
        page: {required: true},
        filters: {required: true}
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
            //[nuynna]
            this.$emit('toggleMore', { item: this.item, index: this.index })
        },
        getDate(timestamp){
            //[nuynna]
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
            //[nuynna]
            this.$emit('toggleMore', { item: this.item, index: this.index })
        },
        getDate(timestamp){
            //[nuynna]
            if (timestamp) {
                return moment.unix(timestamp).tz("Asia/Jerusalem").format('DD/MM/YYYY')
            }
        },
        editItem() {
            this.item.more = false;
            this.editing = true;
        },
        edit() {
            let url = '';
            if(this.fromProjects) {
                url = `/labtests/project/${this.projectId}/edit/${this.item.id}${this.getQueryParamsOfFiltersForUrl()}`;
            } else {
                url = `/reports/labtests/${this.projectId}/edit/${this.item.id}${this.getQueryParamsOfFiltersForUrl()}`;
            }
            if (this.currentLang !== 'en') {
                url = `/${this.currentLang}${url}`;
            }
            this.$destroy();
            sessionStorage.setItem('labtests-page', this.page);
            window.location.href = url;
        },
        getEditLink() {
            let url = '';

            if(this.fromProjects) {
                url = `/labtests/project/${this.projectId}/edit/${this.item.id}`;
            } else {
                url = `/reports/labtests/${this.projectId}/edit/${this.item.id}`;
            }

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
        },
        getQueryParamsOfFiltersForUrl() {
            if(this.filters) {
                let companyIds = encodeURIComponent(JSON.stringify(this.filters.companyIds));
                let projectIds = encodeURIComponent(JSON.stringify(this.filters.projectIds));
                let objectIds = encodeURIComponent(JSON.stringify(this.filters.objectIds));
                let floorIds = encodeURIComponent(JSON.stringify(this.filters.floorIds));
                let placeIds = encodeURIComponent(JSON.stringify(this.filters.placeIds));
                let craftIds = encodeURIComponent(JSON.stringify(this.filters.craftIds));
                let elementIds = encodeURIComponent(JSON.stringify(this.filters.elementIds));
                let statuses = encodeURIComponent(JSON.stringify(this.filters.statuses));
                let from = this.filters.from;
                let to = this.filters.to;

                return `?companyIds=${companyIds}&projectIds=${projectIds}&from=${from}&to=${to}&objectIds=${objectIds}&floorIds=${floorIds}&placeIds=${placeIds}&craftIds=${craftIds}&elementIds=${elementIds}&statuses=${statuses}`;
            } else {
                return '';
            }
        },

    }
});

