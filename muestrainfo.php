<?php
    require_once("connectionconfig.php");
    session_start();
    if ($_SESSION["rol"] == 'admin') {
    } else{
        header("location: /index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Usuarios</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="css/estilos.css">
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
</head>

<body>

    <nav class="navbar navbar-expand-md bg-dark navbar-dark fixed-top">
        <!-- MARCA -->
        <a class="navbar-brand ml-4" href="http://www.manifesto.epizy.com/">Manifesto</a>
        <!-- COLAPSA BARRA -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
          <span class="navbar-toggler-icon"></span>
        </button>
        <!-- BARRA NAV -->
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <div class="navbar-nav ml-auto mr-4">
                <a class="nav-item nav-link" href="http://www.manifesto.epizy.com/">Cerrar sesión</a>
                <a class="nav-item nav-link" href="#">Sobre nosotros</a>
            </div>
        </div>
    </nav>


  <div class="jumbotron jumbotron-fluid text-center">
    <h1>Manifesto</h1>
    <p>Tu gestor de información favorito</p>
  </div>


  <div class="text-center">
    <h2 class="pb-4">Usuarios</h2>
    <div class="table-responsive">
      <?php
      //Conexión con la base de datos
      //require_once("connectionconfig.php");
      $sql = "SELECT idUsuario, nombre, primerApellido, segundoApellido, dni, telefono, email FROM usuario";
      if ($result = mysqli_query($conn, $sql)) {
        if (mysqli_num_rows($result) > 0) {
          echo "
              <table class='table table-striped mytable'>
              <thead class='thead-dark'>
                <tr>
                  <th>Nombre</th>
                  <th>Primer apellido</th>
                  <th>Segundo apellido</th>
                  <th>DNI</th>
                  <th>Teléfono</th>
                  <th>Email</th>
                  <th>Documentación</th>
                </tr>
              </thead>
              ";
          while ($row = mysqli_fetch_array($result)) {
            echo "<tbody>";
            echo "<tr>";
            echo "<td>" . $row['nombre'] . "</td>";
            echo "<td>" . $row['primerApellido'] . "</td>";
            echo "<td>" . $row['segundoApellido'] . "</td>";
            echo "<td>" . $row['dni'] . "</td>";
            echo "<td>" . $row['telefono'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            // TODO link a archivos del usuario
            $idU = $row['idUsuario'];
            echo "<td> <a href='/validardocumento.php?idUsuario=$idU'>Ver archivos</a> </td>";
            echo "</tr>";
          }
          echo "</tbody>";
          echo "</table>";
          mysqli_free_result($result);
        } else {
          echo "No hay datos en la tabla.";
        }
      } else {
        echo "ERROR: No se pudo ejecutar $sql." . mysqli_error($conn);
      }
      mysqli_close($conn);
      ?>
    </div>
  </div>
</body>
</html>