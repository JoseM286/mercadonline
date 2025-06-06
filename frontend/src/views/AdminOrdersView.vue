<template>
  <div class="admin-orders">
    <h1>Gestión de Pedidos</h1>
    <p>Administra los pedidos de la plataforma, cambia su estado o elimínalos</p>
    
    <!-- Controles de búsqueda y filtrado -->
    <div class="admin-controls">
      <div class="search-filter">
        <input 
          type="text" 
          v-model="searchQuery" 
          placeholder="Buscar por ID o cliente..." 
          class="search-input"
          @keyup.enter="fetchOrders(1)"
        />
        <select v-model="statusFilter" class="status-filter">
          <option value="">Todos los estados</option>
          <option value="PENDING">Pendiente</option>
          <option value="PAID">Pagado</option>
          <option value="PROCESSING">Procesando</option>
          <option value="SHIPPED">Enviado</option>
          <option value="DELIVERED">Entregado</option>
          <option value="CANCELLED">Cancelado</option>
        </select>
        <button @click="fetchOrders(1)" class="btn-search">Buscar</button>
        <button @click="resetFilters" class="btn-reset">Resetear</button>
      </div>
      
      <div class="date-filters">
        <div class="date-filter">
          <label>Fecha desde:</label>
          <input type="date" v-model="startDate" class="date-input" />
        </div>
        <div class="date-filter">
          <label>Fecha hasta:</label>
          <input type="date" v-model="endDate" class="date-input" />
        </div>
      </div>
    </div>
    
    <!-- Tabla de pedidos -->
    <LoadingSpinner 
      v-if="loading" 
      message="Cargando pedidos..." 
    />
    
    <div v-else-if="error" class="error-container">
      <h3>Error al cargar pedidos</h3>
      <p>{{ error }}</p>
      <button @click="fetchOrders(currentPage)" class="btn-retry">Reintentar</button>
    </div>
    
    <div v-else-if="orders.length > 0" class="orders-table-container">
      <table class="orders-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Importe</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in orders" :key="order.id">
            <td>#{{ order.id }}</td>
            <td>{{ order.user ? order.user.name || order.user.email : 'Usuario eliminado' }}</td>
            <td>{{ order.total_amount }}€</td>
            <td>
              <span class="order-status" :class="'status-' + order.status.toLowerCase()">
                {{ translateStatus(order.status) }}
              </span>
            </td>
            <td>{{ formatDate(order.created_at) }}</td>
            <td class="actions-cell">
              <button @click="openStatusModal(order)" class="btn-edit">
                Cambiar Estado
              </button>
              <button @click="confirmDeleteOrder(order)" class="btn-delete">
                Eliminar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Paginación -->
      <div class="pagination" v-if="totalPages > 1">
        <button 
          @click="changePage(currentPage - 1)" 
          :disabled="currentPage === 1"
        >
          &laquo; Anterior
        </button>
        
        <button 
          v-for="page in pagesArray" 
          :key="page" 
          @click="changePage(page)"
          :class="{ active: page === currentPage }"
        >
          {{ page }}
        </button>
        
        <button 
          @click="changePage(currentPage + 1)" 
          :disabled="currentPage === totalPages"
        >
          Siguiente &raquo;
        </button>
      </div>
    </div>
    
    <div v-else class="no-orders">
      <p>No se encontraron pedidos con los criterios de búsqueda actuales.</p>
      <button @click="resetFilters" class="btn-reset">Resetear filtros</button>
    </div>
    
    <!-- Modal Cambiar Estado -->
    <div v-if="showStatusModal" class="modal">
      <div class="modal-content">
        <span class="close" @click="showStatusModal = false">&times;</span>
        <h2>Cambiar Estado del Pedido</h2>
        
        <div class="order-details">
          <p><strong>ID:</strong> {{ orderToEdit?.id }}</p>
          <p><strong>Cliente:</strong> {{ orderToEdit?.user?.name || orderToEdit?.user?.email }}</p>
          <p><strong>Importe:</strong> {{ orderToEdit?.total_amount }}€</p>
          <p><strong>Estado actual:</strong> {{ translateStatus(orderToEdit?.status) }}</p>
        </div>
        
        <div class="form-group">
          <label for="new-status">Nuevo estado:</label>
          <select id="new-status" v-model="newStatus" class="form-control">
            <option value="PENDING">Pendiente</option>
            <option value="PAID">Pagado</option>
            <option value="PROCESSING">Procesando</option>
            <option value="SHIPPED">Enviado</option>
            <option value="DELIVERED">Entregado</option>
            <option value="CANCELLED">Cancelado</option>
          </select>
        </div>
        
        <div class="form-actions">
          <button @click="showStatusModal = false" class="btn-cancel">Cancelar</button>
          <button @click="updateOrderStatus" class="btn-primary">Actualizar Estado</button>
        </div>
      </div>
    </div>
    
    <!-- Modal Eliminar Pedido -->
    <div v-if="showDeleteModal" class="modal">
      <div class="modal-content">
        <span class="close" @click="showDeleteModal = false">&times;</span>
        <h2>Eliminar Pedido</h2>
        
        <div class="delete-confirmation">
          <p>¿Estás seguro de que deseas eliminar el pedido #{{ orderToDelete?.id }}?</p>
          <p>Esta acción no se puede deshacer.</p>
        </div>
        
        <div class="form-actions">
          <button @click="showDeleteModal = false" class="btn-cancel">Cancelar</button>
          <button @click="deleteOrder" class="btn-delete">Eliminar Pedido</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import adminService from '@/services/adminService';
