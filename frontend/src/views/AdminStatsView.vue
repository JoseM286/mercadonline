<template>
  <div class="admin-stats">
    <h1>Panel de Estadísticas</h1>
    <p>Esta vista mostrará estadísticas de ventas, stock, etc.</p>
    
    <div class="stats-container">
      <div class="stats-card">
        <h3>Ventas Totales</h3>
        <div class="stats-value">0€</div>
      </div>
      
      <div class="stats-card">
        <h3>Productos en Stock</h3>
        <div class="stats-value">0</div>
      </div>
      
      <div class="stats-card">
        <h3>Usuarios Registrados</h3>
        <div class="stats-value">0</div>
      </div>
      
      <div class="stats-card">
        <h3>Pedidos Completados</h3>
        <div class="stats-value">0</div>
      </div>
    </div>
    
    <p class="note">Esta es una versión preliminar del panel de estadísticas. Las funcionalidades se implementarán proximamente.</p>
  </div>
</template>

<script setup>
// Aquí irá la lógica para cargar estadísticas reales
</script>

<style scoped>
.admin-stats {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

h1 {
  color: #2c5e1a;
  margin-bottom: 2rem;
}

.stats-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
  margin-bottom: 2rem;
}

.stats-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
  text-align: center;
  transition: transform 0.3s ease;
}

.stats-card:hover {
  transform: translateY(-5px);
}

.stats-card h3 {
  color: #3a7a23;
  font-size: 1.2rem;
  margin-bottom: 1rem;
}

.stats-value {
  font-size: 2rem;
  font-weight: bold;
  color: #2c5e1a;
}

.note {
  color: #666;
  font-style: italic;
  text-align: center;
  margin-top: 2rem;
}
</style>