<template id="avatar-component-template">
	<div class="avatar avatar-rounded avatar-filled pointer" :class="'avatar-' + width" :style="avatar != null ? ('background-image: url(' + avatar + ')') : ''">
        <div v-if="avatar == null" class="d-flex align-items-center justify-content-center" style="height: inherit">
            <p class="h6 m-0 color-white" style="line-height: 1">{{ (name != null && name.length > 0 ? name.substr(0, 1).toUpperCase() : '') + (lastname != null ? lastname.substr(0, 1).toUpperCase() : (name != null && name.length > 1 ? name.substr(1, 1).toUpperCase() : '')) }}</p>
        </div>
    </div>
</template>

<script>
	export default {
		template: '#avatar-component-template',
		props: ['avatar', 'name', 'lastname', 'width'],
		data: function(){
			return {
				url: window.location.origin,
				trans: window.trans
			}
		},
		computed: {

		},
		methods: {

		},
		created(){
			var t = this;
			$(document).ready(function(){
			
			});
		}
    }
</script>