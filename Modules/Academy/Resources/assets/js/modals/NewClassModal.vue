<template id="new-class-modal-template">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <p class="h3 modal-title">Nueva clase</p>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; right: 20px">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <p class="h6 m-0">Asignatura (Ocpional)</p>
            <select class="form-control mb-3" v-model="subject">
                <option :value="null">Ninguna</option>
                <option v-for="(subject, index) in currentCourse.subjects" :key="index" :value="subject.id">{{ subject.name }}</option>
            </select>

            <p class="h6 m-0">Título (Opcional)</p>
            <input class="form-control mb-3" placeholder="Título" v-model="title">

            <p class="h6 m-0">Descripción / resumen (Opcional)</p>
            <textarea class="form-control mb-3" rows="4" placeholder="Puedes añadir una descripción, apuntes o un resumen para los estudiantes"></textarea>

            <div class="d-flex flex-wrap">
                <div class="mr-1">
                    <p class="h6 m-0">Fecha</p>
                    <input id="entry-next-from-input" class="form-control mb-1" v-model="from" placeholder="dd-mm-yyyy" style="width: 250px" autocomplete="off">
                </div>
                <div class="mr-1">
                    <p class="h6 m-0">Desde</p>
                    <input id="entry-next-from-time-input" class="form-control" v-model="from_time" placeholder="hh:mm" style="width: 80px;" autocomplete="off">
                </div>
                <div>
                    <p class="h6 m-0">Hasta</p>
                    <input id="entry-next-to-time-input" class="form-control" v-model="to_time" placeholder="hh:mm" style="width: 80px;" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-dismiss="modal" aria-label="Close">
                Cancelar
            </button>
            <button class="btn btn-primary" v-on:click="save()" v-bind:disabled="loading">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="loading"></span>
                Guardar
            </button>
        </div>
    </div>
</div>

</template>

<script>
export default {
    props: [],
    template: "#new-class-modal-template",
    data(){
        return {
            subject: null,
            title: '',
            description: '',
            from: '',
            from_time: '',
            to_time: '',
            loading: false
        }
    },
    computed: {
        currentCourse(){
            return this.$store.state.currentCourse.course;
        }
    },
    methods: {
        setupDateTimePickers(){
            $('#entry-next-from-input').datepicker({
                date: this.from != null ? this.from : moment().format('DD-MM-YYYY'),
                format: 'dd-mm-yyyy',
                startView: 0,
                weekStart: 1,
                months: [this.trans.messages.calendar.january, this.trans.messages.calendar.february, this.trans.messages.calendar.march, this.trans.messages.calendar.april, this.trans.messages.calendar.may, this.trans.messages.calendar.june, this.trans.messages.calendar.july, this.trans.messages.calendar.august, this.trans.messages.calendar.september, this.trans.messages.calendar.october, this.trans.messages.calendar.november, this.trans.messages.calendar.december],
                monthsShort: [this.trans.messages.calendar.jan, this.trans.messages.calendar.feb, this.trans.messages.calendar.mar, this.trans.messages.calendar.apr, this.trans.messages.calendar.my, this.trans.messages.calendar.jun, this.trans.messages.calendar.jul, this.trans.messages.calendar.aug, this.trans.messages.calendar.sep, this.trans.messages.calendar.oct, this.trans.messages.calendar.nov, this.trans.messages.calendar.dec],
                days: [this.trans.messages.calendar.sunday, this.trans.messages.calendar.monday, this.trans.messages.calendar.tuesday, this.trans.messages.calendar.wednesday, this.trans.messages.calendar.thursday, this.trans.messages.calendar.friday, this.trans.messages.calendar.saturday],
                daysShort: [this.trans.messages.calendar.sun, this.trans.messages.calendar.mon, this.trans.messages.calendar.tues, this.trans.messages.calendar.wed, this.trans.messages.calendar.thu, this.trans.messages.calendar.fri, this.trans.messages.calendar.sat],
                daysMin: [this.trans.messages.calendar.sun, this.trans.messages.calendar.mon, this.trans.messages.calendar.tues, this.trans.messages.calendar.wed, this.trans.messages.calendar.thu, this.trans.messages.calendar.fri, this.trans.messages.calendar.sat],
            });
            $('#entry-next-from-input').on('pick.datepicker', (e) => {
                this.from = moment(e.date).format('DD-MM-YYYY');
                if(e.view == "day"){
                    $('#entry-next-from-input').datepicker('hide');
                }
            });
            $('#entry-next-from-time-input').timepicker({
                'scrollDefault': 'now',
                'timeFormat': 'H:i',
                'step': 10,
                'forceRoundTime': false
            });
            $('#entry-next-from-time-input').on('change', (e) => {
                this.from_time = $('#entry-next-from-time-input').val();
            });
            $('#entry-next-to-time-input').timepicker({
                'scrollDefault': 'now',
                'timeFormat': 'H:i',
                'step': 10,
                'forceRoundTime': false
            });
            $('#entry-next-to-time-input').on('change', (e) => {
                this.to_time = $('#entry-next-to-time-input').val();
            });
        },
        save(){
            if(!this.loading){
                this.loading = true
                axios.post(window.location.origin + '/save_new_class', {
                    course: this.currentCourse.id,
                    subject: this.subject,
                    title: this.title.replace(/\s/g, '') == '' ? null : this.title,
                    description: this.description.replace(/\s/g, '') == '' ? null : this.description,
                    from: moment(`${this.from} ${this.from_time}`, 'DD-MM-YYYY HH:mm').format('YYYY-MM-DD HH:mm:ss'),
                    to: moment(`${this.from} ${this.to_time}`, 'DD-MM-YYYY HH:mm').format('YYYY-MM-DD HH:mm:ss'),
                    timezone: moment.tz.guess()
                })
                .then((response) => {
                    if(response.data.add_class){
                        this.$store.state.currentUser.user.classes.push(response.data.class_event);
                        this.$store.state.currentCourse.course.classes.push(response.data.class_event);
                        if(this.subject != null){
                            var index = this.$store.state.currentCourse.course.subjects.findIndex(s => {
                                return s.id == this.subject;
                            })
                            this.$store.state.currentCourse.course.subjects[index].classes.push(response.data.class_event);
                            if(this.$store.state.currentSubject.subject.id == this.subject){
                                this.$store.state.currentSubject.subject.classes.push(response.data.class_event)
                            }
                        }
                    }
                    VueEvent.$emit('refetchCourseCalendarSessions');
                    this.loading = false
                    $('#modal').modal('hide');
                })
                .catch((error) => {
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
        if(this.$store.state.currentSubject.subject != null){
            this.subject = this.$store.state.currentSubject.subject.id;
        }
        if(this.$store.state.currentClass.from != null){
            this.from = this.$store.state.currentClass.from.format('DD-MM-YYYY')
            this.from_time = this.$store.state.currentClass.from.format('HH:mm')
        }
        if(this.$store.state.currentClass.to != null){
            this.to_time = this.$store.state.currentClass.to.format('HH:mm')
        }
        $(document).ready(() => {
            this.setupDateTimePickers();
        });
    }
}
</script>