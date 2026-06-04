<?php
require_once '../BACKEND/conecxion_bd.php';
require_once '../BACKEND/consulta_asientos.php';

// Cargamos tanto el histórico de asientos como el catálogo de cuentas para el formulario
$asientos = obtenerAsientos($conexion);
$cuentas_catalogo = obtenerCuentasParaAsiento($conexion);

// Almacenamos las cuentas en un array de PHP para pasarlo limpiamente a JavaScript
$array_cuentas = [];
if ($cuentas_catalogo && $cuentas_catalogo->num_rows > 0) {
    while ($c = $cuentas_catalogo->fetch_assoc()) {
        $array_cuentas[] = $c;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asientos Diario | Contable EA</title>
    <link rel="stylesheet" href="../DATATABLE/datatables1.css">
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/style_cliente.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../JAVASCRIPT/bootstrap.bundle.min.js"></script>
    <style>
        .totales-footer {
            font-weight: bold;
            background-color: #f1f5f9;
        }
    </style>
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
                        <h3 class="mb-0">Libro Diario</h3>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAsiento" onclick="inicializarAsientoNuevo()">
                            <i class="fas fa-plus-circle"></i> NUEVO ASIENTO
                        </button>
                    </div>
                    <div class="table-wrapper p-3">
                        <table id="tabla" class="table table-hover align-middle">
                            <thead class="table-dark">
    <tr>
        <th style="width: 7%;">ID</th> <th>Fecha</th>
        <th>Comprobante</th>
        <th>Glosa / Descripción</th>
        <th>Monto Total</th>
        <th class="text-center">Acciones</th>
    </tr>
</thead>
                            <tbody>
    <?php if ($asientos && $asientos->num_rows > 0): ?>
        <?php while($row = $asientos->fetch_assoc()): ?>
            <tr>
                <td class="text-muted fw-bold">#<?= $row['id_asiento'] ?></td> 
                
                <td><?= date("d/m/Y", strtotime($row['fecha_asiento'])) ?></td>
                <td><span class="badge bg-secondary"><?= $row['nro_comprobante'] ?></span></td>
                <td><?= htmlspecialchars($row['glosa']) ?></td>
                <td class="fw-bold text-primary"><?= number_format($row['total_debe'], 2) ?> Bs.</td>
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
            <td colspan="6" class="text-center text-muted py-4">No hay asientos registrados en este período.</td> </tr>
    <?php endif; ?>
</tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-search"></i> Detalle de Asiento Contable</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Cuenta Contable</th>
                                <th class="text-end" style="width: 150px;">Debe (Bs.)</th>
                                <th class="text-end" style="width: 150px;">Haber (Bs.)</th>
                            </tr>
                        </thead>
                        <tbody id="contenidoDetalle"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAsiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Registrar Asiento Manual</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="../BACKEND/guardar_asiento.php" method="POST">
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Fecha de Operación</label>
                                <input type="date" name="fecha_asiento" class="form-control" required value="<?= date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Nro. Comprobante</label>
                                <input type="text" name="nro_comprobante" class="form-control" placeholder="Ej: DIARIO-001" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Glosa Explicativa</label>
                                <input type="text" name="glosa" class="form-control" placeholder="Ej: Registro de gastos operativos..." required>
                            </div>
                        </div>

                        <table class="table table-bordered align-middle" id="tablaAsientoDinamico">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50%;">Cuenta Selección</th>
                                    <th style="width: 22%;">Debe (Bs.)</th>
                                    <th style="width: 22%;">Haber (Bs.)</th>
                                    <th style="width: 6%;" class="text-center">Quitar</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoAsiento">
                                </tbody>
                            <tfoot>
                                <tr class="totales-footer table-info">
                                    <td class="text-end fw-bold">TOTALES ACUMULADOS:</td>
                                    <td id="totalDebe" class="fw-bold text-end">0.00 Bs.</td>
                                    <td id="totalHaber" class="fw-bold text-end">0.00 Bs.</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold" onclick="agregarFila()">
                            <i class="fas fa-plus"></i> Añadir Nueva Línea
                        </button>
                    </div>
                    <div class="modal-footer">
                        <div id="statusCuadrado" class="me-auto text-danger fw-bold"><i class="fas fa-times-circle"></i> Asiento Descuadrado</div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" id="btnGuardarAsiento" class="btn btn-success" disabled>Guardar Asiento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Traemos las cuentas desde PHP de forma segura estructuradas en formato JSON para el script
        const cuentasDisponibles = <?= json_encode($array_cuentas); ?>;

        function verDetalle(id) {
            fetch(`../BACKEND/obtener_detalle_asiento.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    data.forEach(f => {
                        let debeVal = parseFloat(f.debe) > 0 ? parseFloat(f.debe).toFixed(2) + ' Bs.' : '-';
                        let haberVal = parseFloat(f.haber) > 0 ? parseFloat(f.haber).toFixed(2) + ' Bs.' : '-';
                        html += `<tr>
                                    <td><strong>${f.codigo_cuenta}</strong> - ${f.nombre_cuenta}</td>
                                    <td class="text-end text-success">${debeVal}</td>
                                    <td class="text-end text-danger">${haberVal}</td>
                                 </tr>`;
                    });
                    document.getElementById('contenidoDetalle').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('modalVerDetalle')).show();
                });
        }

        function eliminarAsiento(id) {
            if (confirm("¿Segura que deseas eliminar este asiento diario junto con todas sus partidas?")) {
                window.location.href = `../BACKEND/eliminar_asiento.php?id=${id}`;
            }
        }

        // Script Dinámico para agregar y validar líneas del Asiento
        function agregarFila() {
            const tbody = document.getElementById('cuerpoAsiento');
            const tr = document.createElement('tr');

            let opciones = `<option value="">Seleccione cuenta...</option>`;
            cuentasDisponibles.forEach(c => {
                opciones += `<option value="${c.id_cuenta}">${c.codigo_cuenta} - ${c.nombre_cuenta}</option>`;
            });

            tr.innerHTML = `
                <td>
                    <select name="id_cuenta[]" class="form-select select-cuenta" required>
                        ${opciones}
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="debe[]" class="form-control text-end input-debe" value="0.00" oninput="calcularCuadrature()" onclick="this.select()">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="haber[]" class="form-control text-end input-haber" value="0.00" oninput="calcularCuadrature()" onclick="this.select()">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerFila(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
            calcularCuadrature();
        }

        function removerFila(boton) {
            const filas = document.querySelectorAll('#cuerpoAsiento tr');
            if(filas.length > 2) {
                boton.closest('tr').remove();
                calcularCuadrature();
            } else {
                alert("Un asiento contable requiere al menos dos partidas (Partida Doble).");
            }
        }

        function calcularCuadrature() {
            let totalDebe = 0;
            let totalHaber = 0;

            document.querySelectorAll('.input-debe').forEach(input => {
                let val = parseFloat(input.value);
                totalDebe += isNaN(val) ? 0 : val;
            });

            document.querySelectorAll('.input-haber').forEach(input => {
                let val = parseFloat(input.value);
                totalHaber += isNaN(val) ? 0 : val;
            });

            document.getElementById('totalDebe').innerText = totalDebe.toFixed(2) + " Bs.";
            document.getElementById('totalHaber').innerText = totalHaber.toFixed(2) + " Bs.";

            const statusBox = document.getElementById('statusCuadrado');
            const btnGuardar = document.getElementById('btnGuardarAsiento');

            // Validamos cuadre contable exacto (Evitamos errores de punto flotante de JS usando diferencia mínima)
            if (totalDebe > 0 && totalHaber > 0 && Math.abs(totalDebe - totalHaber) < 0.01) {
                statusBox.innerHTML = `<i class="fas fa-check-circle text-success"></i> <span class="text-success">Asiento Cuadrado Balanceado</span>`;
                btnGuardar.disabled = false;
            } else {
                statusBox.innerHTML = `<i class="fas fa-times-circle text-danger"></i> <span class="text-danger">Asiento Descuadrado</span>`;
                btnGuardar.disabled = true;
            }
        }

        function inicializarAsientoNuevo() {
            document.getElementById('cuerpoAsiento').innerHTML = "";
            agregarFila();
            agregarFila();
        }

        window.onload = () => {
            if(document.getElementById('cuerpoAsiento').children.length === 0) {
                inicializarAsientoNuevo();
            }
        };
    </script>
    <?php include('script.php'); ?>
</body>
</html>