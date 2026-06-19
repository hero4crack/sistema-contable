<?php
require_once 'conecxion_bd.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "SELECT * FROM facturas WHERE id_factura = '$id'";
    $resultado = mysqli_query($conexion, $sql);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $factura = mysqli_fetch_assoc($resultado);
        echo json_encode([
            'success' => true,
            'factura' => $factura
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Factura no encontrada'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID de factura no proporcionado'
    ]);
}
?>