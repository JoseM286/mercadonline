<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const cartItems = ref([]);
const loading = ref(true);
const error = ref(null);

// Cargar los productos del carrito
const loadCartItems = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    const response = await axios.get(`${import.meta.env.VITE_API_URL}/cart/list`);
    console.log('Respuesta del carrito:', response.data); // Añadir log para depuración
    
    // Corregir la estructura de datos según la respuesta real del backend
    cartItems.value = response.data.items || [];
  } catch (err) {
    console.error('Error al cargar el carrito:', err);
    error.value = 'No se pudo cargar el carrito. Por favor, inténtalo de nuevo más tarde.';
  } finally {
    loading.value = false;
  }
};

// Calcular el total del carrito
const cartTotal = computed(() => {
  return cartItems.value.reduce((total, item) => {
    return total + (parseFloat(item.product.price) * item.quantity);
  }, 0).toFixed(2);
});

// Función para actualizar la cantidad
const updateQuantity = async (itemId, newQuantity) => {
  if (newQuantity < 1) return;
  
  try {
    await axios.put(`${import.meta.env.VITE_API_URL}/cart/update/${itemId}`, {
      quantity: newQuantity
    });
    
    // Actualizar la cantidad en el carrito local
    const item = cartItems.value.find(item => item.id === itemId);
    if (item) {
      item.quantity = newQuantity;
    }
  } catch (err) {
    console.error('Error al actualizar cantidad:', err);
    alert('Error al actualizar la cantidad. Por favor, inténtalo de nuevo.');
  }
};

// Función para eliminar un producto del carrito
const removeItem = async (itemId) => {
  try {
    await axios.delete(`${import.meta.env.VITE_API_URL}/cart/remove/${itemId}`);
    // Eliminar el item del array local
    cartItems.value = cartItems.value.filter(item => item.id !== itemId);
  } catch (err) {
    console.error('Error al eliminar producto:', err);
    alert('Error al eliminar el producto. Por favor, inténtalo de nuevo.');
  }
};

// Función para proceder al pago
const checkout = () => {
  alert('Redirigiendo al proceso de pago...');
  // Aquí iría la lógica para redirigir al proceso de pago
};

// Función para obtener la URL de la imagen
const getImageUrl = (imagePath) => {
  if (!imagePath) return null;
  
  try {
    // Intentar importar la imagen desde assets
    // Primero verificamos si la imagen ya es una URL completa
    if (imagePath.startsWith('http')) {
      return imagePath;
    }
    
    // Si no es una URL completa, intentamos cargarla desde assets
    return new URL(`../assets/images/${imagePath}`, import.meta.url).href;
  } catch (error) {
    console.error('Error loading image:', error, imagePath);
    return null;
  }
};

// Cargar datos al montar el componente
onMounted(() => {
  loadCartItems();
});
</script>

<template>  
  <div class="page-container">
    <div class="page-header-with-logo">
      <h1>Mi Carrito</h1>
      <img src="@/assets/images/logo_verde.png" alt="Logo MercadonLine" class="page-logo" />
    </div>

    <div class="page-content">
      <!-- Spinner de carga -->
      <div v-if="loading" class="loading-container">
        <img src="@/assets/images/spinner.gif" alt="Cargando..." class="spinner-gif" />
        <p>Cargando carrito...</p>
      </div>
      
      <!-- Mensaje de error -->
      <div v-else-if="error" class="error-message">
        {{ error }}
      </div>
      
      <div v-else class="cart-container">
        <div v-if="cartItems.length === 0" class="empty-cart">
          <div class="empty-cart-icon">🛒</div>
          <h2>Tu carrito está vacío</h2>
          <p>Parece que aún no has añadido productos a tu carrito.</p>
          <router-link to="/" class="continue-shopping">Continuar comprando</router-link>
        </div>

        <div v-else class="cart-content">
          <div class="cart-items">
            <div class="cart-header">
              <div class="cart-header-product">Producto</div>
              <div class="cart-header-price">Precio</div>
              <div class="cart-header-quantity">Cantidad</div>
              <div class="cart-header-total">Total</div>
              <div class="cart-header-actions"></div>
            </div>

            <div v-for="item in cartItems" :key="item.id" class="cart-item">
              <div class="cart-item-product">
                <div class="cart-item-image">
                  <img 
                    v-if="item.product && item.product.image_path && getImageUrl(item.product.image_path)" 
                    :src="getImageUrl(item.product.image_path)" 
                    :alt="item.product.name"
                    @error="$event.target.style.display = 'none'; $event.target.nextElementSibling.style.display = 'flex'"
                  />
                  <div v-else class="image-placeholder">🥕</div>
                </div>
                <div class="cart-item-details">
                  <h3>{{ item.product ? item.product.name : 'Producto' }}</h3>
                </div>
              </div>
              
              <div class="cart-item-price">
                {{ item.product ? parseFloat(item.product.price).toFixed(2) : '0.00' }}€
              </div>
              
              <div class="cart-item-quantity">
                <button 
                  class="quantity-btn" 
                  @click="updateQuantity(item.id, item.quantity - 1)"
                  :disabled="item.quantity <= 1"
                >
                  -
                </button>
                <span class="quantity-value">{{ item.quantity }}</span>
                <button 
                  class="quantity-btn" 
                  @click="updateQuantity(item.id, item.quantity + 1)"
                >
                  +
                </button>
              </div>
              
              <div class="cart-item-total">
                {{ item.product ? (parseFloat(item.product.price) * item.quantity).toFixed(2) : '0.00' }}€
              </div>
              
              <div class="cart-item-actions">
                <button class="remove-btn" @click="removeItem(item.id)">
                  ✕
                </button>
              </div>
            </div>
          </div>
          
          <div class="cart-summary">
            <div class="cart-total">
              <span>Total:</span>
              <span class="total-amount">{{ cartTotal }}€</span>
            </div>
            
            <button class="checkout-btn" @click="checkout">
              Proceder al pago
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cart-container {
  margin-top: var(--spacing-lg);
}

