<script setup>
// Importaciones para la navegación
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/router/auth';
import authService from '@/services/authService';
import { computed, ref } from 'vue';

const router = useRouter();
const authStore = useAuthStore();

// Estado de autenticación
const isAuthenticated = computed(() => authStore.isAuthenticated);
const isAdmin = computed(() => authStore.isAdmin);
const userName = computed(() => authStore.user?.name || 'Usuario');

// Estado para el buscador
const searchQuery = ref('');
const selectedCategory = ref('');

// Función para realizar la búsqueda
const handleSearch = () => {
  if (!searchQuery.value.trim()) return;
  
  const query = {
    search: searchQuery.value,
    sort: 'popularity'
  };
  
  // Añadir categoría si está seleccionada
  if (selectedCategory.value) {
    query.category = selectedCategory.value;
  }
  
  // Navegar a la página de resultados de búsqueda con los parámetros
  router.push({ 
    name: 'search-results',
    query
  });
};

// Funciones de navegación
const goToHome = () => {
  router.push('/');
};

const goToAbout = () => {
  router.push('/about');
};

const goToLogin = () => {
  router.push('/login');
};

const goToProfile = () => {
  router.push('/profile');
};

const goToCart = () => {
  router.push('/cart');
};

// Funciones de navegación para secciones de administrador
const goToAdminStats = () => {
  router.push('/admin/stats');
};

const goToAdminProducts = () => {
  router.push('/admin/products');
};

const goToAdminOrders = () => {
  router.push('/admin/orders');
};

const goToAdminUsers = () => {
  router.push('/admin/users');
};

// Función para cerrar sesión
const handleLogout = async () => {
  try {
    console.log('Iniciando proceso de logout');

    // Intentamos cerrar sesión en el servidor primero
    try {
      console.log('Llamando a authService.logout()');
      const response = await authService.logout();
      console.log('Respuesta del servidor:', response);
    } catch (error) {
      console.error('Error al cerrar sesión en el servidor:', error);
      // No es crítico si falla en el servidor
    }

    // Cerramos sesión localmente después
    authStore.logout();
    console.log('Sesión local cerrada');

    // Redirigimos al usuario a la página principal
    router.push('/');
  } catch (error) {
    console.error('Error general en el proceso de logout:', error);
    // Aseguramos que la sesión local se cierre en cualquier caso
    authStore.logout();
    router.push('/');
  }
};
</script>

<template>
  <header class="header">
    <!-- Franja superior -->
    <div class="top-bar">
      <div class="logo-container">
        <img src="@/assets/images/logo_verde.png" alt="Logo MercadonLine" class="header-logo" @click="goToAbout" style="cursor: pointer;" />
      </div>

      <div class="logo-container">
        <h3 @click="goToHome" style="cursor: pointer;">Inicio</h3>
      </div>

      <div class="search-container">
        <div class="search-box">
          <input 
            type="text" 
            placeholder="Buscar productos..." 
            class="search-input" 
            v-model="searchQuery"
            @keyup.enter="handleSearch"
          />
          <select class="category-select" v-model="selectedCategory">
            <option value="">Todas las categorías</option>
            <option value="1">Bebidas</option>
            <option value="2">Frutas y Verduras</option>
            <option value="3">Carnes y Aves</option>
            <option value="4">Pescados y Mariscos</option>
            <option value="5">Lácteos y Huevos</option>
            <option value="6">Panadería y Repostería</option>
            <option value="7">Despensa</option>
            <option value="8">Hogar y Limpieza</option>
          </select>
          <button class="search-button" @click="handleSearch">Buscar</button>
        </div>
      </div>

      <!-- Mostrar opciones según estado de autenticación -->
      <div class="user-actions">
        <template v-if="isAuthenticated">
          <div class="dropdown">
            <a class="user-action-link" style="cursor: pointer;">
              <span class="icon">👤</span>
              <span class="text">{{ userName }}</span>
            </a>
            <div class="dropdown-content">
              <a @click="goToProfile" style="cursor: pointer;">Mi perfil</a>
              <template v-if="isAdmin">
                <a @click="goToAdminStats" style="cursor: pointer;">Estadísticas</a>
                <a @click="goToAdminProducts" style="cursor: pointer;">Gestionar Productos</a>
                <a @click="goToAdminOrders" style="cursor: pointer;">Gestionar Pedidos</a>
                <a @click="goToAdminUsers" style="cursor: pointer;">Gestionar Usuarios</a>
              </template>
              <a @click="handleLogout" style="cursor: pointer;">Cerrar sesión</a>
            </div>
          </div>
        </template>
        <template v-else>
          <a @click="goToLogin" class="user-action-link" style="cursor: pointer;">
            <span class="icon">👤</span>
            <span class="text">Iniciar sesión</span>
          </a>
        </template>
      </div>
      <div class="user-actions margin-right-50">
        <a @click="goToCart" class="user-action-link" style="cursor: pointer;">
          <span class="icon">🛒</span>
          <span class="text">Mi cesta</span>
        </a>
      </div>
    </div>

    <!-- Franja inferior - Categorías -->
    <nav class="categories-nav">
      <ul class="categories-list">
        <li><router-link :to="{ name: 'category', params: { id: 1 } }" class="category-link">Bebidas</router-link></li>
        <li><router-link :to="{ name: 'category', params: { id: 2 } }" class="category-link">Frutas y Verduras</router-link></li>
        <li><router-link :to="{ name: 'category', params: { id: 3 } }" class="category-link">Carnes y Aves</router-link></li>
        <li><router-link :to="{ name: 'category', params: { id: 4 } }" class="category-link">Pescados y Mariscos</router-link></li>
        <li><router-link :to="{ name: 'category', params: { id: 5 } }" class="category-link">Lácteos y Huevos</router-link></li>
        <li><router-link :to="{ name: 'category', params: { id: 6 } }" class="category-link">Panadería y Repostería</router-link></li>
        <li><router-link :to="{ name: 'category', params: { id: 7 } }" class="category-link">Despensa</router-link></li>
        <li><router-link :to="{ name: 'category', params: { id: 8 } }" class="category-link">Hogar y Limpieza</router-link></li>
      </ul>
    </nav>
  </header>
