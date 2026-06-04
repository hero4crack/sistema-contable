<?php
require_once '../BACKEND/conecxion_bd.php';
require_once '../BACKEND/consulta_proveedor.php';

$proveedores = obtenerListaProveedores($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Proveedores | Contable EA</title>
    <link rel="stylesheet" href="../DATATABLE/datatables1.css">
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
                <a href="../VIEWS/registro_proveedor.php" class="active"><i class="fas fa-truck"></i> Proveedores</a>
                <a href="../VIEWS/libro_facturas.php"><i class="fas fa-file-invoice"></i> Libro de Facturas</a>
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
            <?php include('header.php'); ?>

            <div class="container-fluid px-4 py-4">
                
                <?php if(isset($_SESSION['msg_auditoria'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['msg_tipo']; ?> alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas <?php echo ($_SESSION['msg_tipo'] == 'success') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> me-2"></i>
                        <?php 
                            echo $_SESSION['msg_auditoria']; 
                            unset($_SESSION['msg_auditoria']);
                            unset($_SESSION['msg_tipo']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0 row-base mb-4" style="border-radius: 10px;">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary fw-bold">
                            <i class="fas fa-truck me-2 text-muted"></i> Libro de Proveedores Registrados
                        </h5>
                        <button class="primary-btn" style="background: #10b981; border: none; padding: 8px 16px; color: white; border-radius: 6px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalProveedor">
                            <i class="fas fa-plus-circle"></i> REGISTRAR NUEVO PROVEEDOR
                        </button>
                    </div>
                    
                    <div class="table-wrapper p-3">
                        <table id="tabla" class="table table-hover table-bordered shadow-sm ">
                            <thead class="table-dark">
                                <tr>
                                    <th>RIF</th>
                                    <th>Razón Social / Nombre Comercial</th>
                                    <th>Contribuyente</th>
                                    <th>Retención</th>
                                    <th>Teléfono</th>
                                    <th>Contacto / Asesor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php if ($proveedores && $proveedores->num_rows > 0): ?>
                                    <?php while ($p = $proveedores->fetch_assoc()): ?>
                                        <tr>
                                            <td class="fw-bold text-dark"><?php echo $p['rif']; ?></td>
                                            <td>
                                                <span style="display:block; font-weight: 600;"><?php echo htmlspecialchars($p['razon_social']); ?></span>
                                                <?php if(!empty($p['nombre_comercial'])): ?>
                                                    <small class="text-muted">Comercial: <?php echo htmlspecialchars($p['nombre_comercial']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary border-0 px-2 py-1"><?php echo $p['tipo_contribuyente']; ?></span></td>
                                            <td class="fw-bold text-primary"><?php echo $p['porcentaje_retencion']; ?>%</td>
                                            <td><?php echo (!empty($p['telefono'])) ? $p['telefono'] : '<span class="text-muted">-</span>'; ?></td>
                                            <td>
                                                <span style="display:block;"><?php echo htmlspecialchars($p['nombre_contacto']); ?></span>
                                                <small class="text-muted"><?php echo $p['telefono_contacto']; ?></small>
                                            </td>
                                            <td>
                                                <button class="config-btn btn-ver-ficha" title="Ver Ficha" data-id="<?php echo $p['id_proveedor']; ?>"><i class="fas fa-eye"></i></button>
                                                <button class="btn btn-warning fs-6 text-white p-1 btn-editar-proveedor" title="Editar" data-id="<?php echo $p['id_proveedor']; ?>" style="border-radius: 4px; width: 28px; height: 28px; display: inline-flex; justify-content: center; align-items: center; background: #f59e0b; border: none; margin-right: 2px;"><i class="fas fa-pen" style="font-size: 0.85rem;"></i></button>
                                                <a href="../BACKEND/eliminar_proveedor.php?id=<?php echo $p['id_proveedor']; ?>" class="btn btn-danger fs-6 text-white p-1" title="Inactivar" style="width: 28px; height: 28px; display: inline-flex; justify-content: center; align-items: center; padding: 0 !important;"><i class="fa-solid fa-trash" style="font-size: 0.85rem;"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align:center; padding: 20px; color: #94a3b8;">No hay proveedores registrados en el sistema.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <div class="modal fade" id="modalProveedor" tabindex="-1" aria-labelledby="modalProveedorLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <div class="modal-header" style="background: #1e293b; color: white; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" id="modalProveedorLabel"><i class="fas fa-truck"></i> Nueva Ficha de Proveedor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../BACKEND/guardar_proveedor.php" method="POST">
                    <div class="modal-body" style="padding: 30px;">
                        
                        <h6 class="text-secondary border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-id-card me-2"></i>Datos Fiscales Básicos</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">RIF del Proveedor <span class="text-danger">*</span></label>
                                <input type="text" name="rif" class="form-control" placeholder="Ej: J-12345678-9" required pattern="^[JGVGjgwg]-\d{8}-\d$|^[JGVGjgwg]\d{9}$">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Razón Social <span class="text-danger">*</span></label>
                                <input type="text" name="razon_social" class="form-control" placeholder="Nombre legal registrado" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre Comercial</label>
                                <input type="text" name="nombre_comercial" class="form-control" placeholder="Nombre de la firma o tienda">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tipo Contribuyente</label>
                                <select name="tipo_contribuyente" class="form-select">
                                    <option value="ORDINARIO">Ordinario</option>
                                    <option value="FORMAL">Formal</option>
                                    <option value="ESPECIAL">Especial</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-primary fw-bold"><i class="fas fa-percentage me-1"></i> Retención (IVA)</label>
                                <select name="porcentaje_retencion" class="form-select border-primary bg-light-subtle fw-semibold">
                                    <option value="0">0% (Exento)</option>
                                    <option value="75" selected>75% (Estándar)</option>
                                    <option value="100">100% (Especial)</option>
                                </select>
                            </div>
                        </div>

                        <h6 class="text-secondary border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-map-marker-alt me-2"></i>Ubicación e Información de Contacto</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Dirección Fiscal <span class="text-danger">*</span></label>
                                <textarea name="direccion_fiscal" class="form-control" rows="2" placeholder="Dirección legal completa del RIF" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Teléfono de la Empresa</label>
                                <input type="text" name="telefono" class="form-control" placeholder="Ej: 0242-XXXXXXX">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Correo Electrónico Corporativo</label>
                                <input type="email" name="correo_electronico" class="form-control" placeholder="administracion@proveedor.com">
                            </div>
                        </div>

                        <h6 class="text-secondary border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-user-tie me-2"></i>Asesor o Persona de Contacto</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre del Asesor de Ventas</label>
                                <input type="text" name="nombre_contacto" class="form-control" placeholder="Ej: Carlos Pérez">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Teléfono Directo del Contacto</label>
                                <input type="text" name="telefono_contacto" class="form-control" placeholder="Ej: 0412-XXXXXXX">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer" style="background: #f8fafc; border-radius: 0 0 15px 15px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" style="background: #10b981; border: none; padding: 10px 25px;"><i class="fas fa-save me-2"></i>Guardar Proveedor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarProveedor" tabindex="-1" aria-labelledby="modalEditarProveedorLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <div class="modal-header" style="background: #0f172a; color: white; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" id="modalEditarProveedorLabel"><i class="fas fa-pen-to-square me-2"></i> Modificar Expediente de Proveedor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../BACKEND/editar_proveedor.php" method="POST" id="formEditarProveedor">
                    <input type="hidden" name="id_proveedor" id="edit_id_proveedor">
                    
                    <div class="modal-body" style="padding: 30px;">
                        
                        <h6 class="text-secondary border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-id-card me-2"></i>Datos Fiscales Básicos</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">RIF del Proveedor <span class="text-danger">*</span></label>
                                <input type="text" name="rif" id="edit_rif" class="form-control" required pattern="^[JGVGjgwg]-\d{8}-\d$|^[JGVGjgwg]\d{9}$">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Razón Social <span class="text-danger">*</span></label>
                                <input type="text" name="razon_social" id="edit_razon_social" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre Comercial</label>
                                <input type="text" name="nombre_comercial" id="edit_nombre_comercial" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tipo Contribuyente</label>
                                <select name="tipo_contribuyente" id="edit_tipo_contribuyente" class="form-select">
                                    <option value="ORDINARIO">Ordinario</option>
                                    <option value="FORMAL">Formal</option>
                                    <option value="ESPECIAL">Especial</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-warning fw-bold"><i class="fas fa-percentage me-1"></i> Retención (IVA)</label>
                                <select name="porcentaje_retencion" id="edit_porcentaje_retencion" class="form-select border-warning bg-light-subtle fw-semibold">
                                    <option value="0">0% (Exento)</option>
                                    <option value="75">75% (Estándar)</option>
                                    <option value="100">100% (Especial)</option>
                                </select>
                            </div>
                        </div>

                        <h6 class="text-secondary border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-map-marker-alt me-2"></i>Ubicación e Información de Contacto</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Dirección Fiscal <span class="text-danger">*</span></label>
                                <textarea name="direccion_fiscal" id="edit_direccion_fiscal" class="form-control" rows="2" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Teléfono de la Empresa</label>
                                <input type="text" name="telefono" id="edit_telefono" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Correo Electrónico Corporativo</label>
                                <input type="email" name="correo_electronico" id="edit_correo_electronico" class="form-control">
                            </div>
                        </div>

                        <h6 class="text-secondary border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-user-tie me-2"></i>Asesor o Persona de Contacto</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre del Asesor de Ventas</label>
                                <input type="text" name="nombre_contacto" id="edit_nombre_contacto" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Teléfono Directo del Contacto</label>
                                <input type="text" name="telefono_contacto" id="edit_telefono_contacto" class="form-control">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer" style="background: #f8fafc; border-radius: 0 0 15px 15px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning text-white" style="background: #f59e0b; border: none; padding: 10px 25px;"><i class="fas fa-sync-alt me-2"></i>Actualizar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVerFicha" tabindex="-1" aria-labelledby="modalVerFichaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: #0f172a; color: white; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" id="modalVerFichaLabel"><i class="fas fa-id-card-alt me-2"></i> Ficha Técnica del Proveedor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="contenido_ficha">
                    </div>
                <div class="modal-footer" style="background: #f8fafc; border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar Ficha</button>
                </div>
            </div>
        </div>
    </div>

<?php include('script.php'); ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // ----------------------------------------------------
    // LÓGICA ASÍNCRONA: MOSTRAR FICHA TÉCNICA (HTML)
    // ----------------------------------------------------
    const botonesVer = document.querySelectorAll('.btn-ver-ficha');
    botonesVer.forEach(boton => {
        boton.addEventListener('click', function() {
            const idProveedor = this.getAttribute('data-id');
            const contenedorFicha = document.getElementById('contenido_ficha');
            
            contenedorFicha.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2">Consultando datos fiscales...</p>
                </div>`;
            
            const modalFicha = new bootstrap.Modal(document.getElementById('modalVerFicha'));
            modalFicha.show();
            
            fetch(`../BACKEND/obtener_proveedor.php?id=${idProveedor}`)
                .then(response => response.text())
                .then(html => {
                    contenedorFicha.innerHTML = html;
                })
                .catch(error => {
                    contenedorFicha.innerHTML = `<div class="alert alert-danger">Error en la comunicación con el servidor.</div>`;
                    console.error('Error:', error);
                });
        });
    });

    // ----------------------------------------------------
    // LÓGICA ASÍNCRONA: COPIAR DATOS AL FORMULARIO DE EDICIÓN (JSON)
    // ----------------------------------------------------
    const botonesEditar = document.querySelectorAll('.btn-editar-proveedor');
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarProveedor'));

    botonesEditar.forEach(boton => {
        boton.addEventListener('click', function() {
            const idProveedor = this.getAttribute('data-id');

            // Petición al backend para capturar la estructura JSON
            fetch(`../BACKEND/obtener_proveedor_json.php?id=${idProveedor}`)
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success') {
                        const p = res.data;
                        
                        // Rellenar dinámicamente los elementos inputs del Modal de Edición
                        document.getElementById('edit_id_proveedor').value = p.id_proveedor;
                        document.getElementById('edit_rif').value = p.rif;
                        document.getElementById('edit_razon_social').value = p.razon_social;
                        document.getElementById('edit_nombre_comercial').value = p.nombre_comercial || '';
                        document.getElementById('edit_tipo_contribuyente').value = p.tipo_contribuyente;
                        document.getElementById('edit_porcentaje_retencion').value = p.porcentaje_retencion;
                        document.getElementById('edit_direccion_fiscal').value = p.direccion_fiscal;
                        document.getElementById('edit_telefono').value = p.telefono || '';
                        document.getElementById('edit_correo_electronico').value = p.correo_electronico || '';
                        document.getElementById('edit_nombre_contacto').value = p.nombre_contacto || '';
                        document.getElementById('edit_telefono_contacto').value = p.telefono_contacto || '';

                        // Mostrar el Modal estructurado una vez cargados los datos
                        modalEditar.show();
                    } else {
                        alert("Error: " + res.message);
                    }
                })
                .catch(error => {
                    alert("No se pudieron recuperar los datos fiscales para edición.");
                    console.error('Error en JSON Fetch:', error);
                });
        });
    });
});
</script>
</body>
</html>