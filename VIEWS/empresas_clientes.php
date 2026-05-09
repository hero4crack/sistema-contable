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

</head>

<body>
    <div class="app-container">
        <aside class="main-sidebar">
            <div class="brand"><i class="fas fa-calculator"></i> CONTABLE EA</div>
            <nav class="menu">
                <a href="../VIEWS/inicio.php"><i class="fas fa-home"></i>Inicio</a>
                <a href="../VIEWS/empresas_clientes.php" class="active"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="../VIEWS/libro_facturas.php"><i class="fas fa-file-invoice""></i> Libro de Facturas</a>
                <a href=" ../VIEWS/asientos_diario.php"><i class="fas fa-book"></i> Asientos Diario</a>
                <a href="../VIEWS/libro_mayor.php"><i class="fas fa-chart-line"></i> Libro Mayor</a>
                <a href="../VIEWS/balance_comprobacion.php"><i class="fas fa-balance-scale"></i> Balance</a>
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
                    
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <i class="fas fa-plus"></i> AGREGAR CLIENTE
                        </button>


                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5 fw-bold text-center" id="exampleModalLabel">REGISTRO</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <section class="form form-register">
                                            <form method="POST" action="../BACKEND/registro_empresa.php">
                                                <div class="row">
                                                    <div class="col align-self-center p-2">
                                                        <input class="form-control" type="text" name="empresa" id="empresa" placeholder="Ingrese su empresa" required>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="input-group">
                                                        <select class="form-select fs-6 mb-2" id="letra" name="letra">

                                                            <option value="V-">(Venezolano) V-</option>
                                                            <option value="J-">(Juridico) J-</option>
                                                            <option value="E-">(Extranjero) E-</option>
                                                        </select>
                                                        <input class="form-control" type="text" name="rif" id="rif" placeholder="Ingrese su RIF" required>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col align-self-center p-2">
                                                        <input class="form-control" type="text" name="social" id="social" placeholder="Ingrese su razon social" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">

                                                    <select class="form-select fs-6" id="contribuyente" name="contribuyente">
                                                        <option disabled selected> Elija su tipo de contribuyente</option>
                                                        <option value="ORDINARIO">Ordinario</option>
                                                        <option value="ESPECIAL">Especial</option>
                                                        <option value="FORMAL">Formal</option>
                                                    </select>
                                                </div>


                                                <div class="row">
                                                    <div class="col align-self-center p-2">
                                                        <input class="form-control" type="text" name="direccion" id="direccion" placeholder="Ingrese su direccion" required>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col align-self-center p-2">
                                                        <input class="form-control" type="text" name="telefono" id="telefono" placeholder="Ingrese su telefono" required>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col align-self-center p-2">
                                                        <input class="form-control" type="text" name="correo" id="correo" placeholder="Ingrese su correo" required>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col align-self-center p-2">
                                                        <input class="form-control" type="text" name="pais" id="pais" placeholder="Ingrese su pais" required>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col align-self-center p-2">
                                                        <select class="form-select fs-6" id="contribuyente" name="contribuyente">
                                                            <option value="1">Activo</option>
                                                            <option value="0">No activo</option>

                                                        </select>
                                                    </div>
                                                </div>


                                                <input class="btn btn-primary m-1" type="submit" value="Registrar">
                                            </form>

                                        </section>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <?php $sql = "SELECT * FROM empresas_clientes";
                    $result = $conexion->query($sql);
                    if ($result->num_rows > 0) {

                    ?>

                        <div class="table-wrapper">
                            <table id='tabla' class="contable-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Empresa</th>
                                        <th>RIF</th>
                                        <th>Razón Social</th>
                                        <th>Contribuyente</th>
                                        <th>Dirección Fiscal</th>
                                        <th>Telefono</th>
                                        <th>Correo Electronico</th>
                                        <th>País</th>
                                        <th>Estatus</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class='table-group-divider'>
                                    <?php while ($row = $result->fetch_assoc()) { ?>


                                        <tr>
                                            <td>#<?php echo ($row['id_empresa']); ?> </td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <div style="width: 30px; height: 30px; background: #dbeafe; border-radius: 4px; display: grid; place-items: center; font-size: 10px; font-weight: bold;">
                                                        <?php echo substr($row['nombre_empresa'], 0, 1); ?>
                                                    </div>
                                                    <?php echo $row['nombre_empresa']; ?>
                                                </div>
                                            </td>
                                            <td><strong> <?php echo $row['rif']; ?></strong></td>

                                            <td><?php echo $row['razon_social']; ?></td>

                                            <td><span class="tag"><?php echo $row['tipo_contribuyente']; ?></span></td>



                                            <td><span class="tag"><?php echo $row['direccion_fiscal']; ?></span></td>

                                            <td><span class="tag"><?php echo $row['telefono']; ?></span></td>

                                            <td><span class="tag"><?php echo $row['correo_electronico']; ?></span></td>

                                            <td><span class="tag"><?php echo $row['pais']; ?></span></td>

                                            <?php

                                            if ($row['estado_activo'] == 1) {

                                                echo "<td class='fw-bold'> activo </td>";
                                            } else {

                                                echo "<td> No activo </td>";
                                            }
                                            ?>


                                            <td>
                                                <button type="button" class="btn btn-warning text-light" data-bs-toggle="modal" data-bs-target="#exampleModal<?php echo $row['id_empresa'] ?>">
                                                    EDITAR
                                                </button>


                                                <div class="modal fade" id="exampleModal<?php echo $row['id_empresa'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5 fw-bold text-center" id="exampleModalLabel"> EDITAR </h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <section class="form form-register">
                                                                    <form method="POST" action="../BACKEND/editar_empresa.php">
                                                                        <div class="row">
                                                                            <div class="col align-self-center p-2">
                                                                                <input class="form-control" type="text" name="empresa" id="empresa" value="<?php echo $row['nombre_empresa'] ?>" placeholder="Ingrese su empresa" required>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="input-group">

                                                                                <input class="form-control" type="text" name="rif" id="rif" value="<?php echo $row['rif'] ?>" placeholder="Ingrese su RIF" required>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col align-self-center p-2">
                                                                                <input class="form-control" type="text" name="social" id="social" value="<?php echo $row['razon_social'] ?>" placeholder="Ingrese su razon social" required>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">

                                                                            <select class="form-select fs-6" id="contribuyente" name="contribuyente">
                                                                                <option disabled selected> Elija su tipo de contribuyente</option>
                                                                                <option value="ORDINARIO">Ordinario</option>
                                                                                <option value="ESPECIAL">Especial</option>
                                                                                <option value="FORMAL">Formal</option>
                                                                            </select>
                                                                        </div>


                                                                        <div class="row">
                                                                            <div class="col align-self-center p-2">
                                                                                <input class="form-control" type="text" name="direccion" id="direccion" value="<?php echo $row['direccion_fiscal'] ?>" placeholder="Ingrese su direccion" required>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col align-self-center p-2">
                                                                                <input class="form-control" type="text" name="telefono" id="telefono" value="<?php echo $row['telefono'] ?>" placeholder="Ingrese su telefono" required>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col align-self-center p-2">
                                                                                <input class="form-control" type="text" name="correo" id="correo" value="<?php echo $row['correo_electronico'] ?>" placeholder="Ingrese su correo" required>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col align-self-center p-2">
                                                                                <input class="form-control" type="text" name="pais" id="pais" value="<?php echo $row['pais'] ?>" placeholder="Ingrese su pais" required>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col align-self-center p-2">
                                                                                <select class="form-select fs-6" id="estado" name="estado">
                                                                                    <option value="1">Activo</option>
                                                                                    <option value="0">No activo</option>

                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" id="id" name="id" value="<?php echo $row['id_empresa'] ?>">

                                                                        <input class="btn btn-primary m-1" type="submit" value="Registrar">
                                                                    </form>

                                                                </section>

                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <a href=../BACKEND/eliminar_empresa.php?id_empresa=<?php echo $row['id_empresa'] ?>" class="btn btn-danger fs-6 text-white link-underline link-underline-opacity-0"> ELIMINAR</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>

                            <?php }
                        $conexion->close(); ?>
                            </table>
                        </div>
                </div>
            </section>
        </main>
    </div>
    <script src="../JQUERY/jquery.js"></script>
    <script src="../DATATABLE/datatables1.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabla').DataTable({
                lengthMenu: [5, 10, 25, 50, 100],
                pageLength: 25,
                language: {
                    //lengthMenu: "Mostrar MENU registros por pagina",
                    zeroRecords: "Sin resultado - disculpa",
                    //info: "Mostrando la pagina PAGE de PAGES",
                    infoEmpty: "No records available",
                    infoFiltered: "(filtrado de  MAX registros totales)",
                    search: "Buscar: ",
                    paginate: {
                        next: "Siguientes",
                        previous: "Anterior"
                    },
                }
            });
        });
    </script>

    <script>
        document.getElementById('userMenu').addEventListener('click', function(e) {
            // Previene que se cierre si haces clic dentro del menú
            e.stopPropagation();
            this.classList.toggle('active');
        });

        // Cierra el menú si haces clic fuera de él
        window.addEventListener('click', function() {
            document.getElementById('userMenu').classList.remove('active');
        });
    </script>
</body>

</html>