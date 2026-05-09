<?php
require_once 'conecxion_bd.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convertimos a entero por seguridad

    // Iniciamos una transacción para asegurar que se borre todo o nada
    $conexion->begin_transaction();

    try {
        // 1. Borrar los detalles del asiento primero (por la llave foránea)
        $sql_detalle = "DELETE FROM asiento_detalle WHERE id_asiento = $id";
        $conexion->query($sql_detalle);

        // 2. Borrar el encabezado del asiento
        $sql_cabecera = "DELETE FROM asiento_diario WHERE id_asiento = $id";
        $conexion->query($sql_cabecera);

        // Si todo salió bien, confirmamos los cambios
        $conexion->commit();
        
        // Redirigimos con un mensaje de éxito
        header("Location: ../VIEWS/asientos_diario.php?deleted=1");
    } catch (Exception $e) {
        // Si algo falla, deshacemos cualquier cambio
        $conexion->rollback();
        echo "Error al eliminar: " . $conexion->error;
    }
} else {
    header("Location: ../VIEWS/asientos_diario.php");
}
?>