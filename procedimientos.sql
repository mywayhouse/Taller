-- ============================================================
-- stored_procedures.sql
-- Complemento de Base_taller.sql
-- Stored Procedures necesarios para el sistema MVC
-- ============================================================
-- Ejecutar DESPUÉS de haber importado Base_taller.sql.
-- ============================================================

USE `taller_mecanico`;

-- ============================================================
-- CLIENTES
-- ============================================================

-- Listar todos los clientes activos
DROP PROCEDURE IF EXISTS `sp_listar_clientes`;
DELIMITER //
CREATE PROCEDURE `sp_listar_clientes`()
BEGIN
    SELECT id_cliente, nombre, telefono, rnt_dni, estado_activo
    FROM clientes
    ORDER BY nombre ASC;
END//
DELIMITER ;

-- Obtener un cliente por su ID
DROP PROCEDURE IF EXISTS `sp_obtener_cliente_por_id`;
DELIMITER //
CREATE PROCEDURE `sp_obtener_cliente_por_id`(
    IN p_id_cliente INT
)
BEGIN
    SELECT id_cliente, nombre, telefono, rnt_dni, estado_activo
    FROM clientes
    WHERE id_cliente = p_id_cliente;
END//
DELIMITER ;

-- Insertar un nuevo cliente
DROP PROCEDURE IF EXISTS `sp_insertar_cliente`;
DELIMITER //
CREATE PROCEDURE `sp_insertar_cliente`(
    IN p_nombre   VARCHAR(100),
    IN p_telefono VARCHAR(20),
    IN p_rnt_dni  VARCHAR(20)
)
BEGIN
    INSERT INTO clientes (nombre, telefono, rnt_dni, estado_activo)
    VALUES (p_nombre, p_telefono, p_rnt_dni, 1);

    SELECT LAST_INSERT_ID() AS id_cliente;
END//
DELIMITER ;

-- Actualizar datos de un cliente
DROP PROCEDURE IF EXISTS `sp_actualizar_cliente`;
DELIMITER //
CREATE PROCEDURE `sp_actualizar_cliente`(
    IN p_id_cliente INT,
    IN p_nombre     VARCHAR(100),
    IN p_telefono   VARCHAR(20),
    IN p_rnt_dni    VARCHAR(20)
)
BEGIN
    UPDATE clientes
    SET nombre   = p_nombre,
        telefono = p_telefono,
        rnt_dni  = p_rnt_dni
    WHERE id_cliente = p_id_cliente;

    SELECT ROW_COUNT() AS filas_afectadas;
END//
DELIMITER ;

-- Eliminar (desactivar lógicamente) un cliente
DROP PROCEDURE IF EXISTS `sp_eliminar_cliente`;
DELIMITER //
CREATE PROCEDURE `sp_eliminar_cliente`(
    IN p_id_cliente INT
)
BEGIN
    UPDATE clientes
    SET estado_activo = 0
    WHERE id_cliente = p_id_cliente;

    SELECT ROW_COUNT() AS filas_afectadas;
END//
DELIMITER ;

-- ============================================================
-- USUARIOS
-- ============================================================

-- Obtener usuario por correo (para login)
DROP PROCEDURE IF EXISTS `sp_obtener_usuario_por_correo`;
DELIMITER //
CREATE PROCEDURE `sp_obtener_usuario_por_correo`(
    IN p_correo VARCHAR(100)
)
BEGIN
    SELECT id_usuario, nombre, correo, contrasenia, rol, estado_activo
    FROM usuarios
    WHERE correo = p_correo;
END//
DELIMITER ;

-- Listar todos los usuarios activos
DROP PROCEDURE IF EXISTS `sp_listar_usuarios`;
DELIMITER //
CREATE PROCEDURE `sp_listar_usuarios`()
BEGIN
    SELECT id_usuario, nombre, correo, rol, estado_activo
    FROM usuarios
    ORDER BY nombre ASC;
END//
DELIMITER ;

