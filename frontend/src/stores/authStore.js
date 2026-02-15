import { defineStore } from 'pinia'
import { api } from 'src/boot/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    roles: [],
    permission: [],
    role: [],
    permissions: [],
    accessToken: null,
    isReady: false,
    users: [],
  }),
  actions: {
    handleAuthResponse(data) {
      this.accessToken = data.access_token
      this.user = data.user
      this.roles = data.roles
      this.permission = data.permissions
      api.defaults.headers.common['Authorization'] = `Bearer ${this.accessToken}`
    },

    async loadRoles() {
      const res = await api.get('/api/admin/roles')
      this.role = res.data
      console.log('Roles are:', res.data)
    },

    async loadPermissions() {
      const res = await api.get('/api/admin/permissions')
      this.permissions = res.data
      console.log('Permissions are:', res.data)
    },

    async login(credentials) {
      const res = await api.post('/api/login', credentials)
      this.handleAuthResponse(res.data)
      await this.fetchUser()
      return res
    },

    async register(credentials) {
      const res = await api.post('/api/register', credentials)
      this.handleAuthResponse(res.data)
      await this.fetchUser()
      return res
    },

    async fetchUser() {
      try {
        const res = await api.get('/api/me')
        this.user = res.data.user
        this.roles = res.data.roles
        this.permission = res.data.permissions
        // console.log('Full API Response:', res)
      } catch (error) {
        console.error('Error fetching user:', error)
        this.user = null
      }
    },

    async fetchUsers() {
      try {
        const res = await api.get('/api/admin/users')
        this.users = res.data
        console.log('List of users:', res.data)
      } catch (error) {
        console.log(error)
      }
    },

    async logout() {
      const response = await api.post('/api/logout')
      this.user = null
      this.accessToken = null
      delete api.defaults.headers.common['Authorization']
      return response
    },

    async refresh() {
      const response = await api.post('/api/refresh')
      this.handleAuthResponse(response.data)
    },

    async init() {
      try {
        await this.refresh()
        await this.fetchUser()
        if (this.permission?.includes('manage_users')) {
          await this.fetchUsers()
        }
      } catch {
        this.user = null
        this.accessToken = null
        delete api.defaults.headers.common['Authorization']
        console.log('User is a guest.')
      } finally {
        this.isReady = true
      }
    },
  },
})
