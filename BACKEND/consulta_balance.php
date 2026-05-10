<?php
function obtenerBalanceComprobacion($conexion) {
    $sql = "SELECT 
                c.codigo_cuenta, 
                c.nombre_cuenta, 
                SUM(IFNULL(m.debe, 0)) as sumas_debe, 
                SUM(IFNULL(m.haber, 0)) as sumas_haber,
                -- Cálculo de Saldos
                CASE 
                    WHEN (SUM(IFNULL(m.debe, 0)) - SUM(IFNULL(m.haber, 0))) > 0 
                    THEN (SUM(IFNULL(m.debe, 0)) - SUM(IFNULL(m.haber, 0))) 
                    ELSE 0 
                END as saldo_deudor,
                CASE 
                    WHEN (SUM(IFNULL(m.haber, 0)) - SUM(IFNULL(m.debe, 0))) > 0 
                    THEN (SUM(IFNULL(m.haber, 0)) - SUM(IFNULL(m.debe, 0))) 
                    ELSE 0 
                END as saldo_acreedor
            FROM catalogo_cuentas c
            INNER JOIN asiento_detalle m ON c.id_cuenta = m.id_cuenta
            GROUP BY c.id_cuenta, c.codigo_cuenta, c.nombre_cuenta
            ORDER BY c.codigo_cuenta ASC";
            
    return $conexion->query($sql);
}
?>