<?php
require_once 'conecxion_bd.php';

// Función para listar empresas y llenar el "Select" del formulario
function obtenerEmpresasParaFactura($conexion) {
    $sql = "SELECT id_empresa, nombre_empresa FROM empresas_clientes WHERE estado_activo = '1'";
    $resultado = $conexion->query($sql);
    return $resultado;
}

// Función para listar las facturas ya registradas en el Libro
function obtenerLibroFacturas($conexion) {
    $sql = "SELECT f.*, e.nombre_empresa 
            FROM facturas f 
            INNER JOIN empresas_clientes e ON f.id_empresa = e.id_empresa 
            ORDER BY f.fecha_documento DESC";
    $resultado = $conexion->query($sql);
    return $resultado;
}
?>