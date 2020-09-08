<?php
// Iniciar sesión
session_start();

// Comprueba si el usuario está registrado, si es que sí, lo lleva a la página de subir archivos o a la de administrativo

//Si por algún motivo quieres que tu sesión quede iniciada usa esto :D
// if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){

//     header("location: entra.php");
//     exit;
// }

// Conexión
require_once "connectionconfig.php";

// Definir variables e inicializar vacías
$email = $password = "";
$email_err = $password_err = "";

// Procesamiento de los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Comprueba si el email está vacío
    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor, introduzca su email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Comprueba si la contraseña está vacía
    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor, introduzca su contraseña.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validar credenciales
    if (empty($email_err) && empty($password_err)) {
        
        $sql = "SELECT u.idUsuario, u.email, u.pass, r.nombre 
                FROM usuario u, usuario_rol ur, rol r 
                WHERE email = ? 
                AND r.idRol = ur.idRol 
                AND u.idUsuario = ur.idUsuario";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            // Establece parámetros
            $param_email = $email;

            // Ejecuta la sentencia
            if (mysqli_stmt_execute($stmt)) {
                // Guardar resultados
                mysqli_stmt_store_result($stmt);

                // Comprueba si el usuario existe. Si existe, verifica la contraseña
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $idUsuario, $email, $user_real_pass, $rol);
                    if (mysqli_stmt_fetch($stmt)) {
                        if ($password == $user_real_pass) {
                            // Si la contraseña es correcta, inicia una nueva sesión
                            session_start();

                            // Guarda los datos en las variables de la sesión
                            $_SESSION["loggedin"] = true;
                            $_SESSION["idUsuario"] = $idUsuario;
                            $_SESSION["dni"] = $email;
                            $_SESSION["rol"] = $rol;

                            if ($rol == 'admin') {
                                header("location: /muestrainfo.php");
                            } else {

                                header("location: /subirarchivos.php");
                            }
                        } else {
                            // Mensaje de error si la contraseña no es válida
                            $password_err = "La contraseña es incorrecta.";
                        }
                    }
                } else {
                    // Mensaje de error si el usuario no existe
                    $email_err = "No se ha encontrado una cuenta con ese email.";
                }
            } else {
                echo "Algo fue mal. Vuelve a intentarlo más tarde.";
            }

            // Cierre del statement
            mysqli_stmt_close($stmt);
        }
    }

    // Cierre de la conexión
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
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
                <a class="nav-item nav-link" href="http://www.manifesto.epizy.com/">Home</a>
                <a class="nav-item nav-link" href="ManualdeUsuario.html">Manual de usuario</a>
                <a class="nav-item nav-link" href="ManualTecnico.html">Manual técnico</a>
                <a class="nav-item nav-link" href="Documentacion.html">Documentación</a>
            </div>
        </div>
    </nav>

    <div class="jumbotron jumbotron-fluid text-center">
        <h1>Manifesto</h1>
        <p>Tu gestor de información favorito</p>
    </div>

    <div class="myloginform text-center shadow-lg">
        <h2 class="mb-3">Iniciar sesión</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>" placeholder="Email">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <input type="password" name="password" class="form-control" placeholder="Contraseña">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary pl-5 pr-5" value="Acceder">
            </div>
            <p>¿No tiene cuenta? <a href="/registro.php">Regístrese aquí</a>.</p>
        </form>
        <p class="mt-5 mb-3 text-muted">© 2020-2021</p>
    </div>

</body>

</html>