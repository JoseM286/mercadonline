import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL;

class CartService {
  // Añadir un producto al carrito
  async addToCart(productId, quantity = 1) {
    try {
      const response = await axios.post(`${API_URL}/cart/add`, {
        product_id: productId,
        quantity: quantity
      });
      return response.data;
    } catch (error) {
      console.error('Error adding to cart:', error);
      throw error;
    }
  }
  
  // Añadir múltiples productos al carrito en paralelo
  async addMultipleToCart(items) {
    try {
      // Crear un array de promesas para cada producto
      const promises = items.map(item => 
        axios.post(`${API_URL}/cart/add`, {
          product_id: item.productId,
          quantity: item.quantity || 1
        })
      );
      
      // Ejecutar todas las promesas en paralelo
      const responses = await Promise.all(promises);
      
      // Devolver los resultados
      return responses.map(response => response.data);
    } catch (error) {
      console.error('Error adding multiple items to cart:', error);
      throw error;
    }
  }
  
  // Obtener el contenido del carrito
  async getCart() {
    try {
      const response = await axios.get(`${API_URL}/cart/list`);
      return response.data;
    } catch (error) {
      console.error('Error fetching cart:', error);
      throw error;
    }
  }
  
  // Actualizar cantidad de un producto en el carrito
  async updateCartItem(itemId, quantity) {
    try {
      const response = await axios.put(`${API_URL}/cart/update/${itemId}`, {
        quantity: quantity
      });
      return response.data;
    } catch (error) {
      console.error('Error updating cart item:', error);
      throw error;
    }
  }
  
  // Eliminar un producto del carrito
  async removeFromCart(itemId) {
    try {
      const response = await axios.delete(`${API_URL}/cart/remove/${itemId}`);
      return response.data;
    } catch (error) {
      console.error('Error removing from cart:', error);
      throw error;
    }
  }
  
  // Vaciar el carrito
  async clearCart() {
    try {
      const response = await axios.delete(`${API_URL}/cart/clear`);
      return response.data;
    } catch (error) {
      console.error('Error clearing cart:', error);
      throw error;
    }
  }
}

export default new CartService();