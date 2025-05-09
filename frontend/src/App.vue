<script setup>
import { RouterView } from 'vue-router'
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

    <footer class="footer">
      <div class="footer-container">
        <div class="footer-left">
          <router-link to="/sobre-nosotros">Sobre nosotros</router-link>
        </div>
        
        <div class="footer-center">
          <h2>MercadonLine</h2>
        </div>
        
        <div class="footer-right">
          <router-link to="/contacto">Contáctanos</router-link>
        </div>
      </div>
      <div class="copyright">
        <p>&copy; 2025 MercadonLine - Todos los derechos reservados</p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.header {
  padding: var(--spacing-md);
  background-color: var(--color-primary);
  color: white;
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
}

.footer {
  background-color: #2c5e1a; /* Verde oscuro */
  color: white;
  padding: var(--spacing-md) var(--spacing-lg);
}

.footer-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
}

.footer-left, .footer-right, .footer-center {
  flex: 1;
}

.footer-center {
  text-align: center;
}

.footer-right {
  text-align: right;
}

.footer a {
  color: white;
  text-decoration: none;
  transition: opacity 0.3s ease;
}

.footer a:hover {
  opacity: 0.8;
  text-decoration: underline;
}

.footer h2 {
  margin: 0;
  font-size: 1.5rem;
}

.copyright {
  text-align: center;
  margin-top: var(--spacing-md);
  padding-top: var(--spacing-md);
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 768px) {
  .footer-container {
    flex-direction: column;
    gap: var(--spacing-md);
  }
  
  .footer-left, .footer-center, .footer-right {
    text-align: center;
  }
}
</style>


