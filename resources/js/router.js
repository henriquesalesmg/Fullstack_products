import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './pages/Dashboard.vue';
const Auth = () => import('./pages/Auth.vue');
const Login = () => import('./pages/Login.vue');
const Register = () => import('./pages/Register.vue');
const Forgot = () => import('./pages/Forgot.vue');
const Products = () => import('./pages/Products.vue');
const ProductForm = () => import('./pages/ProductForm.vue');
const Settings = () => import('./pages/Settings.vue');
const Logout = () => import('./pages/Logout.vue');

const routes = [
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/auth',
    component: Auth,
    children: [
      { path: '', name: 'Login', component: Login },
      { path: 'register', name: 'Register', component: Register },
      { path: 'forgot', name: 'Forgot', component: Forgot }
    ]
  },
  {
    path: '/products',
    name: 'Products',
    component: Products,
    meta: { requiresAuth: true }
  },
  {
    path: '/products/new',
    name: 'ProductNew',
    component: ProductForm,
    meta: { requiresAuth: true }
  },
  {
    path: '/products/:id/edit',
    name: 'ProductEdit',
    component: ProductForm,
    meta: { requiresAuth: true }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: Settings,
    meta: { requiresAuth: true }
  },
  {
    path: '/logout',
    name: 'Logout',
    component: Logout,
    meta: { requiresAuth: true }
  },
  {
    path: '/',
    redirect: '/dashboard'
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  if (to.meta.requiresAuth && !token) {
    next('/auth');
  } else {
    next();
  }
});

export default router;
