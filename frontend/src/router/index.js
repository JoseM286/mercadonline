import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import AboutView from '../views/AboutView.vue'
import ContactView from '../views/ContactView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/about',
      name: 'about',
      component: AboutView,
    },
    {
      path: '/contact',
      name: 'contact',
      component: ContactView,
    },
    {
      path: '/login',
      name: 'login',
      // Usamos lazy loading para esta vista
      component: () => import('../views/LoginView.vue'),
    },
    {
      path: '/cart',
      name: 'cart',
      // Usamos lazy loading para esta vista
      component: () => import('../views/CartView.vue'),
    },
  ],
})

export default router





