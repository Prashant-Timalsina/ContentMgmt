const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/HomePage.vue'),meta: {requiresAuth: true} }
    ],
  },
  {
    path:'/auth',
    component: () => import('layouts/AuthLayout.vue'),
    children:[
      { path: 'login', name: 'login', component: () => import('pages/LoginPage.vue'),meta: {guestOnly: true}},
      { path:'signup', name:'signup', component: () => import('pages/SignUpPage.vue'),meta: {guestOnly: true}}
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
