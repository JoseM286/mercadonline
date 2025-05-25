<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import ProductCard from '@/components/ProductCard.vue';
import axios from 'axios';
import LoadingSpinner from '@/components/LoadingSpinner.vue';

const route = useRoute();
const categoryId = ref(route.params.id);
const categoryName = ref('');
const products = ref([]);
const loading = ref(true);
const error = ref(null);

// Cargar información de la categoría
const loadCategory = async () => {
  try {
    const response = await axios.get(`${import.meta.env.VITE_API_URL}/categories/show/${categoryId.value}`);
    categoryName.value = response.data.category.name;
  } catch (err) {
    console.error('Error al cargar la categoría:', err);
  }
};

// Cargar productos de la categoría ordenados por popularidad
const loadCategoryProducts = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    const response = await axios.get(`${import.meta.env.VITE_API_URL}/products/list`, {
      params: {
        category: categoryId.value,
        sort: 'popularity',
        limit: 20
      }
    });
    
    products.value = response.data.products || [];
  } catch (err) {
    console.error('Error al cargar productos de la categoría:', err);
    error.value = 'No se pudieron cargar los productos. Por favor, inténtalo de nuevo más tarde.';
  } finally {
    loading.value = false;
  }
};

// Cargar datos al montar el componente
onMounted(() => {
  loadCategory();
  loadCategoryProducts();
});

// Observar cambios en el ID de categoría para recargar los datos
watch(() => route.params.id, (newId) => {
  categoryId.value = newId;
  loadCategory();
  loadCategoryProducts();
});
</script>

<template>
  <div class="page-container">
    <section>
      <h1 class="section-title">{{ categoryName }}</h1>
      
      <!-- Reemplazar el spinner de carga con el nuevo componente -->
      <LoadingSpinner 
        v-if="loading" 
        message="Cargando productos..." 
      />
      
      <!-- Mensaje de error -->
      <div v-else-if="error" class="error-message">
        {{ error }}
      </div>
      
      <!-- Cuadrícula de productos -->
      <div v-else-if="products.length > 0" class="products-grid">
        <div v-for="product in products" :key="product.id" class="product-item">
          <ProductCard :product="product" />
        </div>
      </div>
      
      <!-- Mensaje cuando no hay productos -->
      <div v-else class="no-products-message">
        No hay productos disponibles en esta categoría.
      </div>
    </section>
  </div>
</template>

<style scoped>
.section-title {
  font-size: 2rem;
  color: var(--color-primary);
  text-align: center;
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
