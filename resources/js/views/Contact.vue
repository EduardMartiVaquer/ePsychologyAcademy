<template id="contact-template">
    <div class="app-container container contact-div">
        <div class="row" style="min-height: calc(100vh - 52px)">
            <div class="col-12 col-md-7 offset-md-5 align-self-center">
                <div class="card card-body shadow">
                    <p class="h3">{{ trans.messages.contact.contact }}</p>
                    <p class="h5">{{ trans.messages.contact.subtitle }}</p>

                    <p class="h6 m-0">{{ trans.messages.general.name }}</p>
                    <input class="form-control mb-3" :placeholder="trans.messages.general.name" type="text" v-model="name">

                    <p class="h6 m-0">{{ trans.messages.general.email }}</p>
                    <input class="form-control mb-3" :placeholder="trans.messages.general.email" type="email" v-model="email">

                    <p class="h6 m-0">{{ trans.messages.contact.message }}</p>
                    <textarea name="message" id="message" rows="4" :placeholder="trans.messages.contact.message_subtitle" class="form-control mb-5" v-model="message"></textarea>

                    <div class="d-flex">
                        <button class="btn btn-primary ml-auto mr-0" @click="send()">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="loading"></span>
                            {{ loading ? (trans.messages.general.sending + '...') : trans.messages.general.send }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    template: "#contact-template",
    data: () => ({
        loading: false,
        name: '',
        email: '',
        message: ''
    }),
    methods: {
        send(){
            if(!t.loading){
                this.loading = true;
                axios.post(window.location.origin + '/contact_admin', {
                    name: this.name,
                    email: this.email,
                    message: this.message
                })
                .then((response) => {
                    this.loading = false;
                    VueEvent.$emit('alert', {
                        type: 'success',
                        messages: ['Mensaje enviado correctamente']
                    });
                })
                .catch((error) => {
                    this.loading = false;
                    VueEvent.$emit('alert', {
                        type: 'danger',
                        messages: []
                    });
                })
            }
        }
    }
}
</script>