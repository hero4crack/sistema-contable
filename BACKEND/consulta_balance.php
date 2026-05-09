<?php
function obtenerBalanceComprobacion($conexion) {
    $sql = "SELECT 
                c.codigo_cuenta, 
                c.nombre_cuenta, 
                SUM(d.debe) as sumas_debe, 
                SUM(d.haber) as sumas_haber,
                CASE 
                    WHEN (SUM(d.debe) - SUM(d.haber)) > 0 THEN (SUM(d.debe) - SUM(d.haber))
                    ELSE 0 
                END as saldo_deudor,
                CASE 
                    WHEN (SUM(d.haber) - SUM(d.debe)) > 0 THEN (SUM(d.haber) - SUM(d.debe))
                    ELSE 0 
                END as saldo_acreedor
            FROM catalogo_cuentas c
            INNER JOIN asiento_detalle d ON c.id_cuenta = d.id_cuenta
            GROUP BY c.id_cuenta
            ORDER BY c.codigo_cuenta ASC";
    
    return $conexion->query($sql);
}
?>