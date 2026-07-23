-- ============================================================
-- datos_prueba.sql - Datos de prueba para Taller Mecánico
-- ============================================================
-- Ejecutar DESPUÉS de Base_taller.sql y stored_procedures.sql
-- ============================================================

USE `taller_mecanico`;

-- ============================================================
-- 1. USUARIO ADMINISTRADOR (si no existe)
-- ============================================================
-- Password: admin123 (bcrypt cost 12)
INSERT IGNORE INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `contrasenia`, `rol`, `estado_activo`) VALUES
(1, 'Administrador', 'admin@taller.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj/RK.PZvO.S', 'ADMINISTRADOR', 1);

-- ============================================================
-- 2. CLIENTES DE PRUEBA
-- ============================================================
INSERT INTO `clientes` (`nombre`, `telefono`, `rnt_dni`, `estado_activo`) VALUES
('Juan Pérez López', '9999-1234', '0801-1990-00123', 1),
('María González', '8888-5678', '0501-1985-00456', 1),
('Carlos Rodríguez', '7777-9012', '0801-1992-00789', 1),
('Ana Martínez', '6666-3456', '1201-1988-00234', 1),
('Luis Fernández', '5555-7890', '1501-1995-00567', 1),
('Carmen Díaz', '4444-2345', '0801-1991-00890', 1),
('Pedro Sánchez', '3333-6789', '0501-1987-00345', 1),
('Laura Torres', '2222-1122', '1201-1993-00678', 1),
('Miguel Herrera', '1111-3344', '1501-1989-00901', 1),
('Sofía Jiménez', '9999-5566', '0801-1994-00234', 1);

-- ============================================================
-- 3. VEHÍCULOS DE PRUEBA
-- ============================================================
INSERT INTO `vehiculos` (`placa`, `marca`, `modelo`, `anio`, `tipo`, `id_cliente`) VALUES
('ABC123', 'Toyota', 'Corolla', 2020, 'Sedán', 1),
('XYZ789', 'Honda', 'Civic', 2019, 'Sedán', 2),
('DEF456', 'Ford', 'Ranger', 2021, 'Pickup', 3),
('GHI321', 'Chevrolet', 'Spark', 2018, 'Hatchback', 4),
('JKL654', 'Nissan', 'Sentra', 2022, 'Sedán', 5),
('MNO987', 'Hyundai', 'Tucson', 2020, 'SUV', 6),
('PQR147', 'Kia', 'Sportage', 2021, 'SUV', 7),
('STU258', 'Mazda', 'CX-5', 2019, 'SUV', 8),
('VWX369', 'Volkswagen', 'Jetta', 2020, 'Sedán', 9),
('YZA741', 'Toyota', 'Hilux', 2022, 'Pickup', 10);

-- ============================================================
-- 4. REPUESTOS DE PRUEBA
-- ============================================================
INSERT INTO `repuestos` (`nombre`, `stock_actual`, `stock_minimo`, `unidad_medida`, `precio_venta`, `estado_activo`) VALUES
('Filtro de aceite', 50, 10, 'UND', 85.00, 1),
('Filtro de aire', 30, 5, 'UND', 120.00, 1),
('Pastillas de freno delanteras', 25, 8, 'JUE', 450.00, 1),
('Pastillas de freno traseras', 20, 5, 'JUE', 380.00, 1),
('Aceite motor 5W-30 (1L)', 100, 20, 'UND', 95.00, 1),
('Aceite motor 10W-40 (1L)', 80, 15, 'UND', 90.00, 1),
('Bujías (juego de 4)', 40, 10, 'JUE', 180.00, 1),
('Correa de distribución', 15, 3, 'UND', 320.00, 1),
('Batería 60Ah', 10, 2, 'UND', 850.00, 1),
('Amortiguadores delanteros (par)', 12, 4, 'PAR', 1200.00, 1),
('Amortiguadores traseros (par)', 10, 3, 'PAR', 1100.00, 1),
('Disco de freno delantero', 20, 5, 'UND', 550.00, 1),
('Disco de freno trasero', 18, 4, 'UND', 480.00, 1),
('Líquido de frenos DOT 4 (500ml)', 35, 10, 'UND', 75.00, 1),
('Refrigerante verde (1L)', 45, 10, 'UND', 65.00, 1);

