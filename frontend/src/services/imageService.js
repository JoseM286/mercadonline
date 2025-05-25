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
}

export default new ImageService();