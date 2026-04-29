<?php

include_once('conecxion_bd.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar los datos del formulario
    $empresa = $_POST['empresa'];
    $rif = $_POST['rif'];
    $social = $_POST['social'];
    $contribuyente = $_POST['contribuyente'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $pais = $_POST['pais'];
    $estatus = $_POST['estatus'];



    try {
        $sql = "INSERT INTO empresas_clientes (nombre_empresa, rif, razon_social, direccion_fiscal, telefono, correo_electronico, tipo_contribuyente, estado_activo, pais) 
                VALUES ('$empresa', '$rif', '$social','$direccion', '$telefono', '$correo','$contribuyente','$estatus' ,'$pais')";

        $sql1 = "SELECT * FROM empresas_clientes WHERE nombre_empresa = '$empresa' AND rif = '$rif'";
        $resultado = $conexion->query($sql1);
        $row = $resultado->fetch_assoc();



        if ($conexion->query($sql) === TRUE) {

            echo "<script type='text/javascript'>";
            echo "alert('Responsable ingresado con exito');";
            echo "window.location.href = '../VIEWS/empresas_clientes.php';";
            echo "</script>";
        } else {
            echo "Error al registrar el componente: " . $conexion->error;
        }
    } catch (mysqli_sql_exception $e) {

        $conexion->rollback();

        if ($e->getCode() == 1062) {
            echo "<script type='text/javascript'>";
            echo "alert('Responsable duplicado');";
            echo "window.location.href = '../VIEWS/empresas_clientes.php';";
            echo "</script>";
        } else {
            throw ($e);
        }
    }
}
