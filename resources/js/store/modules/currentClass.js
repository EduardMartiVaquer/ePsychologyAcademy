const state = {
    id: null,
    class: null,
    from: null,
    to: null,
};
const getters = {
    
};
const actions = {
    
};
const mutations = {
    setClass(state, classEvent){
        state.class = classEvent;
        state.id = classEvent.id
    },
    addClassFile(state, file){
        state.class.files.unshift(file);
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}