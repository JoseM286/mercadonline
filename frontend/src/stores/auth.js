import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  // Estado
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)
  const isLoading = ref(false)
  const error = ref(null)

  // Getters
  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'ROLE_ADMIN')
  
  // Acciones
  function setUser(userData) {
    user.value = userData
  }
  
  function setToken(tokenValue) {
    token.value = tokenValue
    if (tokenValue) {
      localStorage.setItem('token', tokenValue)
    } else {
      localStorage.removeItem('token')
    }
  }
  
  async function login(credentials) {
    isLoading.value = true
    error.value = null
    
    try {
      // Aquí implementaremos la llamada a la API de login
      // Por ahora, simulamos una respuesta exitosa
      await new Promise(resolve => setTimeout(resolve, 500))
      
      // Datos de ejemplo
      const userData = {
        id: 1,
        email: 'admin@example.com',
        name: 'Admin User',
        role: 'ROLE_ADMIN'
      }
      
      setUser(userData)
      setToken('fake-jwt-token')
      
      return true
    } catch (err) {
      error.value = err.message || 'Error de autenticación'
      return false
    } finally {
      isLoading.value = false
    }
  }
  
  function logout() {
    setUser(null)
    setToken(null)
  }
  
  // Inicializar: cargar usuario si hay token
  async function initialize() {
    if (token.value) {
      // Aquí implementaremos la verificación del token
      // Por ahora, simulamos un usuario admin
      setUser({
        id: 1,
        email: 'admin@example.com',
        name: 'Admin User',
        role: 'ROLE_ADMIN'
      })
    }
  }
  
  return {
    // Estado
    user,
    token,
    isLoading,
    error,
    // Getters
    isAuthenticated,
    isAdmin,
    // Acciones
    login,
    logout,
    initialize
  }
})