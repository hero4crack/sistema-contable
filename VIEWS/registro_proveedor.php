
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Proveedores | Contable EA</title>
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
                <a href="../VIEWS/registro_proveedor.php"><i class="fas fa-city"></i> Proveedores</a>
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
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                        <h5 class="mb-0 text-primary fw-bold">
                            <i class="fas fa-boxes me-2 text-muted"></i> Ficha de Registro de Proveedor
                        </h5>
                    </div>
                    
                    <div class="card-body p-4">
                        <form action="../BACKEND/guardar_proveedor.php" method="POST">
                            
                            <h6 class="text-secondary border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-id-card me-2"></i>Datos Fiscales Básicos</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">RIF del Proveedor <span class="text-danger">*</span></label>
                                    <input type="text" name="rif" class="form-control" placeholder="Ej: J-12345678-9" required pattern="^[JGVGjgwg]-\d{8}-\d$|^[JGVGjgwg]\d{9}$">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">Razón Social <span class="text-danger">*</span></label>
                                    <input type="text" name="razon_social" class="form-control" placeholder="Nombre legal registrado" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Nombre Comercial</label>
                                    <input type="text" name="nombre_comercial" class="form-control" placeholder="Nombre de la firma o tienda">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tipo Contribuyente</label>
                                    <select name="tipo_contribuyente" class="form-select">
                                        <option value="ORDINARIO">Ordinario</option>
                                        <option value="FORMAL">Formal</option>
                                        <option value="ESPECIAL">Especial</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-primary fw-bold"><i class="fas fa-percentage me-1"></i> Porcentaje Retención (IVA)</label>
                                    <select name="porcentaje_retencion" class="form-select border-primary bg-light-subtle fw-semibold">
                                        <option value="0">0% (Sin retención / Exento)</option>
                                        <option value="75">75% (Retención General SENIAT)</option>
                                        <option value="100">100% (Retención Especial / Sin RIF Ubicable)</option>
                                    </select>
                                </div>
                            </div>

                            <h6 class="text-secondary border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-map-marker-alt me-2"></i>Ubicación e Información de Contacto</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Dirección Fiscal <span class="text-danger">*</span></label>
                                    <textarea name="direccion_fiscal" class="form-control" rows="2" placeholder="Dirección legal completa reflejada en el RIF" required></textarea>
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

                            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                <a href="inicio.php" class="btn btn-secondary bg-opacity-10 text-dark border-0 px-4">Cancelar</a>
                                <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class="fas fa-save me-2"></i>Guardar Proveedor</button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </main>
    </div>

<?php include('script.php'); ?>
</body>
</html>