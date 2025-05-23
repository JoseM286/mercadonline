<template>
  <div class="admin-products">
    <h1>Gestión de Productos</h1>
    <p>En esta vista, los administradores podrán gestionar los productos del catálogo.</p>
    
    <div class="admin-controls">
      <button class="btn-primary">Añadir Nuevo Producto</button>
      <div class="search-filter">
        <input type="text" placeholder="Buscar productos..." class="search-input">
        <select class="filter-select">
          <option value="">Todas las categorías</option>
          <option value="1">Bebidas</option>
          <option value="2">Frutas y Verduras</option>
          <option value="3">Carnes y Aves</option>
          <option value="4">Pescados y Mariscos</option>
          <option value="5">Lácteos y Huevos</option>
          <option value="6">Panadería y Repostería</option>
          <option value="7">Despensa</option>
          <option value="8">Hogar y Limpieza</option>
        </select>
      </div>
    </div>
    
    <div class="products-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td><div class="product-image-placeholder"></div></td>
            <td>Producto de Ejemplo</td>
            <td>Bebidas</td>
            <td>2.99€</td>
            <td>25</td>
            <td>
              <button class="btn-edit">Editar</button>
              <button class="btn-delete">Eliminar</button>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td><div class="product-image-placeholder"></div></td>
            <td>Producto de Ejemplo 2</td>
            <td>Frutas y Verduras</td>
            <td>1.49€</td>
            <td>40</td>
            <td>
              <button class="btn-edit">Editar</button>
              <button class="btn-delete">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <div class="pagination">
      <button>&laquo;</button>
      <button class="active">1</button>
      <button>2</button>
      <button>3</button>
      <button>&raquo;</button>
    </div>
    
    <p class="note">Esta es una versión preliminar del panel de gestión de productos. Las funcionalidades se implementarán proximamente.</p>
  </div>
</template>

<script setup>
// Aquí irá la lógica para gestionar productos
</script>

<style scoped>
.admin-products {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

h1 {
  color: #2c5e1a;
  margin-bottom: 2rem;
}

.admin-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.btn-primary {
  background-color: #2c5e1a;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}

.btn-primary:hover {
  background-color: #3a7a23;
}

.search-filter {
  display: flex;
  gap: 1rem;
}

.search-input, .filter-select {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.products-table {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 2rem;
}

th, td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background-color: #f5f5f5;
  font-weight: bold;
}

tr:hover {
  background-color: #f9f9f9;
}

.product-image-placeholder {
  width: 50px;
  height: 50px;
  background-color: #eee;
  border-radius: 4px;
}

.btn-edit, .btn-delete {
  padding: 0.5rem 0.75rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-right: 0.5rem;
}

.btn-edit {
  background-color: #4a90e2;
  color: white;
}

.btn-delete {
  background-color: #e74c3c;
  color: white;
}

.pagination {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 2rem;
}

.pagination button {
  padding: 0.5rem 0.75rem;
  border: 1px solid #ddd;
  background-color: white;
  cursor: pointer;
  border-radius: 4px;
}

.pagination button.active {
  background-color: #2c5e1a;
  color: white;
  border-color: #2c5e1a;
}

.note {
  color: #666;
  font-style: italic;
  text-align: center;
  margin-top: 2rem;
}
</style>