<?php
require_once '../BACKEND/conecxion_bd.php';
require_once '../BACKEND/consulta_factura.php';

// ============================================
// 1. OBTENER EMPRESAS PARA EL SELECTOR
// ============================================
$query_empresas = "SELECT id_empresa, nombre_empresa, rif FROM empresas_clientes WHERE estado_activo = 1 ORDER BY nombre_empresa ASC";
$result_empresas = mysqli_query($conexion, $query_empresas);
$empresas = [];
while ($row = mysqli_fetch_assoc($result_empresas)) {
    $empresas[] = $row;
}

// ============================================
// 2. OBTENER PROVEEDORES PARA EL SELECTOR
// ============================================
$query_proveedores = "SELECT id_proveedor, razon_social, rif FROM proveedores WHERE estado_activo = 1 ORDER BY razon_social ASC";
$result_proveedores = mysqli_query($conexion, $query_proveedores);
$proveedores = [];
while ($row = mysqli_fetch_assoc($result_proveedores)) {
    $proveedores[] = $row;
}

// ============================================
// 3. CAPTURAR FILTROS (mes, año, empresa, proveedor)
// ============================================
$mes_actual = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$anio_actual = isset($_GET['anio']) ? $_GET['anio'] : date('Y');
$id_empresa_filtro = isset($_GET['id_empresa']) ? $_GET['id_empresa'] : '';
$id_proveedor_filtro = isset($_GET['id_proveedor']) ? $_GET['id_proveedor'] : '';

// ============================================
// 4. OBTENER FACTURAS FILTRADAS
// ============================================
$resultado_facturas = obtenerLibroFacturas($conexion, $mes_actual, $anio_actual, $id_empresa_filtro, $id_proveedor_filtro);

// Separar ventas y compras
$ventas = [];
$compras = [];

if ($resultado_facturas && $resultado_facturas->num_rows > 0) {
    while ($f = $resultado_facturas->fetch_assoc()) {
        if ($f['tipo_transaccion'] === 'VENTA') {
            $ventas[] = $f;
        } else {
            $compras[] = $f;
        }
    }
}

