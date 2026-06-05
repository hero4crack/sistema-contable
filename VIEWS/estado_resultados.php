<?php
require_once '../BACKEND/conecxion_bd.php';
require_once '../BACKEND/consulta_resultados.php';
// ... (código PHP inicial para traer los datos)
$r = obtenerEstadoResultados($conexion);

$ventas = $r['4']; 
$costos = abs($r['6']); // El costo resta
$gastos = abs($r['5']); // Los gastos restan

$utilidad_bruta = $ventas - $costos;
$utilidad_neta  = $utilidad_bruta - $gastos;
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
    <div class="card-header bg-dark text-white"><h3>Estado de Resultados</h3></div>
    <div class="card-body">
        <table class="table">
            <tr><td>Ventas Totales</td><td class="text-end"><?= number_format($ventas, 2) ?></td></tr>
            <tr class="text-danger"><td>(-) Costo de Ventas</td><td class="text-end"><?= number_format($costos, 2) ?></td></tr>
            <tr class="fw-bold"><td>= UTILIDAD BRUTA</td><td class="text-end"><?= number_format($utilidad_bruta, 2) ?></td></tr>
            <tr class="text-danger"><td>(-) Gastos Operativos</td><td class="text-end"><?= number_format($gastos, 2) ?></td></tr>
            <tr class="bg-primary text-white fw-bold"><td>= UTILIDAD NETA</td><td class="text-end"><?= number_format($utilidad_neta, 2) ?></td></tr>
        </table>
    </div>
</div>

                </div>
            </section>
        </main>
    </div>
    <?php include('script.php'); ?>
</body>
</html>