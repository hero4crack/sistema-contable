<?php
// BACKEND/guardar_factura.php
require_once 'conecxion_bd.php';

// =========================================================================
// CRÍTICO: REEMPLAZA ESTOS NÚMEROS CON LOS IDs REALES DE TU CATÁLOGO DE CUENTAS
// =========================================================================
define('CUENTA_VENTAS', 10);          // ID de la cuenta de Ingresos por Venta
define('CUENTA_DEBITO_IVA', 15);      // ID de la cuenta Pasivo: Débito Fiscal IVA
define('CUENTA_POR_COBRAR', 5);       // ID de la cuenta Activo: Cuentas por Cobrar Clientes

define('CUENTA_COMPRAS', 20);         // ID de la cuenta de Egreso/Costo: Compras
define('CUENTA_CREDITO_IVA', 8);      // ID de la cuenta Activo: Crédito Fiscal IVA
define('CUENTA_POR_PAGAR', 12);       // ID de la cuenta Pasivo: Cuentas por Pagar Proveedores
// =========================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Recibir y limpiar datos de la factura
    $fecha_documento    = $_POST['fecha_documento'];
    $tipo_transaccion   = $_POST['tipo_transaccion']; // 'VENTA' o 'COMPRA'
    $nro_factura        = trim($_POST['nro_factura']);
    $nro_control        = trim($_POST['nro_control']);
    $base_imponible     = floatval($_POST['base_imponible']);
    $monto_exento       = floatval($_POST['monto_exento']);
    $monto_iva          = floatval($_POST['monto_iva']);
    $total_factura      = floatval($_POST['total_factura']);
    
    $id_empresa  = !empty($_POST['id_empresa']) ? intval($_POST['id_empresa']) : null;
    $id_proveedor = !empty($_POST['id_proveedor']) ? intval($_POST['id_proveedor']) : null;
    $id_tercero   = ($tipo_transaccion === 'VENTA') ? $id_empresa : $id_proveedor;
    
    $nro_comprobante_retencion = !empty($_POST['nro_comprobante_retencion']) ? trim($_POST['nro_comprobante_retencion']) : null;

    // Extraemos mes y año para amarrar el período contable
    $mes_asiento  = date('m', strtotime($fecha_documento));
    $anio_asiento = date('Y', strtotime($fecha_documento));

    // Iniciamos la transacción para asegurar que si el asiento falla, la factura tampoco se guarde
    $conexion->begin_transaction();

    try {
        // 2. GUARDAR LA FACTURA EN EL LIBRO FISCAL
        $sql_factura = "INSERT INTO facturas (fecha_documento, tipo_transaccion, nro_factura, nro_control, id_empresa, id_tercero, base_imponible, monto_exento, monto_iva, total_factura, nro_comprobante_retencion) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_f = $conexion->prepare($sql_factura);
        $stmt_f->bind_param("ssssiidddds", $fecha_documento, $tipo_transaccion, $nro_factura, $nro_control, $id_empresa, $id_tercero, $base_imponible, $monto_exento, $monto_iva, $total_factura, $nro_comprobante_retencion);
        $stmt_f->execute();
        
        // Capturamos el ID de la factura recién guardada
        $id_factura_reciente = $conexion->insert_id;

        // 3. OBTENER O CREAR EL PERÍODO CONTABLE
        $id_periodo = null;
        $sql_p = "SELECT id_periodo FROM periodo_contable WHERE mes = ? AND anio = ? LIMIT 1";
        $stmt_p = $conexion->prepare($sql_p);
        $stmt_p->bind_param("ss", $mes_asiento, $anio_asiento);
        $stmt_p->execute();
        $res_p = $stmt_p->get_result();

        if ($res_p->num_rows > 0) {
            $fila_p = $res_p->fetch_assoc();
            $id_periodo = $fila_p['id_periodo'];
        } else {
            $sql_crear_p = "INSERT INTO periodo_contable (mes, anio) VALUES (?, ?)";
            $stmt_cp = $conexion->prepare($sql_crear_p);
            $stmt_cp->bind_param("ss", $mes_asiento, $anio_asiento);
            $stmt_cp->execute();
            $id_periodo = $conexion->insert_id;
        }

        // 4. GENERAR CABECERA DEL ASIENTO AUTOMÁTICO
        $tipo_comprobante = $tipo_transaccion; // 'VENTA' o 'COMPRA'
        $estado_asiento   = 'APROBADO';
        $id_usuario       = 1; 
        
        // Generamos el correlativo del comprobante automático
        $resultado_c = $conexion->query("SELECT COUNT(*) + 1 as total FROM asiento_diario");
        $fila_c = $resultado_c->fetch_assoc();
        $nro_comprobante = "AUTO-" . strtoupper($tipo_transaccion[0]) . str_pad($fila_c['total'], 5, "0", STR_PAD_LEFT);
        
        $glosa = "Asiento automático según factura fiscal Nro: " . $nro_factura;

        $sql_cabecera = "INSERT INTO asiento_diario (nro_comprobante, tipo_comprobante, fecha_asiento, glosa, id_usuario, id_factura, id_periodo, estado_asiento) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_a = $conexion->prepare($sql_cabecera);
        $stmt_a->bind_param("ssssiiis", $nro_comprobante, $tipo_comprobante, $fecha_documento, $glosa, $id_usuario, $id_factura_reciente, $id_periodo, $estado_asiento);
        $stmt_a->execute();
        
        $id_asiento_nuevo = $conexion->insert_id;

// 5. GENERAR EL DETALLE DEL ASIENTO (PARTIDA DOBLE AUTOMÁTICA)
        $sql_detalle = "INSERT INTO asiento_detalle (id_asiento, id_cuenta, debe, haber) VALUES (?, ?, ?, ?)";
        $stmt_d = $conexion->prepare($sql_detalle);

        // Definimos variables fijas para ceros y cálculos intermedios para evitar problemas de paso por referencia
        $cero_monto = 0.00;
        $monto_principal = $base_imponible + $monto_exento;

        if ($tipo_transaccion === 'VENTA') {
            // LÓGICA DE LA VENTA:
            
            // Línea 1: Cuentas por Cobrar Clientes (Debe: Total de la factura)
            $id_c1 = CUENTA_POR_COBRAR;
            $stmt_d->bind_param("iidd", $id_asiento_nuevo, $id_c1, $total_factura, $cero_monto);
            $stmt_f_ok = $stmt_d->execute();

            // Línea 2: Ventas (Haber: Base Imponible + Monto Exento)
            $id_c2 = CUENTA_VENTAS;
            $stmt_d->bind_param("iidd", $id_asiento_nuevo, $id_c2, $cero_monto, $monto_principal);
            $stmt_d->execute();

            // Línea 3: Débito Fiscal IVA (Haber: Solo si hubo IVA)
            if ($monto_iva > 0) {
                $id_c3 = CUENTA_DEBITO_IVA;
                $stmt_d->bind_param("iidd", $id_asiento_nuevo, $id_c3, $cero_monto, $monto_iva);
                $stmt_d->execute();
            }

        } else {
            // LÓGICA DE LA COMPRA:
            
            // Línea 1: Compras / Gastos (Debe: Base Imponible + Monto Exento)
            $id_c1 = CUENTA_COMPRAS;
            $stmt_d->bind_param("iidd", $id_asiento_nuevo, $id_c1, $monto_principal, $cero_monto);
            $stmt_d->execute();

            // Línea 2: Crédito Fiscal IVA (Debe: Solo si hubo IVA)
            if ($monto_iva > 0) {
                $id_c2 = CUENTA_CREDITO_IVA;
                $stmt_d->bind_param("iidd", $id_asiento_nuevo, $id_c2, $monto_iva, $cero_monto);
                $stmt_d->execute();
            }

            // Línea 3: Cuentas por Pagar Proveedores (Haber: Total de la factura)
            $id_c3 = CUENTA_POR_PAGAR;
            $stmt_d->bind_param("iidd", $id_asiento_nuevo, $id_c3, $cero_monto, $total_factura);
            $stmt_d->execute();
        }

        // Si todo el proceso se completó de manera perfecta, consolidamos los datos en la BD
        $conexion->commit();
        header("Location: ../VIEWS/libro_facturas.php?status=success");
        exit();

    } catch (Exception $e) {
        // En caso de cualquier error imprevisto, deshacemos todo el proceso
        $conexion->rollback();
        die("Error crítico en la automatización contable: " . $e->getMessage());
    }

} else {
    header("Location: ../VIEWS/libro_facturas.php");
    exit();
}
?>