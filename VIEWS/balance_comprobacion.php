<?php
require_once '../BACKEND/conecxion_bd.php';
require_once '../BACKEND/consulta_balance.php';
$datos = obtenerBalanceComprobacion($conexion);

// Variables para los grandes totales
$totalSumasDebe = 0;
$totalSumasHaber = 0;
$totalSaldoDeudor = 0;
$totalSaldoAcreedor = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Balance de Comprobación | Contable EA</title>
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/style_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <aside class="main-sidebar">
            <div class="brand">CONTABLE EA</div>
            <nav class="menu">
    <a href="../VIEWS/inicio.php"><i class="fas fa-home"></i>Inicio</a>
                <a href="../VIEWS/empresas_clientes.php"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="../VIEWS/libro_facturas.php"><i class="fas fa-file-invoice""></i> Libro de Facturas</a>
                <a href="../VIEWS/asientos_diario.php"><i class="fas fa-book"></i> Asientos Diario</a>
                <a href="../VIEWS/libro_mayor.php"><i class="fas fa-chart-line"></i> Libro Mayor</a>
                <a href="../VIEWS/balance_comprobacion.php" class="active"><i class="fas fa-balance-scale"></i> Balance</a>
                <a href="../VIEWS/empleados.php"><i class="fas fa-users"></i> Empleados</a>
                <a href="../VIEWS/catalogo_cuenta.php"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="#"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>

        <main class="viewport">
            <?php include('header.php'); ?>
            <section class="content p-4">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h3 class="mb-0 text-center">Balance de Comprobación</h3>
                    </div>
                    <div class="table-responsive p-3">
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-secondary">
                                <tr>
                                    <th rowspan="2" class="align-middle">Código</th>
                                    <th rowspan="2" class="align-middle">Cuenta</th>
                                    <th colspan="2">Sumas</th>
                                    <th colspan="2">Saldos</th>
                                </tr>
                                <tr>
                                    <th>Debe</th>
                                    <th>Haber</th>
                                    <th>Deudor</th>
                                    <th>Acreedor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $datos->fetch_assoc()): 
                                    $totalSumasDebe += $row['sumas_debe'];
                                    $totalSumasHaber += $row['sumas_haber'];
                                    $totalSaldoDeudor += $row['saldo_deudor'];
                                    $totalSaldoAcreedor += $row['saldo_acreedor'];
                                ?>
                                    <tr>
                                        <td><?= $row['codigo_cuenta'] ?></td>
                                        <td class="text-start"><?= $row['nombre_cuenta'] ?></td>
                                        <td><?= number_format($row['sumas_debe'], 2) ?></td>
                                        <td><?= number_format($row['sumas_haber'], 2) ?></td>
                                        <td class="text-primary fw-bold"><?= number_format($row['saldo_deudor'], 2) ?></td>
                                        <td class="text-danger fw-bold"><?= number_format($row['saldo_acreedor'], 2) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot class="table-dark">
                                <tr>
                                    <td colspan="2" class="text-end fw-bold">TOTALES GENERALES:</td>
                                    <td><?= number_format($totalSumasDebe, 2) ?></td>
                                    <td><?= number_format($totalSumasHaber, 2) ?></td>
                                    <td><?= number_format($totalSaldoDeudor, 2) ?></td>
                                    <td><?= number_format($totalSaldoAcreedor, 2) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                        
                        <?php if (abs($totalSaldoDeudor - $totalSaldoAcreedor) < 0.01): ?>
                            <div class="alert alert-success mt-3 text-center">
                                <i class="fas fa-check-circle"></i> <strong>¡Contabilidad Cuadrada!</strong> Las sumas y los saldos coinciden perfectamente.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger mt-3 text-center">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Aviso:</strong> Existe una diferencia de BCV <?= number_format(abs($totalSaldoDeudor - $totalSaldoAcreedor), 2) ?>. Revisa tus asientos.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>