-- 1. Desactivar temporalmente las llaves foráneas para evitar conflictos al insertar
SET FOREIGN_KEY_CHECKS = 0;

-- 2. Limpiar tablas dependientes para evitar duplicados si ya tenías algo
TRUNCATE TABLE facturas;
TRUNCATE TABLE detalles_repuestos_orden;
TRUNCATE TABLE ordenes_servicio;
TRUNCATE TABLE repuestos;
TRUNCATE TABLE vehiculos;
TRUNCATE TABLE usuarios;
TRUNCATE TABLE clientes;
TRUNCATE TABLE logs_sistema;


-- ============================================================
-- 3. INSERTAR 15 USUARIOS
-- ============================================================
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `contrasenia`, `rol`, `estado_activo`) VALUES
  (1, 'Carlos Admin', 'admin@taller.com', '$2y$12$SLntkmnB3CPf/IxY4DnL6.RleEhQKi6O027dgYh1tIoV1S0nT1X9a', 'ADMINISTRADOR', 0),
  (2, 'Ana Recepcion', 'ana@taller.com', '$2y$12$SLntkmnB3CPf/IxY4DnL6.RleEhQKi6O027dgYh1tIoV1S0nT1X9a', 'RECEPCIONISTA', 1),
  (3, 'Juan Mecanico', 'juan@taller.com', '$2y$12$SLntkmnB3CPf/IxY4DnL6.RleEhQKi6O027dgYh1tIoV1S0nT1X9a', 'MECANICO', 1),
  (4, 'Erwin Montoya', 'admin@taller2.com', '$2y$12$SLntkmnB3CPf/IxY4DnL6.RleEhQKi6O027dgYh1tIoV1S0nT1X9a', 'ADMINISTRADOR', 1),
  (5, 'Rebeca Flores', 'rebeca.recepcion@taller.com', '$2y$12$SLntkmnB3CPf/IxY4DnL6.RleEhQKi6O027dgYh1tIoV1S0nT1X9a', 'RECEPCIONISTA', 1),
(6, 'Moisés Alvarenga', 'moises.mecanico@taller.com', '$2y$12$SLntkmnB3CPf/IxY4DnL6.RleEhQKi6O027dgYh1tIoV1S0nT1X9a', 'MECANICO', 1),
(7, 'Carlos Mecánico', 'carlos.mecanico@autos.com', '$2y$12$SLntkmnB3CPf/IxY4DnL6.RleEhQKi6O027dgYh1tIoV1S0nT1X9a', 'Mecanico', 1),
(8, 'Esteban Taller', 'esteban.taller@autos.com', '$2y$12$SLntkmnB3CPf/IxY4DnL6.RleEhQKi6O027dgYh1tIoV1S0nT1X9a', 'Mecanico', 1);
UPDATE `ordenes_servicio` SET `id_mecanico` = 7 WHERE `id_orden` IN (3, 7, 10);
UPDATE `ordenes_servicio` SET `id_mecanico` = 8 WHERE `id_orden` IN (4, 8, 12);
-- ============================================================
-- 3. INSERTAR 15 CLIENTES
-- ============================================================
INSERT INTO `clientes` (`id_cliente`, `nombre`, `telefono`, `rnt_dni`, `estado_activo`) VALUES
(1, 'Juan Pérez', '9999-1234', '0801-1990-12345', 1),
(2, 'María Rodríguez', '8888-4321', '0801-1985-54321', 1),
(3, 'Carlos Martínez', '9700-1122', '0501-1992-09876', 1),
(4, 'Ana Gómez', '3344-5566', '0801-1995-11223', 1),
(5, 'José Hernández', '9876-5432', '0301-1988-33445', 1),
(6, 'Lucía Fernández', '9123-4567', '0801-1998-66778', 1),
(7, 'Pedro Sánchez', '8765-4321', '0401-1991-99887', 1),
(8, 'Carmen Díaz', '9234-5678', '0801-1982-12398', 1),
(9, 'Roberto Castro', '9555-8899', '0201-1993-44556', 1),
(10, 'Sofía Ramírez', '3222-1144', '0801-1996-77889', 1),
(11, 'Miguel Torres', '9444-3322', '0601-1987-22334', 1),
(12, 'Elena Flores', '9111-2233', '0801-1994-55667', 1),
(13, 'Jorge Mendoza', '8999-0011', '0701-1989-88990', 1),
(14, 'Valeria Morales', '9888-7766', '0801-1999-33221', 1),
(15, 'David Castillo', '9666-5544', '0101-1986-11009', 1);

