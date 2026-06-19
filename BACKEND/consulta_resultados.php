<?php
// BACKEND/consulta_resultados.php
require_once 'conecxion_bd.php';

// ============================================================
// FUNCIÓN PARA OBTENER DATOS DEL ESTADO DE RESULTADOS
// ============================================================
function obtenerEstadoResultados($conexion, $mes = null, $anio = null, $id_empresa = null) {
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
    // CONSULTA: Ventas (Cuenta 4 - Ingresos)
    // ============================================================
    $sql_ventas = "SELECT 
                        SUM(det.debe) - SUM(det.haber) as total
                    FROM catalogo_cuentas cc
                    INNER JOIN asiento_detalle det ON cc.id_cuenta = det.id_cuenta
                    INNER JOIN asiento_diario ad ON det.id_asiento = ad.id_asiento
                    INNER JOIN facturas f ON ad.id_factura = f.id_factura
                    WHERE cc.codigo_cuenta LIKE '4%'
                    AND MONTH(ad.fecha_asiento) = '$mes' 
                    AND YEAR(ad.fecha_asiento) = '$anio'";
    
    if ($id_empresa !== null && $id_empresa !== '') {
        $sql_ventas .= " AND f.id_empresa = '$id_empresa'";
    }
    
    $result_ventas = $conexion->query($sql_ventas);
    $ventas = 0;
    if ($result_ventas && $row = $result_ventas->fetch_assoc()) {
        $ventas = $row['total'] ?? 0;
    }

    // ============================================================
    // CONSULTA: Costos (Cuenta 6 - Gastos/Costos)
    // ============================================================
    $sql_costos = "SELECT 
                        SUM(det.debe) - SUM(det.haber) as total
                    FROM catalogo_cuentas cc
                    INNER JOIN asiento_detalle det ON cc.id_cuenta = det.id_cuenta
                    INNER JOIN asiento_diario ad ON det.id_asiento = ad.id_asiento
                    INNER JOIN facturas f ON ad.id_factura = f.id_factura
                    WHERE cc.codigo_cuenta LIKE '6%'
                    AND MONTH(ad.fecha_asiento) = '$mes' 
                    AND YEAR(ad.fecha_asiento) = '$anio'";
    
    if ($id_empresa !== null && $id_empresa !== '') {
        $sql_costos .= " AND f.id_empresa = '$id_empresa'";
    }
    
    $result_costos = $conexion->query($sql_costos);
    $costos = 0;
    if ($result_costos && $row = $result_costos->fetch_assoc()) {
        $costos = $row['total'] ?? 0;
    }

    // ============================================================
    // CONSULTA: Gastos Operativos (Cuenta 5 - Gastos)
    // ============================================================
    $sql_gastos = "SELECT 
                        SUM(det.debe) - SUM(det.haber) as total
                    FROM catalogo_cuentas cc
                    INNER JOIN asiento_detalle det ON cc.id_cuenta = det.id_cuenta
                    INNER JOIN asiento_diario ad ON det.id_asiento = ad.id_asiento
                    INNER JOIN facturas f ON ad.id_factura = f.id_factura
                    WHERE cc.codigo_cuenta LIKE '5%'
                    AND MONTH(ad.fecha_asiento) = '$mes' 
                    AND YEAR(ad.fecha_asiento) = '$anio'";
    
    if ($id_empresa !== null && $id_empresa !== '') {
        $sql_gastos .= " AND f.id_empresa = '$id_empresa'";
    }
    
    $result_gastos = $conexion->query($sql_gastos);
    $gastos = 0;
    if ($result_gastos && $row = $result_gastos->fetch_assoc()) {
        $gastos = $row['total'] ?? 0;
    }

    // ============================================================
    // DEVOLVER RESULTADOS
    // ============================================================
    return [
        'ventas' => $ventas,
        'costos' => $costos,
        'gastos' => $gastos
    ];
}
?>