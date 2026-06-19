<?php
// ============================================
// 1. PRIMERO: Incluir sesión y conexión
// ============================================
include_once '../BACKEND/conexion_login.php';

// 2. Verificar autenticación
if (!isset($_SESSION['usuario'])) {
    header("Location: ../VIEWS/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Entidades | Contable EA</title>
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/style_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../JAVASCRIPT/bootstrap.bundle.min.js"></script>
    <style>
        /* Estilos solo para el contenido */
        .welcome-section {
            padding: 30px 20px;
        }

        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 40px;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .welcome-card::after {
            content: '';
            position: absolute;
            bottom: -40%;
            left: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .welcome-card h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .welcome-card p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }

        .welcome-card .icon-decoration {
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 5rem;
            opacity: 0.15;
            z-index: 0;
        }

        .quick-actions {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .quick-actions h4 {
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .quick-actions h4 i {
            color: #667eea;
            margin-right: 10px;
        }

        .btn-quick {
            padding: 15px 25px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid #e2e8f0;
            background: white;
            color: #2d3748;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            min-width: 180px;
            justify-content: center;
        }

        .btn-quick:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
            color: #667eea;
            background: #f7fafc;
            text-decoration: none;
        }

        .btn-quick i {
            font-size: 1.2rem;
            color: #667eea;
        }

        .btn-quick:hover i {
            color: #667eea;
        }

        .btn-quick.primary {
            background: #667eea;
            border-color: #667eea;
            color: white;
        }

        .btn-quick.primary i {
            color: white;
        }

        .btn-quick.primary:hover {
            background: #5a6fd6;
            border-color: #5a6fd6;
            color: white;
        }

        .grid-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .main-content {
            padding: 20px 25px;
        }

        .date-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 18px;
            border-radius: 30px;
            font-size: 0.85rem;
            display: inline-block;
            margin-top: 10px;
            position: relative;
            z-index: 1;
        }

        .date-badge i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="app-container">
        
        <!-- Sidebar -->
        <aside class="main-sidebar">
            <div class="brand"><img src="../IMG/logo_empresa-sinfondo.png" alt="#" class="mi-imagen"></div>
            <nav class="menu">
                <a href="../VIEWS/inicio.php" class="active"><i class="fas fa-home"></i> Inicio</a>
                <a href="../VIEWS/empresas_clientes.php"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="../VIEWS/registro_proveedor.php"><i class="fas fa-truck"></i> Proveedores</a>
                <a href="../VIEWS/libro_facturas.php"><i class="fas fa-file-invoice"></i> Libro de Facturas</a>
                <a href="../VIEWS/asientos_diario.php"><i class="fas fa-book"></i> Asientos Diario</a>
                <a href="../VIEWS/libro_mayor.php"><i class="fas fa-chart-line"></i> Libro Mayor</a>
                <a href="../VIEWS/balance_comprobacion.php"><i class="fas fa-balance-scale"></i> Balance</a>
                <a href="../VIEWS/estado_resultados.php"><i class="fas fa-file-invoice-dollar"></i> Estado de Resultados</a>
                <a href="../VIEWS/empleados.php"><i class="fas fa-users"></i> Empleados</a>
                <a href="../VIEWS/catalogo_cuenta.php"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="../VIEWS/auditoria.php"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="viewport">
            <?php include('header.php'); ?>

            <div class="main-content">
                
                <!-- Mensaje de Bienvenida -->
                <div class="welcome-card">
                    <i class="fas fa-chart-line icon-decoration"></i>
                    <h1>¡Bienvenido al Sistema Contable!</h1>
                    <p>Gestiona tus entidades, facturas y registros contables de manera eficiente.</p>
                    <span class="date-badge">
                        <i class="fas fa-calendar-alt"></i> <?= date('d/m/Y') ?>
                    </span>
                </div>

                <!-- Accesos Rápidos -->
                <div class="quick-actions">
                    <h4><i class="fas fa-bolt"></i> Accesos Rápidos</h4>
                    <div class="grid-actions">
                        <a href="../VIEWS/empresas_clientes.php" class="btn-quick">
                            <i class="fas fa-building"></i> Empresas
                        </a>
                        <a href="../VIEWS/registro_proveedor.php" class="btn-quick">
                            <i class="fas fa-truck"></i> Proveedores
                        </a>
                        <a href="../VIEWS/libro_facturas.php" class="btn-quick">
                            <i class="fas fa-file-invoice"></i> Facturas
                        </a>
                        <a href="../VIEWS/asientos_diario.php" class="btn-quick">
                            <i class="fas fa-book"></i> Asientos
                        </a>
                        <a href="../VIEWS/empleados.php" class="btn-quick">
                            <i class="fas fa-users"></i> Empleados
                        </a>
                        <a href="../VIEWS/catalogo_cuenta.php" class="btn-quick primary">
                            <i class="fas fa-list-ol"></i> Catálogo Cuentas
                        </a>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <?php include('script.php'); ?>
</body>
</html>