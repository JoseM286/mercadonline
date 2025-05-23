import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import AboutView from '../views/AboutView.vue'
import ContactView from '../views/ContactView.vue'
import ProductDetailView from '../views/ProductDetailView.vue'
import { useAuthStore } from './auth' // Importamos el store de autenticación

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
      path: '/product/:id',
      name: 'product-detail',
      component: ProductDetailView,
    },
    {
      path: '/login',
      name: 'login',
      // Usamos lazy loading para esta vista
      component: () => import('../views/LoginView.vue'),
      meta: { requiresGuest: true }
    },
    {
      path: '/register',
      name: 'register',
      // Usamos lazy loading para esta vista
      component: () => import('../views/RegisterView.vue'),
      meta: { requiresGuest: true }
    },
    {
      path: '/cart',
      name: 'cart',
      // Usamos lazy loading para esta vista
      component: () => import('../views/CartView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/ProfileView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/category/:id',
      name: 'category',
      component: () => import('../views/CategoryView.vue'),
    },
    {
      path: '/search',
      name: 'search-results',
      component: () => import('../views/SearchResultsView.vue')
    },
    // Rutas de administración
    {
      path: '/admin/stats',
      name: 'admin-stats',
      component: () => import('../views/AdminStatsView.vue'),
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/products',
      name: 'admin-products',
      component: () => import('../views/AdminProductsView.vue'),
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/users',
      name: 'admin-users',
      component: () => import('../views/AdminUsersView.vue'),
      meta: { requiresAuth: true, requiresAdmin: true }
    },
  ],
})

// Navegación guard para proteger rutas
router.beforeEach((to, from, next) => {
  // Obtenemos el store dentro del guard, no durante la configuración
  const authStore = useAuthStore()
  
  // Si la ruta requiere autenticación y el usuario no está autenticado
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    // Guardar la ruta a la que intentaba acceder para redirigir después del login
    sessionStorage.setItem('redirectAfterLogin', to.fullPath)
    return next('/login')
  }
  
  // Si la ruta requiere rol de administrador y el usuario no es administrador
  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    return next('/')
  }
  
  // Si la ruta es solo para invitados (login/register) y el usuario está autenticado
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    return next('/profile')
  }
  
  next()
})

export default router








