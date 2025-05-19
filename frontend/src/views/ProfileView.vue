<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/router/auth';
import userService from '@/services/userService';
import authService from '@/services/authService';

const router = useRouter();
const authStore = useAuthStore();

// Estado del formulario
const formStatus = ref({
  message: '',
  isError: false,
  isSuccess: false,
  loading: false
});

// Datos del perfil
const profile = ref({
  name: '',
  email: '',
  address: '',
  phone: ''
});

// Cargar datos del perfil al montar el componente
onMounted(async () => {
  try {
    formStatus.value.loading = true;

    // Obtener datos del perfil desde el servidor
    const response = await userService.getProfile();

    // Actualizar el perfil con los datos recibidos
    if (response.user) {
      profile.value = {
        name: response.user.name || '',
        email: response.user.email || '',
        address: response.user.address || '',
        phone: response.user.phone || ''
      };

      // Actualizar también el store de autenticación
      authStore.setUser(response.user);
    }
  } catch (error) {
    console.error('Error al cargar el perfil:', error);
    formStatus.value.message = 'No se pudo cargar la información del perfil';
    formStatus.value.isError = true;

    // Si hay un error de autenticación, redirigir al login
    if (error.message.includes('no autenticado') || error.message.includes('Unauthorized')) {
      authStore.logout();
      router.push('/login');
    }
  } finally {
    formStatus.value.loading = false;
  }
});

// Función para cerrar sesión
const handleLogout = async () => {
  try {
    console.log('Iniciando proceso de logout desde ProfileView');

    // Intentamos cerrar sesión en el servidor primero
    try {
      console.log('Llamando a authService.logout() desde ProfileView');
      const response = await authService.logout();
      console.log('Respuesta del servidor:', response);
    } catch (error) {
      console.error('Error al cerrar sesión en el servidor desde ProfileView:', error);
      // No es crítico si falla en el servidor
    }

    // Cerramos sesión localmente después
    authStore.logout();
    console.log('Sesión local cerrada desde ProfileView');

    // Redireccionar a la página principal
    router.push('/');
  } catch (error) {
    console.error('Error general en el proceso de logout desde ProfileView:', error);
    // Aseguramos que la sesión local se cierre en cualquier caso
    authStore.logout();
    router.push('/');
  }
};

// Función para actualizar el perfil
const updateProfile = async () => {
  formStatus.value.loading = true;
  formStatus.value.message = '';
  formStatus.value.isError = false;
  formStatus.value.isSuccess = false;

  try {
    // Enviar datos actualizados al servidor
    const response = await userService.updateProfile({
      name: profile.value.name,
      address: profile.value.address,
      phone: profile.value.phone
    });

    // Actualizar el store con los datos actualizados
    if (response.user) {
      authStore.setUser(response.user);
    }

    // Mostrar mensaje de éxito
    formStatus.value.message = 'Perfil actualizado correctamente';
    formStatus.value.isSuccess = true;
  } catch (error) {
    console.error('Error al actualizar el perfil:', error);
    formStatus.value.message = error.message || 'Error al actualizar el perfil';
    formStatus.value.isError = true;
  } finally {
    formStatus.value.loading = false;
  }
};
</script>

<template>
  <div class="page-container">
    <div class="page-header-with-logo">
      <h1>Mi Perfil</h1>
      <img src="@/assets/images/logo_verde.png" alt="Logo MercadonLine" class="page-logo" />
    </div>

    <div class="page-content center">
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

            <!-- Overlay de carga con imagen GIF -->
            <div v-if="formStatus.loading" class="loading-overlay">
              <img src="@/assets/images/spinner.gif" alt="Cargando..." class="spinner-gif" />
              <p>Actualizando perfil...</p>
            </div>

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
                  required
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
  font-size: 48px;
  margin-bottom: var(--spacing-sm);
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
  transition: background-color 0.2s;
}

.menu-item:hover {
  background-color: var(--color-bg-hover);
}

.menu-item.active {
  background-color: var(--color-primary-light);
  color: var(--color-primary);
  font-weight: 500;
}

.menu-item.logout {
  margin-top: auto;
  color: var(--color-error);
}

.section-card {
  background-color: var(--color-bg-card);
  border-radius: 8px;
  padding: var(--spacing-lg);
  box-shadow: var(--shadow-sm);
  position: relative;
}

.form-group {
  margin-bottom: var(--spacing-md);
}

.form-group label {
  display: block;
  margin-bottom: var(--spacing-xs);
  font-weight: 500;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid var(--color-border);
  border-radius: var(--border-radius);
}

.form-group input:disabled {
  background-color: var(--color-bg-disabled);
  cursor: not-allowed;
}

.form-group small {
  display: block;
  color: var(--color-text-light);
  margin-top: 4px;
  font-size: 0.85em;
}

.form-actions {
  margin-top: var(--spacing-lg);
}

.update-button {
  padding: 10px 20px;
  background-color: #3a7a23;
  color: white;
  border: none;
  border-radius: var(--border-radius);
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.update-button:hover {
  background-color: #4a9a2e;
}

.update-button:disabled {
  background-color: #8abb7d;
  cursor: not-allowed;
}

.status-message {
  padding: var(--spacing-sm);
  margin-bottom: var(--spacing-md);
  border-radius: var(--border-radius);
}

.error-message {
  background-color: var(--color-error-bg);
  color: var(--color-error);
  border: 1px solid var(--color-error);
}

.success-message {
  background-color: var(--color-success-bg);
  color: var(--color-success);
  border: 1px solid var(--color-success);
}

/* Estilos para el overlay de carga */
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.7);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  z-index: 10;
  border-radius: 8px;
}

.loading-overlay p {
  margin-top: 15px;
  font-weight: bold;
  color: var(--color-primary);
}

.spinner-gif {
  width: 80px;
  height: 80px;
}
</style>



