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
      path: '/sobre-nosotros',
      name: 'about',
      component: AboutView,
    },
    {
      path: '/contacto',
      name: 'contact',
      component: ContactView,
    },
  ],
})

export default router




