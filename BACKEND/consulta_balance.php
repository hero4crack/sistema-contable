<?php
// BACKEND/consulta_balance.php
require_once 'conecxion_bd.php';

// ============================================================
// FUNCIÓN PARA OBTENER BALANCE DE COMPROBACIÓN
// ============================================================
function obtenerBalanceComprobacion($conexion, $mes = null, $anio = null, $id_empresa = null) {
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
                cc.codigo_cuenta,
                cc.nombre_cuenta,
                SUM(det.debe) as sumas_debe,
                SUM(det.haber) as sumas_haber,
                CASE 
                    WHEN SUM(det.debe) > SUM(det.haber) THEN SUM(det.debe) - SUM(det.haber)
                    ELSE 0
                END as saldo_deudor,
                CASE 
                    WHEN SUM(det.haber) > SUM(det.debe) THEN SUM(det.haber) - SUM(det.debe)
                    ELSE 0
                END as saldo_acreedor
            FROM catalogo_cuentas cc
            INNER JOIN asiento_detalle det ON cc.id_cuenta = det.id_cuenta
            INNER JOIN asiento_diario ad ON det.id_asiento = ad.id_asiento
            LEFT JOIN facturas f ON ad.id_factura = f.id_factura
            WHERE MONTH(ad.fecha_asiento) = '$mes' 
              AND YEAR(ad.fecha_asiento) = '$anio'";
    
    // ============================================================
    // AGREGAR FILTRO POR EMPRESA
    // ============================================================
    if ($id_empresa !== null && $id_empresa !== '') {
        $sql .= " AND f.id_empresa = '$id_empresa'";
    }
    
    $sql .= " GROUP BY cc.id_cuenta
              ORDER BY cc.codigo_cuenta ASC";
    
    $resultado = $conexion->query($sql);
    
    if (!$resultado) {
        die("Error al obtener el Balance de Comprobación: " . $conexion->error);
    }
    
    return $resultado;
}
?>