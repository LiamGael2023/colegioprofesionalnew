-- =====================================================
-- Sistema de Gestión de Colegio Profesional
-- Schema de Base de Datos - MySQL 8.0
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '-05:00';

-- =====================================================
-- Tabla: usuarios
-- Descripción: Usuarios del sistema con diferentes roles
-- =====================================================
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL COMMENT 'Hash bcrypt',
  `nombre_completo` VARCHAR(150) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `rol` ENUM('ADMIN', 'CAJERO') NOT NULL DEFAULT 'CAJERO',
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `ultimo_acceso` DATETIME NULL,
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_username` (`username`),
  INDEX `idx_rol` (`rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: personas
-- Descripción: Registro general de personas (público y colegiados)
-- =====================================================
CREATE TABLE IF NOT EXISTS `personas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `dni` VARCHAR(8) NOT NULL UNIQUE,
  `apellido_paterno` VARCHAR(100) NOT NULL,
  `apellido_materno` VARCHAR(100) NOT NULL,
  `nombres` VARCHAR(100) NOT NULL,
  `fecha_nacimiento` DATE NULL,
  `sexo` ENUM('M', 'F', 'O') NULL,
  `direccion` VARCHAR(255) NULL,
  `distrito` VARCHAR(100) NULL,
  `provincia` VARCHAR(100) NULL,
  `departamento` VARCHAR(100) NULL,
  `telefono` VARCHAR(20) NULL,
  `celular` VARCHAR(20) NULL,
  `email` VARCHAR(100) NULL,
  `es_colegiado` TINYINT(1) NOT NULL DEFAULT 0,
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `creado_por` INT UNSIGNED NULL,
  `actualizado_por` INT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_dni` (`dni`),
  INDEX `idx_apellidos` (`apellido_paterno`, `apellido_materno`),
  INDEX `idx_nombres` (`nombres`),
  INDEX `idx_es_colegiado` (`es_colegiado`),
  FOREIGN KEY (`creado_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`actualizado_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: colegiados
-- Descripción: Información específica de colegiados
-- =====================================================
CREATE TABLE IF NOT EXISTS `colegiados` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `persona_id` INT UNSIGNED NOT NULL UNIQUE,
  `numero_colegiatura` VARCHAR(20) NOT NULL UNIQUE,
  `especialidad` VARCHAR(150) NULL,
  `subespecialidad` VARCHAR(150) NULL,
  `universidad` VARCHAR(200) NULL,
  `fecha_colegiatura` DATE NOT NULL,
  `habilitado` TINYINT(1) NOT NULL DEFAULT 1,
  `meses_impagos` INT UNSIGNED NOT NULL DEFAULT 0,
  `observaciones` TEXT NULL,
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `creado_por` INT UNSIGNED NULL,
  `actualizado_por` INT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_persona_id` (`persona_id`),
  UNIQUE KEY `uk_numero_colegiatura` (`numero_colegiatura`),
  INDEX `idx_habilitado` (`habilitado`),
  INDEX `idx_fecha_colegiatura` (`fecha_colegiatura`),
  INDEX `idx_meses_impagos` (`meses_impagos`),
  FOREIGN KEY (`persona_id`) REFERENCES `personas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`creado_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`actualizado_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: cuotas
-- Descripción: Cuotas mensuales de colegiados
-- =====================================================
CREATE TABLE IF NOT EXISTS `cuotas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `persona_id` INT UNSIGNED NOT NULL,
  `mes` TINYINT UNSIGNED NOT NULL COMMENT '1-12',
  `anio` YEAR NOT NULL,
  `monto` DECIMAL(10,2) NOT NULL DEFAULT 150.00,
  `estado` ENUM('PENDIENTE', 'PAGADO', 'VENCIDO') NOT NULL DEFAULT 'PENDIENTE',
  `fecha_vencimiento` DATE NULL,
  `fecha_pago` DATE NULL,
  `observaciones` VARCHAR(255) NULL,
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_persona_mes_anio` (`persona_id`, `mes`, `anio`),
  INDEX `idx_estado` (`estado`),
  INDEX `idx_mes_anio` (`mes`, `anio`),
  INDEX `idx_fecha_vencimiento` (`fecha_vencimiento`),
  FOREIGN KEY (`persona_id`) REFERENCES `personas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: pagos
-- Descripción: Registro de pagos realizados
-- =====================================================
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `persona_id` INT UNSIGNED NOT NULL,
  `monto_total` DECIMAL(10,2) NOT NULL,
  `metodo_pago` ENUM('EFECTIVO', 'TRANSFERENCIA', 'TARJETA', 'YAPE', 'PLIN') NOT NULL,
  `numero_operacion` VARCHAR(50) NULL COMMENT 'Para transferencias',
  `observaciones` TEXT NULL,
  `fecha_pago` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cajero_id` INT UNSIGNED NOT NULL,
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_persona_id` (`persona_id`),
  INDEX `idx_fecha_pago` (`fecha_pago`),
  INDEX `idx_cajero_id` (`cajero_id`),
  INDEX `idx_metodo_pago` (`metodo_pago`),
  FOREIGN KEY (`persona_id`) REFERENCES `personas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`cajero_id`) REFERENCES `usuarios`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: pago_cuotas
