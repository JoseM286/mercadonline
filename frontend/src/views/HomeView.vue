<script setup>
import { ref, onMounted } from 'vue';
import ProductCard from '@/components/ProductCard.vue';
import axios from 'axios';
import LoadingSpinner from '@/components/LoadingSpinner.vue';

const popularProducts = ref([]);
const loading = ref(true);
const error = ref(null);

// Función para cargar los productos más vendidos
const loadPopularProducts = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    const response = await axios.get(`${import.meta.env.VITE_API_URL}/products/popular`, {
      params: {
        limit: 20,
        use_ratio: true
      }
    });
    
    popularProducts.value = response.data.products || [];
    
    // Si no hay productos, intentar sin ratio
    if (popularProducts.value.length === 0) {
      const fallbackResponse = await axios.get(`${import.meta.env.VITE_API_URL}/products/popular`, {
        params: {
          limit: 20,
          use_ratio: false
        }
      });
      
      popularProducts.value = fallbackResponse.data.products || [];
    }
  } catch (err) {
    console.error('Error al cargar productos populares:', err);
    
    // Intentar sin ratio si hay un error
    try {
      const fallbackResponse = await axios.get(`${import.meta.env.VITE_API_URL}/products/popular`, {
        params: {
          limit: 20,
          use_ratio: false
        }
      });
      
      popularProducts.value = fallbackResponse.data.products || [];
    } catch (fallbackErr) {
      console.error('Error en fallback:', fallbackErr);
      error.value = 'No se pudieron cargar los productos. Por favor, inténtalo de nuevo más tarde.';
    }
  } finally {
    loading.value = false;
  }
};

// Cargar productos al montar el componente
onMounted(() => {
  loadPopularProducts();
});
</script>

<template>
  <div class="page-container">

    
    <section>
      <h1 class="section-title">Productos más vendidos</h1>
      
      <!-- Reemplazar el spinner de carga con el nuevo componente -->
      <LoadingSpinner 
        v-if="loading" 
        message="Cargando productos populares..." 
      />
      
      <!-- Mensaje de error -->
      <div v-else-if="error" class="error-message">
        {{ error }}
      </div>
      
      <!-- Cuadrícula de productos -->
      <div v-else-if="popularProducts.length > 0" class="products-grid">
        <div v-for="product in popularProducts" :key="product.id" class="product-item">
          <ProductCard :product="product" />
        </div>
      </div>
      
      <!-- Mensaje cuando no hay productos -->
      <div v-else class="no-products-message">
        No hay productos populares disponibles en este momento.
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



