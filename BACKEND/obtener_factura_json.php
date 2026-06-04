<?php
// BACKEND/obtener_factura_json.php
header('Content-Type: application/json; charset=utf-8');
require_once 'conecxion_bd.php';

// Validar que se reciba el ID por la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No se especificó un ID de factura válido.'
    ]);
    exit();
}

$id_factura = intval($_GET['id']);

// Consulta limpia para extraer los datos de la factura incluyendo la nueva columna
$sql = "SELECT id_factura, id_empresa, id_tercero, tipo_transaccion, nro_factura, nro_control, nro_comprobante_retencion,
               fecha_documento, base_imponible, monto_exento, alicuota_iva, monto_iva, total_factura 
        FROM facturas 
        WHERE id_factura = $id_factura LIMIT 1";

$resultado = $conexion->query($sql);

if ($resultado && $f = $resultado->fetch_assoc()) {
    echo json_encode([
        'status' => 'success',
        'data' => [
            'id_factura'                => $f['id_factura'],
            'id_empresa'                => $f['id_empresa'],
            'id_tercero'                => $f['id_tercero'],
            'tipo_transaccion'          => $f['tipo_transaccion'],
            'nro_factura'               => $f['nro_factura'],
            'nro_control'               => $f['nro_control'],
            'nro_comprobante_retencion' => $f['nro_comprobante_retencion'], // Retorna el valor o null de forma limpia
            'fecha_documento'           => $f['fecha_documento'],
            'base_imponible'            => floatval($f['base_imponible']),
            'monto_exento'              => floatval($f['monto_exento']),
            'alicuota_iva'              => floatval($f['alicuota_iva']),
            'monto_iva'                 => floatval($f['monto_iva']),
            'total_factura'             => floatval($f['total_factura'])
        ]
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'La factura solicitada no existe o fue eliminada.'
    ]);
}

$conexion->close();
?>