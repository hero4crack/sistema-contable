<?php
// ============================================
// 1. PRIMERO: Incluir sesión (siempre al inicio)
// ============================================
include_once '../BACKEND/conexion_login.php';

// 2. Verificar autenticación
if (!isset($_SESSION['usuario'])) {
    header("Location: ../VIEWS/index.php");
    exit();
}

// 3. Incluir los demás archivos
require_once '../BACKEND/conecxion_bd.php';
require_once '../BACKEND/consulta_mayor.php';

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
// 5. CAPTURAR FILTROS
// ============================================
$id_empresa_filtro = isset($_GET['id_empresa']) ? $_GET['id_empresa'] : '';

// ============================================
// 6. OBTENER DATOS DEL LIBRO MAYOR
// ============================================
$saldos = obtenerLibroMayor($conexion, $id_empresa_filtro);

// Variables para totales
$total_debe = 0;
$total_haber = 0;
$total_saldo = 0;
$contador = 0;

// Guardar datos en array para procesar
$datos = [];
if ($saldos && $saldos->num_rows > 0) {
    while ($row = $saldos->fetch_assoc()) {
        $datos[] = $row;
        $total_debe += $row['total_debe'];
        $total_haber += $row['total_haber'];
        $total_saldo += $row['saldo'];
        $contador++;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Libro Mayor | Contable EA</title>
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
        
        .card-mayor {
            border-radius: 20px;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .card-mayor .card-header {
            background: linear-gradient(135deg, #1e293b, #334155);
            color: white;
            padding: 18px 25px;
            border-bottom: none;
        }
        
        .card-mayor .card-header h3 {
            font-weight: 600;
        }
        
        .table-mayor {
            margin-bottom: 0;
        }
        
        .table-mayor thead th {
            background: #f1f5f9;
            color: #1e293b;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 15px;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }
        
        .table-mayor tbody td {
            padding: 10px 15px;
            vertical-align: middle;
        }
        
        .table-mayor tbody tr:hover {
            background: #f8fafc;
        }
        
        .table-mayor tbody tr:last-child td {
            border-bottom: none;
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
        
        .badge-estado {
            padding: 5px 15px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.7rem;
            min-width: 80px;
            display: inline-block;
            text-align: center;
        }
        
        .badge-deudor {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .badge-acreedor {
            background: #fecaca;
            color: #991b1b;
        }
        
        .badge-saldada {
            background: #e5e7eb;
            color: #4b5563;
        }
        
        .sin-datos {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
        }
        
        .sin-datos i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 15px;
            color: #cbd5e1;
        }
        
        .table-wrapper {
            overflow-x: auto;
            padding: 0 5px;
        }
        
        .total-badge {
            background: rgba(255,255,255,0.15);
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 0.8rem;
        }
        
        .total-badge i {
            margin-right: 5px;
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
        
        .main-content {
            padding: 20px 25px;
        }
    </style>
</head>
<body>
    <div class="app-container">
        
        <aside class="main-sidebar">
            <div class="brand"><img src="../IMG/logo_empresa-sinfondo.png" alt="#" class="mi-imagen"></div>
            <nav class="menu">
                <a href="../VIEWS/inicio.php"><i class="fas fa-home"></i> Inicio</a>
                <a href="../VIEWS/empresas_clientes.php"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="../VIEWS/registro_proveedor.php"><i class="fas fa-truck"></i> Proveedores</a>
                <a href="../VIEWS/libro_facturas.php"><i class="fas fa-file-invoice"></i> Libro de Facturas</a>
                <a href="../VIEWS/asientos_diario.php"><i class="fas fa-book"></i> Asientos Diario</a>
                <a href="../VIEWS/libro_mayor.php" class="active"><i class="fas fa-chart-line"></i> Libro Mayor</a>
                <a href="../VIEWS/balance_comprobacion.php"><i class="fas fa-balance-scale"></i> Balance</a>
                <a href="../VIEWS/estado_resultados.php"><i class="fas fa-file-invoice-dollar"></i> Estado de Resultados</a>
                <a href="../VIEWS/empleados.php"><i class="fas fa-users"></i> Empleados</a>
                <a href="../VIEWS/catalogo_cuenta.php"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="../VIEWS/auditoria.php"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>

        <main class="viewport">
            <?php include('header.php'); ?>

            <div class="main-content">
                
                <!-- ============================================ -->
                <!-- FILTROS                                      -->
                <!-- ============================================ -->
                <div class="filter-box">
                    <form method="GET" action="libro_mayor.php" class="row align-items-end g-2">
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.8rem;">
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
                            <button type="submit" class="btn btn-dark w-100 fw-bold">
                                <i class="fas fa-filter me-1"></i> Filtrar
                            </button>
                        </div>

                        <div class="col text-end">
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
                                <span class="empresa-info">
                                    <i class="fas fa-building"></i> 
                                    Mostrando: <strong><?php echo htmlspecialchars($nombre_emp); ?></strong>
                                </span>
                            <?php else: ?>
                                <span class="empresa-info" style="background: #eff6ff; border-color: #93c5fd; color: #1e40af;">
                                    <i class="fas fa-globe"></i> Mostrando: <strong>Todas las empresas</strong>
                                </span>
                            <?php endif; ?>
                        </div>

                    </form>
                </div>

                <!-- ============================================ -->
                <!-- TABLA DEL LIBRO MAYOR                        -->
                <!-- ============================================ -->
                <div class="card card-mayor">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i> Libro Mayor
                        </h3>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="total-badge">
                                <i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y'); ?>
                            </span>
                            <span class="total-badge">
                                <i class="fas fa-file-invoice"></i> <?php echo $contador; ?> cuentas
                            </span>
                        </div>
                    </div>
                    
                    <div class="table-wrapper">
                        <table class="table table-mayor">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Código</th>
                                    <th>Cuenta Contable</th>
                                    <th class="text-end">Total Debe</th>
                                    <th class="text-end">Total Haber</th>
                                    <th class="text-end">Saldo Final</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($datos)): ?>
                                    <?php 
                                    $fila = 1;
                                    foreach ($datos as $row): 
                                        $saldo = $row['saldo'];
                                        
                                        if ($saldo > 0) {
                                            $textoSaldo = "DEUDOR";
                                            $claseBadge = "badge-deudor";
                                            $claseMonto = "text-primary fw-bold";
                                        } elseif ($saldo < 0) {
                                            $textoSaldo = "ACREEDOR";
                                            $claseBadge = "badge-acreedor";
                                            $claseMonto = "text-danger fw-bold";
                                        } else {
                                            $textoSaldo = "SALDADA";
                                            $claseBadge = "badge-saldada";
                                            $claseMonto = "text-muted";
                                        }
                                    ?>
                                        <tr>
                                            <td class="text-center text-muted"><?php echo $fila; ?></td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($row['codigo_cuenta'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($row['nombre_cuenta'] ?? 'Sin nombre'); ?></td>
                                            <td class="text-end text-success fw-semibold">
                                                Bs. <?php echo number_format($row['total_debe'] ?? 0, 2, ',', '.'); ?>
                                            </td>
                                            <td class="text-end text-danger fw-semibold">
                                                Bs. <?php echo number_format($row['total_haber'] ?? 0, 2, ',', '.'); ?>
                                            </td>
                                            <td class="text-end <?php echo $claseMonto; ?>">
                                                Bs. <?php echo number_format(abs($saldo), 2, ',', '.'); ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge-estado <?php echo $claseBadge; ?>">
                                                    <?php echo $textoSaldo; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php 
                                        $fila++;
                                        endforeach; 
                                    ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7">
                                            <div class="sin-datos">
                                                <i class="fas fa-inbox"></i>
                                                <?php if ($id_empresa_filtro): ?>
                                                    No hay movimientos contables para esta empresa.
                                                <?php else: ?>
                                                    No hay movimientos contables registrados en el sistema.
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <?php if (!empty($datos)): ?>
                            <tfoot class="totales-footer">
                                <tr>
                                    <td colspan="3" class="text-end text-uppercase">
                                        <i class="fas fa-calculator me-2"></i> TOTALES GENERALES:
                                    </td>
                                    <td class="text-end text-success fw-bold">
                                        Bs. <?php echo number_format($total_debe, 2, ',', '.'); ?>
                                    </td>
                                    <td class="text-end text-danger fw-bold">
                                        Bs. <?php echo number_format($total_haber, 2, ',', '.'); ?>
                                    </td>
                                    <td class="text-end fw-bold" style="color: #1e293b;">
                                        Bs. <?php echo number_format($total_saldo, 2, ',', '.'); ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">
                                            <?php echo $contador; ?> cuentas
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <?php include('script.php'); ?>
</body>
</html>