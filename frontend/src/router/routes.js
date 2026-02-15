const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'home', component: () => import('src/pages/users/HomePage.vue') },
      { path: 'user', name: 'userProfile', component: () => import('pages/users/UserProfile.vue') },
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
      { path: 'listall', name: 'listAll', component: () => import('pages/admin/ListUsers.vue') },
      { path: 'requests', name: 'adminRequests', component: () => import('pages/admin/RequestsPage.vue') },
    ],
  },
  {
    path: '/editors',
    component: () => import('layouts/EditorLayout.vue'),
    meta: { requiresAuth: true, permission: 'create_articles' },
    children: [
      { path: '', redirect: { name: 'editorsContent' } },
      { path: 'content', name: 'editorsContent', component: () => import('pages/editors/ListContent.vue') },
      { path: 'create', name: 'editorsCreate', component: () => import('pages/editors/CreateArticle.vue') },
      { path: 'edit/:id', name: 'editorsEdit', component: () => import('pages/editors/EditArticle.vue') },
    ],
  },
  {
    path: '/403',
    name: 'forbidden',
    component: () => import('pages/Forbidden.vue'),
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
