<?php
require_once 'conecxion_bd.php';
$id = $_GET['id'];
$resultado = $conexion->query("SELECT * FROM catalogo_cuentas WHERE id_cuenta = $id");
$cuenta = $resultado->fetch_assoc();
?>
<input type="hidden" name="id_cuenta" value="<?= $cuenta['id_cuenta'] ?>">

<label>Movimiento</label>
<select name="movimiento" class="form-control">
    <option value="1" <?= ($cuenta['permite_movimiento'] == 1) ? 'selected' : '' ?>>Sí</option>
    <option value="0" <?= ($cuenta['permite_movimiento'] == 0) ? 'selected' : '' ?>>No</option>
</select>

<label>Nivel</label>
<input type="number" name="nivel" class="form-control" value="<?= $cuenta['nivel'] ?>">