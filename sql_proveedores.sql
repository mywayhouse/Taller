CREATE TABLE IF NOT EXISTS `proveedores` (
  `id_proveedor` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `estado_activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'taller_mecanico' AND TABLE_NAME = 'movimientos_repuestos' AND COLUMN_NAME = 'id_proveedor');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `movimientos_repuestos` ADD COLUMN `id_proveedor` INT DEFAULT NULL AFTER `id_usuario`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = 'taller_mecanico' AND CONSTRAINT_NAME = 'movimientos_repuestos_ibfk_3' AND TABLE_NAME = 'movimientos_repuestos');
SET @sql2 = IF(@fk_exists = 0, 'ALTER TABLE `movimientos_repuestos` ADD CONSTRAINT `movimientos_repuestos_ibfk_3` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`)', 'SELECT 1');
PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;
DELIMITER //
DROP PROCEDURE IF EXISTS `sp_listar_proveedores`//
CREATE PROCEDURE `sp_listar_proveedores`()
BEGIN
    SELECT id_proveedor, nombre, contacto, telefono, direccion, estado_activo
    FROM proveedores
    ORDER BY nombre;
END//
DROP PROCEDURE IF EXISTS `sp_obtener_proveedor_por_id`//
CREATE PROCEDURE `sp_obtener_proveedor_por_id`(IN p_id_proveedor INT)
BEGIN
    SELECT * FROM proveedores WHERE id_proveedor = p_id_proveedor;
END//
DROP PROCEDURE IF EXISTS `sp_insertar_proveedor`//
CREATE PROCEDURE `sp_insertar_proveedor`(
    IN p_nombre VARCHAR(100),
    IN p_contacto VARCHAR(100),
    IN p_telefono VARCHAR(20),
    IN p_direccion VARCHAR(255)
)
BEGIN
    INSERT INTO proveedores (nombre, contacto, telefono, direccion, estado_activo)
    VALUES (p_nombre, p_contacto, p_telefono, p_direccion, 1);
    SELECT LAST_INSERT_ID() AS id_proveedor;
END//
DROP PROCEDURE IF EXISTS `sp_actualizar_proveedor`//
CREATE PROCEDURE `sp_actualizar_proveedor`(
    IN p_id_proveedor INT,
    IN p_nombre VARCHAR(100),
    IN p_contacto VARCHAR(100),
    IN p_telefono VARCHAR(20),
    IN p_direccion VARCHAR(255)
)
BEGIN
    UPDATE proveedores
    SET nombre = p_nombre, contacto = p_contacto, telefono = p_telefono, direccion = p_direccion
    WHERE id_proveedor = p_id_proveedor;
END//
DROP PROCEDURE IF EXISTS `sp_eliminar_proveedor`//
CREATE PROCEDURE `sp_eliminar_proveedor`(IN p_id_proveedor INT)
BEGIN
    DELETE FROM proveedores WHERE id_proveedor = p_id_proveedor;
END//
DROP PROCEDURE IF EXISTS `sp_registrar_entrada_repuesto`//
CREATE PROCEDURE `sp_registrar_entrada_repuesto`(
    IN p_id_repuesto INT,
    IN p_cantidad INT,
    IN p_id_proveedor INT,
    IN p_id_usuario INT,
    IN p_ip_direccion VARCHAR(45),
    IN p_observacion VARCHAR(255)
)
BEGIN
    DECLARE v_stock_actual INT;
    DECLARE v_nuevo_stock INT;
    SELECT stock_actual INTO v_stock_actual FROM repuestos WHERE id_repuesto = p_id_repuesto FOR UPDATE;
    SET v_nuevo_stock = v_stock_actual + p_cantidad;
    UPDATE repuestos SET stock_actual = v_nuevo_stock WHERE id_repuesto = p_id_repuesto;
    INSERT INTO movimientos_repuestos (id_repuesto, tipo, cantidad, stock_anterior, stock_nuevo, id_usuario, id_proveedor, ip_direccion, observacion)
    VALUES (p_id_repuesto, 'ENTRADA', p_cantidad, v_stock_actual, v_nuevo_stock, p_id_usuario, p_id_proveedor, p_ip_direccion, p_observacion);
END//
DROP PROCEDURE IF EXISTS `sp_listar_movimientos_repuesto`//
CREATE PROCEDURE `sp_listar_movimientos_repuesto`(IN p_id_repuesto INT)
BEGIN
    SELECT m.id_movimiento, m.tipo, m.cantidad, m.stock_anterior, m.stock_nuevo,
           u.nombre AS usuario_nombre, m.fecha_hora, m.observacion,
           p.nombre AS proveedor_nombre
    FROM movimientos_repuestos m
    INNER JOIN usuarios u ON u.id_usuario = m.id_usuario
    LEFT JOIN proveedores p ON p.id_proveedor = m.id_proveedor
    WHERE m.id_repuesto = p_id_repuesto
    ORDER BY m.fecha_hora DESC, m.id_movimiento DESC;
END//
DELIMITER ;
