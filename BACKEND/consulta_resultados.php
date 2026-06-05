<?php
function obtenerEstadoResultados($conexion) {
    // Vamos a forzar la consulta para ver qué grupos existen realmente
    $sql = "SELECT 
                LEFT(cc.codigo_cuenta, 1) as grupo,
                SUM(det.haber - det.debe) as total_grupo
            FROM catalogo_cuentas cc
            INNER JOIN asiento_detalle det ON cc.id_cuenta = det.id_cuenta
            GROUP BY grupo";

    $resultado = $conexion->query($sql);
    $datos = ['4' => 0, '5' => 0, '6' => 0];
    
    while ($row = $resultado->fetch_assoc()) {
        // Esto asignará los valores encontrados a los grupos correspondientes
        $datos[$row['grupo']] = $row['total_grupo'];
    }
    
    return $datos;
}
?>