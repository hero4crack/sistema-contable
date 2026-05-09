<?php
require_once 'conecxion_bd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $empresa = $_POST['empresa'];
    $rif = $_POST['rif'];
    $social = $_POST['social'];
    $contribuyente = $_POST['contribuyente'];
    $direccion = $_POST['direccion'];
    $estado = $_POST['estado'];

    // Estos nombres deben coincidir con el atributo 'name' de los inputs del modal
    $n_resp = $_POST['nombre_responsable'];
    $c_resp = $_POST['cedula_responsable'];
    $t_resp = $_POST['telefono_responsable'];
    $m_resp = $_POST['correo_responsable'];

    $sql = "UPDATE empresas_clientes SET 
                nombre_empresa = '$empresa',
                rif = '$rif',
                razon_social = '$social',
                tipo_contribuyente = '$contribuyente',
                direccion_fiscal = '$direccion',
                nombre_responsable = '$n_resp',
                cedula_responsable = '$c_resp',
                telefono_responsable = '$t_resp',
                correo_responsable = '$m_resp',
                estado_activo = '$estado'
            WHERE id_empresa = '$id'";

    if ($conexion->query($sql)) {
        header("Location: ../VIEWS/empresas_clientes.php?update=success");
    } else {
        echo "Error al actualizar: " . $conexion->error;
    }
}
?>