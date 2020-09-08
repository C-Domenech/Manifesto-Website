<?php
// Variables conexión
$hostname = 'HOSTNAME';
$username = 'USERNAME';
$password = 'PASSWORD';
$database = 'DATABASE';

// Conexión
$conn = mysqli_connect($hostname, $username, $password, $database);

mysqli_set_charset($conn, "utf8");

// Comprueba conexión
if($conn === false){
    die("ERROR: No se pudo conectar. " . mysqli_connect_error());
}

// Conectar otros .php a la BBDD -> se pone esto al principio
// require_once("connectionconfig.php");
?>