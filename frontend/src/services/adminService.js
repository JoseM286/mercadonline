import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

const adminService = {
  // User management
  async getUsers() {
    const response = await axios.get(`${API_URL}/admin/users`);
    return response.data;
  },

  async getUserById(id) {
    const response = await axios.get(`${API_URL}/admin/users/${id}`);
    return response.data;
  },

  async changeUserRole(userId, role) {
    const response = await axios.put(`${API_URL}/admin/users/${userId}/change-role`, { role });
    return response.data;
  },

  async deleteUser(userId) {
    const response = await axios.delete(`${API_URL}/admin/users/${userId}`);
    return response.data;
  },

  // Product management
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
  },

  async getProductById(id) {
    const response = await axios.get(`${API_URL}/products/show/${id}`);
    return response.data;
  },

  async createProduct(productData) {
    const response = await axios.post(`${API_URL}/products/create`, productData);
    return response.data;
  },

  async updateProduct(id, productData) {
    const response = await axios.put(`${API_URL}/products/edit/${id}`, productData);
    return response.data;
  },

  async deleteProduct(id) {
    const response = await axios.delete(`${API_URL}/products/delete/${id}`);
    return response.data;
  },

  // Order management
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
  },

  async updateOrderStatus(orderId, status) {
    const response = await axios.put(`${API_URL}/orders/update/${orderId}/status`, { status });
    return response.data;
  },

  // Statistics
  async getUserStatistics(startDate = null, endDate = null) {
    let url = `${API_URL}/admin/statistics`;
    const params = [];
    
    if (startDate) {
      params.push(`start_date=${startDate}`);
    }
    
    if (endDate) {
      params.push(`end_date=${endDate}`);
    }
    
    if (params.length > 0) {
      url += `?${params.join('&')}`;
    }
    
    const response = await axios.get(url);
    return response.data;
  },

  async getPopularProducts(limit = 5, useRatio = true, startDate = null, endDate = null) {
    let url = `${API_URL}/products/popular?limit=${limit}&use_ratio=${useRatio}`;
    
    if (startDate) {
      url += `&start_date=${startDate}`;
    }
    
    if (endDate) {
      url += `&end_date=${endDate}`;
    }
    
    const response = await axios.get(url);
    return response.data;
  },

  // Custom statistics for admin dashboard - Optimized version
  async getDashboardStats(startDate = null, endDate = null) {
    try {
      // Define un valor para limit si no está disponible en este contexto
      const limit = 5;
      
      // Make requests in parallel and use timeout to prevent hanging requests
      const promises = [
        this.getUserStatistics(startDate, endDate),
        this.getPopularProducts(limit, true, startDate, endDate),
        this.getOrders(1, limit, null, null, startDate, endDate),
        this.getProducts(1, 10) // Just need pagination info, reduce items
      ];
      
      const [usersStats, popularProducts, orders, productsData] = await Promise.all(promises);
      
      return {
        users: {
          total: usersStats.statistics.totalUsers,
          totalAdmins: usersStats.statistics.totalAdmins,
          // Eliminamos los campos que ya no existen
        },
        popularProducts: popularProducts.products,
        recentOrders: orders.orders,
        totalProducts: productsData.pagination.total,
        totalSales: orders.totalSales || 0,
        totalOrders: orders.totalOrders || 0,
        // Añadimos las fechas del filtro para mostrarlas en la UI
        dateRange: {
          startDate: usersStats.statistics.startDate,
          endDate: usersStats.statistics.endDate
        }
      };
    } catch (error) {
      console.error('Error fetching dashboard stats:', error);
      throw error;
    }
  }
};

export default adminService;



