<?php
// Función para registrar sin tener que reescribir todo tu lógica de actualización
function registrarAuditoria($conexion, $tabla, $id_registro, $accion, $valor_anterior, $valor_nuevo) {
    // Usamos el ID del usuario de la sesión actual
    $id_usuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 0;
    $ip = $_SERVER['REMOTE_ADDR'];

    $sql = "INSERT INTO auditoria_sistema 
            (id_usuario, tabla_afectada, id_registro_afectado, accion, valor_anterior, valor_nuevo, ip_maquina) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("isissss", $id_usuario, $tabla, $id_registro, $accion, $valor_anterior, $valor_nuevo, $ip);
    $stmt->execute();
}
?>