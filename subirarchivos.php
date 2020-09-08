<?php
// Configuración de la conexión con MySQL
require_once("connectionconfig.php");
session_start();

if (!$_SESSION["loggedin"]) {
    header("location: /index.php");
}

//Cogemos el id del usuario
$idUsuario = $_SESSION["idUsuario"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombreDocsEliminado = $_POST['eliminar'];
    if (empty($nombreDocsEliminado)) {
        //echo "No ha seleccionado ningún documento.";
    } else {
        $n = count($nombreDocsEliminado);
        for ($i = 0; $i < $n; $i++) {
            $nombreDoc = $nombreDocsEliminado[$i];

            $sql = "DELETE FROM documento WHERE nombreDocumento = '$nombreDoc'";

            if(mysqli_query($conn, $sql)){
                //echo "$nombreDoc ha sido eliminado correctamente.";
            } else {
                echo "Error al eliminar $nombreDoc" . mysqli_error($conn);
            }

            $pathEliminado = 'documentos/' . $idUsuario . '/' . $nombreDoc;

            if(unlink($pathEliminado)){
                //echo "$nombreDoc ha sido eliminado correctamente.";
            } else {
                echo "Error al eliminar $nombreDoc";
            }
        }
    }

    if (isset($_POST['submit'])) {
        if (count($_FILES['upload']['name']) > 5) {
            echo "No es posible subir más de 5 archivos.";
        } else {
            if (count($_FILES['upload']['name']) > 0) {
                // Bucle por cada archivo
                for ($i = 0; $i < count($_FILES['upload']['name']); $i++) {
                    $name = $_FILES['upload']['name'][$i];
                    //$type = $_FILES['upload']['type'][$i];
                    $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
                    //$data = file_get_contents($tmp_file);
                    $idUsuario = $_SESSION["idUsuario"];
                    //Nombre archivo final
                    $nombrearchivofinal = $idUsuario . '_' . $name;

                    $sql = "INSERT INTO documento (nombreDocumento, idUsuario) VALUES (?, $idUsuario)";

                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, 's', $nombrearchivofinal);
                    //                 // s -> string b -> blob i -> int d -> double
                    mysqli_stmt_execute($stmt);
                    if ($tmpFilePath != "") {

                        $path = "documentos/$idUsuario/";

                        if(!file_exists($path)) {
                            mkdir($path, 0777, true);
                        } 

                        // Guardar el destino y el archivo con la fecha y hora de subida por si hay archivos con el mismo nombre + nombre (intenté ponerlo al revés, pero el formato se quedaba junto al nombre)
                        $filePath = $path . $nombrearchivofinal;
                        // Subir el archivo a un directorio temporal
                        if (move_uploaded_file($tmpFilePath, $filePath)) {
                            $files[] = $name;
                        }
                    }
                }
                // Comprobación filas cambiadas
                //printf("%d fila cambiada.\n", mysqli_stmt_affected_rows($stmt));

                // Mensaje de éxito
                //echo "<h4>Documentos subidos:</h4>";
                //if (is_array($files)) {
                //    echo "<ul>";
                //    foreach ($files as $file) {
                //        echo "<li>$file</li>";
                //    }
                //    echo "</ul>";
                //} else {
                //    echo "No ha seleccionado ningún archivo.";
                //}
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Subir documentación</title>
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
                <a class="nav-item nav-link" href="#">Sobre nosotros</a>
            </div>
        </div>
    </nav>

    <div class="jumbotron jumbotron-fluid text-center">
        <h1>Manifesto</h1>
        <p>Tu gestor de información favorito</p>
    </div>

    <!-- SUBIR DOCUMENTOS -->
    <div class="myuploadform text-center shadow-lg">
        <form enctype="multipart/form-data" method="POST">
            <div class="custom-file">
                <input type="file" accept="application/pdf" multiple="multiple" class="custom-file-input" id="upload" name="upload[]">
                <label class="custom-file-label text-left" for="upload" data-browse="Seleccionar">Selecciona los documentos</label>
            </div>
            <script>
                // Para que se vea el nombre del archivo elegido
                $(".custom-file-input").on("change", function() {
                    var fileName = $(this).val().split("\\").pop();
                    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
                });
            </script>
            <div class="form-group">
                <input type="submit" class="btn btn-primary pl-5 pr-5 mt-3" name="submit" value="Subir">
                <p class="mt-3"><b>Nota:</b> Solo se permiten 5 archivos en formato .pdf</p>
            </div>
        </form>
    </div>

    <!-- DOCUMENTOS SUBIDOS -->
    <?php
    $sql3 = "SELECT idDocumento, nombreDocumento, fechaDocumento FROM documento WHERE idUsuario = '$idUsuario'";
    if ($result = mysqli_query($conn, $sql3)) {
        if (mysqli_num_rows($result) > 0) {
    ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover mytable">
                    <tr>
                        <th style="width: 30%;">Documentos subidos</th>
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
                                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                                    <input type="checkbox" name="eliminar[]" value="<?php echo $row['nombreDocumento'] ?>">
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
                                    <input type="submit" class="btn btn-danger mt-3" value="Eliminar documento">
                                </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
    <?php
        } else {
            echo "<h5 style='text-align: center'>No ha subido ningún documento.</h5>";
        }
    }
    ?>
    <script>
        function toggle(source) {
            checkboxes = document.getElementsByName('eliminar[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>


</body>

</html>

<?php
    // Fin de la conexión
    mysqli_close($conn);
?>