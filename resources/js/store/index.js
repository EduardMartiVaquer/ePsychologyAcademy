import Vue from "vue"
import Vuex from "vuex"

Vue.use(Vuex);

import currentClass from "./modules/currentClass";
import currentCourse from "./modules/currentCourse";
import currentSubject from "./modules/currentSubject";
import currentUser from "./modules/currentUser";

export default new Vuex.Store({
    modules: {
        currentClass,
        currentUser,
        currentCourse,
        currentSubject,
    }
})