<template id="profile-security-component-template">
<div class="table-display" style="height: calc(100vh - 52px); width: 81%">
    <div class="middle-display">
        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-8">
                    <div class="card shadow">
                        <div class="card-header py-3 bg-white">
                            <p class="h3 m-0">{{ trans.messages.profile.security_privacy }}</p>
                        </div>
                        <div class="card-body">
                            <p class="h4">{{ trans.messages.general.password }}</p>
                            <p>{{ trans.messages.profile.update_password }} <a class="a-normal" @click="updatePassword()">{{ trans.messages.general.click_here }}</a></p>

                            <div class="my-3 border-top"></div>

                            <p class="h4">{{ trans.messages.navbar.notifications }}</p>

                            <div class="form-check mb-0">
                                <input class="form-check-input mt-0" type="checkbox" id="pushCheckbox" v-model="pushNotifications">
                                <label class="form-check-label h6" for="pushCheckbox">
                                    {{ trans.messages.profile.push_notifications }}
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input mt-0" type="checkbox" id="emailCheckbox" v-model="emailNotifications">
                                <label class="form-check-label h6" for="emailCheckbox">
                                    {{ trans.messages.profile.email_notifications }}
                                </label>
                            </div>

                            <!--div class="form-check mb-2">
                                <input class="form-check-input mt-0" type="checkbox" id="smsCheckbox" v-model="smsNotifications">
                                <label class="form-check-label h6" for="smsCheckbox">
                                    {{ trans.messages.profile.sms_notifications }}
                                </label>
                            </div-->

                            <button class="btn btn-primary" @click="save()" :disabled="loading">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="loading"></span>
                                {{ !loading ? trans.messages.general.save : (trans.messages.general.saving + '...') }}
                            </button>

                            <div class="my-3 border-top"></div>
                            
                            <p class="h4">{{ trans.messages.general.my_data }}</p>
                            <p>{{ trans.messages.profile.security_data_info }}</p>
                            <button class="btn btn-primary" @click="sendPersonalData()" :disabled="sendingPersonalData">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="sendingPersonalData"></span>
                                {{ trans.messages.profile.send_my_data }}
                            </button>

                            <div class="my-3 border-top"></div>
                            
                            <p class="h4">{{ trans.messages.general.delete_account }}</p>
                            <p>{{ trans.messages.profile.delete_account_info }}</p>
                            <button class="btn btn-danger" @click="deleteAccount()" :disabled="deletingAccount">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="deletingAccount"></span>
                                {{ trans.messages.profile.delete_my_account }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {
    props: [],
    template: "#profile-security-component-template",
    data:() => ({
        loading: false,
        pushNotifications: true,
        emailNotifications: true,
        smsNotifications: true,
        sendingPersonalData: false,
        deletingAccount: false
    }),
    computed: {
        user(){
            return this.$store.state.currentUser.user
        }
    },
    methods: {
        updatePassword(){
            VueEvent.$emit('changeModal', 'update-password-modal');
        },
        save(){
            if(!t.loading){
                this.loading = true;
                axios.post(window.location.origin + '/update_security', {
                    push: this.pushNotifications ? 1 : 0,
                    email: this.emailNotifications ? 1 : 0,
                    sms: this.smsNotifications ? 1 : 0
                })
                .then((response) => {
                    this.$store.dispatch('currentUser/setPrivacy', {
                        pushNotifications: this.pushNotifications,
                        emailNotifications: this.emailNotifications,
                        smsNotifications: this.smsNotifications,
                    });
                    this.$root.user.push_notifications = this.pushNotifications ? 1 : 0;
                    VueEvent.$emit('alert', {
                        type: 'success',
                        messages: [trans.messages.profile.preferences_saved]
                    });
                    this.loading = false
                })
                .catch((error) => {
                    this.loading = false
                    VueEvent.$emit('alert', {
                        type: 'danger',
                        messages: [trans.messages.error.error_1]
                    });
                })
            }
        },
        sendPersonalData(){
            if(!t.sendingPersonalData){
                this.sendingPersonalData = true;
                axios.post(window.location.origin + '/send_personal_data', {
                    
                })
                .then((response) => {
                    this.sendingPersonalData = false;
                    VueEvent.$emit('alert', {
                        type: 'success',
                        messages: [trans.messages.profile.sent, trans.messages.profile.contact_shortly]
                    });
                })
                .catch((error) => {
                    this.sendingPersonalData = false;
                    VueEvent.$emit('alert', {
                        type: 'danger',
                        messages: [trans.messages.error.error_1]
                    });
                })
            }
        },
        deleteAccount(){
            if(!t.deletingAccount){
                this.deletingAccount = true;
                axios.post(window.location.origin + '/delete_personal_account')
                .then((response) => {
                    this.deletingAccount = false;
                    VueEvent.$emit('alert', {
                        type: 'success',
                        messages: [trans.messages.profile.sent, trans.messages.profile.contact_shortly]
                    });
                })
                .catch((error) => {
                    this.deletingAccount = false;
                    VueEvent.$emit('alert', {
                        type: 'danger',
                        messages: [trans.messages.error.error_1]
                    });
                })
            }
        }
    },
    created(){
        this.pushNotifications = user.push_notifications == 1;
        this.emailNotifications = user.email_notifications == 1;
        this.smsNotifications = user.sms_notifications == 1;
    }
}
</script>