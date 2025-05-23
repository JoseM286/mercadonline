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
  async getOrders(page = 1, limit = 10, status = null, userId = null) {
    let url = `${API_URL}/orders/admin/list?page=${page}&limit=${limit}`;
    
    if (status) {
      url += `&status=${status}`;
    }
    
    if (userId) {
      url += `&user_id=${userId}`;
    }
    
    const response = await axios.get(url);
    return response.data;
  },

  async updateOrderStatus(orderId, status) {
    const response = await axios.put(`${API_URL}/orders/update/${orderId}/status`, { status });
    return response.data;
  },

  // Statistics
  async getUserStatistics(date = null) {
    let url = `${API_URL}/admin/statistics`;
    if (date) {
      url += `?date=${date}`;
    }
    const response = await axios.get(url);
    return response.data;
  },

  async getPopularProducts(limit = 5, useRatio = true) {
    const response = await axios.get(`${API_URL}/products/popular?limit=${limit}&use_ratio=${useRatio}`);
    return response.data;
  },

  // Custom statistics for admin dashboard
  async getDashboardStats() {
    // This method will fetch multiple data points needed for the dashboard
    try {
      const [usersStats, popularProducts, orders] = await Promise.all([
        this.getUserStatistics(),
        this.getPopularProducts(5),
        this.getOrders(1, 5) // Get the 5 most recent orders
      ]);

      // Count total products (using just the first page with a higher limit for estimation)
      const productsData = await this.getProducts(1, 100);
      
      return {
        users: usersStats.statistics,
        popularProducts: popularProducts.products,
        recentOrders: orders.orders,
        totalProducts: productsData.pagination.total
      };
    } catch (error) {
      console.error('Error fetching dashboard stats:', error);
      throw error;
    }
  }
};

export default adminService;