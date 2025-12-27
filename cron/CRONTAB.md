# Configuración de CRON para Generación Automática de Cuotas

## Instrucciones

Para configurar la generación automática de cuotas el día 1 de cada mes:

### 1. Editar crontab

```bash
crontab -e
```

### 2. Agregar la siguiente línea

```cron
# Generar cuotas mensuales el día 1 de cada mes a las 00:00
0 0 1 * * /usr/local/bin/php /var/www/html/cron/generar_cuotas.php >> /var/www/html/logs/cron.log 2>&1
```

### 3. Verificar que el cron está registrado

```bash
crontab -l
```

## Ejecución Manual

Para ejecutar el script manualmente:

```bash
php /var/www/html/cron/generar_cuotas.php
```

## Dentro de Docker

Si estás usando Docker, ejecuta dentro del contenedor:

```bash
docker exec -it colegio_web bash
crontab -e
# Agregar la línea del cron
```

O ejecutar manualmente:

```bash
docker exec colegio_web php /var/www/html/cron/generar_cuotas.php
```

## Logs

Los logs de ejecución se guardan en:
- `/var/www/html/logs/cron.log` - Log de ejecución del script
- Auditoría en la base de datos (tabla `auditoria`)

## Formato de Cron

```
* * * * * comando
│ │ │ │ │
│ │ │ │ └─── Día de la semana (0-7, 0 y 7 = domingo)
│ │ │ └───── Mes (1-12)
│ │ └─────── Día del mes (1-31)
│ └───────── Hora (0-23)
└─────────── Minuto (0-59)
```