-- Insertar un nuevo usuario
DROP PROCEDURE IF EXISTS `sp_insertar_usuario`;
DELIMITER //
CREATE PROCEDURE `sp_insertar_usuario`(
    IN p_nombre       VARCHAR(100),
    IN p_correo       VARCHAR(100),
    IN p_contrasenia  VARCHAR(255),
    IN p_rol          ENUM('ADMINISTRADOR','RECEPCIONISTA','MECANICO')
)
BEGIN
    INSERT INTO usuarios (nombre, correo, contrasenia, rol, estado_activo)
    VALUES (p_nombre, p_correo, p_contrasenia, p_rol, 1);

    SELECT LAST_INSERT_ID() AS id_usuario;
END//
DELIMITER ;

-- ============================================================
-- VEHÍCULOS
-- ============================================================

-- Listar todos los vehículos (con nombre del cliente)
DROP PROCEDURE IF EXISTS `sp_listar_vehiculos`;
DELIMITER //
CREATE PROCEDURE `sp_listar_vehiculos`()
BEGIN
    SELECT v.placa, v.marca, v.modelo, v.anio, v.tipo,
           v.id_cliente, c.nombre AS nombre_cliente
    FROM vehiculos v
    INNER JOIN clientes c ON c.id_cliente = v.id_cliente
    ORDER BY v.placa ASC;
END//
DELIMITER ;

-- Obtener vehículo por placa
DROP PROCEDURE IF EXISTS `sp_obtener_vehiculo_por_placa`;
DELIMITER //
CREATE PROCEDURE `sp_obtener_vehiculo_por_placa`(
    IN p_placa VARCHAR(15)
)
BEGIN
    SELECT v.*, c.nombre AS nombre_cliente
    FROM vehiculos v
    INNER JOIN clientes c ON c.id_cliente = v.id_cliente
    WHERE v.placa = p_placa;
END//
DELIMITER ;

-- Insertar un nuevo vehículo
DROP PROCEDURE IF EXISTS `sp_insertar_vehiculo`;
DELIMITER //
CREATE PROCEDURE `sp_insertar_vehiculo`(
    IN p_placa       VARCHAR(15),
    IN p_marca       VARCHAR(50),
    IN p_modelo      VARCHAR(50),
    IN p_anio        INT,
    IN p_tipo        VARCHAR(30),
    IN p_id_cliente  INT
)
BEGIN
    INSERT INTO vehiculos (placa, marca, modelo, anio, tipo, id_cliente)
    VALUES (p_placa, p_marca, p_modelo, p_anio, p_tipo, p_id_cliente);
END//
DELIMITER ;

-- ============================================================
-- ÓRDENES DE SERVICIO
-- ============================================================

-- Listar órdenes con datos relacionados
DROP PROCEDURE IF EXISTS `sp_listar_ordenes`;
DELIMITER //
CREATE PROCEDURE `sp_listar_ordenes`()
BEGIN
    SELECT o.id_orden, o.diagnostico_preliminar, o.estado,
           o.fecha_ingreso, o.fecha_entrega,
           o.placa_vehiculo, v.marca, v.modelo,
           u_rec.nombre AS recepcionista,
           u_mec.nombre AS mecanico
    FROM ordenes_servicio o
    INNER JOIN vehiculos v ON v.placa = o.placa_vehiculo
    INNER JOIN usuarios u_rec ON u_rec.id_usuario = o.id_recepcionista
    INNER JOIN usuarios u_mec ON u_mec.id_usuario = o.id_mecanico
    ORDER BY o.fecha_ingreso DESC;
END//
DELIMITER ;

-- Obtener orden por ID
DROP PROCEDURE IF EXISTS `sp_obtener_orden_por_id`;
DELIMITER //
CREATE PROCEDURE `sp_obtener_orden_por_id`(
    IN p_id_orden INT
)
BEGIN
    SELECT o.*, v.marca, v.modelo, v.tipo,
           c.id_cliente, c.nombre AS nombre_cliente,
           u_rec.nombre AS recepcionista,
           u_mec.nombre AS mecanico
    FROM ordenes_servicio o
    INNER JOIN vehiculos v ON v.placa = o.placa_vehiculo
    INNER JOIN clientes c ON c.id_cliente = v.id_cliente
    INNER JOIN usuarios u_rec ON u_rec.id_usuario = o.id_recepcionista
    INNER JOIN usuarios u_mec ON u_mec.id_usuario = o.id_mecanico
    WHERE o.id_orden = p_id_orden;
END//
DELIMITER ;

