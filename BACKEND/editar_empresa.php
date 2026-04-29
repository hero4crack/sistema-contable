<?php
include_once('conecxion_bd.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $empresa = $_POST['empresa'];
    $rif = $_POST['rif'];
    $social = $_POST['social'];
    $contribuyente = $_POST['contribuyente'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $pais = $_POST['pais'];
    $estado = $_POST['estado'];

    $sql = "UPDATE empresas_clientes
            SET 
            nombre_empresa = '" . $empresa . "',
            rif = '" . $rif . "',
            razon_social = '" . $social . "',
            direccion_fiscal = '" . $direccion . "',
            telefono = '" . $telefono . "',
            correo_electronico = '" . $correo . "',
            tipo_contribuyente = '" . $contribuyente . "',
            estado_activo = '" . $estado . "',
            pais = '" . $pais . "'
            WHERE id_empresa = '" . $id ."'";

    if ($conexion->query($sql) === TRUE) {

        echo "<script type='text/javascript'>";
        echo "alert('Se edito exitosamente');";
        echo "window.location.href = '../VIEWS/empresas_clientes.php';";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('Error en los datos');";
        echo "window.location.href = '../VIEWS/empresas_clientes.php';";
        echo "</script>";
        echo "Error al actualizar: " . $conexion->error;
    }
} else {
    echo "Método de solicitud no válido.";
}
