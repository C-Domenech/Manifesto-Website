<?php
//Conexión con la base de datos
require_once("connectionconfig.php");
session_start();

if ($_SESSION["rol"] == 'admin') {
} else{
    header("location: /index.php");
}

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

$idUsuario = $_GET['idUsuario'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // VALIDAR
    $idDocumentosvalidados = $_POST['validar'];
    if (empty($idDocumentosvalidados)) {
        echo "No ha seleccionado ningún documento.";
    } else {
        $n = count($idDocumentosvalidados);
        for ($i = 0; $i < $n; $i++) {
            $thisid = $idDocumentosvalidados[$i];

            $sql = "UPDATE documento SET validado = ? WHERE idDocumento = '$thisid'";

            if ($stmt = mysqli_prepare($conn, $sql)) {
                $validado = 1;
                mysqli_stmt_bind_param($stmt, 'i', $validado);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    echo "$thisid ha sido validado.";
                }
            }
        }
    }

    // ANULAR VALIDACIÓN
    $idDocumentosrechazados = $_POST['rechazar'];
    if (empty($idDocumentosrechazados)) {
        echo "No ha seleccionado ningún documento.";
    } else {
        $n = count($idDocumentosrechazados);
        for ($i = 0; $i < $n; $i++) {
            $thisid = $idDocumentosrechazados[$i];

            $sql = "UPDATE documento SET validado = ? WHERE idDocumento = '$thisid'";

            if ($stmt = mysqli_prepare($conn, $sql)) {
                $validado = 0;
                mysqli_stmt_bind_param($stmt, 'i', $validado);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    echo "$thisid ha sido rechazado.";
                }
            }
        }
    }
