-- ============================================================================
-- Script de Creación/Actualización de Base de Datos - ChileChocados
-- ============================================================================
-- Base de datos: chilechocados
-- Motor: MySQL 8.x
-- Charset: utf8mb4_unicode_ci
-- Fecha: 27 de Octubre 2025
-- ============================================================================
-- NOTA: Este script puede ejecutarse múltiples veces de forma segura
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- TABLA: usuarios
-- ============================================================================
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `apellido` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `telefono` VARCHAR(20) DEFAULT NULL,
  `rut` VARCHAR(12) DEFAULT NULL,
  `rol` ENUM('admin', 'vendedor', 'comprador') NOT NULL DEFAULT 'comprador',
  `estado` ENUM('activo', 'suspendido', 'eliminado') NOT NULL DEFAULT 'activo',
  `verificado` TINYINT(1) NOT NULL DEFAULT 0,
  `foto_perfil` VARCHAR(255) DEFAULT NULL,
  `redes_sociales` JSON DEFAULT NULL,
  `token_recuperacion` VARCHAR(100) DEFAULT NULL,
  `token_expira` DATETIME DEFAULT NULL,
  `ultima_conexion` DATETIME DEFAULT NULL,
  `fecha_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_rol` (`rol`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: categorias_padre
-- ============================================================================
CREATE TABLE IF NOT EXISTS `categorias_padre` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `icono` VARCHAR(50) DEFAULT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `orden` INT NOT NULL DEFAULT 0,
  `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: subcategorias
-- ============================================================================
CREATE TABLE IF NOT EXISTS `subcategorias` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `categoria_padre_id` INT UNSIGNED NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `orden` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categoria_slug` (`categoria_padre_id`, `slug`),
  KEY `idx_categoria` (`categoria_padre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar constraint si no existe
SET @constraint_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
  WHERE CONSTRAINT_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'subcategorias' 
  AND CONSTRAINT_NAME = 'fk_subcat_categoria'
);

SET @sql = IF(@constraint_exists = 0,
  'ALTER TABLE `subcategorias` ADD CONSTRAINT `fk_subcat_categoria` FOREIGN KEY (`categoria_padre_id`) REFERENCES `categorias_padre` (`id`) ON DELETE CASCADE',
  'SELECT "Constraint fk_subcat_categoria already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- TABLA: regiones
-- ============================================================================
CREATE TABLE IF NOT EXISTS `regiones` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `codigo` VARCHAR(10) NOT NULL,
  `orden` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: comunas
-- ============================================================================
CREATE TABLE IF NOT EXISTS `comunas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `region_id` INT UNSIGNED NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_region` (`region_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar constraint si no existe
SET @constraint_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
  WHERE CONSTRAINT_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'comunas' 
  AND CONSTRAINT_NAME = 'fk_comuna_region'
);

SET @sql = IF(@constraint_exists = 0,
  'ALTER TABLE `comunas` ADD CONSTRAINT `fk_comuna_region` FOREIGN KEY (`region_id`) REFERENCES `regiones` (`id`) ON DELETE CASCADE',
  'SELECT "Constraint fk_comuna_region already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- TABLA: publicaciones
-- ============================================================================
CREATE TABLE IF NOT EXISTS `publicaciones` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` INT UNSIGNED NOT NULL,
  `categoria_padre_id` INT UNSIGNED NOT NULL,
  `subcategoria_id` INT UNSIGNED DEFAULT NULL,
  `titulo` VARCHAR(200) NOT NULL,
  `tipificacion` VARCHAR(50) DEFAULT NULL COMMENT 'chocado o mecanico',
  `marca` VARCHAR(100) DEFAULT NULL,
  `modelo` VARCHAR(100) DEFAULT NULL,
  `anio` YEAR DEFAULT NULL,
  `descripcion` TEXT NOT NULL,
  `tipo_venta` ENUM('completo', 'desarme') NOT NULL DEFAULT 'completo',
  `precio` DECIMAL(12,2) DEFAULT NULL,
  `region_id` INT UNSIGNED NOT NULL,
  `comuna_id` INT UNSIGNED DEFAULT NULL,
  `estado` ENUM('borrador', 'pendiente', 'aprobada', 'rechazada', 'vendida', 'archivada') NOT NULL DEFAULT 'pendiente',
  `es_destacada` TINYINT(1) NOT NULL DEFAULT 0,
  `fecha_destacada_inicio` DATETIME DEFAULT NULL,
  `fecha_destacada_fin` DATETIME DEFAULT NULL,
  `visitas` INT UNSIGNED NOT NULL DEFAULT 0,
  `foto_principal` VARCHAR(255) DEFAULT NULL,
  `motivo_rechazo` TEXT DEFAULT NULL,
  `fecha_publicacion` DATETIME DEFAULT NULL,
  `fecha_venta` DATETIME DEFAULT NULL,
  `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_categoria` (`categoria_padre_id`),
  KEY `idx_subcategoria` (`subcategoria_id`),
  KEY `idx_region` (`region_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_destacada` (`es_destacada`),
  KEY `idx_fecha_pub` (`fecha_publicacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar constraints de publicaciones
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'publicaciones' AND CONSTRAINT_NAME = 'fk_pub_usuario');
SET @sql = IF(@constraint_exists = 0, 'ALTER TABLE `publicaciones` ADD CONSTRAINT `fk_pub_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE', 'SELECT "Constraint fk_pub_usuario exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'publicaciones' AND CONSTRAINT_NAME = 'fk_pub_categoria');
SET @sql = IF(@constraint_exists = 0, 'ALTER TABLE `publicaciones` ADD CONSTRAINT `fk_pub_categoria` FOREIGN KEY (`categoria_padre_id`) REFERENCES `categorias_padre` (`id`)', 'SELECT "Constraint fk_pub_categoria exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'publicaciones' AND CONSTRAINT_NAME = 'fk_pub_subcategoria');
SET @sql = IF(@constraint_exists = 0, 'ALTER TABLE `publicaciones` ADD CONSTRAINT `fk_pub_subcategoria` FOREIGN KEY (`subcategoria_id`) REFERENCES `subcategorias` (`id`)', 'SELECT "Constraint fk_pub_subcategoria exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'publicaciones' AND CONSTRAINT_NAME = 'fk_pub_region');
SET @sql = IF(@constraint_exists = 0, 'ALTER TABLE `publicaciones` ADD CONSTRAINT `fk_pub_region` FOREIGN KEY (`region_id`) REFERENCES `regiones` (`id`)', 'SELECT "Constraint fk_pub_region exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ============================================================================
-- TABLA: publicacion_fotos
-- ============================================================================
CREATE TABLE IF NOT EXISTS `publicacion_fotos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `publicacion_id` INT UNSIGNED NOT NULL,
  `ruta` VARCHAR(255) NOT NULL,
  `es_principal` TINYINT(1) NOT NULL DEFAULT 0,
  `orden` INT NOT NULL DEFAULT 0,
  `fecha_subida` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_publicacion` (`publicacion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'publicacion_fotos' AND CONSTRAINT_NAME = 'fk_foto_publicacion');
SET @sql = IF(@constraint_exists = 0, 'ALTER TABLE `publicacion_fotos` ADD CONSTRAINT `fk_foto_publicacion` FOREIGN KEY (`publicacion_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE', 'SELECT "Constraint fk_foto_publicacion exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ============================================================================
-- TABLA: mensajes
-- ============================================================================
CREATE TABLE IF NOT EXISTS `mensajes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `publicacion_id` INT UNSIGNED NOT NULL,
  `remitente_id` INT UNSIGNED NOT NULL,
  `destinatario_id` INT UNSIGNED NOT NULL,
  `mensaje` TEXT NOT NULL,
  `archivo_adjunto` VARCHAR(255) DEFAULT NULL,
  `leido` TINYINT(1) NOT NULL DEFAULT 0,
  `fecha_lectura` DATETIME DEFAULT NULL,
  `fecha_envio` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_publicacion` (`publicacion_id`),
  KEY `idx_remitente` (`remitente_id`),
  KEY `idx_destinatario` (`destinatario_id`),
  KEY `idx_leido` (`leido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'mensajes' AND CONSTRAINT_NAME = 'fk_mensaje_publicacion');
SET @sql = IF(@constraint_exists = 0, 'ALTER TABLE `mensajes` ADD CONSTRAINT `fk_mensaje_publicacion` FOREIGN KEY (`publicacion_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE', 'SELECT "Constraint exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'mensajes' AND CONSTRAINT_NAME = 'fk_mensaje_remitente');
SET @sql = IF(@constraint_exists = 0, 'ALTER TABLE `mensajes` ADD CONSTRAINT `fk_mensaje_remitente` FOREIGN KEY (`remitente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE', 'SELECT "Constraint exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'mensajes' AND CONSTRAINT_NAME = 'fk_mensaje_destinatario');
SET @sql = IF(@constraint_exists = 0, 'ALTER TABLE `mensajes` ADD CONSTRAINT `fk_mensaje_destinatario` FOREIGN KEY (`destinatario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE', 'SELECT "Constraint exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ============================================================================
-- TABLA: favoritos
-- ============================================================================
CREATE TABLE IF NOT EXISTS `favoritos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` INT UNSIGNED NOT NULL,
  `publicacion_id` INT UNSIGNED NOT NULL,
  `fecha_agregado` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_publicacion` (`usuario_id`, `publicacion_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_publicacion` (`publicacion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'favoritos' AND CONSTRAINT_NAME = 'fk_fav_usuario');
SET @sql = IF(@constraint_exists = 0, 'ALTER TABLE `favoritos` ADD CONSTRAINT `fk_fav_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE', 'SELECT "Constraint exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'favoritos' AND CONSTRAINT_NAME = 'fk_fav_publicacion');
SET @sql = IF(@constraint_exists = 0, 'ALTER TABLE `favoritos` ADD CONSTRAINT `fk_fav_publicacion` FOREIGN KEY (`publicacion_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE', 'SELECT "Constraint exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ============================================================================
-- TABLA: pagos_flow
-- ============================================================================
CREATE TABLE IF NOT EXISTS `pagos_flow` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `publicacion_id` INT UNSIGNED NOT NULL,
  `usuario_id` INT UNSIGNED NOT NULL,
  `tipo` ENUM('destacado_15', 'destacado_30', 'banner') NOT NULL,
  `monto` DECIMAL(10,2) NOT NULL,
  `flow_token` VARCHAR(255) DEFAULT NULL,
  `flow_orden` VARCHAR(100) DEFAULT NULL,
  `estado` ENUM('pendiente', 'aprobado', 'rechazado', 'expirado') NOT NULL DEFAULT 'pendiente',
  `respuesta_flow` JSON DEFAULT NULL,
  `fecha_pago` DATETIME DEFAULT NULL,
  `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `flow_orden` (`flow_orden`),
  KEY `idx_publicacion` (`publicacion_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'pagos_flow' AND CONSTRAINT_NAME = 'fk_pago_publicacion');
SET @sql = IF(@constraint_exists = 0, 'ALTER TABLE `pagos_flow` ADD CONSTRAINT `fk_pago_publicacion` FOREIGN KEY (`publicacion_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE', 'SELECT "Constraint exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'pagos_flow' AND CONSTRAINT_NAME = 'fk_pago_usuario');
SET @sql = IF(@constraint_exists = 0, 'ALTER TABLE `pagos_flow` ADD CONSTRAINT `fk_pago_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE', 'SELECT "Constraint exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ============================================================================
-- TABLA: auditoria
-- ============================================================================
CREATE TABLE IF NOT EXISTS `auditoria` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` INT UNSIGNED DEFAULT NULL,
  `tabla` VARCHAR(50) NOT NULL,
  `registro_id` INT UNSIGNED NOT NULL,
  `accion` ENUM('crear', 'actualizar', 'eliminar') NOT NULL,
  `datos_anteriores` JSON DEFAULT NULL,
  `datos_nuevos` JSON DEFAULT NULL,
  `ip` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_tabla_registro` (`tabla`, `registro_id`),
  KEY `idx_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: configuraciones
-- ============================================================================
CREATE TABLE IF NOT EXISTS `configuraciones` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `clave` VARCHAR(100) NOT NULL,
  `valor` TEXT NOT NULL,
  `tipo` ENUM('string', 'int', 'float', 'boolean', 'json') NOT NULL DEFAULT 'string',
  `descripcion` TEXT DEFAULT NULL,
  `fecha_actualizacion` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DATOS INICIALES: configuraciones
-- ============================================================================
INSERT IGNORE INTO `configuraciones` (`clave`, `valor`, `tipo`, `descripcion`) VALUES
('site_name', 'ChileChocados', 'string', 'Nombre del sitio'),
('items_per_page', '20', 'int', 'Cantidad de items por página'),
('destacado_15_precio', '15000', 'int', 'Precio destacado 15 días'),
('destacado_30_precio', '25000', 'int', 'Precio destacado 30 días'),
('max_fotos_publicacion', '6', 'int', 'Máximo de fotos por publicación'),
('email_admin', 'admin@chilechocados.cl', 'string', 'Email del administrador');

-- ============================================================================
-- DATOS INICIALES: categorias_padre
-- ============================================================================
INSERT IGNORE INTO `categorias_padre` (`nombre`, `slug`, `icono`, `descripcion`, `orden`) VALUES
('Auto', 'auto', 'car', 'Automóviles y vehículos livianos', 1),
('Moto', 'moto', 'bike', 'Motocicletas y motonetas', 2),
('Camión', 'camion', 'truck', 'Camiones y vehículos de carga', 3),
('Casa Rodante', 'casa-rodante', 'rv', 'Casas rodantes y motorhomes', 4),
('Náutica', 'nautica', 'boat', 'Embarcaciones y vehículos náuticos', 5),
('Bus', 'bus', 'bus', 'Buses y microbuses', 6),
('Maquinaria', 'maquinaria', 'gear', 'Maquinaria pesada y equipos', 7),
('Aéreos', 'aereos', 'plane', 'Vehículos aéreos', 8);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- Script completado exitosamente
-- ============================================================================
