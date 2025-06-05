<template>
  <div class="admin-products">
    <h1>Gestión de Productos</h1>
    <p>Administra los productos del catálogo, edita sus detalles o añade nuevos productos.</p>
    
    <div class="admin-controls">
      <button class="btn-primary" @click="showAddProductModal = true">Añadir Nuevo Producto</button>
      <div class="search-filter">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar productos..."
          class="search-input"
          @keyup.enter="fetchProducts(1)"
        >
        <select v-model="selectedCategory" class="filter-select" @change="fetchProducts(1)">
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
        <button class="btn-search" @click="fetchProducts(1)">Buscar</button>
      </div>
    </div>
    
    <!-- Spinner de carga -->
    <LoadingSpinner 
      v-if="loading" 
      message="Cargando productos..." 
    />
    
    <!-- Mensaje de error -->
    <div v-else-if="error" class="error-container">
      <h3>Error al cargar productos</h3>
      <p>{{ error }}</p>
      <button @click="fetchProducts(currentPage)" class="btn-retry">Reintentar</button>
    </div>
    
    <!-- Tabla de productos -->
    <div v-else class="products-table">
      <table v-if="products && products.length">
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
          <tr v-for="product in products" :key="product.id">
            <td>{{ product.id }}</td>
            <td>
              <div class="product-image">
                <img
                  v-if="product.image_path"
                  :src="getImageUrl(product.image_path)"
                  :alt="product.name"
                  @error="$event.target.style.display = 'none'; $event.target.nextElementSibling.style.display = 'flex'"
                >
                <div v-else class="product-image-placeholder">🛒</div>
              </div>
            </td>
            <td>{{ product.name }}</td>
            <td>{{ product.category.name }}</td>
            <td>{{ product.price }}€</td>
            <td>{{ product.stock }}</td>
            <td>
              <button class="btn-edit" @click="editProduct(product)">Editar</button>
              <button class="btn-delete" @click="confirmDeleteProduct(product)">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-else class="no-products">
        <p>No se encontraron productos con los criterios de búsqueda actuales.</p>
        <button @click="resetFilters" class="btn-reset">Resetear filtros</button>
      </div>
    </div>
    
    <!-- Paginación -->
    <div v-if="products && products.length" class="pagination">
      <button
        @click="changePage(currentPage - 1)"
        :disabled="currentPage === 1"
      >&laquo;</button>
      
      <button
        v-for="page in pagesArray"
        :key="page"
        @click="changePage(page)"
        :class="{ active: currentPage === page }"
      >
        {{ page }}
      </button>
      
      <button
        @click="changePage(currentPage + 1)"
        :disabled="currentPage === totalPages"
      >&raquo;</button>
    </div>
    
    <!-- Modal Añadir/Editar Producto -->
    <div v-if="showAddProductModal || showEditProductModal" class="modal">
      <div class="modal-content">
        <span class="close" @click="closeModals">&times;</span>
        <h2>{{ showEditProductModal ? 'Editar Producto' : 'Añadir Nuevo Producto' }}</h2>
        
        <form @submit.prevent="showEditProductModal ? updateProduct() : createProduct()">
          <div class="form-group">
            <label for="productName">Nombre:</label>
            <input
              id="productName"
              v-model="productForm.name"
              type="text"
              required
            >
          </div>
          
          <div class="form-group">
            <label for="productDescription">Descripción:</label>
            <textarea
              id="productDescription"
              v-model="productForm.description"
              rows="3"
            ></textarea>
          </div>
          
          <div class="form-group">
            <label for="productPrice">Precio:</label>
            <input
              id="productPrice"
              v-model="productForm.price"
              type="number"
              min="0.01"
              step="0.01"
              required
            >
          </div>
          
          <div class="form-group">
            <label for="productStock">Stock:</label>
            <input
              id="productStock"
              v-model="productForm.stock"
              type="number"
              min="0"
              required
            >
          </div>
          
          <div class="form-group">
            <label for="productCategory">Categoría:</label>
            <select
              id="productCategory"
              v-model="productForm.category_id"
              required
            >
              <option value="">Selecciona una categoría</option>
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
          
          <div class="form-group">
            <label for="productImage">Imagen del producto:</label>
            <div class="image-upload-container">
              <!-- Previsualización de la imagen -->
              <div v-if="imagePreview" class="image-preview">
                <img :src="imagePreview" alt="Vista previa" />
                <button type="button" class="btn-remove-image" @click="removeImage">×</button>
              </div>
              
              <!-- Input para URL de imagen -->
              <input
                id="productImageUrl"
                v-model="productForm.image_path"
                type="text"
                placeholder="https://... o nombre del archivo"
                :disabled="imageFile !== null"
              >
              
              <!-- O separador -->
              <div class="separator">
                <span>O</span>
              </div>
              
              <!-- Input para subir archivo -->
              <div class="file-input-container">
                <input
                  id="productImageFile"
                  type="file"
                  accept="image/*"
                  @change="handleImageUpload"
                  ref="fileInput"
                >
                <label for="productImageFile" class="btn-file-upload">
                  Seleccionar archivo
                </label>
              </div>
            </div>
          </div>
          
          <div class="form-actions">
            <button type="button" class="btn-cancel" @click="closeModals">Cancelar</button>
            <button type="submit" class="btn-submit">
              {{ showEditProductModal ? 'Actualizar' : 'Añadir' }}
            </button>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Modal Confirmación Eliminar -->
    <div v-if="showDeleteModal" class="modal">
      <div class="modal-content">
        <span class="close" @click="showDeleteModal = false">&times;</span>
        <h2>Confirmar Eliminación</h2>
        <p>¿Estás seguro de que deseas eliminar el producto "{{ productToDelete?.name }}"?</p>
        <div class="form-actions">
          <button type="button" class="btn-cancel" @click="showDeleteModal = false">Cancelar</button>
          <button type="button" class="btn-delete" @click="deleteProduct">Eliminar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import adminService from '@/services/adminService';
