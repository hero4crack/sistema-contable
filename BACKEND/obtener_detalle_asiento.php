<?php
// Desactivar cualquier salida de error extraña que rompa el JSON
error_reporting(0); 
header('Content-Type: application/json');

require_once 'conecxion_bd.php'; // Verifica que se escriba así con 'xc'

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Seguridad: convertimos a número entero
    
    // Consulta para traer las cuentas, el debe y el haber de ese asiento
    $sql = "SELECT c.nombre_cuenta, d.debe, d.haber 
            FROM asiento_detalle d
            INNER JOIN catalogo_cuentas c ON d.id_cuenta = c.id_cuenta
            WHERE d.id_asiento = $id";
    
    $resultado = $conexion->query($sql);
    $detalles = [];
    
    if ($resultado) {
        while ($row = $resultado->fetch_assoc()) {
            $detalles[] = $row;
        }
    }
    
    // Enviamos los datos al JavaScript
    echo json_encode($detalles);
} else {
    echo json_encode(['error' => 'No se proporcionó ID']);
}
?>