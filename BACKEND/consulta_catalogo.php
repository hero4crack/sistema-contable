<?php
// BACKEND/consulta_catalogo.php
require_once 'conecxion_bd.php';

// ============================================================
// FUNCIÓN PARA OBTENER EL CATÁLOGO DE CUENTAS
// ============================================================
function obtenerCatalogo($conexion, $id_empresa = null) {
    
    // Consulta base
    $sql = "SELECT 
                id_cuenta,
                codigo_cuenta,
                nombre_cuenta,
                tipo_cuenta,
                nivel,
                permite_movimiento
            FROM catalogo_cuentas
            WHERE 1=1";
    
    // Agregar filtro por empresa si se seleccionó una
    if ($id_empresa !== null && $id_empresa !== '') {
        $id_empresa = $conexion->real_escape_string($id_empresa);
        $sql .= " AND id_empresa = '$id_empresa'";
    }
    
    $sql .= " ORDER BY codigo_cuenta ASC";
    
    $resultado = $conexion->query($sql);
    
    if (!$resultado) {
        die("Error al obtener el catálogo: " . $conexion->error);
    }
    
    return $resultado;
}
?>