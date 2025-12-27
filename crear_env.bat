@echo off
REM Script para crear archivo .env en Windows
REM Uso: crear_env.bat

echo Creando archivo .env...

(
echo # Database Configuration
echo DB_HOST=localhost
echo DB_PORT=3307
echo DB_NAME=colegio_profesional
echo DB_USER=root
echo DB_PASS=
echo.
echo # Application Configuration
echo APP_NAME="Sistema de Gestion de Colegio Profesional"
echo APP_ENV=development
echo APP_DEBUG=true
echo APP_URL=http://localhost:8080
echo.
echo # Session Configuration
echo SESSION_LIFETIME=7200
echo SESSION_NAME=COLEGIO_SESSION
echo.
echo # Security
echo SECURITY_SALT=change_this_to_random_string_in_production
echo.
echo # Cuotas Configuration
echo CUOTA_MENSUAL=150.00
echo CUOTA_TOLERANCIA_MESES=3
echo.
echo # Timezone
echo APP_TIMEZONE=America/Lima
) > .env

echo.
echo [OK] Archivo .env creado exitosamente
echo.
echo Configuracion actual:
echo   DB_HOST: localhost
echo   DB_PORT: 3307
echo   DB_USER: root
echo   DB_PASS: (vacio)
echo.
echo Si necesitas cambiar algo, edita el archivo .env
echo.
pause
