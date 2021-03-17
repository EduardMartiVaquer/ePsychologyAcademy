<template id="create-password-template">
<div class="app-container flex-grow-1 align-self-stretch bg-gray-1">
    <div class="table-display" style="height: calc(100vh - 52px)">
        <div class="middle-display">
            <div class="row justify-content-center" style="min-height: 480px">
                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <img :src="url + '/images/password.svg'" alt="Search" height="250" class="d-block mx-auto">
                            <p class="h3 fw-300 text-center mt-2">{{ trans.messages.profile.create_password }}</p>
                            <p class="h5 fw-300">{{ trans.messages.profile.secureit }}</p>
                            <div class="mt-3">
                                <p class="h6 m-0">{{ trans.messages.general.password }}</p>
                                <input id="password-input" name="password" class="form-control form-control-lg" :class="passwordError != null ? 'is-invalid' : ''" :placeholder="trans.messages.general.password" v-model="password" type="password">
                                <p v-if="passwordError != null" class="text-danger mb-2">{{ passwordError }}</p>
                            </div>

                            <div class="mt-3 d-flex">
                                <button  class="btn btn-primary mr-0 ml-auto" @click="checkInfo()">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="loading"></span>
                                    {{ !loading ? trans.messages.general.save : (trans.messages.general.saving + '...') }}
                                </button>
                            </div>
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
    template: "#create-password-template",
    data:() => ({
        passwordError: null,
        password: '',
        loading: false
    }),
    methods: {
        checkInfo(){
            if(!this.loading){
                var infoOk = true;
                
                if(this.password.replace(/\s/g, '') == ''){
                    this.passwordError = "No puedes la contraseña en blanco";
                    infoOk = false;
                } else {
                    this.passwordError = null;
                }

                if(infoOk){
                    this.loading = true;
                    this.save();
                }
            }
        },
        save(){
            axios.post(this.url + '/post_password', {
                password: this.password
            })
            .then((response) => {
                // if(mainVue.user.sessions.length > 0 && mainVue.user.cards.data.length == 0){
                //     VueEvent.$emit('changeView', '/profile?page=edit-billing');
                // } else {
                //     if(mainVue.user.sessions.length > 0 && mainVue.user.cards.data.length > 0){
                //         VueEvent.$emit('changeView', '/sessions');
                //     } else {
                //         VueEvent.$emit('changeView', '/search');
                //     }
                // }
                VueEvent.$emit('changeView', '/');
            })
            .catch((error) => {
                console.log(error)
                this.loading = false;
                VueEvent.$emit('alert', {
                    type: 'danger',
                    messages: [trans.messages.messages.error.error_1]
                });
            })
        }
    }
}
</script>