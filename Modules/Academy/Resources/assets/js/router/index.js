const routes = [
    {path: '/:lang?/class/:id', name: 'classEvent', component: require('../views/ClassEvent.vue').default},
    {path: '/:lang?/class/:id/call', name: 'classCall', component: require('../views/ClassCall.vue').default},
]

export default routes