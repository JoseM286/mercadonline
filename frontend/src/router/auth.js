import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', () => {
    // Estado
    const user = ref(null)
    const isAuthenticated = ref(false)

    // Acciones
    function login(userData) {
        // Lógica de inicio de sesión
        user.value = userData
        isAuthenticated.value = true
        // Guardar en sessionStorage para mantener la sesión entre recargas
        sessionStorage.setItem('user', JSON.stringify(userData))
    }

    function logout() {
        // Lógica de cierre de sesión
        user.value = null
        isAuthenticated.value = false
        // Limpiar sessionStorage
        sessionStorage.removeItem('user')
    }

    // Inicializar desde sessionStorage (para mantener la sesión entre recargas)
    function init() {
        const storedUser = sessionStorage.getItem('user')
        if (storedUser) {
            try {
                user.value = JSON.parse(storedUser)
                isAuthenticated.value = true
            } catch (e) {
                console.error('Error al recuperar la sesión:', e)
                sessionStorage.removeItem('user')
            }
        }
    }

    // Inicializar al cargar
    init()

    // Getters
    const userRole = computed(() => user.value?.role || 'guest')

    return { user, isAuthenticated, login, logout, userRole }
})
