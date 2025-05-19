<script setup>
import { ref, onMounted, onBeforeUnmount, watch, computed } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  text: {
    type: String,
    required: true
  },
  isOpen: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'info', // 'info', 'success', 'error', 'warning'
    validator: (value) => ['info', 'success', 'error', 'warning'].includes(value)
  },
  autoClose: {
    type: Boolean,
    default: false
  },
  autoCloseTime: {
    type: Number,
    default: 3000 // 3 segundos
  }
});

const emit = defineEmits(['close']);

const isVisible = ref(props.isOpen);
let autoCloseTimeout = null;

// Método para cerrar el modal
const closeModal = () => {
  isVisible.value = false;
  emit('close');
};

// Cerrar modal con tecla Escape
const handleKeyDown = (event) => {
  if (event.key === 'Escape' && isVisible.value) {
    closeModal();
  }
};

// Configurar cierre automático si está habilitado
const setupAutoClose = () => {
  if (props.autoClose && isVisible.value) {
    clearTimeout(autoCloseTimeout);
    autoCloseTimeout = setTimeout(() => {
      closeModal();
    }, props.autoCloseTime);
  }
};

// Observar cambios en la prop isOpen
watch(() => props.isOpen, (newValue) => {
  isVisible.value = newValue;
  if (newValue && props.autoClose) {
    setupAutoClose();
  }
});

// Configurar event listeners al montar el componente
onMounted(() => {
  document.addEventListener('keydown', handleKeyDown);
  if (props.isOpen && props.autoClose) {
    setupAutoClose();
  }
});

// Limpiar event listeners al desmontar el componente
onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleKeyDown);
  clearTimeout(autoCloseTimeout);
});

// Determinar la clase de estilo según el tipo
const modalTypeClass = computed(() => {
  return {
    'modal-info': props.type === 'info',
    'modal-success': props.type === 'success',
    'modal-error': props.type === 'error',
    'modal-warning': props.type === 'warning'
  };
});
</script>

<template>
  <Teleport to="body">
    <div v-if="isVisible" class="modal-overlay" @click="closeModal">
      <div class="modal-container" :class="modalTypeClass" @click.stop>
        <div class="modal-header">
          <h2 class="modal-title">{{ title }}</h2>
        </div>
        <div class="modal-content">
          <p>{{ text }}</p>
        </div>
        <div class="modal-footer">
          <button class="modal-button" @click="closeModal">Aceptar</button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  animation: fadeIn 0.3s ease;
}

.modal-container {
  background-color: white;
  border-radius: 20px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  animation: slideIn 0.3s ease;
  border-top: 5px solid #3a7a23; /* Color por defecto */
}

.modal-info {
  border-top-color: #3a7a23; /* Verde para info */
}

.modal-success {
  border-top-color: #2e7d32; /* Verde oscuro para éxito */
}

.modal-error {
  border-top-color: #d32f2f; /* Rojo para error */
}

.modal-warning {
  border-top-color: #f57c00; /* Naranja para advertencia */
}

.modal-header {
  align-items: center;
  padding: 16px 20px;
}

.modal-title {
  margin: 0;
  text-align: center;
  font-size: 1.25rem;
  color: #333;
}

.modal-close:hover {
  color: #333;
}

.modal-content {
  padding: 20px;
  line-height: 1.5;
  text-align: center;
}

.modal-footer {
  padding: 16px 20px;
  display: flex;
  justify-content: center;
}

.modal-button {
  padding: 8px 16px;
  background-color: #3a7a23;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.modal-button:hover {
  background-color: #4a9a2e;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideIn {
  from { transform: translateY(-20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
</style>
