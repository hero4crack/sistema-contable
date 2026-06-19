-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-06-2026 a las 18:10:56
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
-- Base de datos: `bd_sistema_contable_ea`
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

--
-- Volcado de datos para la tabla `asiento_detalle`
--

INSERT INTO `asiento_detalle` (`id_detalle`, `id_asiento`, `id_cuenta`, `debe`, `haber`) VALUES
(1, 4, 11, 100.00, 0.00),
(2, 4, 18, 0.00, 100.00),
(3, 5, 15, 100.00, 0.00),
(4, 5, 16, 0.00, 100.00),
(5, 8, 11, 100.00, 0.00),
(6, 8, 18, 0.00, 50.00),
(7, 8, 17, 0.00, 50.00),
(8, 9, 11, 150.00, 100.00),
(9, 9, 14, 0.00, 50.00),
(12, 10, 20, 100.00, 100.00),
(13, 10, 16, 0.00, 0.00),
(14, 12, 20, 900.00, 400.00),
(15, 12, 15, 0.00, 500.00),
(16, 13, 13, 500.00, 100.00),
(17, 13, 14, 0.00, 400.00),
(18, 15, 5, 3580.00, 0.00),
(19, 15, 10, 0.00, 3100.00),
(20, 15, 15, 0.00, 480.00),
(21, 16, 20, 5100.00, 0.00),
(22, 16, 8, 800.00, 0.00),
(23, 16, 12, 0.00, 5900.00),
(24, 17, 11, 300.00, 100.00),
(25, 17, 14, 0.00, 200.00),
(26, 18, 5, 58.00, 0.00),
(27, 18, 10, 0.00, 50.00),
(28, 18, 15, 0.00, 8.00),
(29, 19, 5, 6443.80, 0.00),
(30, 19, 10, 0.00, 5555.00),
(31, 19, 15, 0.00, 888.80),
(32, 20, 5, 58.00, 0.00),
(33, 20, 10, 0.00, 50.00),
(34, 20, 15, 0.00, 8.00),
(35, 21, 5, 17.40, 0.00),
(36, 21, 10, 0.00, 15.00),
(37, 21, 15, 0.00, 2.40),
(38, 22, 5, 25.52, 0.00),
(39, 22, 10, 0.00, 22.00),
(40, 22, 15, 0.00, 3.52),
(41, 23, 20, 200.00, 0.00),
(42, 23, 8, 32.00, 0.00),
(43, 23, 12, 0.00, 232.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asiento_diario`
--

CREATE TABLE `asiento_diario` (
  `id_asiento` int(11) NOT NULL,
  `nro_comprobante` varchar(20) NOT NULL,
  `tipo_comprobante` enum('ingreso','egreso','diario') NOT NULL,
  `fecha_asiento` date NOT NULL,
  `glosa` varchar(2000) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_factura` int(11) DEFAULT NULL,
  `id_periodo` int(11) DEFAULT NULL,
  `estado_asiento` enum('borrador','posteado','anulado') DEFAULT 'borrador',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asiento_diario`
--

INSERT INTO `asiento_diario` (`id_asiento`, `nro_comprobante`, `tipo_comprobante`, `fecha_asiento`, `glosa`, `id_usuario`, `id_factura`, `id_periodo`, `estado_asiento`, `creado_en`) VALUES
(4, '00004', 'ingreso', '2026-05-09', 'prueba 4', 1, NULL, NULL, 'borrador', '2026-05-09 05:27:34'),
(5, '00005', 'ingreso', '2026-05-09', 'prueba 5', 1, NULL, NULL, 'borrador', '2026-05-09 05:53:02'),
(8, '0001', 'ingreso', '2026-05-09', 'prueba 01', 1, NULL, NULL, 'borrador', '2026-05-09 07:35:01'),
(9, '0002', 'ingreso', '2026-05-09', 'prueba2', 1, NULL, NULL, 'borrador', '2026-05-09 07:37:41'),
(10, '020258', 'ingreso', '2026-06-02', 'prueba de muestra', 1, NULL, NULL, 'borrador', '2026-06-03 00:39:36'),
(12, '0015', 'diario', '2026-06-05', 'prueba A', 1, NULL, 1, '', '2026-06-04 21:18:20'),
(13, '2356', 'diario', '2026-06-05', 'prueba B', 1, NULL, 1, '', '2026-06-04 21:19:19'),
(15, 'AUTO-V00008', '', '2026-06-05', 'Asiento automático según factura fiscal Nro: 2586', 1, 26, 1, '', '2026-06-04 21:48:29'),
(16, 'AUTO-C00009', '', '2026-06-05', 'Asiento automático según factura fiscal Nro: 456987', 1, 27, 1, '', '2026-06-04 21:49:17'),
(17, '0000005', 'diario', '2026-06-05', 'prueba C', 1, NULL, 1, '', '2026-06-04 21:50:54'),
(18, 'AUTO-V00011', '', '2026-06-19', 'Asiento automático según factura fiscal Nro: 00989', 1, 1234567893, 1, '', '2026-06-19 04:01:46'),
(19, 'AUTO-V00012', '', '2026-06-19', 'Asiento automático según factura fiscal Nro: 00232', 1, 1234567894, 1, '', '2026-06-19 14:58:01'),
(20, 'AUTO-V00013', '', '2026-06-19', 'Asiento automático según factura fiscal Nro: 00989', 1, 1234567895, 1, '', '2026-06-19 14:58:45'),
(21, 'AUTO-V00014', '', '2026-06-19', 'Asiento automático según factura fiscal Nro: 00847', 1, 1234567896, 1, '', '2026-06-19 15:00:04'),
(22, 'AUTO-V00015', '', '2026-06-19', 'Asiento automático según factura fiscal Nro: 00443', 1, 1234567897, 1, '', '2026-06-19 15:00:36'),
(23, 'AUTO-C00016', '', '2026-06-19', 'Asiento automático según factura fiscal Nro: 00988', 1, 1234567898, 1, '', '2026-06-19 15:02:42');

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
  `valor_anterior` longtext DEFAULT NULL,
  `valor_nuevo` longtext DEFAULT NULL,
  `ip_maquina` varchar(45) DEFAULT NULL,
  `fecha_accion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria_sistema`
--

INSERT INTO `auditoria_sistema` (`id_auditoria`, `id_usuario`, `tabla_afectada`, `id_registro_afectado`, `accion`, `valor_anterior`, `valor_nuevo`, `ip_maquina`, `fecha_accion`) VALUES
(1, 1, 'prueba_manual', 0, 'INSERT', 'Sin datos', 'Prueba exitosa', '127.0.0.1', '2026-05-10 02:56:18'),
(3, 1, 'catalogo_cuentas', 6, 'UPDATE', 'ACTIVO CORRIENTE', 'ACTIVO CORRIENTE', 'SISTEMA', '2026-06-05 03:40:43'),
(4, 1, 'catalogo_cuentas', 1, 'UPDATE', 'ACTIVO', 'ACTIVO', 'SISTEMA', '2026-06-05 03:55:57');

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

--
-- Volcado de datos para la tabla `catalogo_cuentas`
--

INSERT INTO `catalogo_cuentas` (`id_cuenta`, `codigo_cuenta`, `nombre_cuenta`, `nivel`, `tipo_cuenta`, `cuenta_padre_id`, `permite_movimiento`) VALUES
(1, '1', 'ACTIVO', 1, 'Activo', NULL, 0),
(2, '2', 'PASIVO', 2, 'Pasivo', NULL, 0),
(3, '3', 'PATRIMONIO', 3, 'Patrimonio', NULL, 0),
(4, '4', 'INGRESOS', 4, '', NULL, 0),
(5, '5', 'EGRESOS', 5, 'Egreso', NULL, 0),
(6, '1.1', 'ACTIVO CORRIENTE', 2, 'Activo', NULL, 1),
(7, '1.2', 'ACTIVO NO CORRIENTE', 2, 'Activo', NULL, 0),
(8, '2.1', 'PASIVO CORRIENTE', 2, 'Pasivo', NULL, 0),
(9, '4.1', 'INGRESOS OPERACIONALES', 2, '', NULL, 0),
(10, '5.1', 'GASTOS OPERACIONALES', 2, 'Egreso', NULL, 0),
(11, '1.1.01', 'CAJA CHICA', 3, 'Activo', NULL, 1),
(12, '1.1.02', 'BANCOS', 3, 'Activo', NULL, 0),
(13, '1.1.02.01', 'BANCO BANESCO', 4, 'Activo', NULL, 1),
(14, '1.1.02.02', 'BANCO MERCANTIL', 4, 'Activo', NULL, 1),
(15, '1.1.03', 'CUENTAS POR COBRAR', 3, 'Activo', NULL, 1),
(16, '2.1.01', 'CUENTAS POR PAGAR', 3, 'Pasivo', NULL, 1),
(17, '2.1.02', 'IVA POR PAGAR (DÉBITO FISCAL)', 3, 'Pasivo', NULL, 1),
(18, '4.1.01', 'VENTAS DE MERCANCÍA', 3, '', NULL, 1),
(19, '5.1.01', 'GASTOS DE PERSONAL / NÓMINA', 3, 'Egreso', NULL, 1),
(20, '5.1.02', 'GASTOS DE SERVICIOS PÚBLICOS', 3, 'Egreso', NULL, 1),
(21, '6', 'COSTOS', 5, 'Egreso', NULL, 1),
(22, '6.1', 'COSTO DE VENTAS', 5, 'Egreso', NULL, 1),
(23, '6.1.01', 'COSTO DE VENTAS (MERCANCÍA)', 5, 'Egreso', NULL, 1);

--
-- Disparadores `catalogo_cuentas`
--
DELIMITER $$
CREATE TRIGGER `auditoria_catalogo_trigger` AFTER UPDATE ON `catalogo_cuentas` FOR EACH ROW BEGIN
    INSERT INTO auditoria_sistema 
    (id_usuario, tabla_afectada, id_registro_afectado, accion, valor_anterior, valor_nuevo, ip_maquina) 
    VALUES 
    (1, 'catalogo_cuentas', OLD.id_cuenta, 'UPDATE', OLD.nombre_cuenta, NEW.nombre_cuenta, 'SISTEMA');
END
$$
DELIMITER ;

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
  `cedula` varchar(20) NOT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `telefono` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id_empleado`, `cedula`, `nombre_completo`, `telefono`) VALUES
