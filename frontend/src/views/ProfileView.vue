<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/router/auth';

const router = useRouter();
const authStore = useAuthStore();
const user = ref(authStore.user || {});

// Datos del perfil
const profile = ref({
  name: user.value.name || '',
  email: user.value.email || '',
  address: user.value.address || '',
  phone: user.value.phone || ''
});

// Estado del formulario
const formStatus = ref({
  message: '',
  isError: false,
  isSuccess: false,
  loading: false
});

// Función para cerrar sesión
const handleLogout = async () => {
  try {
    // Llamar al servicio de autenticación para cerrar sesión
    // await authService.logout();
    
    // Actualizar el store
    authStore.logout();
    
    // Redireccionar a la página principal
    router.push('/');
  } catch (error) {
    console.error('Error al cerrar sesión:', error);
    // Incluso si hay error, cerramos sesión localmente
    authStore.logout();
    router.push('/');
  }
};

// Función para actualizar el perfil
const updateProfile = async () => {
  formStatus.value.loading = true;
  
  try {
    // Aquí iría la llamada a la API para actualizar el perfil
    // await userService.updateProfile(profile.value);
    
    // Simulamos una actualización exitosa
    setTimeout(() => {
      formStatus.value = {
        message: 'Perfil actualizado correctamente',
        isSuccess: true,
        loading: false
      };
    }, 1000);
  } catch (error) {
    formStatus.value = {
      message: 'Error al actualizar el perfil',
      isError: true,
      loading: false
    };
  }
};
</script>

<template>
  <div class="page-container">
    <div class="page-header">
      <h1>Mi Perfil</h1>
    </div>

    <div class="page-content">
      <div class="profile-container">
        <div class="profile-sidebar">
          <div class="user-info">
            <div class="avatar">👤</div>
            <h3>{{ profile.name }}</h3>
            <p>{{ profile.email }}</p>
          </div>
          <div class="sidebar-menu">
            <button class="menu-item active">Información Personal</button>
            <button class="menu-item">Mis Pedidos</button>
            <button class="menu-item">Direcciones</button>
            <button class="menu-item">Métodos de Pago</button>
            <button class="menu-item logout" @click="handleLogout">Cerrar Sesión</button>
          </div>
        </div>

        <div class="profile-content">
          <div class="section-card">
            <h2>Información Personal</h2>
            
            <!-- Mensaje de estado del formulario -->
            <div v-if="formStatus.message"
                 :class="['status-message',
                         {'error-message': formStatus.isError,
                          'success-message': formStatus.isSuccess}]">
              {{ formStatus.message }}
            </div>
            
            <form @submit.prevent="updateProfile">
              <div class="form-group">
                <label for="name">Nombre completo</label>
                <input
                  type="text"
                  id="name"
                  v-model="profile.name"
                  placeholder="Tu nombre completo"
                />
              </div>
              
              <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input
                  type="email"
                  id="email"
                  v-model="profile.email"
                  placeholder="tu@email.com"
                  disabled
                />
                <small>El correo electrónico no se puede cambiar</small>
              </div>
              
              <div class="form-group">
                <label for="address">Dirección</label>
                <textarea
                  id="address"
                  v-model="profile.address"
                  placeholder="Tu dirección completa"
                  rows="3"
                ></textarea>
              </div>
              
              <div class="form-group">
                <label for="phone">Teléfono</label>
                <input
                  type="tel"
                  id="phone"
                  v-model="profile.phone"
                  placeholder="Tu número de teléfono"
                />
              </div>
              
              <div class="form-actions">
                <button type="submit" class="update-button" :disabled="formStatus.loading">
                  {{ formStatus.loading ? 'Actualizando...' : 'Actualizar Perfil' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.profile-container {
  display: flex;
  gap: var(--spacing-lg);
  margin-top: var(--spacing-lg);
}

.profile-sidebar {
  width: 250px;
  flex-shrink: 0;
}

.profile-content {
  flex-grow: 1;
}

.user-info {
  background-color: var(--color-bg-accent);
  padding: var(--spacing-md);
  border-radius: 8px;
  text-align: center;
  margin-bottom: var(--spacing-md);
}

.avatar {
  font-size: 3rem;
  margin-bottom: var(--spacing-sm);
}

.user-info h3 {
  margin: 0;
  margin-bottom: var(--spacing-xs);
}

.user-info p {
  margin: 0;
  color: var(--color-text-secondary);
  font-size: 0.9rem;
}

.sidebar-menu {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-xs);
}

.menu-item {
  padding: var(--spacing-sm);
  text-align: left;
  background: none;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.2s;
}

.menu-item:hover {
  background-color: var(--color-bg-hover);
}

.menu-item.active {
  background-color: var(--color-primary-light);
  color: var(--color-primary);
  font-weight: bold;
}

.menu-item.logout {
  margin-top: var(--spacing-md);
  color: var(--color-error);
}

.section-card {
  background-color: white;
  border-radius: 8px;
  padding: var(--spacing-lg);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.section-card h2 {
  margin-top: 0;
  margin-bottom: var(--spacing-md);
  color: var(--color-primary);
}

.form-group {
  margin-bottom: var(--spacing-md);
}

.form-group label {
  display: block;
  margin-bottom: var(--spacing-xs);
  font-weight: bold;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: var(--spacing-sm);
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.form-group input:disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
}

.form-group small {
  display: block;
  color: var(--color-text-secondary);
  margin-top: var(--spacing-xs);
  font-size: 0.8rem;
}

.status-message {
  padding: var(--spacing-sm);
  margin-bottom: var(--spacing-md);
  border-radius: 4px;
}

.error-message {
  background-color: #ffebee;
  color: #c62828;
  border: 1px solid #ef9a9a;
}

.success-message {
  background-color: #e8f5e9;
  color: #2e7d32;
  border: 1px solid #a5d6a7;
}

.form-actions {
  margin-top: var(--spacing-lg);
}

.update-button {
  padding: var(--spacing-sm) var(--spacing-md);
  background-color: var(--color-primary);
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.update-button:hover {
  background-color: var(--color-primary-dark);
}

.update-button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .profile-container {
    flex-direction: column;
  }
  
  .profile-sidebar {
    width: 100%;
  }
}
</style>