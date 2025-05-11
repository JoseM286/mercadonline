<script setup>
import { ref } from 'vue';

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
const formStatus = ref({
  message: '',
  isError: false,
  isSuccess: false
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
    isSuccess: false
  };
  
  // Validar campos requeridos
  if (!registerForm.value.email.trim() || !isValidEmail(registerForm.value.email)) {
    formStatus.value = {
      message: 'Por favor, introduce un correo electrónico válido',
      isError: true
    };
    return;
  }
  
  if (!registerForm.value.password.trim()) {
    formStatus.value = {
      message: 'Por favor, introduce una contraseña',
      isError: true
    };
    return;
  }
  
  if (registerForm.value.password !== registerForm.value.confirmPassword) {
    formStatus.value = {
      message: 'Las contraseñas no coinciden',
      isError: true
    };
    return;
  }
  
  try {
    // Aquí iría la lógica para enviar el formulario a través de una API
    // Por ahora, simulamos una respuesta exitosa
    
    // Simulación de envío (reemplazar con llamada API real)
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    // Mostrar mensaje de éxito
    formStatus.value = {
      message: 'Registro exitoso. ¡Bienvenido a MercadonLine!',
      isSuccess: true
    };
    
    // Aquí se redireccionaría al usuario a la página principal
    // router.push('/');
    
  } catch (error) {
    // Manejar error
    formStatus.value = {
      message: 'Error al registrar la cuenta. Por favor, inténtalo de nuevo.',
      isError: true
    };
    console.error('Error al registrar:', error);
  }
};
</script>

<template>
  <div class="page-container-narrow">
    <div class="page-header-with-logo">
      <h1>Crear cuenta</h1>
      <img src="@/assets/images/logo_verde.png" alt="Logo MercadonLine" class="page-logo" />
    </div>
    
    <div class="page-content">
      <p class="page-intro-centered">
        Crea tu cuenta en MercadonLine para disfrutar de una experiencia de compra personalizada.
      </p>
      
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
          
          <!-- Campos opcionales -->
          <div class="form-group">
            <label for="name">Nombre completo</label>
            <input 
              type="text" 
              id="name" 
              v-model="registerForm.name" 
              placeholder="Tu nombre y apellidos"
            />
          </div>
          
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
            <button type="submit" class="register-button">Crear cuenta</button>
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

.required-fields-note {
  font-size: 0.9rem;
  color: #666;
  margin-top: var(--spacing-md);
  margin-bottom: var(--spacing-md);
}

.form-actions {
  margin-top: var(--spacing-lg);
  text-align: center;
}

.register-button {
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

.register-button:hover {
  background-color: #3a7a23;
}

.form-footer {
  margin-top: var(--spacing-lg);
  text-align: center;
  font-size: 0.9rem;
}

.login-link {
  color: #2c5e1a;
  text-decoration: none;
  font-weight: bold;
}

.login-link:hover {
  text-decoration: underline;
}
</style>