(1, '30009775', 'Hanomiya Hoshi Games', '0426306023'),
(2, '31037086', 'Noa Navas', '04244981635'),
(4, '31334123', 'jose vargas', '04124507316'),
(5, '30097086', 'Miguel Nectario Navarro Flores', '04127402007');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas_clientes`
--

CREATE TABLE `empresas_clientes` (
  `id_empresa` int(11) NOT NULL,
  `nombre_empresa` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'nombre de la empresa o cliente privado',
  `rif` varchar(20) NOT NULL,
  `razon_social` varchar(255) NOT NULL,
  `direccion_fiscal` text DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo_electronico` varchar(100) DEFAULT NULL,
  `tipo_contribuyente` enum('ORDINARIO','ESPECIAL','FORMAL') NOT NULL,
  `estado_activo` tinyint(1) DEFAULT 1,
  `pais` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `nombre_responsable` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'nombre del representante de la empresa',
  `cedula_responsable` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'cedula del responsable',
  `telefono_responsable` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'numero de telefono del responsable',
  `correo_responsable` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'correo del responsable',
  `servicio_activo` tinyint(1) DEFAULT 0 COMMENT '0=Pendiente, 1=Pagado',
  `fecha_ultimo_pago` date DEFAULT NULL,
  `fecha_proximo_pago` date DEFAULT NULL,
  `monto_servicio` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresas_clientes`
--

INSERT INTO `empresas_clientes` (`id_empresa`, `nombre_empresa`, `rif`, `razon_social`, `direccion_fiscal`, `telefono`, `correo_electronico`, `tipo_contribuyente`, `estado_activo`, `pais`, `nombre_responsable`, `cedula_responsable`, `telefono_responsable`, `correo_responsable`, `servicio_activo`, `fecha_ultimo_pago`, `fecha_proximo_pago`, `monto_servicio`) VALUES
(1, 'servi sistemas navarro', 'J-54513455-2', '1', 'mi oficina', '04244981635', 'aaaa@gmail.com', 'ORDINARIO', 1, 'venezuela', 'Miguel Navarro', '30097086', '0412 4562536', 'miguel@gmail.com', 0, NULL, NULL, 0.00),
(4, 'fr servivios y mantenimiento', 'J-29556205-5', 'sabra dios', 'galpon', '', '', 'FORMAL', 1, '', 'Fabianna Rios', '30009775', '04244195063', 'iiiii@gmail.com', 1, '2026-06-17', '2026-06-18', 100.00),
(5, ' CHOCOLATE & MAS', 'G-12575603-4', 'asdf', 'asddfdgfhg', '0426306023', 'john@gmail.com', 'ESPECIAL', 1, 'Venezuela', 'jhon', '31097086', '04244195063', 'mmmm@gmail.com', 1, '2026-06-19', '2026-07-19', 100.00),
(6, 'm&n', 'V-27843605-9', 'qwerty', 'ertyuiop', '0412- 1234875', 'alex@gmail.com', 'ORDINARIO', 0, 'China', 'Alex Jajas', '33009556', '04244195065', 'alexp@gmail.com', 0, NULL, NULL, 0.00),
(7, 'panaderia 67', 'J-29536408-5', 'sabra dios', 'ese mismo', '0415 - 3215648', 'panaderia@gmail.com', 'ESPECIAL', 1, 'Venezuela', 'Miguel Navarro', '30097086', '0412 4562536', 'miguel@gmail.com', 0, NULL, NULL, 0.00),
(8, 'jose vargas', 'J-31006889', 'prueba', 'santa ana', '04163905023', 'prueba@gmail.com', 'FORMAL', 1, 'Venezuela', 'juanito', '31334123', '04124507375', 'prueba2@gmail.com', 0, NULL, NULL, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id_factura` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_periodo` int(11) DEFAULT NULL,
  `id_tercero` int(11) DEFAULT NULL,
  `rif_proveedor` varchar(20) DEFAULT NULL,
  `cliente_nombre` varchar(255) DEFAULT NULL,
  `cliente_rif` varchar(20) DEFAULT NULL,
  `tipo_transaccion` enum('VENTA','COMPRA') NOT NULL,
  `nro_factura` varchar(50) NOT NULL,
  `nro_control` varchar(50) NOT NULL,
  `nro_comprobante_retencion` varchar(20) DEFAULT NULL,
  `fecha_documento` date NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'fecha del registro',
  `base_imponible` decimal(18,2) DEFAULT 0.00,
  `monto_exento` decimal(18,2) DEFAULT 0.00,
  `ventas_tasa_cero` decimal(18,2) DEFAULT 0.00,
  `monto_iva` decimal(18,2) DEFAULT 0.00,
  `alicuota_iva` decimal(5,2) DEFAULT 16.00,
  `monto_igtf` decimal(18,2) DEFAULT 0.00,
  `monto_iva_retenido` decimal(18,2) DEFAULT 0.00,
  `ruta_comprobante_imagen` varchar(255) DEFAULT NULL,
  `total_factura` decimal(18,2) NOT NULL,
  `estado_pago` enum('PAGADA','PENDIENTE','ABONADA') DEFAULT 'PENDIENTE',
  `tipo_documento` enum('FACTURA','NOTA_CREDITO','NOTA_DEBITO') DEFAULT 'FACTURA',
  `imp_base_imponible` decimal(18,2) DEFAULT 0.00,
  `imp_alicuota` decimal(5,2) DEFAULT 0.00,
  `imp_impuesto` decimal(18,2) DEFAULT 0.00,
  `int_base_imponible` decimal(18,2) DEFAULT 0.00,
  `int_alicuota` decimal(5,2) DEFAULT 0.00,
  `int_impuesto` decimal(18,2) DEFAULT 0.00,
  `compras_sin_derecho_credito` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id_factura`, `id_usuario`, `id_empresa`, `id_periodo`, `id_tercero`, `rif_proveedor`, `cliente_nombre`, `cliente_rif`, `tipo_transaccion`, `nro_factura`, `nro_control`, `nro_comprobante_retencion`, `fecha_documento`, `fecha_registro`, `base_imponible`, `monto_exento`, `ventas_tasa_cero`, `monto_iva`, `alicuota_iva`, `monto_igtf`, `monto_iva_retenido`, `ruta_comprobante_imagen`, `total_factura`, `estado_pago`, `tipo_documento`, `imp_base_imponible`, `imp_alicuota`, `imp_impuesto`, `int_base_imponible`, `int_alicuota`, `int_impuesto`, `compras_sin_derecho_credito`) VALUES
