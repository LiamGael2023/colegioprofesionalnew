-- =====================================================
-- DATOS DE PRUEBA
-- Sistema de Gestión de Colegio Profesional
-- =====================================================

USE colegio_profesional;

-- Insertar personas de prueba
INSERT INTO `personas` (`dni`, `apellido_paterno`, `apellido_materno`, `nombres`, `fecha_nacimiento`, `sexo`, `direccion`, `distrito`, `provincia`, `departamento`, `telefono`, `celular`, `email`, `es_colegiado`, `creado_por`) VALUES
('12345678', 'García', 'López', 'Juan Carlos', '1985-03-15', 'M', 'Av. Los Pinos 123', 'Miraflores', 'Lima', 'Lima', '01-2345678', '987654321', 'juan.garcia@email.com', 1, 1),
('23456789', 'Rodríguez', 'Martínez', 'María Elena', '1990-07-22', 'F', 'Jr. Las Flores 456', 'San Isidro', 'Lima', 'Lima', '01-3456789', '987654322', 'maria.rodriguez@email.com', 1, 1),
('34567890', 'Fernández', 'Sánchez', 'Pedro José', '1988-11-10', 'M', 'Calle Los Álamos 789', 'Surco', 'Lima', 'Lima', '01-4567890', '987654323', 'pedro.fernandez@email.com', 1, 1),
('45678901', 'Torres', 'Ramírez', 'Ana Lucía', '1992-05-18', 'F', 'Av. La Marina 321', 'San Miguel', 'Lima', 'Lima', '01-5678901', '987654324', 'ana.torres@email.com', 1, 1),
('56789012', 'Vargas', 'Castillo', 'Luis Alberto', '1987-09-25', 'M', 'Jr. Los Sauces 654', 'Jesús María', 'Lima', 'Lima', '01-6789012', '987654325', 'luis.vargas@email.com', 1, 1),
('67890123', 'Mendoza', 'Flores', 'Carmen Rosa', '1991-02-14', 'F', 'Calle Las Orquídeas 987', 'Lince', 'Lima', 'Lima', '01-7890123', '987654326', 'carmen.mendoza@email.com', 0, 1),
('78901234', 'Quispe', 'Huamán', 'José Antonio', '1989-06-30', 'M', 'Av. Los Héroes 147', 'La Victoria', 'Lima', 'Lima', '01-8901234', '987654327', 'jose.quispe@email.com', 0, 1),
('89012345', 'Paredes', 'Vega', 'Rosa María', '1993-12-08', 'F', 'Jr. Los Girasoles 258', 'Breña', 'Lima', 'Lima', '01-9012345', '987654328', 'rosa.paredes@email.com', 0, 1);

-- Insertar colegiados
INSERT INTO `colegiados` (`persona_id`, `numero_colegiatura`, `especialidad`, `subespecialidad`, `universidad`, `fecha_colegiatura`, `habilitado`, `meses_impagos`, `creado_por`) VALUES
(1, '2023-0001', 'Medicina General', 'Cardiología', 'Universidad Nacional Mayor de San Marcos', '2023-01-15', 1, 0, 1),
(2, '2023-0002', 'Derecho Civil', 'Derecho Comercial', 'Pontificia Universidad Católica del Perú', '2023-02-20', 1, 0, 1),
(3, '2023-0003', 'Ingeniería Civil', 'Estructuras', 'Universidad Nacional de Ingeniería', '2023-03-10', 1, 0, 1),
(4, '2024-0001', 'Medicina General', 'Pediatría', 'Universidad Cayetano Heredia', '2024-01-05', 1, 0, 1),
(5, '2024-0002', 'Ingeniería de Sistemas', 'Desarrollo de Software', 'Universidad de Lima', '2024-02-15', 1, 0, 1);

-- Generar cuotas para los últimos 6 meses
INSERT INTO `cuotas` (`persona_id`, `mes`, `anio`, `monto`, `estado`, `fecha_vencimiento`) VALUES
-- Colegiado 1 (Juan García) - Pagó todos
(1, 7, 2024, 150.00, 'PAGADO', '2024-07-31'),
(1, 8, 2024, 150.00, 'PAGADO', '2024-08-31'),
(1, 9, 2024, 150.00, 'PAGADO', '2024-09-30'),
(1, 10, 2024, 150.00, 'PAGADO', '2024-10-31'),
(1, 11, 2024, 150.00, 'PAGADO', '2024-11-30'),
(1, 12, 2024, 150.00, 'PAGADO', '2024-12-31'),

-- Colegiado 2 (María Rodríguez) - Debe 2 meses
(2, 7, 2024, 150.00, 'PAGADO', '2024-07-31'),
(2, 8, 2024, 150.00, 'PAGADO', '2024-08-31'),
(2, 9, 2024, 150.00, 'PAGADO', '2024-09-30'),
(2, 10, 2024, 150.00, 'PAGADO', '2024-10-31'),
(2, 11, 2024, 150.00, 'PENDIENTE', '2024-11-30'),
(2, 12, 2024, 150.00, 'PENDIENTE', '2024-12-31'),

