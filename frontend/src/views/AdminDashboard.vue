<script setup>
import { ref, onMounted } from 'vue'

// Estado para controlar si el usuario está cargando
const isLoading = ref(true)
// Estado para almacenar estadísticas básicas
const stats = ref({
  totalUsers: 0,
  totalProducts: 0,
  totalOrders: 0,
  totalSales: 0
})

// Función para cargar datos básicos del dashboard
const loadDashboardData = async () => {
  isLoading.value = true
  try {
    // Aquí implementaremos la llamada a la API más adelante
    // Por ahora, usamos datos de ejemplo
    setTimeout(() => {
      stats.value = {
        totalUsers: 120,
        totalProducts: 45,
        totalOrders: 230,
        totalSales: 15600
      }
      isLoading.value = false
    }, 500)
  } catch (error) {
    console.error('Error al cargar datos del dashboard:', error)
    isLoading.value = false
  }
}

onMounted(() => {
  loadDashboardData()
})
</script>

<template>
  <div class="admin-dashboard">
    <h1>Panel de Administración</h1>
    
    <div v-if="isLoading" class="loading">
      Cargando datos...
    </div>
    
    <div v-else class="dashboard-stats">
      <div class="stat-card">
        <h3>Usuarios</h3>
        <div class="stat-value">{{ stats.totalUsers }}</div>
      </div>
      
      <div class="stat-card">
        <h3>Productos</h3>
        <div class="stat-value">{{ stats.totalProducts }}</div>
      </div>
      
      <div class="stat-card">
        <h3>Pedidos</h3>
        <div class="stat-value">{{ stats.totalOrders }}</div>
      </div>
      
      <div class="stat-card">
        <h3>Ventas</h3>
        <div class="stat-value">{{ stats.totalSales }}€</div>
      </div>
    </div>
    
    <div class="admin-menu">
      <h2>Gestión</h2>
      <nav>
        <ul>
          <li><RouterLink to="/admin/products">Productos</RouterLink></li>
          <li><RouterLink to="/admin/categories">Categorías</RouterLink></li>
          <li><RouterLink to="/admin/users">Usuarios</RouterLink></li>
          <li><RouterLink to="/admin/orders">Pedidos</RouterLink></li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<style scoped>
.admin-dashboard {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.dashboard-stats {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
  margin: 30px 0;
}

.stat-card {
  background-color: #f8f9fa;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.stat-value {
  font-size: 2rem;
  font-weight: bold;
  color: #42b983;
  margin-top: 10px;
}

.admin-menu {
  margin-top: 40px;
}

.admin-menu ul {
  list-style: none;
  padding: 0;
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
}

.admin-menu li a {
  display: block;
  padding: 12px 20px;
  background-color: #42b983;
  color: white;
  text-decoration: none;
  border-radius: 4px;
  font-weight: bold;
  transition: background-color 0.3s;
}

.admin-menu li a:hover {
  background-color: #3aa876;
}

.loading {
  text-align: center;
  padding: 40px;
  font-style: italic;
  color: #666;
}
</style>