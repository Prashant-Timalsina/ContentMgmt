import { defineStore } from "pinia";
import { api } from "src/boot/axios";

export const useAuthStore = defineStore('auth', {
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
            }
        },

        async logout(){
            const response = await api.post('/api/logout')
            console.log(response);
            this.user=null
            this.accessToken = null;
            delete api.defaults.headers.common['Authorization'];
        },
        async refresh() {
            try{
                const response = await api.post('/api/refresh');
                console.log(response)
                this.handleAuthResponse(response.data);
            } catch (e) {
                console.log(e)
            }
        },
        async init(){
            try{
                await this.refresh();
                await this.fetchUser();
            }
            catch (e) {
                this.user = null
                this.accessToken = null
                delete api.defaults.headers.common['Authorization'];
                console.log('User is a guest. Errors are:\n',e)
            }
            finally{
                this.isReady = true;
            }
        }
    }
})