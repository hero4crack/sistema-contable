<?php
// BACKEND/consulta_factura.php
require_once 'conecxion_bd.php';

// Función para listar empresas y llenar el "Select" del formulario
function obtenerEmpresasParaFactura($conexion) {
    $sql = "SELECT id_empresa, nombre_empresa FROM empresas_clientes WHERE estado_activo = '1'";
    $resultado = $conexion->query($sql);
    return $resultado;
}

// Función para listar las facturas filtradas por período impositivo (Mes y Año)
function obtenerLibroFacturas($conexion, $mes = null, $anio = null) {
    // Si no se pasan mes y año, por defecto usamos el mes y año actuales del servidor
    if ($mes === null) $mes = date('m');
    if ($anio === null) $anio = date('Y');

    // Escapamos los datos por seguridad
    $mes = $conexion->real_escape_string($mes);
    $anio = $conexion->real_escape_string($anio);

    // Cruzamos la tabla facturas filtrando estrictamente por el período seleccionado
    $sql = "SELECT f.*, e.nombre_empresa, p.razon_social AS nombre_proveedor
            FROM facturas f 
            LEFT JOIN empresas_clientes e ON f.id_empresa = e.id_empresa 
            LEFT JOIN proveedores p ON f.id_tercero = p.id_proveedor 
            WHERE MONTH(f.fecha_documento) = '$mes' 
              AND YEAR(f.fecha_documento) = '$anio'
            ORDER BY f.fecha_documento ASC, f.id_factura ASC";
            
    $resultado = $conexion->query($sql);
    return $resultado;
}
?>