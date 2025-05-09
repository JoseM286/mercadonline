FROM node:20.11-alpine

WORKDIR /app

# Copiar archivos de configuración primero
COPY package*.json ./

# Instalar dependencias
RUN npm install

# Copiar el resto del código
COPY . .

# Variables de entorno para mejorar la detección de cambios
ENV CHOKIDAR_USEPOLLING=true
ENV WATCHPACK_POLLING=true

EXPOSE 3000

# Usar nodemon para reiniciar en caso de problemas
RUN npm install -g nodemon

# Usar nodemon para ejecutar el servidor de desarrollo
CMD ["nodemon", "--exec", "npm run dev", "--watch", "src", "--watch", "vite.config.js"]









