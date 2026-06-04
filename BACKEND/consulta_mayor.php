<?php
// BACKEND/consulta_mayor.php

function obtenerLibroMayor($conexion) {
    // Corregido a 'catalogo_cuentas' en el FROM para solucionar el mysqli_sql_exception
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
            GROUP BY cc.id_cuenta, cc.codigo_cuenta, cc.nombre_cuenta
            ORDER BY cc.codigo_cuenta ASC";

    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error crítico al procesar el Libro Mayor: " . $conexion->error);
    }

    return $resultado;
}
?>