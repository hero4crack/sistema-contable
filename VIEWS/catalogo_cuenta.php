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
require_once '../BACKEND/consulta_catalogo.php';

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
// 6. OBTENER DATOS DEL CATÁLOGO
// ============================================
$datos_catalogo = obtenerCatalogo($conexion, $id_empresa_filtro);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Cuentas | Contable EA</title>
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
        .table-catalogo thead th {
            background: #f1f5f9;
            color: #1e293b;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 15px;
            border-bottom: 2px solid #e2e8f0;
        }
        .table-catalogo tbody td {
            padding: 10px 15px;
            vertical-align: middle;
        }
        .table-catalogo tbody tr:hover {
            background: #f8fafc;
        }
        .nivel-1 { font-weight: 700; background-color: #f8fafc; }
        .nivel-2 { font-weight: 600; }
        .nivel-3 { font-weight: 400; }
        .nivel-4 { font-weight: 300; font-size: 0.9rem; }
        .nivel-5 { font-weight: 300; font-size: 0.85rem; color: #64748b; }
        .codigo-cuenta {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: #1e293b;
        }

        /* ============================================ */
        /* BADGES                                       */
        /* ============================================ */
        .badge-tipo {
            padding: 4px 14px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
        .badge-tipo.activo { background: #dbeafe; color: #1e40af; }
        .badge-tipo.pasivo { background: #fecaca; color: #991b1b; }
        .badge-tipo.patrimonio { background: #d1fae5; color: #065f46; }
        .badge-tipo.ingreso { background: #fef3c7; color: #92400e; }
        .badge-tipo.egreso { background: #fce4ec; color: #c62828; }
        
        .badge-movimiento {
            padding: 4px 14px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
        .badge-movimiento.permite { background: #bbf7d0; color: #166534; }
        .badge-movimiento.no-permite { background: #fee2e2; color: #991b1b; }

        /* ============================================ */
        /* BOTONES - ESTILOS MEJORADOS                  */
        /* ============================================ */
        .btn-agregar {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        .btn-agregar:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            color: white;
            background: linear-gradient(135deg, #059669, #047857);
        }
        .btn-agregar i {
            margin-right: 8px;
        }

        .btn-editar-cuenta {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.25);
        }
        .btn-editar-cuenta:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.35);
            color: white;
            background: linear-gradient(135deg, #d97706, #b45309);
        }
        .btn-editar-cuenta i {
            margin-right: 5px;
        }

        .btn-eliminar-cuenta {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.25);
        }
        .btn-eliminar-cuenta:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.35);
            color: white;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
        }
        .btn-eliminar-cuenta i {
            margin-right: 5px;
        }

        /* ============================================ */
        /* MODALES - CAMPOS MEJORADOS                   */
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
        .modal .form-control:hover,
        .modal .form-select:hover {
            border-color: #94a3b8;
        }

        .btn-modal-guardar {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        .btn-modal-guardar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
            color: white;
        }
        .btn-modal-guardar i {
            margin-right: 8px;
        }

        .btn-modal-crear {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        .btn-modal-crear:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            color: white;
        }
        .btn-modal-crear i {
            margin-right: 8px;
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
        .dataTables_wrapper .dataTables_filter input:hover {
            border-color: #94a3b8;
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
        .dataTables_wrapper .dataTables_length select:hover {
            border-color: #94a3b8;
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
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            transform: none;
            box-shadow: none;
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
                <a href="../VIEWS/balance_comprobacion.php"><i class="fas fa-balance-scale"></i> Balance</a>
                <a href="../VIEWS/estado_resultados.php"><i class="fas fa-file-invoice-dollar"></i> Estado de Resultados</a>
                <a href="../VIEWS/empleados.php"><i class="fas fa-users"></i> Empleados</a>
                <a href="../VIEWS/catalogo_cuenta.php" class="active"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="../VIEWS/auditoria.php"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="viewport">
            <?php include_once('header.php'); ?>

            <div class="main-content">
                
                <!-- ============================================ -->
                <!-- FILTROS                                      -->
                <!-- ============================================ -->
                <div class="filter-box">
                    <form method="GET" action="catalogo_cuenta.php" class="row align-items-end g-2">
                        
                        <div class="col-md-4">
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
                <!-- CATÁLOGO DE CUENTAS                          -->
                <!-- ============================================ -->
                <div class="table-wrapper">
                    <div class="table-header">
                        <h4><i class="fas fa-list-ol"></i> Catálogo de Cuentas</h4>
                        <button class="btn-agregar" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                            <i class="fas fa-plus-circle"></i> Nueva Cuenta
                        </button>
                    </div>

                    <table id="tablaCatalogo" class="table table-catalogo table-hover">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Código</th>
                                <th>Nombre de la Cuenta</th>
                                <th style="width: 130px;">Tipo</th>
                                <th style="width: 80px;">Nivel</th>
                                <th style="width: 130px;">Movimiento</th>
                                <th style="width: 200px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($datos_catalogo && $datos_catalogo->num_rows > 0): ?>
                                <?php while ($cuenta = $datos_catalogo->fetch_assoc()): ?>
                                    <?php 
                                    $sangria = ($cuenta['nivel'] - 1) * 25;
                                    $clase_nivel = 'nivel-' . $cuenta['nivel'];
                                    $tipo_clase = strtolower($cuenta['tipo_cuenta']);
                                    $tipo_clase = $tipo_clase == 'egreso' ? 'egreso' : $tipo_clase;
                                    $movimiento_clase = $cuenta['permite_movimiento'] == 1 ? 'permite' : 'no-permite';
                                    $movimiento_texto = $cuenta['permite_movimiento'] == 1 ? '✅ Sí' : '❌ No';
                                    ?>
                                    <tr class="<?php echo $clase_nivel; ?>">
                                        <td><span class="codigo-cuenta"><?php echo htmlspecialchars($cuenta['codigo_cuenta']); ?></span></td>
                                        <td style="padding-left: <?php echo $sangria; ?>px;">
                                            <?php echo htmlspecialchars($cuenta['nombre_cuenta']); ?>
                                        </td>
                                        <td>
                                            <span class="badge-tipo <?php echo $tipo_clase; ?>">
                                                <?php echo htmlspecialchars($cuenta['tipo_cuenta']); ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?php echo $cuenta['nivel']; ?></td>
                                        <td>
                                            <span class="badge-movimiento <?php echo $movimiento_clase; ?>">
                                                <?php echo $movimiento_texto; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn-editar-cuenta" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $cuenta['id_cuenta']; ?>">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <a href="../BACKEND/eliminar_cuenta.php?id=<?php echo $cuenta['id_cuenta']; ?>" 
                                               class="btn-eliminar-cuenta" 
                                               onclick="return confirm('¿Estás seguro de eliminar esta cuenta?')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </a>

                                            <!-- Modal de Edición -->
                                            <div class="modal fade" id="editModal<?php echo $cuenta['id_cuenta']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
                                                            <h5 class="modal-title">
                                                                <i class="fas fa-edit me-2"></i> Editar Cuenta #<?php echo $cuenta['id_cuenta']; ?>
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="../BACKEND/actualizar_cuenta.php">
                                                            <input type="hidden" name="id" value="<?php echo $cuenta['id_cuenta']; ?>">
                                                            <div class="modal-body">
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Código de Cuenta</label>
                                                                        <input class="form-control" type="text" name="codigo" value="<?php echo htmlspecialchars($cuenta['codigo_cuenta']); ?>" required>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Nombre de Cuenta</label>
                                                                        <input class="form-control" type="text" name="cuenta" value="<?php echo htmlspecialchars($cuenta['nombre_cuenta']); ?>" required>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label class="form-label">Nivel</label>
                                                                        <input class="form-control" type="number" name="nivel" value="<?php echo $cuenta['nivel']; ?>" required min="1" max="5">
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label class="form-label">Tipo</label>
                                                                        <select class="form-select" name="tipo">
                                                                            <option value="Activo" <?php echo ($cuenta['tipo_cuenta'] == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                                                                            <option value="Pasivo" <?php echo ($cuenta['tipo_cuenta'] == 'Pasivo') ? 'selected' : ''; ?>>Pasivo</option>
                                                                            <option value="Patrimonio" <?php echo ($cuenta['tipo_cuenta'] == 'Patrimonio') ? 'selected' : ''; ?>>Patrimonio</option>
                                                                            <option value="Ingreso" <?php echo ($cuenta['tipo_cuenta'] == 'Ingreso') ? 'selected' : ''; ?>>Ingreso</option>
                                                                            <option value="Egreso" <?php echo ($cuenta['tipo_cuenta'] == 'Egreso') ? 'selected' : ''; ?>>Egreso</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label class="form-label">Permite Movimiento</label>
                                                                        <select class="form-select" name="movimiento" required>
                                                                            <option value="1" <?php echo ($cuenta['permite_movimiento'] == 1) ? 'selected' : ''; ?>>✅ Sí</option>
                                                                            <option value="0" <?php echo ($cuenta['permite_movimiento'] == 0) ? 'selected' : ''; ?>>❌ No</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn-modal-cancelar" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn-modal-guardar">
                                                                    <i class="fas fa-save"></i> Guardar Cambios
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x d-block mb-2" style="color: #cbd5e1;"></i>
                                        No hay cuentas registradas en el catálogo.
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
    <!-- MODAL PARA AGREGAR NUEVA CUENTA              -->
    <!-- ============================================ -->
    <div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i> Nueva Cuenta Contable
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="../BACKEND/agregar_cuenta.php">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Código de Cuenta</label>
                                <input class="form-control" type="text" name="codigo" placeholder="Ej: 1101" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre de Cuenta</label>
                                <input class="form-control" type="text" name="cuenta" placeholder="Ej: Caja" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nivel</label>
                                <input class="form-control" type="number" name="nivel" value="1" required min="1" max="5">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tipo</label>
                                <select class="form-select" name="tipo" required>
                                    <option value="Activo">Activo</option>
                                    <option value="Pasivo">Pasivo</option>
                                    <option value="Patrimonio">Patrimonio</option>
                                    <option value="Ingreso">Ingreso</option>
                                    <option value="Egreso">Egreso</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Permite Movimiento</label>
                                <select class="form-select" name="movimiento" required>
                                    <option value="1">✅ Sí</option>
                                    <option value="0">❌ No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-cancelar" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn-modal-crear">
                            <i class="fas fa-save"></i> Crear Cuenta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include('script.php'); ?>

    <script>
        $(document).ready(function() {
            $('#tablaCatalogo').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                },
                "pageLength": 10,
                "order": [[0, 'asc']],
                "columnDefs": [
                    { "orderable": false, "targets": [5] }
                ]
            });
        });
    </script>
</body>
</html>