<?php
// BACKEND/guardar_asiento.php
require_once 'conecxion_bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recibir y limpiar datos de la cabecera
    $fecha_asiento    = $_POST['fecha_asiento'];
    $nro_comprobante  = !empty($_POST['nro_comprobante']) ? trim($_POST['nro_comprobante']) : null;
    $glosa            = trim($_POST['glosa']);
    
    // Extraemos el mes y año de la fecha elegida por el usuario
    $mes_asiento  = date('m', strtotime($fecha_asiento));
    $anio_asiento = date('Y', strtotime($fecha_asiento));

    // Valores por defecto para tu estructura
    $tipo_comprobante = 'DIARIO'; 
    $estado_asiento   = 'APROBADO'; 
    $id_usuario       = 1; // ID temporal del usuario administrador
    $id_factura       = null; 

    // 2. Recibir los arrays del detalle dinámico
    $cuentas = $_POST['id_cuenta'];
    $debes   = $_POST['debe'];
    $haberes = $_POST['haber'];

    if (empty($cuentas) || count($cuentas) < 2) {
        die("Error: Un asiento contable requiere al menos dos cuentas (Principio de Partida Doble).");
    }

    // 3. Validación de cuadre en el servidor
    $total_debe = 0;
    $total_haber = 0;
    foreach ($debes as $index => $val) {
        $total_debe  += floatval($val);
        $total_haber += floatval($haberes[$index]);
    }

    if (abs($total_debe - $total_haber) > 0.01 || $total_debe <= 0) {
        die("Error de consistencia: El asiento está descuadrado. Debe: $total_debe, Haber: $total_haber");
    }

    // 4. INICIAR TRANSACCIÓN
    $conexion->begin_transaction();

    try {
        // --- SOLUCIÓN AL ERROR DE COLUMNA DESCONOCIDA ---
        // Buscamos el id_periodo activo que coincida con el mes y año
        $sql_periodo = "SELECT id_periodo FROM periodo_contable WHERE mes = ? AND anio = ? LIMIT 1";
        $stmt_p = $conexion->prepare($sql_periodo);
        $stmt_p->bind_param("ss", $mes_asiento, $anio_asiento);
        $stmt_p->execute();
        $res_p = $stmt_p->get_result();

        if ($res_p->num_rows > 0) {
            $fila_p = $res_p->fetch_assoc();
            $id_periodo = $fila_p['id_periodo'];
        } else {
            // Insertamos el período únicamente con las columnas esenciales que sabemos que existen
            $sql_crear_p = "INSERT INTO periodo_contable (mes, anio) VALUES (?, ?)";
            $stmt_cp = $conexion->prepare($sql_crear_p);
            $stmt_cp->bind_param("ss", $mes_asiento, $anio_asiento);
            $stmt_cp->execute();
            $id_periodo = $conexion->insert_id;
        }
        // ------------------------------------------------

        // Generar comprobante automático si viene vacío
        if ($nro_comprobante === null) {
            $resultado = $conexion->query("SELECT COUNT(*) + 1 as total FROM asiento_diario");
            $fila = $resultado->fetch_assoc();
            $nro_comprobante = "AS-" . str_pad($fila['total'], 5, "0", STR_PAD_LEFT);
        }

        // Insertar la cabecera (Tabla: asiento_diario)
        $sql_cabecera = "INSERT INTO asiento_diario (nro_comprobante, tipo_comprobante, fecha_asiento, glosa, id_usuario, id_factura, id_periodo, estado_asiento) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conexion->prepare($sql_cabecera);
        $stmt->bind_param("ssssiiis", $nro_comprobante, $tipo_comprobante, $fecha_asiento, $glosa, $id_usuario, $id_factura, $id_periodo, $estado_asiento);
        $stmt->execute();
        
        $id_asiento = $conexion->insert_id;

        // Insertar el detalle (Tabla: asiento_detalle)
        $sql_detalle = "INSERT INTO asiento_detalle (id_asiento, id_cuenta, debe, haber) VALUES (?, ?, ?, ?)";
        $stmt_det = $conexion->prepare($sql_detalle);

        foreach ($cuentas as $i => $id_cuenta) {
            $monto_debe  = floatval($debes[$i]);
            $monto_haber = floatval($haberes[$i]);

            if ($id_cuenta === "" || ($monto_debe == 0 && $monto_haber == 0)) {
                continue;
            }

            $stmt_det->bind_param("iidd", $id_asiento, $id_cuenta, $monto_debe, $monto_haber);
            $stmt_det->execute();
        }

        $conexion->commit();
        header("Location: ../VIEWS/asientos_diario.php?status=success");
        exit();

    } catch (Exception $e) {
        $conexion->rollback();
        die("Error crítico al guardar el asiento contable: " . $e->getMessage());
    }
} else {
    header("Location: ../VIEWS/asientos_diario.php");
    exit();
}
?>