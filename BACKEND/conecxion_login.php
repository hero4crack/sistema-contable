<?php

$server = "localhost";
$user = "root";
$password = "";
$bd = "bd_sistema_contable_ea";

$conexion = new mysqli($server, $user, $password, $bd);

if ($conexion->connect_error) {
    die("conexion fallida" . $conexion->connect_error);

} else {
    
}
?>