import Router from 'vue-router'
import Vue from 'vue'
Vue.use(Router)

import AcademyRoutes from '../../Modules/Academy/Resources/assets/js/router'
import ProfileRoutes from '../../Modules/Profile/Resources/assets/js/router'

const defaultRoutes = [
    {path: '/', name: 'index', component: require('./views/Index.vue').default},
    {path: '/es', name: 'index_es', component: require('./views/Index.vue').default},
    {path: '/en', name: 'index_en', component: require('./views/Index.vue').default},
    {path: '/ca', name: 'index_ca', component: require('./views/Index.vue').default},
    {path: '/:lang?/terms', name: 'terms', component: require('./views/Terms.vue').default},
    {path: '/:lang?/privacy', name: 'privacy', component: require('./views/Privacy.vue').default},
    {path: '/:lang?/cookies', name: 'cookies', component: require('./views/Cookies.vue').default},
    {path: '/:lang?/suggestions', name: 'suggestions', component: require('./views/Suggestions.vue').default},
    {path: '/:lang?/contact', name: 'contact', component: require('./views/Contact.vue').default},
    {path: '/:lang?/about', name: 'about', component: require('./views/About.vue').default},
    {path: '/:lang?/faqs', name: 'FAQs', component: require('./views/Faqs.vue').default},
    {path: '/:lang?/test', name: 'Test', component: require('./views/Test2.vue').default},
]

let routes = []
routes = routes.concat(
    defaultRoutes,
    AcademyRoutes,
    ProfileRoutes
)

export default new Router({
    routes,
    linkActiveClass: '',
    mode: 'history',
    scrollBehavior (to, from, savedPosition) {
        if(to.name == "search" && savedPosition){
            return savedPosition;
        } else {
            return { x: 0, y: 0 };
        }
    }
});