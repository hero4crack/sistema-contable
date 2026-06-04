<?php
require_once '../BACKEND/conecxion_bd.php';
require_once '../BACKEND/consulta_factura.php';

$facturas = obtenerLibroFacturas($conexion);

// Separamos los arrays en el servidor para alimentar cada pestaña de forma limpia
$ventas = [];
$compras = [];

if (isset($facturas) && !empty($facturas)) {
    foreach ($facturas as $f) {
        if ($f['tipo_transaccion'] === 'VENTA') {
            $ventas[] = $f;
        } else {
            $compras[] = $f;
        }
    }
}
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
    <style>
        /* Estilos estéticos para nuestras pestañas fiscales */
        .tab-btn {
            padding: 10px 20px;
            font-weight: bold;
            border: none;
            background: #e2e8f0;
            color: #475569;
            border-radius: 8px 8px 0 0;
            margin-right: 5px;
            transition: all 0.3s ease;
        }
        .tab-btn.active-tab {
            background: #1e293b;
            color: white;
        }
        .tab-btn:hover:not(.active-tab) {
            background: #cbd5e1;
        }
    </style>
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
                <div class="d-flex justify-content-between align-items-end px-2">
                    <div>
                        <button id="btnTabVentas" class="tab-btn active-tab" onclick="cambiarLibro('VENTAS')">
                            <i class="fas fa-arrow-up text-success me-1"></i> LIBRO DE VENTAS (Débito Fiscal)
                        </button>
                        <button id="btnTabCompras" class="tab-btn" onclick="cambiarLibro('COMPRAS')">
                            <i class="fas fa-arrow-down text-warning me-1"></i> LIBRO DE COMPRAS (Crédito Fiscal)
                        </button>
                    </div>
                    <div class="mb-2">
                        <button class="primary-btn" style="background: #10b981; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalRegistro" onclick="limpiarFormularioNuevaFactura()">
                            <i class="fas fa-plus-circle"></i> REGISTRAR FACTURA
                        </button>
                    </div>
                </div>

                <div class="card" style="border-top-left-radius: 0px;">
                    
                    <div id="contenedorVentas" class="table-wrapper">
                        <table id='tablaVentas' class="contable-table w-100">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Nro. Factura / Control</th>
                                    <th>Cliente</th>
                                    <th>Base Imponible</th>
                                    <th>Monto Exento</th>
                                    <th>IVA Débito (16%)</th>
                                    <th>Total Factura</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class='table-group-divider'>
                                <?php if (!empty($ventas)): ?>
                                    <?php foreach ($ventas as $v): ?>
                                        <tr>
                                            <td><?php echo date("d/m/Y", strtotime($v['fecha_documento'])); ?></td>
                                            <td>
                                                <span style="display:block; font-weight: bold;"><?php echo $v['nro_factura']; ?></span>
                                                <small style="color: #64748b;">Ctrl: <?php echo $v['nro_control']; ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($v['nombre_empresa']); ?></td>
                                            <td><?php echo number_format($v['base_imponible'], 2); ?> Bs.</td>
                                            <td><?php echo number_format($v['monto_exento'], 2); ?> Bs.</td>
                                            <td class="text-success fw-semibold">+<?php echo number_format($v['monto_iva'], 2); ?> Bs.</td>
                                            <td style="font-weight: 800; color: #1e293b;"><?php echo number_format($v['total_factura'], 2); ?> Bs.</td>
                                            <td>
                                                <button class="config-btn text-warning" title="Editar Venta" onclick="editarFactura(<?php echo $v['id_factura']; ?>)" style="background: transparent; border: none; margin-right: 5px;"><i class="fas fa-edit"></i></button>
                                                <a href="../BACKEND/eliminar_facturas.php?id_factura=<?php echo $v['id_factura'] ?>" class="btn btn-danger fs-6 text-white p-1" style="width: 28px; height: 28px; display: inline-flex; justify-content: center; align-items: center; padding: 0 !important;"> <i class="fa-solid fa-trash" style="font-size: 0.85rem;"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center; padding: 20px; color: #94a3b8;">No hay operaciones de ventas registradas.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div id="contenedorCompras" class="table-wrapper d-none">
                        <table id='tablaCompras' class="contable-table w-100">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Nro. Factura / Control</th>
                                    <th>Proveedor</th>
                                    <th>Base Imponible</th>
                                    <th>IVA Crédito (16%)</th>
                                    <th>Retención IVA (Col. Exento)</th>
                                    <th>Total Factura</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class='table-group-divider'>
                                <?php if (!empty($compras)): ?>
                                    <?php foreach ($compras as $c): ?>
                                        <tr>
                                            <td><?php echo date("d/m/Y", strtotime($c['fecha_documento'])); ?></td>
                                            <td>
                                                <span style="display:block; font-weight: bold;"><?php echo $c['nro_factura']; ?></span>
                                                <small style="color: #64748b; display: block;">Ctrl: <?php echo $c['nro_control']; ?></small>
                                                <?php if(!empty($c['nro_comprobante_retencion'])): ?>
                                                    <small class="badge bg-primary-subtle text-primary p-1 mt-1" style="font-size: 0.75rem;">
                                                        <i class="fas fa-receipt me-1"></i> Ret: <?php echo htmlspecialchars($c['nro_comprobante_retencion']); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($c['nombre_proveedor']); ?></td>
                                            <td><?php echo number_format($c['base_imponible'], 2); ?> Bs.</td>
                                            <td class="text-danger fw-semibold">+<?php echo number_format($c['monto_iva'], 2); ?> Bs.</td>
                                            <td class="text-primary fw-bold" style="background: #f0fdf4;"><?php echo number_format($c['monto_exento'], 2); ?> Bs.</td>
                                            <td style="font-weight: 800; color: #1e293b;"><?php echo number_format($c['total_factura'], 2); ?> Bs.</td>
                                            <td>
                                                <button class="config-btn text-warning" title="Editar Compra" onclick="editarFactura(<?php echo $c['id_factura']; ?>)" style="background: transparent; border: none; margin-right: 5px;"><i class="fas fa-edit"></i></button>
                                                <a href="../BACKEND/eliminar_facturas.php?id_factura=<?php echo $c['id_factura'] ?>" class="btn btn-danger fs-6 text-white p-1" style="width: 28px; height: 28px; display: inline-flex; justify-content: center; align-items: center; padding: 0 !important;"> <i class="fa-solid fa-trash" style="font-size: 0.85rem;"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center; padding: 20px; color: #94a3b8;">No hay operaciones de compras registradas.</td>
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

                            <div class="col-md-12 d-none" id="grupo_comprobante">
                                <label class="form-label fw-bold text-primary"><i class="fas fa-receipt me-1"></i> Nro. Comprobante de Retención (SENIAT)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-file-signature text-muted"></i></span>
                                    <input type="text" name="nro_comprobante_retencion" id="nro_comprobante_retencion" class="form-control border-primary" placeholder="Ej: 20260600000001">
                                </div>
                                <small class="text-muted">Formato exigido por el SENIAT: Año (4d) + Mes (2d) + Secuencia (8d).</small>
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
        // FUNCIÓN: Alterna la visibilidad de los libros en caliente
        function cambiarLibro(tipo) {
            const tabVentas = document.getElementById('btnTabVentas');
            const tabCompras = document.getElementById('btnTabCompras');
            const contenedorVentas = document.getElementById('contenedorVentas');
            const contenedorCompras = document.getElementById('contenedorCompras');

            if (tipo === 'VENTAS') {
                tabVentas.classList.add('active-tab');
                tabCompras.classList.remove('active-tab');
                contenedorVentas.classList.remove('d-none');
                contenedorCompras.classList.add('d-none');
            } else {
                tabCompras.classList.add('active-tab');
                tabVentas.classList.remove('active-tab');
                contenedorCompras.classList.remove('d-none');
                contenedorVentas.classList.add('d-none');
            }
        }

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
            const grupoComprobante = document.getElementById('grupo_comprobante');
            const selectCliente = document.getElementById('id_empresa');
            const selectProveedor = document.getElementById('id_proveedor');
            const inputComprobante = document.getElementById('nro_comprobante_retencion');

            if (tipo === 'COMPRA') {
                grupoCliente.classList.add('d-none');
                grupoProveedor.classList.remove('d-none');
                grupoComprobante.classList.remove('d-none');
                selectCliente.required = false;
                selectProveedor.required = true;
            } else {
                grupoProveedor.classList.add('d-none');
                grupoCliente.classList.remove('d-none');
                grupoComprobante.classList.add('d-none');
                selectProveedor.required = false;
                selectCliente.required = true;
                inputComprobante.value = ""; // Se limpia al pasar a Venta
            }
        }

        function limpiarFormularioNuevaFactura() {
            document.getElementById('formFactura').action = "../BACKEND/guardar_factura.php";
            document.getElementById('tituloModal').innerHTML = '<i class="fas fa-file-invoice me-2"></i> Nueva Factura Fiscal';
            document.getElementById('edit_id_factura').value = "";
            document.getElementById('fecha_documento').value = "<?php echo date('Y-m-d'); ?>";
            document.getElementById('tipo_transaccion').value = "VENTA";
            document.getElementById('nro_factura').value = "";
            document.getElementById('nro_control').value = "";
            document.getElementById('nro_comprobante_retencion').value = "";
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

        function editarFactura(id) {
            fetch(`../BACKEND/obtener_factura_json.php?id=${id}`)
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success') {
                        const f = res.data;
                        
                        document.getElementById('formFactura').action = "../BACKEND/modificar_factura.php";
                        document.getElementById('tituloModal').innerHTML = '<i class="fas fa-edit me-2"></i> Modificar Factura Fiscal';
                        
                        document.getElementById('edit_id_factura').value = f.id_factura;
                        document.getElementById('fecha_documento').value = f.fecha_documento;
                        document.getElementById('tipo_transaccion').value = f.tipo_transaccion;
                        document.getElementById('nro_factura').value = f.nro_factura;
                        document.getElementById('nro_control').value = f.nro_control;
                        document.getElementById('base_modal').value = f.base_imponible;
                        document.getElementById('nro_comprobante_retencion').value = f.nro_comprobante_retencion || "";

                        alternarTerceros();
                        
                        if (f.tipo_transaccion === 'COMPRA') {
                            document.getElementById('id_proveedor').value = f.id_tercero;
                            document.getElementById('id_empresa').value = "";
                            document.getElementById('exento_modal').value = "0.00";
                            cambiarLibro('COMPRAS');
                        } else {
                            document.getElementById('id_empresa').value = f.id_empresa;
                            document.getElementById('id_proveedor').value = "";
                            document.getElementById('exento_modal').value = f.monto_exento;
                            cambiarLibro('VENTAS');
                        }
                        
                        calcularTotalesModal();
                        
                        const btn = document.getElementById('btnSubmitModal');
                        btn.className = "btn btn-warning text-white";
                        btn.style.background = "#f59e0b";
                        btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Actualizar Factura';

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