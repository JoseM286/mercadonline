FROM node:20.11-alpine

WORKDIR /app

# Copiar archivos de configuración primero
COPY package*.json ./

# Instalar dependencias
RUN npm install

# Copiar el resto del código
COPY . .

EXPOSE 3000

CMD ["npm", "run", "dev"]



