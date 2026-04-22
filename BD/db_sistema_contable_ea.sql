-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-04-2026 a las 08:07:35
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_sistema_contable_ea`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asiento_detalle`
--

CREATE TABLE `asiento_detalle` (
  `id_detalle` int(11) NOT NULL,
  `id_asiento` int(11) NOT NULL,
  `id_cuenta` int(11) NOT NULL,
  `debe` decimal(18,2) DEFAULT 0.00,
  `haber` decimal(18,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asiento_diario`
--

CREATE TABLE `asiento_diario` (
  `id_asiento` int(11) NOT NULL,
  `nro_comprobante` varchar(20) NOT NULL,
  `tipo_comprobante` enum('ingreso','egreso','diario') NOT NULL,
  `fecha_asiento` date NOT NULL,
  `glosa` varchar(255) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_factura` int(11) DEFAULT NULL,
  `estado_asiento` enum('borrador','posteado','anulado') DEFAULT 'borrador',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_sistema`
--

CREATE TABLE `auditoria_sistema` (
  `id_auditoria` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tabla_afectada` varchar(50) NOT NULL,
  `id_registro_afectado` int(11) NOT NULL,
  `accion` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `valor_anterior` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`valor_anterior`)),
  `valor_nuevo` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`valor_nuevo`)),
  `ip_maquina` varchar(45) DEFAULT NULL,
  `fecha_accion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalogo_cuentas`
--

