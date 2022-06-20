<template>
    <b-modal v-model="showModal" @ok="create($event)" id="create" size="xl" title="Create User" no-close-on-backdrop centered>
        <div class="row" style="margin-right: 10px; margin-left: 10px;">
            <div class="col-md-3">
                <myUpload field="img" @crop-success="cropSuccess" v-model="photo.show" :width="500" :height="500"
                    :params="params" :headers="headers" lang-type="en" img-format="png">
                </myUpload>
                <div @click="toggleShow" class="fuzone" style="width: 225px; height: 210px;">
                    <div v-if="photo.url != ''">
                        <img :src="photo.url" style="width: 200px; height: 200x;">
                    </div>
                    <div v-else class="fu-text" @click="toggleShow">
                        <span>
                            <i class="fa fa-picture"></i> Click here to upload<br>
                            <!-- <span v-if="errors.avatar" style="color: red; font-size: 12px; margin-top: -20px;">({{ errors.avatar[0] }})</span> -->
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <b-form class="customform">
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>Lastname: <span v-if="form.errors.lastname" v-text="form.errors.lastname"
                                        class="haveerror"></span></label>
                                <input type="text" class="form-control"
                                    :class="[(form.errors.lastname) ? 'is-invalid' : '']" v-model="form.lastname"
                                    style="text-transform: capitalize;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Firstname: <span v-if="form.errors.firstname" v-text="form.errors.firstname"
                                        class="haveerror"></span></label>
                                <input type="text" class="form-control"
                                    :class="[(form.errors.lastname) ? 'is-invalid' : '']" v-model="form.firstname"
                                    style="text-transform: capitalize;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Middlename: <span v-if="form.errors.middlename" v-text="form.errors.middlename"
                                        class="haveerror"></span></label>
                                <input type="text" class="form-control"
                                    :class="[(form.errors.lastname) ? 'is-invalid' : '']" v-model="form.middlename"
                                    style="text-transform: capitalize;">
                            </div>
                        </div>
                        <div class="col-md-4" style="margin-top: -7px;">
                            <div class="form-group">
                                <label>Email: <span v-if="form.errors.email" v-text="form.errors.email" class="haveerror"></span></label>
                                <input type="email" class="form-control" :class="[(form.errors.email) ? 'is-invalid' : '']" v-model="form.email" style="text-transform: lowercase;">
                            </div>
                        </div>
                        <div class="col-md-4" style="margin-top: -7px;">
                            <div class="form-group">
                                <label>Mobile No.:  <span v-if="form.errors.mobile" v-text="form.errors.mobile" class="haveerror"></span></label>
                                <input type="text" class="form-control" :class="[(form.errors.mobile) ? 'is-invalid' : '']" v-model="form.mobile">
                            </div>
                        </div>
                        <div class="col-md-4" style="margin-top: -7px;">
                            <div class="form-group">
                                <label>Role: <span v-if="form.errors.role" v-text="form.errors.role" class="haveerror"></span></label>
                                <Multiselect 
                                    v-model="form.role" 
                                    :options="roles"
                                    :allow-empty="false"
                                    :show-labels="false"
                                    placeholder="Select Role"/>
                            </div>
                        </div>
                         <div class="col-md-6" style="margin-top: -10px;">
                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio mb-3">
                                        <input type="radio" id="customRadio1" class="custom-control-input mr-2"  value="Male" v-model="form.gender">
                                        <label class="custom-control-label" for="customRadio1">Male</label>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="custom-control custom-radio mb-3">
                                        <input type="radio" id="customRadio2" class="custom-control-input mr-2" value="Female" v-model="form.gender">
                                        <label class="custom-control-label" for="customRadio2">Female</label>
                                    </div>
                                </div>
                            </div>
                        </div>        
                    </div>
                </b-form>
            </div>
        </div>
        <template v-slot:footer>
            <b-button @click="hide()" variant="secondary" block>Cancel</b-button>
            <b-button @click="create('ok')" variant="primary" :disabled="form.processing" block>Save</b-button>
        </template>
    </b-modal>
</template>

<script>
    import myUpload from 'vue-image-crop-upload/upload-3.vue';
    import Multiselect from '@suadelabs/vue3-multiselect';
    export default {
        components: { myUpload , Multiselect },
        data() {
            return {
                currentUrl: window.location.origin,
                roles: ['Super Administrator','Regional Director', 'Accountant', 'Scholarship Coordinator', 'Scholarship Staff'],
                form: this.$inertia.form({
                    id: '',
                    email: '',
                    firstname: '',
                    lastname: '',
                    middlename: '',
                    role: '',
                    gender: '',
                    mobile: '',
                    avatar: 'avatar.jpg',
                    img: '',
                    profile_id: '',
                    editable: false
                }),
                showModal: false,
                photo: {
                    show: false,
                    url: '',
                    signature: ''
                },
                params: {
                    token: '123456798',
                    name: 'avatar'
                },
                headers: {
                    smail: '*_~'
                },
            }
        },
        watch: {
            datares: {
                deep: true,
                handler(val = null) {
                    if(val != null && val !== ''){
                        this.$emit('info', val.data);
                    }
                },
            },
        },
        computed: {
            datares() {
                return this.$page.props.flash.datares;
            },
        },
        methods: {
            show() {
                this.form.reset().clearErrors();
                this.showModal = true;
            },

            create() {
                if(!this.form.editable){
                    this.form.post('/users',{
                        preserveScroll: true,
                        onSuccess: (response) => {
                            this.hide();
                        }
                    });
                }else{
                    this.form.put('/users/update',{
                        preserveScroll: true,
                        onSuccess: (response) => {
                            this.hide();
                        }
                    });
                }
            },

            edit(data) {
                this.form.id = data.id;
                this.form.profile_id = data.profile_id;
                this.form.firstname = data.firstname;
                this.form.lastname = data.lastname;
                this.form.middlename = data.middlename;
                this.form.email = data.email;
                this.form.mobile = data.mobile;
                this.form.role = data.role;
                this.form.gender = data.gender;
                this.form.avatar = data.avatar; 
                this.form.editable = true;
                this.showModal = true;
            },

            hide(){
                this.form.reset().clearErrors();
                this.showModal = false;
            },

            toggleShow() {
                this.photo.show = !this.photo.show;
            },

            cropSuccess(imgDataUrl, field) {
                this.photo.url = imgDataUrl;
                this.form.img = imgDataUrl;
            }
        }
    }
</script>
