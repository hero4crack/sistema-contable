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
                <a href="../VIEWS/empleados.php"><i class="fas fa-users"></i> Empleados</a>
                <a href="../VIEWS/catalogo_cuenta.php" class="active"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="#"><i class="fas fa-shield-alt"></i> Auditoría</a>
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
                while($cuenta = $datos_catalogo->fetch_assoc()): 
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
                        <button class="config-btn"><i class="fas fa-edit"></i></button>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
                    </section>
        </main>
    </div>
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