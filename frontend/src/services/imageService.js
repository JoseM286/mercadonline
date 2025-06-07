import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL;

/**
 * Servicio para manejar imágenes en la aplicación
 */
class ImageService {
  getImageUrl(imagePath) {
    if (!imagePath) {
      return null;
    }
    return imagePath;
  }
}

export default new ImageService();