(14, NULL, 4, NULL, 0, NULL, NULL, NULL, 'VENTA', '001', '00-112', NULL, '2026-05-10', '2026-05-10 01:56:33', 505.00, 20.00, 0.00, 80.80, 16.00, 0.00, 0.00, NULL, 605.80, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(16, 1, NULL, NULL, 1, NULL, NULL, NULL, 'COMPRA', 'CP-0001', 'CTRL-001', '0000003', '2026-05-10', '2026-06-04 19:19:35', 1000.00, 0.00, 0.00, 160.00, 16.00, 0.00, 0.00, NULL, 1160.00, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(17, NULL, 8, NULL, NULL, NULL, NULL, NULL, 'VENTA', '235688', '020215', NULL, '2026-06-03', '2026-06-19 14:47:22', 1000.00, 50.00, 0.00, 160.00, 16.00, 0.00, 0.00, NULL, 1210.00, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(19, NULL, NULL, NULL, 2, NULL, NULL, NULL, 'COMPRA', '0202', '00-222', '0000002', '2026-06-04', '2026-06-04 19:19:11', 200.00, 32.00, 0.00, 32.00, 16.00, 0.00, 0.00, NULL, 232.00, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(20, NULL, 8, NULL, NULL, NULL, NULL, NULL, 'VENTA', '00123', '00-26', NULL, '2026-06-04', '2026-06-04 15:00:44', 1000.00, 20.30, 0.00, 160.00, 16.00, 0.00, 0.00, NULL, 1180.30, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(21, NULL, 4, NULL, NULL, NULL, NULL, NULL, 'VENTA', '00009', '00-56', NULL, '2026-06-04', '2026-06-04 15:01:17', 2000.00, 100.00, 0.00, 320.00, 16.00, 0.00, 0.00, NULL, 2420.00, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(22, NULL, NULL, NULL, 3, NULL, NULL, NULL, 'COMPRA', '0005', '00-85', '0000004', '2026-06-04', '2026-06-04 19:20:49', 800.00, 96.00, 0.00, 128.00, 16.00, 0.00, 0.00, NULL, 978.00, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(23, NULL, 8, NULL, NULL, NULL, NULL, NULL, 'VENTA', '00056', '00-45', NULL, '2026-06-04', '2026-06-04 19:21:36', 900.00, 50.00, 0.00, 144.00, 16.00, 0.00, 0.00, NULL, 1094.00, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(24, NULL, NULL, NULL, 1, NULL, NULL, NULL, 'COMPRA', '00213', '00-78', '0000005', '2026-06-05', '2026-06-04 20:35:31', 20000.00, 0.00, 0.00, 3200.00, 16.00, 0.00, 0.00, NULL, 23200.00, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(26, NULL, 5, NULL, 5, NULL, NULL, NULL, 'VENTA', '2586', '00-741', NULL, '2026-06-05', '2026-06-04 21:48:29', 3000.00, 100.00, 0.00, 480.00, 16.00, 0.00, 0.00, NULL, 3580.00, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(27, NULL, NULL, NULL, 2, NULL, NULL, NULL, 'COMPRA', '456987', '00-25714', '0000006', '2026-06-05', '2026-06-04 21:49:17', 5000.00, 100.00, 0.00, 800.00, 16.00, 0.00, 0.00, NULL, 5900.00, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(1234567892, 1, 4, 1, 13, 'J 29556208-6', 'juanito perez', 'V 30009665-5', 'VENTA', '0039123', '00023', '0000056', '2026-05-30', '2026-06-13 16:19:56', 1465.52, 200.00, 0.00, 16.00, 16.00, 5.00, 10.00, NULL, 1700.00, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(1234567893, NULL, 8, NULL, 8, NULL, NULL, NULL, 'VENTA', '00989', '00-00555', NULL, '2026-06-19', '2026-06-19 04:01:46', 50.00, 0.00, 0.00, 8.00, 16.00, 0.00, 0.00, NULL, 58.00, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(1234567894, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'VENTA', '00232', '00-00333', NULL, '2026-06-19', '2026-06-19 14:58:01', 5555.00, 0.00, 0.00, 888.80, 16.00, 0.00, 0.00, NULL, 6443.80, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(1234567895, NULL, 4, NULL, 4, NULL, NULL, NULL, 'VENTA', '00989', '00-00333', NULL, '2026-06-19', '2026-06-19 14:58:45', 50.00, 0.00, 0.00, 8.00, 16.00, 0.00, 0.00, NULL, 58.00, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(1234567896, NULL, 7, NULL, 7, NULL, NULL, NULL, 'VENTA', '00847', '00-00212', NULL, '2026-06-19', '2026-06-19 15:00:04', 15.00, 0.00, 0.00, 2.40, 16.00, 0.00, 0.00, NULL, 17.40, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(1234567897, NULL, 1, NULL, 1, NULL, NULL, NULL, 'VENTA', '00443', '00-00334', NULL, '2026-06-19', '2026-06-19 15:00:36', 22.00, 0.00, 0.00, 3.52, 16.00, 0.00, 0.00, NULL, 25.52, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(1234567898, NULL, NULL, NULL, 1, NULL, NULL, NULL, 'COMPRA', '00988', '00-00576', '202606178768768768', '2026-06-19', '2026-06-19 15:02:42', 200.00, 0.00, 0.00, 32.00, 16.00, 0.00, 0.00, NULL, 232.00, 'PENDIENTE', 'FACTURA', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodo_contable`
--

CREATE TABLE `periodo_contable` (
  `id_periodo` int(11) NOT NULL,
  `mes` int(2) NOT NULL,
  `anio` int(4) NOT NULL,
  `estado` enum('Abierto','Cerrado') DEFAULT 'Abierto',
  `fecha_cierre` timestamp NULL DEFAULT NULL,
  `id_usuario_cierre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `periodo_contable`
--

INSERT INTO `periodo_contable` (`id_periodo`, `mes`, `anio`, `estado`, `fecha_cierre`, `id_usuario_cierre`) VALUES
(1, 6, 2026, 'Abierto', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id_proveedor` int(11) NOT NULL,
  `nombre_comercial` varchar(150) NOT NULL,
  `razon_social` varchar(150) NOT NULL,
  `rif` varchar(20) NOT NULL,
  `direccion_fiscal` text NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo_electronico` varchar(100) DEFAULT NULL,
  `tipo_contribuyente` enum('ORDINARIO','FORMAL','ESPECIAL') DEFAULT 'ORDINARIO',
  `porcentaje_retencion` enum('0','75','100') DEFAULT '0',
  `estado_activo` tinyint(1) DEFAULT 1,
  `nombre_contacto` varchar(100) DEFAULT NULL,
  `telefono_contacto` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id_proveedor`, `nombre_comercial`, `razon_social`, `rif`, `direccion_fiscal`, `telefono`, `correo_electronico`, `tipo_contribuyente`, `porcentaje_retencion`, `estado_activo`, `nombre_contacto`, `telefono_contacto`) VALUES
(1, 'juenitos burguer', 'juanito', 'J-12345678-9', 'su casa', '0242-3726260', 'juanito@gmail.com', 'ORDINARIO', '0', 1, 'juanita rodriguez', '04163907016'),
(2, 'juana shop', 'Juana', 'J-54513455-2', 'su tienda', '04247895624', 'juanashop@gmail.com', 'FORMAL', '100', 1, 'juanito gonzalez', '042448956'),
(3, 'shopdani', 'Carmen Zavala', 'V-55455345-2', 'tienda tangamandapio', '04123694578', 'shopdani@gmail.com', 'ESPECIAL', '75', 1, 'Carmen Zavala', '04123694578');

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
  `nombre_usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('ADMIN','CONTADOR','ASISTENTE') DEFAULT 'CONTADOR'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `password`, `rol`) VALUES
(1, 'fabiannarios', '123456789', 'ADMIN');

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
  ADD KEY `fk_asiento_periodo` (`id_periodo`),
  ADD KEY `fk_factura_asiento` (`id_factura`);

--
-- Indices de la tabla `auditoria_sistema`
--
ALTER TABLE `auditoria_sistema`
  ADD PRIMARY KEY (`id_auditoria`),
  ADD KEY `auditoria_usuario` (`id_usuario`);

--
-- Indices de la tabla `catalogo_cuentas`
--
ALTER TABLE `catalogo_cuentas`
  ADD PRIMARY KEY (`id_cuenta`),
  ADD UNIQUE KEY `codigo_cuenta` (`codigo_cuenta`),
  ADD KEY `id_cuenta_cuenta_padre` (`cuenta_padre_id`);

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
  ADD UNIQUE KEY `cedula_2` (`cedula`);

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
  ADD KEY `fk_usuario_factura` (`id_usuario`),
  ADD KEY `fk_factura_periodo` (`id_periodo`);

--
-- Indices de la tabla `periodo_contable`
--
ALTER TABLE `periodo_contable`
  ADD PRIMARY KEY (`id_periodo`),
  ADD KEY `fk_usuario_cierre` (`id_usuario_cierre`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id_proveedor`),
  ADD UNIQUE KEY `rif` (`rif`);

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
  ADD UNIQUE KEY `username` (`nombre_usuario`),
  ADD UNIQUE KEY `username_2` (`nombre_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asiento_detalle`
--
ALTER TABLE `asiento_detalle`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `asiento_diario`
--
ALTER TABLE `asiento_diario`
  MODIFY `id_asiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `auditoria_sistema`
--
ALTER TABLE `auditoria_sistema`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `catalogo_cuentas`
--
ALTER TABLE `catalogo_cuentas`
  MODIFY `id_cuenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `configuracion_cuentas`
--
ALTER TABLE `configuracion_cuentas`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `empresas_clientes`
--
ALTER TABLE `empresas_clientes`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1234567899;

--
-- AUTO_INCREMENT de la tabla `periodo_contable`
--
ALTER TABLE `periodo_contable`
  MODIFY `id_periodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  ADD CONSTRAINT `fk_ADiario_PContable` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_contable` (`id_periodo`),
  ADD CONSTRAINT `fk_asiento_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_contable` (`id_periodo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_factura_asiento` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id_factura`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usuario_asiento` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `auditoria_sistema`
--
ALTER TABLE `auditoria_sistema`
  ADD CONSTRAINT `auditoria_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `catalogo_cuentas`
--
ALTER TABLE `catalogo_cuentas`
  ADD CONSTRAINT `catalogo_cuentas_ibfk_1` FOREIGN KEY (`cuenta_padre_id`) REFERENCES `catalogo_cuentas` (`id_cuenta`),
  ADD CONSTRAINT `id_cuenta_cuenta_padre` FOREIGN KEY (`cuenta_padre_id`) REFERENCES `catalogo_cuentas` (`id_cuenta`);

--
-- Filtros para la tabla `configuracion_cuentas`
--
ALTER TABLE `configuracion_cuentas`
  ADD CONSTRAINT `fk_config_cuenta` FOREIGN KEY (`id_cuenta`) REFERENCES `catalogo_cuentas` (`id_cuenta`);

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `fk_factura_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas_clientes` (`id_empresa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_factura_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_contable` (`id_periodo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usuario_factura` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `periodo_contable`
--
ALTER TABLE `periodo_contable`
  ADD CONSTRAINT `fk_usuario_cierre` FOREIGN KEY (`id_usuario_cierre`) REFERENCES `usuarios` (`id_usuario`);

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
