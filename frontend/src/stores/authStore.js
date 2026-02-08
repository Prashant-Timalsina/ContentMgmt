import { defineStore } from "pinia";
import { api } from "src/boot/axios";

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        accessToken: null,
        isReady: false
    }),
    actions: {
        handleAuthResponse(data) {
            
            this.user = data.user;
            this.accessToken = data.access_token;

            api.defaults.headers.common['Authorization'] = `Bearer ${this.accessToken}`
        },

        async login(credentials) {
            const res = await api.post('/api/login', credentials)
            this.handleAuthResponse(res.data);
            await this.fetchUser();
            return res
        },

        async register(credentials) {
            const res = await api.post('/api/register', credentials)
            this.handleAuthResponse(res.data);
            await this.fetchUser();
            return res
        },
            
        async fetchUser() {
            try {
                const res = await api.get('/api/me')
                this.user = res.data;
                console.log('Full API Response:', res);
            } catch (error) {
                console.error('Error fetching user:', error);
                this.user = null
            }
        },

        async logout() {
            const response = await api.post('/api/logout')
            this.user = null
            this.accessToken = null;
            delete api.defaults.headers.common['Authorization'];
            return response
        },

        async refresh() {
            const response = await api.post('/api/refresh');
            this.handleAuthResponse(response.data);
        },

        async init() {
            try {
                await this.refresh();
                await this.fetchUser();
            }
            catch  {
                this.user = null
                this.accessToken = null
                delete api.defaults.headers.common['Authorization'];
                console.log('User is a guest.')
            }
            finally {
                this.isReady = true;
            }
        },
    }
});