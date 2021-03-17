require('./bootstrap');

//Main vue elements
import Vue from 'vue'
import router from './router.js';
import store from "./store"
window.VueEvent = new Vue();

//Lang
import Lang from 'lang.js'
Vue.prototype.trans = new Lang({
    messages: window.trans,
    locale: window.lang,
    fallback: window.fallback_locale
});

/* ----- COMPONENTS ----- */
//Basic
Vue.component('alert', require('./components/AlertComponent.vue').default);
Vue.component('avatar-component', require('./components/AvatarComponent.vue').default);

//Index
Vue.component('index-component', require('./components/IndexComponent.vue').default);
Vue.component('home-component', require('./components/HomeComponent.vue').default);

//Course
Vue.component('course-component', require('../../Modules/Academy/Resources/assets/js/components/CourseComponent.vue').default);
Vue.component('course-profile-component', require('../../Modules/Academy/Resources/assets/js/components/CourseProfileComponent.vue').default);

//Subject
Vue.component('subject-component', require('../../Modules/Academy/Resources/assets/js/components/SubjectComponent.vue').default);



/* ----- MODALS ----- */
//Basic
Vue.component('loading-modal', require('./modals/LoadingModal.vue').default);

//Course
Vue.component('create-subject-modal', require('../../Modules/Academy/Resources/assets/js/modals/CreateSubjectModal.vue').default);

//Class
Vue.component('class-files-modal', require('../../Modules/Academy/Resources/assets/js/modals/ClassFilesModal.vue').default);
Vue.component('new-class-modal', require('../../Modules/Academy/Resources/assets/js/modals/NewClassModal.vue').default);



//App
const app = new Vue({
    el: '#app',
    router,
    store,
    data(){
        return {
            modal: 'loading-modal',
            previousModal: null,
            OTsession: null,
            OTpublisher: null,
            OTscreenPublisher: null,
            OTstreams: [],
            OTsubscribers: [],
            OTvisible: false,
            OTcallOptions: {
                publishAudio: true,
                publishVideo: true,
                hasVideo: true,
                sender: null
            },
            OTapiKey: 46581412,
        }
    },
    computed: {
        user(){
            return this.$store.state.currentUser.user
        },
        navbar(){
            if(this.user == null && (this.$route.name == "index" || this.$route.name == "login" || this.$route.name == "register" || this.$route.name == "psychologist-register")){
                return {
                    brandUrl: this.url + (this.user != null && this.user.type > 2 ? '/images/company_logo_white.svg' : '/images/logo_white.svg?v=2.0'),
                    mainClass: 'navbar-dark',
                    background: this.navbarCollapsed ? 'linear-gradient(rgba(0, 0, 0, 0.3) , rgba(0, 0, 0, 0))' : 'linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.2))',
                    brandColor: '#ffffff',
                    linkColor: '#ffffff',
                    collapseColor: 'rgba(0, 0, 0, 0.0)',
                    badgeColor: this.$store.state.currentUser.notifications.length > 0 ? '#0071bd' : '#999999'
                }
            } else {
                return {
                    brandUrl: this.url + (this.user != null && this.user.type > 2 ? '/images/company_logo.svg' : '/images/logo.svg?v=2.0'),
                    mainClass: 'navbar-light',
                    background: '#ffffff',
                    brandColor: '#333333',
                    linkColor: '#333333',
                    collapseColor: 'rgba(0, 0, 0, 0.0)',
                    badgeColor: this.$store.state.currentUser.notifications.length > 0 ? '#0071bd' : '#999999'
                }
            }
        },
        readNotifications(){
            return this.$store.getters["currentUser/readNotifications"]
        },
        unreadNotifications(){
            return this.$store.getters["currentUser/unreadNotifications"]
        }
    },
    methods: {
        formatDate(d, f){
            return moment(d).format(f);
        },
        toggleNavbar(){
            $('#navbarRightContent').collapse('toggle');
        },
        startUserEcho(){
            Echo.private('App.Models.User.' + this.user.id)
            .notification(notification => {
                console.log(notification)
            })
        },
        logout(){
            window.location.href = window.location.origin + "/auth-logout";
        },
        endCall(){
            this.OTvisible = false;
            if(this.OTsession != null){
                this.OTsession.disconnect();
                this.OTsession = null;
            }
            if(this.OTpublisher != null){
                this.OTpublisher.destroy();
                this.OTpublisher = null;
            }
            this.OTstreams = [];
        }
    },
    created(){
        //Set main variables
        window.app = this;
        Vue.prototype.domain = domain;
        Vue.prototype.url = window.location.origin;

        //Router
        router.beforeEach((to, from, next) => {
            next();
            if(from.name == "classCall"){
                this.endCall();
            }
        });

        //User
        if(this.user != null){
            this.$store.dispatch('currentUser/getNotifications');
            this.$store.commit('currentCourse/setCourse', _.clone(this.user.courses[0]));
            this.startUserEcho();
        }

        //Helpers
        VueEvent.$on('changeModal', (modal) => {
            $('#modal').modal('show');
            if(this.modal != 'loading-modal'){
                this.previousModal = this.modal;
            }
            this.modal = modal;
        });
        VueEvent.$on('changeView', function(view){
            router.push(view);
        });

        //Navbar
        $('#navbarRightContent').on('show.bs.collapse', function () {
            this.navbarCollapsed = false;
        })
        $('#navbarRightContent').on('shown.bs.collapse', function () {
            this.navbarCollapsed = false;
        })
        $('#navbarRightContent').on('hide.bs.collapse', function () {
            this.navbarCollapsed = true;
        })
        $('#navbarRightContent').on('hidden.bs.collapse', function () {
            this.navbarCollapsed = true;
        })

        //Moment
        moment.locale(window.lang != undefined ? window.lang : 'es');

        $(document).ready(() => {
            $(window).on('resize', () => {
                if ($(window).width() >= 992) {
                    $('#navbarRightContent').collapse('hide');
                    this.navbarCollapsed = true;
                }
            });
        });
    }
})