</template>

<style scoped>
.header {
  width: 100%;
  background-color: #2c5e1a;
  color: white;
}

/* Franja superior */
.top-bar {
  display: flex;
  justify-content: space-around;
  align-items: center;

  background-color: #2c5e1a;
}

.logo-container {
  flex: 1;
  margin-left: 30px;
}

.header-logo {
  height: 80px;
  width: auto;
  margin-left: 20px;
}

.search-container {
  flex: 7;
  display: flex;
  justify-content: center;
}

.search-box {
  display: flex;
  width: 100%;
  max-width: 1200px;
  border-radius: 4px;
  overflow: hidden;
}

.search-input {
  flex: 5;
  padding: var(--spacing-sm);
  border: none;
  font-size: 1.2rem;
}

.category-select {
  flex: 2;
  padding: var(--spacing-sm);
  text-align: center;
  border: none;
  border-left: 1px solid #ddd;
  font-size: 1.3rem;
  background-color: white;
}

.search-button {
  flex: 0.8;
  padding: 0.7rem var(--spacing-md);
  background-color: #3a7a23;
  color: white;
  border: none;
  font-weight: bold;
  font-size: 1.3rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.search-button:hover {
  background-color: #4a9a2e;
}

.user-actions {
  flex: 1;
  display: flex;
  justify-content: flex-end;
  gap: var(--spacing-md);
}

.margin-right-50{
  margin-right: 50px;
}

.user-action-link {
  display: flex;
  flex-direction: column;
  align-items: center;
  color: white;
  text-decoration: none;
  transition: opacity 0.3s ease;
}

.user-action-link:hover {
  opacity: 0.8;
}

.icon {
  font-size: 2rem;
  margin-bottom: var(--spacing-xs);
}

.text {
  font-size: 1rem;
}

/* Dropdown menu */
.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  right: 0;
  background-color: white;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
  border-radius: 4px;
}

.dropdown-content a {
  color: #333;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
  text-align: left;
}

.dropdown-content a:hover {
  background-color: #f1f1f1;
}

.dropdown:hover .dropdown-content {
  display: block;
}

/* Franja inferior - Categorías */
.categories-nav {
  background-color: #3a7a23;
  padding: var(--spacing-sm) var(--spacing-lg);
}

.categories-list {
  display: flex;
  justify-content: space-between;
  list-style: none;
  margin: 0;
  padding: 0;
}

.category-link {
  color: white;
  text-decoration: none;
  padding: var(--spacing-xs) var(--spacing-sm);
  white-space: nowrap;
  font-size: 0.9rem;
  transition: background-color 0.3s ease;
  border-radius: 4px;
}

.category-link:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

/* Responsive */
@media (max-width: 992px) {
  .top-bar {
    flex-direction: column;
    gap: var(--spacing-md);
  }

  .logo-container, .search-container, .user-actions {
    width: 100%;
  }

  .user-actions {
    justify-content: center;
  }

  .categories-list {
    flex-wrap: nowrap;
    justify-content: flex-start;
    gap: var(--spacing-md);
    padding-bottom: var(--spacing-xs);
  }
}

@media (max-width: 768px) {
  .search-box {
    flex-direction: column;
  }

  .category-select {
    border-left: none;
    border-top: 1px solid #ddd;
  }
}
</style>





