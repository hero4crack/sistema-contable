<?php include_once('../BACKEND/conecxion_login.php'); ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Entidades | Contable EA</title>
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/style_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<body>
    <div class="app-container">
        <aside class="main-sidebar">
            <div class="brand"><i class="fas fa-calculator"></i> CONTABLE EA</div>
            <nav class="menu">
                <a href="#"><i class="fas fa-chart-line"></i> Dashboard</a>
                <a href="#" class="active"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="#"><i class="fas fa-file-invoice"></i> Libro de Facturas</a>
                <a href="#"><i class="fas fa-book"></i> Asientos Diario</a>
                <a href="#"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="#"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>

        <main class="viewport">
            <header class="navbar">
                <div class="nav-title">GESTIÓN DE ENTIDADES</div>
                <div class="user-info">
                    <div class="user-avatar">EA</div>
                </div>
            </header>

            <section class="content">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; padding: 20px; border-bottom: 1px solid #e2e8f0;">
                        <input type="text" placeholder="Buscar empresa..." style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; width: 300px;">
                        <button class="bg-primary primary-btn fw-normal p-2 h-25" >
                            <i class="fas fa-plus"></i> AGREGAR CLIENTE
                        </button>
                    </div>

                    <?php $sql = "SELECT * FROM empresas_clientes";
                    $result = $conexion->query($sql);
                    if ($result->num_rows > 0) {

                    ?>

                        <div class="table-wrapper">
                            <table class="contable-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Empresa</th>
                                        <th>RIF</th>
                                        <th>Razón Social</th>
                                        <th>Contribuyente</th>
                                        <th>Estatus</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                                            <td><strong><?php echo $row['rif']; ?></strong></td>

                                            <td><?php echo $row['razon_social']; ?></td>

                                            <td><span class="tag"><?php echo $row['tipo_contribuyente']; ?></span></td>

                                            <?php

                                            if ($row['estado_activo'] == 1) {

                                                echo "<td class='fw-bold'> activo </td>";
                                            } else{

                                                echo "<td> En proceso </td>";
                                            } 
                                            ?>
                                            <td>
                                                <button class="config-btn"><i class="fas fa-sliders-h"></i></button>
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
</body>

</html>