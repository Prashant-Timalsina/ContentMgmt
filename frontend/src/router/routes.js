const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        component: () => import('src/pages/users/HomePage.vue'),
        meta: { requiresAuth: true },
      },
      { path: '/user', component: () => import('pages/users/UserProfile.vue') },
    ],
  },
  {
    path: '/auth',
    component: () => import('layouts/AuthLayout.vue'),
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import('pages/auth/LoginPage.vue'),
        meta: { guestOnly: true },
      },
      {
        path: 'signup',
        name: 'signup',
        component: () => import('pages/auth/SignUpPage.vue'),
        meta: { guestOnly: true },
      },
    ],
  },
  {
    path: '/admin',
    component: () => import('layouts/AdminLayout.vue'),
    meta: { requiresAuth: true, role: 'admin' },
    children: [
      { path: '', name: 'dashboard', component: () => import('pages/admin/AdminDashboard.vue') },
      { path: '/listall', name: 'listAll', component: () => import('pages/admin/ListUsers.vue') },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