CREATE TABLE `catalogo_cuentas` (
  `id_cuenta` int(11) NOT NULL,
  `codigo_cuenta` varchar(20) NOT NULL,
  `nombre_cuenta` varchar(100) NOT NULL,
  `nivel` int(11) NOT NULL,
  `tipo_cuenta` enum('Activo','Pasivo','Patrimonio','Ingreso','Egreso') NOT NULL,
  `cuenta_padre_id` int(11) DEFAULT NULL,
  `permite_movimiento` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_cuentas`
--

CREATE TABLE `configuracion_cuentas` (
  `id_config` int(11) NOT NULL,
  `codigo_proceso` varchar(50) NOT NULL,
  `descripcion_proceso` varchar(100) DEFAULT NULL,
  `id_cuenta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id_empleado` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `nombre_completo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas_clientes`
--

CREATE TABLE `empresas_clientes` (
  `id_empresa` int(11) NOT NULL,
  `nombre_empresa` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'nombre de la empresa o cliente privado',
  `rif` varchar(20) NOT NULL,
  `razon_social` varchar(255) NOT NULL,
  `tipo_contribuyente` enum('ORDINARIO','ESPECIAL','FORMAL') NOT NULL,
  `estado_activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id_factura` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `id_tercero` int(11) NOT NULL,
  `tipo_transaccion` enum('VENTA','COMPRA') NOT NULL,
  `nro_factura` varchar(50) NOT NULL,
  `nro_control` varchar(50) NOT NULL,
  `fecha_documento` date NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'fecha del registro',
  `monto_exento` decimal(18,2) DEFAULT 0.00,
  `base_imponible` decimal(18,2) DEFAULT 0.00,
  `monto_iva` decimal(18,2) DEFAULT 0.00,
  `monto_igtf` decimal(18,2) DEFAULT 0.00,
  `total_factura` decimal(18,2) NOT NULL,
  `estado_pago` enum('PAGADA','PENDIENTE','ABONADA') DEFAULT 'PENDIENTE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `retenciones_islr`
--

CREATE TABLE `retenciones_islr` (
  `id_retencion_islr` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `monto_retenido_islr` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `retenciones_iva`
--

CREATE TABLE `retenciones_iva` (
  `id_retencion` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `nro_comprobante` varchar(50) NOT NULL,
  `monto_retenido` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('ADMIN','CONTADOR') DEFAULT 'CONTADOR'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asiento_detalle`
--
ALTER TABLE `asiento_detalle`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `fk_asiento_detalle` (`id_asiento`),
  ADD KEY `fk_cuenta_detalle` (`id_cuenta`);

--
-- Indices de la tabla `asiento_diario`
--
ALTER TABLE `asiento_diario`
  ADD PRIMARY KEY (`id_asiento`),
  ADD UNIQUE KEY `nro_comprobante` (`nro_comprobante`),
  ADD KEY `fk_usuario_asiento` (`id_usuario`),
  ADD KEY `fk_factura_asiento` (`id_factura`);

--
-- Indices de la tabla `auditoria_sistema`
--
ALTER TABLE `auditoria_sistema`
  ADD PRIMARY KEY (`id_auditoria`),
  ADD KEY `fk_auditoria_usuario` (`id_usuario`);

--
-- Indices de la tabla `catalogo_cuentas`
--
ALTER TABLE `catalogo_cuentas`
  ADD PRIMARY KEY (`id_cuenta`),
  ADD UNIQUE KEY `codigo_cuenta` (`codigo_cuenta`),
  ADD KEY `cuenta_padre_id` (`cuenta_padre_id`);

--
-- Indices de la tabla `configuracion_cuentas`
--
ALTER TABLE `configuracion_cuentas`
  ADD PRIMARY KEY (`id_config`),
  ADD UNIQUE KEY `codigo_proceso` (`codigo_proceso`),
  ADD KEY `fk_config_cuenta` (`id_cuenta`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id_empleado`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD UNIQUE KEY `cedula_2` (`cedula`),
  ADD KEY `fk_empleado_empresa` (`id_empresa`);

--
-- Indices de la tabla `empresas_clientes`
--
ALTER TABLE `empresas_clientes`
  ADD PRIMARY KEY (`id_empresa`),
  ADD UNIQUE KEY `rif` (`rif`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id_factura`),
  ADD KEY `fk_factura_empresa` (`id_empresa`),
  ADD KEY `fk_factura_tercero` (`id_tercero`),
  ADD KEY `fk_usuario_factura` (`id_usuario`);

--
-- Indices de la tabla `retenciones_islr`
--
ALTER TABLE `retenciones_islr`
  ADD PRIMARY KEY (`id_retencion_islr`),
  ADD UNIQUE KEY `id_factura` (`id_factura`);

--
-- Indices de la tabla `retenciones_iva`
--
ALTER TABLE `retenciones_iva`
  ADD PRIMARY KEY (`id_retencion`),
  ADD UNIQUE KEY `id_factura` (`id_factura`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `username_2` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asiento_detalle`
--
ALTER TABLE `asiento_detalle`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asiento_diario`
--
ALTER TABLE `asiento_diario`
  MODIFY `id_asiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auditoria_sistema`
--
ALTER TABLE `auditoria_sistema`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `catalogo_cuentas`
--
ALTER TABLE `catalogo_cuentas`
  MODIFY `id_cuenta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion_cuentas`
--
ALTER TABLE `configuracion_cuentas`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresas_clientes`
--
ALTER TABLE `empresas_clientes`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `retenciones_islr`
--
ALTER TABLE `retenciones_islr`
  MODIFY `id_retencion_islr` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `retenciones_iva`
--
ALTER TABLE `retenciones_iva`
  MODIFY `id_retencion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asiento_detalle`
--
ALTER TABLE `asiento_detalle`
  ADD CONSTRAINT `fk_asiento_detalle` FOREIGN KEY (`id_asiento`) REFERENCES `asiento_diario` (`id_asiento`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cuenta_detalle` FOREIGN KEY (`id_cuenta`) REFERENCES `catalogo_cuentas` (`id_cuenta`);

--
-- Filtros para la tabla `asiento_diario`
--
ALTER TABLE `asiento_diario`
  ADD CONSTRAINT `fk_factura_asiento` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id_factura`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_usuario_asiento` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `auditoria_sistema`
--
ALTER TABLE `auditoria_sistema`
  ADD CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `catalogo_cuentas`
--
ALTER TABLE `catalogo_cuentas`
  ADD CONSTRAINT `catalogo_cuentas_ibfk_1` FOREIGN KEY (`cuenta_padre_id`) REFERENCES `catalogo_cuentas` (`id_cuenta`);

--
-- Filtros para la tabla `configuracion_cuentas`
--
ALTER TABLE `configuracion_cuentas`
  ADD CONSTRAINT `fk_config_cuenta` FOREIGN KEY (`id_cuenta`) REFERENCES `catalogo_cuentas` (`id_cuenta`);

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `fk_empleado_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas_clientes` (`id_empresa`) ON DELETE CASCADE;

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `fk_factura_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas_clientes` (`id_empresa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usuario_factura` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `retenciones_islr`
--
ALTER TABLE `retenciones_islr`
  ADD CONSTRAINT `fk_islr_factura` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id_factura`) ON DELETE CASCADE;

--
-- Filtros para la tabla `retenciones_iva`
--
ALTER TABLE `retenciones_iva`
  ADD CONSTRAINT `fk_retencion_factura` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id_factura`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
