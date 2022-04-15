Vue.component('file-control', {
    template: `
        <div class="filters-wraper files-section">
            <div class="labtest_attachment">
                <div class="ltest-input-label" style="padding: 5px">{{ trans.attached_files }} (jpeg, jpg, png)</div>
                <div class="ltest-input-label" style="padding: 5px">( {{ trans.file_size_text }} )</div>
                <div class="attach_file"
                    :class="{'q4b-disabled': !canChange}"
                    @click="handleAttachFileClick()"
                >
                </div>
                <input type="file" :id="fileInputName" @change="fileChange($event)" style="display: none" />
                <div class="q4b-attachment-wrapper">
                    <div v-if="files.length" class="q4b-attachment-title">{{ trans.list_of_files }}</div>
                    <template v-for="(file, index) in files">
                        <div class="q4b-attachment-item" :key="index">
                            <div v-if="includeEditor && fileIsImage(file.fileName)" class="q4b-attachment-icon">
                                <i class="icon q4bikon-file" @click="openModal(index)"></i>
                            </div>
                            <div v-else class="q4b-attachment-icon">
                                
                                <a v-if="file.id" :href="file.src" target="_blank" style="text-decoration: none;">
                                    <img :src="file.src">
                                </a>
                                <a v-else="file.id" style="text-decoration: none;cursor: auto">
                                    <img :src="file.src">
                                </a>
                            </div>
                            <div class="q4b-attachment-data">
                                <div class="q4b-attachment-data-content">
                                    <div class="q4b-attachment-name">{{ file.fileName }}</div>
                                    <div class="q4b-attachment-line"></div>
                                    <div class="q4b-attachment-uploaded">{{ trans.uploaded }}</div>
                                </div>
                                <div
                                    class="q4b-remove-image-icon"
                                    :class="{'q4b-disabled': !canChange}"
                                    @click.stop.prevent="deleteFile(index)"
                                ></div>
                            </div>
                        </div>    
                    </template>                                                  
                </div>
            </div>
        </div>
`,

    props: {
        translations: {required: true},
        data: {required: true},
        includeEditor: {required: true},
        allowedFormats: {type: Array},
        canChange: {required: true}
    },

    data() {
        return {
            files: JSON.parse(JSON.stringify(this.data)),
            openedFileIndex: null,
            showEditor: false,
            trans: JSON.parse(this.translations),
            fileInputName: 'fileInput_'+ (+(new Date()))
        }
    },
    watch: {
        files(files) {
          this.$emit('filesUpdated', files)
      }
    },
    methods: {
        handleAttachFileClick() {
            if(!this.canChange) return false;
            document.getElementById(this.fileInputName).click()
        },
        fileIsImage(file){
            let ext = file.split('.').pop().toLowerCase()
            return ['jpe','jpeg','jpg','png','tif','tiff'].includes(ext)
        },
        fileChange(event){
            let file = event.target.files[0];
            if(file){

                let ext = file.type.split('/')[1];
                if(!['jpe','jpeg','jpg','png','tif','tiff','pdf'].includes(ext)) {
                    ext = file.name.split('.').pop().toLowerCase()
                }
                if(this.allowedFormats.length) {
                    if(!this.allowedFormats.includes(ext)) {
                        return false;
                    }
                }
                let fileReader = new FileReader();
                fileReader.onload = (e) => {
                    this.files.unshift({
                        id: null,
                        fileName: Q4U.timestamp() + '.' + ext,
                        fileOriginalName: file.name,
                        filePath: '',
                        src: e.target.result,
                        ext: ext === 'jpg' ? 'jpeg' : ext
                    });
                }
                fileReader.readAsDataURL(file);
            }
        },
        openModal(index){
            this.openedFileIndex = index;
            setTimeout( () => {
                this.editor.resetCanvas(true);
                this.editor.setActiveTool('select')
                this.editor.loadBackgroundImageFromUrl(this.files[index].src , (error) => {
                    if(error){
                        this.closeModal();
                    }
                })
                this.showEditor = true;
                document.getElementsByTagName('body')[0].classList.add("hide_body_scroll");
            }, 1000)
        },
        filePath(path,name){
            if(path.indexOf('https://') >= 0){
                return path + '/' + name
            }
            return '/' + path + '/' + name
        },
        deleteFile(index){
            if(!this.canChange) return false;
            if(this.files[index]){
                this.files.splice(index, 1);
            }
        },
        closeModal(canceled){
            this.editor.tool('crop').cancel();
            document.getElementsByTagName('body')[0].classList.remove("hide_body_scroll");
            if (this.files[this.openedFileIndex].planId) {
                this.files.splice(this.openedFileIndex, 1);
            }
            if (this.editorOpenedFromPlans && canceled) {
                this.files.splice(this.openedFileIndex, 1);
            }

            this.editorOpenedFromPlans = false;
            this.openedFileIndex = null;
            this.showEditor = false;
        }
    },
    mounted() {
    }
});

