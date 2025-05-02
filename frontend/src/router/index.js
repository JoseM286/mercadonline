import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import AdminDashboard from '../views/AdminDashboard.vue'
import LoginView from '../views/LoginView.vue'
import { useAuthStore } from '../stores/auth'

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
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    // Rutas de administración
    {
      path: '/admin',
      name: 'admin',
      component: AdminDashboard,
      meta: { requiresAdmin: true },
    }
  ],
})

// Guardia de navegación para proteger rutas de administración
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  // Si la ruta requiere permisos de administrador
  if (to.meta.requiresAdmin) {
    // Si no está autenticado, redirigir al login
    if (!authStore.isAuthenticated) {
      next({ name: 'login' })
    } 
    // Si está autenticado pero no es admin, redirigir a home
    else if (!authStore.isAdmin) {
      next({ name: 'home' })
    } 
    // Si es admin, permitir acceso
    else {
      next()
    }
  } else {
    // Para rutas que no requieren permisos especiales
    next()
  }
})

export default router



