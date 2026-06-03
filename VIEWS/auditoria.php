<?php include_once('../BACKEND/conecxion_bd.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Auditoría de Sistema | Contable EA</title>
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
            <?php include_once('header.php'); ?>

            <section class="content p-4">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-history me-2"></i>Historial de Movimientos (Log)</h4>
                        <span class="badge bg-info">Seguridad Informática</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabla_auditoria" class="table table-striped table-hover border">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha/Hora</th>
                                        <th>Usuario</th>
                                        <th>Acción</th>
                                        <th>Tabla</th>
                                        <th>ID Ref.</th>
                                        <th>Detalles</th>
                                        <th>IP Origen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Usamos el nombre real: nombre_usuario
                                    $sql = "SELECT a.*, u.nombre_usuario 
                                            FROM auditoria_sistema a 
                                            LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario 
                                            ORDER BY a.fecha_accion DESC";
                                    $res = $conexion->query($sql);
                                    
                                    if ($res) {
                                        while($row = $res->fetch_assoc()): 
                                            $badge = 'bg-secondary';
                                            if($row['accion'] == 'INSERT') $badge = 'bg-success';
                                            if($row['accion'] == 'UPDATE') $badge = 'bg-warning text-dark';
                                            if($row['accion'] == 'DELETE') $badge = 'bg-danger';
                                    ?>
                                    <tr>
                                        <td class="small"><?php echo date("d/m/Y H:i:s", strtotime($row['fecha_accion'])); ?></td>
                                        <td><strong><?php echo $row['nombre_usuario'] ?? 'Sistema/Anon'; ?></strong></td>
                                        <td><span class="badge <?php echo $badge; ?>"><?php echo $row['accion']; ?></span></td>
                                        <td><code class="text-primary"><?php echo $row['tabla_afectada']; ?></code></td>
                                        <td>#<?php echo $row['id_registro_afectado']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="popover" title="Valores" 
                                                    data-bs-content="ANT: <?php echo htmlspecialchars($row['valor_anterior'] ?? 'N/A'); ?> | NVU: <?php echo htmlspecialchars($row['valor_nuevo'] ?? 'N/A'); ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                        <td class="small text-muted"><?php echo $row['ip_maquina']; ?></td>
                                    </tr>
                                    <?php 
                                        endwhile; 
                                    } 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="../JQUERY/jquery.js"></script>
    <script src="../DATATABLE/datatables1.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabla_auditoria').DataTable({
                "order": [[ 0, "desc" ]],
                "language": {
                    "sProcessing":     "Procesando...",
                    "sLengthMenu":     "Mostrar _MENU_ registros",
                    "sZeroRecords":    "No se encontraron resultados",
                    "sEmptyTable":     "Ningún dato disponible en esta tabla",
                    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sSearch":         "Buscar:",
                    "oPaginate": {
                        "sFirst":    "Primero",
                        "sLast":     "Último",
                        "sNext":     "Siguiente",
                        "sPrevious": "Anterior"
                    }
                }
            });

            // Inicializar Popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl)
            });
        });
    </script>
</body>
</html>