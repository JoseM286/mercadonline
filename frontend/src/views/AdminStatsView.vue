<template>
  <div class="admin-stats">
    <h1>Panel de Estadísticas</h1>
    <p>Vista general de las estadísticas de ventas, stock y usuarios.</p>
    
    <!-- Filtros de fecha -->
    <div class="date-filters">
      <div class="filter-group">
        <label for="startDate">Fecha desde:</label>
        <input 
          type="date" 
          id="startDate" 
          v-model="startDate" 
          :max="endDate || today"
        >
      </div>
      
      <div class="filter-group">
        <label for="endDate">Fecha hasta:</label>
        <input 
          type="date" 
          id="endDate" 
          v-model="endDate" 
          :min="startDate"
          :max="today"
        >
      </div>
      
      <button @click="applyDateFilter" class="btn-filter">Aplicar filtro</button>
      <button @click="resetDateFilter" class="btn-reset">Resetear</button>
    </div>
    
    <!-- Mostrar rango de fechas activo si hay filtro -->
    <div v-if="dashboardStats.dateRange && (dashboardStats.dateRange.startDate || dashboardStats.dateRange.endDate)" class="active-filter">
      <p>
        <strong>Filtro activo:</strong> 
        {{ dashboardStats.dateRange.startDate ? `Desde ${formatDate(dashboardStats.dateRange.startDate)}` : 'Desde el inicio' }} 
        {{ dashboardStats.dateRange.endDate ? `hasta ${formatDate(dashboardStats.dateRange.endDate)}` : 'hasta hoy' }}
      </p>
    </div>
    
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Cargando estadísticas...</p>
    </div>
    
    <div v-else-if="error" class="error-container">
      <p>{{ error }}</p>
      <button @click="fetchDashboardStats" class="btn-retry">Reintentar</button>
    </div>
    
    <div v-else class="stats-grid">
      <!-- Estadísticas de usuarios -->
      <div class="stats-card">
        <h3>Usuarios Registrados</h3>
        <div class="stats-value">{{ dashboardStats.users?.total || 0 }}</div>
      </div>
      
      <div class="stats-card">
        <h3>Administradores</h3>
        <div class="stats-value">{{ dashboardStats.users?.totalAdmins || 0 }}</div>
      </div>
      
      <!-- Eliminamos las tarjetas de estadísticas que ya no existen -->
      
      <!-- Estadísticas de productos y ventas -->
      <div class="stats-card">
        <h3>Total Productos</h3>
        <div class="stats-value">{{ dashboardStats.totalProducts || 0 }}</div>
      </div>
      
      <div class="stats-card">
        <h3>Total Pedidos</h3>
        <div class="stats-value">{{ dashboardStats.totalOrders || 0 }}</div>
      </div>
      
      <div class="stats-card">
        <h3>Total Ventas</h3>
        <div class="stats-value">{{ dashboardStats.totalSales || 0 }}€</div>
      </div>
      
      <!-- Resto del template sin cambios -->
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
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

// Estado para filtros de fecha
const startDate = ref('');
const endDate = ref('');
const today = computed(() => {
  const now = new Date();
  return now.toISOString().split('T')[0];
});

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

// Aplicar filtro de fechas
const applyDateFilter = () => {
  fetchDashboardStats(startDate.value, endDate.value);
};

// Resetear filtro de fechas
const resetDateFilter = () => {
  startDate.value = '';
  endDate.value = '';
  fetchDashboardStats();
};

// Obtener estadísticas del dashboard
const fetchDashboardStats = async (startDate = null, endDate = null) => {
  loading.value = true;
  error.value = null;
  
  try {
    // Obtener estadísticas del dashboard con filtros de fecha
    const stats = await adminService.getDashboardStats(startDate, endDate);
    dashboardStats.value = stats;
    
    // Calcular el total de ventas sumando los importes de los pedidos
    let totalSales = 0;
    let totalOrders = 0;
    
    if (stats.recentOrders && stats.recentOrders.length) {
      totalOrders = stats.totalOrders || stats.recentOrders.length;
      totalSales = stats.totalSales || stats.recentOrders.reduce((sum, order) => {
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

/* Nuevos estilos para los filtros de fecha */
.date-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 2rem;
  padding: 1rem;
  background-color: #f9f9f9;
  border-radius: 8px;
  align-items: flex-end;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-group label {
  font-weight: bold;
  color: #3a7a23;
}

.filter-group input {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.btn-filter {
  background-color: #3a7a23;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}

.btn-reset {
  background-color: #6c757d;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}

@media (max-width: 768px) {
  .date-filters {
    flex-direction: column;
  }
}

/* Estilo para el filtro activo */
.active-filter {
  background-color: #e9f7e9;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  margin-bottom: 1rem;
  border-left: 4px solid #3a7a23;
}
</style>
