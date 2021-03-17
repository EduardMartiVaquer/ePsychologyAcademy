<template id="subject-component">
<div class="flex-grow-1 p-3">
    <p class="h1 m-0">{{ currentSubject.name }}</p>
    <div class="row no-gutters pt-3">
        <div class="col-12 col-md-6 col-lg-7 px-3 pt-3 mb-md-0">
            <div class="card card-body shadow">
                <div class="d-flex">
                    <div class="btn-group">
                        <button type="button" class="btn" :class="tableType == 1 ? 'btn-primary' : 'btn-outline-primary'" @click="tableType = 1">Clases realizadas</button>
                        <button type="button" class="btn" :class="tableType == 0 ? 'btn-primary' : 'btn-outline-primary'" @click="tableType = 0">Próximas clases</button>
                    </div>
                    <button class="btn btn-primary ml-auto mr-0" v-if="isAdmin || isTeacher" @click="addClass()">Añadir clase</button>
                </div>

                <div class="table-responsive mt-3" v-if="computedClasses.length > 0">
                    <table class="table">
                        <tbody>
                            <tr v-for="(classEvent, index) in computedClasses" :key="index" class="pointer">
                                <td>
                                    <a class="a-normal m-0" @click="openClass(index)">{{ 'Clase de ' + classDate(classEvent) }}</a>
                                </td>
                                <td>
                                    <a class="a-normal m-0" @click="openClassFiles(index)">Ver archivos</a>
                                </td>
                                <td>
                                    <a class="a-normal m-0" @click="openClassRoom(index)">Sala de videollamada</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="h4 my-3 color-gray-7 text-center" v-if="computedClasses.length == 0">No hay clases en la lista</p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-5 px-3 pt-3">
            <div class="card card-body shadow">
                <div class="d-flex mb-2">
                    <p class="h4 m-0">Archivos</p>
                    <button v-if="isTeacher || isAdmin" class="btn btn-primary ml-auto" onclick="document.getElementById('file-input').click();">+ Añadir archivo</button>
                    <input id="file-input" type="file" name="files[]" class="d-none" @change="submitFile">
                </div>
                <div class="progress mb-1" v-if="savingFile">
                    <div class="progress-bar active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;" :style="savingFile ? 'width: ' + filePercentage + '%' : 'width: 0%'">
                        {{ filePercentage == 100 ? 'Preparando..' : filePercentage + '%' }}
                    </div>
                </div>
                <div class="d-flex flex-wrap">
                    <div class="mr-1 mb-2 d-flex flex-column align-items-center" style="width: 120px" v-for="(file, index) in currentSubject.files" :key="index">
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
</template>
<script>
export default {
    template: "#subject-component",
    data(){
        return {
            filePercentage: 0,
            savingFile: false,
            tableType: 0
        }
    },
    computed: {
        user(){
            return this.$store.state.currentUser.user;
        },
        currentSubject(){
            return this.$store.state.currentSubject.subject
        },
        isAdmin(){
            return this.user.type == 0;
        },
        isTeacher(){
            var isTeacher = this.currentSubject.teachers.some(teacher => {
                return teacher.id == this.user.id
            })
            return isTeacher;
        },
        computedClasses(){
            return this.user.classes.filter(c => {
                return this.tableType == 0 ? moment.utc(c.to).isAfter(moment()) : moment.utc(c.to).isBefore(moment());
            }).sort((a, b) => {
                return this.tableType == 0 ? moment.utc(a.from).isAfter(moment()) : moment.utc(a.from).isBefore(moment());
            })
        }
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
                formData.append('subject', this.currentSubject.id);
                formData.append('file', document.getElementById('file-input').files[0]);
                formData.append('name', name);
                formData.append('type', type);
                formData.append('extension', extension);
                
                axios.post(window.location.origin + '/upload_subject_file', formData, {
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
                    this.$store.commit('currentSubject/addSubjectFile', response.data.file)
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
        openClass(index){
            this.$store.state.currentClass.id = _.clone(this.computedClasses[index].id);
            this.$store.state.currentClass.class = _.clone(this.computedClasses[index]);
            VueEvent.$emit('changeView', `/class/${this.$store.state.currentClass.id}`);
        },
        openClassFiles(index){
            this.$store.state.currentClass.id = _.clone(this.computedClasses[index].id);
            this.$store.state.currentClass.class = _.clone(this.computedClasses[index]);
            VueEvent.$emit('changeModal', 'class-files-modal')
        },
        openClassRoom(index){
            this.$store.state.currentClass.id = _.clone(this.computedClasses[index].id);
            this.$store.state.currentClass.class = _.clone(this.computedClasses[index]);
            VueEvent.$emit('changeView', `/class/${this.$store.state.currentClass.id}/call`);
        },
        classDate(classEvent){
            return moment.utc(classEvent.from).local().format('dddd DD MMM HH:mm') + ' a ' + moment.utc(classEvent.to).local().format('HH:mm');
        },
        addClass(){
            this.$store.state.currentClass.from = null;
            this.$store.state.currentClass.to = null;
            return VueEvent.$emit('changeModal', 'new-class-modal');
        }
    },
    created(){
        
    }
}
</script>