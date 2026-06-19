<?php
require_once 'conecxion_bd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_factura = $_POST['id_factura'];
    $fecha_documento = $_POST['fecha_documento'];
    $tipo_transaccion = $_POST['tipo_transaccion'];
    $nro_factura = $_POST['nro_factura'];
    $nro_control = $_POST['nro_control'];
    $base_imponible = $_POST['base_imponible'];
    $monto_exento = $_POST['monto_exento'];
    $monto_iva = $_POST['monto_iva'];
    $total_factura = $_POST['total_factura'];
    $nro_comprobante_retencion = $_POST['nro_comprobante_retencion'] ?? null;
    
    // Iniciar la consulta base
    $sql = "UPDATE facturas SET 
                fecha_documento = '$fecha_documento',
                tipo_transaccion = '$tipo_transaccion',
                nro_factura = '$nro_factura',
                nro_control = '$nro_control',
                base_imponible = '$base_imponible',
                monto_exento = '$monto_exento',
                monto_iva = '$monto_iva',
                total_factura = '$total_factura'";
    
    // Agregar campos específicos según tipo
    if ($tipo_transaccion == 'VENTA') {
        $id_empresa = $_POST['id_empresa'];
        $sql .= ", id_empresa = '$id_empresa'";
    } else {
        $id_proveedor = $_POST['id_proveedor'];
        $sql .= ", id_tercero = '$id_proveedor'";
        $sql .= ", nro_comprobante_retencion = " . ($nro_comprobante_retencion ? "'$nro_comprobante_retencion'" : "NULL");
    }
    
    $sql .= " WHERE id_factura = '$id_factura'";
    
    if (mysqli_query($conexion, $sql)) {
        echo "<script>
            alert('✅ Factura actualizada exitosamente.');
            window.location.href = '../VIEWS/libro_facturas.php?status=updated';
        </script>";
    } else {
        echo "<script>
            alert('❌ Error al actualizar la factura: " . mysqli_error($conexion) . "');
            window.history.back();
        </script>";
    }
}
?>