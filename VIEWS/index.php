<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flip Card Container</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-Yo12wWUeC8X1h7YlNv0oBv6f9/V5WtM2PAE9mRiFQwF6f3QJnI6HscfrF3YhQ7PwChDL8AJ+ScGdgLZcQuZmCw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <script src="../JAVASCRIPT/bootstrap.bundle.min.js"></script>
</head>

<body>

<div class="container ">
    <div class="flip-card w-50 m-auto" id="flipCard">
        <div class="flip-card-inner">
            <div class="flip-card-front">
                <form id="loginForm">
                    <h1 style="color: black; text-align: center;">INICIO
                    </h1>
                    <input class="m-1" type="text" placeholder="usuario" required>

                    <input class="m-1" type="password" placeholder="contraseña" required>

                    <div style="position: relative;">
                        <input type="checkbox" id="rememberPassword">
                        <label for="rememberPassword">Recordar contraseña</label>
                        <div class="password-toggle" onclick="togglePasswordVisibility()">
                            <i class="far fa-eye" id="togglePassword"></i>
                        </div>
                    </div>
                    <button class="m-2" type="submit">Iniciar sesion</button>
                </form>
                <p><a href="../VIEWS/registro.php">Registrarse</a></p>
                
            </div>
            
        </div>
    </div>
</div>

</body>
</html>