-- ============================================================
-- 4. INSERTAR 15 VEHÍCULOS (Asociados a los 15 clientes)
-- ============================================================
INSERT INTO `vehiculos` (`placa`, `marca`, `modelo`, `anio`, `tipo`, `id_cliente`) VALUES
('HAA1001', 'Toyota', 'Corolla', 2018, 'Sedán', 1),
('HBB2002', 'Honda', 'Civic', 2017, 'Sedán', 2),
('HCC3003', 'Ford', 'Ranger', 2020, 'Pickup', 3),
('HDD4004', 'Hyundai', 'Elantra', 2019, 'Sedán', 4),
('HEE5005', 'Nissan', 'Frontier', 2016, 'Pickup', 5),
('HFF6006', 'Toyota', 'Hilux', 2021, 'Pickup', 6),
('HGG7007', 'Mazda', 'BT-50', 2018, 'Pickup', 7),
('HHH8008', 'Kia', 'Rio', 2020, 'Sedán', 8),
('HII9009', 'Chevrolet', 'D-Max', 2017, 'Pickup', 9),
('HJJ0010', 'Honda', 'CR-V', 2019, 'SUV', 10),
('HKK1111', 'Toyota', 'RAV4', 2021, 'SUV', 11),
('HLL2222', 'Hyundai', 'Tucson', 2018, 'SUV', 12),
('HMM3333', 'Mitsubishi', 'L200', 2015, 'Pickup', 13),
('HNN4444', 'Suzuki', 'Vitara', 2020, 'SUV', 14),
('HOO5555', 'Volkswagen', 'Jetta', 2019, 'Sedán', 15);

-- ============================================================
-- 5. INSERTAR 15 REPUESTOS
-- ============================================================
INSERT INTO `repuestos` (`id_repuesto`, `nombre`, `stock_actual`, `stock_minimo`, `unidad_medida`, `precio_venta`, `estado_activo`) VALUES
(1, 'Filtro de Aceite', 25, 5, 'Unidad', 150.00, 1),
(2, 'Pastillas de Freno Delanteras', 12, 3, 'Juego', 850.00, 1),
(3, 'Bujía de Iridio', 40, 10, 'Unidad', 220.00, 1),
(4, 'Aceite de Motor 20W-50 (Litro)', 50, 15, 'Litro', 180.00, 1),
(5, 'Filtro de Aire', 18, 4, 'Unidad', 280.00, 1),
(6, 'Amortiguador Delantero', 8, 2, 'Unidad', 1450.00, 1),
(7, 'Banda de Distribución', 10, 3, 'Unidad', 950.00, 1),
(8, 'Líquido de Frenos (500ml)', 20, 5, 'Unidad', 130.00, 1),
(9, 'Batería 12V 17 Mas', 6, 2, 'Unidad', 2200.00, 1),
(10, 'Filtro de Combustible', 15, 4, 'Unidad', 310.00, 1),
(11, 'Termostato', 11, 3, 'Unidad', 400.00, 1),
(12, 'Bomba de Agua', 5, 2, 'Unidad', 1850.00, 1),
(13, 'Disco de Freno', 9, 3, 'Unidad', 1200.00, 1),
(14, 'Foco LED H4', 30, 8, 'Par', 450.00, 1),
(15, 'Refrigerante de Motor (Galón)', 22, 6, 'Galón', 260.00, 1);

-- ============================================================
-- 6. INSERTAR 15 ÓRDENES DE SERVICIO 
-- (Usando id_recepcionista = 2 y id_mecanico = 3 de tu tabla de usuarios)
-- ============================================================
INSERT INTO `ordenes_servicio` (`id_orden`, `diagnostico_preliminar`, `estado`, `fecha_ingreso`, `fecha_entrega`, `id_recepcionista`, `id_mecanico`, `placa_vehiculo`, `costo_mano_obra`) VALUES
(1, 'Mantenimiento general y cambio de aceite', 'ENTREGADO', '2026-07-01 08:30:00', '2026-07-01 14:00:00', 2, 3, 'HAA1001', 500.00),
(2, 'Revisión de frenos delanteros y vibración', 'ENTREGADO', '2026-07-02 09:15:00', '2026-07-02 16:30:00', 2, 3, 'HBB2002', 750.00),
(3, 'Cambio de bujías y filtro de aire', 'LISTO', '2026-07-05 10:00:00', '2026-07-05 15:00:00', 2, 3, 'HCC3003', 400.00),
(4, 'Problema con la suspensión delantera', 'EN PROCESO', '2026-07-10 07:45:00', NULL, 2, 3, 'HDD4004', 1200.00),
(5, 'Cambio de batería y revisión eléctrica', 'ENTREGADO', '2026-07-11 11:20:00', '2026-07-11 13:30:00', 2, 3, 'HEE5005', 300.00),
(6, 'Fuga de refrigerante y cambio de termostato', 'ENTREGADO', '2026-07-12 08:00:00', '2026-07-12 17:00:00', 2, 3, 'HFF6006', 900.00),
(7, 'Servicio de 50,000 km y fajas', 'RECIBIDO', '2026-07-15 09:00:00', NULL, 2, 3, 'HGG7007', 1100.00),
(8, 'Ruido extraño al girar el volante', 'RECIBIDO', '2026-07-16 10:30:00', NULL, 2, 3, 'HHH8008', 600.00),
(9, 'Cambio de discos y pastillas de freno', 'LISTO', '2026-07-17 08:15:00', '2026-07-17 16:00:00', 2, 3, 'HII9009', 850.00),
(10, 'Escaneo general por luz Check Engine', 'EN PROCESO', '2026-07-18 13:00:00', NULL, 2, 3, 'HJJ0010', 450.00),
(11, 'Cambio de bomba de agua y refrigerante', 'ENTREGADO', '2026-07-19 09:00:00', '2026-07-19 16:30:00', 2, 3, 'HKK1111', 1300.00),
(12, 'Instalación de luces LED y revisión', 'RECIBIDO', '2026-07-20 14:10:00', NULL, 2, 3, 'HLL2222', 350.00),
(13, 'Mantenimiento de sistema de inyección', 'RECIBIDO', '2026-07-21 08:30:00', NULL, 2, 3, 'HMM3333', 950.00),
(14, 'Cambio de aceite y filtro de combustible', 'RECIBIDO', '2026-07-22 09:45:00', NULL, 2, 3, 'HNN4444', 500.00),
(15, 'Revisión general de pre-viaje', 'RECIBIDO', '2026-07-22 11:00:00', NULL, 2, 3, 'HOO5555', 400.00);

