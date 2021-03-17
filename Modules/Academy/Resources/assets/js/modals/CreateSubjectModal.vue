<template id="create-subject-modal-template">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <p class="h3 modal-title">{{ currentSubject == null ? 'Crear' : 'Editar' }} asignatura</p>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; right: 20px">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <transition name="fade" mode="out-in">
                <div v-if="page == 0" v-bind:key="0">
                    <p class="h4 mb-3">Info. de la asignatura</p>
                    <p class="h6 m-0">Nombre</p>
                    <input type="text" class="form-control mb-3" placeholder="Nombre" v-model="name">
                    
                    <p class="h6">Descripción</p>
                    <textarea class="form-control" rows="5" placeholder="Descripción de la asignatura" v-model="description"></textarea>
                </div>
                <div v-if="page == 1" v-bind:key="1">
                    <p class="h4 mb-3">Profesores/as</p>
                    <p>Selecciona los profesores del curso que estarán a cargo de esta asignatura</p>
                    <div v-for="(teacher, index) in teachers" :key="index" class="d-flex align-items-center mb-2" style="max-width: 300px">
                        <avatar-component :avatar="teacher.avatar" :name="teacher.name" :lastName="null" :width="40"></avatar-component>
                        <div class="ml-3">
                            <p class="h5 m-0">{{ teacher.name }}</p>
                        </div>
                        <div class="form-group form-check mb-0 ml-auto mr-0">
                            <input type="checkbox" class="form-check-input pointer" v-model="teacher.selected">
                            <label class="form-check-label" for="exampleCheck1">Elegir como profesor/a</label>
                        </div>
                    </div>
                </div>
                <div v-if="page == 2" v-bind:key="2">
                    <p class="h4 mb-3">Alumnos/as</p>
                    <p>Selecciona los alumnos del curso que podrán acceder a esta asignatura</p>
                    <div v-for="(student, index) in students" :key="index" class="d-flex align-items-center mb-2" style="max-width: 300px">
                        <avatar-component :avatar="student.avatar" :name="student.name" :lastName="null" :width="40"></avatar-component>
                        <div class="ml-3">
                            <p class="h5 m-0">{{ student.name }}</p>
                        </div>
                        <div class="form-group form-check mb-0 ml-auto mr-0">
                            <input type="checkbox" class="form-check-input pointer" v-model="student.selected">
                            <label class="form-check-label" for="exampleCheck1">Elegir como alumno/a</label>
                        </div>
                    </div>
                </div>
            </transition>
            
        </div>
        <div class="modal-footer">
            <div class="d-flex w-100">
                <button v-if="page > 0" class="btn btn-default ml-0" v-on:click="previous()"><i class="fa fa-angle-left"></i> Anterior</button>
                <button v-if="page < 2" class="btn btn-primary ml-auto mr-0" v-on:click="next()">Siguiente <i class="fa fa-angle-right"></i></button>
                <button v-if="page == 2" class="btn btn-primary ml-auto mr-0" v-on:click="save()" v-bind:disabled="loading">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="loading"></span>
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>

</template>

<script>
export default {
    props: [],
    template: "#create-subject-modal-template",
    data(){
        return {
            page: 0,
            name: '',
            description: '',
            teachers: [],
            students: [],
            loading: false
        }
    },
    computed: {
        user(){
            return this.$store.state.currentUser.user;
        },
        currentCourse(){
            return this.$store.state.currentCourse.course;
        },
        currentSubject(){
            return this.$store.state.currentSubject.subject;
        },
        isTeacher(){
            return this.teachers.some(teacher => {
                return teacher.id == this.user.id && teacher.selected
            })
        }
    },
    methods: {
        previous(){this.page -= 1},
        next(){this.page += 1},
        save(){
            if(!this.loading){
                this.loading = true
                var selectedTeachers = [];
                this.teachers.forEach(teacher => {
                    if(teacher.selected){
                        selectedTeachers.push(teacher.id);
                    }
                });
                var selectedStudents = []
                this.students.forEach(student => {
                    if(student.selected){
                        selectedStudents.push(student.id);
                    }
                });
                axios.post(window.location.origin + '/save_subject', {
                    course: this.currentCourse.id,
                    subject: this.currentSubject == null ? null : this.currentSubject.id,
                    name: this.name,
                    description: this.description,
                    teachers: selectedTeachers.join(";"),
                    students: selectedStudents.join(";"),
                })
                .then((response) => {
                    console.log(response);
                    if(this.currentSubject == null){
                        this.$store.state.currentCourse.course.subjects.unshift(response.data.subject);
                        if(this.isTeacher){
                            this.$store.state.currentUser.user.subjects.unshift(response.data.subject);
                        }
                        VueEvent.$emit('selectSubject', response.data.subject.id)
                    } else {
                        this.$store.state.currentSubject.subject = response.data.subject;
                        for(var s in this.$store.state.currentCourse.course.subjects){
                            if(this.$store.state.currentCourse.course.subjects[s].id == response.data.subject.id){
                                this.$store.state.currentCourse.course.subjects[s] = response.data.subject;
                                break;
                            }
                        }
                        for(var s in this.$store.state.currentUser.user.subjects){
                            if(this.$store.state.currentUser.user.subjects[s].id == response.data.subject.id){
                                this.$store.state.currentUser.user.subjects[s] = response.data.subject;
                                break;
                            }
                        }
                    }
                    this.loading = false
                    $('#modal').modal('hide');
                })
                .catch((error) => {
                    console.log(error);
                    this.loading = false
                    VueEvent.$emit('alert', {
                        type: 'danger',
                        messages: [this.trans.messages.error.error_1]
                    });
                })
            }
        }
    },
    created(){
        this.currentCourse.teachers.forEach(teacher => {
            var teacher = _.clone(teacher);
            teacher.selected = teacher.id == this.user.id;
            this.teachers.push(teacher);
        })
        this.currentCourse.students.forEach(student => {
            var student = _.clone(student);
            student.selected = true;
            this.students.push(student);
        })
    }
}
</script>