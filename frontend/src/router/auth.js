import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    isAuthenticated: !!localStorage.getItem('user')
  }),
  
  getters: {
    isAdmin: (state) => state.user && state.user.role === 'ROLE_ADMIN',
    userName: (state) => state.user ? state.user.name : ''
  },
  
  actions: {
    setUser(user) {
      this.user = user;
      this.isAuthenticated = !!user;
      
      // Guardar en localStorage para persistencia
      if (user) {
        localStorage.setItem('user', JSON.stringify(user));
      }
    },
    
    logout() {
      this.user = null;
      this.isAuthenticated = false;
      localStorage.removeItem('user');
    }
  }
});

