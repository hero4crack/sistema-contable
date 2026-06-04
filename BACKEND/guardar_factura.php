<?php
// BACKEND/guardar_factura.php
require_once 'conecxion_bd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitizar y capturar campos comunes
    $tipo_transaccion = $conexion->real_escape_string($_POST['tipo_transaccion']);
    $nro_factura      = $conexion->real_escape_string($_POST['nro_factura']);
    $nro_control      = $conexion->real_escape_string($_POST['nro_control']);
    $base_imponible   = floatval($_POST['base_imponible']);
    $monto_exento     = floatval($_POST['monto_exento']);
    
    // Inicializar variables de relación en la BD
    $id_empresa = "NULL";
    $id_tercero = "NULL";

    // Evaluar la lógica según lo que dictó el contador
    if ($tipo_transaccion === 'COMPRA') {
        // Si es compra, el tercero es el proveedor
        $id_tercero = intval($_POST['id_proveedor']);
    } else {
        // Si es venta, se asocia a la empresa cliente
        $id_empresa = intval($_POST['id_empresa']);
    }
    
    // Cálculos de ingeniería fiscal en el servidor
    $alicuota = 16.00; // 16% estándar de IVA
    $monto_iva = $base_imponible * ($alicuota / 100);
    $total_factura = $base_imponible + $monto_exento + $monto_iva;
    
    $fecha_registro = date("Y-m-d");

    // Preparamos la consulta SQL incluyendo tipo_transaccion e id_tercero
    $sql = "INSERT INTO facturas (id_empresa, id_tercero, tipo_transaccion, nro_factura, nro_control, 
                                  fecha_documento, base_imponible, monto_exento, alicuota_iva, monto_iva, total_factura) 
            VALUES (" . ($id_empresa !== "NULL" ? "'$id_empresa'" : "NULL") . ", 
                    " . ($id_tercero !== "NULL" ? "'$id_tercero'" : "NULL") . ", 
                    '$tipo_transaccion', '$nro_factura', '$nro_control', '$fecha_registro', 
                    '$base_imponible', '$monto_exento', '$alicuota', '$monto_iva', '$total_factura')";

    // Ejecutamos la consulta
    if ($conexion->query($sql) === TRUE) {
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