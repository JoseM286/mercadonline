import axios from 'axios';

// Obtenemos la URL base de la API del archivo de entorno o usamos un valor por defecto
// Aseguramos que la URL termine con /api
const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8080';
const API_URL = baseUrl.endsWith('/api') ? baseUrl : `${baseUrl}/api`;

/**
 * Servicio para manejar operaciones relacionadas con usuarios
 */
export default {
  /**
   * Obtiene el perfil del usuario actual
   * @returns {Promise} - Promesa con los datos del perfil
   */
  async getProfile() {
    try {
      const response = await axios.get(`${API_URL}/users/profile`, {
        withCredentials: true // Importante para enviar cookies de sesión
      });
      return response.data;
    } catch (error) {
      console.error('Error detallado:', error);
      if (error.response) {
        throw new Error(error.response.data.error || 'Error al obtener el perfil');
      } else if (error.request) {
        throw new Error('No se pudo conectar con el servidor');
      } else {
        throw new Error('Error al procesar la solicitud');
      }
    }
  },

  /**
   * Actualiza el perfil del usuario actual
   * @param {Object} profileData - Datos del perfil a actualizar
   * @returns {Promise} - Promesa con los datos actualizados
   */
  async updateProfile(profileData) {
    try {
      const response = await axios.put(`${API_URL}/users/profile`, profileData, {
        withCredentials: true // Importante para enviar cookies de sesión
      });
      return response.data;
    } catch (error) {
      console.error('Error detallado:', error);
      if (error.response) {
        throw new Error(error.response.data.error || 'Error al actualizar el perfil');
      } else if (error.request) {
        throw new Error('No se pudo conectar con el servidor');
      } else {
        throw new Error('Error al procesar la solicitud');
      }
    }
  },

  /**
   * Cambia la contraseña del usuario actual
   * @param {Object} passwordData - Datos de contraseña (currentPassword, newPassword)
   * @returns {Promise} - Promesa con el resultado de la operación
   */
  async changePassword(passwordData) {
    try {
      const response = await axios.post(`${API_URL}/users/change-password`, passwordData, {
        withCredentials: true
      });
      return response.data;
    } catch (error) {
      if (error.response) {
        throw new Error(error.response.data.error || 'Error al cambiar la contraseña');
      } else if (error.request) {
        throw new Error('No se pudo conectar con el servidor');
      } else {
        throw new Error('Error al procesar la solicitud');
      }
    }
  }
};
