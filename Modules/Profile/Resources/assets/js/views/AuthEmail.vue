<template id="auth-email-template">
    <div class="app-container table-display text-center">
        <div class="middle-display">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6">
                        <div class="card form-card">
                            <div class="card-body">
                                <p class="h3">Restablecer Contraseña</p>

                                <div class="w-100 border-top mt-3 mb-3"></div>

                                <p>Dinos tu email y te mandaremos un link para que puedas recuperar tu contraseña.</p>

                                <div class="container-fluid">
                                    <div class="row justify-content-center">
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9">
                                            <input v-model="email" type="email" class="form-control" name="email" placeholder="Email" @keyup.enter="submit()" required>
                                        </div>
                                    </div>
                                </div>

                                <button @click="checkEmail()" :disabled="loading" class="btn btn-primary mt-3">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="loading"></span>
                                    Enviar Link
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
        template: '#auth-email-template',
        data:() => ({
            loading: false,
            email: ''
        }),
        methods: {
            checkEmail(){
                if(!this.loading && this.email.replace(/\s/g, '') != ''){
                    this.loading = true;
                    axios.post(window.location.origin + '/check_email_address', {
                        email: this.email
                    })
                    .then((response) => {
                        if(response.data.status == "OK"){
                            this.submit();
                        } else {
                            this.loading = false;
                            VueEvent.$emit('alert', {
                                type: 'danger',
                                messages: ["No existe ninguna cuenta con esta dirección de email"]
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
                }
            },
            submit(){
                axios.post(window.location.origin + '/password/email', {
                    email: this.email
                })
                .then((response) => {
                    this.loading = false;
                    VueEvent.$emit('alert', {
                        type: 'success',
                        messages: ["Se ha enviado un email con un link para cambiar la contraseña"]
                    });
                    
                })
                .catch((error) => {
                    this.loading = false;
                    var coincidence = false;
                    if(error.errors != null){
                        if(error.errors.email != null){
                            if(error.errors.email[0] == "passwords.throttled"){
                                coincidence = true;
                            }
                        }
                    }
                    VueEvent.$emit('alert', {
                        type: 'danger',
                        messages: [coincidence ? "Has solicitado un link para restablecer contraseña hace menos de un minuto." : "Ha habido un error, intentalo de nuevo más tarde."]
                    });
                })
            }
        }
    }
</script>