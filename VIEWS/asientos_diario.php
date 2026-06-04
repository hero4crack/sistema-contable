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
            <div class="brand"><img src="../IMG/logo_empresa-sinfondo.png" alt="#" class="mi-imagen"></div>
            <nav class="menu">
                <a href="../VIEWS/inicio.php"><i class="fas fa-home"></i> Inicio</a>
                <a href="../VIEWS/empresas_clientes.php"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="../VIEWS/registro_proveedor.php "><i class="fas fa-truck"></i> Proveedores</a>
                <a href="../VIEWS/libro_facturas.php"><i class="fas fa-file-invoice"></i> Libro de Facturas</a>
                <a href="../VIEWS/asientos_diario.php" class="active"><i class="fas fa-book"></i> Asientos Diario</a>
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
                                            <td class="fw-bold text-primary">BCV <?= number_format($row['total_debe'], 2) ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-info me-1" onclick="verDetalle(<?= $row['id_asiento'] ?>)" title="Ver Detalle">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarAsiento(<?= $row['id_asiento'] ?>)" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No hay asientos registrados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- MODAL VER DETALLE -->
    <div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-search"></i> Detalle del Asiento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Cuenta Contable</th>
                                <th class="text-end">Debe</th>
                                <th class="text-end">Haber</th>
                            </tr>
                        </thead>
                        <tbody id="contenidoDetalle"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL REGISTRO -->
    <div class="modal fade" id="modalAsiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Registrar Asiento Contable</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="../BACKEND/guardar_asiento.php" method="POST">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Fecha</label>
                                <input type="date" name="fecha_asiento" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Nro. Comprobante</label>
                                <input type="text" name="nro_comprobante" class="form-control" placeholder="DIARIO-001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Descripción / Glosa</label>
                                <input type="text" name="glosa" class="form-control" required>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50%;">Cuenta</th>
                                    <th>Debe ($)</th>
                                    <th>Haber ($)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoAsiento"></tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <td class="text-end fw-bold">TOTALES:</td>
                                    <td id="totalDebe" class="fw-bold">0.00</td>
                                    <td id="totalHaber" class="fw-bold">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarFila()">
                            <i class="fas fa-plus"></i> Añadir Línea
                        </button>
                    </div>
                    <div class="modal-footer">
                        <div id="statusCuadrado" class="me-auto text-danger fw-bold">Asiento Descuadrado</div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" id="btnGuardarAsiento" class="btn btn-primary" disabled>Guardar Asiento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../JAVASCRIPT/asiento_dinamico.js"></script>
    <script>
        function verDetalle(id) {
            fetch(`../BACKEND/obtener_detalle_asiento.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    data.forEach(f => {
                        html += `<tr><td>${f.nombre_cuenta}</td><td class="text-end text-success">${parseFloat(f.debe).toFixed(2)}</td><td class="text-end text-danger">${parseFloat(f.haber).toFixed(2)}</td></tr>`;
                    });
                    document.getElementById('contenidoDetalle').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('modalVerDetalle')).show();
                });
        }

        function eliminarAsiento(id) {
            if (confirm("¿Segura que deseas eliminar este asiento y sus detalles?")) {
                window.location.href = `../BACKEND/eliminar_asiento.php?id=${id}`;
            }
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

        window.onload = () => { agregarFila(); agregarFila(); };
    </script>
    <?php include('script.php'); ?>
</body>
</html>