<?php
// Configuración de la conexión con MySQL
require_once("connectionconfig.php");

// Define las variables y las inicializa vacías
$nombre = $primerapellido = $segundoapellido = $dni = $telefono = $email = $password = $confirm_password = "";
$nombre_err = $primerapellido_err = $segundoapellido_err = $dni_err = $telefono_err = $email_err = $password_err = $confirm_password_err = "";

// Procesamiento de los datos del formulario cuando se registra el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar nombre
    if (empty(trim($_POST["nombre"]))) {
        $nombre_err = "Por favor, introduce un nombre.";
    } else {
        // Preparar declaración
        $sql = "SELECT idUsuario FROM usuario WHERE nombre = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Vincula las variables a la declaración como parámetros
            mysqli_stmt_bind_param($stmt, "s", $param_nombre);

            // Establece parámetros
            $param_nombre = trim($_POST["nombre"]);

            // Intentar ejecutar la declaración
            if (mysqli_stmt_execute($stmt)) {
                //Guardar resultado
                mysqli_stmt_store_result($stmt);

                $nombre = trim($_POST["nombre"]);
            } else {
                echo "Algo fue mal. Vuelva a intentarlo más tarde.";
            }

            // Fin de la declaración
            mysqli_stmt_close($stmt);
        }
    }

    // TODO validaciones
    // Validar primer apellido
    if (empty(trim($_POST["primerapellido"]))) {
        $primerapellido_err = "Por favor, introduce un apellido.";
    } else {
        // Preparar declaración
        $sql = "SELECT idUsuario FROM usuario WHERE primerapellido = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Vincula las variables a la declaración como parámetros
            mysqli_stmt_bind_param($stmt, "s", $param_primerapellido);

            // Establece parámetros
            $param_primerapellido = trim($_POST["primerapellido"]);

            // Intentar ejecutar la declaración
            if (mysqli_stmt_execute($stmt)) {
                //Guardar resultado
                mysqli_stmt_store_result($stmt);

                $primerapellido = trim($_POST["primerapellido"]);
            } else {
                echo "Algo fue mal. Vuelva a intentarlo más tarde.";
            }

            // Fin de la declaración
            mysqli_stmt_close($stmt);
        }
    }

    // Validar segundo apellido
    if (empty(trim($_POST["segundoapellido"]))) {
        $segundoapellido_err = "Por favor, introduce un apellido.";
    } else {
        // Preparar declaración
        $sql = "SELECT idUsuario FROM usuario WHERE segundoapellido = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Vincula las variables a la declaración como parámetros
            mysqli_stmt_bind_param($stmt, "s", $param_segundoapellido);

            // Establece parámetros
            $param_segundoapellido = trim($_POST["segundoapellido"]);

            // Intentar ejecutar la declaración
            if (mysqli_stmt_execute($stmt)) {
                //Guardar resultado
                mysqli_stmt_store_result($stmt);

                $segundoapellido = trim($_POST["segundoapellido"]);
            } else {
                echo "Algo fue mal. Vuelva a intentarlo más tarde.";
            }

            // Fin de la declaración
            mysqli_stmt_close($stmt);
        }
    }

    // Validar dni
    if (empty(trim($_POST["dni"]))) {
        $dni_err = "Por favor, introduce un DNI.";
    } else {
        // Preparar declaración
        $sql = "SELECT idUsuario FROM usuario WHERE dni = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Vincula las variables a la declaración como parámetros
            mysqli_stmt_bind_param($stmt, "s", $param_dni);

            // Establece parámetros
            $param_dni = trim($_POST["dni"]);

            // Intentar ejecutar la declaración
            if (mysqli_stmt_execute($stmt)) {
                //Guardar resultado
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $dni_err = "Este DNI ya está en el sistema.";
                } else {
                    $dni = trim($_POST["dni"]);
                }
            } else {
                echo "Algo fue mal. Vuelva a intentarlo más tarde.";
            }

            // Fin de la declaración
            mysqli_stmt_close($stmt);
        }
    }

    // Validar teléfono
    if (empty(trim($_POST["telefono"]))) {
        $telefono_err = "Por favor, introduce un teléfono.";
    } else {
        // Preparar declaración
        $sql = "SELECT idUsuario FROM usuario WHERE telefono = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Vincula las variables a la declaración como parámetros
            mysqli_stmt_bind_param($stmt, "s", $param_telefono);

            // Establece parámetros
            $param_telefono = trim($_POST["telefono"]);

            // Intentar ejecutar la declaración
            if (mysqli_stmt_execute($stmt)) {
                //Guardar resultado
                mysqli_stmt_store_result($stmt);

                $telefono = trim($_POST["telefono"]);
            } else {
                echo "Algo fue mal. Vuelva a intentarlo más tarde.";
            }

            // Fin de la declaración
            mysqli_stmt_close($stmt);
        }
    }

    // Validar email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor, introduce un email.";
    } else {
        // Preparar declaración
        $sql = "SELECT idUsuario FROM usuario WHERE email = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Vincula las variables a la declaración como parámetros
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            // Establece parámetros
            $param_email = trim($_POST["email"]);

            // Intentar ejecutar la declaración
            if (mysqli_stmt_execute($stmt)) {
                //Guardar resultado
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "Este email ya está en el sistema.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Algo fue mal. Vuelva a intentarlo más tarde.";
            }

            // Fin de la declaración
            mysqli_stmt_close($stmt);
        }
    }


    // hasta aquí
    // Validar contraseña
    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor, introduce la contraseña.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "La contraseña tiene que tener al menos 6 caracteres.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validar confirmación de contraseña
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Por favor, confirma la contraseña.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "La contraseña no coincide.";
        }
    }

    // Comprobar los errores antes de añadir los datos a la BBDD
    if (empty($nombre_err) && empty($primerapellido_err) && empty($segundoapellido_err) && empty($dni_err) && empty($telefono_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {

        // Declaración
        $sql = "INSERT INTO usuario (nombre, primerapellido, segundoapellido, dni, telefono, email, pass) VALUES (?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Vincula las variables a la declaración como parámetros
            mysqli_stmt_bind_param($stmt, "sssssss", $param_nombre, $param_primerapellido, $param_segundoapellido, $param_dni, $param_telefono, $param_email, $param_password);
            // s significa string, hay tantas s como string hay en los parámetros

            // Establece parámetros
            $param_nombre = $nombre;
            $param_primerapellido = $primerapellido;
            $param_segundoapellido = $segundoapellido;
            $param_dni = $dni;
            $param_telefono = $telefono;
            $param_email = $email;
            $param_password = $password;

            // Intentar ejecutar la declaración
            if (mysqli_stmt_execute($stmt)) {
                
            } else {
                echo "Algo fue mal. Vuelva a intentarlo más tarde.";
            }

            // Fin de la declaración
            mysqli_stmt_close($stmt);
        }

        $sql2 = "INSERT INTO usuario_rol (idUsuario, idRol) VALUES (LAST_INSERT_ID(), 2)";

        if ($stmt = mysqli_prepare($conn, $sql2)) {

            // Intentar ejecutar la declaración
            if (mysqli_stmt_execute($stmt)) {
                
                // Redirige a login 
                header("location: /index.php");
                
            } else {
                echo "Algo fue mal. Vuelva a intentarlo más tarde.";
            }

            // Fin de la declaración
            mysqli_stmt_close($stmt);
        }
        
    }

    // Fin de la conexión
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro de usuarios</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/estilos.css">
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
                <a class="nav-item nav-link" href="http://www.manifesto.epizy.com/">Iniciar sesión</a>
                <a class="nav-item nav-link" href="#">Sobre nosotros</a>
            </div>
        </div>
    </nav>

    <div class="jumbotron jumbotron-fluid text-center mh-50">
        <h1>Manifesto</h1>
        <p>Tu gestor de información favorito</p>
    </div>

    <div class="wrapper myregform text-center shadow-lg">
        <h2>Regístrate</h2>
        <p>Por favor, rellena este formulario para crear una cuenta.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($nombre_err)) ? 'has-error' : ''; ?>">
                <input type="text" name="nombre" class="form-control" value="<?php echo $nombre; ?>" placeholder="Nombre">
                <span class="help-block"><?php echo $nombre_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($primerapellido_err)) ? 'has-error' : ''; ?>">
                <input type="text" name="primerapellido" class="form-control" value="<?php echo $primerapellido; ?>" placeholder="Primer Apellido">
                <span class="help-block"><?php echo $primerapellido_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($segundoapellido_err)) ? 'has-error' : ''; ?>">
                <input type="text" name="segundoapellido" class="form-control" value="<?php echo $segundoapellido; ?>" placeholder="Segundo Apellido">
                <span class="help-block"><?php echo $segundoapellido_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($dni_err)) ? 'has-error' : ''; ?>">
                <input type="text" name="dni" class="form-control" value="<?php echo $dni; ?>" placeholder="DNI">
                <span class="help-block"><?php echo $dni_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($telefono_err)) ? 'has-error' : ''; ?>">
                <input type="tel" name="telefono" class="form-control" value="<?php echo $telefono; ?>" placeholder="Teléfono">
                <span class="help-block"><?php echo $telefono_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" placeholder="Email">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <!--  -->
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>" placeholder="Contraseña"> 
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>" placeholder="Repita su contraseña">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary pl-5 pr-5" value="Registrarse">
                <input type="reset" class="btn btn-default" value="Borrar">
            </div>
            <p>¿Ya estás registrado? <a href="/index.php">Iniciar sesión</a>.</p>
        </form>
    </div>
    
</body>

</html>