-- Colegiado 3 (Pedro Fernández) - Debe 4 meses (debe estar inhabilitado)
(3, 7, 2024, 150.00, 'PAGADO', '2024-07-31'),
(3, 8, 2024, 150.00, 'PAGADO', '2024-08-31'),
(3, 9, 2024, 150.00, 'VENCIDO', '2024-09-30'),
(3, 10, 2024, 150.00, 'VENCIDO', '2024-10-31'),
(3, 11, 2024, 150.00, 'VENCIDO', '2024-11-30'),
(3, 12, 2024, 150.00, 'PENDIENTE', '2024-12-31'),

-- Colegiado 4 (Ana Torres) - Al día
(4, 7, 2024, 150.00, 'PAGADO', '2024-07-31'),
(4, 8, 2024, 150.00, 'PAGADO', '2024-08-31'),
(4, 9, 2024, 150.00, 'PAGADO', '2024-09-30'),
(4, 10, 2024, 150.00, 'PAGADO', '2024-10-31'),
(4, 11, 2024, 150.00, 'PAGADO', '2024-11-30'),
(4, 12, 2024, 150.00, 'PAGADO', '2024-12-31'),

-- Colegiado 5 (Luis Vargas) - Debe 1 mes
(5, 7, 2024, 150.00, 'PAGADO', '2024-07-31'),
(5, 8, 2024, 150.00, 'PAGADO', '2024-08-31'),
(5, 9, 2024, 150.00, 'PAGADO', '2024-09-30'),
(5, 10, 2024, 150.00, 'PAGADO', '2024-10-31'),
(5, 11, 2024, 150.00, 'PAGADO', '2024-11-30'),
(5, 12, 2024, 150.00, 'PENDIENTE', '2024-12-31');

-- Insertar algunos pagos de ejemplo
INSERT INTO `pagos` (`persona_id`, `monto_total`, `metodo_pago`, `fecha_pago`, `cajero_id`) VALUES
(1, 900.00, 'TRANSFERENCIA', '2024-12-15 10:30:00', 1),
(2, 600.00, 'EFECTIVO', '2024-12-10 14:20:00', 1),
(3, 300.00, 'TARJETA', '2024-08-20 11:15:00', 1),
(4, 900.00, 'YAPE', '2024-12-20 16:45:00', 1),
(5, 750.00, 'EFECTIVO', '2024-12-18 09:30:00', 1);

-- Relacionar pagos con cuotas
INSERT INTO `pago_cuotas` (`pago_id`, `cuota_id`, `monto_aplicado`) VALUES
-- Pago 1 - Juan García pagó 6 meses
(1, 1, 150.00), (1, 2, 150.00), (1, 3, 150.00), (1, 4, 150.00), (1, 5, 150.00), (1, 6, 150.00),
-- Pago 2 - María Rodríguez pagó 4 meses
(2, 7, 150.00), (2, 8, 150.00), (2, 9, 150.00), (2, 10, 150.00),
-- Pago 3 - Pedro Fernández pagó 2 meses
(3, 13, 150.00), (3, 14, 150.00),
-- Pago 4 - Ana Torres pagó 6 meses
(4, 19, 150.00), (4, 20, 150.00), (4, 21, 150.00), (4, 22, 150.00), (4, 23, 150.00), (4, 24, 150.00),
-- Pago 5 - Luis Vargas pagó 5 meses
(5, 25, 150.00), (5, 26, 150.00), (5, 27, 150.00), (5, 28, 150.00), (5, 29, 150.00);

-- Generar recibos
INSERT INTO `recibos` (`numero_recibo`, `pago_id`, `serie`, `correlativo`) VALUES
('R001-00000001', 1, 'R001', 1),
('R001-00000002', 2, 'R001', 2),
('R001-00000003', 3, 'R001', 3),
('R001-00000004', 4, 'R001', 4),
('R001-00000005', 5, 'R001', 5);

-- Actualizar correlativo en configuración
UPDATE `configuracion` SET `valor` = '6' WHERE `clave` = 'correlativo_recibo';

-- Actualizar meses impagos de colegiados
UPDATE `colegiados` SET `meses_impagos` = 0, `habilitado` = 1 WHERE `persona_id` = 1;
UPDATE `colegiados` SET `meses_impagos` = 2, `habilitado` = 1 WHERE `persona_id` = 2;
UPDATE `colegiados` SET `meses_impagos` = 4, `habilitado` = 0 WHERE `persona_id` = 3; -- Inhabilitado
UPDATE `colegiados` SET `meses_impagos` = 0, `habilitado` = 1 WHERE `persona_id` = 4;
UPDATE `colegiados` SET `meses_impagos` = 1, `habilitado` = 1 WHERE `persona_id` = 5;

-- Registrar en auditoría
INSERT INTO `auditoria` (`usuario_id`, `modulo`, `accion`, `tabla_afectada`, `ip_address`) VALUES
(1, 'SEEDERS', 'DATOS_PRUEBA_CARGADOS', 'multiple', '127.0.0.1');

-- =====================================================
-- FIN DE DATOS DE PRUEBA
-- =====================================================

SELECT 'Datos de prueba cargados exitosamente!' AS Mensaje;
SELECT CONCAT('Total Personas: ', COUNT(*)) AS Total FROM personas;
SELECT CONCAT('Total Colegiados: ', COUNT(*)) AS Total FROM colegiados;
SELECT CONCAT('Total Cuotas: ', COUNT(*)) AS Total FROM cuotas;
SELECT CONCAT('Total Pagos: ', COUNT(*)) AS Total FROM pagos;
