const state = {
    user: user,
    notifications: []
};
const getters = {
    readNotifications: state => {
        return state.notifications.filter(notification => notification.read_at != null)
    },
    unreadNotifications: state => {
        return state.notifications.filter(notification => notification.read_at == null)
    }
};
const actions = {
    getNotifications({ commit }){
        axios.post('/get_notifications')
        .then((response) => {
            commit("setNotifications", response.data);
        })
    },
    checkNotification({ commit, state }, index){
        commit("markNotification", index);
        axios.post('/mark_notification', {
            id: state.notifications[index].id
        })
    },
    checkAllNotifications({ commit }, mark){
        commit("markAllNotifications", mark);
        axios.post('/mark_all_notifications', {
            mark: mark
        })
        .then((response) => {
            dispatch('getNotifications');
        })
    }
};
const mutations = {
    setNotifications(state, notifications){
        state.notifications = notifications
    },
    markNotification(state, index){
        state.notifications[index].read_at = moment().format('YYYY-MM-DD HH:mm:ss');
    },
    markAllNotifications(state, mark){
        state.notifications.forEach((notification, index, array) => {
            notification.read_at = mark ? moment().format('YYYY-MM-DD HH:mm:ss') : null;
        });
    },
    setPrivacy(state, data){
        state.user.push_notifications = data.pushNotifications ? 1 : 0;
        state.user.email_notifications = data.emailNotifications ? 1 : 0;
        state.user.sms_notifications = data.smsNotifications ? 1 : 0;
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}