-- Insertar una nueva orden de servicio
DROP PROCEDURE IF EXISTS `sp_insertar_orden`;
DELIMITER //
CREATE PROCEDURE `sp_insertar_orden`(
    IN p_diagnostico_preliminar TEXT,
    IN p_fecha_ingreso          DATETIME,
    IN p_id_recepcionista       INT,
    IN p_id_mecanico            INT,
    IN p_placa_vehiculo         VARCHAR(15)
)
BEGIN
    INSERT INTO ordenes_servicio (
        diagnostico_preliminar, estado, fecha_ingreso,
        id_recepcionista, id_mecanico, placa_vehiculo
    ) VALUES (
        p_diagnostico_preliminar, 'RECIBIDO', p_fecha_ingreso,
        p_id_recepcionista, p_id_mecanico, p_placa_vehiculo
    );

    SELECT LAST_INSERT_ID() AS id_orden;
END//
DELIMITER ;

-- Actualizar estado de una orden
DROP PROCEDURE IF EXISTS `sp_actualizar_estado_orden`;
DELIMITER //
CREATE PROCEDURE `sp_actualizar_estado_orden`(
    IN p_id_orden INT,
    IN p_estado   ENUM('RECIBIDO','EN PROCESO','LISTO','ENTREGADO')
)
BEGIN
    UPDATE ordenes_servicio
    SET estado = p_estado,
        fecha_entrega = IF(p_estado = 'ENTREGADO', NOW(), fecha_entrega)
    WHERE id_orden = p_id_orden;
END//
DELIMITER ;

-- ============================================================
-- REPUESTOS
-- ============================================================

-- Listar todos los repuestos
DROP PROCEDURE IF EXISTS `sp_listar_repuestos`;
DELIMITER //
CREATE PROCEDURE `sp_listar_repuestos`()
BEGIN
    SELECT id_repuesto, nombre, stock_actual, stock_minimo,
           unidad_medida, precio_venta, estado_activo
    FROM repuestos
    ORDER BY nombre ASC;
END//
DELIMITER ;

-- Obtener repuesto por ID
DROP PROCEDURE IF EXISTS `sp_obtener_repuesto_por_id`;
DELIMITER //
CREATE PROCEDURE `sp_obtener_repuesto_por_id`(
    IN p_id_repuesto INT
)
BEGIN
    SELECT * FROM repuestos WHERE id_repuesto = p_id_repuesto;
END//
DELIMITER ;

-- Insertar un nuevo repuesto
DROP PROCEDURE IF EXISTS `sp_insertar_repuesto`;
DELIMITER //
CREATE PROCEDURE `sp_insertar_repuesto`(
    IN p_nombre         VARCHAR(100),
    IN p_stock_actual   INT,
    IN p_stock_minimo   INT,
    IN p_unidad_medida  VARCHAR(20),
    IN p_precio_venta   DECIMAL(10,2)
)
BEGIN
    INSERT INTO repuestos (nombre, stock_actual, stock_minimo, unidad_medida, precio_venta, estado_activo)
    VALUES (p_nombre, p_stock_actual, p_stock_minimo, p_unidad_medida, p_precio_venta, 1);

    SELECT LAST_INSERT_ID() AS id_repuesto;
END//
DELIMITER ;

-- Descontar stock de un repuesto
DROP PROCEDURE IF EXISTS `sp_descontar_stock_repuesto`;
DELIMITER //
CREATE PROCEDURE `sp_descontar_stock_repuesto`(
    IN p_id_repuesto INT,
    IN p_cantidad    INT
)
BEGIN
    UPDATE repuestos
    SET stock_actual = stock_actual - p_cantidad
    WHERE id_repuesto = p_id_repuesto
      AND stock_actual >= p_cantidad;

    SELECT ROW_COUNT() AS filas_afectadas;
END//
DELIMITER ;

-- ============================================================
-- FACTURAS
-- ============================================================

-- Listar todas las facturas
DROP PROCEDURE IF EXISTS `sp_listar_facturas`;
DELIMITER //
CREATE PROCEDURE `sp_listar_facturas`()
BEGIN
    SELECT f.id_factura, f.numero_factura, f.fecha_emision,
           f.total_pagar, o.id_orden, v.placa,
           c.nombre AS nombre_cliente
    FROM facturas f
    INNER JOIN ordenes_servicio o ON o.id_orden = f.id_orden
    INNER JOIN vehiculos v ON v.placa = o.placa_vehiculo
    INNER JOIN clientes c ON c.id_cliente = v.id_cliente
    ORDER BY f.fecha_emision DESC;
