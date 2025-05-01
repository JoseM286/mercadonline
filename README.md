# MercadonLine

## Requisitos previos

- Docker
- Docker Compose

## Instalación y ejecución

1. Clonar el repositorio:

```bash
git clone https://github.com/JoseM286/mercadonline.git
cd MercadonLine
```

2. Configurar variables de entorno:

```bash
cp .env.example .env
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env
```

3. Ejecutar script de verificación de Windows:

```bash
chmod +x check-environment.sh
./check-environment.sh
```

4. Iniciar los servicios:

```bash
docker-compose up -d
```

5. Verificar que todo funciona:

```bash
docker-compose ps
docker-compose logs -f frontend
```

## Puertos utilizados

- Frontend: http://localhost:3000
- Backend: http://localhost:8080
- MySQL: 3306

## Solución de problemas comunes

Si el frontend no inicia correctamente:

```bash
docker-compose down
docker-compose build --no-cache frontend
docker-compose up -d
```
