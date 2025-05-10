<script setup>
// Importaciones y lógica aquí
</script>

<template>
  <div class="page-container">
    <div class="page-header">
      <h1>Bienvenido a MercadonLine</h1>
    </div>
    
    <p class="page-intro">Tu tienda online de confianza</p>
    
    <section>
      <h2 class="section-title">Productos destacados</h2>
      <div class="products-grid">
        <!-- Aquí irán los productos destacados -->
        <p>Cargando productos...</p>
      </div>
    </section>
  </div>
</template>

<style scoped>
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: var(--spacing-md);
  margin-top: var(--spacing-md);
  width: 100%;
}
</style>


