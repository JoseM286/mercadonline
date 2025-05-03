import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', () => {
    // Estado
    const user = ref(null)
    const token = ref(null)

    // Acciones
    function login(userData) {
        // Lógica de inicio de sesión
        user.value = userData
        isAuthenticated.value = true
    }

    function logout() {
        // Lógica de cierre de sesión
        user.value = null
        isAuthenticated.value = false
    }

    // Getters
    const userRole = computed(() => user.value?.role || 'guest')

    return { user, isAuthenticated, login, logout, userRole }
})