import LoadingSpinner from '@/components/LoadingSpinner.vue';

// Estado para la lista de pedidos
const orders = ref([]);
const loading = ref(true);
const error = ref(null);

// Estado para filtros y paginación
const searchQuery = ref('');
const statusFilter = ref('');
const startDate = ref('');
const endDate = ref('');
const currentPage = ref(1);
const totalOrders = ref(0);
const totalPages = ref(1);
const ordersPerPage = ref(10);

// Estado para modales
const showStatusModal = ref(false);
const showDeleteModal = ref(false); // Asegurarnos de que esté inicializado como false
const orderToEdit = ref(null);
const orderToDelete = ref(null);
const newStatus = ref('');

// Fecha actual formateada para el input date
const today = computed(() => {
  const date = new Date();
  return date.toISOString().split('T')[0];
});

// Calcular array de páginas para la paginación
const pagesArray = computed(() => {
  const pages = [];
  const maxVisiblePages = 5;
  
  if (totalPages.value <= maxVisiblePages) {
    // Si hay menos páginas que el máximo visible, mostrar todas
    for (let i = 1; i <= totalPages.value; i++) {
      pages.push(i);
    }
  } else {
    // Calcular rango de páginas a mostrar
    let startPage = Math.max(1, currentPage.value - Math.floor(maxVisiblePages / 2));
    let endPage = startPage + maxVisiblePages - 1;
    
    if (endPage > totalPages.value) {
      endPage = totalPages.value;
      startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
      pages.push(i);
    }
  }
  
  return pages;
});

// Formatear fecha para mostrar
const formatDate = (dateString) => {
  if (!dateString) return 'No especificada';
  
  const date = new Date(dateString);
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Traducir estado de pedido
const translateStatus = (status) => {
  if (!status) return '';
  
  const statusMap = {
    'pending': 'Pendiente',
    'paid': 'Pagado',
    'processing': 'Procesando',
    'shipped': 'Enviado',
    'delivered': 'Entregado',
    'cancelled': 'Cancelado'
  };
  
  return statusMap[status.toLowerCase()] || status;
};

// Truncar texto largo
const truncateText = (text, maxLength) => {
  if (!text) return '';
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength) + '...';
};

// Obtener pedidos desde el backend
const fetchOrders = async (page) => {
  loading.value = true;
  error.value = null;
  currentPage.value = page;
  
  try {
    const response = await adminService.getOrders(
      page,
      ordersPerPage.value,
      searchQuery.value || null, // Añadir searchQuery como parámetro
      statusFilter.value || null,
      startDate.value || null,
      endDate.value || null
    );
    
    orders.value = response.orders;
    totalOrders.value = response.pagination.total;
    totalPages.value = response.pagination.pages;
    
    // Ajustar página actual si es mayor que el total de páginas
    if (currentPage.value > totalPages.value && totalPages.value > 0) {
      changePage(totalPages.value);
    }
  } catch (err) {
    console.error('Error al obtener pedidos:', err);
    error.value = 'No se pudieron cargar los pedidos. Por favor, inténtalo de nuevo.';
  } finally {
    loading.value = false;
  }
};

// Cambiar página
const changePage = (page) => {
  if (page < 1 || page > totalPages.value) return;
  fetchOrders(page);
};

// Resetear filtros
const resetFilters = () => {
  searchQuery.value = '';
  statusFilter.value = '';
  startDate.value = '';
  endDate.value = '';
  fetchOrders(1);
};

// Mostrar modal de cambio de estado
const openStatusModal = (order) => {
  orderToEdit.value = order;
  newStatus.value = order.status; // Inicializar con el estado actual
  showStatusModal.value = true;
};

// Actualizar estado del pedido
const updateOrderStatus = async () => {
  try {
    await adminService.updateOrderStatus(orderToEdit.value.id, newStatus.value);
    
    // Actualizar el pedido en la lista local
    const index = orders.value.findIndex(o => o.id === orderToEdit.value.id);
    if (index !== -1) {
      orders.value[index].status = newStatus.value;
    }
    
    showStatusModal.value = false;
  } catch (err) {
    console.error('Error al actualizar estado del pedido:', err);
    alert('Error al actualizar el estado del pedido: ' + (err.response?.data?.error || err.message));
  }
};

