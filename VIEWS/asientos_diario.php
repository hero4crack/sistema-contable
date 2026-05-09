<?php
require_once '../BACKEND/conecxion_bd.php';
require_once '../BACKEND/consulta_asientos.php';
$asientos = obtenerAsientos($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asientos Diario | Contable EA</title>
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
                <a href="../VIEWS/libro_facturas.php"><i class="fas fa-file-invoice"></i> Libro de Facturas</a>
                <a href="../VIEWS/asientos_diario.php" class="active"><i class="fas fa-book"></i> Asientos Diario</a>
                <a href="../VIEWS/empleados.php"><i class="fas fa-users"></i> Empleados</a>
                <a href="../VIEWS/catalogo_cuenta.php"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
            </nav>
        </aside>

        <main class="viewport">
            <?php include('header.php'); ?>
            <section class="content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center p-3">
                        <h3>Libro Diario</h3>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAsiento">
                            <i class="fas fa-plus-circle"></i> NUEVO ASIENTO
                        </button>
                    </div>
                    <div class="table-wrapper p-3">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Comprobante</th>
                                    <th>Glosa</th>
                                    <th>Total</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($asientos && $asientos->num_rows > 0): ?>
                                    <?php while($row = $asientos->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= date("d/m/Y", strtotime($row['fecha_asiento'])) ?></td>
                                            <td><?= $row['nro_comprobante'] ?></td>
                                            <td><?= $row['glosa'] ?></td>
                                            <td class="fw-bold">BCV <?= number_format($row['total_debe'], 2) ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-info" onclick="verDetalle(<?= $row['id_asiento'] ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- MODAL PARA VER DETALLES (Faltaba este bloque) -->
    <div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Detalle del Asiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Cuenta Contable</th>
                                <th class="text-end">Debe</th>
                                <th class="text-end">Haber</th>
                            </tr>
                        </thead>
                        <tbody id="contenidoDetalle">
                            <!-- Se llena con JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE REGISTRO (Lo mantienes igual) -->
    <div class="modal fade" id="modalAsiento" tabindex="-1" aria-hidden="true">
        <!-- ... Tu código de modalAsiento que ya funciona ... -->
    </div>

    <script src="../JAVASCRIPT/asiento_dinamico.js"></script>
    <script>
        function verDetalle(id) {
            fetch(`../BACKEND/obtener_detalle_asiento.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    data.forEach(fila => {
                        html += `<tr>
                            <td>${fila.nombre_cuenta}</td>
                            <td class="text-end">${parseFloat(fila.debe).toFixed(2)}</td>
                            <td class="text-end">${parseFloat(fila.haber).toFixed(2)}</td>
                        </tr>`;
                    });
                    document.getElementById('contenidoDetalle').innerHTML = html;
                    let m = new bootstrap.Modal(document.getElementById('modalVerDetalle'));
                    m.show();
                })
                .catch(error => console.error('Error:', error));
        }

        function actualizarOpcionesCuentas(selectElement) {
            let opciones = `<option value="">Seleccione cuenta...</option>`;
            <?php 
                $cuentas = obtenerCuentasParaAsiento($conexion);
                while($c = $cuentas->fetch_assoc()): 
            ?>
                opciones += `<option value="<?= $c['id_cuenta'] ?>"><?= $c['codigo_cuenta'] ?> - <?= $c['nombre_cuenta'] ?></option>`;
            <?php endwhile; ?>
            selectElement.innerHTML = opciones;
        }
    </script>
</body>
</html>