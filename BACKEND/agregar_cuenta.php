<?php
require_once 'conecxion_bd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = $_POST['codigo'];
    $nombre = $_POST['cuenta'];
    $nivel = $_POST['nivel'];
    $tipo = $_POST['tipo'];
    $movimiento = $_POST['movimiento'];
    
    $sql = "INSERT INTO catalogo_cuentas 
            (codigo_cuenta, nombre_cuenta, nivel, tipo_cuenta, permite_movimiento) 
            VALUES 
            ('$codigo', '$nombre', '$nivel', '$tipo', '$movimiento')";
    
    if ($conexion->query($sql)) {
        echo "<script>
            alert('✅ Cuenta creada exitosamente.');
            window.location.href = '../VIEWS/catalogo_cuenta.php';
        </script>";
    } else {
        echo "<script>
            alert('❌ Error al crear la cuenta: " . $conexion->error . "');
            window.history.back();
        </script>";
    }
}
?>