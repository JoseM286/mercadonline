import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL;

/**
 * Servicio para manejar imágenes en la aplicación
 */
class ImageService {
  /**
   * Obtiene la URL de una imagen
   * @param {string} imagePath - Ruta de la imagen
   * @returns {string|null} - URL de la imagen o null si no hay imagen
   */
  getImageUrl(imagePath) {
    if (!imagePath) return null;
    
    // Si la imagen ya es una URL completa, devolverla directamente
    if (imagePath.startsWith('http')) {
      return imagePath;
    }
    
    try {
      // Intentar importar la imagen desde assets
      return new URL(`../assets/images/${imagePath}`, import.meta.url).href;
    } catch (error) {
      console.error('Error loading image:', error);
      return null;
    }
  }
  
  /**
   * Verifica si una URL de imagen es válida
   * @param {string} url - URL de la imagen a verificar
   * @returns {Promise<boolean>} - Promise que se resuelve a true si la imagen es válida
   */
  async isImageValid(url) {
    if (!url) return false;
    
    return new Promise((resolve) => {
      const img = new Image();
      img.onload = () => resolve(true);
      img.onerror = () => resolve(false);
      img.src = url;
    });
  }
  
  /**
   * Sube una imagen al servidor
   * @param {FormData} formData - FormData con la imagen a subir
   * @returns {Promise<Object>} - Promise con la respuesta del servidor
   */
  async uploadImage(formData) {
    try {
      console.log('Intentando subir imagen al servidor...');
      
      const response = await axios.post(`${API_URL}/upload/image`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });
      
      console.log('Respuesta del servidor:', response.data);
      return response.data;
    } catch (error) {
      console.error('Error uploading image:', error);
      
      // Mostrar más detalles del error para depuración
      if (error.response) {
        // El servidor respondió con un código de estado fuera del rango 2xx
        console.error('Respuesta del servidor:', error.response.data);
        console.error('Código de estado:', error.response.status);
      } else if (error.request) {
        // La petición fue hecha pero no se recibió respuesta
        console.error('No se recibió respuesta del servidor');
      } else {
        // Algo ocurrió al configurar la petición
        console.error('Error de configuración:', error.message);
      }
      
      throw error;
    }
  }
}

export default new ImageService();

