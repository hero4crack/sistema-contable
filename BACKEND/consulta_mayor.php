<?php
function obtenerLibroMayor($conexion) {
    $sql = "SELECT 
                c.codigo_cuenta, 
                c.nombre_cuenta, 
                SUM(d.debe) as total_debe, 
                SUM(d.haber) as total_haber,
                (SUM(d.debe) - SUM(d.haber)) as saldo
            FROM catalogo_cuentas c
            INNER JOIN asiento_detalle d ON c.id_cuenta = d.id_cuenta
            GROUP BY c.id_cuenta
            ORDER BY c.codigo_cuenta ASC";
    
    return $conexion->query($sql);
}
?>