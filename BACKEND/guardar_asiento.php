<?php
require_once 'conecxion_bd.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fecha = $_POST['fecha_asiento'];
    $comprobante = $_POST['nro_comprobante'];
    $glosa = $_POST['glosa'];
    $id_usuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 1;

    // 1. Insertar Encabezado
    $sql_cabecera = "INSERT INTO asiento_diario (nro_comprobante, fecha_asiento, glosa, id_usuario) 
                        VALUES ('$comprobante', '$fecha', '$glosa', '$id_usuario')";

    if ($conexion->query($sql_cabecera)) {
        $id_asiento = $conexion->insert_id;

        // 2. Capturar arrays del formulario (CORREGIDO: 'cuentas')
        $cuentas = isset($_POST['cuentas']) ? $_POST['cuentas'] : [];
        $debes = isset($_POST['debe']) ? $_POST['debe'] : [];
        $haberes = isset($_POST['haber']) ? $_POST['haber'] : [];

 //3. Insertar Detalle
if (count($cuentas) > 0) {
    // AQUÍ FALTABA EL INICIO DEL BUCLE FOR
    for ($i = 0; $i < count($cuentas); $i++) {
        $id_cta = $cuentas[$i];
        $debe = empty($debes[$i]) ? 0 : $debes[$i];
        $haber = empty($haberes[$i]) ? 0 : $haberes[$i];

        if (!empty($id_cta)) {
            $sql_detalle = "INSERT INTO asiento_detalle (id_asiento, id_cuenta, debe, haber) 
                            VALUES ('$id_asiento', '$id_cta', '$debe', '$haber')";
            $conexion->query($sql_detalle);
        }
    } // ESTA LLAVE CIERRA EL FOR
} // ESTA LLAVE CIERRA EL IF (count...)

header("Location: ../VIEWS/asientos_diario.php?success=1");
    } else {
        echo "Error al guardar: " . $conexion->error;
    }
}
?>