<?php include_once('../BACKEND/conecxion_bd.php'); ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Empleados | Contable EA</title>
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
                <a href="../VIEWS/inicio.php"><i class="fas fa-home""></i>Inicio</a>
                <a href="../VIEWS/empresas_clientes.php"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="../VIEWS/libro_facturas.php"><i class="fas fa-file-invoice"></i>Libro de Facturas</a>
                <a href="../VIEWS/asientos_diario.php"><i class="fas fa-book"></i> Asientos Diario</a>
                <a href="../VIEWS/libro_mayor.php"><i class="fas fa-chart-line"></i> Libro Mayor</a>
                <a href="../VIEWS/balance_comprobacion.php"><i class="fas fa-balance-scale"></i> Balance</a>
                <a href="../VIEWS/estado_resultados.php"><i class="fas fa-file-invoice-dollar"></i> Estado de Resultados</a>
                <a href="../VIEWS/empleados.php" class="active"><i class="fas fa-users"></i> Empleados</a>
                <a href="../VIEWS/catalogo_cuenta.php"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="../VIEWS/auditoria.php"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>

        <main class="viewport">


            <?php include_once('header.php') ?>


            <section class="content">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; padding: 20px;">
                        <input type="text" placeholder="Buscar por Cédula o Nombre..." style="padding: 10px; border-radius: 5px; border: 1px solid #ddd; width: 300px;">

                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <i class="fas fa-user-plus"></i> REGISTRAR EMPLEADO
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
                                            <form method="POST" action="../BACKEND/conexion_empleados.php">
                                                <div class="row">
                                                    <div class="col align-self-center p-2">
                                                        <input class="form-control" type="text" name="cedula" id="cedula" placeholder="Ingrese su Cedula" required>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col align-self-center p-2">
                                                        <input class="form-control" type="text" name="nombre" id="nombre" placeholder="Ingrese su Nombre completo" required>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col align-self-center p-2">
                                                        <input class="form-control" type="text" name="telefono" id="telefono" placeholder="Ingrese su telefono" required>
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



                    <?php $sql = "SELECT * FROM empleados";
                    $result = $conexion->query($sql);
                    if ($result->num_rows > 0) {

                    ?>

                        <div class="table-wrapper">
                            <table id='tabla' class="contable-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cédula</th>
                                        <th>Nombre Completo</th>
                                        <th>Número de Telefono</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody class='table-group-divider'>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td>#<?php echo htmlspecialchars($row['id_empleado']); ?></td>

                                            <!--
                                            <td><i class="fas fa-building" style="color: #94a3b8;"></i>

                                                </*?php

                                                $sql = "SELECT * FROM empresas_clientes WHERE id_empresa = '" . $row['id_empresa'] . "'";
                                                $result2 = $conexion->query($sql);
                                                $row2 = mysqli_fetch_assoc($result2);

                                                echo $row2['nombre_empresa']; */?>


                                            </td>-->

                                            <td><strong><?php echo htmlspecialchars($row['cedula']); ?></strong></td>

                                            <td><?php echo htmlspecialchars($row['nombre_completo']); ?></td>

                                            <td><?php echo htmlspecialchars($row['telefono']); ?></td>

                                            <td>

                                                <button type="button" class="btn btn-warning fs-6" data-bs-toggle="modal" data-bs-target="#exampleModal<?php echo $row['id_empleado'] ?>">
                                                    </i> EDITAR EMPLEADO
                                                </button>


                                                <div class="modal fade" id="exampleModal<?php echo $row['id_empleado'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5 fw-bold text-center" id="exampleModalLabel">EDITAR</h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <section class="form form-register">
                                                                    <form method="POST" action="../BACKEND/editar_empleado.php">
                                                                        <div class="row">
                                                                            <div class="col align-self-center p-2">
                                                                                <input class="form-control" type="text" name="cedula" id="cedula" placeholder="Ingrese su Cedula" value="<?php echo $row['cedula'] ?>" required>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col align-self-center p-2">
                                                                                <input class="form-control" type="text" name="nombre" id="nombre" placeholder="Ingrese su Nombre completo" value="<?php echo $row['nombre_completo'] ?>" required>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col align-self-center p-2">
                                                                                <input class="form-control" type="text" name="telefono" id="telefono" placeholder="Ingrese su telefono" value="<?php echo $row['telefono'] ?>" required>
                                                                            </div>
                                                                        </div>

                                                                        <input type="hidden" id="id" name="id" value="<?php echo $row['id_empleado'] ?>">
                                                                        <input class="btn btn-primary m-1" type="submit" value="Editar">
                                                                    </form>

                                                                </section>

                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <a href=../BACKEND/eliminar_empleado.php?id_empleado=<?php echo $row['id_empleado'] ?>" class="btn btn-danger fs-6 text-white link-underline link-underline-opacity-0"> ELIMINAR</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            <?php } ?>

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