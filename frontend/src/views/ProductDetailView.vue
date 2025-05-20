<template>
  <div class="page-container">
    <!-- Spinner de carga -->
    <div v-if="loading" class="loading-container">
      <img src="@/assets/images/spinner.gif" alt="Cargando..." class="spinner-gif" />
      <p>Cargando detalles del producto...</p>
    </div>
    
    <!-- Mensaje de error -->
    <div v-else-if="error" class="error-message">
      {{ error }}
    </div>
    
    <!-- Detalles del producto -->
    <div v-else-if="product" class="product-detail">
      <div class="product-detail-grid">
        <!-- Columna de imagen -->
        <div class="product-image-container">
          <img
            v-if="!imageError && product.image_path"
            :src="getImageUrl(product.image_path)"
            :alt="product.name"
            @error="handleImageError"
            class="product-detail-image"
          />
          <div v-else class="image-placeholder-large">
            <span>🛒</span>
          </div>
        </div>
        
        <!-- Columna de información -->
        <div class="product-info-container">
          <h1 class="product-title">{{ product.name }}</h1>
          
          <div class="product-meta">
            <span class="product-category-badge">{{ product.category.name }}</span>
            <span v-if="product.stock > 0" class="product-stock in-stock">En stock</span>
            <span v-else class="product-stock out-of-stock">Agotado</span>
          </div>
          
          <p class="product-price-large">{{ parseFloat(product.price).toFixed(2) }}€</p>
          
          <div class="product-description">
            <h3>Descripción</h3>
            <p v-if="product.description">{{ product.description }}</p>
            <p v-else>No hay descripción disponible para este producto.</p>
          </div>
          
          <div class="product-actions">
            <div class="quantity-selector">
              <button 
                class="quantity-btn" 
                @click="quantity > 1 ? quantity-- : null"
                :disabled="quantity <= 1"
              >
                -
              </button>
              <span class="quantity-value">{{ quantity }}</span>
              <button 
                class="quantity-btn" 
                @click="quantity++"
              >
                +
              </button>
            </div>
            
            <button class="add-to-cart-btn-large" @click="addToCart">
              Añadir al carrito
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Mensaje cuando no hay producto -->
    <div v-else class="no-product-message">
      No se encontró el producto solicitado.
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '@/router/auth';

const route = useRoute();
const router = useRouter();
const productId = route.params.id;

const product = ref(null);
const loading = ref(true);
const error = ref(null);
const quantity = ref(1);
const imageError = ref(false);

// Añadir el store de autenticación
const authStore = useAuthStore();

// Cargar detalles del producto
const loadProductDetails = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    const response = await axios.get(`${import.meta.env.VITE_API_URL}/products/show/${productId}`);
    product.value = response.data.product;
  } catch (err) {
    console.error('Error al cargar detalles del producto:', err);
    error.value = 'No se pudieron cargar los detalles del producto. Por favor, inténtalo de nuevo más tarde.';
  } finally {
    loading.value = false;
  }
};

// Función para añadir al carrito
const addToCart = async () => {
  try {
    // Verificar si el usuario está autenticado
    if (!authStore.isAuthenticated) {
      // Redirigir al login si no está autenticado
      router.push('/login');
      return;
    }
    
    // Verificar stock disponible
    if (product.value.stock < quantity.value) {
      error.value = 'No hay suficiente stock disponible';
      return;
    }
    
    // Enviar petición al backend
    const response = await axios.post(`${import.meta.env.VITE_API_URL}/cart/add`, {
      product_id: productId,
      quantity: quantity.value
    });
    
    // Mostrar mensaje de éxito (puedes usar un modal si lo prefieres)
    alert('Producto añadido al carrito correctamente');
    
    console.log('Producto añadido al carrito:', response.data);
  } catch (err) {
    console.error('Error al añadir al carrito:', err);
    error.value = 'Error al añadir el producto al carrito. Por favor, inténtalo de nuevo.';
  }
};

// Placeholder para cuando la imagen no carga
const handleImageError = () => {
  imageError.value = true;
};

// Función para obtener la URL de la imagen
const getImageUrl = (imagePath) => {
  if (!imagePath) return null;
  
  try {
    // Intentar importar la imagen desde assets
    return new URL(`../assets/images/${imagePath}`, import.meta.url).href;
  } catch (error) {
    console.error('Error loading image:', error);
    return null;
  }
};

// Cargar datos al montar el componente
onMounted(() => {
  loadProductDetails();
});
</script>

<style scoped>
.page-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px;
}

.spinner-gif {
  width: 60px;
  height: 60px;
  margin-bottom: 20px;
}

.error-message, .no-product-message {
  background-color: #f8d7da;
  color: #721c24;
  padding: 15px;
  border-radius: 4px;
  margin: 20px 0;
  text-align: center;
}

.product-detail {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.product-detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
}

@media (max-width: 768px) {
  .product-detail-grid {
    grid-template-columns: 1fr;
  }
}

.product-image-container {
  padding: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f8f8f8;
}

.product-detail-image {
  max-width: 100%;
  max-height: 400px;
  object-fit: contain;
}

.image-placeholder-large {
  width: 100%;
  height: 400px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f0f0f0;
  font-size: 5rem;
  color: #ccc;
}

.product-info-container {
  padding: 30px;
}

.product-title {
  font-size: 1.8rem;
  font-weight: 700;
  margin: 0 0 15px 0;
  color: var(--color-text);
}

.product-meta {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.product-category-badge {
  background-color: #e9ecef;
  color: #495057;
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 0.85rem;
  margin-right: 15px;
}

.product-stock {
  font-size: 0.9rem;
  font-weight: 600;
}

.in-stock {
  color: #28a745;
}

.out-of-stock {
  color: #dc3545;
}

.product-price-large {
  font-size: 2rem;
  font-weight: 700;
  color: #2c5e1a;
  margin: 0 0 20px 0;
}

.product-description {
  margin-bottom: 30px;
}

.product-description h3 {
  font-size: 1.2rem;
  margin-bottom: 10px;
  color: var(--color-text);
}

.product-description p {
  line-height: 1.6;
  color: var(--color-text);
}

.product-actions {
  display: flex;
  align-items: center;
  gap: 15px;
}

.quantity-selector {
  display: flex;
  align-items: center;
  border: 1px solid #ddd;
  border-radius: 4px;
  overflow: hidden;
}

.quantity-btn {
  background-color: #f8f9fa;
  border: none;
  width: 40px;
  height: 40px;
  font-size: 1.2rem;
  cursor: pointer;
}

.quantity-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity-value {
  width: 40px;
  text-align: center;
  font-weight: 600;
}

.add-to-cart-btn-large {
  background-color: #3a7a23;
  color: white;
  border: none;
  padding: 0 30px;
  height: 40px;
  font-weight: 600;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.2s;
  flex-grow: 1;
}

.add-to-cart-btn-large:hover {
  background-color: #4a9a2e;
}
</style>

