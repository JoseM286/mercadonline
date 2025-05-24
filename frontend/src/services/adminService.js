import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL;

class AdminService {
  // Obtener estadísticas de usuarios
  async getUserStatistics(startDate = null, endDate = null) {
    let url = `${API_URL}/admin/statistics`;
    
    if (startDate || endDate) {
      url += '?';
      if (startDate) {
        url += `start_date=${startDate}`;
      }
      if (endDate) {
        url += startDate ? `&end_date=${endDate}` : `end_date=${endDate}`;
      }
    }
    
    const response = await axios.get(url);
    return response.data;
  }
  
  // Obtener productos populares
  async getPopularProducts(limit = 5, startDate = null, endDate = null) {
    let url = `${API_URL}/products/popular?limit=${limit}`;
    
    if (startDate) {
      url += `&start_date=${startDate}`;
    }
    
    if (endDate) {
      url += `&end_date=${endDate}`;
    }
    
    const response = await axios.get(url);
    return response.data;
  }
  
  // Obtener pedidos con filtros
  async getOrders(page = 1, limit = 10, search = null, status = null, startDate = null, endDate = null) {
    try {
      // Corregir la URL para que coincida con la ruta definida en el backend
      let url = `${API_URL}/admin/orders?page=${page}&limit=${limit}`;
      
      if (search) url += `&search=${encodeURIComponent(search)}`;
      if (status) url += `&status=${encodeURIComponent(status)}`;
      if (startDate) url += `&startDate=${encodeURIComponent(startDate)}`;
      if (endDate) url += `&endDate=${encodeURIComponent(endDate)}`;
      
      const response = await axios.get(url);
      return response.data;
    } catch (error) {
      console.error('Error fetching orders:', error);
      throw error;
    }
  }
  
  // Obtener productos
  async getProducts(page = 1, limit = 10, search = '', categoryId = null, startDate = null, endDate = null) {
    let url = `${API_URL}/products/list?page=${page}&limit=${limit}`;
    
    if (search) {
      url += `&search=${encodeURIComponent(search)}`;
    }
    
    if (categoryId) {
      url += `&category=${categoryId}`;
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
  
  // Obtener estadísticas del dashboard en una sola llamada
  async getDashboardStats(startDate = null, endDate = null) {
    try {
      let url = `${API_URL}/admin/dashboard`;
      
      if (startDate || endDate) {
        url += '?';
        if (startDate) {
          url += `start_date=${startDate}`;
        }
        if (endDate) {
          url += startDate ? `&end_date=${endDate}` : `end_date=${endDate}`;
        }
      }
      
      const response = await axios.get(url);
      return response.data;
    } catch (error) {
      console.error('Error fetching dashboard stats:', error);
      throw error;
    }
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
}

export default new AdminService();





