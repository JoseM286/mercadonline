<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import authService from '@/services/authService';

const router = useRouter();

// Variables reactivas para el formulario
const registerForm = ref({
  email: '',
  password: '',
  confirmPassword: '',
  name: '',
  address: '',
  phone: ''
});

// Variable para mostrar mensajes de estado
const formStatus = reactive({
  message: '',
  isError: false,
  isSuccess: false,
  isLoading: false
});

// Función para validar el email
const isValidEmail = (email) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
};

// Función para manejar el envío del formulario
const handleSubmit = async () => {
  // Resetear el estado del formulario
  formStatus.message = '';
  formStatus.isError = false;
  formStatus.isSuccess = false;
  formStatus.isLoading = true;
  
  // Validar campos requeridos
  if (!registerForm.value.email.trim() || !isValidEmail(registerForm.value.email)) {
    formStatus.message = 'Por favor, introduce un correo electrónico válido';
    formStatus.isError = true;
    formStatus.isLoading = false;
    return;
  }
  
  if (!registerForm.value.password.trim()) {
    formStatus.message = 'Por favor, introduce una contraseña';
    formStatus.isError = true;
    formStatus.isLoading = false;
    return;
  }
  
  if (registerForm.value.password !== registerForm.value.confirmPassword) {
    formStatus.message = 'Las contraseñas no coinciden';
    formStatus.isError = true;
    formStatus.isLoading = false;
    return;
  }

  if (!registerForm.value.name.trim()) {
    formStatus.message = 'Por favor, introduce tu nombre';
    formStatus.isError = true;
    formStatus.isLoading = false;
    return;
  }
  
  try {
    // Preparar datos para enviar al servidor
    const userData = {
      email: registerForm.value.email,
      password: registerForm.value.password,
      name: registerForm.value.name,
      address: registerForm.value.address || null,
      phone: registerForm.value.phone || null
    };
    
    // Enviar datos al servidor
    const response = await authService.register(userData);
    
    // Mostrar mensaje de éxito
    formStatus.message = 'Registro exitoso. ¡Bienvenido a MercadonLine!';
    formStatus.isSuccess = true;
    
    // Esperar un momento antes de redirigir
    setTimeout(() => {
      // Redirigir al usuario a la página de login
      router.push('/login');
    }, 2000);
    
  } catch (error) {
    // Manejar error
    formStatus.message = error.message || 'Error al registrar la cuenta. Por favor, inténtalo de nuevo.';
    formStatus.isError = true;
    console.error('Error al registrar:', error);
  } finally {
    formStatus.isLoading = false;
  }
};
</script>

<template>
  <div class="register-page">
    <div class="container">
      <div class="page-intro-centered">
        <h1>Crear cuenta</h1>
        <p>Únete a MercadonLine para disfrutar de una experiencia de compra única</p>
      </div>
      
      <div class="centered-container">
        <form @submit.prevent="handleSubmit" class="section-card-accent">
          <!-- Mensaje de estado del formulario -->
          <div v-if="formStatus.message" 
               :class="['status-message', 
                       {'error-message': formStatus.isError, 
                        'success-message': formStatus.isSuccess}]">
            {{ formStatus.message }}
          </div>
          
          <!-- Campos requeridos -->
          <div class="form-group">
            <label for="email">Correo electrónico *</label>
            <input 
              type="email" 
              id="email" 
              v-model="registerForm.email" 
              placeholder="tu@email.com"
              required
            />
          </div>
          
          <div class="form-group">
            <label for="password">Contraseña *</label>
            <input 
              type="password" 
              id="password" 
              v-model="registerForm.password" 
              placeholder="Tu contraseña"
              required
            />
          </div>
          
          <div class="form-group">
            <label for="confirmPassword">Confirmar contraseña *</label>
            <input 
              type="password" 
              id="confirmPassword" 
              v-model="registerForm.confirmPassword" 
              placeholder="Repite tu contraseña"
              required
            />
          </div>
          
          <div class="form-group">
            <label for="name">Nombre completo *</label>
            <input 
              type="text" 
              id="name" 
              v-model="registerForm.name" 
              placeholder="Tu nombre y apellidos"
              required
            />
          </div>
          
          <!-- Campos opcionales -->
          <div class="form-group">
            <label for="address">Dirección</label>
            <input 
              type="text" 
              id="address" 
              v-model="registerForm.address" 
              placeholder="Tu dirección completa"
            />
          </div>
          
          <div class="form-group">
            <label for="phone">Teléfono</label>
            <input 
              type="tel" 
              id="phone" 
              v-model="registerForm.phone" 
              placeholder="Tu número de teléfono"
            />
          </div>
          
          <p class="required-fields-note">* Campos obligatorios</p>
          
          <div class="form-actions">
            <button 
              type="submit" 
              class="register-button" 
              :disabled="formStatus.isLoading"
            >
              {{ formStatus.isLoading ? 'Creando cuenta...' : 'Crear cuenta' }}
            </button>
          </div>
          
          <div class="form-footer">
            <p>¿Ya tienes cuenta? <router-link to="/login" class="login-link">Inicia sesión aquí</router-link></p>
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
  max-width: 500px;
  padding: var(--spacing-lg);
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
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 10px;
  border: 1px solid var(--color-border);
  border-radius: var(--border-radius);
}

.required-fields-note {
  font-size: 0.9em;
  color: var(--color-text-light);
  margin-top: var(--spacing-md);
}

.form-actions {
  margin-top: var(--spacing-lg);
}

.register-button {
  width: 100%;
  padding: 12px;
  background-color: var(--color-primary);
  color: white;
  border: none;
  border-radius: var(--border-radius);
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.register-button:hover {
  background-color: var(--color-primary-dark);
}

.register-button:disabled {
  background-color: var(--color-primary-light);
  cursor: not-allowed;
}

.form-footer {
  margin-top: var(--spacing-md);
  text-align: center;
}

.login-link {
  color: var(--color-primary);
  text-decoration: none;
  font-weight: 500;
}

.login-link:hover {
  text-decoration: underline;
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
</style>
