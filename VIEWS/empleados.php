<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Empleados | Contable EA</title>
    <link rel="stylesheet" href="../CSS/style_cliente.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <aside class="main-sidebar">
            <div class="brand"><i class="fas fa-calculator"></i> CONTABLE EA</div>
            <nav class="menu">
                <a href="#"><i class="fas fa-chart-line"></i> Dashboard</a>
                <a href="empresas_clientes.php"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="#" class="active"><i class="fas fa-users"></i> Empleados</a>
                <a href="#"><i class="fas fa-book"></i> Asientos Diario</a>
                <a href="#"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>

        <main class="viewport">
            <header class="navbar">
                <div class="nav-title">GESTIÓN DE NÓMINA / EMPLEADOS</div>
                <div class="user-avatar">EA</div>
            </header>

            <section class="content">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; padding: 20px;">
                        <input type="text" placeholder="Buscar por Cédula o Nombre..." style="padding: 10px; border-radius: 5px; border: 1px solid #ddd; width: 300px;">
                        <button class="primary-btn" style="background: #1e293b; color: white; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                           <i class="fas fa-user-plus"></i> REGISTRAR EMPLEADO
                        </button>
                    </div>

                    <div class="table-wrapper">
                        <table class="contable-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Empresa (ID)</th>
                                    <th>Cédula</th>
                                    <th>Nombre Completo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($empleados as $empleado): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($empleado['id_empleado']); ?></td>
                                    <td><i class="fas fa-building" style="color: #94a3b8;"></i> <?php echo htmlspecialchars($empleado['id_empresa']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($empleado['cedula']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($empleado['nombre_completo']); ?></td>
                                    <td>
                                        <button class="config-btn" title="Editar"><i class="fas fa-edit"></i></button>
                                        <button class="config-btn" title="Eliminar" style="color: #ef4444;"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>