<?php
// 1. Incluir la conexión ANTES de usarla
require_once '../BACKEND/conecxion_bd.php';

// 2. Verificar que recibimos un ID
if (!isset($_GET['id'])) {
    echo "ID no especificado.";
    exit;
}

$id = $_GET['id'];

// 3. Ejecutar la consulta para obtener los datos
$sql = "SELECT * FROM catalogo_cuentas WHERE id_cuenta = $id";
$resultado = $conexion->query($sql);

// 4. Verificar si la consulta encontró la cuenta
if ($resultado && $resultado->num_rows > 0) {
    $cuenta = $resultado->fetch_assoc();
} else {
    echo "Cuenta no encontrada.";
    exit;
}
?>

<input type="hidden" name="id_cuenta" value="<?= $cuenta['id_cuenta'] ?>">

<div class="mb-3">
    <label>Código</label>
    <input type="text" name="codigo_cuenta" class="form-control" value="<?= $cuenta['codigo_cuenta'] ?>">
</div>

<div class="mb-3">
    <label>Nombre de la Cuenta</label>
    <input type="text" name="nombre_cuenta" class="form-control" value="<?= $cuenta['nombre_cuenta'] ?>">
</div>