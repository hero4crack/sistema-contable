<?php
require_once 'conecxion_bd.php';

// 2. Verificamos que los datos vengan por el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Capturamos los datos del formulario (Modal)
    $id_empresa    = $_POST['id_empresa'];
    $nro_factura   = $_POST['nro_factura'];
    $nro_control   = $_POST['nro_control'];
    $base_imponible = $_POST['base_imponible'];
    $monto_exento  = $_POST['monto_exento'];
    
    // Realizamos los cálculos de ingeniería en el servidor (por seguridad)
    $alicuota = 16.00; // 16% estándar
    $monto_iva = $base_imponible * ($alicuota / 100);
    $total_factura = $base_imponible + $monto_exento + $monto_iva;
    
    // Definimos una fecha por defecto (hoy) o puedes capturarla del formulario
    $fecha_registro = date("Y-m-d");

    // 3. Preparamos la consulta SQL de inserción
    // Asegúrate de que los nombres de las columnas coincidan con tu tabla 'facturas'
    $sql = "INSERT INTO facturas (id_empresa, nro_factura, nro_control, fecha_documento, 
                                  base_imponible, monto_exento, alicuota_iva, monto_iva, total_factura) 
            VALUES ('$id_empresa', '$nro_factura', '$nro_control', '$fecha_registro', 
                    '$base_imponible', '$monto_exento', '$alicuota', '$monto_iva', '$total_factura')";

    // 4. Ejecutamos la consulta
    if ($conexion->query($sql) === TRUE) {
        // Si todo sale bien, redirigimos de vuelta al libro con un mensaje de éxito
        header("Location: ../VIEWS/libro_factura.php");
    } else {
        // Si hay un error, lo mostramos
        echo "Error al registrar: " . $conexion->error;
    }

} else {
    // Si alguien intenta entrar al archivo sin enviar el formulario
    header("Location: ../VIEWS/libro_factura.php");
}

$conexion->close();
?>