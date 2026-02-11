import { defineRouter } from '#q-app/wrappers'
import {
  createRouter,
  createMemoryHistory,
  createWebHistory,
  createWebHashHistory,
} from 'vue-router'
import routes from './routes'
import { useAuthStore } from 'src/stores/authStore.js'

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

export default defineRouter(function ({ store }) {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : process.env.VUE_ROUTER_MODE === 'history'
      ? createWebHistory
      : createWebHashHistory

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,

    // Leave this as is and make changes in quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    history: createHistory(process.env.VUE_ROUTER_BASE),
  })

  Router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore(store)

    // says is rehydration ie. re-water every time i reload or is necessary
    if (!authStore.isReady) {
      await authStore.init()
    }

    const isAuthenticated = !!authStore.accessToken
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth)
    const isGuestOnly = to.matched.some((record) => record.meta.guestOnly)

    const userRoles = authStore.roles || []
    const hasReqRole = to.meta.role ? userRoles.includes(to.meta.role) : true

    const userPermissions = authStore.permissions || []
    const hasReqPermission = to.meta.permission
      ? userPermissions.includes(to.meta.permission)
      : true

    //Bouncer Logic

    //Guest only page but user is authenticated?(redirect to homepage)
    if (isGuestOnly && isAuthenticated) {
      return next('/')
    }

    //requires login but isnt logged in? (require login)
    if (requiresAuth && !isAuthenticated) {
      return next('/auth/login')
    }

    if (to.meta.role && !hasReqRole) {
      console.warn(`Access denied. Role ${to.meta.role} required`)
      return next('/403')
    }

    if (to.meta.permission && !hasReqPermission) {
      console.warn(`Access denied. Permission ${to.meta.permission} required`)
      return next('/403')
    }

    return next()
  })

  return Router
})
