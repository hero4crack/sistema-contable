<?php 
include_once('conecxion_bd.php');

session_unset();
session_destroy();
header('location:../VIEWS/index.php');
?>