import LoadingSpinner from '@/components/LoadingSpinner.vue';
import imageService from '@/services/imageService';

// Estado para la lista de productos
const products = ref([]);
const loading = ref(true);
const error = ref(null);

// Estado para filtros y paginación
const searchQuery = ref('');
const selectedCategory = ref('');
const currentPage = ref(1);
const totalProducts = ref(0);
const totalPages = ref(1);
const productsPerPage = ref(10);

// Formulario producto
const productForm = ref({
  name: '',
  description: '',
  price: '',
  stock: '',
  category_id: '',
  image_path: ''
});

// Estado modales
const showAddProductModal = ref(false);
const showEditProductModal = ref(false);
const showDeleteModal = ref(false);
const productToEdit = ref(null);
const productToDelete = ref(null);

// Estado para la carga de imágenes
const imageFile = ref(null);
const imagePreview = ref(null);
const fileInput = ref(null);

// Función para manejar la carga de imágenes
const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (!file) return;
  
  // Validar que sea una imagen
  if (!file.type.match('image.*')) {
    alert('Por favor, selecciona un archivo de imagen válido');
    return;
  }
  
  // Guardar el archivo
  imageFile.value = file;
  
  // Crear URL para previsualización
  imagePreview.value = URL.createObjectURL(file);
  
  // Limpiar el campo de URL de imagen
  productForm.value.image_path = '';
};

