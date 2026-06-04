<?php
require_once '../BACKEND/conecxion_bd.php';
require_once '../BACKEND/consulta_mayor.php';
$saldos = obtenerLibroMayor($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Libro Mayor | Contable EA</title>
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/style_cliente.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../JAVASCRIPT/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
</head>
<body>
    <div class="app-container">
        
        <aside class="main-sidebar">
            <div class="brand"><img src="../IMG/logo_empresa-sinfondo.png" alt="#" class="mi-imagen"></div>
            <nav class="menu">
                <a href="../VIEWS/inicio.php"><i class="fas fa-home"></i> Inicio</a>
                <a href="../VIEWS/empresas_clientes.php"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="../VIEWS/registro_proveedor.php"><i class="fas fa-city"></i> Proveedores</a>
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
            <?php include('header.php'); ?>
            <section class="content p-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Libro Mayor - Resumen de Saldos</h3>
                        <span class="badge bg-light text-primary"><?php echo date('d/m/Y'); ?></span>
                    </div>
                    <div class="table-responsive p-3">
                        <table id="tabla_mayor" class="table table-striped table-hover border align-middle">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Fecha Op.</th>
                                    <th>Código</th>
                                    <th>Cuenta Contable</th>
                                    <th class="text-end">Total Debe</th>
                                    <th class="text-end">Total Haber</th>
                                    <th class="text-end">Saldo Final</th>
                                    <th>Estado de Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $saldos->fetch_assoc()): 
                                    $saldo = $row['saldo'];
                                    
                                    if ($saldo > 0) {
                                        $textoSaldo = "DEUDOR";
                                        $claseBadge = "bg-primary";
                                        $claseMonto = "text-primary";
                                    } elseif ($saldo < 0) {
                                        $textoSaldo = "ACREEDOR";
                                        $claseBadge = "bg-danger";
                                        $claseMonto = "text-danger";
                                    } else {
                                        $textoSaldo = "SALDADA";
                                        $claseBadge = "bg-secondary";
                                        $claseMonto = "text-muted";
                                    }
                                ?>
                                    <tr>
                                        <td class="text-center small">
                                            <?= (isset($row['fecha_operacion']) && !empty($row['fecha_operacion'])) ? date("d/m/Y", strtotime($row['fecha_operacion'])) : 'Sin fecha' ?>
                                        </td>
                                        <td class="text-center"><?= $row['codigo_cuenta'] ?></td>
                                        <td class="fw-bold"><?= $row['nombre_cuenta'] ?></td>
                                        <td class="text-end text-success"><?= number_format($row['total_debe'], 2, ',', '.') ?></td>
                                        <td class="text-end text-danger"><?= number_format($row['total_haber'], 2, ',', '.') ?></td>
                                        <td class="text-end fw-bold <?= $claseMonto ?>">
                                            <?= number_format(abs($saldo), 2, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?= $claseBadge ?> w-75 py-2">
                                                <?= $textoSaldo ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
    $(document).ready(function() {
        $('#tabla_mayor').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            }
        });
    });
    </script>
    <?php include('script.php'); ?>
</body>
</html>