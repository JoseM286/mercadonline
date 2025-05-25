import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL;

class AdminService {
  // Obtener productos
  async getProducts(page = 1, limit = 10, search = '', categoryId = null) {
    let url = `${API_URL}/products/list?page=${page}&limit=${limit}`;
    
    if (search) {
      url += `&search=${encodeURIComponent(search)}`;
    }
    
    if (categoryId) {
      url += `&category=${categoryId}`;
    }
    
    const response = await axios.get(url);
    return response.data;
  }
  
  // Crear un nuevo producto
  async createProduct(productData) {
    try {
      const response = await axios.post(`${API_URL}/products/create`, productData);
      return response.data;
    } catch (error) {
      console.error('Error creating product:', error);
      throw error;
    }
  }
  
  // Actualizar un producto existente
  async updateProduct(productId, productData) {
    try {
      const response = await axios.put(`${API_URL}/products/edit/${productId}`, productData);
      return response.data;
    } catch (error) {
      console.error('Error updating product:', error);
      throw error;
    }
  }
  
  // Eliminar un producto
  async deleteProduct(productId) {
    try {
      const response = await axios.delete(`${API_URL}/products/delete/${productId}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting product:', error);
      throw error;
    }
  }
  
  // Obtener pedidos
  async getOrders(page = 1, limit = 10, search = null, status = null, startDate = null, endDate = null) {
    let url = `${API_URL}/admin/orders?page=${page}&limit=${limit}`;
    
    if (search) {
      url += `&search=${encodeURIComponent(search)}`;
    }
    
    if (status) {
      url += `&status=${status}`;
    }
    
    if (startDate) {
      url += `&start_date=${startDate}`;
    }
    
    if (endDate) {
      url += `&end_date=${endDate}`;
    }
    
    const response = await axios.get(url);
    return response.data;
  }
  
  // Actualizar estado de un pedido
  async updateOrderStatus(orderId, newStatus) {
    try {
      const response = await axios.put(`${API_URL}/orders/${orderId}/status`, {
        status: newStatus
      });
      return response.data;
    } catch (error) {
      console.error('Error updating order status:', error);
      throw error;
    }
  }
  
  // Eliminar un pedido
  async deleteOrder(orderId) {
    try {
      const response = await axios.delete(`${API_URL}/orders/${orderId}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting order:', error);
      throw error;
    }
  }
  
  // Obtener usuarios
  async getUsers() {
    try {
      const response = await axios.get(`${API_URL}/admin/users`);
      return response.data;
    } catch (error) {
      console.error('Error fetching users:', error);
      throw error;
    }
  }
  
  // Cambiar rol de usuario
  async changeUserRole(userId, newRole) {
    try {
      const response = await axios.put(`${API_URL}/admin/users/${userId}/role`, {
        role: newRole
      });
      return response.data;
    } catch (error) {
      console.error('Error changing user role:', error);
      throw error;
    }
  }
  
  // Eliminar un usuario
  async deleteUser(userId) {
    try {
      const response = await axios.delete(`${API_URL}/admin/users/${userId}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting user:', error);
      throw error;
    }
  }
  
  // Obtener estadísticas
  async getStats() {
    try {
      const response = await axios.get(`${API_URL}/admin/stats`);
      return response.data;
    } catch (error) {
      console.error('Error fetching stats:', error);
      throw error;
    }
  }
}

export default new AdminService();

