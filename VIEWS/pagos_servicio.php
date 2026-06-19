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

// ============================================
// 4. PROCESAR REGISTRO DE PAGO
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar_pago'])) {
    $id_empresa = $_POST['id_empresa'];
    $monto = $_POST['monto_servicio'];
    $fecha_pago = $_POST['fecha_pago'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    
    $sql = "UPDATE empresas_clientes 
            SET servicio_activo = 1,
                fecha_ultimo_pago = '$fecha_pago',
                fecha_proximo_pago = '$fecha_vencimiento',
                monto_servicio = '$monto'
            WHERE id_empresa = '$id_empresa'";
    
    if ($conexion->query($sql)) {
        echo "<script>
            alert('✅ Pago registrado exitosamente.');
            window.location.href = 'pagos_servicio.php';
        </script>";
    } else {
        echo "<script>
            alert('❌ Error al registrar el pago: " . $conexion->error . "');
        </script>";
    }
}

// ============================================
// 5. OBTENER EMPRESAS CON SU ESTADO REAL
// ============================================
$query_empresas = "SELECT 
                        id_empresa, 
                        nombre_empresa, 
                        rif,
                        servicio_activo,
                        fecha_ultimo_pago,
                        fecha_proximo_pago,
                        monto_servicio,
                        CASE 
                            WHEN servicio_activo = 1 AND (fecha_proximo_pago IS NULL OR fecha_proximo_pago >= CURDATE()) THEN 'pagado'
                            WHEN servicio_activo = 1 AND fecha_proximo_pago < CURDATE() THEN 'vencido'
                            ELSE 'pendiente'
                        END as estado_pago
                    FROM empresas_clientes 
                    WHERE estado_activo = 1 
                    ORDER BY nombre_empresa ASC";
$result_empresas = mysqli_query($conexion, $query_empresas);
$empresas = [];
while ($row = mysqli_fetch_assoc($result_empresas)) {
    $empresas[] = $row;
}

// Contar estadísticas
$total_empresas = count($empresas);
$pagadas = 0;
$vencidas = 0;
$pendientes = 0;

