-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.0.30 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para taller_mecanico
CREATE DATABASE IF NOT EXISTS `taller_mecanico` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `taller_mecanico`;

-- Volcando estructura para tabla taller_mecanico.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `rnt_dni` varchar(20) NOT NULL,
  `estado_activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `rnt_dni` (`rnt_dni`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla taller_mecanico.clientes: ~1 rows (aproximadamente)
INSERT INTO `clientes` (`id_cliente`, `nombre`, `telefono`, `rnt_dni`, `estado_activo`) VALUES
	(1, 'Juan Pérez', '9999-1234', '0801-1990-12345', 1);

-- Volcando estructura para tabla taller_mecanico.detalles_repuestos_orden
CREATE TABLE IF NOT EXISTS `detalles_repuestos_orden` (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `id_orden` int NOT NULL,
  `id_repuesto` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario_historico` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `id_orden` (`id_orden`),
  KEY `id_repuesto` (`id_repuesto`),
  CONSTRAINT `detalles_repuestos_orden_ibfk_1` FOREIGN KEY (`id_orden`) REFERENCES `ordenes_servicio` (`id_orden`) ON DELETE CASCADE,
  CONSTRAINT `detalles_repuestos_orden_ibfk_2` FOREIGN KEY (`id_repuesto`) REFERENCES `repuestos` (`id_repuesto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla taller_mecanico.detalles_repuestos_orden: ~0 rows (aproximadamente)

-- Volcando estructura para tabla taller_mecanico.facturas
CREATE TABLE IF NOT EXISTS `facturas` (
  `id_factura` int NOT NULL AUTO_INCREMENT,
  `numero_factura` varchar(50) NOT NULL,
  `fecha_emision` datetime NOT NULL,
  `subtotal_mano_obra` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal_repuestos` decimal(10,2) NOT NULL DEFAULT '0.00',
  `isv` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_pagar` decimal(10,2) NOT NULL DEFAULT '0.00',
  `id_orden` int NOT NULL,
  PRIMARY KEY (`id_factura`),
  UNIQUE KEY `numero_factura` (`numero_factura`),
  KEY `id_orden` (`id_orden`),
  CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`id_orden`) REFERENCES `ordenes_servicio` (`id_orden`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla taller_mecanico.facturas: ~0 rows (aproximadamente)

-- Volcando estructura para tabla taller_mecanico.idiomas
CREATE TABLE IF NOT EXISTS `idiomas` (
  `id_idioma` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(5) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `defecto` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_idioma`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla taller_mecanico.idiomas: ~2 rows (aproximadamente)
INSERT INTO `idiomas` (`id_idioma`, `codigo`, `nombre`, `defecto`) VALUES
  (1, 'es', 'Español', 1),
  (2, 'en', 'English', 0);

-- Volcando estructura para tabla taller_mecanico.traducciones
CREATE TABLE IF NOT EXISTS `traducciones` (
  `id_traduccion` int NOT NULL AUTO_INCREMENT,
  `clave_etiqueta` varchar(100) NOT NULL,
  `id_idioma` int NOT NULL,
  `texto` text,
  PRIMARY KEY (`id_traduccion`),
  UNIQUE KEY `uk_clave_idioma` (`clave_etiqueta`, `id_idioma`),
  KEY `id_idioma` (`id_idioma`),
  CONSTRAINT `traducciones_ibfk_1` FOREIGN KEY (`id_idioma`) REFERENCES `idiomas` (`id_idioma`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla taller_mecanico.traducciones: ~6 rows (aproximadamente)
INSERT INTO `traducciones` (`clave_etiqueta`, `id_idioma`, `texto`) VALUES
	('welcome_msg', 1, 'Bienvenido al Sistema del Taller'),
	('error_login', 1, 'Usuario o contraseña incorrectos'),
	('status_received', 1, 'Recibido'),
	('welcome_msg', 2, 'Welcome to the Workshop System'),
	('error_login', 2, 'Incorrect user or password'),
	('status_received', 2, 'Received');

-- Volcando estructura para tabla taller_mecanico.logs_sistema
CREATE TABLE IF NOT EXISTS `logs_sistema` (
  `id_log` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `accion` varchar(255) NOT NULL,
  `fecha_hora` datetime DEFAULT CURRENT_TIMESTAMP,
  `ip_direccion` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_log`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `logs_sistema_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla taller_mecanico.logs_sistema: ~1 rows (aproximadamente)
INSERT INTO `logs_sistema` (`id_log`, `id_usuario`, `accion`, `fecha_hora`, `ip_direccion`) VALUES
	(1, 2, 'Usuario probando procedimiento', '2026-06-30 10:32:24', '127.0.0.1');

-- Volcando estructura para tabla taller_mecanico.ordenes_servicio
CREATE TABLE IF NOT EXISTS `ordenes_servicio` (
  `id_orden` int NOT NULL AUTO_INCREMENT,
  `diagnostico_preliminar` text,
  `estado` enum('RECIBIDO','EN PROCESO','LISTO','ENTREGADO') NOT NULL DEFAULT 'RECIBIDO',
  `fecha_ingreso` datetime NOT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `id_recepcionista` int NOT NULL,
  `id_mecanico` int NOT NULL,
  `placa_vehiculo` varchar(15) NOT NULL,
  PRIMARY KEY (`id_orden`),
  KEY `id_recepcionista` (`id_recepcionista`),
  KEY `id_mecanico` (`id_mecanico`),
  KEY `placa_vehiculo` (`placa_vehiculo`),
  CONSTRAINT `ordenes_servicio_ibfk_1` FOREIGN KEY (`id_recepcionista`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `ordenes_servicio_ibfk_2` FOREIGN KEY (`id_mecanico`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `ordenes_servicio_ibfk_3` FOREIGN KEY (`placa_vehiculo`) REFERENCES `vehiculos` (`placa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla taller_mecanico.ordenes_servicio: ~0 rows (aproximadamente)

-- Volcando estructura para tabla taller_mecanico.repuestos
CREATE TABLE IF NOT EXISTS `repuestos` (
  `id_repuesto` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `stock_actual` int NOT NULL DEFAULT '0',
  `stock_minimo` int NOT NULL DEFAULT '0',
  `unidad_medida` varchar(20) DEFAULT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `estado_activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_repuesto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla taller_mecanico.repuestos: ~0 rows (aproximadamente)

-- Volcando estructura para procedimiento taller_mecanico.sp_registrar_log
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

-- Volcando estructura para tabla taller_mecanico.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasenia` varchar(255) NOT NULL,
  `rol` enum('ADMINISTRADOR','RECEPCIONISTA','MECANICO') NOT NULL,
  `estado_activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*
-- Volcando datos para la tabla taller_mecanico.usuarios: ~6 rows (aproximadamente)
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `contrasenia`, `rol`, `estado_activo`) VALUES
	(1, 'Carlos Admin', 'admin@taller.com', '$2y$12$CSMAZmb1R/30O76zjioZB.SHkTtr3zn1hqjEcd6Qk3eLWhGFvChFq', 'ADMINISTRADOR', 0),
	(2, 'Ana Recepcion', 'ana@taller.com', '$2y$12$4hkQuFKQoTUbQvFVwEmmTelqGZCQvL0jEVhOLcs6IkWwmIIOT9NS6', 'RECEPCIONISTA', 1),
	(3, 'Juan Mecanico', 'juan@taller.com', '$2y$12$FcI4yBzX.L9/2rNJognLaeRUXimlFMv1qc4b5W/UGWPQ0unlEFftG', 'MECANICO', 1),
	(4, 'Erwin Montoya', 'admin@taller2.com', '$2y$12$r7PfNFKmQLPJN1pBk2gzDOq1Hd3TwLfvCv.bc4xjeIhfgq11.ZVYG', 'ADMINISTRADOR', 1),
	(5, 'Rebeca Flores', 'rebeca.recepcion@taller.com', '$2y$12$Mcnq8tc2moQ/BcCZYTplMeoGp/TfgWaSdaIvdPdW8pxcB7IDU5YLy', 'RECEPCIONISTA', 1),
	(6, 'Moisés Alvarenga', 'moises.mecanico@taller.com', '$2y$12$dPtfPl2RoyMWfoteaQfMRe5BcQk/gkCfNnsACYVUxANPjyxBNw0kG', 'MECANICO', 1);
*/
-- Volcando estructura para tabla taller_mecanico.vehiculos
CREATE TABLE IF NOT EXISTS `vehiculos` (
  `placa` varchar(15) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `modelo` varchar(50) NOT NULL,
  `anio` int NOT NULL,
  `tipo` varchar(30) NOT NULL,
  `id_cliente` int NOT NULL,
  PRIMARY KEY (`placa`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `vehiculos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla taller_mecanico.vehiculos: ~0 rows (aproximadamente)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
ALTER TABLE facturas ADD COLUMN metodo_pago VARCHAR(50) DEFAULT NULL;
ALTER TABLE facturas ADD COLUMN estado_activo TINYINT DEFAULT 1;