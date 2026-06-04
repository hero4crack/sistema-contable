<?php
require_once('conecxion_bd.php'); 

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar las entradas del formulario
    $nombre_comercial     = $conexion->real_escape_string($_POST['nombre_comercial']);
    $razon_social         = $conexion->real_escape_string($_POST['razon_social']);
    $rif                  = strtoupper($conexion->real_escape_string($_POST['rif']));
    $direccion_fiscal     = $conexion->real_escape_string($_POST['direccion_fiscal']);
    $telefono             = $conexion->real_escape_string($_POST['telefono']);
    $correo_electronico   = $conexion->real_escape_string($_POST['correo_electronico']);
    $tipo_contribuyente   = $conexion->real_escape_string($_POST['tipo_contribuyente']);
    $porcentaje_retencion = $conexion->real_escape_string($_POST['porcentaje_retencion']);
    $nombre_contacto      = $conexion->real_escape_string($_POST['nombre_contacto']);
    $telefono_contacto    = $conexion->real_escape_string($_POST['telefono_contacto']);

    // Validar RIF único
    $checkRif = $conexion->query("SELECT id_proveedor FROM proveedores WHERE rif = '$rif'");
    
    if ($checkRif->num_rows > 0) {
        $_SESSION['msg_auditoria'] = "El RIF ya se encuentra registrado.";
        $_SESSION['msg_tipo'] = "danger";
    } else {
        // Insertar registro
        $sql = "INSERT INTO proveedores (nombre_comercial, razon_social, rif, direccion_fiscal, telefono, correo_electronico, tipo_contribuyente, porcentaje_retencion, nombre_contacto, telefono_contacto) 
                VALUES ('$nombre_comercial', '$razon_social', '$rif', '$direccion_fiscal', '$telefono', '$correo_electronico', '$tipo_contribuyente', '$porcentaje_retencion', '$nombre_contacto', '$telefono_contacto')";
        
        if ($conexion->query($sql)) {
            $_SESSION['msg_auditoria'] = "Proveedor registrado exitosamente.";
            $_SESSION['msg_tipo'] = "success";
        } else {
            $_SESSION['msg_auditoria'] = "Error en la base de datos: " . $conexion->error;
            $_SESSION['msg_tipo'] = "danger";
        }
    }
    
    // Redirigir de vuelta a la vista del formulario
    header("Location: ../VIEWS/registro_proveedor.php");
    exit();
}
?>