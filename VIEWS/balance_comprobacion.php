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
require_once '../BACKEND/consulta_balance.php';

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
// 6. OBTENER DATOS DEL BALANCE
// ============================================
$datos = obtenerBalanceComprobacion($conexion, $mes_actual, $anio_actual, $id_empresa_filtro);

// Variables para los grandes totales
$totalSumasDebe = 0;
$totalSumasHaber = 0;
$totalSaldoDeudor = 0;
$totalSaldoAcreedor = 0;

// Guardar datos en array para procesar
$cuentas = [];
if ($datos && $datos->num_rows > 0) {
    while ($row = $datos->fetch_assoc()) {
        $cuentas[] = $row;
        $totalSumasDebe += $row['sumas_debe'];
        $totalSumasHaber += $row['sumas_haber'];
        $totalSaldoDeudor += $row['saldo_deudor'];
        $totalSaldoAcreedor += $row['saldo_acreedor'];
    }
}

// Determinar si la contabilidad está cuadrada
$diferencia = abs($totalSaldoDeudor - $totalSaldoAcreedor);
$contabilidad_cuadrada = $diferencia < 0.01;

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
    <title>Balance de Comprobación | Contable EA</title>
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
        .resumen-card.debe .icono { color: #3b82f6; }
        .resumen-card.debe .numero { color: #1e40af; }
        .resumen-card.debe { border-left-color: #3b82f6; }
        
        .resumen-card.haber .icono { color: #ef4444; }
        .resumen-card.haber .numero { color: #991b1b; }
        .resumen-card.haber { border-left-color: #ef4444; }
        
        .resumen-card.deudor .icono { color: #16a34a; }
        .resumen-card.deudor .numero { color: #16a34a; }
        .resumen-card.deudor { border-left-color: #16a34a; }
        
        .resumen-card.acreedor .icono { color: #f59e0b; }
        .resumen-card.acreedor .numero { color: #d97706; }
        .resumen-card.acreedor { border-left-color: #f59e0b; }

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

        .table-balance thead th {
            background: #f1f5f9;
            color: #1e293b;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 15px;
            border-bottom: 2px solid #e2e8f0;
        }
        .table-balance tbody td {
            padding: 10px 15px;
            vertical-align: middle;
        }
        .table-balance tbody tr:hover {
            background: #f8fafc;
        }
        .table-balance tfoot td {
            background: #f1f5f9;
            font-weight: 700;
            padding: 12px 15px;
            border-top: 3px double #cbd5e1;
        }

        .texto-debe {
            color: #1e40af;
            font-weight: 600;
        }
        .texto-haber {
            color: #991b1b;
            font-weight: 600;
        }
        .texto-deudor {
            color: #16a34a;
            font-weight: 700;
        }
        .texto-acreedor {
            color: #d97706;
            font-weight: 700;
        }

        /* ============================================ */
        /* ALERTAS                                      */
        /* ============================================ */
        .alert-cuadrada {
            background: #f0fdf4;
            border: 2px solid #bbf7d0;
            color: #166534;
            border-radius: 12px;
            padding: 15px 20px;
            font-weight: 600;
        }
        .alert-cuadrada i {
            color: #16a34a;
        }
        
        .alert-diferencia {
            background: #fef2f2;
            border: 2px solid #fca5a5;
            color: #991b1b;
            border-radius: 12px;
            padding: 15px 20px;
            font-weight: 600;
        }
        .alert-diferencia i {
            color: #dc2626;
        }

        /* ============================================ */
        /* BOTONES                                      */
        /* ============================================ */
        .btn-exportar {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
        }
        .btn-exportar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(22, 163, 74, 0.4);
            color: white;
        }
        .btn-exportar i {
            margin-right: 8px;
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
                <a href="../VIEWS/asientos_diario.php"><i class="fas fa-book"></i> Asientos Diario</a>
                <a href="../VIEWS/libro_mayor.php"><i class="fas fa-chart-line"></i> Libro Mayor</a>
                <a href="../VIEWS/balance_comprobacion.php" class="active"><i class="fas fa-balance-scale"></i> Balance</a>
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
                    <form method="GET" action="balance_comprobacion.php" class="row align-items-end g-2">
                        
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
                <!-- TARJETAS DE RESUMEN                          -->
                <!-- ============================================ -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="resumen-card debe">
                            <i class="fas fa-arrow-up icono"></i>
                            <div class="numero">Bs. <?php echo number_format($totalSumasDebe, 2); ?></div>
                            <div class="label">Total Sumas Debe</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="resumen-card haber">
                            <i class="fas fa-arrow-down icono"></i>
                            <div class="numero">Bs. <?php echo number_format($totalSumasHaber, 2); ?></div>
                            <div class="label">Total Sumas Haber</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="resumen-card deudor">
                            <i class="fas fa-check-circle icono"></i>
                            <div class="numero">Bs. <?php echo number_format($totalSaldoDeudor, 2); ?></div>
                            <div class="label">Saldo Deudor</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="resumen-card acreedor">
                            <i class="fas fa-exclamation-circle icono"></i>
                            <div class="numero">Bs. <?php echo number_format($totalSaldoAcreedor, 2); ?></div>
                            <div class="label">Saldo Acreedor</div>
                        </div>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- TABLA DEL BALANCE                            -->
                <!-- ============================================ -->
                <div class="table-wrapper">
                    <div class="table-header">
                        <h4><i class="fas fa-balance-scale"></i> Balance de Comprobación</h4>
                        <button onclick="exportarExcel()" class="btn-exportar">
                            <i class="fas fa-file-excel"></i> Exportar a Excel
                        </button>
                    </div>

                    <table id="tablaBalance" class="table table-balance table-hover">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Código</th>
                                <th>Cuenta</th>
                                <th class="text-end">Sumas Debe</th>
                                <th class="text-end">Sumas Haber</th>
                                <th class="text-end">Saldo Deudor</th>
                                <th class="text-end">Saldo Acreedor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($cuentas)): ?>
                                <?php foreach ($cuentas as $row): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($row['codigo_cuenta']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($row['nombre_cuenta']); ?></td>
                                        <td class="text-end texto-debe">Bs. <?php echo number_format($row['sumas_debe'], 2); ?></td>
                                        <td class="text-end texto-haber">Bs. <?php echo number_format($row['sumas_haber'], 2); ?></td>
                                        <td class="text-end texto-deudor">
                                            <?php if ($row['saldo_deudor'] > 0): ?>
                                                Bs. <?php echo number_format($row['saldo_deudor'], 2); ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end texto-acreedor">
                                            <?php if ($row['saldo_acreedor'] > 0): ?>
                                                Bs. <?php echo number_format($row['saldo_acreedor'], 2); ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x d-block mb-2" style="color: #cbd5e1;"></i>
                                        <?php if ($id_empresa_filtro): ?>
                                            No hay datos para esta empresa en el período seleccionado.
                                        <?php else: ?>
                                            No hay datos registrados en el sistema.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($cuentas)): ?>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end fw-bold">TOTALES GENERALES:</td>
                                    <td class="text-end texto-debe">Bs. <?php echo number_format($totalSumasDebe, 2); ?></td>
                                    <td class="text-end texto-haber">Bs. <?php echo number_format($totalSumasHaber, 2); ?></td>
                                    <td class="text-end texto-deudor">Bs. <?php echo number_format($totalSaldoDeudor, 2); ?></td>
                                    <td class="text-end texto-acreedor">Bs. <?php echo number_format($totalSaldoAcreedor, 2); ?></td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>

                    <!-- ============================================ -->
                    <!-- ALERTA DE CONTABILIDAD                      -->
                    <!-- ============================================ -->
                    <?php if (!empty($cuentas)): ?>
                        <?php if ($contabilidad_cuadrada): ?>
                            <div class="alert-cuadrada mt-3 text-center">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>¡Contabilidad Cuadrada!</strong> Las sumas y los saldos coinciden perfectamente.
                                <span class="badge bg-success ms-2">✅</span>
                            </div>
                        <?php else: ?>
                            <div class="alert-diferencia mt-3 text-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>¡Atención!</strong> Existe una diferencia de 
                                <strong>Bs. <?php echo number_format($diferencia, 2); ?></strong>
                                entre el saldo deudor y acreedor. Revisa tus asientos contables.
                                <span class="badge bg-danger ms-2">⚠️</span>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>

    <?php include('script.php'); ?>

    <script>
        $(document).ready(function() {
            $('#tablaBalance').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                },
                "pageLength": 10,
                "order": [[0, 'asc']],
                "columnDefs": [
                    { "orderable": false, "targets": [1] }
                ]
            });
        });

        function exportarExcel() {
            var tabla = document.querySelector("#tablaBalance");
            var html = tabla.outerHTML;
            
            // Agregar estilos para Excel
            var estilos = `
                <style>
                    th { background-color: #e2e8f0; font-weight: bold; }
                    td { border: 1px solid #cbd5e1; }
                    .texto-debe { color: #1e40af; }
                    .texto-haber { color: #991b1b; }
                    .texto-deudor { color: #16a34a; font-weight: bold; }
                    .texto-acreedor { color: #d97706; font-weight: bold; }
                </style>
            `;
            
            var contenido = `
                <html>
                    <head>
                        <meta charset="UTF-8">
                        ${estilos}
                    </head>
                    <body>
                        <h2>Balance de Comprobación</h2>
                        <p>Período: <?php echo $meses[$mes_actual] . " " . $anio_actual; ?></p>
                        ${html}
                    </body>
                </html>
            `;
            
            var url = 'data:application/vnd.ms-excel;charset=utf-8,' + encodeURIComponent(contenido);
            var link = document.createElement("a");
            link.download = "Balance_Comprobacion_<?php echo date('d_m_Y'); ?>.xls";
            link.href = url;
            link.click();
        }
    </script>
</body>
</html>