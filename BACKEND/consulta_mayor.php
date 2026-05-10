<?php
function obtenerLibroMayor($conexion) {
    // Usamos el nombre exacto de tu tabla: fecha_asiento
    $sql = "SELECT 
                c.codigo_cuenta, 
                c.nombre_cuenta, 
                SUM(IFNULL(m.debe, 0)) as total_debe, 
                SUM(IFNULL(m.haber, 0)) as total_haber,
                (SUM(IFNULL(m.debe, 0)) - SUM(IFNULL(m.haber, 0))) as saldo,
                MAX(a.fecha_asiento) as fecha_operacion 
            FROM catalogo_cuentas c
            LEFT JOIN asiento_detalle m ON c.id_cuenta = m.id_cuenta
            LEFT JOIN asiento_diario a ON m.id_asiento = a.id_asiento
            GROUP BY c.id_cuenta, c.codigo_cuenta, c.nombre_cuenta
            ORDER BY c.codigo_cuenta ASC";
            
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error en la consulta: " . $conexion->error);
    }

    return $resultado;
}
?>