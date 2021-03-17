
<template id="profile-template">
	<div class="app-container pb-0 d-flex">
		<div class="sidebar sidebar-opened">
        	<div class="w-100 d-flex flex-column">
				<p class="h3 d-none d-lg-block">{{ trans.messages.profile.profile }}</p>
				
				<div id="profile-sidebar-first-option" class="sidebar-item d-flex justify-content-center" @click="changeComponent('profile-component')" >
					<p class="h5">
                        <span style="min-width: 20px"><i class="fas fa-user sidebar-icon-opened"></i></span> 
                        <span class="d-none d-lg-inline-block">{{ trans.messages.profile.edit_profile }}</span> 
                    </p>
					<p class="h5 sidebar-arrow ml-auto mr-0 d-none d-lg-inline-block"><i class="fa fa-angle-right"></i></p>
				</div>

				<div class="sidebar-item d-flex justify-content-center" @click="changeComponent('profile-security-component')">
					<p class="h5">
                        <span style="min-width: 20px"><i class="fa fa-lock sidebar-icon-opened"></i></span> 
                        <span class="d-none d-lg-inline-block">{{ trans.messages.profile.security_privacy }}</span>
                    </p>
					<p class="h5 sidebar-arrow ml-auto mr-0 d-none d-lg-inline-block"><i class="fa fa-angle-right"></i></p>
				</div>
			</div>
        </div>

		<transition name="fade" mode="out-in">
			<component :is="component"></component>
		</transition>
	</div>
</template>

<script>
    import ProfileComponent from '../components/ProfileComponent';
    import ProfileSecurityComponent from '../components/ProfileSecurityComponent';
	export default {
        components: {
            ProfileComponent,
            ProfileSecurityComponent
        },
		template: '#profile-template',
		data:() => ({
			page: 0,
			component: 'profile-component',
		}),
		computed: {
			user(){
				return this.$store.state.currentUser.user;
			}
		},
		methods: {
			changeComponent(component){
				this.component = component;
				window.history.pushState('page', document.title, '/profile?page=' + component.replace('-component', ''))
			}
		},
		created(){
			if(this.$route.query.page != undefined){
				this.changeComponent(this.$route.query.page + '-component')
			}
		}
    }
</script>