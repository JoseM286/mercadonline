import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/productos',
      name: 'products',
      component: () => import('../views/ProductsView.vue'),
    },
    {
      path: '/carrito',
      name: 'cart',
      component: () => import('../views/CartView.vue'),
    },
    {
      path: '/sobre-nosotros',
      name: 'about',
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/contacto',
      name: 'contact',
      component: () => import('../views/AboutView.vue'), // Temporalmente usando AboutView
    },
  ],
})

export default router