.empty-cart {
  text-align: center;
  padding: var(--spacing-xl) 0;
}

.empty-cart-icon {
  font-size: 4rem;
  margin-bottom: var(--spacing-md);
  color: var(--color-text-secondary);
}

.empty-cart h2 {
  margin-bottom: var(--spacing-md);
  color: var(--color-text-primary);
}

.empty-cart p {
  margin-bottom: var(--spacing-lg);
  color: var(--color-text-secondary);
}

.continue-shopping {
  display: inline-block;
  padding: var(--spacing-sm) var(--spacing-md);
  background-color: var(--color-primary);
  color: white;
  text-decoration: none;
  border-radius: 4px;
  font-weight: bold;
  transition: background-color 0.3s;
}

.continue-shopping:hover {
  background-color: var(--color-primary-dark);
}

.cart-content {
  display: flex;
  gap: var(--spacing-lg);
}

.cart-items {
  flex: 3;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.cart-header {
  display: grid;
  grid-template-columns: 3fr 1fr 1fr 1fr 0.5fr;
  padding: var(--spacing-md);
  background-color: var(--color-bg-accent);
  font-weight: bold;
}

.cart-item {
  display: grid;
  grid-template-columns: 3fr 1fr 1fr 1fr 0.5fr;
  padding: var(--spacing-md);
  border-bottom: 1px solid #eee;
  align-items: center;
}

.cart-item:last-child {
  border-bottom: none;
}

.cart-item-product {
  display: flex;
  align-items: center;
  gap: var(--spacing-md);
}

.cart-item-image {
  width: 60px;
  height: 60px;
  border-radius: 4px;
  overflow: hidden;
  background-color: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;
}

.image-placeholder {
  font-size: 2rem;
}

.cart-item-details h3 {
  margin: 0;
  font-size: 1rem;
}

.cart-item-quantity {
  display: flex;
  align-items: center;
  gap: var(--spacing-xs);
}

.quantity-btn {
  width: 30px;
  height: 30px;
  border: 1px solid #ddd;
  background-color: white;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  transition: background-color 0.2s;
}

.quantity-btn:hover {
  background-color: #f0f0f0;
}

.quantity-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity-value {
  width: 30px;
  text-align: center;
}

.remove-btn {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.2rem;
  color: var(--color-text-secondary);
  transition: color 0.2s;
}

.remove-btn:hover {
  color: var(--color-error);
}

.cart-summary {
  flex: 1;
}

.summary-card {
  background-color: white;
  border-radius: 8px;
  padding: var(--spacing-lg);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.summary-card h2 {
  margin-top: 0;
  margin-bottom: var(--spacing-md);
  color: var(--color-primary);
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: var(--spacing-sm);
  padding-bottom: var(--spacing-sm);
  border-bottom: 1px solid #eee;
}

.summary-row.total {
  font-weight: bold;
  font-size: 1.2rem;
  margin-top: var(--spacing-md);
  border-bottom: none;
}

.checkout-btn {
  width: 100%;
  padding: var(--spacing-md);
  background-color: #3a7a23;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1.1rem;
  font-weight: bold;
  cursor: pointer;
  margin-top: var(--spacing-md);
  transition: background-color 0.3s;
}

.checkout-btn:hover {
  background-color: #4a9a2e;
}

.continue-shopping-link {
  display: block;
  text-align: center;
  margin-top: var(--spacing-md);
  color: #3a7a23;
  text-decoration: none;
}

.continue-shopping-link:hover {
  text-decoration: underline;
}
</style>



