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
                <a href="../VIEWS/libro_mayor.php" class="active"><i class="fas fa-chart-line"></i> Libro Mayor</a>
                <a href="../VIEWS/balance_comprobacion.php"><i class="fas fa-balance-scale"></i> Balance</a>
                <a href="../VIEWS/empleados.php"><i class="fas fa-users"></i> Empleados</a>
                <a href="../VIEWS/catalogo_cuenta.php"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="#"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>

        <main class="viewport">
            <?php include('header.php'); ?>
            <section class="content p-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Libro Mayor - Resumen de Saldos</h3>
                    </div>
                    <div class="table-responsive p-3">
                        <table class="table table-striped table-hover border">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Cuenta Contable</th>
                                    <th class="text-end">Total Debe</th>
                                    <th class="text-end">Total Haber</th>
                                    <th class="text-end">Saldo Final</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $saldos->fetch_assoc()): 
                                    $saldo = $row['saldo'];
                                    $claseSaldo = ($saldo >= 0) ? 'text-primary' : 'text-danger';
                                ?>
                                    <tr>
                                        <td><?= $row['codigo_cuenta'] ?></td>
                                        <td class="fw-bold"><?= $row['nombre_cuenta'] ?></td>
                                        <td class="text-end text-success"><?= number_format($row['total_debe'], 2) ?></td>
                                        <td class="text-end text-danger"><?= number_format($row['total_haber'], 2) ?></td>
                                        <td class="text-end fw-bold <?= $claseSaldo ?>">
                                            BCV <?= number_format(abs($saldo), 2) ?> 
                                            <?= ($saldo >= 0) ? '(D)' : '(A)' ?>
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
</body>
</html>