// Array de meses
$meses = [
    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
    '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
    '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Libro de Facturas | Contable EA</title>
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/style_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../JAVASCRIPT/bootstrap.bundle.min.js"></script>
    <style>
        .tab-btn {
            padding: 10px 20px;
            font-weight: bold;
            border: none;
            background: #e2e8f0;
            color: #475569;
            border-radius: 8px 8px 0 0;
            margin-right: 5px;
            transition: all 0.3s ease;
        }
        .tab-btn.active-tab {
            background: #1e293b;
            color: white;
        }
        .tab-btn:hover:not(.active-tab) {
            background: #cbd5e1;
        }
        .totales-footer {
            background-color: #f8fafc !important;
            font-weight: bold;
            border-top: 3px double #cbd5e1 !important;
            border-bottom: 3px double #cbd5e1 !important;
        }
        .filter-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .empresa-selector {
            background: white;
            border: 2px solid #667eea;
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 500;
            color: #1e293b;
        }
        .empresa-selector:focus {
            border-color: #764ba2;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
        .proveedor-selector {
            background: white;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 500;
            color: #1e293b;
        }
        .proveedor-selector:focus {
            border-color: #d97706;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
        }
        .table-wrapper {
            overflow-x: auto;
        }
        .table th {
            white-space: nowrap;
        }
        .sin-datos {
            text-align: center;
            padding: 20px;
            color: #94a3b8;
        }
        .filtro-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 20px;">
        <strong>¡Excelente!</strong> El movimiento ha sido procesado correctamente en el libro fiscal.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="app-container">
        
        <aside class="main-sidebar">
            <div class="brand"><img src="../IMG/logo_empresa-sinfondo.png" alt="#" class="mi-imagen"></div>
            <nav class="menu">
                <a href="../VIEWS/inicio.php"><i class="fas fa-home"></i> Inicio</a>
                <a href="../VIEWS/empresas_clientes.php"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="../VIEWS/registro_proveedor.php"><i class="fas fa-truck"></i> Proveedores</a>
                <a href="../VIEWS/libro_facturas.php" class="active"><i class="fas fa-file-invoice"></i> Libro de Facturas</a>
                <a href="../VIEWS/asientos_diario.php"><i class="fas fa-book"></i> Asientos Diario</a>
                <a href="../VIEWS/libro_mayor.php"><i class="fas fa-chart-line"></i> Libro Mayor</a>
                <a href="../VIEWS/balance_comprobacion.php"><i class="fas fa-balance-scale"></i> Balance</a>
                <a href="../VIEWS/estado_resultados.php"><i class="fas fa-file-invoice-dollar"></i> Estado de Resultados</a>
                <a href="../VIEWS/empleados.php"><i class="fas fa-users"></i> Empleados</a>
                <a href="../VIEWS/catalogo_cuenta.php"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="../VIEWS/auditoria.php"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>

        <main class="viewport">
            <?php include('header.php') ?>

            <section class="content">
                
                <!-- ============================================ -->
                <!-- SECCIÓN DE FILTROS                           -->
                <!-- ============================================ -->
                <div class="filter-box">
                    <form method="GET" action="libro_facturas.php" class="row align-items-end g-2">
                        
                        <!-- Selector de Empresa (para VENTAS) -->
                        <div class="col-md-2">
                            <label class="form-label fw-bold text-secondary text-uppercase filtro-label">
                                <i class="fas fa-building me-1"></i> Empresa
                            </label>
                            <select name="id_empresa" class="form-select empresa-selector">
                                <option value="">Todas las empresas</option>
                                <?php foreach ($empresas as $emp): ?>
                                    <option value="<?php echo $emp['id_empresa']; ?>" 
                                        <?php echo ($id_empresa_filtro == $emp['id_empresa']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($emp['nombre_empresa']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Selector de Proveedor (para COMPRAS) -->
                        <div class="col-md-2">
                            <label class="form-label fw-bold text-secondary text-uppercase filtro-label">
                                <i class="fas fa-truck me-1"></i> Proveedor
                            </label>
                            <select name="id_proveedor" class="form-select proveedor-selector">
                                <option value="">Todos los proveedores</option>
                                <?php foreach ($proveedores as $prov): ?>
                                    <option value="<?php echo $prov['id_proveedor']; ?>" 
                                        <?php echo ($id_proveedor_filtro == $prov['id_proveedor']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($prov['razon_social']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Mes -->
                        <div class="col-md-2">
                            <label class="form-label fw-bold text-secondary text-uppercase filtro-label">
                                <i class="fas fa-calendar-alt me-1"></i> Mes
                            </label>
                            <select name="mes" class="form-select border-secondary-subtle">
                                <?php foreach ($meses as $num => $nombre): ?>
                                    <option value="<?php echo $num; ?>" <?php echo ($num == $mes_actual) ? 'selected' : ''; ?>>
                                        <?php echo $nombre; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Año -->
                        <div class="col-md-2">
                            <label class="form-label fw-bold text-secondary text-uppercase filtro-label">
                                <i class="fas fa-clock me-1"></i> Año
                            </label>
                            <select name="anio" class="form-select border-secondary-subtle">
                                <?php 
                                $anio_base = 2024;
                                $anio_limite = date('Y') + 1;
                                for ($a = $anio_base; $a <= $anio_limite; $a++): 
                                ?>
                                    <option value="<?php echo $a; ?>" <?php echo ($a == $anio_actual) ? 'selected' : ''; ?>>
                                        <?php echo $a; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Botón Filtrar -->
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark w-100 fw-bold">
                                <i class="fas fa-filter me-1"></i> Filtrar
                            </button>
                        </div>

                        <!-- Información del período -->
                        <div class="col text-end">
                            <span class="badge bg-secondary p-2 fs-6 text-uppercase fw-semibold">
                                <i class="fas fa-calendar-check me-1"></i>
                                <?php echo $meses[$mes_actual] . " " . $anio_actual; ?>
                                <?php if ($id_empresa_filtro): ?>
                                    <?php 
                                    $nombre_emp = '';
                                    foreach ($empresas as $emp) {
                                        if ($emp['id_empresa'] == $id_empresa_filtro) {
                                            $nombre_emp = $emp['nombre_empresa'];
                                            break;
                                        }
                                    }
                                    ?>
                                    <span class="ms-2 text-warning">| <i class="fas fa-building"></i> <?php echo htmlspecialchars($nombre_emp); ?></span>
                                <?php endif; ?>
                                <?php if ($id_proveedor_filtro): ?>
                                    <?php 
                                    $nombre_prov = '';
                                    foreach ($proveedores as $prov) {
                                        if ($prov['id_proveedor'] == $id_proveedor_filtro) {
                                            $nombre_prov = $prov['razon_social'];
                                            break;
                                        }
                                    }
                                    ?>
                                    <span class="ms-2 text-info">| <i class="fas fa-truck"></i> <?php echo htmlspecialchars($nombre_prov); ?></span>
                                <?php endif; ?>
                            </span>
                        </div>

                    </form>
                </div>

                <!-- Botones de pestañas -->
                <div class="d-flex justify-content-between align-items-end px-2">
                    <div>
                        <button id="btnTabVentas" class="tab-btn active-tab" onclick="cambiarLibro('VENTAS')">
                            <i class="fas fa-arrow-up text-success me-1"></i> LIBRO DE VENTAS (Débito Fiscal)
                        </button>
                        <button id="btnTabCompras" class="tab-btn" onclick="cambiarLibro('COMPRAS')">
                            <i class="fas fa-arrow-down text-warning me-1"></i> LIBRO DE COMPRAS (Crédito Fiscal)
                        </button>
                    </div>
                    <div class="mb-2">
                        <button class="primary-btn" style="background: #10b981; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalRegistro" onclick="limpiarFormularioNuevaFactura()">
                            <i class="fas fa-plus-circle"></i> REGISTRAR FACTURA
                        </button>
                    </div>
                </div>

                <div class="card" style="border-top-left-radius: 0px;">
                    
                    <!-- ================= TABLA DE VENTAS ================= -->
                    <div id="contenedorVentas" class="table-wrapper">
                        <table class="table table-hover table-bordered shadow-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 70px;">ID</th>
                                    <th>Fecha</th>
                                    <th>Nro. Factura / Control</th>
                                    <th>Cliente</th>
                                    <th>Base Imponible</th>
                                    <th>Monto Exento</th>
                                    <th>IVA Débito (16%)</th>
                                    <th>Total Factura</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php 
                                $total_base_ventas = 0;
                                $total_exento_ventas = 0;
                                $total_iva_ventas = 0;
                                $total_general_ventas = 0;

                                if (!empty($ventas)): 
                                    foreach ($ventas as $v): 
                                        $total_base_ventas += $v['base_imponible'];
                                        $total_exento_ventas += $v['monto_exento'];
                                        $total_iva_ventas += $v['monto_iva'];
                                        $total_general_ventas += $v['total_factura'];
                                ?>
                                        <tr>
                                            <td><strong class="text-secondary">#<?php echo $v['id_factura']; ?></strong></td>
                                            <td><?php echo date("d/m/Y", strtotime($v['fecha_documento'])); ?></td>
                                            <td>
                                                <span style="display:block; font-weight: bold;"><?php echo $v['nro_factura']; ?></span>
                                                <small style="color: #64748b;">Ctrl: <?php echo $v['nro_control']; ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($v['nombre_empresa'] ?? 'N/A'); ?></td>
                                            <td><?php echo number_format($v['base_imponible'], 2); ?> Bs.</td>
                                            <td><?php echo number_format($v['monto_exento'], 2); ?> Bs.</td>
                                            <td class="text-success fw-semibold">+<?php echo number_format($v['monto_iva'], 2); ?> Bs.</td>
                                            <td style="font-weight: 800; color: #1e293b;"><?php echo number_format($v['total_factura'], 2); ?> Bs.</td>
                                            <td>
                                                <button class="config-btn text-warning" title="Editar Venta" onclick="editarFactura(<?php echo $v['id_factura']; ?>)" style="background: transparent; border: none; margin-right: 5px;"><i class="fas fa-edit"></i></button>
                                                <a href="../BACKEND/eliminar_facturas.php?id_factura=<?php echo $v['id_factura'] ?>" class="btn btn-danger fs-6 text-white p-1" style="width: 28px; height: 28px; display: inline-flex; justify-content: center; align-items: center; padding: 0 !important;"> <i class="fa-solid fa-trash" style="font-size: 0.85rem;"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="sin-datos">
                                            <?php if ($id_empresa_filtro): ?>
                                                No hay operaciones de ventas para esta empresa en el período seleccionado.
                                            <?php else: ?>
                                                No hay operaciones de ventas en este período.
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <?php if (!empty($ventas)): ?>
                            <tfoot>
                                <tr class="totales-footer">
                                    <td colspan="4" class="text-end text-uppercase pe-3">Totales Libro Ventas:</td>
                                    <td><?php echo number_format($total_base_ventas, 2); ?> Bs.</td>
                                    <td><?php echo number_format($total_exento_ventas, 2); ?> Bs.</td>
                                    <td class="text-success">+<?php echo number_format($total_iva_ventas, 2); ?> Bs.</td>
                                    <td style="color: #1e293b; font-weight: 800;"><?php echo number_format($total_general_ventas, 2); ?> Bs.</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>

                    <!-- ================= TABLA DE COMPRAS ================= -->
                    <div id="contenedorCompras" class="table-wrapper d-none">
                        <table class="table table-hover table-bordered shadow-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 70px;">ID</th>
                                    <th>Fecha</th>
                                    <th>Nro. Factura / Control</th>
                                    <th>Proveedor</th>
                                    <th>Base Imponible</th>
                                    <th>IVA Crédito (16%)</th>
                                    <th>Retención IVA (Col. Exento)</th>
                                    <th>Total Factura</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php 
                                $total_base_compras = 0;
                                $total_iva_compras = 0;
                                $total_exento_compras = 0;
                                $total_general_compras = 0;

                                if (!empty($compras)): 
                                    foreach ($compras as $c): 
                                        $total_base_compras += $c['base_imponible'];
                                        $total_iva_compras += $c['monto_iva'];
                                        $total_exento_compras += $c['monto_exento']; 
                                        $total_general_compras += $c['total_factura'];
                                ?>
                                        <tr>
                                            <td><strong class="text-secondary">#<?php echo $c['id_factura']; ?></strong></td>
                                            <td><?php echo date("d/m/Y", strtotime($c['fecha_documento'])); ?></td>
                                            <td>
                                                <span style="display:block; font-weight: bold;"><?php echo $c['nro_factura']; ?></span>
                                                <small style="color: #64748b; display: block;">Ctrl: <?php echo $c['nro_control']; ?></small>
                                                <?php if(!empty($c['nro_comprobante_retencion'])): ?>
                                                    <small class="badge bg-primary-subtle text-primary p-1 mt-1" style="font-size: 0.75rem;">
                                                        <i class="fas fa-receipt me-1"></i> Ret: <?php echo htmlspecialchars($c['nro_comprobante_retencion']); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($c['nombre_proveedor'] ?? 'N/A'); ?></td>
                                            <td><?php echo number_format($c['base_imponible'], 2); ?> Bs.</td>
                                            <td class="text-danger fw-semibold">+<?php echo number_format($c['monto_iva'], 2); ?> Bs.</td>
                                            <td class="text-primary fw-bold" style="background: #f0fdf4;"><?php echo number_format($c['monto_exento'], 2); ?> Bs.</td>
                                            <td style="font-weight: 800; color: #1e293b;"><?php echo number_format($c['total_factura'], 2); ?> Bs.</td>
                                            <td>
                                                <button class="config-btn text-warning" title="Editar Compra" onclick="editarFactura(<?php echo $c['id_factura']; ?>)" style="background: transparent; border: none; margin-right: 5px;"><i class="fas fa-edit"></i></button>
                                                <a href="../BACKEND/eliminar_facturas.php?id_factura=<?php echo $c['id_factura'] ?>" class="btn btn-danger fs-6 text-white p-1" style="width: 28px; height: 28px; display: inline-flex; justify-content: center; align-items: center; padding: 0 !important;"> <i class="fa-solid fa-trash" style="font-size: 0.85rem;"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="sin-datos">
                                            <?php if ($id_proveedor_filtro): ?>
                                                No hay operaciones de compras para este proveedor en el período seleccionado.
                                            <?php else: ?>
                                                No hay operaciones de compras en este período.
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <?php if (!empty($compras)): ?>
                            <tfoot>
                                <tr class="totales-footer">
                                    <td colspan="4" class="text-end text-uppercase pe-3">Totales Libro Compras:</td>
                                    <td><?php echo number_format($total_base_compras, 2); ?> Bs.</td>
                                    <td class="text-danger">+<?php echo number_format($total_iva_compras, 2); ?> Bs.</td>
                                    <td class="text-primary" style="background: #f0fdf4;"><?php echo number_format($total_exento_compras, 2); ?> Bs.</td>
                                    <td style="color: #1e293b; font-weight: 800;"><?php echo number_format($total_general_compras, 2); ?> Bs.</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>

                </div>
            </section>
        </main>
    </div>

    <!-- ============================================ -->
    <!-- MODAL DE REGISTRO DE FACTURA                 -->
    <!-- ============================================ -->
    <div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <div class="modal-header" style="background: #1e293b; color: white; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" id="tituloModal"><i class="fas fa-file-invoice me-2"></i> Nueva Factura Fiscal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../BACKEND/guardar_factura.php" method="POST" id="formFactura">
                    <input type="hidden" name="id_factura" id="edit_id_factura" value="">
                    
                    <div class="modal-body" style="padding: 30px;">
                        <div class="row g-3">
                            
                            <div class="col-md-4 mb-2">
                                <label class="form-label fw-bold text-secondary">Fecha de Emisión</label>
                                <input type="date" name="fecha_documento" id="fecha_documento" class="form-control border-primary" required value="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label class="form-label fw-bold text-secondary">Tipo de Movimiento</label>
                                <select name="tipo_transaccion" id="tipo_transaccion" class="form-select border-primary fw-semibold" onchange="alternarTerceros()" required>
                                    <option value="VENTA">Venta (Libro Ventas)</option>
                                    <option value="COMPRA">Compra (Libro Compras)</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-2" id="grupo_cliente">
                                <label class="form-label fw-bold text-success"><i class="fas fa-city me-1"></i> Empresa Cliente</label>
                                <select name="id_empresa" id="id_empresa" class="form-select border-success">
                                    <option value="">Seleccione...</option>
                                    <?php
                                    $empresas_modal = mysqli_query($conexion, "SELECT id_empresa, nombre_empresa FROM empresas_clientes WHERE estado_activo = '1' ORDER BY nombre_empresa ASC");
                                    while ($emp = $empresas_modal->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $emp['id_empresa']; ?>"><?php echo $emp['nombre_empresa']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-4 mb-2 d-none" id="grupo_proveedor">
                                <label class="form-label fw-bold text-warning"><i class="fas fa-truck me-1"></i> Proveedor</label>
                                <select name="id_proveedor" id="id_proveedor" class="form-select border-warning">
                                    <option value="">Seleccione...</option>
                                    <?php
                                    $provQuery = $conexion->query("SELECT id_proveedor, razon_social FROM proveedores WHERE estado_activo = 1");
                                    while ($prov = $provQuery->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $prov['id_proveedor']; ?>"><?php echo $prov['razon_social']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nro. Factura</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-hashtag text-muted"></i></span>
                                    <input type="text" name="nro_factura" id="nro_factura" class="form-control" placeholder="Ej: 00234" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nro. Control</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-shield-alt text-muted"></i></span>
                                    <input type="text" name="nro_control" id="nro_control" class="form-control" placeholder="Ej: 00-00234" required>
                                </div>
                            </div>

                            <div class="col-md-12 d-none" id="grupo_comprobante">
                                <label class="form-label fw-bold text-primary"><i class="fas fa-receipt me-1"></i> Nro. Comprobante de Retención (SENIAT)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-file-signature text-muted"></i></span>
                                    <input type="text" name="nro_comprobante_retencion" id="nro_comprobante_retencion" class="form-control border-primary" placeholder="Ej: 20260600000001">
                                </div>
                                <small class="text-muted">Formato exigido por el SENIAT: Año (4d) + Mes (2d) + Secuencia (8d).</small>
                            </div>

                            <hr class="text-muted my-4">

                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark">Base Imponible (Bs.)</label>
                                <input type="number" step="0.01" min="0" name="base_imponible" id="base_modal" class="form-control fw-bold border-dark" oninput="calcularTotalesModal()" required placeholder="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-secondary">Monto Exento (Bs.)</label>
                                <input type="number" step="0.01" min="0" name="monto_exento" id="exento_modal" class="form-control text-secondary" oninput="calcularTotalesModal()" value="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-danger">IVA (16% Bs.)</label>
                                <input type="text" name="monto_iva" id="iva_modal" class="form-control text-danger fw-bold" readonly style="background: #fff5f5; border-color: #feb2b2;" value="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-primary">Total Factura (Bs.)</label>
                                <input type="hidden" name="total_factura" id="total_modal_hidden" value="0.00">
                                <input type="text" id="iva_total_visual" class="form-control bg-primary-subtle text-primary fw-bolder fs-5 border-primary" readonly value="0.00">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer" style="background: #f8fafc; border-radius: 0 0 15px 15px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnSubmitModal" class="btn btn-success" style="background: #10b981; border: none; padding: 10px 25px; font-weight: bold;">
                            <i class="fas fa-save me-1"></i> Procesar Factura
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include('script.php'); ?>

    <script>
        // ============================================
        // FUNCIÓN PARA CAMBIAR ENTRE PESTAÑAS
        // ============================================
        function cambiarLibro(tipo) {
            if (tipo === 'VENTAS') {
                document.getElementById('contenedorVentas').classList.remove('d-none');
                document.getElementById('contenedorCompras').classList.add('d-none');
                document.getElementById('btnTabVentas').classList.add('active-tab');
                document.getElementById('btnTabCompras').classList.remove('active-tab');
            } else {
                document.getElementById('contenedorVentas').classList.add('d-none');
                document.getElementById('contenedorCompras').classList.remove('d-none');
                document.getElementById('btnTabCompras').classList.add('active-tab');
                document.getElementById('btnTabVentas').classList.remove('active-tab');
            }
        }

        // ============================================
        // FUNCIONES PARA EL MODAL
        // ============================================
        function alternarTerceros() {
            var tipo = document.getElementById('tipo_transaccion').value;
            if (tipo === 'VENTA') {
                document.getElementById('grupo_cliente').classList.remove('d-none');
                document.getElementById('grupo_proveedor').classList.add('d-none');
                document.getElementById('grupo_comprobante').classList.add('d-none');
            } else {
                document.getElementById('grupo_cliente').classList.add('d-none');
                document.getElementById('grupo_proveedor').classList.remove('d-none');
                document.getElementById('grupo_comprobante').classList.remove('d-none');
            }
        }

        function limpiarFormularioNuevaFactura() {
            document.getElementById('formFactura').reset();
            document.getElementById('edit_id_factura').value = '';
            document.getElementById('iva_modal').value = '0.00';
            document.getElementById('iva_total_visual').value = '0.00';
            document.getElementById('total_modal_hidden').value = '0.00';
            alternarTerceros();
        }

        function calcularTotalesModal() {
            var base = parseFloat(document.getElementById('base_modal').value) || 0;
            var exento = parseFloat(document.getElementById('exento_modal').value) || 0;
            var iva = base * 0.16;
            var total = base + exento + iva;
            
            document.getElementById('iva_modal').value = iva.toFixed(2);
            document.getElementById('iva_total_visual').value = total.toFixed(2) + ' Bs.';
            document.getElementById('total_modal_hidden').value = total.toFixed(2);
        }

        function editarFactura(id) {
            alert('Función de edición en desarrollo');
        }
    </script>

</body>
</html>