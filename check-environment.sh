#!/bin/bash

echo "Verificando entorno... en Windows"

# Verificar Docker
if ! command -v docker &> /dev/null; then
    echo "❌ Docker no está instalado"
    exit 1
else
    echo "✅ Docker está instalado"
fi

# Verificar Docker Compose
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose no está instalado"
    exit 1
else
    echo "✅ Docker Compose está instalado"
fi

# Función para verificar puerto en Windows
check_port() {
    if netstat -ano | findstr ":$1" > /dev/null; then
        echo "❌ Puerto $1 está en uso"
        return 1
    else
        echo "✅ Puerto $1 está disponible"
        return 0
    fi
}

# Verificar puertos disponibles
check_port 3000
check_port 8080
check_port 3306

echo "Verificación completada"
