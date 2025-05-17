<script setup>
import { useRoute } from 'vue-router';
import { computed } from 'vue';

// Obtener la ruta actual
const route = useRoute();

// Comprobar si estoy en estas paginas para no poner el volver arriba
const isContactPage = computed(() => route.path === '/contact');
const isProfilePage = computed(() => route.path === '/profile');

// Función para volver arriba
const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
};
</script>

<template>
    <button v-if="!isContactPage && !isProfilePage" class="scroll-top-button" @click="scrollToTop">
      Volver arriba ↑
    </button>
  <footer class="footer">
    
    <div class="footer-container">
      <div class="footer-left">
        <router-link to="/about">Sobre nosotros</router-link>
      </div>
      
      <div class="copyright">
      <h4>&copy; 2025 MercadonLine</h4>
    </div>
      
      <div class="footer-right">
        <router-link to="/contact">Contáctanos</router-link>
      </div>
    </div>
    
  </footer>
</template>

<style scoped>
.footer {
  background-color: #2c5e1a; /* Verde oscuro */
  color: white;
  padding: var(--spacing-md) var(--spacing-lg);
  width: 100%;
}

.scroll-top-button {
  display: block;
  width: 100%;
  padding: var(--spacing-sm);
  background-color: #3a7a23; /* Verde un poco más claro */
  color: white;
  border: none;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.3s ease;
  text-align: center;
}

.scroll-top-button:hover {
  background-color: #4a9a2e; /* Verde aún más claro al pasar el cursor */
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



