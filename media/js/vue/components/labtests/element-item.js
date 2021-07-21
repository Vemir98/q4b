Vue.component('element-item', {
    template: `
     <tr>
          <td scope="row" class="element_name">
          <template v-if="item.id && !editing">
                <div>{{ item.name }} </div>

          </template>
          <template v-else>
                <input :id="'input_'+index" type="text" :readonly="item.id && !editing" v-model="item.name" maxlength="50">
          </template>
              <button class="save_elem" :class="{ 'labtest-disabled': !item.name }" v-if="editing" @click="updateElement"></button>
          </td>
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
                      <div class="element_edit" v-if="item.id" @click="editItem">{{ trans.edit }}</div>
                      <div class="element_delete" :class="{'labtest-disabled': item.labtestsCount>0}" @click="deleteItem">{{ trans.delete }}</div>
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
        focusInput() {
            setTimeout(() => {
                document.getElementById('input_'+ this.index).focus();
            }, 1000)
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
            this.focusInput();
        },
        updateElement() {
            let url = `/projects/52/labtests/elements/${this.item.id}`;
            qfetch(url, {method: 'PUT', headers: {}, body: {name: this.item.name}})
                .then(response => {
                    this.$emit('itemUpdated', { index: this.index, name: this.item.name })
                    this.editing = false
                })
        },
        deleteItem() {
            this.item.more = false;
            this.$emit('deleteItem', { index: this.index })
        }
    },
});

