<template id="index-template">
<div>
    <transition name="fade" mode="out-in">
        <component v-bind:is="component"></component>
    </transition>
</div>
</template>

<script>
export default {
    template: "#index-template",
    data: () => ({
        
    }),
    computed: {
        user(){
            return this.$store.state.currentUser.user;
        },
        component(){
            return this.user == null ? 'index-component' : 'home-component';
        }
    },
    methods: {
        
    },
    created(){
        
    }
}
</script>