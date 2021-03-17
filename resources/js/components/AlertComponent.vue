<template id="alert-template">
	<div style="position: fixed; z-index: 2000; padding: 0 20px; left: 0; right: 0; top: 20px">
		<transition name="fadeLeft">
			<div v-if="shown" class="alert" :class="'alert-' + type" role="alert" style="max-width: 500px; margin-left:auto;"> 
				<button @click="close" class="close"><span aria-hidden="true">&times;</span></button>
				<h4 class="alert-heading">{{ type == 'danger' ? 'Error' : type == 'success' ? 'Genial' : 'Atención' }}</h4>
				<ul>
					<li v-for="(message, index) in messages" :key="index">{{ message }}</li>
				</ul>
			</div>
		</transition>
	</div>
</template>

<script>
	export default {
		template: '#alert-template',
		data: () => ({
			type: 'danger',
			messages: [],
			shown: false
		}),
		methods: {
			close: function(){
				this.shown = false
			}
		},
		created: function(){
			var t = this;
			VueEvent.$on('alert', function(data){
				t.shown = true;
				t.type = data.type;
				t.messages = data.messages;
				setTimeout(function(){
					t.shown = false;
				}, 3000);
			})
		}
	}
</script>