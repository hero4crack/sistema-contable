<?php include_once('../BACKEND/conecxion_bd.php'); ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Entidades | Contable EA</title>
    <link rel="stylesheet" href="../DATATABLE/datatables1.css">
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/style_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../JAVASCRIPT/bootstrap.bundle.min.js"></script>
    <style>
        .seccion-titulo {
            background: #f8f9fa;
            padding: 5px 10px;
            border-left: 4px solid #0d6efd;
            margin-top: 15px;
            margin-bottom: 10px;
            font-size: 0.9rem;
            font-weight: bold;
            color: #495057;
        }
    </style>
</head>

<body>
    <div class="app-container">
        <aside class="main-sidebar">
            <div class="brand"><i class="fas fa-calculator"></i> CONTABLE EA</div>
            <nav class="menu">
                <a href="../VIEWS/inicio.php"><i class="fas fa-home"></i> Inicio</a>
                <a href="../VIEWS/empresas_clientes.php" class="active"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="../VIEWS/libro_facturas.php"><i class="fas fa-file-invoice"></i> Libro de Facturas</a>
                <a href="../VIEWS/asientos_diario.php"><i class="fas fa-book"></i> Asientos Diario</a>
                <a href="../VIEWS/libro_mayor.php"><i class="fas fa-chart-line"></i> Libro Mayor</a>
                <a href="../VIEWS/balance_comprobacion.php"><i class="fas fa-balance-scale"></i> Balance</a>
                <a href="../VIEWS/estado_resultados.php"><i class="fas fa-file-invoice-dollar"></i> Estado de Resultados</a>
                <a href="../VIEWS/empleados.php"><i class="fas fa-users"></i> Empleados</a>
                <a href="../VIEWS/catalogo_cuenta.php"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="#"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>

        <main class="viewport">
            <?php include_once('header.php'); ?>

            <section class="content">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; padding: 20px; border-bottom: 1px solid #e2e8f0;">
                        <h3 class="fw-bold">Gestión de Empresas</h3>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistro">
                            <i class="fas fa-plus"></i> AGREGAR CLIENTE
                        </button>

                        <!-- MODAL DE REGISTRO -->
                        <div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="modalRegistroLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h1 class="modal-title fs-5 fw-bold" id="modalRegistroLabel">REGISTRO DE EMPRESA CLIENTE</h1>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="../BACKEND/registro_empresa.php">
                                            <div class="seccion-titulo">DATOS FISCALES DE LA EMPRESA</div>
                                            <div class="row g-2">
                                                <div class="col-md-6 p-1">
                                                    <label class="small fw-bold">Nombre de la Empresa</label>
                                                    <input class="form-control" type="text" name="empresa" placeholder="Nombre Fantasía" required>
                                                </div>
                                                <div class="col-md-6 p-1">
                                                    <label class="small fw-bold">RIF</label>
                                                    <div class="input-group">
                                                        <select class="form-select" style="max-width: 80px;" name="letra">
                                                            <option value="J-">J-</option>
                                                            <option value="V-">V-</option>
                                                            <option value="G-">G-</option>
                                                            <option value="E-">E-</option>
                                                        </select>
                                                        <input class="form-control" type="text" name="rif" placeholder="12345678-0" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-8 p-1">
                                                    <label class="small fw-bold">Razón Social</label>
                                                    <input class="form-control" type="text" name="social" placeholder="Nombre Legal Completo" required>
                                                </div>
                                                <div class="col-md-4 p-1">
                                                    <label class="small fw-bold">Contribuyente</label>
                                                    <select class="form-select" name="contribuyente">
                                                        <option value="ORDINARIO">Ordinario</option>
                                                        <option value="ESPECIAL">Especial</option>
                                                        <option value="FORMAL">Formal</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12 p-1">
                                                    <label class="small fw-bold">Dirección Fiscal</label>
                                                    <input class="form-control" type="text" name="direccion" placeholder="Dirección completa" required>
                                                </div>
                                                <div class="col-md-4 p-1">
                                                    <label class="small fw-bold">Teléfono Empresa</label>
                                                    <input class="form-control" type="text" name="telefono" placeholder="0212-0000000">
                                                </div>
                                                <div class="col-md-4 p-1">
                                                    <label class="small fw-bold">Correo Empresa</label>
                                                    <input class="form-control" type="email" name="correo" placeholder="empresa@correo.com">
                                                </div>
                                                <div class="col-md-4 p-1">
                                                    <label class="small fw-bold">País</label>
                                                    <input class="form-control" type="text" name="pais" value="Venezuela">
                                                </div>
                                            </div>

                                            <div class="seccion-titulo">DATOS DEL RESPONSABLE (Evaluación Prof.)</div>
                                            <div class="row g-2">
                                                <div class="col-md-6 p-1">
                                                    <label class="small fw-bold">Nombre del Responsable</label>
                                                    <input class="form-control" type="text" name="nombre_responsable" required>
                                                </div>
                                                <div class="col-md-6 p-1">
                                                    <label class="small fw-bold">Cédula</label>
                                                    <input class="form-control" type="text" name="cedula_responsable" required>
                                                </div>
                                                <div class="col-md-6 p-1">
                                                    <label class="small fw-bold">Teléfono Responsable</label>
                                                    <input class="form-control" type="text" name="telefono_responsable">
                                                </div>
                                                <div class="col-md-6 p-1">
                                                    <label class="small fw-bold">Correo Responsable</label>
                                                    <input class="form-control" type="email" name="correo_responsable">
                                                </div>
                                                <div class="col-md-12 p-1">
                                                    <label class="small fw-bold">Estatus de Cuenta</label>
                                                    <select class="form-select" name="estado">
                                                        <option value="1">Activo</option>
                                                        <option value="0">Inactivo</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <button type="submit" class="btn btn-primary w-100">REGISTRAR EMPRESA</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    $sql = "SELECT * FROM empresas_clientes";
                    $result = $conexion->query($sql);
                    if ($result->num_rows > 0) {
                    ?>
                        <div class="table-wrapper p-3">
                            <table id='tabla' class="table table-hover table-bordered shadow-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Empresa / RIF</th>
                                        <th>Responsable / Telf</th>
                                        <th>Contribuyente</th>
                                        <th>Estatus</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td>#<?php echo $row['id_empresa']; ?></td>
                                            <td>
                                                <div class="fw-bold"><?php echo $row['nombre_empresa']; ?></div>
                                                <small class="text-muted"><?php echo $row['rif']; ?></small>
                                            </td>
                                            <td>
                                                <div class="fw-bold"><?php echo $row['nombre_responsable']; ?></div>
                                                <div class="text-muted small"><i class="fas fa-phone me-1"></i><?php echo $row['telefono_responsable']; ?></div>
                                            </td>
                                            <td><span class="badge bg-info text-dark"><?php echo $row['tipo_contribuyente']; ?></span></td>
                                            <td>
                                                <?php if ($row['estado_activo'] == 1): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <!-- BOTÓN VER INFO COMPLETA -->
                                                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $row['id_empresa'] ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <!-- BOTÓN EDITAR -->
                                                    <button type="button" class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id_empresa'] ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <!-- BOTÓN ELIMINAR -->
                                                    <a href="../BACKEND/eliminar_empresa.php?id_empresa=<?php echo $row['id_empresa'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta empresa?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>

                                                <!-- 1. MODAL VER TODA LA INFORMACIÓN -->
                                                <div class="modal fade" id="viewModal<?php echo $row['id_empresa'] ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-info text-white">
                                                                <h5 class="modal-title fw-bold"><i class="fas fa-building me-2"></i>Detalle de Cliente</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                <p class="mb-1"><strong>Razón Social:</strong> <?php echo $row['razon_social']; ?></p>
                                                                <p class="mb-1"><strong>RIF:</strong> <?php echo $row['rif']; ?></p>
                                                                <p class="mb-1"><strong>Dirección:</strong> <?php echo $row['direccion_fiscal']; ?></p>
                                                                <p class="mb-1"><strong>Teléfono Empresa:</strong> <?php echo $row['telefono']; ?></p>
                                                                <p class="mb-1"><strong>Correo Empresa:</strong> <?php echo $row['correo_electronico']; ?></p>
                                                                <p class="mb-1"><strong>País:</strong> <?php echo $row['pais']; ?></p>
                                                                <hr>
                                                                <h6 class="fw-bold text-primary">Información del Responsable</h6>
                                                                <p class="mb-1"><strong>Nombre:</strong> <?php echo $row['nombre_responsable']; ?></p>
                                                                <p class="mb-1"><strong>Cédula:</strong> <?php echo $row['cedula_responsable']; ?></p>
                                                                <p class="mb-1"><strong>Teléfono:</strong> <?php echo $row['telefono_responsable']; ?></p>
                                                                <p class="mb-1"><strong>Correo:</strong> <?php echo $row['correo_responsable']; ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 2. MODAL EDITAR COMPLETO -->
                                                <div class="modal fade" id="editModal<?php echo $row['id_empresa'] ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content text-start">
                                                            <div class="modal-header bg-warning">
                                                                <h5 class="modal-title fw-bold text-white"><i class="fas fa-edit me-2"></i>EDITAR ENTIDAD #<?php echo $row['id_empresa'] ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="POST" action="../BACKEND/editar_empresa.php">
                                                                <input type="hidden" name="id" value="<?php echo $row['id_empresa'] ?>">
                                                                <div class="modal-body">
                                                                    <div class="seccion-titulo">DATOS DE LA EMPRESA</div>
                                                                    <div class="row g-2">
                                                                        <div class="col-md-6 mb-2">
                                                                            <label class="form-label small fw-bold">Nombre Empresa</label>
                                                                            <input class="form-control" type="text" name="empresa" value="<?php echo $row['nombre_empresa'] ?>" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-2">
                                                                            <label class="form-label small fw-bold">RIF</label>
                                                                            <input class="form-control" type="text" name="rif" value="<?php echo $row['rif'] ?>" required>
                                                                        </div>
                                                                        <div class="col-md-8 mb-2">
                                                                            <label class="form-label small fw-bold">Razón Social</label>
                                                                            <input class="form-control" type="text" name="social" value="<?php echo $row['razon_social'] ?>" required>
                                                                        </div>
                                                                        <div class="col-md-4 mb-2">
                                                                            <label class="form-label small fw-bold">Contribuyente</label>
                                                                            <select class="form-select" name="contribuyente">
                                                                                <option value="ORDINARIO" <?php echo ($row['tipo_contribuyente'] == 'ORDINARIO') ? 'selected' : ''; ?>>Ordinario</option>
                                                                                <option value="ESPECIAL" <?php echo ($row['tipo_contribuyente'] == 'ESPECIAL') ? 'selected' : ''; ?>>Especial</option>
                                                                                <option value="FORMAL" <?php echo ($row['tipo_contribuyente'] == 'FORMAL') ? 'selected' : ''; ?>>Formal</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-7 mb-2">
                                                                            <label class="form-label small fw-bold">Dirección Fiscal</label>
                                                                            <input class="form-control" type="text" name="direccion" value="<?php echo $row['direccion_fiscal'] ?>" required>
                                                                        </div>
                                                                        <div class="col-md-4 mb-2">
                                                                            <label class="form-label small fw-bold" for="estado">Estado:</label>
                                                                            <select class="form-select" id="estado" name="estado" required>
                                                                                <option value="1">Activo</option>
                                                                                <option value="0">Inactivo</option>
                                                                            </select>
                                                                        </div>

                                                                    </div>
                                                                    <div class="seccion-titulo mt-3">DATOS DEL RESPONSABLE</div>
                                                                    <div class="row g-2">
                                                                        <div class="col-md-6 mb-2">
                                                                            <label class="form-label small fw-bold">Nombre Responsable</label>
                                                                            <input class="form-control" type="text" name="nombre_responsable" value="<?php echo $row['nombre_responsable'] ?>" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-2">
                                                                            <label class="form-label small fw-bold">Cédula</label>
                                                                            <input class="form-control" type="text" name="cedula_responsable" value="<?php echo $row['cedula_responsable'] ?>" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-2">
                                                                            <label class="form-label small fw-bold">Teléfono Responsable</label>
                                                                            <input class="form-control" type="text" name="telefono_responsable" value="<?php echo $row['telefono_responsable'] ?>">
                                                                        </div>
                                                                        <div class="col-md-6 mb-2">
                                                                            <label class="form-label small fw-bold">Correo Responsable</label>
                                                                            <input class="form-control" type="email" name="correo_responsable" value="<?php echo $row['correo_responsable'] ?>">
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
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php
                    } else {
                        echo "<div class='p-5 text-center'>No hay empresas registradas aún.</div>";
                    }
                    $conexion->close();
                    ?>
                </div>
            </section>
        </main>
    </div>

    <script src="../JQUERY/jquery.js"></script>
    <script src="../DATATABLE/datatables1.js"></script>

    <script>
        $(document).ready(function() {
            $('#tabla').DataTable({
                lengthMenu: [5, 10, 25, 50],
                pageLength: 10,
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar:",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    }
                }
            });
        });
    </script>
</body>

</html>