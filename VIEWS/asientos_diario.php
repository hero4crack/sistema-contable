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
                <a href="#"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>

        <main class="viewport">
            <?php include('header.php'); ?>

            <section class="content">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 20px;">
                        <h3>Libro Diario</h3>
                        <button class="primary-btn" style="background: #3b82f6;" data-bs-toggle="modal" data-bs-target="#modalAsiento">
                        <i class="fas fa-plus-circle"></i> NUEVO ASIENTO
                        </button>
                    </div>
                    
                    <div class="table-wrapper">
                    </div>
                </div>
            </section>
        </main>
    </div>

    <div class="modal fade" id="modalAsiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: #1e293b; color: white;">
                <h5 class="modal-title"><i class="fas fa-book"></i> Registrar Asiento Contable</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../BACKEND/guardar_asiento.php" method="POST">
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="fecha_asiento" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nro. Comprobante</label>
                            <input type="text" name="nro_comprobante" class="form-control" placeholder="DIARIO-001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Descripción / Glosa</label>
                            <input type="text" name="glosa" class="form-control" placeholder="Ej: Registro de venta según factura..." required>
                        </div>
                    </div>

                    <table class="table table-bordered" id="tablaAsiento">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50%;">Cuenta Contable</th>
                                <th>Debe ($)</th>
                                <th>Haber ($)</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoAsiento">
                            </tbody>
                        <tfoot>
                            <tr class="table-info">
                                <td class="text-end"><strong>TOTALES:</strong></td>
                                <td id="totalDebe">0.00</td>
                                <td id="totalHaber">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarFila()">
                        <i class="fas fa-plus"></i> Añadir Línea
                    </button>
                </div>
                <div class="modal-footer">
                    <div id="statusCuadrado" class="me-auto text-danger" style="font-weight: bold;">
                        <i class="fas fa-exclamation-triangle"></i> Asiento Descuadrado
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGuardarAsiento" class="btn btn-primary" disabled>Guardar Asiento</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <script src="../JAVASCRIPT/asiento_dinamico.js"></script>
    
    <script>
        // Esta función es el "puente". 
        // El JS la llamará cada vez que alguien dé clic en "Añadir Línea"
        function actualizarOpcionesCuentas(selectElement) {
            let opciones = `<option value="">Seleccione cuenta...</option>`;
            
            <?php 
                // Buscamos las cuentas en la BD usando PHP
                $cuentas = obtenerCuentasParaAsiento($conexion);
                while($c = $cuentas->fetch_assoc()): 
            ?>
                // Las inyectamos en el HTML que el JS va a construir
                opciones += `<option value="<?= $c['id_cuenta'] ?>"><?= $c['codigo_cuenta'] ?> - <?= $c['nombre_cuenta'] ?></option>`;
            <?php endwhile; ?>
            
            selectElement.innerHTML = opciones;
        }

        // Esto hace que al cargar la página ya aparezcan las primeras 2 filas vacías
        window.onload = () => { 
            agregarFila(); 
            agregarFila(); 
        };
    </script>
</body>
</html>