<?php
// BACKEND/consulta_asientos.php
require_once 'conecxion_bd.php';

// ============================================================
// FUNCIÓN PARA OBTENER ASIENTOS (con filtros)
// ============================================================
function obtenerAsientos($conexion, $mes = null, $anio = null, $id_empresa = null) {
    // Si no se pasan mes y año, por defecto usamos el mes y año actuales
    if ($mes === null) $mes = date('m');
    if ($anio === null) $anio = date('Y');

    // Escapamos los datos por seguridad
    $mes = $conexion->real_escape_string($mes);
    $anio = $conexion->real_escape_string($anio);
    
    if ($id_empresa !== null && $id_empresa !== '') {
        $id_empresa = $conexion->real_escape_string($id_empresa);
    }

    // ============================================================
    // CONSULTA PRINCIPAL
    // ============================================================
    $sql = "SELECT 
                ad.id_asiento,
                ad.fecha_asiento,
                ad.nro_comprobante,
                ad.glosa,
                SUM(det.debe) as total_debe,
                SUM(det.haber) as total_haber
            FROM asiento_diario ad
            INNER JOIN asiento_detalle det ON ad.id_asiento = det.id_asiento
            LEFT JOIN facturas f ON ad.id_factura = f.id_factura
            WHERE MONTH(ad.fecha_asiento) = '$mes' 
              AND YEAR(ad.fecha_asiento) = '$anio'";
    
    // ============================================================
    // AGREGAR FILTRO POR EMPRESA
    // ============================================================
    if ($id_empresa !== null && $id_empresa !== '') {
        $sql .= " AND f.id_empresa = '$id_empresa'";
    }
    
    $sql .= " GROUP BY ad.id_asiento
              ORDER BY ad.fecha_asiento DESC, ad.id_asiento DESC";
    
    $resultado = $conexion->query($sql);
    
    if (!$resultado) {
        die("Error al obtener los asientos: " . $conexion->error);
    }
    
    return $resultado;
}

// ============================================================
// FUNCIÓN PARA OBTENER CUENTAS DEL CATÁLOGO
// ============================================================
function obtenerCuentasParaAsiento($conexion) {
    $sql = "SELECT id_cuenta, codigo_cuenta, nombre_cuenta 
            FROM catalogo_cuentas 
            WHERE permite_movimiento = 1 
            ORDER BY codigo_cuenta ASC";
    
    $resultado = $conexion->query($sql);
    
    if (!$resultado) {
        die("Error al obtener las cuentas: " . $conexion->error);
    }
    
    return $resultado;
}
?>