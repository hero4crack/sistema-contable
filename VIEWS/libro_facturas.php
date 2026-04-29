<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Libro de Facturas | Contable EA</title>
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/style_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../JAVASCRIPT/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="app-container">
        <aside class="main-sidebar">
            <div class="brand"><i class="fas fa-calculator"></i> CONTABLE EA</div>
            <nav class="menu">
                <a href="../VIEWS/inicio.php"><i class="fas fa-home"></i> Inicio</a>
                <a href="../VIEWS/empresas_clientes.php"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="../VIEWS/libro_facturas.php" class="active"><i class="fas fa-file-invoice"></i> Libro de Facturas</a>
                <a href="#"><i class="fas fa-book"></i> Asientos Diario</a>
                <a href="../VIEWS/empleados.php"><i class="fas fa-users"></i> Empleados</a>
                <a href="../VIEWS/catalogo_cuenta.php"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="#"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>

        <main class="viewport">
          
        <?php include('header.php') ?>

            <section class="content">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 20px;">
                        <div class="search-group">
                            <select class="filter-select">
                                <option>Periodo: Abril 2026</option>
                                <option>Periodo: Marzo 2026</option>
                            </select>
                            <input type="text" placeholder="Buscar por RIF o Nro Factura..." class="search-input" style="width: 250px; margin-left: 10px;">
                        </div>
                        <button class="primary-btn" style="background: #10b981;">
                           <i class="fas fa-plus-circle"></i> REGISTRAR NUEVA FACTURA
                        </button>
                    </div>

                    <div class="table-wrapper">
                        <table class="contable-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Nro. Factura / Control</th>
                                    <th>Cliente / Proveedor</th>
                                    <th>Base Imponible</th>
                                    <th>Monto Exento</th>
                                    <th>IVA (16%)</th>
                                    <th>Total Factura</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($facturas)): ?>
                                    <?php foreach ($facturas as $f): ?>
                                    <tr>
                                        <td><?php echo date("d/m/Y", strtotime($f['fecha_documento'])); ?></td>
                                        <td>
                                            <span style="display:block; font-weight: bold;"><?php echo $f['nro_factura']; ?></span>
                                            <small style="color: #64748b;">Ctrl: <?php echo $f['nro_control']; ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($f['nombre_empresa']); ?></td>
                                        <td><?php echo number_format($f['base_imponible'], 2); ?> $</td>
                                        <td><?php echo number_format($f['monto_exento'], 2); ?> $</td>
                                        <td><?php echo number_format($f['monto_iva'], 2); ?> $ <small>(<?php echo $f['alicuota_iva']; ?>%)</small></td>
                                        <td style="font-weight: 800; color: #1e293b;"><?php echo number_format($f['total_factura'], 2); ?> $</td>
                                        <td>
                                            <button class="config-btn" title="Ver Detalle"><i class="fas fa-eye"></i></button>
                                            <button class="config-btn" title="Imprimir" style="color: #3b82f6;"><i class="fas fa-print"></i></button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center; padding: 20px; color: #94a3b8;">No hay facturas registradas en este periodo.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        const userMenu = document.getElementById('userMenu');
        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
        });
        window.onclick = function() {
            userMenu.classList.remove('active');
        }
    </script>
</body>
</html>