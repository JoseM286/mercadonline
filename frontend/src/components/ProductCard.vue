<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
});

const router = useRouter();

// Función para navegar al detalle del producto
const goToProductDetail = () => {
  router.push(`/product/${props.product.id}`);
};

// Función para añadir al carrito
const addToCart = (event) => {
  // Detener la propagación para evitar que se active goToProductDetail
  event.stopPropagation();
  // Aquí iría la lógica para añadir al carrito
  console.log('Añadir al carrito:', props.product.id);
};

// Placeholder para cuando la imagen no carga
const imageError = ref(false);
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
</script>

<template>
  <div class="product-card">
    <div class="product-image" @click="goToProductDetail">
      <img
        v-if="!imageError && product.image_path"
        :src="getImageUrl(product.image_path)"
        :alt="product.name"
        @error="handleImageError"
      />
      <div v-else class="image-placeholder" @click="goToProductDetail">
        <span>🛒</span>
      </div>
    </div>
    <div class="product-info" @click="goToProductDetail">
      <h3 class="product-name">{{ product.name }}</h3>
      <p class="product-category">{{ product.category.name }}</p>
      <p class="product-price">{{ parseFloat(product.price).toFixed(2) }}€</p>
    </div>
    <button class="add-to-cart-btn" @click.stop="addToCart">
      Añadir al carrito
    </button>
  </div>
</template>

<style scoped>
.product-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.product-image {
  height: 180px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f8f8f8;
  cursor: pointer;
  position: relative;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.3s ease;
}

.product-image img:hover {
  transform: scale(1.05);
}

.image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f0f0f0;
  font-size: 3rem;
  color: #ccc;
}

.product-info {
  padding: 15px;
  flex-grow: 1;
  cursor: pointer;
}

.product-name {
  font-size: 1rem;
  font-weight: 600;
  margin: 0 0 8px 0;
  color: var(--color-text);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  height: 2.4rem;
}

.product-category {
  font-size: 0.85rem;
  color: var(--color-text-light);
  margin: 0 0 8px 0;
}

.product-price {
  font-size: 1.2rem;
  font-weight: 700;
  color: #2c5e1a; /* Color verde oscuro para el precio */
  margin: 8px 0 0 0;
}

.add-to-cart-btn {
  background-color: #3a7a23; /* Color verde claro del header */
  color: white;
  border: none;
  padding: 10px;
  width: 100%;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
}

.add-to-cart-btn:hover {
  background-color: #4a9a2e; /* Verde más claro al pasar el cursor */
}
</style>





