<script setup>
defineProps({
  message: {
    type: String,
    default: 'Cargando...'
  },
  overlay: {
    type: Boolean,
    default: false
  }
});
</script>

<template>
  <div :class="['loading-container', { 'loading-overlay': overlay }]">
    <img src="@/assets/images/spinner.gif" alt="Cargando..." class="spinner-gif" />
    <p v-if="message" class="loading-message">{{ message }}</p>
  </div>
</template>

<style scoped>
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: var(--spacing-xl) 0;
}

.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.7);
  z-index: 10;
  border-radius: 8px;
}

.spinner-gif {
  width: 80px;
  height: 80px;
  margin-bottom: var(--spacing-md);
}

.loading-message {
  margin-top: 15px;
  font-weight: bold;
  color: var(--color-primary);
}
</style>