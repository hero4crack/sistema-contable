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
        <strong>¡Excelente!</strong> El movimiento ha sido procesado correctamente en el libro fiscal.
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
                        <button class="primary-btn" style="background: #10b981;" data-bs-toggle="modal" data-bs-target="#modalRegistro" onclick="limpiarFormularioNuevaFactura()">
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
                                                <button class="config-btn text-warning" title="Editar Factura" onclick="editarFactura(<?php echo $f['id_factura']; ?>)" style="background: transparent; border: none; margin-right: 5px;"><i class="fas fa-edit"></i></button>
                                                <a href="../BACKEND/eliminar_facturas.php?id_factura=<?php echo $f['id_factura'] ?>" class="btn btn-danger fs-6 text-white p-1" style="width: 28px; height: 28px; display: inline-flex; justify-content: center; align-items: center; padding: 0 !important;"> <i class="fa-solid fa-trash" style="font-size: 0.85rem;"></i></a>
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
                    <h5 class="modal-title" id="tituloModal"><i class="fas fa-file-invoice me-2"></i> Nueva Factura Fiscal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../BACKEND/guardar_factura.php" method="POST" id="formFactura">
                    <input type="hidden" name="id_factura" id="edit_id_factura" value="">
                    
                    <div class="modal-body" style="padding: 30px;">
                        <div class="row g-3">
                            
                            <div class="col-md-4 mb-2">
                                <label class="form-label fw-bold text-secondary">Fecha de Emisión</label>
                                <input type="date" name="fecha_documento" id="fecha_documento" class="form-control border-primary" required value="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label class="form-label fw-bold text-secondary">Tipo de Movimiento</label>
                                <select name="tipo_transaccion" id="tipo_transaccion" class="form-select border-primary fw-semibold" onchange="alternarTerceros()" required>
                                    <option value="VENTA">Venta (Libro Ventas)</option>
                                    <option value="COMPRA">Compra (Libro Compras)</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-2" id="grupo_cliente">
                                <label class="form-label fw-bold text-success"><i class="fas fa-city me-1"></i> Empresa Cliente</label>
                                <select name="id_empresa" id="id_empresa" class="form-select border-success">
                                    <option value="">Seleccione...</option>
                                    <?php
                                    $empresas = obtenerEmpresasParaFactura($conexion);
                                    while ($emp = $empresas->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $emp['id_empresa']; ?>"><?php echo $emp['nombre_empresa']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-4 mb-2 d-none" id="grupo_proveedor">
                                <label class="form-label fw-bold text-warning"><i class="fas fa-truck me-1"></i> Proveedor</label>
                                <select name="id_proveedor" id="id_proveedor" class="form-select border-warning">
                                    <option value="">Seleccione...</option>
                                    <?php
                                    $provQuery = $conexion->query("SELECT id_proveedor, razon_social FROM proveedores WHERE estado_activo = 1");
                                    while ($prov = $provQuery->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $prov['id_proveedor']; ?>"><?php echo $prov['razon_social']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nro. Factura</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-hashtag text-muted"></i></span>
                                    <input type="text" name="nro_factura" id="nro_factura" class="form-control" placeholder="Ej: 00234" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nro. Control</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-shield-alt text-muted"></i></span>
                                    <input type="text" name="nro_control" id="nro_control" class="form-control" placeholder="Ej: 00-00234" required>
                                </div>
                            </div>

                            <hr class="text-muted my-4">

                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark">Base Imponible (Bs.)</label>
                                <input type="number" step="0.01" min="0" name="base_imponible" id="base_modal" class="form-control fw-bold border-dark" oninput="calcularTotalesModal()" required placeholder="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-secondary">Monto Exento (Bs.)</label>
                                <input type="number" step="0.01" min="0" name="monto_exento" id="exento_modal" class="form-control text-secondary" oninput="calcularTotalesModal()" value="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-danger">IVA (16% Bs.)</label>
                                <input type="text" name="monto_iva" id="iva_modal" class="form-control text-danger fw-bold" readonly style="background: #fff5f5; border-color: #feb2b2;" value="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-primary">Total Factura (Bs.)</label>
                                <input type="hidden" name="total_factura" id="total_modal_hidden" value="0.00">
                                <input type="text" id="iva_total_visual" class="form-control bg-primary-subtle text-primary fw-bolder fs-5 border-primary" readonly value="0.00">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer" style="background: #f8fafc; border-radius: 0 0 15px 15px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnSubmitModal" class="btn btn-success" style="background: #10b981; border: none; padding: 10px 25px; font-weight: bold;">
                            <i class="fas fa-save me-1"></i> Procesar Factura
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function calcularTotalesModal() {
            const base = parseFloat(document.getElementById('base_modal').value) || 0;
            const exento = parseFloat(document.getElementById('exento_modal').value) || 0;
            const iva = base * 0.16;
            const total = base + exento + iva;
            
            document.getElementById('iva_modal').value = iva.toFixed(2);
            document.getElementById('total_modal_hidden').value = total.toFixed(2);
            document.getElementById('iva_total_visual').value = total.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + " Bs.";
        }

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
                selectProveedor.required = true;
            } else {
                grupoProveedor.classList.add('d-none');
                grupoCliente.classList.remove('d-none');
                selectProveedor.required = false;
                selectCliente.required = true;
            }
        }

        // Restablece el modal a su estado original de inserción limpia
        function limpiarFormularioNuevaFactura() {
            document.getElementById('formFactura').action = "../BACKEND/guardar_factura.php";
            document.getElementById('tituloModal').innerHTML = '<i class="fas fa-file-invoice me-2"></i> Nueva Factura Fiscal';
            document.getElementById('edit_id_factura').value = "";
            document.getElementById('fecha_documento').value = "<?php echo date('Y-m-d'); ?>";
            document.getElementById('tipo_transaccion').value = "VENTA";
            document.getElementById('nro_factura').value = "";
            document.getElementById('nro_control').value = "";
            document.getElementById('base_modal').value = "";
            document.getElementById('exento_modal').value = "0.00";
            document.getElementById('id_empresa').value = "";
            document.getElementById('id_proveedor').value = "";
            
            const btn = document.getElementById('btnSubmitModal');
            btn.className = "btn btn-success";
            btn.style.background = "#10b981";
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Procesar Factura';
            
            alternarTerceros();
            calcularTotalesModal();
        }

        // LÓGICA ASÍNCRONA: LLENAR FORMULARIO PARA EDICIÓN
        function editarFactura(id) {
            fetch(`../BACKEND/obtener_factura_json.php?id=${id}`)
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success') {
                        const f = res.data;
                        
                        // 1. Redireccionar formulario a modificar
                        document.getElementById('formFactura').action = "../BACKEND/modificar_factura.php";
                        document.getElementById('tituloModal').innerHTML = '<i class="fas fa-edit me-2"></i> Modificar Factura Fiscal';
                        
                        // 2. Rellenar campos comunes
                        document.getElementById('edit_id_factura').value = f.id_factura;
                        document.getElementById('fecha_documento').value = f.fecha_documento;
                        document.getElementById('tipo_transaccion').value = f.tipo_transaccion;
                        document.getElementById('nro_factura').value = f.nro_factura;
                        document.getElementById('nro_control').value = f.nro_control;
                        document.getElementById('base_modal').value = f.base_imponible;

                        // 3. Ajustar selectores de terceros y montos exentos
                        alternarTerceros();
                        
                        if (f.tipo_transaccion === 'COMPRA') {
                            document.getElementById('id_proveedor').value = f.id_tercero;
                            document.getElementById('id_empresa').value = "";
                            // Como en compras guardas la retención en monto_exento, reflejamos el exento original en 0.00 para la edición estándar
                            document.getElementById('exento_modal').value = "0.00";
                        } else {
                            document.getElementById('id_empresa').value = f.id_empresa;
                            document.getElementById('id_proveedor').value = "";
                            document.getElementById('exento_modal').value = f.monto_exento;
                        }
                        
                        // 4. Forzar cálculos y estilos visuales de botón de actualización
                        calcularTotalesModal();
                        
                        const btn = document.getElementById('btnSubmitModal');
                        btn.className = "btn btn-warning text-white";
                        btn.style.background = "#f59e0b";
                        btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Actualizar Factura';

                        // 5. Mostrar Modal
                        const modal = new bootstrap.Modal(document.getElementById('modalRegistro'));
                        modal.show();
                    } else {
                        alert("Error: " + res.message);
                    }
                })
                .catch(error => {
                    console.error("Error en AJAX:", error);
                    alert("No se pudieron extraer los datos del documento fiscal.");
                });
        }
    </script>
    <?php include('script.php'); ?>
</body>

</html>