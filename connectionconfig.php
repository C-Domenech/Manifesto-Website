<?php
// Variables conexión
$hostname = 'sql306.epizy.com';
$username = 'epiz_25969362';
$password = 'TpdKY7mLzHzuh3S';
$database = 'epiz_25969362_manifesto';

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