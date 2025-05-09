<script setup>
// Importaciones y lógica aquí
</script>

<template>
  <div class="home">
    <h2>Bienvenido a MercadonLine</h2>
    <p>Tu tienda online de confianza</p>
    
    <section class="featured-products">
      <h3>Productos destacados</h3>
      <div class="products-grid">
        <!-- Aquí irán los productos destacados -->
        <p>Cargando productos...</p>
      </div>
    </section>
  </div>
</template>

<style scoped>
.home {
  max-width: 1000px;
  margin: 0 auto;
}

h2 {
  font-size: 2rem;
  margin-bottom: var(--spacing-md);
  color: var(--color-primary);
}

.featured-products {
  margin-top: var(--spacing-xl);
}

h3 {
  font-size: 1.5rem;
  margin-bottom: var(--spacing-md);
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: var(--spacing-md);
  margin-top: var(--spacing-md);
}
</style>

