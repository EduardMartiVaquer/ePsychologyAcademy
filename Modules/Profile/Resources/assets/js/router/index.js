const routes = [
    {path: '/:lang?/login', name: 'login', component: require('../views/LogIn.vue').default},
    {path: '/:lang?/register', name: 'register', component: require('../views/Register.vue').default},
    {path: '/:lang?/profile', name: 'profile', component: require('../views/Profile.vue').default},
    {path: '/:lang?/notifications', name: 'notifications', component: require('../views/Notifications.vue').default},
    {path: '/:lang?/password/email', name: 'authEmail', component: require('../views/AuthEmail.vue').default},
    {path: '/:lang?/password/reset', name: 'authReset', component: require('../views/AuthReset.vue').default},
]

export default routes