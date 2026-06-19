<?php
require_once 'conecxion_bd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_empresa = $_POST['id_empresa'];
    $monto = $_POST['monto_servicio'];
    $fecha_pago = $_POST['fecha_pago'];
    
    // Calcular próximo pago (1 mes después)
    $fecha_proximo = date('Y-m-d', strtotime($fecha_pago . ' +1 month'));
    
    $sql = "UPDATE empresas_clientes 
            SET servicio_activo = 1,
                fecha_ultimo_pago = '$fecha_pago',
                fecha_proximo_pago = '$fecha_proximo',
                monto_servicio = '$monto'
            WHERE id_empresa = '$id_empresa'";
    
    if ($conexion->query($sql)) {
        echo "<script>
            alert('✅ Pago registrado exitosamente.');
            window.location.href = '../VIEWS/inicio.php';
        </script>";
    } else {
        echo "<script>
            alert('❌ Error al registrar el pago: " . $conexion->error . "');
            window.location.href = '../VIEWS/inicio.php';
        </script>";
    }
}
?>