-- ============================================================
-- 7. INSERTAR DETALLES DE REPUESTOS POR ORDEN
-- ============================================================
INSERT INTO `detalles_repuestos_orden` (`id_orden`, `id_repuesto`, `cantidad`, `precio_unitario_historico`) VALUES
(1, 1, 1, 150.00),
(1, 4, 4, 180.00),
(2, 2, 1, 850.00),
(2, 8, 1, 130.00),
(3, 3, 4, 220.00),
(3, 5, 1, 280.00),
(4, 6, 2, 1450.00),
(5, 9, 1, 2200.00),
(6, 11, 1, 400.00),
(6, 15, 2, 260.00),
(9, 2, 1, 850.00),
(9, 13, 2, 1200.00),
(11, 12, 1, 1850.00),
(11, 15, 1, 260.00);

-- ============================================================
-- 8. INSERTAR 15 FACTURAS (Para las órdenes completadas/entregadas)
-- ============================================================
INSERT INTO `facturas` (`numero_factura`, `fecha_emision`, `subtotal_mano_obra`, `subtotal_repuestos`, `isv`, `total_pagar`, `id_orden`, `metodo_pago`, `estado_activo`) VALUES
('FAC-2026-0001', '2026-07-01 14:10:00', 500.00, 870.00, 205.50, 1575.50, 1, 'Efectivo', 1),
('FAC-2026-0002', '2026-07-02 16:45:00', 750.00, 980.00, 259.50, 1989.50, 2, 'Tarjeta', 1),
('FAC-2026-0003', '2026-07-05 15:15:00', 400.00, 1160.00, 234.00, 1794.00, 3, 'Transferencia', 1),
('FAC-2026-0004', '2026-07-11 13:45:00', 300.00, 2200.00, 375.00, 2875.00, 5, 'Efectivo', 1),
('FAC-2026-0005', '2026-07-12 17:15:00', 900.00, 920.00, 273.00, 2093.00, 6, 'Tarjeta', 1),
('FAC-2026-0006', '2026-07-17 16:10:00', 850.00, 3250.00, 615.00, 4715.00, 9, 'Transferencia', 1),
('FAC-2026-0007', '2026-07-19 16:45:00', 1300.00, 2110.00, 511.50, 3921.50, 11, 'Efectivo', 1),
('FAC-2026-0008', '2026-07-20 10:00:00', 400.00, 500.00, 135.00, 1035.00, 4, 'Tarjeta', 1),
('FAC-2026-0009', '2026-07-20 11:00:00', 500.00, 450.00, 142.50, 1092.50, 7, 'Efectivo', 1),
('FAC-2026-0010', '2026-07-21 12:00:00', 600.00, 800.00, 210.00, 1610.00, 8, 'Transferencia', 1),
('FAC-2026-0011', '2026-07-21 14:00:00', 450.00, 300.00, 112.50, 862.50, 10, 'Efectivo', 1),
('FAC-2026-0012', '2026-07-21 15:00:00', 350.00, 600.00, 142.50, 1092.50, 12, 'Tarjeta', 1),
('FAC-2026-0013', '2026-07-22 08:00:00', 950.00, 1100.00, 307.50, 2357.50, 13, 'Transferencia', 1),
('FAC-2026-0014', '2026-07-22 09:00:00', 500.00, 400.00, 135.00, 1035.00, 14, 'Efectivo', 1),
('FAC-2026-0015', '2026-07-22 10:00:00', 400.00, 550.00, 142.50, 1092.50, 15, 'Tarjeta', 1);

UPDATE ordenes_servicio 
SET fecha_ingreso = '2026-06-15 08:30:00' 
WHERE id_orden = 1;
-- 1. Insertar una orden nueva de prueba (ej. con el vehículo de la placa HAA1001)
INSERT INTO `ordenes_servicio` (`diagnostico_preliminar`, `estado`, `fecha_ingreso`, `id_recepcionista`, `id_mecanico`, `placa_vehiculo`, `costo_mano_obra`) 
VALUES ('Revisión extra para nueva factura', 'ENTREGADO', '2026-07-22 14:00:00', 2, 3, 'HAA1001', 450.00);

-- 9. Volver a activar la verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 1;