foreach ($empresas as $emp) {
    if ($emp['estado_pago'] == 'pagado') $pagadas++;
    elseif ($emp['estado_pago'] == 'vencido') $vencidas++;
    else $pendientes++;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pagos de Servicio | Contable EA</title>
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/style_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../JAVASCRIPT/bootstrap.bundle.min.js"></script>
    <style>
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
            padding: 12px 20px;
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
            font-weight: 600;
            color: #1e293b;
            min-width: 200px;
        }
        .empresa-pago-item .rif {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-left: 10px;
        }
        .empresa-pago-item .estado {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 14px;
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
            font-size: 0.8rem;
            color: #64748b;
        }
        .empresa-pago-item .monto {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.9rem;
            min-width: 100px;
            text-align: right;
        }
        .btn-registrar {
            background: #10b981;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-registrar:hover {
            background: #059669;
            color: white;
            transform: scale(1.05);
        }
        .main-content {
            padding: 20px 25px;
        }
        .card-pagos {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
        .btn-volver {
            background: #667eea;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-volver:hover {
            background: #5a6fd6;
            color: white;
            transform: translateY(-2px);
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
                <a href="../VIEWS/libro_mayor.php"><i class="fas fa-chart-line"></i> Libro Mayor</a>
                <a href="../VIEWS/balance_comprobacion.php"><i class="fas fa-balance-scale"></i> Balance</a>
                <a href="../VIEWS/estado_resultados.php"><i class="fas fa-file-invoice-dollar"></i> Estado de Resultados</a>
                <a href="../VIEWS/empleados.php"><i class="fas fa-users"></i> Empleados</a>
                <a href="../VIEWS/catalogo_cuenta.php"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="../VIEWS/auditoria.php"><i class="fas fa-shield-alt"></i> Auditoría</a>
                <a href="../VIEWS/pagos_servicio.php" class="active"><i class="fas fa-hand-holding-usd"></i> Pagos Servicio</a>
            </nav>
        </aside>

        <main class="viewport">
            <?php include('header.php'); ?>

            <div class="main-content">
                
                <!-- Encabezado -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3><i class="fas fa-hand-holding-usd text-success me-2"></i>Pagos de Servicio Contable</h3>
                    <a href="inicio.php" class="btn-volver">
                        <i class="fas fa-arrow-left"></i> Volver al Inicio
                    </a>
                </div>

                <!-- Estadísticas rápidas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-pago-box pagado">
                            <div class="numero"><?php echo $pagadas; ?></div>
                            <div class="label"><i class="fas fa-check-circle text-success"></i> Pagadas</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-pago-box pendiente">
                            <div class="numero"><?php echo $pendientes; ?></div>
                            <div class="label"><i class="fas fa-clock text-warning"></i> Pendientes</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-pago-box vencido">
                            <div class="numero"><?php echo $vencidas; ?></div>
                            <div class="label"><i class="fas fa-exclamation-circle text-danger"></i> Vencidas</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-pago-box" style="background: #eff6ff; border: 1px solid #93c5fd;">
                            <div class="numero" style="color: #1e40af;"><?php echo $total_empresas; ?></div>
                            <div class="label"><i class="fas fa-building text-primary"></i> Total Empresas</div>
                        </div>
                    </div>
                </div>

                <!-- Lista de empresas -->
                <div class="card-pagos">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Empresas y su Estado de Pago</h5>
                        <button class="btn btn-success" style="background: #10b981; border: none; padding: 8px 20px; border-radius: 10px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalRegistroPago">
                            <i class="fas fa-plus-circle me-1"></i> Registrar Pago
                        </button>
                    </div>
                    
                    <div style="max-height: 500px; overflow-y: auto;">
                        <?php if (!empty($empresas)): ?>
                            <?php foreach ($empresas as $emp): ?>
                                <?php
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
                                    <div class="d-flex align-items-center gap-4">
                                        <span class="fechas">
                                            <i class="fas fa-calendar-alt"></i> 
                                            Último: <?php echo $emp['fecha_ultimo_pago'] ? date('d/m/Y', strtotime($emp['fecha_ultimo_pago'])) : 'N/A'; ?>
                                            <i class="fas fa-arrow-right mx-1"></i>
                                            Vence: <?php echo $emp['fecha_proximo_pago'] ? date('d/m/Y', strtotime($emp['fecha_proximo_pago'])) : 'N/A'; ?>
                                        </span>
                                        <span class="monto">Bs. <?php echo number_format($emp['monto_servicio'], 2); ?></span>
                                        <span class="estado <?php echo $clase_estado; ?>"><?php echo $texto_estado; ?></span>
                                        <button class="btn-registrar" data-bs-toggle="modal" data-bs-target="#modalRegistroPago" 
                                                onclick="cargarEmpresa(<?php echo $emp['id_empresa']; ?>, '<?php echo htmlspecialchars($emp['nombre_empresa']); ?>', <?php echo $emp['monto_servicio']; ?>)">
                                            <i class="fas fa-edit"></i> Registrar
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x d-block mb-3" style="color: #cbd5e1;"></i>
                                No hay empresas registradas
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- ============================================ -->
    <!-- MODAL PARA REGISTRAR PAGO                    -->
    <!-- ============================================ -->
    <div class="modal fade" id="modalRegistroPago" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #334155); color: white; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title">
                        <i class="fas fa-hand-holding-usd me-2" style="color: #34d399;"></i> 
                        Registrar Pago de Servicio
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="pagos_servicio.php">
                    <div class="modal-body" style="padding: 25px;">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Empresa</label>
                            <select name="id_empresa" id="modal_id_empresa" class="form-select" required>
                                <option value="">Seleccione una empresa...</option>
                                <?php foreach ($empresas as $emp): ?>
                                    <option value="<?php echo $emp['id_empresa']; ?>">
                                        <?php echo htmlspecialchars($emp['nombre_empresa']); ?> 
                                        (<?php echo htmlspecialchars($emp['rif']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Monto del Servicio (Bs.)</label>
                            <input type="number" step="0.01" min="0" name="monto_servicio" id="modal_monto" class="form-control" placeholder="0.00" required value="100.00">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Fecha de Pago</label>
                            <input type="date" name="fecha_pago" id="modal_fecha_pago" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Fecha de Vencimiento (Próximo Pago)</label>
                            <input type="date" name="fecha_vencimiento" id="modal_fecha_vencimiento" class="form-control" value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>" required>
                            <small class="text-muted"><i class="fas fa-info-circle"></i> El servicio estará activo hasta esta fecha</small>
                        </div>
                        
                        <div class="alert alert-info" style="background: #f0fdf4; border-color: #bbf7d0; color: #166534; font-size: 0.85rem;">
                            <i class="fas fa-info-circle me-2"></i>
                            Al registrar el pago, la empresa quedará con servicio activo hasta la fecha de vencimiento seleccionada.
                        </div>
                    </div>
                    <div class="modal-footer" style="background: #f8fafc; border-radius: 0 0 15px 15px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="registrar_pago" class="btn btn-success" style="background: #10b981; border: none; padding: 10px 25px; font-weight: bold;">
                            <i class="fas fa-save me-1"></i> Registrar Pago
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include('script.php'); ?>

    <script>
        function cargarEmpresa(id, nombre, monto) {
            document.getElementById('modal_id_empresa').value = id;
            document.getElementById('modal_monto').value = monto || 100.00;
        }

        document.getElementById('modal_fecha_pago').addEventListener('change', function() {
            var fechaPago = this.value;
            if (fechaPago) {
                var fecha = new Date(fechaPago);
                fecha.setMonth(fecha.getMonth() + 1);
                var year = fecha.getFullYear();
                var month = String(fecha.getMonth() + 1).padStart(2, '0');
                var day = String(fecha.getDate()).padStart(2, '0');
                document.getElementById('modal_fecha_vencimiento').value = year + '-' + month + '-' + day;
            }
        });
    </script>
</body>
</html>