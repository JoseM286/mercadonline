import axios from 'axios';

// Obtenemos la URL base de la API del archivo de entorno o usamos un valor por defecto
const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8080/api';

/**
 * Servicio para manejar la autenticación de usuarios
 */
export default {
  /**
   * Registra un nuevo usuario
   * @param {Object} userData - Datos del usuario a registrar
   * @returns {Promise} - Promesa con los datos del usuario registrado
   */
  async register(userData) {
    try {
      const response = await axios.post(`${API_URL}/users/register`, userData);
      return response.data;
    } catch (error) {
      if (error.response) {
        throw new Error(error.response.data.error || 'Error al registrar usuario');
      } else if (error.request) {
        throw new Error('No se pudo conectar con el servidor');
      } else {
        throw new Error('Error al procesar la solicitud');
      }
    }
  },

  /**
   * Inicia sesión con un usuario
   * @param {Object} credentials - Credenciales del usuario (email y password)
   * @returns {Promise} - Promesa con los datos del usuario autenticado
   */
  async login(credentials) {
    try {
      const response = await axios.post(`${API_URL}/users/login`, credentials);
      // Guardamos el usuario en localStorage para mantener la sesión
      localStorage.setItem('user', JSON.stringify(response.data.user));
      return response.data;
    } catch (error) {
      if (error.response) {
        throw new Error(error.response.data.error || 'Credenciales inválidas');
      } else if (error.request) {
        throw new Error('No se pudo conectar con el servidor');
      } else {
        throw new Error('Error al procesar la solicitud');
      }
    }
  },

  /**
   * Cierra la sesión del usuario actual
   */
  logout() {
    localStorage.removeItem('user');
  },

  /**
   * Obtiene el usuario actualmente autenticado
   * @returns {Object|null} - Usuario autenticado o null si no hay sesión
   */
  getCurrentUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  }
};

