<?php
require_once '../BACKEND/conecxion_bd.php';
require_once '../BACKEND/consulta_estado_resultados.php';
$datos = obtenerEstadoResultados($conexion);

$total_ingresos = 0;
$total_costos = 0;
$total_gastos = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Resultados | Contable EA</title>
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/style_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <a href="../VIEWS/estado_resultados.php" class="active"><i class="fas fa-file-invoice-dollar"></i> Estado de Resultados</a>
                <a href="../VIEWS/empleados.php"><i class="fas fa-users"></i> Empleados</a>
                <a href="../VIEWS/catalogo_cuenta.php"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="../VIEWS/auditoria.php"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>


        <main class="viewport">
            <?php include('header.php'); ?>
            <section class="content p-4">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center">
                        <h3 class="mb-0">Estado de Resultados</h3>
                        <p class="mb-0 small">Periodo Actual</p>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <tbody>
                                <?php 
                                while($row = $datos->fetch_assoc()): 
                                    $codigo = $row['codigo_cuenta'];
                                    $monto = ($codigo[0] == '4') ? $row['saldo_ingreso'] : $row['saldo_egreso'];
                                    
                                    if($codigo[0] == '4') $total_ingresos += $monto;
                                    if($codigo[0] == '5') $total_costos += $monto;
                                    if($codigo[0] == '6') $total_gastos += $monto;
                                ?>
                                    <tr>
                                        <td><?= $codigo ?> - <?= $row['nombre_cuenta'] ?></td>
                                        <td class="text-end"><?= number_format($monto, 2, ',', '.') ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                
                                <tr class="table-secondary fw-bold">
                                    <td>UTILIDAD BRUTA (Ingresos - Costos)</td>
                                    <td class="text-end"><?= number_format($total_ingresos - $total_costos, 2, ',', '.') ?></td>
                                </tr>
                                <tr class="table-dark fw-bold">
                                    <?php $utilidad_neta = $total_ingresos - $total_costos - $total_gastos; ?>
                                    <td>UTILIDAD O PÉRDIDA NETA DEL EJERCICIO</td>
                                    <td class="text-end <?= ($utilidad_neta >= 0) ? 'text-success' : 'text-danger' ?>">
                                        <?= number_format($utilidad_neta, 2, ',', '.') ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <?php include('script.php'); ?>
</body>
</html>