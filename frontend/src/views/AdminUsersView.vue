<template>
  <div class="admin-users">
    <h1>Gestión de Usuarios</h1>
    <p>Administra los usuarios de la plataforma, cambia roles o elimina usuarios.</p>
    
    <div class="admin-controls">
      <div class="search-filter">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar por nombre o email..."
          class="search-input"
          @keyup.enter="filterUsers"
        >
        <select v-model="roleFilter" class="filter-select" @change="filterUsers">
          <option value="">Todos los roles</option>
          <option value="ROLE_USER">Usuario</option>
          <option value="ROLE_ADMIN">Administrador</option>
        </select>
        <button class="btn-search" @click="filterUsers">Buscar</button>
      </div>
    </div>
    
    <!-- Spinner de carga -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Cargando usuarios...</p>
    </div>
    
    <!-- Mensaje de error -->
    <div v-else-if="error" class="error-container">
      <h3>Error al cargar usuarios</h3>
      <p>{{ error }}</p>
      <button @click="fetchUsers" class="btn-retry">Reintentar</button>
    </div>
    
    <!-- Tabla de usuarios -->
    <div v-else-if="filteredUsers.length" class="users-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Fecha de Registro</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in filteredUsers" :key="user.id">
            <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>
              <span :class="'role-badge ' + (user.role === 'ROLE_ADMIN' ? 'role-admin' : 'role-user')">
                {{ user.role === 'ROLE_ADMIN' ? 'Administrador' : 'Usuario' }}
              </span>
            </td>
            <td>{{ formatDate(user.createdAt) }}</td>
            <td>
              <button
                class="btn-role"
                @click="toggleUserRole(user)"
                :disabled="isCurrentUser(user)"
              >
                {{ user.role === 'ROLE_ADMIN' ? 'Hacer Usuario' : 'Hacer Admin' }}
              </button>
              <button
                class="btn-delete"
                @click="confirmDeleteUser(user)"
                :disabled="isCurrentUser(user)"
              >
                Eliminar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- No hay usuarios -->
    <div v-else class="no-users">
      <p>No se encontraron usuarios con los criterios de búsqueda actuales.</p>
      <button @click="resetFilters" class="btn-reset">Resetear filtros</button>
    </div>
    
    <!-- Modal Confirmación Cambio de Rol -->
    <div v-if="showRoleModal" class="modal">
      <div class="modal-content">
        <span class="close" @click="showRoleModal = false">&times;</span>
        <h2>Confirmar Cambio de Rol</h2>
        <p>
          ¿Estás seguro de que deseas cambiar el rol de "{{ userToEdit?.name }}" de
          {{ userToEdit?.role === 'ROLE_ADMIN' ? 'Administrador a Usuario' : 'Usuario a Administrador' }}?
        </p>
        <div class="form-actions">
          <button type="button" class="btn-cancel" @click="showRoleModal = false">Cancelar</button>
          <button type="button" class="btn-submit" @click="changeUserRole">Confirmar</button>
        </div>
      </div>
    </div>
    
    <!-- Modal Confirmación Eliminar -->
    <div v-if="showDeleteModal" class="modal">
      <div class="modal-content">
        <span class="close" @click="showDeleteModal = false">&times;</span>
        <h2>Confirmar Eliminación</h2>
        <p>¿Estás seguro de que deseas eliminar al usuario "{{ userToDelete?.name }}"?</p>
        <p class="warning">Esta acción no se puede deshacer y eliminará todos los datos asociados a este usuario.</p>
        <div class="form-actions">
          <button type="button" class="btn-cancel" @click="showDeleteModal = false">Cancelar</button>
          <button type="button" class="btn-delete" @click="deleteUser">Eliminar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/router/auth';
import adminService from '@/services/adminService';

const authStore = useAuthStore();
const currentUser = computed(() => authStore.user);

// Estado para la lista de usuarios
const users = ref([]);
const loading = ref(true);
const error = ref(null);

// Estado para filtros
const searchQuery = ref('');
const roleFilter = ref('');
const filteredUsers = computed(() => {
  if (!searchQuery.value && !roleFilter.value) return users.value;
  
  return users.value.filter(user => {
    const matchesSearch = !searchQuery.value ||
      user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      user.email.toLowerCase().includes(searchQuery.value.toLowerCase());
    
    const matchesRole = !roleFilter.value || user.role === roleFilter.value;
    
    return matchesSearch && matchesRole;
  });
});

