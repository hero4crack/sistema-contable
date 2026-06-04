<?php
// BACKEND/consulta_factura.php
require_once 'conecxion_bd.php';

// Función para listar empresas y llenar el "Select" del formulario
function obtenerEmpresasParaFactura($conexion) {
    $sql = "SELECT id_empresa, nombre_empresa FROM empresas_clientes WHERE estado_activo = '1'";
    $resultado = $conexion->query($sql);
    return $resultado;
}

// Función para listar las facturas de compras y ventas unificadas
function obtenerLibroFacturas($conexion) {
    // Cruzamos la tabla facturas con clientes y proveedores usando LEFT JOIN
    $sql = "SELECT f.*, e.nombre_empresa, p.razon_social AS nombre_proveedor
            FROM facturas f 
            LEFT JOIN empresas_clientes e ON f.id_empresa = e.id_empresa 
            LEFT JOIN proveedores p ON f.id_tercero = p.id_proveedor 
            ORDER BY f.fecha_documento DESC";
            
    $resultado = $conexion->query($sql);
    return $resultado;
}
?>