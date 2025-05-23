<template>
  <div class="admin-users">
    <h1>Gestión de Usuarios</h1>
    <p>En esta vista, los administradores podrán gestionar los usuarios de la plataforma.</p>
    
    <div class="admin-controls">
      <button class="btn-primary">Añadir Nuevo Usuario</button>
      <div class="search-filter">
        <input type="text" placeholder="Buscar usuarios..." class="search-input">
        <select class="filter-select">
          <option value="">Todos los roles</option>
          <option value="ROLE_USER">Usuario</option>
          <option value="ROLE_ADMIN">Administrador</option>
        </select>
      </div>
    </div>
    
    <div class="users-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Fecha de Registro</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Usuario de Ejemplo</td>
            <td>usuario@ejemplo.com</td>
            <td>Usuario</td>
            <td>01/01/2025</td>
            <td>
              <button class="btn-edit">Editar</button>
              <button class="btn-delete">Eliminar</button>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td>Admin de Ejemplo</td>
            <td>admin@ejemplo.com</td>
            <td>Administrador</td>
            <td>01/01/2025</td>
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
    
    <p class="note">Esta es una versión preliminar del panel de gestión de usuarios. Las funcionalidades se implementarán proximamente.</p>
  </div>
</template>

<script setup>
// Aquí irá la lógica para gestionar usuarios
</script>

<style scoped>
.admin-users {
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

.users-table {
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