<template id="notifications-template">
	<div class="container app-container">
		<p class="h1 mt-3">{{ trans.messages.navbar.notifications }}</p>
		<p class="my-2" v-if="notifications.length > 0"><a class="a-normal" @click="markAllNotifications(true)">{{ trans.messages.profile.all_read }}</a></p>
		<table class="table table-hover">
			<thead>
				<tr>
					<th scope="col">{{ trans.messages.general.notification }}</th>
					<th scope="col">{{ trans.messages.general.date }}</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(notification, index) in notifications" :key="index" @click="markNotification(index)" class="pointer" :class="notification.read_at == null ? 'table-primary' : ''">
					<td>{{ notification.data.title }}</td>
					<td>{{ prettifyDate(notification.created_at) }}</td>
				</tr>
				
				<tr v-if="notifications.length == 0">
					<td colspan="2" class="text-center color-gray-3">{{ trans.messages.profile.no_notifications }}</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
	export default {
		template: '#notifications-template',
		computed: {
			notifications(){
				return this.$store.state.currentUser.notifications;
			}
		},
		methods: {
			prettifyDate(dateString){
				moment.locale(window.lang);
				var m = moment(dateString);
				var s = m.format('dddd DD') + ' de ' + moment(dateString).format('MMMM') + ' ' + moment(dateString).format('HH:mm');
				s = s.substr(0, 1).toUpperCase() + s.substr(1);
				return s;
			},
			markNotification(index){
				this.$store.dispatch('currentUser/checkNotification', index);	
			},
			markAllNotifications(mark){
				this.$store.dispatch('currentUser/checkAllNotifications', mark);
			},
		}
    }
</script>