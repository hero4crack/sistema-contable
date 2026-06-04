<?php
// BACKEND/editar_proveedor.php
session_start();
require_once 'conecxion_bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitización y captura de datos de entrada
    $id_proveedor = intval($_POST['id_proveedor']);
    $rif = $conexion->real_escape_string(trim($_POST['rif']));
    $razon_social = $conexion->real_escape_string(trim($_POST['razon_social']));
    $nombre_comercial = $conexion->real_escape_string(trim($_POST['nombre_comercial']));
    $tipo_contribuyente = $conexion->real_escape_string($_POST['tipo_contribuyente']);
    $porcentaje_retencion = intval($_POST['porcentaje_retencion']);
    $direccion_fiscal = $conexion->real_escape_string(trim($_POST['direccion_fiscal']));
    $telefono = $conexion->real_escape_string(trim($_POST['telefono']));
    $correo_electronico = $conexion->real_escape_string(trim($_POST['correo_electronico']));
    $nombre_contacto = $conexion->real_escape_string(trim($_POST['nombre_contacto']));
    $telefono_contacto = $conexion->real_escape_string(trim($_POST['telefono_contacto']));

    // Validación básica obligatoria
    if (empty($rif) || empty($razon_social) || empty($direccion_fiscal) || $id_proveedor <= 0) {
        $_SESSION['msg_auditoria'] = "Error: Faltan campos obligatorios para actualizar el proveedor.";
        $_SESSION['msg_tipo'] = "danger";
        header("Location: ../VIEWS/registro_proveedor.php");
        exit();
    }

    // Consulta SQL de actualización
    $sql = "UPDATE proveedores SET 
                rif = '$rif', 
                razon_social = '$razon_social', 
                nombre_comercial = '$nombre_comercial', 
                tipo_contribuyente = '$tipo_contribuyente', 
                porcentaje_retencion ='$porcentaje_retencion', 
                direccion_fiscal = '$direccion_fiscal', 
                telefono = '$telefono', 
                correo_electronico = '$correo_electronico', 
                nombre_contacto = '$nombre_contacto', 
                telefono_contacto = '$telefono_contacto' 
            WHERE id_proveedor = $id_proveedor";

    if ($conexion->query($sql)) {
        $_SESSION['msg_auditoria'] = "El proveedor [RIF: $rif] ha sido actualizado exitosamente.";
        $_SESSION['msg_tipo'] = "success";
    } else {
        $_SESSION['msg_auditoria'] = "Error al actualizar la base de datos: " . $conexion->error;
        $_SESSION['msg_tipo'] = "danger";
    }

    $conexion->close();
    header("Location: ../VIEWS/registro_proveedor.php");
    exit();
} else {
    header("Location: ../VIEWS/registro_proveedor.php");
    exit();
}
?>