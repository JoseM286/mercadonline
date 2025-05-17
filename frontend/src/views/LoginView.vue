<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/router/auth';
import authService from '@/services/authService';

// Router para redireccionar después del login
const router = useRouter();

// Store de autenticación
const authStore = useAuthStore();

// Variables reactivas para el formulario
const loginForm = ref({
  email: '',
  password: ''
});

// Variable para mostrar mensajes de estado
const formStatus = ref({
  message: '',
  isError: false,
  isSuccess: false,
  loading: false
});

// Función para validar el email
const isValidEmail = (email) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
};

// Función para manejar el envío del formulario
const handleSubmit = async () => {
  // Resetear el estado del formulario
  formStatus.value = {
    message: '',
    isError: false,
    isSuccess: false,
    loading: true
  };

  // Validar campos
  if (!loginForm.value.email.trim() || !isValidEmail(loginForm.value.email)) {
    formStatus.value = {
      message: 'Por favor, introduce un correo electrónico válido',
      isError: true,
      loading: false
    };
    return;
  }

  if (!loginForm.value.password.trim()) {
    formStatus.value = {
      message: 'Por favor, introduce tu contraseña',
      isError: true,
      loading: false
    };
    return;
  }

  try {
    // Llamada real a la API de login
    const response = await authService.login({
      email: loginForm.value.email,
      password: loginForm.value.password
    });

    // Guardar datos del usuario en el store
    authStore.setUser(response.user);  // Cambiado de login a setUser

    // Login exitoso
    formStatus.value = {
      message: '',
      isSuccess: true,
      loading: false
    };

    // Redireccionar al usuario a la página que intentaba visitar o a la página principal
    setTimeout(() => {
      const redirectPath = sessionStorage.getItem('redirectAfterLogin') || '/';
      sessionStorage.removeItem('redirectAfterLogin'); // Limpiar después de usar
      router.push(redirectPath);
    }, 1000);

  } catch (error) {
    // Manejar error
    formStatus.value = {
      message: error.message || 'Error al iniciar sesión. Por favor, verifica tus credenciales.',
      isError: true,
      loading: false
    };
    console.error('Error al iniciar sesión:', error);
  }
};
</script>

<template>
  <div class="page-container-narrow">
    <div class="page-header-with-logo">
      <h1>Iniciar sesión</h1>
      <img src="@/assets/images/logo_verde.png" alt="Logo MercadonLine" class="page-logo" />
    </div>

    <div class="page-content">
      <p class="page-intro-centered">
        Accede a tu cuenta para gestionar tus pedidos y disfrutar de ofertas exclusivas.
      </p>

      <div class="centered-container">
        <form @submit.prevent="handleSubmit" class="section-card-accent">
          <!-- Overlay de carga con imagen GIF -->
          <div v-if="formStatus.loading" class="loading-overlay">
            <img src="@/assets/images/spinner.gif" alt="Cargando..." class="spinner-gif" />
            <p>Iniciando sesión...</p>
          </div>
          
          <!-- Mensaje de estado del formulario -->
          <div v-if="formStatus.message"
               :class="['status-message',
                       {'error-message': formStatus.isError,
                        'success-message': formStatus.isSuccess}]">
            {{ formStatus.message }}
          </div>

          <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input
              type="email"
              id="email"
              v-model="loginForm.email"
              placeholder="tu@email.com"
              required
            />
          </div>

          <div class="form-group">
            <label for="password">Contraseña</label>
            <input
              type="password"
              id="password"
              v-model="loginForm.password"
              placeholder="Tu contraseña"
              required
            />
          </div>

          <div class="form-actions">
            <button type="submit" class="login-button" :disabled="formStatus.loading">
              Iniciar sesión
            </button>
          </div>

          <div class="form-footer">
            <p>¿No tienes cuenta? <router-link to="/register" class="register-link">Regístrate aquí</router-link></p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.centered-container {
  display: flex;
  justify-content: center;
  width: 100%;
}

.section-card-accent {
  text-align: left;
  width: 100%;
  max-width: 400px;
  padding: var(--spacing-lg);
  position: relative;
}

.page-intro-centered {
  text-align: center;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: var(--spacing-lg);
}

.form-group {
  margin-bottom: var(--spacing-md);
}

.form-group label {
  display: block;
  margin-bottom: var(--spacing-xs);
  font-weight: bold;
}

.form-group input {
  width: 100%;
  padding: var(--spacing-sm);
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.status-message {
  padding: var(--spacing-sm);
  margin-bottom: var(--spacing-md);
  border-radius: 4px;
  text-align: center;
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
  text-align: center;
}

.login-button {
  width: 100%;
  padding: var(--spacing-md);
  background-color: #2c5e1a;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1.1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.login-button:hover {
  background-color: #3a7a23;
}

.form-footer {
  margin-top: var(--spacing-lg);
  text-align: center;
  font-size: 0.9rem;
}

.register-link {
  color: #2c5e1a;
  text-decoration: none;
  font-weight: bold;
}

.register-link:hover {
  text-decoration: underline;
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
  color: #2c5e1a;
}

.spinner-gif {
  width: 80px;
  height: 80px;
}
</style>













