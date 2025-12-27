#!/bin/bash
# Script para crear archivo .env automáticamente
# Uso: bash crear_env.sh

echo "Creando archivo .env..."

cat > .env << 'EOF'
# Database Configuration
DB_HOST=localhost
DB_PORT=3307
DB_NAME=colegio_profesional
DB_USER=root
DB_PASS=

# Application Configuration
APP_NAME="Sistema de Gestión de Colegio Profesional"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8080

# Session Configuration
SESSION_LIFETIME=7200
SESSION_NAME=COLEGIO_SESSION

# Security
SECURITY_SALT=change_this_to_random_string_in_production

# Cuotas Configuration
CUOTA_MENSUAL=150.00
CUOTA_TOLERANCIA_MESES=3

# Timezone
APP_TIMEZONE=America/Lima
EOF

echo "✓ Archivo .env creado exitosamente"
echo ""
echo "Configuración actual:"
echo "  DB_HOST: localhost"
echo "  DB_PORT: 3307"
echo "  DB_USER: root"
echo "  DB_PASS: (vacío)"
echo ""
echo "Si necesitas cambiar algo, edita el archivo .env"
