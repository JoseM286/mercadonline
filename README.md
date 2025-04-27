# MercadonLine

## Requisitos previos

- Docker
- Docker Compose

## Instalación y ejecución

1. Clonar el repositorio:

```bash
git clone <url-del-repositorio>
cd MercadonLine
```

2. Ejecutar script de verificación de Windows:

```bash
chmod +x check-environment.sh
./check-environment.sh
```

3. Iniciar los servicios:

```bash
docker-compose up -d
```

4. Verificar que todo funciona:

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
