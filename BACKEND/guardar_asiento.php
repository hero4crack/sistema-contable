<?php
require_once 'conecxion_bd.php';
session_start(); // 1. IMPORTANTE: Iniciar sesión para saber quién es el usuario

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fecha = $_POST['fecha_asiento'];
    $comprobante = $_POST['nro_comprobante'];
    $glosa = $_POST['glosa'];
    
    // 2. Obtenemos el ID del usuario de la sesión. 
    // Si no existe, usamos el ID 1 por defecto para que no falle mientras pruebas.
    $id_usuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 1; 
    
    // 3. Insertar el encabezado INCLUYENDO el id_usuario
    $sql_cabecera = "INSERT INTO asiento_diario (nro_comprobante, fecha_asiento, glosa, id_usuario) 
                     VALUES ('$comprobante', '$fecha', '$glosa', '$id_usuario')";
    
    if ($conexion->query($sql_cabecera)) {
        $id_asiento = $conexion->insert_id; 
        
        $cuentas = $_POST['id_cuenta'];
        $debes = $_POST['debe'];
        $haberes = $_POST['haber'];

        for ($i = 0; $i < count($cuentas); $i++) {
            $id_cta = $cuentas[$i];
            $debe = !empty($debes[$i]) ? $debes[$i] : 0;
            $haber = !empty($haberes[$i]) ? $haberes[$i] : 0;

            $sql_detalle = "INSERT INTO asiento_detalle (id_asiento, id_cuenta, debe, haber) 
                            VALUES ('$id_asiento', '$id_cta', '$debe', '$haber')";
            $conexion->query($sql_detalle);
        }
        
        echo "<script>alert('Asiento guardado con éxito'); window.location='../VIEWS/asientos_diario.php';</script>";
    } else {
        // Esto te dirá exactamente qué campo falta si vuelve a fallar
        echo "Error en la base de datos: " . $conexion->error;
    }
}
?>