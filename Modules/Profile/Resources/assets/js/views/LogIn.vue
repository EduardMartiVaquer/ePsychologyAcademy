<template id="login-template">
    <div class="row no-gutters login-wallpaper justify-content-center">
        <div class="col-12 col-md-5 px-3 px-md-5 pb-3" >
            <div class="d-flex align-items-center justify-content-center" style="min-height: 100vh">
                <div class="w-100" style="max-width: 400px;">
                    <p class="h2 mb-1">{{ trans.messages.login.login }}</p>
                     <p class="m-0 mb-4">{{ trans.messages.login.no_account }} <a class="a-normal" @click="goToRegister()">{{ trans.messages.login.register_here }}</a></p>

                    <button class="btn btn-lg btn-google btn-block bg-white" @click="googleRegister()"><img :src="url + '/images/google.svg'" height="24" class="mr-2"> {{ trans.messages.login.google }}</button>

                    <div class="mt-5 mb-3"></div>

                    <p class="d-block text-center mb-0" style="position: relative; top: -15px;"><span class="px-2 fw-400">{{ trans.messages.login.or }}</span></p>

                    <transition name="fade" mode="out-in">
                        <div class="mb-4" :key="0" v-if="emailType == 0">
                            <p class="h6 m-0">Email</p>
                            <input class="form-control form-control-lg" placeholder="Email" v-model="email" autocomplete="nope" @keyup.enter="login()">
                            <p class="fw-400"><a class="a-normal" @click="changeEmailType(1)">Entrar con mi móvil</a></p>
                        </div>
                        <div class="mb-4" :key="1" v-if="emailType == 1">
                            <p class="h6 m-0">Teléfono</p>
                            <input id="phone-input" class="form-control form-control-lg" placeholder="Teléfono" v-model="phone" autocomplete="nope" @keyup.enter="login()">
                            <p class="fw-400"><a class="a-normal" @click="changeEmailType(0)">Entrar con mi email</a></p>
                        </div>
                    </transition>

                    <p class="h6 mb-1">{{ trans.messages.general.password }}</p>
                    <input id="login-password" class="form-control form-control-lg" type="password" :placeholder="trans.messages.general.password" @keyup.enter="login()" v-model="password" name="password" required>

                    <button class="btn btn-dark btn-lg btn-block mt-3 mx-auto" @click="login()" :disabled="loading">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="loading"></span>
                        {{ trans.messages.login.login }}
                    </button>

                    <p class="m-0 mt-3">{{ trans.messages.login.forgot }} <a class="a-normal" @click="forgotPassword()">{{ trans.messages.general.click_here }}</a></p>
                </div>
            </div>
        </div>
        <!--div class="d-none d-md-block col-md-7 register-wallpaper">
            
        </div-->
    </div>
</template>

<script>
var iti;
export default {
    template: "#login-template",
    data:() => ({
        email: '',
        emailType: 0,
        phone: '',
        password: '',
        loading: false,
        lang: window.lang
    }),
    methods: {
        login(){
            this.loading = true;
            axios.post(this.url + '/login', {
                email: this.email,
                password: this.password
            })
            .then((response) => {
                if(response.data.status == "OK"){
                    window.location.href = window.location.origin + "/";
                } else {
                    this.loading = false;
                    VueEvent.$emit('alert', {
                        type: 'danger',
                        messages: [trans.messages.error.credentials]
                    });
                }
            })
            .catch((error) => {
                this.loading = false;
                VueEvent.$emit('alert', {
                    type: 'danger',
                    messages: [trans.messages.error.error_1]
                });
            })
        },
        goToRegister() {
            VueEvent.$emit('changeView', '/register')
        },
        goToMain(){
            VueEvent.$emit('changeView', '/');
        },
        forgotPassword(){
            window.location.href = url + '/password/email';
        },
        changeEmailType(i){
            this.email = "";
            this.phone = "";
            this.emailType = i;
            if(i == 1){
                waitForElement('#phone-input', () => {
                    var input = document.querySelector("#phone-input");
                    iti = window.intlTelInput(input, {
                        initialCountry: window.geoip.country_code2.toLocaleLowerCase(),
                        separateDialCode: true
                    });
                    window.setTimeout(() => {
                        this.email = "";
                        this.phone = "";
                    }, 100)
                });
            }
        }
    },
    created(){
        if(this.$store.state.currentUser.user != null){
            VueEvent.$emit('changeView', '/');
        }
    }
}
function waitForElement(elementPath, callBack){
	window.setTimeout(() => {
		if($(elementPath).length){
			callBack(elementPath, $(elementPath));
		}else{
			waitForElement(elementPath, callBack);
		}
	},50)
}
</script>