<script setup>
import { ref } from 'vue';

// Variables reactivas para el formulario
const contactForm = ref({
  title: '',
  email: '',
  message: ''
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
  
  // Validar campos
  if (!contactForm.value.title.trim()) {
    formStatus.value = {
      message: 'Por favor, introduce un título para tu mensaje',
      isError: true
    };
    return;
  }
  
  if (!contactForm.value.email.trim() || !isValidEmail(contactForm.value.email)) {
    formStatus.value = {
      message: 'Por favor, introduce un correo electrónico válido',
      isError: true
    };
    return;
  }
  
  if (!contactForm.value.message.trim()) {
    formStatus.value = {
      message: 'Por favor, escribe tu mensaje',
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
      message: '¡Gracias por contactar con nosotros! Te responderemos lo antes posible.',
      isSuccess: true
    };
    
    // Resetear el formulario
    contactForm.value = {
      title: '',
      email: '',
      message: ''
    };
    
  } catch (error) {
    // Manejar error
    formStatus.value = {
      message: 'Ha ocurrido un error al enviar tu mensaje. Por favor, inténtalo de nuevo más tarde.',
      isError: true
    };
    console.error('Error al enviar el formulario:', error);
  }
};
</script>

<template>
  <div class="page-container-narrow">
    <div class="page-header-with-logo">
      <h1>Contáctanos</h1>
      <img src="@/assets/images/logo_verde.png" alt="Logo MercadonLine" class="page-logo" />
    </div>
    
    <div class="page-content">
      <p class="page-intro-centered">
        ¿Tienes alguna pregunta, sugerencia o comentario? Estamos aquí para ayudarte. 
        Completa el siguiente formulario y nos pondremos en contacto contigo lo antes posible.
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
          
          <div class="form-group">
            <label for="title">Asunto</label>
            <input 
              type="text" 
              id="title" 
              v-model="contactForm.title" 
              placeholder="Escribe el asunto de tu mensaje"
              required
            />
          </div>
          
          <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input 
              type="email" 
              id="email" 
              v-model="contactForm.email" 
              placeholder="tu@email.com"
              required
            />
          </div>
          
          <div class="form-group">
            <label for="message">Mensaje</label>
            <textarea 
              id="message" 
              v-model="contactForm.message" 
              placeholder="Escribe tu mensaje aquí..."
              rows="6"
              required
            ></textarea>
          </div>
          
          <button type="submit" class="contact-submit-button">Enviar mensaje</button>
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
}

.page-intro-centered {
  text-align: center;
  margin-left: auto;
  margin-right: auto;
}

.contact-submit-button {
  display: block;
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
  text-align: center;
  margin-top: var(--spacing-md);
}

.contact-submit-button:hover {
  background-color: #3a7a23;
}
</style>