// Estado modales
const showRoleModal = ref(false);
const showDeleteModal = ref(false);
const userToEdit = ref(null);
const userToDelete = ref(null);

// Verificar si es el usuario actual
const isCurrentUser = (user) => {
  return user.id === currentUser.value?.id;
};

// Formatear fecha
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('es-ES');
};

// Obtener usuarios desde el backend
const fetchUsers = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    const response = await adminService.getUsers();
    users.value = response.users || [];
  } catch (err) {
    console.error('Error al obtener usuarios:', err);
    error.value = 'No se pudieron cargar los usuarios. Por favor, inténtalo de nuevo.';
  } finally {
    loading.value = false;
  }
};

// Filtrar usuarios
const filterUsers = () => {
  // La filtración se hace a través del computed filteredUsers
};

// Resetear filtros
const resetFilters = () => {
  searchQuery.value = '';
  roleFilter.value = '';
};

// Cambiar rol de usuario
const toggleUserRole = (user) => {
  userToEdit.value = user;
  showRoleModal.value = true;
};

const changeUserRole = async () => {
  try {
    const newRole = userToEdit.value.role === 'ROLE_ADMIN' ? 'ROLE_USER' : 'ROLE_ADMIN';
    await adminService.changeUserRole(userToEdit.value.id, newRole);
    
    // Actualizar usuario en la lista local
    const index = users.value.findIndex(u => u.id === userToEdit.value.id);
    if (index !== -1) {
      users.value[index].role = newRole;
    }
    
    showRoleModal.value = false;
  } catch (err) {
    console.error('Error al cambiar rol de usuario:', err);
    alert('Error al cambiar el rol del usuario: ' + (err.response?.data?.error || err.message));
  }
};

// Confirmar eliminación
const confirmDeleteUser = (user) => {
  userToDelete.value = user;
  showDeleteModal.value = true;
};

// Eliminar usuario
const deleteUser = async () => {
  try {
    await adminService.deleteUser(userToDelete.value.id);
    
    // Eliminar usuario de la lista local
    users.value = users.value.filter(u => u.id !== userToDelete.value.id);
    
    showDeleteModal.value = false;
  } catch (err) {
    console.error('Error al eliminar usuario:', err);
    alert('Error al eliminar el usuario: ' + (err.response?.data?.error || err.message));
  }
};

// Cargar usuarios cuando se monte el componente
onMounted(() => {
  fetchUsers();
});
</script>

<style scoped>
.admin-users {
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
  justify-content: flex-end;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.btn-search, .btn-role, .btn-retry, .btn-reset, .btn-submit, .btn-cancel, .btn-delete {
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.2s;
}

.btn-search {
  background-color: #4a90e2;
  color: white;
}

.btn-search:hover {
  background-color: #3a80d2;
}

.btn-role {
  background-color: #f39c12;
  color: white;
  margin-right: 0.5rem;
}

.btn-role:hover {
  background-color: #e08e0b;
}

.btn-role:disabled, .btn-delete:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-retry, .btn-reset {
  background-color: #6c757d;
  color: white;
}

.btn-retry:hover, .btn-reset:hover {
  background-color: #5a6268;
}

.btn-submit {
  background-color: #2c5e1a;
  color: white;
}

.btn-submit:hover {
  background-color: #3a7a23;
}

.btn-cancel {
  background-color: #6c757d;
  color: white;
  margin-right: 1rem;
}

.btn-cancel:hover {
  background-color: #5a6268;
}

.btn-delete {
  background-color: #e74c3c;
  color: white;
}

.btn-delete:hover {
  background-color: #d73c2c;
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

.users-table {
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

.role-badge {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.85rem;
  font-weight: bold;
}

.role-admin {
  background-color: #d4edda;
  color: #155724;
}

.role-user {
  background-color: #cce5ff;
  color: #004085;
}

.loading-container, .no-users, .error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 200px;
  text-align: center;
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
  background-color: #fff0f0;
  border-radius: 8px;
  padding: 2rem;
  margin: 2rem 0;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  border-radius: 8px;
  padding: 2rem;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
}

.close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  font-size: 1.5rem;
  cursor: pointer;
  color: #999;
}

.close:hover {
  color: #333;
}

.warning {
  color: #e74c3c;
  font-weight: bold;
  margin-top: 1rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 2rem;
}

@media (max-width: 768px) {
  .admin-controls {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-filter {
    flex-direction: column;
  }
  
  .modal-content {
    width: 95%;
    padding: 1.5rem;
  }
}
</style>