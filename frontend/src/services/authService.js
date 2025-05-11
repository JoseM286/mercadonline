import axios from 'axios';

// Obtenemos la URL base de la API del archivo de entorno o usamos un valor por defecto
const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8080/api';

const authService = {
  /**
   * Inicia sesión con las credenciales proporcionadas
   * @param {Object} credentials - Credenciales del usuario (email, password)
   * @returns {Promise} - Promesa con los datos del usuario autenticado
   */
  async login(credentials) {
    try {
      const response = await axios.post(`${API_URL}/users/login`, credentials, {
        withCredentials: true // Importante para manejar cookies de sesión
      });
      return response.data;
    } catch (error) {
      // Procesamos el error para devolver un mensaje más amigable
      if (error.response) {
        // El servidor respondió con un código de estado fuera del rango 2xx
        throw new Error(error.response.data.error || 'Error de autenticación');
      } else if (error.request) {
        // La petición fue hecha pero no se recibió respuesta
        throw new Error('No se pudo conectar con el servidor');
      } else {
        // Algo ocurrió al configurar la petición
        throw new Error('Error al procesar la solicitud');
      }
    }
  },

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
   * Cierra la sesión del usuario actual
   * @returns {Promise} - Promesa con el resultado del cierre de sesión
   */
  async logout() {
    try {
      const response = await axios.post(`${API_URL}/users/logout`, {}, {
        withCredentials: true
      });
      return response.data;
    } catch (error) {
      console.error('Error al cerrar sesión:', error);
      // Incluso si hay un error, consideramos que el usuario ha cerrado sesión localmente
      throw new Error('Error al cerrar sesión en el servidor');
    }
  },

  /**
   * Obtiene el perfil del usuario actual
   * @returns {Promise} - Promesa con los datos del perfil del usuario
   */
  async getProfile() {
    try {
      const response = await axios.get(`${API_URL}/users/profile`, {
        withCredentials: true
      });
      return response.data;
    } catch (error) {
      if (error.response && error.response.status === 401) {
        throw new Error('Sesión expirada o usuario no autenticado');
      } else {
        throw new Error('Error al obtener el perfil');
      }
    }
  }
};

export default authService;