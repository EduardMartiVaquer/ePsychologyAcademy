const state = {
    id: null,
    subject: null
};
const getters = {
    
};
const actions = {
    
};
const mutations = {
    setSubject(state, subject){
        state.id = subject.id;
        state.subject = subject;
    },
    addSubjectFile(state, file){
        state.subject.files.unshift(file);
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}