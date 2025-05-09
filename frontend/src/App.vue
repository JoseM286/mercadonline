<script setup>
import { RouterView } from 'vue-router'
import AppFooter from './components/AppFooter.vue'
</script>

<template>
  <div class="app-container">
    <header class="header">
      <h1>MercadonLine</h1>
      <nav class="main-nav">
        <router-link to="/">Inicio</router-link>
        <router-link to="/productos">Productos</router-link>
        <router-link to="/carrito">Carrito</router-link>
      </nav>
    </header>

    <main class="main-content">
      <RouterView />
    </main>

    <AppFooter />
  </div>
</template>

<style scoped>
.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  width: 100%;
  max-width: 100%;
  margin: 0;
  padding: 0;
}

.header {
  padding: var(--spacing-md);
  background-color: var(--color-primary);
  color: white;
  width: 100%;
}

.main-nav {
  display: flex;
  gap: var(--spacing-md);
  margin-top: var(--spacing-sm);
}

.main-nav a {
  color: white;
  padding: var(--spacing-xs) var(--spacing-sm);
  border-radius: 4px;
}

.main-nav a:hover,
.main-nav a.router-link-active {
  background-color: rgba(255, 255, 255, 0.2);
}

.main-content {
  flex: 1;
  padding: var(--spacing-md);
  width: 100%;
}
</style>




