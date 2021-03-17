<template id="class-files-modal-template">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <p class="h3 modal-title">Archivos</p>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; right: 20px">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="progress mb-1" v-if="savingFile">
                <div class="progress-bar active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;" :style="savingFile ? 'width: ' + filePercentage + '%' : 'width: 0%'">
                    {{ filePercentage == 100 ? 'Preparando..' : filePercentage + '%' }}
                </div>
            </div>
            <p class="h4 color-gray-7 text-center my-3" v-if="currentClass.files.length == 0">No hay archivos</p>
            <div class="d-flex flex-wrap">
                <div class="mr-1 mb-2 d-flex flex-column align-items-center" style="width: 120px" v-for="(file, index) in currentClass.files" :key="index">
                    <div class="mini-document d-flex align-items-center justify-content-center pointer" :style="isBackgroundable(file.type) ? 'background-image: url(' + file.path + ')' : ''" @click="downloadFile(file)">
                        <p class="h4 m-0" v-if="!isBackgroundable(file.type)">{{ file.extension.toUpperCase() }}</p>
                    </div>
                    <p class="h6 fw-300 text-center mt-1 mb-1">{{ file.name }}</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input id="modal-file-input" type="file" name="files[]" class="d-none" @change="submitFile">
            <button class="btn btn-primary" v-if="isTeacher || isAdmin" onclick="document.getElementById('modal-file-input').click();">+ Añadir archivo</button>
        </div>
    </div>
</div>

</template>

<script>
export default {
    props: [],
    template: "#class-files-modal-template",
    data(){
        return {
            filePercentage: 0,
            savingFile: false,
        }
    },
    computed: {
        user(){
            return this.$store.state.currentUser.user;
        },
        currentClass(){
            return this.$store.state.currentClass.class;
        },
        isAdmin(){
            return this.user.type == 0;
        },
        isTeacher(){
            var isTeacher = this.currentClass.users.some(u => {
                return u.id == this.user.id && u.type == 2
            })
            return isTeacher;
        },
    },
    methods: {
        submitFile(e){
            if(e.target.files[0] != undefined){
                var file = e.target.files[0];
                var name = file.name;
                var extension = (/[.]/.exec(name)) ? /[^.]+$/.exec(name)[0] : null;
                if(extension != null && typeof extension == "string"){
                    this.uploadFile(name, file.type, extension);
                }
            }
        },
        uploadFile(name, type, extension){
            if(!this.savingFile){
                this.filePercentage = 0;
                this.savingFile = true;
                
                var formData = new FormData();
                formData.append('class', this.currentClass.id);
                formData.append('file', document.getElementById('modal-file-input').files[0]);
                formData.append('name', name);
                formData.append('type', type);
                formData.append('extension', extension);
                
                axios.post(window.location.origin + '/upload_class_file', formData, {
                    onUploadProgress: function (progressEvent) {
                        var percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        if(percentCompleted > this.filePercentage){
                            this.filePercentage = percentCompleted;
                        }
                    }
                })
                .then((response) => {
                    $('#modal-file-input').val('')
                    this.filePercentage = 0;
                    this.savingFile = false;
                    this.$store.commit('currentClass/addClassFile', response.data.file)
                })
                .catch((error) => {
                    console.log(error);
                    $('#modal-file-input').val('')
                    this.filePercentage = 0;
                    this.savingFile = false;
                    VueEvent.$emit('alert', {
                        type: 'danger',
                        messages: [this.trans.messages.error.error_1]
                    });
                });
            }
        },
        downloadFile(file){
            const link = document.createElement('a');
            link.href = file.path;
            link.setAttribute('download', name);
            link.setAttribute('class', 'd-none');
            document.body.appendChild(link);
            link.click();
            link.remove();
        },
        isBackgroundable(type){
            try {
                return type.split('/')[0] == "image";
            } catch (error) {
                return false
            }
            return false;
        },
    },
    created(){
        var t = this;
        $(document).ready(function(){
            
        });
    }
}
</script>