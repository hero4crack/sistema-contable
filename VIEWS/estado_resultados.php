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
require_once '../BACKEND/consulta_resultados.php';

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
// 6. OBTENER DATOS DEL ESTADO DE RESULTADOS
// ============================================
$resultados = obtenerEstadoResultados($conexion, $mes_actual, $anio_actual, $id_empresa_filtro);

// Extraer valores por tipo de cuenta
$ventas_totales = $resultados['ventas'] ?? 0;
$costo_ventas = abs($resultados['costos'] ?? 0);
$gastos_operativos = abs($resultados['gastos'] ?? 0);

// Cálculos principales
$utilidad_bruta = $ventas_totales - $costo_ventas;
$utilidad_neta = $utilidad_bruta - $gastos_operativos;

// Porcentajes
$porcentaje_utilidad_bruta = $ventas_totales > 0 ? ($utilidad_bruta / $ventas_totales) * 100 : 0;
$porcentaje_utilidad_neta = $ventas_totales > 0 ? ($utilidad_neta / $ventas_totales) * 100 : 0;

// Determinar color según resultado
$color_utilidad_neta = $utilidad_neta >= 0 ? 'text-success' : 'text-danger';
$icono_utilidad_neta = $utilidad_neta >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
$bg_utilidad_neta = $utilidad_neta >= 0 ? 'bg-success' : 'bg-danger';

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
    <title>Estado de Resultados | Contable EA</title>
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/style_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../JAVASCRIPT/bootstrap.bundle.min.js"></script>
    <style>
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
        .resultado-card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 30px;
        }
        .resultado-card .card-header {
            background: linear-gradient(135deg, #1e293b, #334155);
            color: white;
            padding: 18px 25px;
            border-bottom: none;
        }
        .resultado-card .card-header h3 {
            font-weight: 600;
        }
        .resultado-card .card-body {
            padding: 25px;
        }
        .resultado-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .resultado-item:last-child {
            border-bottom: none;
        }
        .resultado-item .label {
            font-size: 1rem;
            color: #475569;
        }
        .resultado-item .value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
        }
        .resultado-item .value.positive {
            color: #16a34a;
        }
        .resultado-item .value.negative {
            color: #dc2626;
        }
        .resultado-item .value.total {
            font-size: 1.3rem;
            font-weight: 700;
        }
        .resultado-item .badge-porcentaje {
            background: #f1f5f9;
            padding: 2px 10px;
            border-radius: 30px;
            font-size: 0.7rem;
            color: #475569;
        }
        .resumen-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.3s;
        }
        .resumen-card:hover {
            transform: translateY(-5px);
        }
        .resumen-card .numero {
            font-size: 2rem;
            font-weight: 700;
        }
        .resumen-card .label {
            font-size: 0.8rem;
            color: #64748b;
        }
        .resumen-card .icono {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }
        .resumen-card.ventas .icono { color: #3b82f6; }
        .resumen-card.ventas .numero { color: #1e293b; }
        .resumen-card.utilidad .icono { color: #16a34a; }
        .resumen-card.utilidad .numero { color: #16a34a; }
        .resumen-card.perdida .icono { color: #dc2626; }
        .resumen-card.perdida .numero { color: #dc2626; }
        .resumen-card.margen .icono { color: #8b5cf6; }
        .resumen-card.margen .numero { color: #8b5cf6; }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
        }
        .empty-state i {
            font-size: 3rem;
            display: block;
            margin-bottom: 15px;
            color: #cbd5e1;
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
        .filtro-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
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
                <a href="../VIEWS/balance_comprobacion.php"><i class="fas fa-balance-scale"></i> Balance</a>
                <a href="../VIEWS/estado_resultados.php" class="active"><i class="fas fa-file-invoice-dollar"></i> Estado de Resultados</a>
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
                    <form method="GET" action="estado_resultados.php" class="row align-items-end g-2">
                        
                        <!-- Selector de Empresa -->
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

                        <!-- Información -->
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
                <!-- ESTADO DE RESULTADOS                         -->
                <!-- ============================================ -->
                <?php if ($ventas_totales > 0 || $costo_ventas > 0 || $gastos_operativos > 0): ?>
                    
                    <!-- Tarjetas de Resumen -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="resumen-card ventas">
                                <i class="fas fa-dollar-sign icono"></i>
                                <div class="numero">Bs. <?php echo number_format($ventas_totales, 2); ?></div>
                                <div class="label">Ventas Totales</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="resumen-card <?php echo $utilidad_bruta >= 0 ? 'utilidad' : 'perdida'; ?>">
                                <i class="fas fa-chart-line icono"></i>
                                <div class="numero">Bs. <?php echo number_format($utilidad_bruta, 2); ?></div>
                                <div class="label">Utilidad Bruta</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="resumen-card <?php echo $utilidad_neta >= 0 ? 'utilidad' : 'perdida'; ?>">
                                <i class="fas fa-<?php echo $icono_utilidad_neta; ?> icono"></i>
                                <div class="numero">Bs. <?php echo number_format($utilidad_neta, 2); ?></div>
                                <div class="label">Utilidad Neta</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="resumen-card margen">
                                <i class="fas fa-percentage icono"></i>
                                <div class="numero"><?php echo number_format($porcentaje_utilidad_neta, 1); ?>%</div>
                                <div class="label">Margen de Utilidad</div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalle del Estado de Resultados -->
                    <div class="resultado-card">
                        <div class="card-header">
                            <h3><i class="fas fa-file-invoice-dollar me-2"></i> Estado de Resultados</h3>
                        </div>
                        <div class="card-body">
                            <!-- Ventas -->
                            <div class="resultado-item">
                                <span class="label">
                                    <i class="fas fa-arrow-up text-success me-2"></i> Ventas Totales
                                </span>
                                <span class="value positive">Bs. <?php echo number_format($ventas_totales, 2); ?></span>
                            </div>

                            <!-- Costo de Ventas -->
                            <div class="resultado-item" style="padding-left: 20px;">
                                <span class="label">
                                    <i class="fas fa-minus text-danger me-2"></i> (-) Costo de Ventas
                                    <span class="badge-porcentaje"><?php echo $ventas_totales > 0 ? number_format(($costo_ventas / $ventas_totales) * 100, 1) : 0; ?>%</span>
                                </span>
                                <span class="value negative">Bs. <?php echo number_format($costo_ventas, 2); ?></span>
                            </div>

                            <!-- Utilidad Bruta -->
                            <div class="resultado-item" style="border-top: 2px solid #e2e8f0; padding-top: 15px; margin-top: 5px;">
                                <span class="label fw-bold">
                                    <i class="fas fa-equals text-primary me-2"></i> = UTILIDAD BRUTA
                                    <span class="badge-porcentaje"><?php echo number_format($porcentaje_utilidad_bruta, 1); ?>%</span>
                                </span>
                                <span class="value <?php echo $utilidad_bruta >= 0 ? 'positive' : 'negative'; ?> total">
                                    Bs. <?php echo number_format($utilidad_bruta, 2); ?>
                                </span>
                            </div>

                            <!-- Gastos Operativos -->
                            <div class="resultado-item" style="padding-left: 20px;">
                                <span class="label">
                                    <i class="fas fa-minus text-danger me-2"></i> (-) Gastos Operativos
                                    <span class="badge-porcentaje"><?php echo $ventas_totales > 0 ? number_format(($gastos_operativos / $ventas_totales) * 100, 1) : 0; ?>%</span>
                                </span>
                                <span class="value negative">Bs. <?php echo number_format($gastos_operativos, 2); ?></span>
                            </div>

                            <!-- Utilidad Neta -->
                            <div class="resultado-item" style="border-top: 3px double #cbd5e1; padding-top: 15px; margin-top: 5px; background: <?php echo $utilidad_neta >= 0 ? '#f0fdf4' : '#fef2f2'; ?>; border-radius: 10px; padding: 15px;">
                                <span class="label fw-bold fs-5">
                                    <i class="fas fa-<?php echo $icono_utilidad_neta; ?> <?php echo $color_utilidad_neta; ?> me-2"></i> 
                                    = UTILIDAD NETA
                                    <span class="badge bg-<?php echo $utilidad_neta >= 0 ? 'success' : 'danger'; ?> text-white ms-2">
                                        <?php echo $utilidad_neta >= 0 ? 'GANANCIA' : 'PÉRDIDA'; ?>
                                    </span>
                                </span>
                                <span class="value <?php echo $color_utilidad_neta; ?> total fs-3">
                                    Bs. <?php echo number_format(abs($utilidad_neta), 2); ?>
                                </span>
                            </div>

                            <!-- Resumen adicional -->
                            <div class="row mt-4 pt-3" style="border-top: 1px solid #e2e8f0;">
                                <div class="col-md-4">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i> 
                                        Margen Bruto: <?php echo number_format($porcentaje_utilidad_bruta, 1); ?>%
                                    </small>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i> 
                                        Margen Neto: <?php echo number_format($porcentaje_utilidad_neta, 1); ?>%
                                    </small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i> 
                                        Período: <?php echo $meses[$mes_actual] . " " . $anio_actual; ?>
                                        <?php if ($id_empresa_filtro): ?>
                                            | <?php echo htmlspecialchars($nombre_emp ?? ''); ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Sin datos -->
                    <div class="resultado-card">
                        <div class="card-header">
                            <h3><i class="fas fa-file-invoice-dollar me-2"></i> Estado de Resultados</h3>
                        </div>
                        <div class="card-body">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <?php if ($id_empresa_filtro): ?>
                                    No hay datos de ingresos o gastos para esta empresa en el período seleccionado.
                                <?php else: ?>
                                    No hay datos de ingresos o gastos registrados en el sistema.
                                <?php endif; ?>
                                <p class="mt-2 text-muted" style="font-size: 0.85rem;">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Asegúrate de tener registros de ventas, costos y gastos en el período seleccionado.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <?php include('script.php'); ?>
</body>
</html>