END//
DELIMITER ;

-- Insertar una factura
DROP PROCEDURE IF EXISTS `sp_insertar_factura`;
DELIMITER //
CREATE PROCEDURE `sp_insertar_factura`(
    IN p_numero_factura     VARCHAR(50),
    IN p_subtotal_mano_obra DECIMAL(10,2),
    IN p_subtotal_repuestos DECIMAL(10,2),
    IN p_isv                DECIMAL(10,2),
    IN p_total_pagar        DECIMAL(10,2),
    IN p_id_orden           INT
)
BEGIN
    INSERT INTO facturas (
        numero_factura, fecha_emision,
        subtotal_mano_obra, subtotal_repuestos, isv, total_pagar, id_orden
    ) VALUES (
        p_numero_factura, NOW(),
        p_subtotal_mano_obra, p_subtotal_repuestos, p_isv, p_total_pagar, p_id_orden
    );

    SELECT LAST_INSERT_ID() AS id_factura;
END//
DELIMITER ;

-- ============================================================
-- DASHBOARD (Estadísticas)
-- ============================================================

-- Obtener conteos para el panel principal
DROP PROCEDURE IF EXISTS `sp_contar_dashboard`;

DELIMITER //
CREATE PROCEDURE `sp_contar_dashboard`()
BEGIN
    SELECT
        (SELECT COUNT(*) FROM ordenes_servicio WHERE estado != 'ENTREGADO') AS ordenes_pendientes,
        (SELECT COUNT(*) FROM clientes WHERE estado_activo = 1) AS clientes_activos,
        (SELECT COUNT(*) FROM ordenes_servicio WHERE estado IN ('RECIBIDO','EN PROCESO')) AS vehiculos_en_taller,
        (SELECT COUNT(*) FROM repuestos WHERE stock_actual <= stock_minimo AND estado_activo = 1) AS repuestos_stock_bajo,
        (SELECT COALESCE(AVG(TIMESTAMPDIFF(HOUR, fecha_ingreso, fecha_entrega)), 0) 
         FROM ordenes_servicio 
         WHERE estado IN ('ENTREGADO', 'LISTO') AND fecha_entrega IS NOT NULL) AS tiempo_promedio_pedidos;
END//
DELIMITER ;

-- ============================================================
-- IDIOMAS Y TRADUCCIONES
-- ============================================================

-- Listar todos los idiomas disponibles
DROP PROCEDURE IF EXISTS `sp_listar_idiomas`;
DELIMITER //
CREATE PROCEDURE `sp_listar_idiomas`()
BEGIN
    SELECT id_idioma, codigo, nombre, defecto
    FROM idiomas
    ORDER BY defecto DESC, nombre ASC;
END//
DELIMITER ;

-- Obtener todas las traducciones para un idioma dado
DROP PROCEDURE IF EXISTS `sp_obtener_traducciones`;
DELIMITER //
CREATE PROCEDURE `sp_obtener_traducciones`(
    IN p_codigo_idioma VARCHAR(5)
)
BEGIN
    SELECT t.clave_etiqueta, t.texto
    FROM traducciones t
    INNER JOIN idiomas i ON i.id_idioma = t.id_idioma
    WHERE i.codigo = p_codigo_idioma;
END//
DELIMITER ;

-- ============================================================
-- LOGS
-- ============================================================

-- Registrar un evento en la bitácora
DROP PROCEDURE IF EXISTS `sp_registrar_log`;
DELIMITER //
CREATE PROCEDURE `sp_registrar_log`(
    IN p_id_usuario INT,
    IN p_accion VARCHAR(255),
    IN p_ip_direccion VARCHAR(45)
)
BEGIN
    INSERT INTO logs_sistema (id_usuario, accion, fecha_hora, ip_direccion)
    VALUES (p_id_usuario, p_accion, NOW(), p_ip_direccion);
END//
DELIMITER ;

