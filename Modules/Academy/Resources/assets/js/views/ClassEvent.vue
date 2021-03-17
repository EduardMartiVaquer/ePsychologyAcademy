<template id="class-event-template">
<div>
    <div v-if="loading" class="app-container d-flex justify-content-center align-items-center" style="height: calc(100vh - 52px)">
        <p class="h1 m-0"><span class="spinner-border" role="status" aria-hidden="true"></span></p>
    </div>
    <div v-if="!loading">
        <div v-if="!error && currentClass != null" class="app-container">
            <div class="d-flex pt-4">
                <div class="d-none d-sm-block pl-4 pt-2" style="width: 150px">
                    <button class="big-back-button" @click="back()"><i class="fa fa-chevron-left"></i></button>
                </div>
                <div class="px-3 flex-grow-1">
                    <button class="big-back-button d-block d-sm-none mb-3" @click="back()"><i class="fa fa-chevron-left"></i></button>

                    <div class="row no-gutters">
                        <div class="col-12 col-md-6 col-lg-7 px-3 pt-3 mb-md-0">
                            <div class="card card-body shadow mb-3">
                                <div class="d-flex">
                                    <p class="h2 mb-3">{{ currentClass.name != null ? currentClass.name : `Clase de ${currentClassDate}` }}</p>
                                    <p class="ml-auto" v-if="isAdmin || isTeacher"><a class="a-normal" @click="edit()">Editar</a></p>
                                </div>
                                <div v-if="currentClass.description != null">
                                    <pre>{{ currentClass.description }}</pre>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-5 px-3 pt-3">
                            <div class="card card-body shadow mb-3">
                                <p class="h4">Entrar en la sala</p>
                                <button class="btn btn-primary btn-lg" v-on:click="openCall()">Entrar en la sala</button>
                            </div>

                            <div class="card card-body shadow">
                                <div class="d-flex mb-2">
                                    <p class="h4 m-0">Archivos</p>
                                    <button v-if="isTeacher || isAdmin" class="btn btn-primary ml-auto" onclick="document.getElementById('file-input').click();">+ Añadir archivo</button>
                                    <input id="file-input" type="file" name="files[]" class="d-none" @change="submitFile">
                                </div>
                                <div class="progress mb-1" v-if="savingFile">
                                    <div class="progress-bar active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;" v-bind:style="savingFile ? 'width: ' + filePercentage + '%' : 'width: 0%'">
                                        {{ filePercentage == 100 ? 'Preparando..' : filePercentage + '%' }}
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap">
                                    <div class="mr-1 mb-2 d-flex flex-column align-items-center" style="width: 120px" v-for="(file, index) in currentClass.files" :key="index">
                                        <div class="mini-document d-flex align-items-center justify-content-center pointer" :style="isBackgroundable(file.type) ? 'background-image: url(' + file.path + ')' : ''" @click="downloadFile(file)">
                                            <p class="h4 m-0" v-if="!isBackgroundable(file.type)">{{ file.extension.toUpperCase() }}</p>
                                        </div>
                                        <p class="h6 fw-300 text-center mt-1 mb-1">{{ file.name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="error || currentClass == null" class="app-container d-flex justify-content-center align-items-center" style="height: calc(100vh - 52px)">
            <p class="display-3 m-0 text-danger">Error</p>
        </div>
    </div>
</div>
</template>

<script>
export default {
    template: "#class-event-template",
    data(){
        return {
            loading: true,
            error: false,
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
        currentClassMembers(){
            return this.currentClass.users;
        },
        currentClassDate(){
            if(this.currentClass != null){
                return moment.utc(this.currentClass.from).local().format('dddd DD MMM HH:mm') + ' a ' +moment.utc(this.currentClass.to).local().format('HH:mm');
            }
            return null;
        },
        isAdmin(){
            return this.user.type == 0;
        },
        isTeacher(){
            var isTeacher = this.currentClass.users.some(u => {
                return u.id == this.user.id && u.type == 2;
            })
            return isTeacher;
        },
    },
    methods: {
        getClassEvent(){
            axios.post(window.location.origin + '/get_class_event', {
                classEvent: this.$route.params.id
            })
            .then((response) => {
                if(response.data.status == "OK"){
                    this.$store.state.currentClass.class = response.data.class;
                    this.$store.state.currentClass.id = response.data.class.id;
                } else {
                    this.error = true;
                    VueEvent.$emit('alert', {
                        type: 'danger',
                        messages: [this.trans.messages.error.error_1]
                    });
                }
                this.loading = false
            })
            .catch((error) => {
                this.error = true;
                this.loading = false;
                VueEvent.$emit('alert', {
                    type: 'danger',
                    messages: [this.trans.messages.error.error_1]
                });
            })
        },
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
                formData.append('file', document.getElementById('file-input').files[0]);
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
                    $('#file-input').val('')
                    this.filePercentage = 0;
                    this.savingFile = false;
                    this.$store.commit('currentClass/addClassFile', response.data.file)
                })
                .catch((error) => {
                    $('#file-input').val('')
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
        openCall(){
            VueEvent.$emit('changeView', `/class/${this.currentClass.id}/call`)
        },
        back(){
            app._router.back();
        },
        edit(){
            VueEvent.$emit('changeModal', 'new-class-modal-template')
        },
        getTypeFromId(id){
            var types = ["admin", "student", "teacher", "patient", "psychologist"];
            var member = this.currentClassMembers.find(m => {
                return m.id == id;
            })
            if(typeof member != "undefined"){
                return types[member.class_user.type]
            }
            return null;
        }
    },
    created(){
        $(document).ready(() => {
            this.getClassEvent();
        });
    }
}
</script>