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
  
  // Obtener pedidos
  async getOrders(page = 1, limit = 10, status = null, userId = null, startDate = null, endDate = null) {
    let url = `${API_URL}/orders/admin/list?page=${page}&limit=${limit}`;
    
    if (status) {
      url += `&status=${status}`;
    }
    
    if (userId) {
      url += `&user_id=${userId}`;
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
}

export default new AdminService();

