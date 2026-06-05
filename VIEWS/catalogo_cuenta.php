<?php
// 1. Llamamos al archivo que tiene la consulta
// Usamos ../ para salir de VIEWS y entrar a BACKEND
require_once '../BACKEND/conecxion_bd.php';
require_once '../BACKEND/consulta_catalogo.php';

// 2. Ejecutamos la función para obtener los datos
$datos_catalogo = obtenerCatalogo($conexion);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Catálogo de Cuentas | Contable EA</title>
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
                <a href="../VIEWS/registro_proveedor.php"><i class="fas fa-truck"></i> Proveedores</a>
                <a href="../VIEWS/libro_facturas.php"><i class="fas fa-file-invoice"></i> Libro de Facturas</a>
                <a href="../VIEWS/asientos_diario.php"><i class="fas fa-book"></i> Asientos Diario</a>
                <a href="../VIEWS/libro_mayor.php"><i class="fas fa-chart-line"></i> Libro Mayor</a>
                <a href="../VIEWS/balance_comprobacion.php"><i class="fas fa-balance-scale"></i> Balance</a>
                <a href="../VIEWS/estado_resultados.php"><i class="fas fa-file-invoice-dollar"></i> Estado de Resultados</a>
                <a href="../VIEWS/empleados.php"><i class="fas fa-users"></i> Empleados</a>
                <a href="../VIEWS/catalogo_cuenta.php" class="active"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="../VIEWS/auditoria.php"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>


        <main class="viewport">

            <?php include_once('header.php') ?>

            <section class="content">
                <div class="table-wrapper">
                    <table class="contable-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre de la Cuenta</th>
                                <th>Tipo</th>
                                <th>Nivel</th>
                                <th>Movimiento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // 3. Recorremos los datos de la base de datos
                            while ($cuenta = $datos_catalogo->fetch_assoc()):
                                // Calculamos la sangría según el nivel
                                $sangria = ($cuenta['nivel'] - 1) * 25;
                            ?>
                                <tr class="nivel-<?php echo $cuenta['nivel']; ?>">
                                    <td><?php echo $cuenta['codigo_cuenta']; ?></td>

                                    <td style="padding-left: <?php echo $sangria; ?>px;">
                                        <?php echo $cuenta['nombre_cuenta']; ?>
                                    </td>

                                    <td><?php echo $cuenta['tipo_cuenta']; ?></td>
                                    <td><?php echo $cuenta['nivel']; ?></td>

                                    <td>
                                        <?php echo ($cuenta['permite_movimiento'] == 1) ? '✅ Sí' : '❌ No'; ?>
                                    </td>

                                    <td>
                                        <button class="config-btn"><i class="fas fa-edit" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $cuenta['id_cuenta'] ?>"></i></button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="editModal<?php echo $cuenta['id_cuenta'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content text-start">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title fw-bold text-white"><i class="fas fa-edit me-2"></i>EDITAR ENTIDAD #<?php echo $cuenta['id_cuenta'] ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="../BACKEND/editar_empresa.php">
                                                        <input type="hidden" name="id" value="<?php echo $cuenta['id_cuenta'] ?>">
                                                        <div class="modal-body">
                                                            <div class="seccion-titulo">DATOS DE LA EMPRESA</div>
                                                            <div class="row g-2">
                                                                <div class="col-md-6 mb-2">
                                                                    <label class="form-label small fw-bold">Nombre de cuenta</label>
                                                                    <input class="form-control" type="text" name="empresa" value="<?php echo $cuenta['nombre_cuenta'] ?>" required>
                                                                </div>

                                                                <div class="col-md-4 mb-2">
                                                                    <label class="form-label small fw-bold">Nivel</label>
                                                                    <input class="form-control" type="text" name="social" value="<?php echo $cuenta['nivel'] ?>" required>
                                                                </div>
                                                                <div class="col-md-4 mb-2">
                                                                    <label class="form-label small fw-bold">Tipo</label>
                                                                    <select class="form-select" name="contribuyente">
                                                                        <option value="Activo" <?php echo ($cuenta['tipo_cuenta'] == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                                                                        <option value="Pasivo" <?php echo ($cuenta['tipo_cuenta'] == 'Pasivo') ? 'selected' : ''; ?>>Pasivo</option>
                                                                        <option value="Patrimonio" <?php echo ($cuenta['tipo_cuenta'] == 'Patrimonio') ? 'selected' : ''; ?>>Patrimonio</option>
                                                                        <option value="Ingreso" <?php echo ($cuenta['tipo_cuenta'] == 'Ingreso') ? 'selected' : ''; ?>>Ingreso</option>
                                                                        <option value="Egreso" <?php echo ($cuenta['tipo_cuenta'] == 'Egreso') ? 'selected' : ''; ?>>Egreso</option>
                                                                    </select>
                                                                </div>
                                                                
                                                                <div class="col-md-4 mb-2">
                                                                    <label class="form-label small fw-bold">Movimiento:</label>
                                                                    <select class="form-select" id="movimiento" name="movimiento" required>
                                                                        <option value="1">Permite</option>
                                                                        <option value="0">No permite</option>
                                                                    </select>
                                                                </div>

                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-warning text-white fw-bold w-100">GUARDAR CAMBIOS</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <?php include('script.php'); ?>
</body>

</html>