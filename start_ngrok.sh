#!/bin/bash

# 1. Iniciar Base de Datos (Docker)
echo "🚀 Iniciando base de datos..."
# docker-compose up -d db

# Esperar a que la DB esté lista (opcional, pero recomendado)
echo "⏳ Esperando a la base de datos..."
sleep 5

# 2. Iniciar Servidor Laravel (en segundo plano)
echo "🚀 Iniciando servidor Laravel..."
nohup php -d extension=gd artisan serve --port=8002 > laravel.log 2>&1 &
LARAVEL_PID=$!

# 3. Iniciar Ngrok (en segundo plano)
echo "🚀 Iniciando Ngrok..."
nohup ngrok http 8002 > ngrok.log 2>&1 &
NGROK_PID=$!

# Esperar a que Ngrok genere la URL
sleep 3

# 4. Obtener la URL de Ngrok
# Intentar obtener desde la API local (más robusto)
if command -v jq >/dev/null 2>&1; then
    NGROK_URL=$(curl -s http://localhost:4040/api/tunnels | jq -r '.tunnels[] | select(.proto=="https") | .public_url' | head -n1)
else
    # Fallback si no hay jq
    NGROK_URL=$(curl -s http://localhost:4040/api/tunnels | grep -o 'https://[^"]*\.ngrok-free\.dev' | head -n1)
fi

# Fallback final: revisar el log si la API falló
if [ -z "$NGROK_URL" ]; then
    NGROK_URL=$(grep -o 'https://[^"]*\.ngrok-free\.dev' ngrok.log | head -n1)
fi

if [ -z "$NGROK_URL" ]; then
    echo "❌ No se pudo obtener la URL de Ngrok. Revisa ngrok.log"
    # Matar procesos si falla
    kill $LARAVEL_PID
    kill $NGROK_PID
    exit 1
fi

echo "✅ URL de Ngrok detectada: $NGROK_URL"

# 5. Actualizar .env
echo "🔄 Actualizando configuración..."
sed -i "s|APP_URL=.*|APP_URL=$NGROK_URL|" .env

# Asegurar que NGROK_URL=true esté presente
if ! grep -q "NGROK_URL=true" .env; then
    echo "NGROK_URL=true" >> .env
fi

# 6. Reconstruir Assets y Limpiar Caché
echo "🔨 Construyendo assets..."
npm run build > /dev/null 2>&1

echo "🧹 Limpiando caché..."
php artisan config:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1

echo ""
echo "🎉 ¡Todo listo!"
echo "🌍 Tu aplicación está disponible en: $NGROK_URL"
echo ""
echo "⚠️  Para detener todo, ejecuta: kill $LARAVEL_PID $NGROK_PID"
