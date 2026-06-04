<?php
// BACKEND/obtener_proveedor_json.php
require_once 'conecxion_bd.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM proveedores WHERE id_proveedor = $id LIMIT 1";
    $resultado = $conexion->query($sql);
    
    if ($resultado && $resultado->num_rows > 0) {
        $proveedor = $resultado->fetch_assoc();
        echo json_encode(['status' => 'success', 'data' => $proveedor]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Proveedor no encontrado.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado.']);
}
$conexion->close();
?>