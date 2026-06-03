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
        <?php include('sidebar.php'); ?>

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