<template id="home-component-template">
<div>
    <div class="app-container pb-0 d-flex">
        <div class="sidebar" v-bind:class="opened ? 'sidebar-opened' : ''">
            <div class="w-100 d-flex flex-column" v-if="currentCourse != null">
                <p class="h3 d-none d-lg-block pointer" v-if="opened" @click="changeComponent('course-component')">{{ currentCourse.name }}</p>
                
                <div class="sidebar-item d-flex justify-content-center" @click="toggleSubjects()">
                    <p class="h5"><span class="d-none d-lg-inline-block" v-if="opened">Asignaturas</span></p>
                    <p class="h5"><span class="d-inline-block" v-bind:class="!opened ? '' : 'd-lg-none'" v-if="!opened"><i class="fa fa-couch"></i></span></p>
                    <p class="h5 sidebar-arrow ml-auto mr-0 d-none d-lg-inline-block" v-if="opened" :style="subjectsShown ? 'transform: rotate(-180deg)' : 'transform: rotate(0deg)'"><i class="fa fa-angle-down"></i></p>
                </div>

                <div class="collapse" id="subjects-collapse">
                    <div class="pl-3" v-if="isAdmin || isTeacher">
                        <div @click="createSubject()" class="sidebar-item d-flex">
                            <p class="h6"><span class="d-none d-lg-inline-block" v-if="opened"><a class="a-normal">+ Crear asignatura</a></span></p>
                        </div>
                    </div>
                    <div class="pl-3">
                        <div v-for="(subject, index) in currentCourse.subjects" :key="index" @click="selectSubject(index)" class="sidebar-item d-flex justify-content-center">
                            <p class="h6"><span class="d-none d-lg-inline-block" v-if="opened">{{ subject.name }}</span></p>
                            <p class="h6 sidebar-arrow ml-auto mr-0 d-none d-lg-inline-block" v-if="opened"><i class="fa fa-angle-right"></i></p>
                        </div>
                    </div>
                </div>

                <div v-if="isAdmin || isTeacher" class="sidebar-item d-flex justify-content-center" @click="changeComponent('course-profile-component')" >
                    <p class="h5"><span class="d-none d-lg-inline-block" v-if="opened">Perfil del curso</span></p>
                    <p class="h5"><span class="d-inline-block" :class="!opened ? '' : 'd-lg-none'" v-if="!opened"><i class="fa fa-home"></i></span></p>
                    <p class="h5 sidebar-arrow ml-auto mr-0 d-none d-lg-inline-block" v-if="opened"><i class="fa fa-angle-right"></i></p>
                </div>
            </div>
        </div>

        <transition name="fade" mode="out-in">
            <component v-bind:is="component"></component>
        </transition>
    </div>
    <!--div class="container pt-3">
        <h1>Curso actual</h1>
        <p class="h4 pl-3">{{ currentCourse.name }}</p>

        <h2 class="mt-5">Asignaturas:</h2>
        <p class="h5 pl-3" v-for="(subject, index) in currentCourse.subjects" :key="index">• {{ subject.name }}</p>

        <h2 class="mt-5">Próximas clases:</h2>
        <p class="h5 pl-3" v-for="(userClass, index) in userClasses" :key="index">• {{ formatDate(userClass.from, 'dddd DD MMM HH:mm') }}</p>
    </div-->
</div>
</template>

<script>
export default {
    props: [],
    template: "#home-component-template",
    data(){
        return {
            component: 'course-component',
            opened: true,
            subjectsShown: false
        }
    },
    computed: {
        user(){
            return this.$store.state.currentUser.user;
        },
        currentCourse(){
            return this.user.courses[0];
        },
        currentSubject(){
            return this.$store.state.currentSubject.subject;
        },
        isAdmin(){
            return this.user.type == 0;
        },
        isTeacher(){
            var isTeacher = false;
            if(this.currentCourse != null){
                this.currentCourse.teachers.forEach((teacher) => {
                    if(teacher.id == this.user.id){
                        isTeacher = true;
                    }
                });
            }
            return isTeacher;
        },
        userClasses(){
            return this.user.classes;
        }
    },
    methods: {
        formatDate(d, f){
            return this.$root.formatDate(d, f);
        },
        toggleSubjects(){
            $('#subjects-collapse').collapse(this.subjectsShown ? 'hide' : 'show')
            this.subjectsShown = !this.subjectsShown
        },
        changeComponent(component){
            this.component = component
        },
        selectSubject(index){
            this.$store.commit('currentSubject/setSubject', this.currentCourse.subjects[index]);
            window.history.pushState('page', document.title, '/?subject=' + this.currentSubject.name.toLowerCase().replace(/\s/g, '-'))
            if(this.component != 'subject-component'){
                this.component = 'subject-component';
            }
        },
        createSubject(){
            this.$store.state.currentSubject.id = null;
            this.$store.state.currentSubject.subject = null;
            VueEvent.$emit('changeModal', 'create-subject-modal');
        }
    },
    created(){
        VueEvent.$on('selectSubject', (id) => {
            var index = this.currentCourse.subjects.findIndex(subject => {
                return subject.id == id;
            })
            if(index !== -1){
                this.selectSubject(index);
            }
        });
        this.user.classes.forEach(classEvent => {
            if(typeof Echo.connector.channels[`presence-ClassCallChannel.${classEvent.id}`] != "undefined"){
                Echo.leave(`ClassCallChannel.${classEvent.id}`);
            }
        })
        $(document).ready(() => {
            if(this.$route.query.subject){
                var index = this.currentCourse.subjects.findIndex(subject => {
                    return subject.name.toLowerCase().replace(/\s/g, '-') == this.$route.query.subject;
                })
                if(index !== -1){
                    this.selectSubject(index);
                } else {
                    window.history.pushState('page', document.title, '/');
                }
            }    
        });
    }
}
</script>