import { defineBoot } from '#q-app/wrappers'
import axios from 'axios'
import {useAuthStore} from 'src/stores/authStore'

// Be careful when using SSR for cross-request state pollution
// due to creating a Singleton instance here;
// If any client changes this (global) instance, it might be a
// good idea to move this instance creation inside of the
// "export default () => {}" function below (which runs individually
// for each client)
const api = axios.create({ 
  baseURL: 'http://localhost:8000',
  withCredentials:true
})

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const authStore = useAuthStore();
    const originalRequest = error.config;

    if(!error.response) return Promise.reject(error);

    const skipUrls = ['/api/refresh', '/api/logout', '/api/me'];

    const shouldSkip = skipUrls.some(url => originalRequest.url?.includes(url));

    // if(originalRequest.url.includes('/api/refresh') || originalRequest.url.includes('/api/logout')){
    //   return Promise.reject(error);
    // }

    if( error.response.status === 401 && !originalRequest._retry && !shouldSkip){
      originalRequest._retry = true;
      try{
        await authStore.refresh();
  
        originalRequest.headers['Authorization'] = `Bearer ${authStore.accessToken}`;
        return api(originalRequest)
      }
      catch (refreshError){
        authStore.accessToken = null;
        authStore.user = null;
        return Promise.reject(refreshError)
      }
    }
    return Promise.reject(error)
  }
)

export default defineBoot(({ app }) => {
  // for use inside Vue files (Options API) through this.$axios and this.$api

  app.config.globalProperties.$axios = axios
  // ^ ^ ^ this will allow you to use this.$axios (for Vue Options API form)
  //       so you won't necessarily have to import axios in each vue file

  app.config.globalProperties.$api = api
  // ^ ^ ^ this will allow you to use this.$api (for Vue Options API form)
  //       so you can easily perform requests against your app's API
})

export { api }
