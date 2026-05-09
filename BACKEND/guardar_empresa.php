<?php
require_once 'conecxion_bd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Capturamos los datos de la empresa
    $nombre = $_POST['empresa'];
    // Unimos la letra (V-, J-, etc) con el número de RIF
    $rif = $_POST['letra'] . $_POST['rif']; 
    $razon_social = $_POST['social'];
    $contribuyente = $_POST['contribuyente'];
    $direccion = $_POST['direccion'];
    $telefono_emp = $_POST['telefono'];
    $correo_emp = $_POST['correo'];
    $pais = $_POST['pais'];
    $estado = $_POST['estado'];

    // 2. Capturamos los datos del responsable (Sugerencia Prof.)
    $n_resp = $_POST['nombre_responsable'];
    $c_resp = $_POST['cedula_responsable'];
    $t_resp = $_POST['telefono_responsable'];
    $m_resp = $_POST['correo_responsable'];

    // 3. Preparamos el SQL con los nombres EXACTOS de tu tabla
    $sql = "INSERT INTO empresas_clientes (
                nombre_empresa, rif, razon_social, tipo_contribuyente, 
                direccion_fiscal, telefono, correo_electronico, pais, 
                nombre_responsable, cedula_responsable, telefono_responsable, correo_responsable,
                estado_activo
            ) VALUES (
                '$nombre', '$rif', '$razon_social', '$contribuyente', 
                '$direccion', '$telefono_emp', '$correo_emp', '$pais',
                '$n_resp', '$c_resp', '$t_resp', '$m_resp', '$estado'
            )";

    if ($conexion->query($sql)) {
        // Si tiene éxito, regresamos a la vista
        header("Location: ../VIEWS/empresas_clientes.php?success=1");
    } else {
        // Si hay error, lo mostramos para depurar
        echo "Error técnico: " . $conexion->error;
    }
}
?>