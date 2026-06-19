<?php
// BACKEND/consulta_mayor.php
require_once 'conecxion_bd.php';

function obtenerLibroMayor($conexion, $id_empresa = null) {
    
    // ============================================================
    // CONSULTA BASE CON RELACIÓN A FACTURAS
    // ============================================================
    $sql = "SELECT 
                MAX(ad.fecha_asiento) as fecha_operacion,
                cc.codigo_cuenta,
                cc.nombre_cuenta,
                SUM(det.debe) as total_debe,
                SUM(det.haber) as total_haber,
                CASE 
                    -- Si el código de cuenta empieza con 1, 5 o 6 (Activos, Costos, Gastos): Debe - Haber
                    WHEN cc.codigo_cuenta LIKE '1%' OR cc.codigo_cuenta LIKE '5%' OR cc.codigo_cuenta LIKE '6%' 
                        THEN (SUM(det.debe) - SUM(det.haber))
                    -- Si empieza con 2, 3 o 4 (Pasivos, Patrimonio, Ingresos): Haber - Debe
                    ELSE (SUM(det.haber) - SUM(det.debe))
                END as saldo
            FROM catalogo_cuentas cc
            INNER JOIN asiento_detalle det ON cc.id_cuenta = det.id_cuenta
            INNER JOIN asiento_diario ad ON det.id_asiento = ad.id_asiento
            INNER JOIN facturas f ON ad.id_factura = f.id_factura";
    
    // ============================================================
    // AGREGAR FILTRO POR EMPRESA (si se seleccionó una)
    // ============================================================
    if ($id_empresa !== null && $id_empresa !== '') {
        $id_empresa = $conexion->real_escape_string($id_empresa);
        $sql .= " WHERE f.id_empresa = '$id_empresa'";
    }
    
    $sql .= " GROUP BY cc.id_cuenta, cc.codigo_cuenta, cc.nombre_cuenta
              ORDER BY cc.codigo_cuenta ASC";

    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error crítico al procesar el Libro Mayor: " . $conexion->error);
    }

    return $resultado;
}
?>