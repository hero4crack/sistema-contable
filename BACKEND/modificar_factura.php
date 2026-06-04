<?php
// BACKEND/modificar_factura.php
require_once 'conecxion_bd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Validar que se reciba el ID de la factura a modificar
    if (!isset($_POST['id_factura']) || empty($_POST['id_factura'])) {
        echo "Error crítico: ID de factura no especificado para la actualización.";
        exit();
    }
    
    $id_factura = intval($_POST['id_factura']);
    
    // 2. Sanitizar y capturar campos desde el formulario
    $tipo_transaccion = $conexion->real_escape_string($_POST['tipo_transaccion']);
    $nro_factura      = $conexion->real_escape_string($_POST['nro_factura']);
    $nro_control      = $conexion->real_escape_string($_POST['nro_control']);
    $base_imponible   = floatval($_POST['base_imponible']);
    $monto_exento     = floatval($_POST['monto_exento']);
    $fecha_documento  = $conexion->real_escape_string($_POST['fecha_documento']);
    
    // NUEVO: Capturar y sanitizar el número de comprobante de retención al editar
    $nro_comprobante_retencion = isset($_POST['nro_comprobante_retencion']) ? $conexion->real_escape_string(trim($_POST['nro_comprobante_retencion'])) : '';

    // Inicializar variables de relación y fiscales
    $id_empresa = "NULL";
    $id_tercero = "NULL";
    $porcentaje_retencion = 0;
    $monto_retenido = 0.00;

    // 3. Evaluar lógica de Terceros y obtener Retenciones Fiscales actuales
    if ($tipo_transaccion === 'COMPRA') {
        $id_tercero = intval($_POST['id_proveedor']);
        
        // FISCAL: Buscamos el % de retención configurado en el expediente de este proveedor
        $query_prov = $conexion->query("SELECT porcentaje_retencion FROM proveedores WHERE id_proveedor = $id_tercero LIMIT 1");
        if ($query_prov && $prov = $query_prov->fetch_assoc()) {
            $porcentaje_retencion = intval($prov['porcentaje_retencion']);
        }
    } else {
        // Si es venta, se asocia a la empresa cliente y limpiamos la retención
        $id_empresa = intval($_POST['id_empresa']);
        $nro_comprobante_retencion = '';
    }
    
    // Asignar el valor final para la consulta SQL
    $val_comprobante = (!empty($nro_comprobante_retencion)) ? "'$nro_comprobante_retencion'" : "NULL";
    
    // 4. Recálculos de ingeniería fiscal en el servidor (Garantiza consistencia matemática)
    $alicuota = 16.00; // 16% estándar de IVA (Venezuela)
    $monto_iva = $base_imponible * ($alicuota / 100);
    
    // Si es compra, recalculamos el dinero que le retenemos al proveedor
    if ($tipo_transaccion === 'COMPRA' && $porcentaje_retencion > 0) {
        $monto_retenido = $monto_iva * ($porcentaje_retencion / 100);
    }
    
    // El total de la factura sigue siendo la suma de sus componentes fiscales
    $total_factura = $base_imponible + $monto_exento + $monto_iva;

    // APLICACIÓN DE LA COLUMNA COMPARTIDA:
    // Si es COMPRA guarda la retención de IVA calculada; si es VENTA guarda el monto exento original
    $valor_exento = ($tipo_transaccion === 'COMPRA') ? $monto_retenido : $monto_exento;

    // 5. Preparar la consulta SQL de actualización (UPDATE) incluyendo el comprobante
    $sql = "UPDATE facturas SET 
                id_empresa = " . ($id_empresa !== "NULL" ? "'$id_empresa'" : "NULL") . ", 
                id_tercero = " . ($id_tercero !== "NULL" ? "'$id_tercero'" : "NULL") . ", 
                tipo_transaccion = '$tipo_transaccion', 
                nro_factura = '$nro_factura', 
                nro_control = '$nro_control', 
                nro_comprobante_retencion = $val_comprobante,
                fecha_documento = '$fecha_documento', 
                base_imponible = '$base_imponible', 
                monto_exento = '$valor_exento', 
                alicuota_iva = '$alicuota', 
                monto_iva = '$monto_iva', 
                total_factura = '$total_factura' 
            WHERE id_factura = $id_factura";

    // 6. Ejecutamos la consulta
    if ($conexion->query($sql) === TRUE) {
        
        // =========================================================================
        // ESPACIO RESERVADO: AQUÍ SE ACTUALIZARÁ EL ASIENTO DIARIO AUTOMÁTICO
        // actualizarAsientoContable($conexion, $id_factura, $tipo_transaccion, $base_imponible, $monto_iva, $monto_retenido, $total_factura);
        // =========================================================================

        header("Location: ../VIEWS/libro_facturas.php?status=success");
        exit();
    } else {
        echo "Error crítico al actualizar el registro en el libro fiscal: " . $conexion->error;
    }

} else {
    header("Location: ../VIEWS/libro_facturas.php");
    exit();
}

$conexion->close();
?>