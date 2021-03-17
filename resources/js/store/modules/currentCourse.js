const state = {
    course: null
};
const getters = {
    
};
const actions = {
    
};
const mutations = {
    setCourse(state, course){
        state.course = course
    },
    addCourseFile(state, file){
        state.course.files.unshift(file);
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}