// Función para eliminar la imagen seleccionada
const removeImage = () => {
  imageFile.value = null;
  imagePreview.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

// Función para subir la imagen al servidor
const uploadImage = async () => {
  if (!imageFile.value) return null;
  
  try {
    console.log('Preparando imagen para subir:', imageFile.value.name);
    
    const formData = new FormData();
    formData.append('image', imageFile.value);
    
    console.log('Enviando imagen al servidor...');
    const response = await imageService.uploadImage(formData);
    
    if (!response.success || !response.imagePath) {
      throw new Error('La respuesta del servidor no contiene la ruta de la imagen');
    }
    
    console.log('Imagen subida correctamente:', response.imagePath);
    return response.imagePath;
  } catch (error) {
    console.error('Error al subir la imagen:', error);
    
    // Mostrar un mensaje de error más descriptivo
    let errorMessage = 'No se pudo subir la imagen.';
    
    if (error.response && error.response.data && error.response.data.error) {
      errorMessage += ' ' + error.response.data.error;
    } else if (error.message) {
      errorMessage += ' ' + error.message;
    }
    
    throw new Error(errorMessage);
  }
};

// Función para obtener la URL de la imagen
const getImageUrl = (imagePath) => {
  return imageService.getImageUrl(imagePath);
};

// Calcular array de páginas para la paginación
const pagesArray = computed(() => {
  const pages = [];
  const maxVisiblePages = 5;
  
  if (totalPages.value <= maxVisiblePages) {
    for (let i = 1; i <= totalPages.value; i++) {
      pages.push(i);
    }
  } else {
    // Siempre mostrar primera página
    if (currentPage.value > 3) {
      pages.push(1);
      if (currentPage.value > 4) {
        pages.push('...');
      }
    }
    
    // Páginas alrededor de la actual
    const startPage = Math.max(2, currentPage.value - 1);
    const endPage = Math.min(totalPages.value - 1, currentPage.value + 1);
    
    for (let i = startPage; i <= endPage; i++) {
      pages.push(i);
    }
    
    // Siempre mostrar última página
    if (currentPage.value < totalPages.value - 2) {
      if (currentPage.value < totalPages.value - 3) {
        pages.push('...');
      }
      pages.push(totalPages.value);
    }
  }
  
  return pages;
});

// Obtener productos desde el backend
const fetchProducts = async (page) => {
  loading.value = true;
  error.value = null;
  currentPage.value = page;
  
  try {
    const response = await adminService.getProducts(
      page,
      productsPerPage.value,
      searchQuery.value,
      selectedCategory.value || null
    );
    
    products.value = response.products;
    totalProducts.value = response.pagination.total;
    totalPages.value = response.pagination.pages;
    
    // Ajustar página actual si es mayor que el total de páginas
    if (currentPage.value > totalPages.value && totalPages.value > 0) {
      changePage(totalPages.value);
    }
  } catch (err) {
    console.error('Error al obtener productos:', err);
    error.value = 'No se pudieron cargar los productos. Por favor, inténtalo de nuevo.';
  } finally {
    loading.value = false;
  }
};

// Cambiar página
const changePage = (page) => {
  if (page < 1 || page > totalPages.value) return;
  fetchProducts(page);
};

// Resetear filtros
const resetFilters = () => {
  searchQuery.value = '';
  selectedCategory.value = '';
  fetchProducts(1);
};

// Crear producto
const createProduct = async () => {
  try {
    loading.value = true;
    
    // Si hay una imagen seleccionada, subirla primero
    let imagePath = productForm.value.image_path;
    if (imageFile.value) {
      imagePath = await uploadImage();
    }
    
    await adminService.createProduct({
      name: productForm.value.name,
      description: productForm.value.description,
      price: parseFloat(productForm.value.price),
      stock: parseInt(productForm.value.stock),
      category_id: parseInt(productForm.value.category_id),
      image_path: imagePath
    });
    
    showAddProductModal.value = false;
    resetProductForm();
    fetchProducts(currentPage.value);
    alert('Producto creado correctamente');
  } catch (err) {
    console.error('Error al crear producto:', err);
    alert('Error al crear el producto: ' + (err.response?.data?.error || err.message));
  } finally {
    loading.value = false;
  }
};

// Editar producto
const editProduct = (product) => {
  productToEdit.value = product;
  productForm.value = {
    name: product.name,
    description: product.description || '',
    price: product.price,
    stock: product.stock,
    category_id: product.category.id.toString(),
    image_path: product.image_path || ''
  };
  showEditProductModal.value = true;
};

// Actualizar producto
const updateProduct = async () => {
  try {
    if (!productToEdit.value) {
      console.error('No hay producto seleccionado para editar');
      return;
    }
    
    loading.value = true;
    
    // Si hay una imagen seleccionada, subirla primero
    let imagePath = productForm.value.image_path;
    if (imageFile.value) {
      imagePath = await uploadImage();
    }
    
    await adminService.updateProduct(productToEdit.value.id, {
      name: productForm.value.name,
      description: productForm.value.description,
      price: parseFloat(productForm.value.price),
      stock: parseInt(productForm.value.stock),
      category_id: parseInt(productForm.value.category_id),
      image_path: imagePath
    });
    
    showEditProductModal.value = false;
    resetProductForm();
    await fetchProducts(currentPage.value);
    alert('Producto actualizado correctamente');
  } catch (err) {
    console.error('Error al actualizar producto:', err);
    error.value = 'Error al actualizar el producto: ' + (err.response?.data?.error || err.message);
    alert('Error al actualizar el producto: ' + (err.response?.data?.error || err.message));
  } finally {
    loading.value = false;
  }
};

// Confirmar eliminación
const confirmDeleteProduct = (product) => {
  productToDelete.value = product;
  showDeleteModal.value = true;
};

// Eliminar producto
const deleteProduct = async () => {
  try {
    await adminService.deleteProduct(productToDelete.value.id);
    showDeleteModal.value = false;
    fetchProducts(currentPage.value);
  } catch (err) {
    console.error('Error al eliminar producto:', err);
    alert('Error al eliminar el producto: ' + (err.response?.data?.error || err.message));
  }
};

// Cerrar modales
const closeModals = () => {
  showAddProductModal.value = false;
  showEditProductModal.value = false;
  resetProductForm();
};

// Resetear formulario
const resetProductForm = () => {
  productForm.value = {
    name: '',
    description: '',
    price: '',
    stock: '',
    category_id: '',
    image_path: ''
  };
  productToEdit.value = null;
  imageFile.value = null;
  imagePreview.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

// Cargar productos cuando se monte el componente
onMounted(() => {
  fetchProducts(1);
});
</script>

<style scoped>
.admin-products {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

h1 {
  color: #2c5e1a;
  margin-bottom: 1rem;
}

h2 {
  color: #3a7a23;
  margin-bottom: 1.5rem;
}

.admin-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.btn-primary, .btn-search, .btn-retry, .btn-reset, .btn-submit, .btn-cancel {
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.2s;
}

.btn-primary {
  background-color: #2c5e1a;
  color: white;
}

.btn-primary:hover {
  background-color: #3a7a23;
}

.btn-search {
  background-color: #4a90e2;
  color: white;
}

.btn-search:hover {
  background-color: #3a80d2;
}

.btn-retry, .btn-reset {
  background-color: #6c757d;
  color: white;
}

.btn-retry:hover, .btn-reset:hover {
  background-color: #5a6268;
}

.btn-submit {
  background-color: #2c5e1a;
  color: white;
}

.btn-submit:hover {
  background-color: #3a7a23;
}

.btn-cancel {
  background-color: #6c757d;
  color: white;
  margin-right: 1rem;
}

.btn-cancel:hover {
  background-color: #5a6268;
}

.search-filter {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.search-input, .filter-select {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.search-input {
  min-width: 250px;
}

.products-table {
  overflow-x: auto;
  margin-bottom: 2rem;
}

table {
  width: 100%;
  border-collapse: collapse;
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

.product-image {
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.product-image img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
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
  font-weight: bold;
}

.btn-edit {
  background-color: #4a90e2;
  color: white;
}

.btn-edit:hover {
  background-color: #3a80d2;
}

.btn-delete {
  background-color: #e74c3c;
  color: white;
}

.btn-delete:hover {
  background-color: #d73c2c;
}

.pagination {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 2rem;
  flex-wrap: wrap;
}

.pagination button {
  padding: 0.5rem 0.75rem;
  border: 1px solid #ddd;
  background-color: white;
  cursor: pointer;
  border-radius: 4px;
  min-width: 40px;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination button.active {
  background-color: #2c5e1a;
  color: white;
  border-color: #2c5e1a;
}

.loading-container, .no-products, .error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 200px;
  text-align: center;
}

.loading-spinner {
  border: 5px solid #f3f3f3;
  border-top: 5px solid #3a7a23;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-container {
  background-color: #fff0f0;
  border-radius: 8px;
  padding: 2rem;
  margin: 2rem 0;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  border-radius: 8px;
  padding: 2rem;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
}

.close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  font-size: 1.5rem;
  cursor: pointer;
  color: #999;
}

.close:hover {
  color: #333;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
}

.form-group input, .form-group textarea, .form-group select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-sizing: border-box;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 2rem;
}

.image-upload-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.image-preview {
  position: relative;
  width: 150px;
  height: 150px;
  border: 1px solid #ddd;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 1rem;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.btn-remove-image {
  position: absolute;
  top: 5px;
  right: 5px;
  width: 25px;
  height: 25px;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.8);
  border: 1px solid #ddd;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 16px;
  font-weight: bold;
}

.btn-remove-image:hover {
  background-color: #d73c2c;
}

.separator {
  display: flex;
  align-items: center;
  text-align: center;
  margin: 1rem 0;
}

.separator::before,
.separator::after {
  content: '';
  flex: 1;
  border-bottom: 1px solid #ddd;
}

.separator span {
  padding: 0 10px;
  color: #666;
  font-size: 0.9rem;
}

.file-input-container {
  position: relative;
}

.file-input-container input[type="file"] {
  position: absolute;
  width: 0.1px;
  height: 0.1px;
  opacity: 0;
  overflow: hidden;
  z-index: -1;
}

.btn-file-upload {
  display: inline-block;
  padding: 0.75rem 1rem;
  background-color: #4a90e2;
  color: white;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  text-align: center;
}

.btn-file-upload:hover {
  background-color: #3a80d2;
}

@media (max-width: 768px) {
  .admin-controls {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-filter {
    flex-direction: column;
  }
  
  .modal-content {
    width: 95%;
    padding: 1.5rem;
  }
}
</style>
