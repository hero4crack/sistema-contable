<?php
require_once '../BACKEND/conecxion_bd.php';
require_once '../BACKEND/consulta_factura.php';

$facturas = obtenerLibroFacturas($conexion);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Libro de Facturas | Contable EA</title>
    <link rel="stylesheet" href="../DATATABLE/datatables1.css">
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/style_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../JAVASCRIPT/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 20px;">
        <strong>¡Excelente!</strong> La factura ha sido registrada correctamente en el libro.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="app-container">
        
        <aside class="main-sidebar">
            <div class="brand"><img src="../IMG/logo_empresa-sinfondo.png" alt="#" class="mi-imagen"></div>
            <nav class="menu">
                <a href="../VIEWS/inicio.php"><i class="fas fa-home"></i> Inicio</a>
                <a href="../VIEWS/empresas_clientes.php"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="../VIEWS/registro_proveedor.php"><i class="fas fa-truck"></i> Proveedores</a>
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
            <?php include('header.php') ?>

            <section class="content">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 20px;">
                        <button class="primary-btn" style="background: #10b981;" data-bs-toggle="modal" data-bs-target="#modalRegistro">
                            <i class="fas fa-plus-circle"></i> REGISTRAR NUEVA FACTURA
                        </button>
                    </div>

                    <div class="table-wrapper">
                        <table id='tabla' class="contable-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Nro. Factura / Control</th>
                                    <th>Cliente / Proveedor</th>
                                    <th>Base Imponible</th>
                                    <th>Monto Exento</th>
                                    <th>IVA (16%)</th>
                                    <th>Total Factura</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class='table-group-divider'>
                                <?php if (isset($facturas) && !empty($facturas)): ?>
                                    <?php foreach ($facturas as $f): ?>
                                        <tr>
                                            <td><?php echo date("d/m/Y", strtotime($f['fecha_documento'])); ?></td>
                                            <td>
                                                <span class="badge <?php echo ($f['tipo_transaccion'] == 'VENTA') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'; ?> p-1 fs-6">
                                                    <?php echo $f['tipo_transaccion']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span style="display:block; font-weight: bold;"><?php echo $f['nro_factura']; ?></span>
                                                <small style="color: #64748b;">Ctrl: <?php echo $f['nro_control']; ?></small>
                                            </td>
                                            <td>
                                                <?php 
                                                    echo htmlspecialchars($f['tipo_transaccion'] == 'VENTA' ? $f['nombre_empresa'] : $f['nombre_proveedor']); 
                                                ?>
                                            </td>
                                            <td><?php echo number_format($f['base_imponible'], 2); ?> Bs.</td>
                                            <td><?php echo number_format($f['monto_exento'], 2); ?> Bs.</td>
                                            <td><?php echo number_format($f['monto_iva'], 2); ?> Bs. <small>(<?php echo $f['alicuota_iva']; ?>%)</small></td>
                                            <td style="font-weight: 800; color: #1e293b;"><?php echo number_format($f['total_factura'], 2); ?> Bs.</td>
                                            <td>
                                                <button class="config-btn" title="Ver Detalle"><i class="fas fa-eye"></i></button>
                                                <a href="../BACKEND/eliminar_facturas.php?id_factura=<?php echo $f['id_factura'] ?>" class="btn btn-danger fs-6 text-white p-1"> <i class="fa-solid fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" style="text-align:center; padding: 20px; color: #94a3b8;">No hay facturas registradas en este periodo.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <div class="modal-header" style="background: #1e293b; color: white; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title"><i class="fas fa-file-invoice"></i> Nueva Factura Fiscal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../BACKEND/guardar_factura.php" method="POST">
                    <div class="modal-body" style="padding: 30px;">
                        <div class="row g-3">
                            
                            <div class="col-md-4 mb-2">
                                <label class="form-label fw-bold">Tipo de Movimiento</label>
                                <select name="tipo_transaccion" id="tipo_transaccion" class="form-select border-primary" onchange="alternarTerceros()" required>
                                    <option value="VENTA">Venta (Alimenta Libro Ventas)</option>
                                    <option value="COMPRA">Compra (Alimenta Libro Compras)</option>
                                </select>
                            </div>

                            <div class="col-md-8 mb-2" id="grupo_cliente">
                                <label class="form-label fw-bold">Empresa Cliente</label>
                                <select name="id_empresa" id="id_empresa" class="form-select">
                                    <option value="">Seleccione la empresa...</option>
                                    <?php
                                    $empresas = obtenerEmpresasParaFactura($conexion);
                                    while ($emp = $empresas->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $emp['id_empresa']; ?>"><?php echo $emp['nombre_empresa']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-8 mb-2 d-none" id="grupo_proveedor">
                                <label class="form-label fw-bold">Proveedor</label>
                                <select name="id_proveedor" id="id_proveedor" class="form-select">
                                    <option value="">Seleccione el proveedor...</option>
                                    <?php
                                    // Consulta directa para simplificar el flujo de proveedores
                                    $provQuery = $conexion->query("SELECT id_proveedor, razon_social FROM proveedores WHERE estado_activo = 1");
                                    while ($prov = $provQuery->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $prov['id_proveedor']; ?>"><?php echo $prov['razon_social']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nro. Factura</label>
                                <input type="text" name="nro_factura" class="form-control" placeholder="0001" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nro. Control</label>
                                <input type="text" name="nro_control" class="form-control" placeholder="00-001" required>
                            </div>

                            <div class="col-md-4 mt-4">
                                <label class="form-label">Base Imponible (Bs.)</label>
                                <input type="number" step="0.01" name="base_imponible" id="base_modal" class="form-control" oninput="calcularIvaModal()" required>
                            </div>
                            <div class="col-md-4 mt-4">
                                <label class="form-label">Monto Exento (Bs.)</label>
                                <input type="number" step="0.01" name="monto_exento" id="exento_modal" class="form-control" oninput="calcularIvaModal()" value="0.00">
                            </div>
                            <div class="col-md-4 mt-4">
                                <label class="form-label">IVA (16%)</label>
                                <input type="text" name="monto_iva" id="iva_modal" class="form-control" readonly style="background: #f8fafc;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="background: #f8fafc; border-radius: 0 0 15px 15px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" style="background: #10b981; border: none; padding: 10px 25px;">Guardar Factura</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function calcularIvaModal() {
            const base = parseFloat(document.getElementById('base_modal').value) || 0;
            const iva = base * 0.16;
            document.getElementById('iva_modal').value = iva.toFixed(2);
        }

        // Función JS para alternar campos entre Clientes y Proveedores según la transacción
        function alternarTerceros() {
            const tipo = document.getElementById('tipo_transaccion').value;
            const grupoCliente = document.getElementById('grupo_cliente');
            const grupoProveedor = document.getElementById('grupo_proveedor');
            const selectCliente = document.getElementById('id_empresa');
            const selectProveedor = document.getElementById('id_proveedor');

            if (tipo === 'COMPRA') {
                grupoCliente.classList.add('d-none');
                grupoProveedor.classList.remove('d-none');
                selectCliente.required = false;
                selectCliente.value = "";
                selectProveedor.required = true;
            } else {
                grupoProveedor.classList.add('d-none');
                grupoCliente.classList.remove('d-none');
                selectProveedor.required = false;
                selectProveedor.value = "";
                selectCliente.required = true;
            }
        }
    </script>
    <?php include('script.php'); ?>
</body>
</html>