// Mostrar modal de eliminación
const confirmDeleteOrder = (order) => {
  orderToDelete.value = order;
  showDeleteModal.value = true;
};

// Eliminar pedido
const deleteOrder = async () => {
  try {
    await adminService.deleteOrder(orderToDelete.value.id);
    
    // Eliminar el pedido de la lista local
    orders.value = orders.value.filter(o => o.id !== orderToDelete.value.id);
    
    showDeleteModal.value = false;
    
    // Si no quedan pedidos en la página actual y no es la primera página,
    // ir a la página anterior
    if (orders.value.length === 0 && currentPage.value > 1) {
      changePage(currentPage.value - 1);
    } else {
      // Refrescar la página actual para actualizar la paginación
      fetchOrders(currentPage.value);
    }
  } catch (err) {
    console.error('Error al eliminar pedido:', err);
    alert('Error al eliminar el pedido: ' + (err.response?.data?.error || err.message));
  }
};

// Cargar pedidos cuando se monte el componente
onMounted(() => {
  fetchOrders(1);
});
</script>

<style scoped>
.admin-orders {
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
  margin-bottom: 1.5rem;
}

.admin-controls {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.search-filter {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.search-input, .filter-select {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.search-input {
  min-width: 250px;
}

.date-filters {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  align-items: flex-end;
}

.filter-group {
  display: flex;
  flex-direction: column;
}

.filter-group label {
  margin-bottom: 0.25rem;
  font-size: 0.9rem;
}

.btn-search, .btn-filter, .btn-reset, .btn-retry {
  padding: 0.75rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
}

.btn-search, .btn-filter {
  background-color: #4caf50;
  color: white;
}

.btn-search:hover, .btn-filter:hover {
  background-color: #3d8b40;
}

.btn-reset, .btn-retry {
  background-color: #f0f0f0;
  color: #333;
}

.btn-reset:hover, .btn-retry:hover {
  background-color: #e0e0e0;
}

.loading-message, .error-message, .no-orders {
  text-align: center;
  padding: 2rem;
  background-color: #f9f9f9;
  border-radius: 8px;
  margin-bottom: 2rem;
}

.error-message {
  color: #d32f2f;
  background-color: #ffebee;
}

.orders-table {
  overflow-x: auto;
  margin-bottom: 2rem;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 1rem;
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

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.85rem;
  font-weight: 500;
}

.status-pending {
  background-color: #fff3cd;
  color: #856404;
}

.status-paid {
  background-color: #d1ecf1;
  color: #0c5460;
}

.status-processing {
  background-color: #d4edda;
  color: #155724;
}

.status-shipped {
  background-color: #cce5ff;
  color: #004085;
}

.status-delivered {
  background-color: #d4edda;
  color: #155724;
}

.status-cancelled {
  background-color: #f8d7da;
  color: #721c24;
}

.btn-edit, .btn-delete {
  padding: 0.5rem 0.75rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.85rem;
  margin-right: 0.5rem;
}

.btn-edit {
  background-color: #2196f3;
  color: white;
}

.btn-edit:hover {
  background-color: #0b7dda;
}

.btn-delete {
  background-color: #f44336;
  color: white;
}

.btn-delete:hover {
  background-color: #d32f2f;
}

.pagination {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 2rem;
}

.pagination button {
  padding: 0.5rem 0.75rem;
  border: 1px solid #ddd;
  background-color: white;
  cursor: pointer;
  border-radius: 4px;
}

.pagination button.active {
  background-color: #4caf50;
  color: white;
  border-color: #4caf50;
}

.pagination button:hover:not(.active):not(:disabled) {
  background-color: #f0f0f0;
}

.pagination button:disabled {
  color: #ccc;
  cursor: not-allowed;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  padding: 2rem;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  position: relative;
}

.close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  font-size: 1.5rem;
  cursor: pointer;
}

.order-details, .delete-confirmation {
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.btn-primary, .btn-cancel, .btn-delete {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
}

.btn-primary {
  background-color: #4caf50;
  color: white;
}

.btn-primary:hover {
  background-color: #3d8b40;
}

.btn-cancel {
  background-color: #f0f0f0;
  color: #333;
}

.btn-cancel:hover {
  background-color: #e0e0e0;
}

@media (max-width: 768px) {
  .admin-controls {
    flex-direction: column;
  }
  
  .search-filter, .date-filters {
    width: 100%;
  }
  
  .search-input {
    width: 100%;
  }
  
  th, td {
    padding: 0.75rem 0.5rem;
    font-size: 0.9rem;
  }
  
  .btn-edit, .btn-delete {
    padding: 0.4rem 0.6rem;
    font-size: 0.8rem;
  }
}
</style>