-- ============================================================
-- 5. ÓRDENES DE SERVICIO DE PRUEBA
-- ============================================================
-- Recepcionista: Ana (id=2), Mecánico: Juan (id=3)
INSERT INTO `ordenes_servicio` (`diagnostico_preliminar`, `estado`, `fecha_ingreso`, `fecha_entrega`, `id_recepcionista`, `id_mecanico`, `placa_vehiculo`) VALUES
('Cambio de aceite y filtro', 'ENTREGADO', '2026-01-15 08:30:00', '2026-01-15 10:00:00', 2, 3, 'ABC123'),
('Revisión de frenos', 'ENTREGADO', '2026-01-20 09:00:00', '2026-01-20 12:30:00', 2, 3, 'XYZ789'),
('Mantenimiento 50,000 km', 'ENTREGADO', '2026-02-05 08:00:00', '2026-02-05 14:00:00', 2, 3, 'DEF456'),
('Cambio de bujías', 'ENTREGADO', '2026-02-10 10:00:00', '2026-02-10 11:30:00', 2, 3, 'GHI321'),
('Alineación y balanceo', 'EN PROCESO', '2026-02-15 08:30:00', NULL, 2, 3, 'JKL654'),
('Revisión suspensión', 'RECIBIDO', '2026-02-18 09:00:00', NULL, 2, 3, 'MNO987'),
('Cambio de correa distribución', 'EN PROCESO', '2026-02-20 08:00:00', NULL, 2, 3, 'PQR147'),
('Revisión aire acondicionado', 'RECIBIDO', '2026-02-22 10:30:00', NULL, 2, 3, 'STU258'),
('Fuga de aceite', 'LISTO', '2026-02-25 08:00:00', '2026-02-26 16:00:00', 2, 3, 'VWX369'),
('Mantenimiento general', 'RECIBIDO', '2026-02-28 08:30:00', NULL, 2, 3, 'YZA741');

-- ============================================================
-- 6. DETALLES DE REPUESTOS EN ÓRDENES
-- ============================================================
-- Orden 1: Cambio aceite (filtro aceite + aceite 5W-30)
INSERT INTO `detalles_repuestos_orden` (`id_orden`, `id_repuesto`, `cantidad`, `precio_unitario_historico`) VALUES
(1, 1, 1, 85.00),   -- Filtro de aceite
(1, 5, 4, 95.00),   -- Aceite 5W-30 x4

-- Orden 2: Frenos (pastillas delanteras + discos delanteros + líquido frenos)
(2, 3, 1, 450.00),  -- Pastillas delanteras
(2, 12, 2, 550.00), -- Discos delanteros x2
(2, 14, 1, 75.00),  -- Líquido frenos

-- Orden 3: Mantenimiento 50k (filtro aceite + filtro aire + aceite 5W-30 + bujías)
(3, 1, 1, 85.00),   -- Filtro aceite
(3, 2, 1, 120.00),  -- Filtro aire
(3, 5, 4, 95.00),   -- Aceite 5W-30 x4
(3, 7, 1, 180.00),  -- Bujías

-- Orden 4: Bujías
(4, 7, 1, 180.00),  -- Bujías

-- Orden 5: Alineación (sin repuestos, solo mano de obra)

-- Orden 6: Suspensión (amortiguadores)
(6, 10, 1, 1200.00), -- Amortiguadores delanteros
(6, 11, 1, 1100.00), -- Amortiguadores traseros

-- Orden 7: Correa distribución
(7, 8, 1, 320.00),  -- Correa distribución

-- Orden 8: A/C (refrigerante)
(8, 15, 2, 65.00),  -- Refrigerante x2

-- Orden 9: Fuga aceite (junta + aceite)
(9, 5, 4, 95.00),   -- Aceite 5W-30 x4

-- Orden 10: Mantenimiento general (filtro aceite + filtro aire + aceite 10W-40)
(10, 1, 1, 85.00),  -- Filtro aceite
(10, 2, 1, 120.00), -- Filtro aire
(10, 6, 4, 90.00);  -- Aceite 10W-40 x4

-- ============================================================
-- 7. FACTURAS DE PRUEBA
-- ============================================================
INSERT INTO `facturas` (`numero_factura`, `fecha_emision`, `subtotal_mano_obra`, `subtotal_repuestos`, `isv`, `total_pagar`, `id_orden`) VALUES
('FAC-2026-0001', '2026-01-15 10:05:00', 150.00, 465.00, 92.70, 707.70, 1),
('FAC-2026-0002', '2026-01-20 12:35:00', 300.00, 1555.00, 278.10, 2133.10, 2),
('FAC-2026-0003', '2026-02-05 14:10:00', 500.00, 955.00, 219.90, 1674.90, 3),
('FAC-2026-0004', '2026-02-10 11:35:00', 100.00, 180.00, 42.00, 322.00, 4),
('FAC-2026-0005', '2026-02-18 09:05:00', 400.00, 2300.00, 402.00, 3102.00, 6),
('FAC-2026-0006', '2026-02-26 16:10:00', 200.00, 380.00, 87.00, 667.00, 9);

-- ============================================================
-- 8. VERIFICACIÓN
-- ============================================================
SELECT 'Usuarios' as tabla, COUNT(*) as total FROM usuarios UNION ALL
SELECT 'Clientes', COUNT(*) FROM clientes UNION ALL
SELECT 'Vehículos', COUNT(*) FROM vehiculos UNION ALL
SELECT 'Repuestos', COUNT(*) FROM repuestos UNION ALL
SELECT 'Órdenes', COUNT(*) FROM ordenes_servicio UNION ALL
SELECT 'Detalles', COUNT(*) FROM detalles_repuestos_orden UNION ALL
SELECT 'Facturas', COUNT(*) FROM facturas;