<?php
// BACKEND/consulta_factura.php
require_once 'conecxion_bd.php';

// ============================================================
// FUNCIÓN PARA OBTENER FACTURAS (con filtros)
// ============================================================
function obtenerLibroFacturas($conexion, $mes = null, $anio = null, $id_empresa = null, $id_proveedor = null) {
    // Si no se pasan mes y año, por defecto usamos el mes y año actuales
    if ($mes === null) $mes = date('m');
    if ($anio === null) $anio = date('Y');

    // Escapamos los datos por seguridad
    $mes = $conexion->real_escape_string($mes);
    $anio = $conexion->real_escape_string($anio);
    
    // Escapar filtros si vienen
    if ($id_empresa !== null && $id_empresa !== '') {
        $id_empresa = $conexion->real_escape_string($id_empresa);
    }
    if ($id_proveedor !== null && $id_proveedor !== '') {
        $id_proveedor = $conexion->real_escape_string($id_proveedor);
    }

    // ============================================================
    // CONSULTA PRINCIPAL
    // ============================================================
    $sql = "SELECT 
                f.*, 
                e.nombre_empresa, 
                e.rif,
                p.razon_social AS nombre_proveedor,
                p.rif AS rif_proveedor
            FROM facturas f 
            LEFT JOIN empresas_clientes e ON f.id_empresa = e.id_empresa 
            LEFT JOIN proveedores p ON f.id_tercero = p.id_proveedor 
            WHERE MONTH(f.fecha_documento) = '$mes' 
              AND YEAR(f.fecha_documento) = '$anio'";
    
    // ============================================================
    // AGREGAR FILTRO POR EMPRESA (para VENTAS)
    // ============================================================
    if ($id_empresa !== null && $id_empresa !== '') {
        $sql .= " AND f.id_empresa = '$id_empresa'";
    }
    
    // ============================================================
    // AGREGAR FILTRO POR PROVEEDOR (para COMPRAS)
    // ============================================================
    if ($id_proveedor !== null && $id_proveedor !== '') {
        $sql .= " AND f.id_tercero = '$id_proveedor'";
    }
    
    $sql .= " ORDER BY f.fecha_documento ASC, f.id_factura ASC";
            
    $resultado = $conexion->query($sql);
    return $resultado;
}



// ============================================================
// FUNCIÓN PARA OBTENER ESTADO DE PAGOS
// ============================================================
function obtenerEstadoPagos($conexion) {
    $sql = "SELECT 
                id_empresa,
                nombre_empresa,
                rif,
                servicio_activo,
                fecha_ultimo_pago,
                fecha_proximo_pago,
                monto_servicio,
                CASE 
                    WHEN servicio_activo = 1 AND (fecha_proximo_pago IS NULL OR fecha_proximo_pago >= CURDATE()) THEN 'pagado'
                    WHEN servicio_activo = 1 AND fecha_proximo_pago < CURDATE() THEN 'vencido'
                    ELSE 'pendiente'
                END as estado_pago
            FROM empresas_clientes 
            WHERE estado_activo = 1
            ORDER BY nombre_empresa ASC";
    
    $resultado = $conexion->query($sql);
    $empresas = [];
    while ($row = $resultado->fetch_assoc()) {
        $empresas[] = $row;
    }
    return $empresas;
}



?>