-- Listar todos los logs del sistema con datos del usuario
DROP PROCEDURE IF EXISTS `sp_listar_logs`;
DELIMITER //
CREATE PROCEDURE `sp_listar_logs`()
BEGIN
    SELECT l.id_log, l.accion, l.fecha_hora, l.ip_direccion,
           u.nombre AS usuario_nombre, u.rol AS usuario_rol
    FROM logs_sistema l
    INNER JOIN usuarios u ON u.id_usuario = l.id_usuario
    ORDER BY l.fecha_hora DESC;
END//
DELIMITER ;

DELIMITER //
CREATE PROCEDURE sp_buscar_cliente_por_rtn(IN p_rtn VARCHAR(50))
BEGIN
    SELECT id_cliente, nombre, rnt_dni 
    FROM clientes 
    WHERE rnt_dni = p_rtn;
END //

DELIMITER ;

DROP PROCEDURE IF EXISTS `sp_tiempo_promedio_ordenes`;
DELIMITER //
CREATE PROCEDURE `sp_tiempo_promedio_ordenes`()
BEGIN
    SELECT 
        COALESCE(
            AVG(TIMESTAMPDIFF(HOUR, fecha_ingreso, fecha_entrega)),
            0
        ) AS tiempo_promedio_horas
    FROM ordenes_servicio
    WHERE estado IN ('ENTREGADO', 'LISTO') 
      AND fecha_entrega IS NOT NULL;
END//
DELIMITER ;

DELIMITER //
CREATE PROCEDURE `sp_actualizar_repuesto` (
    IN p_id_repuesto INT,
    IN p_nombre VARCHAR(100),
    IN p_stock_actual INT,
    IN p_stock_minimo INT,
    IN p_unidad_medida VARCHAR(20),
    IN p_precio_venta DECIMAL(10,2)
)
BEGIN
    UPDATE repuestos
    SET nombre = p_nombre,
        stock_actual = p_stock_actual,
        stock_minimo = p_stock_minimo,
        unidad_medida = p_unidad_medida,
        precio_venta = p_precio_venta
    WHERE id_repuesto = p_id_repuesto;

    SELECT ROW_COUNT() AS filas_afectadas;
END//
DELIMITER ;

DELIMITER //
CREATE PROCEDURE `sp_repuestos_mas_vendidos`()
BEGIN
    SELECT 
        r.nombre AS repuesto,
        COALESCE(SUM(dr.cantidad), 0) AS total_vendido
    FROM repuestos r
    LEFT JOIN detalles_repuestos_orden dr ON r.id_repuesto = dr.id_repuesto
    GROUP BY r.id_repuesto, r.nombre
    ORDER BY total_vendido DESC
    LIMIT 5;
END//
DELIMITER ;

DELIMITER //
CREATE PROCEDURE `sp_ingresos_semanales`()
BEGIN
    SELECT 
        DAYNAME(f.fecha_emision) AS dia_semana,
        COALESCE(SUM(f.total_pagar), 0) AS ingresos_totales,
        COALESCE(SUM(f.subtotal_mano_obra), 0) AS ingresos_mano_obra
    FROM facturas f
    GROUP BY WEEKDAY(f.fecha_emision), DAYNAME(f.fecha_emision)
    ORDER BY WEEKDAY(f.fecha_emision) ASC;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS `sp_ordenes_mec_mes`;
DELIMITER //
CREATE PROCEDURE `sp_ordenes_mec_mes`()
BEGIN
    SELECT 
        DATE_FORMAT(o.fecha_ingreso, '%Y-%m') AS mes_anio,
        DATE_FORMAT(o.fecha_ingreso, '%M') AS mes_nombre,
        u.nombre AS mecanico,
        COUNT(o.id_orden) AS total_ordenes
    FROM ordenes_servicio o
    JOIN usuarios u ON o.id_mecanico = u.id_usuario
    WHERE u.rol = 'MECANICO'
    GROUP BY mes_anio, mes_nombre, u.id_usuario, u.nombre
    ORDER BY mes_anio ASC;
END//
DELIMITER ;

DELIMITER //
CREATE PROCEDURE 'sp_modelos_vehiculos_frecuentes'()
BEGIN
    SELECT v.modelo, COUNT(*) AS total_visitas
    FROM vehiculos v
    INNER JOIN ordenes_servicio o ON o.placa_vehiculo = v.placa
    GROUP BY v.modelo
    ORDER BY total_visitas DESC
    LIMIT 5;
END //
DELIMITER ;