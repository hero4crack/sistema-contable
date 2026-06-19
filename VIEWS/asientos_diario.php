<?php
// ============================================
// 1. PRIMERO: Incluir sesión (como en inicio.php)
// ============================================
include_once '../BACKEND/conexion_login.php';

// 2. Verificar autenticación
if (!isset($_SESSION['usuario'])) {
    header("Location: ../VIEWS/index.php");
    exit();
}

// 3. Incluir los demás archivos
require_once '../BACKEND/conecxion_bd.php';
require_once '../BACKEND/consulta_asientos.php';

// ============================================
// 4. OBTENER EMPRESAS PARA EL SELECTOR
// ============================================
$query_empresas = "SELECT id_empresa, nombre_empresa, rif FROM empresas_clientes WHERE estado_activo = 1 ORDER BY nombre_empresa ASC";
$result_empresas = mysqli_query($conexion, $query_empresas);
$empresas = [];
while ($row = mysqli_fetch_assoc($result_empresas)) {
    $empresas[] = $row;
}

// ============================================
// 5. CAPTURAR FILTROS (mes, año, empresa)
// ============================================
$mes_actual = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$anio_actual = isset($_GET['anio']) ? $_GET['anio'] : date('Y');
$id_empresa_filtro = isset($_GET['id_empresa']) ? $_GET['id_empresa'] : '';

// ============================================
// 6. OBTENER DATOS DE ASIENTOS
// ============================================
$asientos = obtenerAsientos($conexion, $mes_actual, $anio_actual, $id_empresa_filtro);
$cuentas_catalogo = obtenerCuentasParaAsiento($conexion);