// EMAIL
    $sql2 = "SELECT nombre, primerApellido, segundoApellido, email FROM usuario WHERE idUsuario = '$idUsuario'";
    if ($result = mysqli_query($conn, $sql2)) {
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $nombre = $row['nombre'];
                $apellido1 = $row['primerApellido'];
                $apellido2 = $row['segundoApellido'];
                $email = $row['email'];

                $sqlvalidado = "SELECT nombreDocumento FROM documento WHERE idUsuario = '$idUsuario' AND validado = 1";

                if ($resultvalidado = mysqli_query($conn, $sqlvalidado)) {

                    if (mysqli_num_rows($resultvalidado) > 0) {

                        $documentosvalidados = '<tr><br><td><b>Documentos validados: </b></td></tr>';

                        while ($row2 = mysqli_fetch_array($resultvalidado)) {

                            $documentosvalidados .= '<tr><td>' . $row2['nombreDocumento'] . '</td></tr>';
                        }
                    }
                }

                $sqlnovalidado = "SELECT nombreDocumento FROM documento WHERE idUsuario = '$idUsuario' AND validado = 0";

                if ($resultnovalidado = mysqli_query($conn, $sqlnovalidado)) {

                    if (mysqli_num_rows($resultnovalidado) > 0) {

                        $documentosnovalidados = '<tr><br><td><b>Revise los siguientes documentos: </b></td></tr>';

                        while ($row3 = mysqli_fetch_array($resultnovalidado)) {

                            $documentosnovalidados .= '<tr><td>' . $row3['nombreDocumento'] . '</td></tr>';
                        }
                    }
                }

                try {
                    //Server settings
                    $mail->SMTPDebug = 0;                                 // Enable verbose debug output
                    $mail->isSMTP();                                      // Set mailer to use SMTP
                    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
                    $mail->SMTPAuth = true;                               // Enable SMTP authentication
                    $mail->Username = 'landingpageproyecto@gmail.com';                 // SMTP username
                    $mail->Password = 'tciirutjlukrgxgx';                           // SMTP password
                    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, ssl also accepted
                    $mail->Port = 587;                                    // TCP port to connect to

                    //Recipients
                    $mail->setFrom('landingpageproyecto@gmail.com', 'Manifesto');
                    $mail->addAddress($email, $nombre);     // Add a recipient
                    //$mail->addAddress('ellen@example.com');               // Name is optional
                    //$mail->addReplyTo('info@example.com', 'Information');
                    //$mail->addCC('cc@example.com');
                    //$mail->addBCC('bcc@example.com');

                    //Attachments
                    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

                    //Content
                    $mail->CharSet  = 'UTF-8';
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = 'Bienvenido a Manifesto';
                    $mail->Body    = "                    
                        <html>
                        <head>
                        <title>Bienvenido a Manifesto</title>
                        </head>
                        <body>
                        <p>$nombre, te damos la bienvenida a tu área privada de Manifesto.</p>
                        <p>Tu cuenta se ha creado correctamente con el siguiente email: $email</p>
                        <p>Estos son los datos de su cuenta:</p>
                        <table>
                        <tr>
                        <td><b>Nombre y apellidos: </b></td>
                        </tr>
                        <tr>
                        <td>$nombre $apellido1 $apellido2</td>
                        </tr>

                        $documentosvalidados
                        
                        $documentosnovalidados

                        </table>
                        <p>Un saludo y hasta pronto.</p>
                        </body>
                        </html>";
                    $mail->AltBody = "$nombre, te damos la bienvenida a tu área privada de Manifesto. 
                        Tu cuenta se ha creado correctamente con el siguiente email: $email. 
                        Estos son los datos de su cuenta: 
                        Nombre: $nombre 
                        Apellidos: $apellido1 $apellido2
                        Empieza a disfrutar de todas tus ventajas. 
                        Un saludo y hasta pronto";

                    if ($mail->send()) {
                        echo 'El usuario ha recibido un email de confirmación.';
                        header("Location: /validardocumento.php?idUsuario=$idUsuario");
                        exit;
                    } else {
                        echo "ERROR: " . $mail->ErrorInfo;
                    }
                } catch (Exception $e) {
                    echo 'El usuario no recibió un email de confirmación.';
                    echo 'Error: ' . $mail->ErrorInfo;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Documentación</title>
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
                <a class="nav-item nav-link" href="http://www.manifesto.epizy.com/">Cerrar sesión</a>
                <a class="nav-item nav-link" href="muestrainfo.php">Volver a usuarios</a>
                <a class="nav-item nav-link" href="#">Sobre nosotros</a>
            </div>
        </div>
    </nav>


    <div class="jumbotron jumbotron-fluid text-center">
        <h1>Manifesto</h1>
        <p>Tu gestor de información favorito</p>
    </div>

    <!-- VALIDAR DOCUMENTOS -->
    <?php
    $sql3 = "SELECT idDocumento, nombreDocumento, fechaDocumento FROM documento WHERE idUsuario = '$idUsuario' AND validado = 0";
    if ($result = mysqli_query($conn, $sql3)) {
        if (mysqli_num_rows($result) > 0) {
    ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover mytable">
                    <tr>
                        <th style="width: 30%;">Documentos no validados</th>
                        <th style="width: 30%;">Descarga</th>
                        <th style="width: 30%;"><input type="checkbox" onclick="toggle(this)">&nbsp; Seleccionar</th>
                    </tr>
                    <?php
                    while ($row = mysqli_fetch_array($result)) {
                    ?>
                        <tr>
                            <td><?php echo $row['nombreDocumento'] ?></td>
                            <td><a href="/documentos/<?php echo $idUsuario . '/' . $row['nombreDocumento'] ?>">Descargar</a></td>
                            <td>
                                <form action="<?php echo $_SERVER["PHP_SELF"] . "/?idUsuario=$idUsuario"; ?>" method="POST">
                                    <input type="checkbox" name="validar[]" value="<?php echo $row['idDocumento'] ?>">
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>
                            <div class="row">
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary mt-3" value="Validar documento">
                                </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
    <?php
        }
    }
    ?>
    <script>
        function toggle(source) {
            checkboxes = document.getElementsByName('validar[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>

    <!-- ANULAR VALIDACIÓN DE DOCUMENTOS -->
    <?php
    $sql4 = "SELECT idDocumento, nombreDocumento, fechaDocumento FROM documento WHERE idUsuario = '$idUsuario' AND validado = 1";
    if ($result = mysqli_query($conn, $sql4)) {
        if (mysqli_num_rows($result) > 0) {
    ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover mytable">
                    <tr>
                        <th style="width: 30%;">Documentos validados</th>
                        <th style="width: 30%;">Descarga</th>
                        <th style="width: 30%;"><input type="checkbox" onclick="togglerechazar(this)">&nbsp; Seleccionar</th>
                    </tr>
                    <?php
                    while ($row = mysqli_fetch_array($result)) {
                    ?>
                        <tr>
                            <td><?php echo $row['nombreDocumento'] ?></td>
                            <td><a href="/documentos/<?php echo $row['nombreDocumento'] ?>">Descargar</a></td>
                            <td>
                                <form action="<?php echo $_SERVER["PHP_SELF"] . "/?idUsuario=$idUsuario"; ?>" method="POST">
                                    <input type="checkbox" name="rechazar[]" value="<?php echo $row['idDocumento'] ?>">
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>
                            <div class="row">
                                <div class="form-group">
                                    <input type="submit" class="btn btn-danger mt-3" value="Anular validación">
                                </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
    <?php
        }
    }
    ?>
    <script>
        function togglerechazar(source) {
            checkboxes = document.getElementsByName('rechazar[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>

</body>

</html>