<?php
require_once 'conecxion_bd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. CAPTURA DE DATOS FISCALES
    $nombre_f = $_POST['empresa'];
    // Concatenamos la letra del RIF con el número ingresado
    $rif_completo = $_POST['letra'] . $_POST['rif']; 
    $razon_social = $_POST['social'];
    $tipo_contrib = $_POST['contribuyente'];
    $dir_fiscal = $_POST['direccion'];
    $telf_emp = $_POST['telefono'];
    $correo_emp = $_POST['correo'];
    $pais = $_POST['pais'];

    // 2. CAPTURA DE DATOS DEL RESPONSABLE (Evaluación Prof.)
    $n_resp = $_POST['nombre_responsable'];
    $c_resp = $_POST['cedula_responsable'];
    $t_resp = $_POST['telefono_responsable'];
    $m_resp = $_POST['correo_responsable'];
    $estado = $_POST['estado'];

    // 3. SENTENCIA SQL (Asegúrate de que los nombres coincidan con tu tabla)
    $sql = "INSERT INTO empresas_clientes (
                nombre_empresa, 
                rif, 
                razon_social, 
                tipo_contribuyente, 
                direccion_fiscal, 
                telefono, 
                correo_electronico, 
                pais, 
                nombre_responsable, 
                cedula_responsable, 
                telefono_responsable, 
                correo_responsable, 
                estado_activo
            ) VALUES (
                '$nombre_f', 
                '$rif_completo', 
                '$razon_social', 
                '$tipo_contrib', 
                '$dir_fiscal', 
                '$telf_emp', 
                '$correo_emp', 
                '$pais', 
                '$n_resp', 
                '$c_resp', 
                '$t_resp', 
                '$m_resp', 
                '$estado'
            )";

    // 4. EJECUCIÓN Y REDIRECCIÓN
    if ($conexion->query($sql)) {
        // Redirige de vuelta con un mensaje de éxito
        header("Location: ../VIEWS/empresas_clientes.php?status=success");
    } else {
        // En caso de error, lo muestra para depuración técnica
        echo "Error en el registro: " . $conexion->error;
    }
}

$conexion->close();
?>