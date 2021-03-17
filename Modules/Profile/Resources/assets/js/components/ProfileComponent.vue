<template id="profile-component-template">
<div class="table-display" style="height: calc(100vh - 52px); width: 81%">
    <div class="middle-display">
        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-9" :class="user.type == 2 ? 'mb-2' : ''">
                    <div class="card shadow">
                        <div class="card-header py-3 bg-white">
                            <p class="h3 m-0">{{ trans.messages.profile.personal_info }}</p>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <p class="h6 mb-2">Foto de perfil</p>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-rounded avatar-filled pointer avatar-70" :style="user.avatar != null ? ('background-image: url(' + user.avatar + ')') : ''" onclick="document.getElementById('profile-image-input').click();">
                                        <div v-if="user.avatar == null" class="table-display" style="height: inherit">
                                            <div class="middle-display">
                                                <p class="h6 m-0 text-center color-white">{{ user.name.substr(0, 1).toUpperCase() + (user.last_name != null ? user.last_name.substr(0, 1).toUpperCase() : user.name.substr(1, 1).toUpperCase()) }}</p>
                                            </div>
                                        </div>
                                        <form action="'/update_avatar" method="post" enctype="multipart/form-data" class="d-none">
                                            <input id="profile-image-input" type="file" name="files[]" @change="submitAvatar" accept="image/*">
                                        </form>
                                    </div>
                                    <p class="m-0 ml-2"><a class="a-normal" onclick="document.getElementById('profile-image-input').click();"><i class="fas fa-pen"></i> {{ trans.messages.general.edit }}</a></p>
                                </div>
                            </div>   

                            <div class="row no-gutters">
                                <div class="col-12 col-lg-6 pr-lg-1 mb-2">
                                    <p class="h6 m-0">{{ trans.messages.general.name }}</p>
                                    <input class="form-control" type="text" name="name" :placeholder="trans.messages.general.name" v-model="name" required autofocus>
                                </div>
                                <div class="col-12 col-lg-6 mb-2">
                                    <p class="h6 m-0">{{ trans.messages.general.last_name }}</p>
                                    <input class="form-control" type="text" name="last_name" :placeholder="trans.messages.general.last_name" v-model="last_name" required>
                                </div>
                            </div>

                            <p class="h6 m-0">Email</p>
                            <input class="form-control mb-3" type="email" name="email" placeholder="Email" v-model="email" required>

                            <p class="h6 m-0">{{ trans.messages.general.phone }} {{ user.type != 1 ? '(Opcional)' : '' }}</p>
                            <input id="profile-phone-input" name="phone" class="form-control" :placeholder="trans.messages.general.phone" v-model="phone" type="text">
                        </div>
                        <div class="card-footer bg-white d-flex flex-row-reverse">
                            <button class="btn btn-primary" @click="checkInfo()" :disabled="savingPersonalInfo">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="savingPersonalInfo"></span>
                                {{ !savingPersonalInfo ? trans.messages.general.save : (trans.messages.general.saving + '...') }}
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
	var iti;
	export default {
		template: '#profile-component-template',
		data:() => ({
			name: '',
            last_name: '',
            email: '',
            phone: '',
            savingPersonalInfo: false,
            nameError: null,
            lastNameError: null,
            phoneError: null,
            emailError: null,
            savingSocialNetworks: false
		}),
		computed: {
			user(){
                console.log(this.$store.state)
                return this.$store.state.currentUser.user;
            }
		},
		methods: {
			submitAvatar(e){
                if(e.target.files[0] != undefined){
                    var reader = new FileReader();
                    reader.readAsDataURL(e.target.files[0]);
                    reader.onload = (e) => {
                        this.$root.propFile = e.target.result;
                        $('#profile-image-input').val('')
                        this.$route.cropAvatarType = 0;
                        VueEvent.$emit('changeModal', 'crop-avatar-image-modal');
                    }
                }
            },
            checkInfo(){
                if(!this.savingPersonalInfo){
                    var infoOk = true;
                    if(this.name.replace(/\s/g, '') == ''){
                        this.nameError = trans.messages.error.name_blank;
                        infoOk = false;
                    } else {
                        this.nameError = null;
                    }
                    if(this.email.replace(/\s/g, '') == ''){
                        this.emailError = trans.messages.error.email_blank;
                        infoOk = false;
                    } else {
                        this.emailError = null;
                    }
                    if(infoOk){
                        this.savingPersonalInfo = true;
                        this.updatePersonalInfo();
                    }
                }
            },
			updatePersonalInfo(){
                var phone = this.phone.replace(/\s/g, '') == '' ? null : this.phone + '' + iti.getSelectedCountryData().dialCode;
                axios.post(this.url + '/update_user_info', {
                    name: this.name,
                    last_name: this.last_name,
                    email: this.email.replace(/\s/g, '') != '' && isNaN(this.email) ? this.email : phone,
                    phone_prefix: this.phone.replace(/\s/g, '') == '' ? null : iti.getSelectedCountryData().dialCode,
                    phone: this.phone.replace(/\s/g, '') == '' ? null : this.phone,
                })
                .then((response) => {
                    this.savingPersonalInfo = false;
                    if(response.data.status == "OK"){
                        this.$root.user.name = this.name;
                        this.$root.user.last_name = this.last_name;
                        this.$root.user.email = isNaN(this.email) ? this.email : '';
                        this.$root.user.phone_prefix = this.phone.replace(/\s/g, '') == '' ? null : iti.getSelectedCountryData().dialCode;
                        this.$root.user.phone = this.phone.replace(/\s/g, '') == '' ? null : this.phone;

                        VueEvent.$emit('alert', {
                            type: 'success',
                            messages: [trans.messages.profile.personal_info_saved]
                        })
                    } else {
                        if(response.data.status == "USER_ALREADY_EXISTS"){
                            VueEvent.$emit('alert', {
                                type: 'danger',
                                messages: [trans.messages.error.user_exists]
                            })
                        }
                    }
                })
                .catch((error) => {
                    this.savingPersonalInfo = false;
                    VueEvent.$emit('alert', {
                        type: 'danger',
                        messages: [window.trans.messages.error.error_1]
                    })
                })
            },
            saveSocialNetworks(){
                if(!this.savingSocialNetworks){
                    this.savingSocialNetworks = true;
                    axios.post(this.url + '/save_social_networks', {
                        
                    })
                    .then((response) => {
                        this.savingSocialNetworks = false;
                    })
                    .catch((error) => {
                        this.savingSocialNetworks = false;
                        VueEvent.$emit('alert', {
                            type: 'danger',
                            messages: []
                        });
                    })
                }
            }
		},
		created(){
			$(document).ready(() => {
				this.name = this.user.name;
                this.last_name = this.user.last_name != null ? this.user.last_name : '';
                this.email = this.user.email;
                this.phone = this.user.phone != null ? this.user.phone : '';

                var input = document.querySelector("#profile-phone-input");
                iti = window.intlTelInput(input, {
                    initialCountry: window.lang,
                    separateDialCode: true
                });
                if(this.user.phone_prefix != null && this.user.phone != null){
                	iti.setNumber("+" + this.user.phone_prefix + this.user.phone);
                }
			});
		}
    }
</script>