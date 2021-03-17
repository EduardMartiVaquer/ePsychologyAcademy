<template id="auth-reset-template">
    <div class="app-container table-display text-center">
        <div class="middle-display">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6">
                        <div class="card form-card">
                            <div class="card-body">
                                <p class="h3">Nueva Contraseña</p>

                                <div class="w-100 border-top mt-3 mb-3"></div>

                                <p>Dinos tu email y te mandaremos un link para que puedas recuperar tu contraseña.</p>

                                <div class="container-fluid">
                                    <div class="row justify-content-center">
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9">
                                            <input v-model="email" type="email" class="form-control mb-1" name="email" placeholder="Email" required>
                                            <input v-model="password" type="password" class="form-control mb-1" name="password" placeholder="New password" required>
                                            <input v-model="password_confirmation" type="password" class="form-control mb-1" name="password_confirmation" placeholder="Confirm password" required>
                                        </div>
                                    </div>
                                </div>

                                <button @click="submit" :disabled="loading" class="btn btn-primary mt-3">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="loading"></span>
                                    Guardar y entrar
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
        template: '#auth-reset-template',
        data:() => ({
            email: window.oldEmail,
            password: '',
            password_confirmation: '',
            loading: false
        }),
        methods: {
            submit(){
                this.loading = true;
                axios.post(window.location.origin + '/password/reset', {
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                    token: window.resetToken
                })
                .then((response) => {
                    axios.post(window.location.origin + '/login', {
                        email: this.email,
                        password: this.password,
                        remember: 1
                    })
                    .then((response) => {
                        window.location.href = window.location.origin + '/profile';
                    })
                    .catch((error) => {
                        this.loading = false;
                        VueEvent.$emit('alert', {
                            type: 'danger',
                            messages: ["Ha habido un error, porfavor intentalo de nuevo más tarde"]
                        });
                    })
                })
                .catch((error) => {
                    this.loading = false;
                    VueEvent.$emit('alert', {
                        type: 'danger',
                        messages: ["Ha habido un error, intentalo de nuevo más tarde"]
                    });
                })
            },
            getUrlVars() {
                var vars = {};
                var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, (m,key,value) => {
                    vars[key] = value;
                });
                return vars;
            },
            getUrlParam(parameter, defaultvalue){
                var urlparameter = defaultvalue;
                if(window.location.href.indexOf(parameter) > -1){
                    urlparameter = this.getUrlVars()[parameter];
                    }
                return urlparameter;
            }
        },
        created(){
            $(document).ready(() => {
                if(window.resetToken == null){
                    var hrefArray = window.location.href.split('/');
                    if(hrefArray.length == 7){
                        window.resetToken = hrefArray[hrefArray.length - 1];
                    }
                }
                this.email = this.getUrlParam("email", null) != null ? this.getUrlParam("email", null).replace('%40', '@') : '';
            })
        }
    }
</script>