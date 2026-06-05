<?php
// BACKEND/consulta_balance.php

function obtenerBalanceComprobacion($conexion) {
    // Consulta matemática avanzada para distribuir sumas y saldos según naturaleza contable
    $sql = "SELECT 
                cc.codigo_cuenta,
                cc.nombre_cuenta,
                SUM(det.debe) as sumas_debe,
                SUM(det.haber) as sumas_haber,
                
                -- Cálculo dinámico del Saldo Deudor
                CASE 
                    -- Cuentas de Activos (1), Costos (5) y Gastos (6): saldo natural en el Debe
                    WHEN cc.codigo_cuenta LIKE '1%' OR cc.codigo_cuenta LIKE '5%' OR cc.codigo_cuenta LIKE '6%' THEN
                        IF(SUM(det.debe) >= SUM(det.haber), SUM(det.debe) - SUM(det.haber), 0.00)
                    -- Cuentas de Pasivos, Patrimonio e Ingresos: solo muestran saldo aquí si el Debe supera al Haber (Inusual)
                    ELSE
                        IF(SUM(det.debe) > SUM(det.haber), SUM(det.debe) - SUM(det.haber), 0.00)
                END as saldo_deudor,

                -- Cálculo dinámico del Saldo Acreedor
                CASE 
                    -- Cuentas de Pasivos (2), Patrimonio (3) e Ingresos (4): saldo natural en el Haber
                    WHEN cc.codigo_cuenta LIKE '2%' OR cc.codigo_cuenta LIKE '3%' OR cc.codigo_cuenta LIKE '4%' THEN
                        IF(SUM(det.haber) >= SUM(det.debe), SUM(det.haber) - SUM(det.debe), 0.00)
                    -- Cuentas de Activos, Costos y Gastos: solo muestran saldo aquí si el Haber supera al Debe (Inusual)
                    ELSE
                        IF(SUM(det.haber) > SUM(det.debe), SUM(det.haber) - SUM(det.debe), 0.00)
                END as saldo_acreedor

            FROM catalogo_cuentas cc
            INNER JOIN asiento_detalle det ON cc.id_cuenta = det.id_cuenta
            INNER JOIN asiento_diario ad ON det.id_asiento = ad.id_asiento
            GROUP BY cc.id_cuenta, cc.codigo_cuenta, cc.nombre_cuenta
            ORDER BY cc.codigo_cuenta ASC";

    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error crítico al calcular el Balance de Comprobación: " . $conexion->error);
    }

    return $resultado;
}
?>