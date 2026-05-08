<?php
// 1. Conexión a la base de datos (usando tu archivo con "cx")
require_once '../BACKEND/conecxion_bd.php'; 

// 2. Llamamos al archivo de consultas que creamos hoy
require_once '../BACKEND/consulta_factura.php'; 

// 3. Obtenemos las facturas reales de la base de datos
// Pasamos $conexion porque es la variable que definiste en conecxion_bd.php
$facturas = obtenerLibroFacturas($conexion); 
?>

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
                        <button class="primary-btn" style="background: #10b981;" data-bs-toggle="modal" data-bs-target="#modalRegistro">
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
            <div class="col-md-12 mb-3">
              <label class="form-label" style="font-weight: 600;">Empresa Cliente</label>
              <select name="id_empresa" class="form-select" required>
                <option value="">Seleccione la empresa...</option>
                <?php 
                // Aquí usamos la variable que ya cargamos en el Backend
                $empresas = obtenerEmpresasParaFactura($conexion);
                while($emp = $empresas->fetch_assoc()): 
                ?>
                  <option value="<?php echo $emp['id_empresa']; ?>"><?php echo $emp['nombre_empresa']; ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nro. Factura</label>
              <input type="text" name="nro_factura" class="form-control" placeholder="0001" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nro. Control</label>
              <input type="text" name="nro_control" class="form-control" placeholder="00-001">
            </div>

            <div class="col-md-4 mt-4">
              <label class="form-label">Base Imponible ($)</label>
              <input type="number" step="0.01" name="base_imponible" id="base_modal" class="form-control" oninput="calcularIvaModal()" required>
            </div>
            <div class="col-md-4 mt-4">
              <label class="form-label">Monto Exento ($)</label>
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
</script>

</body>
</html>