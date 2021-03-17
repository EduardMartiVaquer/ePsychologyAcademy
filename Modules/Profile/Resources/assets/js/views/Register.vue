<template id="register-template">
    <div class="row no-gutters login-wallpaper justify-content-center">
        <div class="col-12 col-md-5 px-3 px-md-5 pb-3" >
            <div class="d-flex align-items-center justify-content-center" style="min-height: 100vh">
                <transition name="fade" mode="out-in">
                    <div v-if="page == 0" :key="0" class="w-100 pt-3" style="max-width: 500px">
                        <p class="h2">{{ trans.messages.register.create }}</p>
                        <!--div class="d-flex align-items-center mb-4">
                            <p class="h5 m-0">{{ trans.messages.register.are_you_psycho }}</p>
                            <button class="btn btn-primary ml-2" @click="goToPsycologist()">{{ trans.messages.register.psyco_register }}</button>
                        </div-->

                        <div class="mb-4">
                            <p class="h6 m-0">{{ trans.messages.general.name }}</p>
                            <input id="name-input" name="name" class="form-control form-control-lg" :class="nameError != null ? 'is-invalid' : ''" :placeholder="trans.messages.general.name" v-model="name" type="text" autofocus>
                            <p v-if="nameError != null" class="text-danger fw-400 mb-2">{{ nameError }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="h6 m-0">Email</p>
                            <input class="form-control form-control-lg" :class="emailError != null ? 'is-invalid' : ''" placeholder="Email" v-model="email" autocomplete="nope">
                            <p v-if="emailError != null" class="text-danger fw-400 mb-2">{{ emailError }}</p>
                        </div>

                        <div class="mb-4">
                            <!--p class="h2">{{ trans.messages.register.choose_password }}</p-->
                            <p class="h6 m-0">{{ trans.messages.general.password }}</p>
                            <input id="password-input" name="password" class="form-control form-control-lg" :class="passwordError != null || passwordRepeatError ? 'is-invalid' : ''" :placeholder="trans.messages.general.password" v-model="password" type="password">
                            <p v-if="passwordError != null" class="text-danger fw-400 mb-2">{{ passwordError }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="h6 m-0 mt-4">{{ trans.messages.register.repeat_password }}</p>
                            <input id="repeat-password-input" name="repeat-password" class="form-control form-control-lg" :class="passwordRepeatError != null ? 'is-invalid' : ''" :placeholder="trans.messages.register.repeat_password" v-model="repeatPassword" type="password">
                            <p v-if="passwordRepeatError != null" class="text-danger fw-400 mb-2">{{ passwordRepeatError }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="h6 m-0 mt-4">Código de registro</p>
                            <input class="form-control form-control-lg" v-model="code" :class="codeError ? 'is-invalid' : ''" placeholder="Si tienes un código de registro escribelo aquí"> 
                            <p v-if="codeError != null" class="text-danger fw-400 mb-2">{{ codeError }}</p>
                        </div>

                        <div class="form-check mt-4 d-flex">
                            <input type="checkbox" id="conditions-checkbox" v-model="acceptConditions" class="pointer" style="margin-top: 0.15rem">
                            <p class="m-0 ml-1" :class="conditionsError != null ? 'text-danger fw-400' : ''">{{ trans.messages.register.accept }} <a class="a-normal" href="https://epsychology.es/terms" target="_blank">{{ trans.messages.register.terms_link }}</a> {{ trans.messages.register.accept_2 }}</p>
                        </div>

                        <div class="form-check mt-1 d-flex">
                            <input type="checkbox" id="conditions-checkbox" v-model="acceptPrivacy" class="pointer" style="margin-top: 0.15rem">
                            <p class="m-0 ml-1" :class="conditionsError != null ? 'text-danger fw-400' : ''">{{ trans.messages.register.accept_3}} <a class="a-normal" href="https://epsychology.es/privacy" target="_blank">{{ trans.messages.register.privacy_link }}</a> {{ trans.messages.register.accept_4 }}</p>
                        </div>

                        <p v-if="conditionsError" class="text-danger fw-400 m-0">{{ conditionsError }}</p>
                        <p v-if="privacyError" class="text-danger fw-400 mb-4">{{ privacyError }}</p>

                        <button class="btn btn-dark btn-lg btn-block mt-5 mx-auto register-gtag" @click="checkInfo()" :disabled="loading">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="loading"></span>
                            {{ !loading ? trans.messages.register.register : (trans.messages.register.registering + '...') }}
                        </button>

                        <div class="d-flex align-items-center mt-3">
                            <p class="h5 m-0">{{ trans.messages.register.have }}</p>
                            <button class="btn btn-primary btn-sm ml-2" @click="goToLogin()">{{ trans.messages.login.login }}</button>
                        </div>
                    </div>
                    
                </transition>
            </div>
        </div>
    </div>
</template>

<script>
var iti;
export default {
    props: [],
    template: "#register-template",
    data:() => ({
        loading: false,
        name: '',
        email: '',
        password: '',
        repeatPassword: '',
        code: '',
        nameError: null,
        emailError: null,
        passwordError: null,
        codeError: null,
        acceptConditions: false,
        conditionsError: null,
        acceptPrivacy: false,
        privacyError: null,
        lang: window.lang,
        page: 0,

    }),
    computed: {
        passwordRepeatError(){
            var error = null;
            if(this.repeatPassword.replace(/\s/g, '') != ''){
                if(this.repeatPassword != this.password){
                    error = this.trans.messages.register.passwords_dont_match
                }
            }
            return error;
        }
    },
    methods: {
        checkInfo(){
            if(!this.loading){
                var infoOk = true;
                this.codeError = null;
                if(this.name.replace(/\s/g, '') == ''){
                    this.nameError = this.trans.messages.error.name_blank;
                    infoOk = false;
                } else {
                    this.nameError = null;
                }
                
                if(this.email.replace(/\s/g, '') == ''){
                    this.emailError = this.trans.messages.error.email_blank;
                    infoOk = false;
                } else {
                    this.emailError = null;
                }
                if(this.password.replace(/\s/g, '') == ''){
                    this.passwordError = this.trans.messages.error.password_blank;
                    infoOk = false;
                } else {
                    this.passwordError = null;
                }
                if(this.passwordRepeatError != null){
                    infoOk = false;
                }
                if(!this.acceptConditions){
                    this.conditionsError = this.trans.messages.error.accept_terms
                    infoOk = false;
                } else {
                    this.conditionsError = null;
                }
                if(!this.acceptPrivacy){
                    this.privacyError = this.trans.messages.error.accept_privacy
                    infoOk = false;
                } else {
                    this.privacyError = null;
                }

                if(infoOk){
                    this.loading = true;
                    this.register();
                }
            }
        },
        register(){
            axios.post(window.location.origin + '/register', {
                type: 1,
                name: this.name,
                email: this.email,
                password: this.password,
                code: this.code,
                timezone: moment.tz.guess()
            })
            .then((response) => {
                switch (response.data.status) {
                    case "OK":
                        axios.post(window.location.origin + '/login', {
                            email: this.email,
                            password: this.password,
                            remember: 1
                        })
                        .then((response) => {
                            window.location.href = this.url + "/";
                        })
                        .catch((error) => {
                            this.loading = false;
                            VueEvent.$emit('alert', {
                                type: 'danger',
                                messages: [this.trans.messages.error.error_1]
                            });
                        })
                    case "USER_ALREADY_EXISTS":
                        this.emailError = this.trans.messages.error.user_exists;
                    case "INVALID_EMAIL":
                        this.emailError = this.trans.messages.error.incorrect_email;
                    case "CODE_ERROR":
                        this.codeError = "Este código no existe o no es válido";
                    case "CODE_USED":
                        this.codeError = "Este código ya se ha utilizado";
                    default:
                        break;
                }
            })
            .catch((error) => {
                console.log(error);
                this.loading = false;
                VueEvent.$emit('alert', {
                    type: 'danger',
                    messages: [this.trans.messages.error.error_1]
                });
            })
        },
        goToLogin(){
            VueEvent.$emit('changeView', '/login');
        },
        goToMain(){
            VueEvent.$emit('changeView', '/');
        },
        goToPsycologist(){
            VueEvent.$emit('changeView', '/psychologist-register');
        },
        goToOrganization(){
            VueEvent.$emit('changeView', '/organization-register');
        },
        getInvitationCode(){
            axios.post(window.location.origin + '/get_invitation_code', {
                code: this.$route.query["code"]
            })
            .then((response) => {
                if(response.data.email != null){
                    this.email = response.data.email
                }
            })
        }
    },
    created(){
        if(this.$store.state.currentUser.user != null){
            VueEvent.$emit('changeView', '/');
        }
        $(document).ready(() => {
            if(this.$route.query["code"] != undefined){
                this.getInvitationCode();
            }
        });
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