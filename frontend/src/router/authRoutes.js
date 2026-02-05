import { defineStore } from "pinia";
import { api } from "src/boot/axios";

const authStore = defineStore('auth', {
    state :() => ({
        user:null,
        token:'',
        loading:false
    }),
    actions: {
        async getCsrfCookie(){
            await api.get('/sanctum/csrf-cookie');
        },
        
        async login(credentials){
            await this.getCsrfCookie();
            const response = await api.post('/login',credentials);
            console.log(response);
            await this.fetchUser();
        },

        async fetchUser(){
            try{
                const res = await api.get('/api/me')
                this.user = res.data;
            } catch(e){
                console.log(e);
                this.user = null;
            }
        },

        async logout(){
            const response = await api.post('/logout')
            console.log(response);
            this.user=null
        }
    }
})