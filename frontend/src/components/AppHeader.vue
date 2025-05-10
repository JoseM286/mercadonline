<script setup>
// Importaciones y lógica aquí (se implementará más adelante)
</script>

<template>
  <header class="header">
    <!-- Franja superior -->
    <div class="top-bar">
      <div class="logo-container">
        <img src="@/assets/images/logo_verde.png" alt="Logo MercadonLine" class="header-logo" />
      </div>
      
      <div class="search-container">
        <div class="search-box">
          <input type="text" placeholder="Buscar productos..." class="search-input" />
          <select class="category-select">
            <option value="">Todas las categorías</option>
            <option value="bebidas">Bebidas</option>
            <option value="frutas-verduras">Frutas y Verduras</option>
            <option value="carnes-aves">Carnes y Aves</option>
            <option value="pescados-mariscos">Pescados y Mariscos</option>
            <option value="lacteos-huevos">Lácteos y Huevos</option>
            <option value="panaderia-reposteria">Panadería y Repostería</option>
            <option value="despensa">Despensa</option>
            <option value="hogar-limpieza">Hogar y Limpieza</option>
          </select>
          <button class="search-button">Buscar</button>
        </div>
      </div>
      
      <div class="user-actions">
        <a href="#" class="user-action-link">
          <span class="icon">👤</span>
          <span class="text">Iniciar sesión</span>
        </a>
        <a href="#" class="user-action-link">
          <span class="icon">🛒</span>
          <span class="text">Mi cesta</span>
        </a>
      </div>
    </div>
    
    <!-- Franja inferior - Categorías -->
    <nav class="categories-nav">
      <ul class="categories-list">
        <li><a href="#" class="category-link">Bebidas</a></li>
        <li><a href="#" class="category-link">Frutas y Verduras</a></li>
        <li><a href="#" class="category-link">Carnes y Aves</a></li>
        <li><a href="#" class="category-link">Pescados y Mariscos</a></li>
        <li><a href="#" class="category-link">Lácteos y Huevos</a></li>
        <li><a href="#" class="category-link">Panadería y Repostería</a></li>
        <li><a href="#" class="category-link">Despensa</a></li>
        <li><a href="#" class="category-link">Hogar y Limpieza</a></li>
      </ul>
    </nav>
  </header>
</template>

<style scoped>
.header {
  width: 100%;
  background-color: #2c5e1a;
  color: white;
}

/* Franja superior */
.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--spacing-md) var(--spacing-lg);
  background-color: #2c5e1a;
}

.logo-container {
  flex: 1;
}

.header-logo {
  height: 40px;
  width: auto;
}

.search-container {
  flex: 3;
  display: flex;
  justify-content: center;
}

.search-box {
  display: flex;
  width: 100%;
  max-width: 600px;
  border-radius: 4px;
  overflow: hidden;
}

.search-input {
  flex: 3;
  padding: var(--spacing-sm);
  border: none;
  font-size: 1rem;
}

.category-select {
  flex: 2;
  padding: var(--spacing-sm);
  border: none;
  border-left: 1px solid #ddd;
  font-size: 0.9rem;
  background-color: white;
}

.search-button {
  padding: var(--spacing-sm) var(--spacing-md);
  background-color: #3a7a23;
  color: white;
  border: none;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.search-button:hover {
  background-color: #4a9a2e;
}

.user-actions {
  flex: 1;
  display: flex;
  justify-content: flex-end;
  gap: var(--spacing-md);
}

.user-action-link {
  display: flex;
  flex-direction: column;
  align-items: center;
  color: white;
  text-decoration: none;
  transition: opacity 0.3s ease;
}

.user-action-link:hover {
  opacity: 0.8;
}

.icon {
  font-size: 1.5rem;
  margin-bottom: var(--spacing-xs);
}

.text {
  font-size: 0.8rem;
}

/* Franja inferior - Categorías */
.categories-nav {
  background-color: #3a7a23;
  padding: var(--spacing-sm) var(--spacing-lg);
}

.categories-list {
  display: flex;
  justify-content: space-between;
  list-style: none;
  margin: 0;
  padding: 0;
  overflow-x: auto;
}

.category-link {
  color: white;
  text-decoration: none;
  padding: var(--spacing-xs) var(--spacing-sm);
  white-space: nowrap;
  font-size: 0.9rem;
  transition: background-color 0.3s ease;
  border-radius: 4px;
}

.category-link:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

/* Responsive */
@media (max-width: 992px) {
  .top-bar {
    flex-direction: column;
    gap: var(--spacing-md);
  }
  
  .logo-container, .search-container, .user-actions {
    width: 100%;
  }
  
  .user-actions {
    justify-content: center;
  }
  
  .categories-list {
    flex-wrap: nowrap;
    justify-content: flex-start;
    gap: var(--spacing-md);
    padding-bottom: var(--spacing-xs);
  }
}

@media (max-width: 768px) {
  .search-box {
    flex-direction: column;
  }
  
  .category-select {
    border-left: none;
    border-top: 1px solid #ddd;
  }
}
</style>