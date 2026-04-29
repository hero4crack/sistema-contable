<?php include('../BACKEND/conexion_login.php'); ?>


<header class="navbar">
                <!--<div class="nav-title">Inicio</div>-->
                
                <div class="user-menu-container" id="userMenu">
                    <div class="user-info">
                        <span class="user-name"><?php echo $_SESSION['usuario'] ?></span> <div class="user-avatar">EA</div>
                        <i class="fas fa-chevron-down caret"></i>
                    </div>
                    
                    <div class="menu-plegable">
                        <a href="#"><i class="fas fa-user-cog"></i> Mi Perfil</a>
                        <a href="#"><i class="fas fa-key"></i> Cambiar Contraseña</a>
                        <div class="dropdown-divider"></div>
                        <a href="../BACKEND/logout.php" class="logout-link"  ><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                    </div>
                </div>
            </header>