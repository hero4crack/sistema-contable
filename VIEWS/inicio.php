<?php
include_once '../BACKEND/conexion_login.php';
include_once '../BACKEND/consulta_factura.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../VIEWS/index.php");
    exit();
}

// Obtener datos de pagos
$empresas_pago = obtenerEstadoPagos($conexion);

// Contar estadísticas
$total_empresas = count($empresas_pago);
$pagadas = 0;
$vencidas = 0;
$pendientes = 0;

foreach ($empresas_pago as $emp) {
    if ($emp['estado_pago'] == 'pagado') $pagadas++;
    elseif ($emp['estado_pago'] == 'vencido') $vencidas++;
    else $pendientes++;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Entidades | Contable EA</title>
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/style_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../JAVASCRIPT/bootstrap.bundle.min.js"></script>
    <style>
        .welcome-section {
            padding: 30px 20px;
        }

        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 40px;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .welcome-card::after {
            content: '';
            position: absolute;
            bottom: -40%;
            left: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .welcome-card h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .welcome-card p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }

        .welcome-card .icon-decoration {
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 5rem;
            opacity: 0.15;
            z-index: 0;
        }

        .quick-actions {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .quick-actions h4 {
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .quick-actions h4 i {
            color: #667eea;
            margin-right: 10px;
        }

        .btn-quick {
            padding: 15px 25px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid #e2e8f0;
            background: white;
            color: #2d3748;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            min-width: 180px;
            justify-content: center;
        }

        .btn-quick:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
            color: #667eea;
            background: #f7fafc;
            text-decoration: none;
        }

        .btn-quick i {
            font-size: 1.2rem;
            color: #667eea;
        }

        .btn-quick:hover i {
            color: #667eea;
        }

        .btn-quick.primary {
            background: #667eea;
            border-color: #667eea;
            color: white;
        }

        .btn-quick.primary i {
            color: white;
        }

        .btn-quick.primary:hover {
            background: #5a6fd6;
            border-color: #5a6fd6;
            color: white;
        }

        .btn-quick.pago {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .btn-quick.pago i {
            color: #10b981;
        }

        .btn-quick.pago:hover {
            background: #10b981;
            border-color: #10b981;
            color: white;
        }

        .btn-quick.pago:hover i {
            color: white;
        }

        .grid-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .main-content {
            padding: 20px 25px;
        }

        .date-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 18px;
            border-radius: 30px;
            font-size: 0.85rem;
            display: inline-block;
            margin-top: 10px;
            position: relative;
            z-index: 1;
        }

        .date-badge i {
            margin-right: 8px;
        }

        /* ============================================ */
        /* ESTILOS PARA EL PANEL DE PAGOS               */
        /* ============================================ */
        .pagos-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .pagos-card h4 {
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .pagos-card h4 i {
            color: #10b981;
            margin-right: 10px;
        }

        .stat-pago-box {
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s;
        }

        .stat-pago-box:hover {
            transform: translateY(-3px);
        }

        .stat-pago-box .numero {
            font-size: 2rem;
            font-weight: 700;
        }

        .stat-pago-box .label {
            font-size: 0.8rem;
            color: #64748b;
        }

        .stat-pago-box.pagado {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }
        .stat-pago-box.pagado .numero { color: #166534; }

        .stat-pago-box.pendiente {
            background: #fffbeb;
            border: 1px solid #fde68a;
        }
        .stat-pago-box.pendiente .numero { color: #92400e; }

        .stat-pago-box.vencido {
            background: #fef2f2;
            border: 1px solid #fca5a5;
        }
        .stat-pago-box.vencido .numero { color: #991b1b; }

        .empresa-pago-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            border-radius: 10px;
            background: #f8fafc;
            margin-bottom: 8px;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .empresa-pago-item:hover {
            background: #f1f5f9;
        }

        .empresa-pago-item .nombre {
            font-weight: 500;
            color: #1e293b;
        }

        .empresa-pago-item .rif {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-left: 10px;
        }

        .empresa-pago-item .estado {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 3px 12px;
            border-radius: 30px;
        }

        .estado-pagado {
            background: #bbf7d0;
            color: #166534;
        }
        .estado-pendiente {
            background: #fde68a;
            color: #92400e;
        }
        .estado-vencido {
            background: #fca5a5;
            color: #991b1b;
        }

        .empresa-pago-item .fechas {
            font-size: 0.7rem;
            color: #64748b;
        }

        .empresa-pago-item .monto {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.85rem;
        }

        .scroll-pagos {
            max-height: 300px;
            overflow-y: auto;
        }

        .scroll-pagos::-webkit-scrollbar {
            width: 4px;
        }
        .scroll-pagos::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .scroll-pagos::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .btn-ver-pagos {
            background: #10b981;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
        }
        .btn-ver-pagos:hover {
            background: #059669;
            color: white;
        }
    </style>
</head>
<body>
    <div class="app-container">
        
        <!-- Sidebar -->
        <aside class="main-sidebar">
            <div class="brand"><img src="../IMG/logo_empresa-sinfondo.png" alt="#" class="mi-imagen"></div>
            <nav class="menu">
                <a href="../VIEWS/inicio.php" class="active"><i class="fas fa-home"></i> Inicio</a>
                <a href="../VIEWS/empresas_clientes.php"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="../VIEWS/registro_proveedor.php"><i class="fas fa-truck"></i> Proveedores</a>
                <a href="../VIEWS/libro_facturas.php"><i class="fas fa-file-invoice"></i> Libro de Facturas</a>
                <a href="../VIEWS/asientos_diario.php"><i class="fas fa-book"></i> Asientos Diario</a>
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
                <!-- MENSAJE DE BIENVENIDA                        -->
                <!-- ============================================ -->
                <div class="welcome-card">
                    <i class="fas fa-chart-line icon-decoration"></i>
                    <h1>¡Bienvenido al Sistema Contable!</h1>
                    <p>Gestiona tus entidades, facturas y registros contables de manera eficiente.</p>
                    <span class="date-badge">
                        <i class="fas fa-calendar-alt"></i> <?= date('d/m/Y') ?>
                    </span>
                </div>

                <!-- ============================================ -->
                <!-- PANEL DE ESTADO DE PAGOS                     -->
                <!-- ============================================ -->
                <div class="pagos-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-hand-holding-usd"></i> Estado de Pagos - Servicios Contables</h4>
                        <a href="../VIEWS/pagos_servicio.php" class="btn-ver-pagos">
                            <i class="fas fa-arrow-right me-1"></i> Ver todos
                        </a>
                    </div>
                    
                    <!-- Estadísticas rápidas -->
                    <div class="row mb-4">
                        <div class="col-4">
                            <div class="stat-pago-box pagado">
                                <div class="numero"><?php echo $pagadas; ?></div>
                                <div class="label"><i class="fas fa-check-circle text-success"></i> Pagadas</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-pago-box pendiente">
                                <div class="numero"><?php echo $pendientes; ?></div>
                                <div class="label"><i class="fas fa-clock text-warning"></i> Pendientes</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-pago-box vencido">
                                <div class="numero"><?php echo $vencidas; ?></div>
                                <div class="label"><i class="fas fa-exclamation-circle text-danger"></i> Vencidas</div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de empresas -->
                    <div class="scroll-pagos">
                        <?php if (!empty($empresas_pago)): ?>
                            <?php 
                            // Mostrar solo las primeras 5 empresas en el dashboard
                            $contador = 0;
                            foreach ($empresas_pago as $emp): 
                                if ($contador >= 5) break;
                                $contador++;
                                $estado = $emp['estado_pago'];
                                $clase_estado = '';
                                $texto_estado = '';
                                $color_borde = '';

                                if ($estado == 'pagado') {
                                    $clase_estado = 'estado-pagado';
                                    $texto_estado = '✅ Pagado';
                                    $color_borde = '#22c55e';
                                } elseif ($estado == 'vencido') {
                                    $clase_estado = 'estado-vencido';
                                    $texto_estado = '⚠️ Vencido';
                                    $color_borde = '#ef4444';
                                } else {
                                    $clase_estado = 'estado-pendiente';
                                    $texto_estado = '⏳ Pendiente';
                                    $color_borde = '#f59e0b';
                                }
                            ?>
                                <div class="empresa-pago-item" style="border-left-color: <?php echo $color_borde; ?>;">
                                    <div>
                                        <span class="nombre"><?php echo htmlspecialchars($emp['nombre_empresa']); ?></span>
                                        <span class="rif"><?php echo htmlspecialchars($emp['rif']); ?></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="fechas">
                                            <i class="fas fa-calendar-alt"></i> 
                                            <?php echo $emp['fecha_ultimo_pago'] ? date('d/m/Y', strtotime($emp['fecha_ultimo_pago'])) : 'N/A'; ?>
                                            <i class="fas fa-arrow-right mx-1"></i>
                                            <?php echo $emp['fecha_proximo_pago'] ? date('d/m/Y', strtotime($emp['fecha_proximo_pago'])) : 'N/A'; ?>
                                        </span>
                                        <span class="monto">Bs. <?php echo number_format($emp['monto_servicio'], 2); ?></span>
                                        <span class="estado <?php echo $clase_estado; ?>"><?php echo $texto_estado; ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if ($total_empresas > 5): ?>
                                <div class="text-center mt-2">
                                    <small class="text-muted">Mostrando 5 de <?php echo $total_empresas; ?> empresas. 
                                        <a href="../VIEWS/pagos_servicio.php" class="text-success">Ver todas</a>
                                    </small>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-inbox fa-2x d-block mb-2" style="color: #cbd5e1;"></i>
                                No hay empresas registradas
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- ACCESOS RÁPIDOS                              -->
                <!-- ============================================ -->
                <div class="quick-actions">
                    <h4><i class="fas fa-bolt"></i> Accesos Rápidos</h4>
                    <div class="grid-actions">
                        <a href="../VIEWS/empresas_clientes.php" class="btn-quick">
                            <i class="fas fa-building"></i> Empresas
                        </a>
                        <a href="../VIEWS/registro_proveedor.php" class="btn-quick">
                            <i class="fas fa-truck"></i> Proveedores
                        </a>
                        <a href="../VIEWS/libro_facturas.php" class="btn-quick">
                            <i class="fas fa-file-invoice"></i> Facturas
                        </a>
                        <a href="../VIEWS/asientos_diario.php" class="btn-quick">
                            <i class="fas fa-book"></i> Asientos
                        </a>
                        <a href="../VIEWS/empleados.php" class="btn-quick">
                            <i class="fas fa-users"></i> Empleados
                        </a>
                        <a href="../VIEWS/pagos_servicio.php" class="btn-quick pago">
                            <i class="fas fa-hand-holding-usd"></i> Pagos Servicio
                        </a>
                        <a href="../VIEWS/catalogo_cuenta.php" class="btn-quick primary">
                            <i class="fas fa-list-ol"></i> Catálogo Cuentas
                        </a>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <?php include('script.php'); ?>
</body>
</html>