<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <link rel="stylesheet" href="../CSS/bootstrap.min.css">
  <link rel="stylesheet" href="../CSS/Rstyle.css">
  <script src="../JAVASCRIPT/bootstrap.bundle.min.js"></script>
  <title>Formulario Registro</title>
</head>

<body>
  <div class="container text-center">
    <section class="form form-register">
      <form method="POST" action="../BACKEND/conexion_reg.php">
        <h4>REGISTRO</h4>

        <div class="row">
          <div class="col align-self-center p-2">
            <input class="form-control" type="text" name="username" id="username" placeholder="Ingrese su Nombre" required>
          </div>
        </div>

        <div class="row">
          <div class="col align-self-center p-2">
            <input class="form-control" type="email" name="password_hash" id="password_hash" placeholder="Ingrese su Correo" required>
          </div>
        </div>

         <div class="row">
          <div class="col align-self-center p-2">
        <input class="form-control" type="password" name="rol" id="rol" placeholder="Ingrese el Rol" required>
        
       </div>
        </div>
        <input class="btn btn-primary m-1" type="submit" value="Registrar">
      </form>
      <p><a href="../VIEWS/index.php">¿Ya tengo Cuenta?</a></p>
    </section>
  </div>

</body>

</html>