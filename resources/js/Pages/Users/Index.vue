<template>
    <Header :title="title" :content="title" :items="items" />

    <div class="col-12 mt-n3">
        <div class="card">
            <div class="card-body" :style="{ height: height + 'px' }">
                <div class="row mb-3">
                    <div class="col-xl-6 col-sm-6 d-inline-block">
                        <button @click="create" type="button" class="btn btn-danger waves-effect waves-light me-1"><i class='bx bx-plus-medical'></i></button>
                        <form class="d-inline-block ">
                            <div class="search-box">
                                <div class="position-relative">
                                    <input type="text" class="form-control bg-light border-light rounded" placeholder="Search..." v-model="keyword">
                                    <i class='bx bx-search-alt search-icon'></i>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-xl-6 col-sm-6">
                        <ul class="list-inline user-chat-nav text-end mb-0 dropdown">
                            <Pagination v-if="meta" @fetch="fetch" :links="links" :pagination="meta"/>
                        </ul>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-nowrap align-middle">
                        <thead class="thead-light">
                            <tr class="font-size-11">
                                <th style="width: 2%;"></th>
                                <th>Name</th>
                                <th class="text-center">Contact Information</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="list in lists" v-bind:key="list.id">
                                <td>
                                    <div class="avatar-xs" v-if="list.avatar == 'avatar.jpg'">
                                        <span class="avatar-title rounded-circle">{{list.lastname.charAt(0)}}</span>
                                    </div>
                                    <div v-else>
                                        <img class="rounded-circle avatar-xs" :src="currentUrl+'/images/avatars/'+list.avatar" alt="">
                                    </div>
                                </td>
                                <td>
                                    <h5 class="font-size-13 mb-0 text-dark">{{list.lastname}}, {{list.firstname}} {{list.middlename}}.</h5>
                                    <p class="font-size-12 text-muted mb-0">{{list.role}}</p>
                                </td>
                                <td class="text-center">
                                    <h5 class="font-size-12 mb-0 text-dark">{{list.email}}</h5>
                                    <p class="font-size-12 text-muted mb-0">{{list.mobile}}</p>
                                </td>
                                <td class="text-center">
                                    <span v-if="list.is_active == 1" class="badge bg-success fs-11">Active</span>
                                    <span v-else class="badge bg-danger fs-11">Inactive</span>
                                </td>
                                <td class="text-center">
                                    <a class="me-3 " @click="update(list)">
                                        <i v-bind:class="(list.is_active == 1) ? 'text-success bx bx-lock-open' : 'text-dark bx bxs-lock'"></i>
                                    </a>
                                    <a class="me-3 text-warning" @click="edit(list)"><i class='bx bx-edit-alt' ></i></a>
                                    <a class="text-info" @click="verify(list)"><i class='bx bx-mail-send'></i></a>
                                </td>
                            </tr>
                            <tr v-if="lists.length == 0">
                                <td class="text-center" colspan="5">
                                    No users found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
    
            </div>
        </div>
        <Update @info="message" ref="update"/>
        <Create @info="message" ref="create"/>
        <Verify ref="verify"/>
    </div>

</template>
<script>
import Verify from './Modals/Verify.vue';
import Update from './Modals/Update.vue';
import Create from './Modals/Create.vue';
import Header from "@/Shared/Header.vue";
import Pagination from "@/Shared/Pagination.vue";
import _ from 'lodash';

export default {
    components : { Header, Pagination, Update, Create, Verify },
    inject:['count', 'height'],
    data() {
        return {
            currentUrl: window.location.origin,
            title: "Users",
            items: [
                {text: "User", href: "/",},
                {text: "Lists",active: true,},
            ],
            lists: [],
            meta: {},
            links: {},
            keyword: '',
            showModal: false,
            editable: false
        };
    },
    watch: {
        keyword(newVal){
            this.checkSearchStr(newVal)
        }
    },
    created(){
        this.fetch();
    },
    methods : {
        checkSearchStr: _.debounce(function(string) {
            this.fetch();
        }, 300),

        fetch(page_url){
            page_url = page_url || '/users';
            axios.get(page_url,{
                params : {
                    keyword : this.keyword,
                    count: this.count,
                    search: true
                }
            })
            .then(response => {
                if(response){
                    this.lists = response.data.data;
                    this.meta = response.data.meta;
                    this.links = response.data.links;
                }
            })
            .catch(err => console.log(err));
        },

        update(list){
            this.$refs.update.set(list);
            this.editable = true;
        },

        create(){
            this.$refs.create.show();
            this.editable = false;
        },

        edit(list){
            this.$refs.create.edit(list);
            this.editable = true;
        },

        verify(list){
            this.$refs.verify.set(list);
        },

        message(list){
            if(!this.editable){
                this.lists.unshift(list);
            }else{
                let index = this.lists.findIndex(u => u.id === list.id);
                this.lists[index] = list;
            }
        },
    }
}
</script>

<!-- Inertia.get('/users', { keyword: string }, { preserveState: true, replace: true }); -->