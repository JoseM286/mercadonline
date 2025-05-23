<template>
  <div class="admin-stats">
    <h1>Panel de Estadísticas</h1>
    <p>Vista general de las estadísticas de ventas, stock y usuarios.</p>
    
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Cargando estadísticas...</p>
    </div>
    
    <div v-else-if="error" class="error-container">
      <h3>Error al cargar estadísticas</h3>
      <p>{{ error }}</p>
      <button @click="fetchDashboardStats" class="btn-retry">Reintentar</button>
    </div>
    
    <div v-else>
      <div class="stats-container">
        <div class="stats-card">
          <h3>Ventas Totales</h3>
          <div class="stats-value">{{ dashboardStats.totalSales || 0 }}€</div>
        </div>
        
        <div class="stats-card">
          <h3>Productos en Catálogo</h3>
          <div class="stats-value">{{ dashboardStats.totalProducts || 0 }}</div>
        </div>
        
        <div class="stats-card">
          <h3>Usuarios Registrados</h3>
          <div class="stats-value">{{ dashboardStats.users?.total || 0 }}</div>
        </div>
        
        <div class="stats-card">
          <h3>Pedidos Realizados</h3>
          <div class="stats-value">{{ dashboardStats.totalOrders || 0 }}</div>
        </div>
      </div>
      
      <div class="detailed-stats">
        <div class="stats-section">
          <h2>Productos Populares</h2>
          <div class="table-container">
            <table v-if="dashboardStats.popularProducts && dashboardStats.popularProducts.length">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Categoría</th>
                  <th>Precio</th>
                  <th>Ventas</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="product in dashboardStats.popularProducts" :key="product.id">
                  <td>{{ product.id }}</td>
                  <td>{{ product.name }}</td>
                  <td>{{ product.category.name }}</td>
                  <td>{{ product.price }}€</td>
                  <td>{{ product.sales || 0 }}</td>
                </tr>
              </tbody>
            </table>
            <p v-else>No hay datos disponibles</p>
          </div>
        </div>
        
        <div class="stats-section">
          <h2>Pedidos Recientes</h2>
          <div class="table-container">
            <table v-if="dashboardStats.recentOrders && dashboardStats.recentOrders.length">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Usuario</th>
                  <th>Total</th>
                  <th>Estado</th>
                  <th>Fecha</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="order in dashboardStats.recentOrders" :key="order.id">
                  <td>{{ order.id }}</td>
                  <td>{{ order.user.name }}</td>
                  <td>{{ order.total_amount }}€</td>
                  <td>
                    <span :class="'status-badge status-' + order.status">
                      {{ translateStatus(order.status) }}
                    </span>
                  </td>
                  <td>{{ formatDate(order.created_at) }}</td>
                </tr>
              </tbody>
            </table>
            <p v-else>No hay pedidos recientes</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import adminService from '@/services/adminService';

// Estado para los datos del dashboard
const dashboardStats = ref({
  users: {},
  popularProducts: [],
  recentOrders: [],
  totalProducts: 0,
  totalSales: 0,
  totalOrders: 0
});
const loading = ref(true);
const error = ref(null);

// Formatear fecha
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('es-ES');
};

// Traducir estados de pedidos
const translateStatus = (status) => {
  const statusMap = {
    'pending': 'Pendiente',
    'paid': 'Pagado',
    'processing': 'Procesando',
    'shipped': 'Enviado',
    'delivered': 'Entregado',
    'cancelled': 'Cancelado'
  };
  return statusMap[status] || status;
};

// Obtener estadísticas del dashboard
const fetchDashboardStats = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    // Obtener estadísticas del dashboard
    const stats = await adminService.getDashboardStats();
    dashboardStats.value = stats;
    
    // Calcular el total de ventas sumando los importes de los pedidos
    let totalSales = 0;
    let totalOrders = 0;
    
    if (stats.recentOrders && stats.recentOrders.length) {
      totalOrders = stats.recentOrders.length;
      totalSales = stats.recentOrders.reduce((sum, order) => {
        return sum + parseFloat(order.total_amount || 0);
      }, 0);
    }
    
    dashboardStats.value.totalSales = totalSales.toFixed(2);
    dashboardStats.value.totalOrders = totalOrders;
    
  } catch (err) {
    console.error('Error al obtener estadísticas:', err);
    error.value = 'No se pudieron cargar las estadísticas. Por favor, inténtalo de nuevo.';
  } finally {
    loading.value = false;
  }
};

// Cargar estadísticas al montar el componente
onMounted(() => {
  fetchDashboardStats();
});
</script>

<style scoped>
.admin-stats {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

h1 {
  color: #2c5e1a;
  margin-bottom: 1rem;
}

h2 {
  color: #3a7a23;
  margin: 2rem 0 1rem;
}

.stats-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
  margin-bottom: 2rem;
}

.stats-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
  text-align: center;
  transition: transform 0.3s ease;
}

.stats-card:hover {
  transform: translateY(-5px);
}

.stats-card h3 {
  color: #3a7a23;
  font-size: 1.2rem;
  margin-bottom: 1rem;
}

.stats-value {
  font-size: 2rem;
  font-weight: bold;
  color: #2c5e1a;
}

.detailed-stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

@media (max-width: 992px) {
  .detailed-stats {
    grid-template-columns: 1fr;
  }
}

.stats-section {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
}

.table-container {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}

th, td {
  padding: 0.75rem;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background-color: #f5f5f5;
  font-weight: bold;
}

tr:hover {
  background-color: #f9f9f9;
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 300px;
}

.loading-spinner {
  border: 5px solid #f3f3f3;
  border-top: 5px solid #3a7a23;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-container {
  text-align: center;
  padding: 2rem;
  background-color: #fff0f0;
  border-radius: 8px;
  margin: 2rem 0;
}

.btn-retry {
  background-color: #3a7a23;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  margin-top: 1rem;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.85rem;
  font-weight: bold;
}

.status-pending {
  background-color: #fff3cd;
  color: #856404;
}

.status-paid {
  background-color: #d4edda;
  color: #155724;
}

.status-processing {
  background-color: #cce5ff;
  color: #004085;
}

.status-shipped {
  background-color: #d1ecf1;
  color: #0c5460;
}

.status-delivered {
  background-color: #d4edda;
  color: #155724;
}

.status-cancelled {
  background-color: #f8d7da;
  color: #721c24;
}
</style>