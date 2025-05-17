import axios from 'axios';

// Obtenemos la URL base de la API del archivo de entorno o usamos un valor por defecto
// Aseguramos que la URL termine con /api
const API_URL = import.meta.env.VITE_API_URL;

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
      const response = await axios.post(`${API_URL}/users/login`, credentials, {
        withCredentials: true
      });
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
   * @returns {Promise} - Promesa con el resultado de la operación
   */
  async logout() {
    try {
      // Aseguramos que estamos usando la URL correcta y el método correcto
      const logoutUrl = `${API_URL}/users/logout`;
      console.log('Intentando cerrar sesión en:', logoutUrl);

      // Usamos directamente axios.post para asegurar que se usa el método POST
      const response = await axios.post(logoutUrl, {}, {
        withCredentials: true,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      });

      // Eliminar datos del usuario del localStorage
      localStorage.removeItem('user');
      return response.data;
    } catch (error) {
      console.error('Error en logout:', error);
      // Incluso si hay error, eliminamos los datos locales
      localStorage.removeItem('user');
      if (error.response) {
        throw new Error(error.response.data.error || 'Error al cerrar sesión');
      } else if (error.request) {
        throw new Error('No se pudo conectar con el servidor');
      } else {
        throw new Error('Error al procesar la solicitud');
      }
    }
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



