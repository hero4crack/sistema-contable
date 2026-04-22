<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Entidades | Contable EA</title>
    <link rel="stylesheet" href="../CSS/style_inicio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <aside class="main-sidebar">
            <div class="brand"><i class="fas fa-calculator"></i> CONTABLE EA</div>
            <nav class="menu">
                <a href="../VIEWS/inicio.php"><i class="fas fa-home"></i> Inicio</a>
                <a href="../VIEWS/empresas_clientes.php" class="active"><i class="fas fa-city"></i> Empresas Clientes</a>
                <a href="#"><i class="fas fa-file-invoice"></i> Libro de Facturas</a>
                <a href="#"><i class="fas fa-book"></i> Asientos Diario</a>
                <a href="../VIEWS/empleados.php"><i class="fas fa-users"></i> Empleados</a>
                <a href="#"><i class="fas fa-list-ol"></i> Catálogo Cuentas</a>
                <a href="#"><i class="fas fa-shield-alt"></i> Auditoría</a>
            </nav>
        </aside>

        <main class="viewport">
            <header class="navbar">
                <div class="nav-title">Inicio</div>
                
                <div class="user-menu-container" id="userMenu">
                    <div class="user-info">
                        <span class="user-name">Administrador</span> <div class="user-avatar">EA</div>
                        <i class="fas fa-chevron-down caret"></i>
                    </div>
                    
                    <div class="dropdown-menu">
                        <a href="#"><i class="fas fa-user-cog"></i> Mi Perfil</a>
                        <a href="#"><i class="fas fa-key"></i> Cambiar Contraseña</a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="logout-link"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                    </div>
                </div>
            </header>
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