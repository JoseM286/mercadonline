<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import ProductCard from '@/components/ProductCard.vue';
import axios from 'axios';
import LoadingSpinner from '@/components/LoadingSpinner.vue';

const route = useRoute();
const searchQuery = ref(route.query.search || '');
const categoryId = ref(route.query.category || '');
const products = ref([]);
const loading = ref(true);
const error = ref(null);
const totalProducts = ref(0);
const currentPage = ref(1);
const totalPages = ref(1);
const limit = 20;

// Cargar productos según los parámetros de búsqueda
const loadSearchResults = async () => {
  if (!searchQuery.value) return;
  
  loading.value = true;
  error.value = null;
  
  try {
    const params = {
      search: searchQuery.value,
      sort: 'popularity',
      page: currentPage.value,
      limit
    };
    
    // Añadir categoría si está seleccionada
    if (categoryId.value) {
      params.category = categoryId.value;
    }
    
    const response = await axios.get(`${import.meta.env.VITE_API_URL}/products/list`, { params });
    
    products.value = response.data.products || [];
    totalProducts.value = response.data.pagination?.total || 0;
    totalPages.value = response.data.pagination?.pages || 1;
    
  } catch (err) {
    console.error('Error al cargar resultados de búsqueda:', err);
    error.value = 'No se pudieron cargar los resultados. Por favor, inténtalo de nuevo más tarde.';
  } finally {
    loading.value = false;
  }
};

// Cargar datos al montar el componente
onMounted(() => {
  loadSearchResults();
});

// Observar cambios en los parámetros de búsqueda
watch(() => [route.query.search, route.query.category], ([newSearch, newCategory]) => {
  searchQuery.value = newSearch || '';
  categoryId.value = newCategory || '';
  currentPage.value = 1; // Resetear a la primera página
  loadSearchResults();
}, { deep: true });

// Función para cambiar de página
const changePage = (page) => {
  currentPage.value = page;
  loadSearchResults();
  // Scroll al inicio de los resultados
  window.scrollTo({ top: 0, behavior: 'smooth' });
};
</script>

<template>
  <div class="page-container">
    <section>
      <h1 class="section-title">
        Resultados para "{{ searchQuery }}"
        <span v-if="categoryId" class="category-filter">
          en categoría seleccionada
        </span>
      </h1>
      
      <!-- Reemplazar el spinner de carga con el nuevo componente -->
      <LoadingSpinner 
        v-if="loading" 
        message="Buscando productos..." 
      />
      
      <!-- Mensaje de error -->
      <div v-else-if="error" class="error-message">
        {{ error }}
      </div>
      
      <!-- Resultados de búsqueda -->
      <div v-else>
        <!-- Contador de resultados -->
        <p class="results-count">
          {{ totalProducts }} productos encontrados
        </p>
        
        <!-- Cuadrícula de productos -->
        <div v-if="products.length > 0" class="products-grid">
          <div v-for="product in products" :key="product.id" class="product-item">
            <ProductCard :product="product" />
          </div>
        </div>
        
        <!-- Mensaje cuando no hay productos -->
        <div v-else class="no-products-message">
          No se encontraron productos que coincidan con tu búsqueda.
        </div>
        
        <!-- Paginación -->
        <div v-if="totalPages > 1" class="pagination">
          <button 
            v-for="page in totalPages" 
            :key="page" 
            @click="changePage(page)"
            :class="{ active: page === currentPage }"
            class="page-button"
          >
            {{ page }}
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.section-title {
  font-size: 2rem;
  color: var(--color-primary);
  text-align: center;
  margin-bottom: var(--spacing-lg);
}

.category-filter {
  font-size: 1.2rem;
  color: var(--color-secondary);
  display: block;
  margin-top: var(--spacing-xs);
}

.results-count {
  text-align: center;
  margin-bottom: var(--spacing-md);
  color: var(--color-text-light);
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--spacing-md);
  margin-top: var(--spacing-md);
}

.product-item {
  height: 100%;
}

.pagination {
  display: flex;
  justify-content: center;
  gap: var(--spacing-sm);
  margin-top: var(--spacing-lg);
}

.page-button {
  padding: var(--spacing-sm) var(--spacing-md);
  border: 1px solid var(--color-border);
  background-color: white;
  cursor: pointer;
  border-radius: 4px;
  transition: all 0.3s ease;
}

.page-button.active {
  background-color: var(--color-primary);
  color: white;
  border-color: var(--color-primary);
}

.page-button:hover:not(.active) {
  background-color: var(--color-border);
}

/* Estilos para pantallas más pequeñas */
@media (max-width: 1200px) {
  .products-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .products-grid {
    grid-template-columns: 1fr;
  }
  
  .pagination {
    flex-wrap: wrap;
  }
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: var(--spacing-xl) 0;
}

.spinner-gif {
  width: 80px;
  height: 80px;
  margin-bottom: var(--spacing-md);
}

.error-message {
  text-align: center;
  color: var(--color-error);
  padding: var(--spacing-lg);
  background-color: var(--color-error-bg);
  border-radius: 8px;
  margin: var(--spacing-md) 0;
}

.no-products-message {
  text-align: center;
  padding: var(--spacing-lg);
  color: var(--color-text-light);
}
</style>
