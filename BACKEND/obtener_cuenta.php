<?php
require_once 'conecxion_bd.php';
$id = $_GET['id'];
$resultado = $conexion->query("SELECT * FROM catalogo_cuentas WHERE id_cuenta = $id");
$cuenta = $resultado->fetch_assoc();
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
<div class="mb-3">
    <label>Tipo</label>
    <select name="tipo" class="form-control">
        <option value="Activo" <?= ($cuenta['tipo_cuenta']=='Activo')?'selected':'' ?>>Activo</option>
        <option value="Egreso" <?= ($cuenta['tipo_cuenta']=='Egreso')?'selected':'' ?>>Egreso</option>
        </select>
</div>