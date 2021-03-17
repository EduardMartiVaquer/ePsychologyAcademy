<template id="course-profile-component-template">
<div class="flex-grow-1 p-3" v-if="currentCourse != null">
    <div class="card shadow">
        
    </div>
</div>
</template>

<script>
export default {
    template: "#course-profile-component-template",
    data(){
        return {
            
        }
    },
    computed: {
        user(){
            return this.$store.state.currentUser.user;
        },
        currentCourse(){
            return this.$store.state.currentCourse.course;
        }
    },
    methods: {
        
    },
    created(){
        $(document).ready(() => {
            
        });
    }
}
</script>