-- Descripción: Relación N:N entre pagos y cuotas
-- =====================================================
CREATE TABLE IF NOT EXISTS `pago_cuotas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pago_id` INT UNSIGNED NOT NULL,
  `cuota_id` INT UNSIGNED NOT NULL,
  `monto_aplicado` DECIMAL(10,2) NOT NULL,
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pago_cuota` (`pago_id`, `cuota_id`),
  INDEX `idx_pago_id` (`pago_id`),
  INDEX `idx_cuota_id` (`cuota_id`),
  FOREIGN KEY (`pago_id`) REFERENCES `pagos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`cuota_id`) REFERENCES `cuotas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: recibos
-- Descripción: Recibos de pago con numeración correlativa
-- =====================================================
CREATE TABLE IF NOT EXISTS `recibos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_recibo` VARCHAR(20) NOT NULL UNIQUE,
  `pago_id` INT UNSIGNED NOT NULL UNIQUE,
  `serie` VARCHAR(10) NOT NULL DEFAULT 'R001',
  `correlativo` INT UNSIGNED NOT NULL,
  `fecha_emision` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_numero_recibo` (`numero_recibo`),
  UNIQUE KEY `uk_pago_id` (`pago_id`),
  INDEX `idx_serie_correlativo` (`serie`, `correlativo`),
  INDEX `idx_fecha_emision` (`fecha_emision`),
  FOREIGN KEY (`pago_id`) REFERENCES `pagos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: auditoria
-- Descripción: Log de todas las operaciones del sistema
-- =====================================================
CREATE TABLE IF NOT EXISTS `auditoria` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` INT UNSIGNED NULL,
  `modulo` VARCHAR(50) NOT NULL,
  `accion` VARCHAR(50) NOT NULL,
  `tabla_afectada` VARCHAR(50) NULL,
  `registro_id` INT UNSIGNED NULL,
  `datos_anteriores` JSON NULL,
  `datos_nuevos` JSON NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_usuario_id` (`usuario_id`),
  INDEX `idx_modulo` (`modulo`),
  INDEX `idx_accion` (`accion`),
  INDEX `idx_tabla_afectada` (`tabla_afectada`),
  INDEX `idx_creado_en` (`creado_en`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: configuracion
-- Descripción: Configuraciones del sistema
-- =====================================================
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `clave` VARCHAR(100) NOT NULL UNIQUE,
  `valor` TEXT NOT NULL,
  `descripcion` VARCHAR(255) NULL,
  `tipo` ENUM('STRING', 'NUMBER', 'BOOLEAN', 'JSON') NOT NULL DEFAULT 'STRING',
  `actualizado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `actualizado_por` INT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_clave` (`clave`),
  FOREIGN KEY (`actualizado_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATOS INICIALES
-- =====================================================

-- Usuario administrador por defecto (password: admin123)
INSERT INTO `usuarios` (`username`, `password`, `nombre_completo`, `email`, `rol`, `activo`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador del Sistema', 'admin@colegio.pe', 'ADMIN', 1);

-- Configuraciones iniciales
INSERT INTO `configuracion` (`clave`, `valor`, `descripcion`, `tipo`) VALUES
('cuota_mensual', '150.00', 'Monto de la cuota mensual en soles', 'NUMBER'),
('tolerancia_meses_impago', '3', 'Número de meses de tolerancia antes de inhabilitar', 'NUMBER'),
('serie_recibo', 'R001', 'Serie actual para recibos', 'STRING'),
('correlativo_recibo', '1', 'Siguiente número correlativo de recibo', 'NUMBER'),
('nombre_colegio', 'Colegio Profesional', 'Nombre del colegio profesional', 'STRING'),
('ruc_colegio', '20123456789', 'RUC del colegio profesional', 'STRING'),
('direccion_colegio', 'Av. Principal 123, Lima', 'Dirección del colegio', 'STRING'),
('telefono_colegio', '(01) 234-5678', 'Teléfono del colegio', 'STRING'),
('email_colegio', 'info@colegio.pe', 'Email del colegio', 'STRING');

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- FIN DEL SCHEMA
-- =====================================================
