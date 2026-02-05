import { defineStore } from "pinia";
import { api } from "src/boot/axios";

const authStore = defineStore('auth', {
    state :() => ({
        user:null,
        accessToken:null,
        isReady:false
    }),
    actions: {
        handleAuthResponse(data){
            this.user = data.user;
            this.accessToken = data.access_token;

            api.defaults.headers.common['Authorization'] = `Bearer ${this.accessToken}`
        },

        async login(credentials){
           const res = await api.post('/api/login',credentials)
           this.handleAuthResponse(res.data);
        },

        async register(credentials){
            const res = await api.post('/api/register',credentials)
            this.handleAuthResponse(res.data);
        },

        async fetchUser(){
            try{
                const res = await api.get('/api/me')
                this.user = res.data;
            } catch(e){
                console.log(e);
                this.logout();
            }
        },

        async logout(){
            const response = await api.post('/logout')
            console.log(response);
            this.user=null
            this.accessToken = null;
            delete api.defaults.headers.common['Authorization'];
        },
        async init(){
            try{
                await this.refresh();
                await this.fetchUser();
            }
            catch (e) {
                console.log('No active session found');
            }
            finally{
                this.isReady = true;
            }
        }
    }
})