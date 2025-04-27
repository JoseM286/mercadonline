# Imagen base de node.js 16 basada en alpine
FROM node:16-alpine

# Cualquier comando que se ejecute en la imagen se ejecutará en este directorio
WORKDIR /app

# Copia el archivo package.json y package-lock.json en el directorio de trabajo
COPY package*.json ./
# Copiar solo los archivos de configuración primero
COPY vite.config.js ./
COPY .eslintrc.* ./
COPY tsconfig*.json ./

# Instalar dependencias
RUN npm install

# Copiar el resto del código
COPY . .

# Exponer el puerto
EXPOSE 3000

# Comando para ejecutar en desarrollo
CMD ["npm", "run", "dev", "--", "--host"]