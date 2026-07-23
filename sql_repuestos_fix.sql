CREATE TABLE IF NOT EXISTS `movimientos_repuestos` (
  `id_movimiento` int NOT NULL AUTO_INCREMENT,
  `id_repuesto` int NOT NULL,
  `tipo` enum('ENTRADA','SALIDA','AJUSTE') NOT NULL,
  `cantidad` int NOT NULL,
  `stock_anterior` int NOT NULL,
  `stock_nuevo` int NOT NULL,
  `id_usuario` int NOT NULL,
  `ip_direccion` varchar(45) DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_movimiento`),
  KEY `id_repuesto` (`id_repuesto`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `movimientos_repuestos_ibfk_1` FOREIGN KEY (`id_repuesto`) REFERENCES `repuestos` (`id_repuesto`) ON DELETE CASCADE,
  CONSTRAINT `movimientos_repuestos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
DELIMITER //
DROP PROCEDURE IF EXISTS `sp_eliminar_repuesto`//
CREATE PROCEDURE `sp_eliminar_repuesto`(IN p_id_repuesto INT)
BEGIN
    DELETE FROM repuestos WHERE id_repuesto = p_id_repuesto;
END//
DROP PROCEDURE IF EXISTS `sp_buscar_repuestos_todos`//
CREATE PROCEDURE `sp_buscar_repuestos_todos`(IN p_termino VARCHAR(100), IN p_stock_bajo INT, IN p_estado INT)
BEGIN
    SELECT id_repuesto, nombre, stock_actual, stock_minimo, unidad_medida, precio_venta, estado_activo
    FROM repuestos
    WHERE (p_termino = '' OR nombre LIKE CONCAT('%', p_termino, '%'))
      AND (p_stock_bajo = 0 OR stock_actual <= stock_minimo)
      AND (p_estado = -1 OR estado_activo = p_estado)
    ORDER BY nombre;
END//
DROP PROCEDURE IF EXISTS `sp_ajustar_stock_repuesto`//
CREATE PROCEDURE `sp_ajustar_stock_repuesto`(IN p_id_repuesto INT, IN p_nuevo_stock INT, IN p_id_usuario INT, IN p_ip_direccion VARCHAR(45), IN p_observacion VARCHAR(255))
BEGIN
    DECLARE v_stock_actual INT;
    DECLARE v_tipo VARCHAR(10);
    DECLARE v_cantidad INT;
    SELECT stock_actual INTO v_stock_actual FROM repuestos WHERE id_repuesto = p_id_repuesto FOR UPDATE;
    SET v_cantidad = ABS(p_nuevo_stock - v_stock_actual);
    IF p_nuevo_stock > v_stock_actual THEN
        SET v_tipo = 'ENTRADA';
    ELSEIF p_nuevo_stock < v_stock_actual THEN
        SET v_tipo = 'SALIDA';
    ELSE
        SET v_tipo = 'AJUSTE';
    END IF;
    UPDATE repuestos SET stock_actual = p_nuevo_stock WHERE id_repuesto = p_id_repuesto;
    INSERT INTO movimientos_repuestos (id_repuesto, tipo, cantidad, stock_anterior, stock_nuevo, id_usuario, ip_direccion, observacion)
    VALUES (p_id_repuesto, v_tipo, v_cantidad, v_stock_actual, p_nuevo_stock, p_id_usuario, p_ip_direccion, p_observacion);
END//
DROP PROCEDURE IF EXISTS `sp_listar_movimientos_repuesto`//
CREATE PROCEDURE `sp_listar_movimientos_repuesto`(IN p_id_repuesto INT)
BEGIN
    SELECT m.id_movimiento, m.tipo, m.cantidad, m.stock_anterior, m.stock_nuevo,
           u.nombre AS usuario_nombre, m.fecha_hora, m.observacion
    FROM movimientos_repuestos m
    INNER JOIN usuarios u ON u.id_usuario = m.id_usuario
    WHERE m.id_repuesto = p_id_repuesto
    ORDER BY m.fecha_hora DESC, m.id_movimiento DESC;
END//
DELIMITER ;
