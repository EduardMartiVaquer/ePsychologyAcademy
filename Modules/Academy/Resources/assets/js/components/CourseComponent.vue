<template id="course-component-template">
<div class="flex-grow-1 p-3" v-if="currentCourse != null">
    <p class="h1 m-0">{{ currentCourse.name }}</p>
    <div class="row no-gutters pt-3">
        <div class="col-12 col-md-6 col-lg-7 px-3 pt-3 mb-md-0">
            <div class="d-flex">
                <FullCalendar :options="calendarOptions" ref="fullCalendar" class="align-self-center w-100"/>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-5 px-3 pt-3">
            <div class="card card-body shadow mb-3">
                <button class="btn btn-primary" v-on:click="openTest()">Start Test</button>
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
                    <div class="mr-1 mb-2 d-flex flex-column align-items-center" style="width: 120px" v-for="(file, index) in currentCourse.files" :key="index">
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
import FullCalendar from '@fullcalendar/vue';
import momentPlugin from '@fullcalendar/moment';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import bootstrapPlugin from '@fullcalendar/bootstrap';
import allLocales from '@fullcalendar/core/locales-all';
import interactionPlugin from '@fullcalendar/interaction';
export default {
    props: [],
    template: "#course-component-template",
    data(){
        return {
            filePercentage: 0,
            savingFile: false
        }
    },
    components: {
        FullCalendar
    },
    computed: {
        user(){
            return this.$store.state.currentUser.user;
        },
        currentCourse(){
            return this.$store.state.currentCourse.course;
        },
        isAdmin(){
            return this.user.type == 0;
        },
        isTeacher(){
            var isTeacher = false;
            this.currentCourse.teachers.forEach((teacher) => {
                if(teacher.id == this.user.id){
                    isTeacher = true;
                }
            })
            return isTeacher;
        },
        calendarOptions(){
            return {
                plugins: [
                    momentPlugin,
                    dayGridPlugin,
                    timeGridPlugin,
                    bootstrapPlugin,
                    interactionPlugin,
                ],
                headerToolbar: {
                    left: 'dayGridMonth,timeGridWeek,timeGridDay',
                    center: 'title',
                    right: 'prev,next,today'
                },
                buttonText: {
                    today: trans.calendar.today,
                    month: trans.calendar.month,
                    week: trans.calendar.week,
                    day: trans.calendar.day,
                    list: trans.calendar.list
                },
                initialView: 'dayGridMonth',
                slotLabelFormat: 'HH:mm',
                themeSystem: 'bootstrap',
                allDaySlot: false,
                firstDay: 1,
                height: 600,
                aspectRatio: 1.8,
                locales: allLocales,
                locale: window.lang,
                selectable: true,
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false},
                events: (fetchInfo, successCallback, failureCallback) => {
                    axios.post(window.location.origin + '/get_calendar_events/' + moment.tz.guess().replace('/', 'xxx'), {
                        start: fetchInfo.start.valueOf(),
                        end: fetchInfo.end.valueOf()
                    })
                    .then((response) => {
                        successCallback(response.data)
                    })
                    .catch((error) => {
                        failureCallback(error);
                    })
                },
                dateClick: (info) => {
                    if(info.view.type == "dayGridMonth"){
                        this.$refs.fullCalendar.getApi().gotoDate(info.date);
                        this.$refs.fullCalendar.getApi().changeView("timeGridWeek", info.date);
                    }
                },
                select: (info) => {
                    if(info.view.type == "timeGridWeek"){
                        if(this.isAdmin || this.isTeacher){
                            this.$store.state.currentSubject.id = null;
                            this.$store.state.currentSubject.subject = null;
                            this.$store.state.currentClass.from = moment(info.startStr);
                            this.$store.state.currentClass.to = moment(info.endStr);
                            VueEvent.$emit('changeModal', 'new-class-modal');
                        }
                    }
                },
                eventClick: (info) => {
                    var index = this.$store.state.currentUser.user.classes.findIndex(c => {
                        return c.id == info.event.id
                    });
                    if(index !== -1){
                        this.$store.state.currentClass.id = _.clone(this.$store.state.currentUser.user.classes[index].id);
                        this.$store.state.currentClass.class = _.clone(this.$store.state.currentUser.user.classes[index]);
                        VueEvent.$emit('changeView', `/class/${this.$store.state.currentClass.id}`);
                    }
                }
            }
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
                formData.append('course', this.currentCourse.id);
                formData.append('file', document.getElementById('file-input').files[0]);
                formData.append('name', name);
                formData.append('type', type);
                formData.append('extension', extension);
                
                axios.post(window.location.origin + '/upload_course_file', formData, {
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
                    this.$store.commit('currentCourse/addCourseFile', response.data.file);
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
        openTest(){
            VueEvent.$emit('changeView', '/test');
        }
    },
    created(){
        VueEvent.$on('refetchCourseCalendarSessions', () => {
            this.$refs.fullCalendar.getApi().removeAllEvents();
            this.$refs.fullCalendar.getApi().refetchEvents();
        });
    }
}
</script>