// Almacenamos las cuentas en un array de PHP para pasarlo limpiamente a JavaScript
$array_cuentas = [];
if ($cuentas_catalogo && $cuentas_catalogo->num_rows > 0) {
    while ($c = $cuentas_catalogo->fetch_assoc()) {
        $array_cuentas[] = $c;
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
    <title>Asientos Diario | Contable EA</title>
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/style_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../JAVASCRIPT/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <style>
        /* ============================================ */
        /* ESTILOS GENERALES                            */
        /* ============================================ */
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
        .main-content {
            padding: 20px 25px;
        }
        .filtro-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
        }
        .empresa-info {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 8px 15px;
            color: #166534;
            font-size: 0.85rem;
        }
        .empresa-info i {
            margin-right: 8px;
            color: #10b981;
        }

        /* ============================================ */
        /* TARJETAS DE RESUMEN                          */
        /* ============================================ */
        .resumen-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.3s;
            border-left: 4px solid transparent;
        }
        .resumen-card:hover {
            transform: translateY(-5px);
        }
        .resumen-card .numero {
            font-size: 1.8rem;
            font-weight: 700;
        }
        .resumen-card .label {
            font-size: 0.8rem;
            color: #64748b;
        }
        .resumen-card .icono {
            font-size: 1.5rem;
            margin-bottom: 8px;
            display: block;
        }
        .resumen-card.total { border-left-color: #3b82f6; }
        .resumen-card.total .icono { color: #3b82f6; }
        .resumen-card.total .numero { color: #1e40af; }
        
        .resumen-card.monto { border-left-color: #10b981; }
        .resumen-card.monto .icono { color: #10b981; }
        .resumen-card.monto .numero { color: #16a34a; }

        /* ============================================ */
        /* TABLA                                       */
        /* ============================================ */
        .table-wrapper {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
        .table-wrapper .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .table-wrapper .table-header h4 {
            font-weight: 600;
            color: #1e293b;
        }
        .table-wrapper .table-header h4 i {
            color: #667eea;
            margin-right: 10px;
        }

        .table-asientos thead th {
            background: #f1f5f9;
            color: #1e293b;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 15px;
            border-bottom: 2px solid #e2e8f0;
        }
        .table-asientos tbody td {
            padding: 10px 15px;
            vertical-align: middle;
        }
        .table-asientos tbody tr:hover {
            background: #f8fafc;
        }

        /* ============================================ */
        /* BOTONES                                      */
        /* ============================================ */
        .btn-nuevo-asiento {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        .btn-nuevo-asiento:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-nuevo-asiento i {
            margin-right: 8px;
        }

        .btn-ver {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-ver:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.35);
            color: white;
        }
        .btn-ver i {
            margin-right: 4px;
        }

        .btn-eliminar-asiento {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-eliminar-asiento:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.35);
            color: white;
        }
        .btn-eliminar-asiento i {
            margin-right: 4px;
        }

        .btn-modal-guardar {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-modal-guardar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            color: white;
        }
        .btn-modal-guardar:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-modal-cancelar {
            background: #f1f5f9;
            color: #475569;
            border: none;
            padding: 10px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-modal-cancelar:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        /* ============================================ */
        /* MODALES                                      */
        /* ============================================ */
        .modal-content {
            border-radius: 16px !important;
            border: none !important;
        }
        .modal-header {
            border-radius: 16px 16px 0 0 !important;
            padding: 18px 25px;
        }
        .modal-body {
            padding: 25px !important;
        }
        .modal-footer {
            border-radius: 0 0 16px 16px !important;
            padding: 15px 25px !important;
            background: #f8fafc;
        }

        .modal .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #475569;
            margin-bottom: 5px;
        }
        .modal .form-control,
        .modal .form-select {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 10px 15px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .modal .form-control:focus,
        .modal .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
        }

        .select-cuenta {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.9rem;
            width: 100%;
            transition: all 0.3s ease;
        }
        .select-cuenta:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
            outline: none;
        }

        .input-debe, .input-haber {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.9rem;
            text-align: right;
            width: 100%;
            transition: all 0.3s ease;
        }
        .input-debe:focus, .input-haber:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
            outline: none;
        }
        .input-debe {
            border-color: #3b82f6;
        }
        .input-haber {
            border-color: #ef4444;
        }

        .btn-agregar-fila {
            background: transparent;
            border: 2px dashed #667eea;
            color: #667eea;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-agregar-fila:hover {
            background: #667eea;
            color: white;
        }

        .btn-remover-fila {
            background: transparent;
            border: none;
            color: #ef4444;
            transition: all 0.3s ease;
            padding: 5px 10px;
            border-radius: 8px;
        }
        .btn-remover-fila:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        .totales-footer {
            background: #f1f5f9 !important;
            font-weight: 700;
            border-top: 3px double #cbd5e1 !important;
        }
        .totales-footer td {
            padding: 12px 15px !important;
            font-size: 0.9rem;
        }

        .status-cuadrado {
            font-weight: 600;
            font-size: 0.9rem;
        }
        .status-cuadrado .cuadrado {
            color: #16a34a;
        }
        .status-cuadrado .descuadrado {
            color: #dc2626;
        }

        /* ============================================ */
        /* DATATABLES - ESTILOS MEJORADOS               */
        /* ============================================ */
        .dataTables_wrapper {
            padding-top: 10px;
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dataTables_filter label {
            font-weight: 600;
            color: #475569;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: flex-end;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 16px 8px 40px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E") no-repeat 12px center;
            background-size: 18px;
            width: 250px;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
            outline: none;
            width: 300px;
        }

        .dataTables_wrapper .dataTables_length {
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dataTables_length label {
            font-weight: 600;
            color: #475569;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 6px 30px 6px 12px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #1e293b;
            background: white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='2' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") no-repeat right 12px center;
            background-size: 12px;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
            outline: none;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 20px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 8px 16px;
            margin: 0 3px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            color: #475569;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            background: white;
            cursor: pointer;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
            color: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-color: #667eea;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: linear-gradient(135deg, #5a6fd6, #6a3f9e);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }

        .dataTables_wrapper .dataTables_info {
            color: #64748b;
            font-size: 0.85rem;
            padding-top: 15px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
            }
            .dataTables_wrapper .dataTables_filter input:focus {
                width: 100%;
            }
            .dataTables_wrapper .dataTables_length label {
                flex-wrap: wrap;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 6px 10px;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        
        <!-- Sidebar -->
        <aside class="main-sidebar">
            <div class="brand"><img src="../IMG/logo_empresa-sinfondo.png" alt="#" class="mi-imagen"></div>
            <nav class="menu">
                <a href="../VIEWS/inicio.php"><i class="fas fa-home"></i> Inicio</a>
                <a href="../VIEWS/empresas_clientes.php"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="../VIEWS/registro_proveedor.php"><i class="fas fa-truck"></i> Proveedores</a>
                <a href="../VIEWS/libro_facturas.php"><i class="fas fa-file-invoice"></i> Libro de Facturas</a>
                <a href="../VIEWS/asientos_diario.php" class="active"><i class="fas fa-book"></i> Asientos Diario</a>
                <a href="../VIEWS/libro_mayor.php"><i class="fas fa-chart-line"></i> Libro Mayor</a>
                <a href="../VIEWS/balance_comprobacion.php"><i class="fas fa-balance-scale"></i> Balance</a>
                <a href="../VIEWS/estado_resultados.php"><i class="fas fa-file-invoice-dollar"></i> Estado de Resultados</a>
                <a href="../VIEWS/empleados.php"><i class="fas fa-users"></i> Empleados</a>
                <a href="../VIEWS/catalogo_cuenta.php"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="../VIEWS/auditoria.php"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="viewport">
            <?php include('header.php'); ?>

            <div class="main-content">
                
                <!-- ============================================ -->
                <!-- FILTROS                                      -->
                <!-- ============================================ -->
                <div class="filter-box">
                    <form method="GET" action="asientos_diario.php" class="row align-items-end g-2">
                        
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-secondary text-uppercase filtro-label">
                                <i class="fas fa-building me-1"></i> Empresa
                            </label>
                            <select name="id_empresa" class="form-select empresa-selector">
                                <option value="">Todas las empresas</option>
                                <?php foreach ($empresas as $emp): ?>
                                    <option value="<?php echo $emp['id_empresa']; ?>" 
                                        <?php echo ($id_empresa_filtro == $emp['id_empresa']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($emp['nombre_empresa']); ?> 
                                        (<?php echo htmlspecialchars($emp['rif']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

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

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark w-100 fw-bold">
                                <i class="fas fa-filter me-1"></i> Filtrar
                            </button>
                        </div>

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
                                <?php else: ?>
                                    <span class="ms-2 text-info">| <i class="fas fa-globe"></i> Todas</span>
                                <?php endif; ?>
                            </span>
                        </div>

                    </form>
                </div>

                <!-- ============================================ -->
                <!-- ASIENTOS DIARIO                             -->
                <!-- ============================================ -->
                <div class="table-wrapper">
                    <div class="table-header">
                        <h4><i class="fas fa-book"></i> Libro Diario</h4>
                        <button class="btn-nuevo-asiento" data-bs-toggle="modal" data-bs-target="#modalAsiento" onclick="inicializarAsientoNuevo()">
                            <i class="fas fa-plus-circle"></i> Nuevo Asiento
                        </button>
                    </div>

                    <table id="tablaAsientos" class="table table-asientos table-hover">
                        <thead>
                            <tr>
                                <th style="width: 7%;">ID</th>
                                <th>Fecha</th>
                                <th>Comprobante</th>
                                <th>Glosa / Descripción</th>
                                <th>Monto Total</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($asientos && $asientos->num_rows > 0): ?>
                                <?php while($row = $asientos->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-muted fw-bold">#<?= $row['id_asiento'] ?></td>
                                        <td><?= date("d/m/Y", strtotime($row['fecha_asiento'])) ?></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($row['nro_comprobante']) ?></span></td>
                                        <td><?= htmlspecialchars($row['glosa']) ?></td>
                                        <td class="fw-bold text-primary"><?= number_format($row['total_debe'], 2) ?> Bs.</td>
                                        <td class="text-center">
                                            <button class="btn-ver" onclick="verDetalle(<?= $row['id_asiento'] ?>)" title="Ver Detalle">
                                                <i class="fas fa-eye"></i> Ver
                                            </button>
                                            <button class="btn-eliminar-asiento" onclick="eliminarAsiento(<?= $row['id_asiento'] ?>)" title="Eliminar">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x d-block mb-2" style="color: #cbd5e1;"></i>
                                        <?php if ($id_empresa_filtro): ?>
                                            No hay asientos para esta empresa en el período seleccionado.
                                        <?php else: ?>
                                            No hay asientos registrados en el sistema.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </main>
    </div>

    <!-- ============================================ -->
    <!-- MODAL VER DETALLE                            -->
    <!-- ============================================ -->
    <div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #3b82f6, #2563eb); color: white;">
                    <h5 class="modal-title"><i class="fas fa-search me-2"></i> Detalle de Asiento Contable</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Cuenta Contable</th>
                                    <th class="text-end" style="width: 150px;">Debe (Bs.)</th>
                                    <th class="text-end" style="width: 150px;">Haber (Bs.)</th>
                                </tr>
                            </thead>
                            <tbody id="contenidoDetalle"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancelar" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL NUEVO ASIENTO                          -->
    <!-- ============================================ -->
    <div class="modal fade" id="modalAsiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #334155); color: white;">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Registrar Asiento Manual</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="../BACKEND/guardar_asiento.php" method="POST">
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Fecha de Operación</label>
                                <input type="date" name="fecha_asiento" class="form-control" required value="<?= date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Nro. Comprobante</label>
                                <input type="text" name="nro_comprobante" class="form-control" placeholder="Ej: DIARIO-001" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Glosa Explicativa</label>
                                <input type="text" name="glosa" class="form-control" placeholder="Ej: Registro de gastos operativos..." required>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="tablaAsientoDinamico">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50%;">Cuenta Selección</th>
                                        <th style="width: 22%;">Debe (Bs.)</th>
                                        <th style="width: 22%;">Haber (Bs.)</th>
                                        <th style="width: 6%;" class="text-center">Quitar</th>
                                    </tr>
                                </thead>
                                <tbody id="cuerpoAsiento"></tbody>
                                <tfoot>
                                    <tr class="totales-footer">
                                        <td class="text-end fw-bold">TOTALES ACUMULADOS:</td>
                                        <td id="totalDebe" class="fw-bold text-end text-primary">0.00 Bs.</td>
                                        <td id="totalHaber" class="fw-bold text-end text-danger">0.00 Bs.</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <button type="button" class="btn-agregar-fila" onclick="agregarFila()">
                            <i class="fas fa-plus"></i> Añadir Nueva Línea
                        </button>
                    </div>
                    <div class="modal-footer">
                        <div id="statusCuadrado" class="me-auto status-cuadrado">
                            <i class="fas fa-times-circle text-danger"></i> 
                            <span class="descuadrado">Asiento Descuadrado</span>
                        </div>
                        <button type="button" class="btn-modal-cancelar" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" id="btnGuardarAsiento" class="btn-modal-guardar" disabled>
                            <i class="fas fa-save me-1"></i> Guardar Asiento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include('script.php'); ?>

    <script>
        // Traemos las cuentas desde PHP de forma segura
        const cuentasDisponibles = <?= json_encode($array_cuentas); ?>;

        // ============================================
        // VER DETALLE DEL ASIENTO
        // ============================================
        function verDetalle(id) {
            fetch(`../BACKEND/obtener_detalle_asiento.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    data.forEach(f => {
                        let debeVal = parseFloat(f.debe) > 0 ? parseFloat(f.debe).toFixed(2) + ' Bs.' : '-';
                        let haberVal = parseFloat(f.haber) > 0 ? parseFloat(f.haber).toFixed(2) + ' Bs.' : '-';
                        html += `<tr>
                                    <td><strong>${f.codigo_cuenta}</strong> - ${f.nombre_cuenta}</td>
                                    <td class="text-end text-success">${debeVal}</td>
                                    <td class="text-end text-danger">${haberVal}</td>
                                 </tr>`;
                    });
                    document.getElementById('contenidoDetalle').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('modalVerDetalle')).show();
                })
                .catch(error => {
                    alert('Error al cargar el detalle del asiento');
                });
        }

        // ============================================
        // ELIMINAR ASIENTO
        // ============================================
        function eliminarAsiento(id) {
            if (confirm("¿Estás seguro de que deseas eliminar este asiento diario junto con todas sus partidas?")) {
                window.location.href = `../BACKEND/eliminar_asiento.php?id=${id}`;
            }
        }

        // ============================================
        // SCRIPT DINÁMICO DEL ASIENTO
        // ============================================
        function agregarFila() {
            const tbody = document.getElementById('cuerpoAsiento');
            const tr = document.createElement('tr');

            let opciones = `<option value="">Seleccione cuenta...</option>`;
            cuentasDisponibles.forEach(c => {
                opciones += `<option value="${c.id_cuenta}">${c.codigo_cuenta} - ${c.nombre_cuenta}</option>`;
            });

            tr.innerHTML = `
                <td>
                    <select name="id_cuenta[]" class="form-select select-cuenta" required>
                        ${opciones}
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="debe[]" class="form-control input-debe" value="0.00" oninput="calcularCuadrature()" onclick="this.select()">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="haber[]" class="form-control input-haber" value="0.00" oninput="calcularCuadrature()" onclick="this.select()">
                </td>
                <td class="text-center">
                    <button type="button" class="btn-remover-fila" onclick="removerFila(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
            calcularCuadrature();
        }

        function removerFila(boton) {
            const filas = document.querySelectorAll('#cuerpoAsiento tr');
            if(filas.length > 2) {
                boton.closest('tr').remove();
                calcularCuadrature();
            } else {
                alert("Un asiento contable requiere al menos dos partidas (Partida Doble).");
            }
        }

        function calcularCuadrature() {
            let totalDebe = 0;
            let totalHaber = 0;

            document.querySelectorAll('.input-debe').forEach(input => {
                let val = parseFloat(input.value);
                totalDebe += isNaN(val) ? 0 : val;
            });

            document.querySelectorAll('.input-haber').forEach(input => {
                let val = parseFloat(input.value);
                totalHaber += isNaN(val) ? 0 : val;
            });

            document.getElementById('totalDebe').innerHTML = totalDebe.toFixed(2) + " Bs.";
            document.getElementById('totalHaber').innerHTML = totalHaber.toFixed(2) + " Bs.";

            const statusBox = document.getElementById('statusCuadrado');
            const btnGuardar = document.getElementById('btnGuardarAsiento');

            // Validamos cuadre contable exacto
            if (totalDebe > 0 && totalHaber > 0 && Math.abs(totalDebe - totalHaber) < 0.01) {
                statusBox.innerHTML = `
                    <i class="fas fa-check-circle text-success"></i> 
                    <span class="cuadrado">Asiento Cuadrado Balanceado</span>
                `;
                btnGuardar.disabled = false;
            } else {
                statusBox.innerHTML = `
                    <i class="fas fa-times-circle text-danger"></i> 
                    <span class="descuadrado">Asiento Descuadrado</span>
                `;
                btnGuardar.disabled = true;
            }
        }

        function inicializarAsientoNuevo() {
            document.getElementById('cuerpoAsiento').innerHTML = "";
            agregarFila();
            agregarFila();
        }

        window.onload = () => {
            if(document.getElementById('cuerpoAsiento').children.length === 0) {
                inicializarAsientoNuevo();
            }
            
            // Inicializar DataTables
            if ($('#tablaAsientos').length) {
                $('#tablaAsientos').DataTable({
                    "language": {
                        "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                    },
                    "pageLength": 10,
                    "order": [[0, 'desc']],
                    "columnDefs": [
                        { "orderable": false, "targets": [5] }
                    ]
                });
            }
        };
    </script>
</body>
</html>