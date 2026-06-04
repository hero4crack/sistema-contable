<?php
// BACKEND/guardar_factura.php
require_once 'conecxion_bd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Sanitizar y capturar campos comunes desde el formulario
    $tipo_transaccion = $conexion->real_escape_string($_POST['tipo_transaccion']);
    $nro_factura      = $conexion->real_escape_string($_POST['nro_factura']);
    $nro_control      = $conexion->real_escape_string($_POST['nro_control']);
    $base_imponible   = floatval($_POST['base_imponible']);
    $monto_exento     = floatval($_POST['monto_exento']);
    
    // CAPTURA CORRECTA: Usamos la fecha real de la factura enviada desde el modal
    $fecha_documento  = $conexion->real_escape_string($_POST['fecha_documento']);
    
    // Inicializar variables de relación y fiscales
    $id_empresa = "NULL";
    $id_tercero = "NULL";
    $porcentaje_retencion = 0;
    $monto_retenido = 0.00;

    // 2. Evaluar lógica de Terceros y obtener Retenciones Fiscales
    if ($tipo_transaccion === 'COMPRA') {
        $id_tercero = intval($_POST['id_proveedor']);
        
        // FISCAL: Buscamos el % de retención configurado en el expediente de este proveedor
        $query_prov = $conexion->query("SELECT porcentaje_retencion FROM proveedores WHERE id_proveedor = $id_tercero LIMIT 1");
        if ($query_prov && $prov = $query_prov->fetch_assoc()) {
            $porcentaje_retencion = intval($prov['porcentaje_retencion']);
        }
    } else {
        // Si es venta, se asocia a la empresa cliente
        $id_empresa = intval($_POST['id_empresa']);
    }
    
    // 3. Cálculos de ingeniería fiscal en el servidor
    $alicuota = 16.00; // 16% estándar de IVA (Venezuela)
    $monto_iva = $base_imponible * ($alicuota / 100);
    
    // Si es compra, calculamos el dinero que le retenemos al proveedor
    if ($tipo_transaccion === 'COMPRA' && $porcentaje_retencion > 0) {
        $monto_retenido = $monto_iva * ($porcentaje_retencion / 100);
    }
    
    // El total de la factura sigue siendo la suma de sus componentes fiscales
    $total_factura = $base_imponible + $monto_exento + $monto_iva;

    // LÓGICA DE LA COLUMNA COMPARTIDA REQUERIDA POR TU BASE DE DATOS:
    // Si es COMPRA guarda la retención de IVA calculada; si es VENTA guarda el monto exento original
    $valor_exento = ($tipo_transaccion === 'COMPRA') ? $monto_retenido : $monto_exento;

    // 4. Preparar la consulta SQL definitiva apuntando a tu columna 'monto_exento'
    $sql = "INSERT INTO facturas (id_empresa, id_tercero, tipo_transaccion, nro_factura, nro_control, 
                                  fecha_documento, base_imponible, monto_exento, alicuota_iva, monto_iva, total_factura) 
            VALUES (" . ($id_empresa !== "NULL" ? "'$id_empresa'" : "NULL") . ", 
                    " . ($id_tercero !== "NULL" ? "'$id_tercero'" : "NULL") . ", 
                    '$tipo_transaccion', '$nro_factura', '$nro_control', '$fecha_documento', 
                    '$base_imponible', '$valor_exento', '$alicuota', '$monto_iva', '$total_factura')";

    // 5. Ejecutamos la transacción
    if ($conexion->query($sql) === TRUE) {
        
        // =========================================================================
        // ESPACIO RESERVADO: AQUÍ IRA LA FUNCIÓN DE ASIENTO DIARIO AUTOMÁTICO
        // generarAsientoContable($conexion, $tipo_transaccion, $base_imponible, $monto_iva, $monto_retenido, $total_factura);
        // =========================================================================

        header("Location: ../VIEWS/libro_facturas.php?status=success");
        exit();
    } else {
        echo "Error crítico al registrar en el libro fiscal: " . $conexion->error;
    }

} else {
    header("Location: ../VIEWS/libro_facturas.php");
    exit();
}

$conexion->close();
?>